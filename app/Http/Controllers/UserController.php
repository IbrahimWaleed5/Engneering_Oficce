<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\OfficeMember;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless(
            $request->user()?->role === 'admin',
            403
        );
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $query = User::with([
            'employeeProfile.specialty',
            'ownedOffice',
        ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statistics = [
            'all' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'engineers' => User::where('role', 'engineer')->count(),
            'employees' => User::where('role', 'employee')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'office_owners' => User::where('role', 'office_owner')->count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];

        return view('users.index', compact(
            'users',
            'statistics'
        ));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'engineer',
                    'employee',
                    'customer',
                    'office_owner',
                ]),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);

            $this->syncEmployeeProfile($user, $validated);

            $user->notify(
                new SystemNotification(
                    title: 'تم إنشاء حسابك',
                    message: 'تم إنشاء حساب جديد لك في نظام المكتب الهندسي.',
                    url: '/login',
                    sendMail: true,
                    buttonText: 'تسجيل الدخول'
                )
            );
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function edit(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $user->load([
            'employeeProfile',
            'ownedOffice',
            'officeMemberships.office',
        ]);

        $offices = Office::query()
            ->with('owner:id,name,email')
            ->orderBy('name')
            ->get([
                'id',
                'owner_user_id',
                'name',
                'slug',
                'city',
                'country',
                'status',
            ]);

        return view('users.edit', compact(
            'user',
            'offices'
        ));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user),
            ],
            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'engineer',
                    'employee',
                    'customer',
                    'office_owner',
                ]),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
            'job_title' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],

            'office_owner_action' => [
                'nullable',
                Rule::in(['keep', 'assign', 'remove']),
            ],
            'office_id' => [
                'nullable',
                'integer',
                'exists:offices,id',
                Rule::requiredIf(
                    $request->input('office_owner_action') === 'assign'
                ),
            ],
        ]);

        if (
            $user->is($request->user())
            && $validated['status'] === 'inactive'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'status' => 'لا يمكنك تعطيل حسابك الحالي.',
                ]);
        }

        if (
            $user->is($request->user())
            && $validated['role'] !== 'admin'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'لا يمكنك إزالة صلاحية المدير من حسابك الحالي.',
                ]);
        }

        $oldRole = $user->role;
        $oldStatus = $user->status;
        $ownershipChanged = false;
        $assignedOffice = null;

        DB::transaction(function () use (
            $request,
            $user,
            $validated,
            &$ownershipChanged,
            &$assignedOffice
        ) {
            $ownerAction = $validated['office_owner_action'] ?? 'keep';

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);

            $this->syncEmployeeProfile($user, $validated);

            if ($ownerAction === 'assign') {
                $assignedOffice = Office::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['office_id']);

                $this->assignOfficeOwner(
                    $request,
                    $user,
                    $assignedOffice
                );

                $ownershipChanged = true;
            }

            if ($ownerAction === 'remove') {
                $this->removeOfficeOwnership($user);
                $ownershipChanged = true;
            }
        });

        $user->refresh();

        if ($oldRole !== $user->role) {
            $user->notify(
                new SystemNotification(
                    'تم تغيير دور حسابك',
                    'تم تغيير دور حسابك إلى: '
                    . $this->roleLabel($user->role),
                    '/dashboard'
                )
            );
        }

        if ($oldStatus !== $user->status) {
            $user->notify(
                new SystemNotification(
                    'تم تغيير حالة الحساب',
                    $user->status === 'active'
                        ? 'تم تفعيل حسابك ويمكنك استخدام النظام.'
                        : 'تم تعطيل حسابك. تواصل مع إدارة النظام.',
                    '/dashboard'
                )
            );
        }

        if ($ownershipChanged) {
            $message = $assignedOffice
                ? 'تم تعيينك مالكًا لمكتب: ' . $assignedOffice->name
                : 'تم إنهاء ملكيتك للمكتب الهندسي.';

            $user->notify(
                new SystemNotification(
                    'تحديث ملكية المكتب',
                    $message,
                    '/dashboard'
                )
            );
        }

        return redirect()
            ->route('users.edit', $user)
            ->with(
                'success',
                $ownershipChanged
                    ? 'تم تحديث المستخدم وملكية المكتب بنجاح.'
                    : 'تم تحديث بيانات المستخدم بنجاح.'
            );
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        if ($user->is($request->user())) {
            return back()->withErrors([
                'delete' => 'لا يمكنك حذف حسابك الحالي.',
            ]);
        }

        if ($user->ownedOffice()->exists()) {
            return back()->withErrors([
                'delete' => 'لا يمكن حذف مالك مكتب قبل نقل أو إزالة ملكية المكتب.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    private function assignOfficeOwner(
        Request $request,
        User $newOwner,
        Office $office
    ): void {
        /*
        |--------------------------------------------------------------------------
        | إزالة ملكية المستخدم من أي مكتب سابق
        |--------------------------------------------------------------------------
        */
        Office::query()
            ->where('owner_user_id', $newOwner->id)
            ->whereKeyNot($office->id)
            ->get()
            ->each(function (Office $previousOffice) use ($newOwner) {
                $previousOffice->update([
                    'owner_user_id' => null,
                ]);

                OfficeMember::query()
                    ->where('office_id', $previousOffice->id)
                    ->where('user_id', $newOwner->id)
                    ->where('office_role', 'owner')
                    ->update([
                        'status' => 'inactive',
                        'left_at' => now(),
                    ]);
            });

        /*
        |--------------------------------------------------------------------------
        | إنهاء ملكية المالك السابق للمكتب
        |--------------------------------------------------------------------------
        */
        $previousOwnerId = $office->owner_user_id;

        if (
            $previousOwnerId
            && (int) $previousOwnerId !== (int) $newOwner->id
        ) {
            OfficeMember::query()
                ->where('office_id', $office->id)
                ->where('user_id', $previousOwnerId)
                ->where('office_role', 'owner')
                ->update([
                    'status' => 'inactive',
                    'left_at' => now(),
                ]);

            $previousOwner = User::query()->find($previousOwnerId);

            if (
                $previousOwner
                && $previousOwner->role === 'office_owner'
            ) {
                $previousOwner->update([
                    'role' => 'customer',
                ]);

                $previousOwner->notify(
                    new SystemNotification(
                        'تم نقل ملكية المكتب',
                        'تم نقل ملكية مكتب '
                        . $office->name
                        . ' إلى مستخدم آخر بواسطة مدير النظام.',
                        '/dashboard'
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | تعيين المالك الجديد
        |--------------------------------------------------------------------------
        */
        $office->update([
            'owner_user_id' => $newOwner->id,
        ]);

        OfficeMember::query()->updateOrCreate(
            [
                'office_id' => $office->id,
                'user_id' => $newOwner->id,
            ],
            [
                'office_role' => 'owner',
                'position' => 'مالك المكتب',
                'status' => 'active',
                'approved_by' => $request->user()->id,
                'joined_at' => now(),
                'left_at' => null,
            ]
        );

        $newOwner->update([
            'role' => 'office_owner',
            'status' => 'active',
        ]);
    }

    private function removeOfficeOwnership(User $user): void
    {
        $ownedOffices = Office::query()
            ->where('owner_user_id', $user->id)
            ->lockForUpdate()
            ->get();

        foreach ($ownedOffices as $office) {
            $office->update([
                'owner_user_id' => null,
            ]);

            OfficeMember::query()
                ->where('office_id', $office->id)
                ->where('user_id', $user->id)
                ->where('office_role', 'owner')
                ->update([
                    'status' => 'inactive',
                    'left_at' => now(),
                ]);
        }

        if ($user->role === 'office_owner') {
            $user->update([
                'role' => 'customer',
            ]);
        }
    }

    private function syncEmployeeProfile(
        User $user,
        array $data
    ): void {
        if (
            ! in_array(
                $user->role,
                ['engineer', 'employee'],
                true
            )
        ) {
            $user->employeeProfile()?->delete();

            return;
        }

        $user->employeeProfile()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'employee_number' => sprintf(
                    'EMP-%06d',
                    $user->id
                ),
                'job_title' => $data['job_title']
                    ?? (
                        $user->role === 'engineer'
                            ? 'مهندس'
                            : 'موظف'
                    ),
                'salary' => $data['salary'] ?? 0,
                'hire_date' => $data['hire_date']
                    ?? now()->toDateString(),
                'specialty_id' => $user
                    ->employeeProfile
                    ?->specialty_id,
            ]
        );
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'مدير النظام',
            'engineer' => 'مهندس',
            'employee' => 'موظف',
            'customer' => 'عميل',
            'office_owner' => 'مالك مكتب',
            default => 'مستخدم',
        };
    }
}
