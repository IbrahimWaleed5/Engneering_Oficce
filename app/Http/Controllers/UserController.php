<?php

namespace App\Http\Controllers;

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
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'engineer',
                    'employee',
                    'customer',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'hire_date' => [
                'nullable',
                'date',
            ],
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

            $this->syncEmployeeProfile(
                $user,
                $validated
            );

            $user->notify(
                new SystemNotification(
                    'مرحبًا بك في المكتب الهندسي',
                    'تم إنشاء حسابك بنجاح. يمكنك الآن تسجيل الدخول ومتابعة حسابك.',
                    '/dashboard'
                )
            );
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح.');
            $user->notify(
    new SystemNotification(
        title: 'تم إنشاء حسابك',
        message: 'تم إنشاء حساب جديد لك في نظام المكتب الهندسي.',
        url: '/login',
        sendMail: true,
        buttonText: 'تسجيل الدخول'
    )
);

    }


    public function edit(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $user->load('employeeProfile');

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

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
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'hire_date' => [
                'nullable',
                'date',
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

        DB::transaction(function () use (
            $user,
            $validated
        ) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'role' => $validated['role'],
                'status' => $validated['status'],
            ]);

            $this->syncEmployeeProfile(
                $user,
                $validated
            );
        });

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

        return redirect()
            ->route('users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureAdmin($request);

        if ($user->is($request->user())) {
            return back()->withErrors([
                'delete' => 'لا يمكنك حذف حسابك الحالي.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
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
            default => 'مستخدم',
        };

    }

}
