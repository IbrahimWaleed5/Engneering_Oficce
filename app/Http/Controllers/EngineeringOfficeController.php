<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\UpdateOfficeProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EngineeringOfficeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض جميع المكاتب للمهندسين
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->query('search')
        );

        $status = $request->query('status');

        $offices = Office::query()
            ->with([
                'owner:id,name',
            ])
            ->withCount([
                'activeMembers',
                'consultations',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'city',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'country',
                                'like',
                                '%' . $search . '%'
                            )
                            ->orWhere(
                                'description',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->when(
                in_array(
                    $status,
                    ['active', 'suspended'],
                    true
                ),
                function ($query) use ($status) {
                    $query->where(
                        'status',
                        $status
                    );
                },
                function ($query) {
                    /*
                    | تظهر المكاتب الفعالة والموقوفة فقط.
                    | المكاتب المرفوضة أو المغلقة لا تظهر للمهندسين.
                    */
                    $query->whereIn(
                        'status',
                        [
                            'active',
                            'suspended',
                        ]
                    );
                }
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statistics = [
            'all' => Office::query()
                ->whereIn(
                    'status',
                    [
                        'active',
                        'suspended',
                    ]
                )
                ->count(),

            'active' => Office::query()
                ->where('status', 'active')
                ->count(),

            'suspended' => Office::query()
                ->where('status', 'suspended')
                ->count(),
        ];

        return view(
            'offices.index',
            compact(
                'offices',
                'statistics',
                'search',
                'status'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | الملف الشخصي للمكتب
    |--------------------------------------------------------------------------
    */

   /*
|--------------------------------------------------------------------------
| الملف الشخصي العام للمكتب
|--------------------------------------------------------------------------
*/

public function show(
    Request $request,
    Office $office
): View {
    /*
    | المكاتب المعلقة أو المرفوضة أو المغلقة
    | لا تظهر في الدليل العام.
    */
    abort_if(
        in_array(
            $office->status,
            [
                'pending',
                'rejected',
                'closed',
            ],
            true
        ),
        404
    );

    $office->load([
        'owner:id,name',

        'activeMembers' => function ($query) {
            $query
                ->with([
                    'user:id,name,email,profile_photo,role',
                    'specialty:id,name',
                ])
                ->latest('joined_at');
        },
    ]);

    $office->loadCount([
        'activeMembers',
        'consultations',
    ]);

    /*
    |--------------------------------------------------------------------------
    | القيم الافتراضية للزائر والعميل
    |--------------------------------------------------------------------------
    */

    $pendingApplication = null;
    $latestApplication = null;
    $membership = null;
    $canApply = false;

    $user = $request->user();

    /*
    |--------------------------------------------------------------------------
    | بيانات الانضمام تظهر للمهندس فقط
    |--------------------------------------------------------------------------
    */

    if ($user && $user->role === 'engineer') {
        $pendingApplication = $office
            ->membershipApplications()
            ->where(
                'engineer_id',
                $user->id
            )
            ->where('status', 'pending')
            ->latest()
            ->first();

        $latestApplication = $office
            ->membershipApplications()
            ->where(
                'engineer_id',
                $user->id
            )
            ->latest()
            ->first();

        $membership = $office
            ->members()
            ->where(
                'user_id',
                $user->id
            )
            ->latest()
            ->first();

        $isActiveMember =
            $membership
            && $membership->status === 'active';

        $canApply =
            $office->status === 'active'
            && $office->subscription_status === 'active'
            && $office->subscription_ends_at !== null
            && $office->subscription_ends_at->isFuture()
            && ! $pendingApplication
            && ! $isActiveMember;
    }

    return view(
        'offices.show',
        compact(
            'office',
            'pendingApplication',
            'latestApplication',
            'membership',
            'canApply'
        )
    );
}
    /*
|--------------------------------------------------------------------------
| صفحة تعديل الملف الشخصي للمكتب
|--------------------------------------------------------------------------
*/

public function profile(Request $request): View
{
    $managerMembership = $request
        ->user()
        ->managedOfficeMemberships()
        ->with('office')
        ->where('status', 'active')
        ->whereIn('office_role', [
            'owner',
            'manager',
        ])
        ->first();

    abort_unless(
        $managerMembership !== null,
        403,
        'ليس لديك صلاحية إدارة ملف مكتب.'
    );

    $office = $managerMembership->office;

    return view(
        'office.profile',
        compact(
            'office',
            'managerMembership'
        )
    );
}

/*
|--------------------------------------------------------------------------
| حفظ تعديلات الملف الشخصي للمكتب
|--------------------------------------------------------------------------
*/

public function updateProfile(
    UpdateOfficeProfileRequest $request
): RedirectResponse {
    $managerMembership = $request
        ->user()
        ->managedOfficeMemberships()
        ->with('office')
        ->where('status', 'active')
        ->whereIn('office_role', [
            'owner',
            'manager',
        ])
        ->first();

    abort_unless(
        $managerMembership !== null,
        403,
        'ليس لديك صلاحية تعديل ملف مكتب.'
    );

    $office = $managerMembership->office;
    $validated = $request->validated();

    /*
    |--------------------------------------------------------------------------
    | حذف الشعار الحالي
    |--------------------------------------------------------------------------
    */

    if (
        $request->boolean('remove_logo')
        && $office->logo_path
    ) {
        Storage::disk('public')->delete(
            $office->logo_path
        );

        $office->logo_path = null;
    }

    /*
    |--------------------------------------------------------------------------
    | حذف صورة الغلاف الحالية
    |--------------------------------------------------------------------------
    */

    if (
        $request->boolean('remove_cover')
        && $office->cover_path
    ) {
        Storage::disk('public')->delete(
            $office->cover_path
        );

        $office->cover_path = null;
    }

    /*
    |--------------------------------------------------------------------------
    | رفع شعار جديد
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('logo')) {
        if ($office->logo_path) {
            Storage::disk('public')->delete(
                $office->logo_path
            );
        }

        $office->logo_path = $request
            ->file('logo')
            ->store(
                'offices/'
                . $office->id
                . '/logo',
                'public'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | رفع غلاف جديد
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('cover')) {
        if ($office->cover_path) {
            Storage::disk('public')->delete(
                $office->cover_path
            );
        }

        $office->cover_path = $request
            ->file('cover')
            ->store(
                'offices/'
                . $office->id
                . '/cover',
                'public'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث البيانات النصية
    |--------------------------------------------------------------------------
    */

    $office->fill([
        'name' => $validated['name'],

        'email' => $validated['email'],

        'phone' =>
            $validated['phone'] ?? null,

        'commercial_registration' =>
            $validated['commercial_registration']
            ?? null,

        'license_number' =>
            $validated['license_number']
            ?? null,

        'country' =>
            $validated['country'] ?? null,

        'city' =>
            $validated['city'] ?? null,

        'address' =>
            $validated['address'] ?? null,

        'description' =>
            $validated['description'] ?? null,
    ]);

    $office->save();

    return redirect()
        ->route('office.profile')
        ->with(
            'success',
            'تم تحديث الملف الشخصي للمكتب بنجاح.'
        );
}
/*
|--------------------------------------------------------------------------
| لوحة تحكم المكتب
|--------------------------------------------------------------------------
*/

public function dashboard(Request $request): View
{
    $managerMembership = $request
        ->user()
        ->managedOfficeMemberships()
        ->with('office')
        ->where('status', 'active')
        ->whereIn('office_role', [
            'owner',
            'manager',
        ])
        ->first();

    abort_unless(
        $managerMembership !== null,
        403,
        'ليس لديك صلاحية الدخول إلى لوحة المكتب.'
    );

    $office = $managerMembership->office;

    $office->load([
        'owner:id,name,email,phone',
    ]);

    $office->loadCount([
        'members',
        'activeMembers',
        'consultations',
        'membershipApplications',
    ]);

    $pendingApplicationsCount = $office
        ->membershipApplications()
        ->where('status', 'pending')
        ->count();

    $pendingConsultationsCount = $office
        ->consultations()
        ->where('status', 'pending')
        ->count();

    $inProgressConsultationsCount = $office
        ->consultations()
        ->where('status', 'in_progress')
        ->count();

    $completedConsultationsCount = $office
        ->consultations()
        ->where('status', 'completed')
        ->count();

    $latestApplications = $office
        ->membershipApplications()
        ->with([
            'engineer:id,name,email,profile_photo',
            'specialty:id,name',
        ])
        ->latest()
        ->limit(5)
        ->get();

    $latestConsultations = $office
        ->consultations()
        ->with([
            'customer:id,name,email',
            'engineer:id,name,email',
            'consultationType:id,name',
        ])
        ->latest()
        ->limit(5)
        ->get();

    $latestSubscription = $office
        ->subscriptions()
        ->latest()
        ->first();

    $statistics = [
        'members' =>
            $office->members_count ?? 0,

        'active_members' =>
            $office->active_members_count ?? 0,

        'membership_applications' =>
            $office->membership_applications_count ?? 0,

        'pending_applications' =>
            $pendingApplicationsCount,

        'consultations' =>
            $office->consultations_count ?? 0,

        'pending_consultations' =>
            $pendingConsultationsCount,

        'in_progress_consultations' =>
            $inProgressConsultationsCount,

        'completed_consultations' =>
            $completedConsultationsCount,
    ];

    return view(
        'office.dashboard',
        compact(
            'office',
            'managerMembership',
            'statistics',
            'latestApplications',
            'latestConsultations',
            'latestSubscription'
        )
    );
}
}
