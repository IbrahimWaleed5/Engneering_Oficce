<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationOfficeAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\AssignConsultationToOfficeRequest;
use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultationOfficeAssignmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض استشارات المكتب
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $managerMembership = $request
            ->user()
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->first();

        abort_unless(
            $managerMembership !== null,
            403,
            'ليس لديك صلاحية إدارة استشارات مكتب.'
        );

        $office = $managerMembership->office;

        $consultations = Consultation::query()
            ->where('assigned_office_id', $office->id)
            ->with([
                'customer:id,name,email,phone',
                'consultationType:id,name',
                'engineer:id,name,email',
                'assignedOffice:id,name,slug',
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'all' => Consultation::query()
                ->where('assigned_office_id', $office->id)
                ->count(),

            'pending' => Consultation::query()
                ->where('assigned_office_id', $office->id)
                ->where('status', 'pending')
                ->count(),

            'in_progress' => Consultation::query()
                ->where('assigned_office_id', $office->id)
                ->where('status', 'in_progress')
                ->count(),

            'completed' => Consultation::query()
                ->where('assigned_office_id', $office->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view(
            'office.consultations',
            compact(
                'office',
                'consultations',
                'statistics',
                'managerMembership'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تعيين مهندس من المكتب للاستشارة
    |--------------------------------------------------------------------------
    */

    public function assignEngineer(
        Request $request,
        Consultation $consultation
    ): RedirectResponse {
        $managerMembership = $request
            ->user()
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->first();

        abort_unless(
            $managerMembership !== null,
            403,
            'ليس لديك صلاحية إدارة استشارات مكتب.'
        );

        abort_unless(
            (int) $consultation->assigned_office_id
            === (int) $managerMembership->office_id,
            403,
            'هذه الاستشارة غير محولة إلى مكتبك.'
        );

        $validated = $request->validate(
            [
                'engineer_id' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ],
            [
                'engineer_id.required' =>
                    'يجب اختيار مهندس.',

                'engineer_id.exists' =>
                    'المهندس المختار غير موجود.',
            ]
        );

        $member = $managerMembership
            ->office
            ->members()
            ->where('user_id', $validated['engineer_id'])
            ->where('office_role', 'engineer')
            ->where('status', 'active')
            ->first();

        abort_unless(
            $member !== null,
            422,
            'المهندس المختار ليس عضوًا فعالًا في المكتب.'
        );

        $oldEngineerId = $consultation->engineer_id;

        $consultation->update([
            'engineer_id' => $validated['engineer_id'],
            'status' => $consultation->status === 'pending'
                ? 'in_progress'
                : $consultation->status,
        ]);

        ConsultationOfficeAssignment::query()
            ->where('consultation_id', $consultation->id)
            ->where('office_id', $managerMembership->office_id)
            ->latest()
            ->first()
            ?->update([
                'assigned_engineer_id' => $validated['engineer_id'],
                'previous_engineer_id' => $oldEngineerId,
                'engineer_assigned_by' => $request->user()->id,
                'engineer_assigned_at' => now(),
            ]);

        return back()->with(
            'success',
            'تم تعيين المهندس للاستشارة بنجاح.'
        );
    }
    /*
|--------------------------------------------------------------------------
| نموذج تحويل الاستشارة إلى مكتب — مدير النظام
|--------------------------------------------------------------------------
*/

public function adminAssignForm(
    Consultation $consultation
): View {
    $offices = Office::query()
        ->where('status', 'active')
        ->where('subscription_status', 'active')
        ->whereNotNull('subscription_ends_at')
        ->where(
            'subscription_ends_at',
            '>',
            now()
        )
        ->withCount([
            'activeMembers',
            'consultations',
        ])
        ->orderBy('name')
        ->get();

    $consultation->load([
        'customer:id,name,email,phone',
        'consultationType:id,name',
        'engineer:id,name,email',
        'assignedOffice:id,name,slug,status',
    ]);

    return view(
        'admin.consultation-office.assign',
        compact(
            'consultation',
            'offices'
        )
    );
}

/*
|--------------------------------------------------------------------------
| حفظ تحويل الاستشارة إلى مكتب — مدير النظام
|--------------------------------------------------------------------------
*/

public function adminAssign(
    AssignConsultationToOfficeRequest $request,
    Consultation $consultation
): RedirectResponse {
    if (
        in_array(
            $consultation->status,
            [
                'completed',
                'cancelled',
            ],
            true
        )
    ) {
        return back()->with(
            'error',
            'لا يمكن تحويل استشارة مكتملة أو ملغاة.'
        );
    }

    $office = Office::query()
        ->whereKey(
            $request->validated('office_id')
        )
        ->firstOrFail();

    if (! $office->isOperational()) {
        return back()
            ->withInput()
            ->with(
                'error',
                'المكتب المختار غير فعال أو اشتراكه منتهي.'
            );
    }

    if (
        (int) $consultation->assigned_office_id
        === (int) $office->id
    ) {
        return back()->with(
            'info',
            'الاستشارة محولة بالفعل إلى هذا المكتب.'
        );
    }

    DB::transaction(function () use (
        $request,
        $consultation,
        $office
    ) {
        $previousOfficeId =
            $consultation->assigned_office_id;

        $previousEngineerId =
            $consultation->engineer_id;

        if ($previousOfficeId) {
            ConsultationOfficeAssignment::query()
                ->where(
                    'consultation_id',
                    $consultation->id
                )
                ->whereNull('unassigned_at')
                ->update([
                    'unassigned_at' => now(),

                    'unassigned_by' =>
                        $request->user()->id,
                ]);
        }

        $consultation->update([
            'assigned_office_id' =>
                $office->id,

            'assigned_office_by' =>
                $request->user()->id,

            'assigned_office_at' => now(),

            'engineer_id' => null,

            'status' =>
                $consultation->status === 'pending'
                    ? 'in_progress'
                    : $consultation->status,
        ]);

        ConsultationOfficeAssignment::create([
            'consultation_id' =>
                $consultation->id,

            'office_id' =>
                $office->id,

            'previous_office_id' =>
                $previousOfficeId,

            'previous_engineer_id' =>
                $previousEngineerId,

            'assigned_by' =>
                $request->user()->id,

            'assigned_at' => now(),

            'notes' =>
                $request->validated('notes'),
        ]);
    });

    return redirect()
        ->route('consultations.index')
        ->with(
            'success',
            'تم تحويل الاستشارة إلى المكتب الهندسي بنجاح.'
        );
}
/*
|--------------------------------------------------------------------------
| التحقق من أن المهندس عضو فعال في مكتب الاستشارة
|--------------------------------------------------------------------------
*/

private function ensureEngineerBelongsToAssignedOffice(
    Request $request,
    Consultation $consultation
): void {
    $user = $request->user();

    abort_unless(
        $user !== null
        && $user->role === 'engineer',
        403,
        'هذا الإجراء مخصص للمهندسين.'
    );

    abort_unless(
        (int) $consultation->engineer_id
        === (int) $user->id,
        403,
        'هذه الاستشارة غير مسندة إليك.'
    );

    abort_unless(
        $consultation->assigned_office_id !== null,
        403,
        'الاستشارة غير مرتبطة بمكتب هندسي.'
    );

    $isActiveOfficeMember = $user
        ->officeMemberships()
        ->where(
            'office_id',
            $consultation->assigned_office_id
        )
        ->where('office_role', 'engineer')
        ->where('status', 'active')
        ->exists();

    abort_unless(
        $isActiveOfficeMember,
        403,
        'أنت لست عضوًا فعالًا في المكتب المسؤول عن هذه الاستشارة.'
    );

    $office = $consultation
        ->assignedOffice()
        ->first();

    abort_unless(
        $office !== null
        && $office->isOperational(),
        403,
        'المكتب المسؤول عن الاستشارة غير فعال حاليًا.'
    );
}
/*
|--------------------------------------------------------------------------
| عرض إيصال اشتراك المكتب
|--------------------------------------------------------------------------
*/

public function subscriptionReceipt(
    Request $request,
    \App\Models\OfficeSubscription $officeSubscription
): StreamedResponse {
    $user = $request->user();

    abort_unless(
        $user !== null,
        403,
        'يجب تسجيل الدخول للوصول إلى الملف.'
    );

    $isAdmin =
        $user->role === 'admin';

    $isOfficeManager = $user
        ->managedOfficeMemberships()
        ->where(
            'office_id',
            $officeSubscription->office_id
        )
        ->where('status', 'active')
        ->whereIn(
            'office_role',
            ['owner', 'manager']
        )
        ->exists();

    abort_unless(
        $isAdmin || $isOfficeManager,
        403,
        'ليس لديك صلاحية الوصول إلى إيصال هذا المكتب.'
    );

    $path = $officeSubscription->receipt_path;

    abort_if(
        empty($path),
        404,
        'لا يوجد إيصال مرفوع لهذا الاشتراك.'
    );

    abort_unless(
        Storage::exists($path),
        404,
        'ملف الإيصال غير موجود في التخزين.'
    );

    $extension = pathinfo(
        $path,
        PATHINFO_EXTENSION
    );

    $officeName = $officeSubscription
        ->office()
        ->value('name') ?? 'office';

    $safeOfficeName = preg_replace(
        '/[^\pL\pN\-_]+/u',
        '-',
        $officeName
    );

    $fileName =
        'Subscription-Receipt-'
        . $safeOfficeName
        . '-'
        . $officeSubscription->id
        . '.'
        . $extension;

    return Storage::download(
        $path,
        $fileName
    );
}
/*
|--------------------------------------------------------------------------
| إلغاء تحويل الاستشارة من المكتب — مدير النظام
|--------------------------------------------------------------------------
*/

public function adminUnassign(
    Request $request,
    Consultation $consultation
): RedirectResponse {
    abort_unless(
        $request->user()?->role === 'admin',
        403,
        'هذا الإجراء مخصص لمدير النظام.'
    );

    if (! $consultation->assigned_office_id) {
        return back()->with(
            'info',
            'الاستشارة غير محولة إلى مكتب هندسي.'
        );
    }

    if (
        in_array(
            $consultation->status,
            [
                'completed',
                'cancelled',
            ],
            true
        )
    ) {
        return back()->with(
            'error',
            'لا يمكن إلغاء تحويل استشارة مكتملة أو ملغاة.'
        );
    }

    DB::transaction(function () use (
        $request,
        $consultation
    ): void {
        $previousOfficeId =
            $consultation->assigned_office_id;

        $previousEngineerId =
            $consultation->engineer_id;

        ConsultationOfficeAssignment::query()
            ->where(
                'consultation_id',
                $consultation->id
            )
            ->where(
                'office_id',
                $previousOfficeId
            )
            ->whereNull('unassigned_at')
            ->update([
                'unassigned_at' => now(),

                'unassigned_by' =>
                    $request->user()->id,
            ]);

        $consultation->update([
            'assigned_office_id' => null,

            'assigned_office_by' => null,

            'assigned_office_at' => null,

            /*
            | عند إزالة المكتب نحذف المهندس الذي عيّنه المكتب.
            */
            'engineer_id' => null,

            /*
            | نعيد الاستشارة إلى الانتظار حتى يعاد تعيينها.
            */
            'status' => 'pending',
        ]);

        /*
        | إزالة المهندس السابق من محادثة الاستشارة إن وجدت.
        */
        if ($previousEngineerId) {
            $conversation = \App\Models\Conversation::query()
                ->where(
                    'type',
                    'consultation'
                )
                ->where(
                    'consultation_id',
                    $consultation->id
                )
                ->first();

            if ($conversation) {
                $conversation
                    ->participants()
                    ->detach($previousEngineerId);
            }
        }
    });

    return redirect()
        ->route(
            'consultations.index'
        )
        ->with(
            'success',
            'تم إلغاء تحويل الاستشارة من المكتب الهندسي.'
        );
}
}
