<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewOfficeApplicationRequest;
use App\Http\Requests\ReviewOfficeSubscriptionRequest;
use App\Models\Office;
use App\Models\OfficeApplication;
use App\Models\OfficeMember;
use App\Models\OfficeSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOfficeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | طلبات انضمام المكاتب
    |--------------------------------------------------------------------------
    */

    public function applicationsIndex(): View
    {
        $applications = OfficeApplication::query()
            ->with([
                'applicant:id,name,email,phone,role',
                'reviewer:id,name',
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'pending' => OfficeApplication::query()
                ->where('status', 'pending')
                ->count(),

            'approved' => OfficeApplication::query()
                ->where('status', 'approved')
                ->count(),

            'rejected' => OfficeApplication::query()
                ->where('status', 'rejected')
                ->count(),
        ];

        return view(
            'admin.office-applications.index',
            compact(
                'applications',
                'statistics'
            )
        );
    }

    public function applicationShow(
        OfficeApplication $officeApplication
    ): View {
        $officeApplication->load([
            'applicant:id,name,email,phone,role',
            'reviewer:id,name',
        ]);

        return view(
            'admin.office-applications.show',
            [
                'application' => $officeApplication,
            ]
        );
    }

    public function reviewApplication(
        ReviewOfficeApplicationRequest $request,
        OfficeApplication $officeApplication
    ): RedirectResponse {
        if ($officeApplication->status !== 'pending') {
            return back()->with(
                'error',
                'تمت مراجعة هذا الطلب سابقًا.'
            );
        }

        $decision = $request->validated('decision');

        if ($decision === 'reject') {
            $officeApplication->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' =>
                    $request->validated(
                        'rejection_reason'
                    ),
            ]);

            return redirect()
                ->route(
                    'admin.office-applications.index'
                )
                ->with(
                    'success',
                    'تم رفض طلب المكتب.'
                );
        }

        $officeExists = Office::query()
            ->where(
                'owner_user_id',
                $officeApplication->user_id
            )
            ->exists();

        if ($officeExists) {
            return back()->with(
                'error',
                'هذا المستخدم يملك مكتبًا بالفعل.'
            );
        }

        DB::transaction(function () use (
            $officeApplication,
            $request
        ) {
            $office = Office::create([
                'owner_user_id' =>
                    $officeApplication->user_id,

                'name' =>
                    $officeApplication->office_name,

                'email' =>
                    $officeApplication->email,

                'phone' =>
                    $officeApplication->phone,

                'commercial_registration' =>
                    $officeApplication
                        ->commercial_registration,

                'license_number' =>
                    $officeApplication
                        ->license_number,

                'country' =>
                    $officeApplication->country,

                'city' =>
                    $officeApplication->city,

                'address' =>
                    $officeApplication->address,

                'description' =>
                    $officeApplication->notes,

                'status' => 'active',

                'subscription_status' => 'pending',

                'monthly_subscription_amount' => 300,

                'subscription_currency' => 'USD',

                'approved_at' => now(),

                'approved_by' =>
                    $request->user()->id,
            ]);

            OfficeMember::create([
                'office_id' => $office->id,

                'user_id' =>
                    $officeApplication->user_id,

                'position' => 'مدير المكتب',

                'office_role' => 'owner',

                'status' => 'active',

                'approved_by' =>
                    $request->user()->id,

                'joined_at' => now(),
            ]);

            OfficeSubscription::create([
                'office_id' => $office->id,
                'amount' => 300,
                'currency' => 'USD',
                'duration_value' => 1,
                'duration_unit' => 'month',
                'status' => 'under_review',
                'payment_method' =>
                    $officeApplication->payment_method
                    ?? 'bank_transfer',
                'payment_reference' =>
                    $officeApplication->payment_reference,
                'receipt_path' =>
                    $officeApplication->payment_receipt_path,
                'paid_at' =>
                    $officeApplication->paid_at
                    ?? now(),
                'notes' =>
                    'الاشتراك الشهري الأول المرفق مع طلب تسجيل المكتب.',
            ]);

            $officeApplication
                ->applicant()
                ->update([
                    'role' => 'office_owner',
                ]);

            $officeApplication->update([
                'status' => 'approved',

                'reviewed_by' =>
                    $request->user()->id,

                'reviewed_at' => now(),

                'rejection_reason' => null,
            ]);
        });

        return redirect()
            ->route(
                'admin.office-applications.index'
            )
            ->with(
                'success',
                'تم قبول المكتب وإنشاء اشتراك شهري بقيمة 300 دولار، وتم إرسال الإيصال إلى مراجعة الاشتراكات.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | اشتراكات المكاتب
    |--------------------------------------------------------------------------
    */

    public function subscriptionsIndex(): View
    {
        $subscriptions = OfficeSubscription::query()
            ->with([
                'office:id,owner_user_id,name,status,subscription_status',
                'office.owner:id,name,email,phone',
                'approver:id,name',
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'under_review' =>
                OfficeSubscription::query()
                    ->where(
                        'status',
                        'under_review'
                    )
                    ->count(),

            'active' =>
                OfficeSubscription::query()
                    ->where('status', 'active')
                    ->count(),

            'rejected' =>
                OfficeSubscription::query()
                    ->where('status', 'rejected')
                    ->count(),
        ];

        return view(
            'admin.office-subscriptions.index',
            compact(
                'subscriptions',
                'statistics'
            )
        );
    }

    public function reviewSubscription(
        ReviewOfficeSubscriptionRequest $request,
        OfficeSubscription $officeSubscription
    ): RedirectResponse {
        if (
            $officeSubscription->status
            !== 'under_review'
        ) {
            return back()->with(
                'error',
                'هذا الاشتراك ليس قيد المراجعة.'
            );
        }

        $officeSubscription->load('office');

        $office = $officeSubscription->office;

        if (! $office) {
            return back()->with(
                'error',
                'المكتب المرتبط بالاشتراك غير موجود.'
            );
        }

        if (
            in_array(
                $office->status,
                ['closed', 'rejected'],
                true
            )
        ) {
            return back()->with(
                'error',
                'لا يمكن اعتماد اشتراك مكتب مغلق أو مرفوض.'
            );
        }

        $decision = $request->validated('decision');

        if ($decision === 'reject') {
            DB::transaction(function () use (
                $officeSubscription,
                $office,
                $request
            ) {
                $officeSubscription->update([
                    'status' => 'rejected',

                    'approved_by' =>
                        $request->user()->id,

                    'approved_at' => now(),

                    'rejection_reason' =>
                        $request->validated(
                            'rejection_reason'
                        ),

                    'notes' =>
                        $request->validated('notes')
                        ?? $officeSubscription->notes,
                ]);

                $hasActiveSubscription = $office
                    ->subscriptions()
                    ->where('status', 'active')
                    ->where(
                        'ends_at',
                        '>',
                        now()
                    )
                    ->exists();

                $office->update([
                    'subscription_status' =>
                        $hasActiveSubscription
                            ? 'active'
                            : 'pending',
                ]);
            });

            return redirect()
                ->route(
                    'admin.office-subscriptions.index'
                )
                ->with(
                    'success',
                    'تم رفض إيصال اشتراك المكتب.'
                );
        }

        DB::transaction(function () use (
            $officeSubscription,
            $office,
            $request
        ) {
            $durationValue =
                (int) $request->validated('duration_value');

            $durationUnit =
                $request->validated('duration_unit');

            $startsAt = now();

            $endsAt = match ($durationUnit) {
                'day' => $startsAt
                    ->copy()
                    ->addDays($durationValue),

                'month' => $startsAt
                    ->copy()
                    ->addMonthsNoOverflow($durationValue),

                'year' => $startsAt
                    ->copy()
                    ->addYears($durationValue),

                default => $startsAt
                    ->copy()
                    ->addMonth(),
            };

            $office
                ->subscriptions()
                ->where('status', 'active')
                ->where('id', '!=', $officeSubscription->id)
                ->where(
                    'ends_at',
                    '<=',
                    now()
                )
                ->update([
                    'status' => 'expired',
                ]);

            $officeSubscription->update([
                'status' => 'active',

                'duration_value' => $durationValue,

                'duration_unit' => $durationUnit,

                'starts_at' => $startsAt,

                'ends_at' => $endsAt,

                'approved_by' =>
                    $request->user()->id,

                'approved_at' => now(),

                'rejection_reason' => null,

                'notes' =>
                    $request->validated('notes')
                    ?? $officeSubscription->notes,
            ]);

            $office->update([
                'subscription_status' => 'active',

                'subscription_starts_at' =>
                    $startsAt,

                'subscription_ends_at' =>
                    $endsAt,
            ]);
        });

        return redirect()
            ->route(
                'admin.office-subscriptions.index'
            )
            ->with(
                'success',
                'تم اعتماد اشتراك المكتب وتفعيله بالمدة المحددة.'
            );
    }
    /*
|--------------------------------------------------------------------------
| إدارة المكاتب الهندسية
|--------------------------------------------------------------------------
*/

public function officesIndex(): View
{
    $offices = Office::query()
        ->with([
            'owner:id,name,email,phone',
        ])
        ->withCount([
            'members',
            'consultations',
        ])
        ->latest()
        ->paginate(20)
        ->withQueryString();

    $statistics = [
        'active' => Office::query()
            ->where('status', 'active')
            ->count(),

        'suspended' => Office::query()
            ->where('status', 'suspended')
            ->count(),

        'closed' => Office::query()
            ->where('status', 'closed')
            ->count(),

        'subscription_active' => Office::query()
            ->where(
                'subscription_status',
                'active'
            )
            ->count(),
    ];

    return view(
        'admin.offices.index',
        compact(
            'offices',
            'statistics'
        )
    );
}

public function officeShow(Office $office): View
{
    $office->load([
        'owner:id,name,email,phone',
        'approver:id,name',
        'suspender:id,name',
        'members.user:id,name,email,phone,role',
        'members.specialty',
    ]);

    $office->loadCount([
        'members',
        'consultations',
        'subscriptions',
    ]);

    $latestSubscriptions = $office
        ->subscriptions()
        ->latest()
        ->limit(10)
        ->get();

    return view(
        'admin.offices.show',
        compact(
            'office',
            'latestSubscriptions'
        )
    );
}

public function updateOfficeStatus(
    \App\Http\Requests\UpdateOfficeStatusRequest $request,
    Office $office
): RedirectResponse {
    $status = $request->validated('status');
    $reason = $request->validated('reason');

    if ($office->status === $status) {
        return back()->with(
            'info',
            'المكتب موجود بالفعل على الحالة المختارة.'
        );
    }

    DB::transaction(function () use (
        $office,
        $status,
        $reason,
        $request
    ) {
        if ($status === 'suspended') {
            $office->update([
                'status' => 'suspended',

                'suspended_at' => now(),

                'suspended_by' =>
                    $request->user()->id,

                'suspension_reason' =>
                    $reason,

                'closed_at' => null,

                'closed_by' => null,

                'closure_reason' => null,
            ]);

            return;
        }

        if ($status === 'closed') {
            $office->update([
                'status' => 'closed',

                'closed_at' => now(),

                'closed_by' =>
                    $request->user()->id,

                'closure_reason' =>
                    $reason,
            ]);

            return;
        }

        $office->update([
            'status' => 'active',

            'suspended_at' => null,

            'suspended_by' => null,

            'suspension_reason' => null,

            'closed_at' => null,

            'closed_by' => null,

            'closure_reason' => null,
        ]);
    });

    $message = match ($status) {
        'suspended' =>
            'تم إيقاف المكتب عن العمل.',

        'closed' =>
            'تم إغلاق المكتب.',

        default =>
            'تم إعادة تفعيل المكتب.',
    };

    return redirect()
        ->route(
            'admin.offices.show',
            $office
        )
        ->with(
            'success',
            $message
        );
}
}
