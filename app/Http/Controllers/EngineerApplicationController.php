<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\EngineerApplication;
use App\Models\EngineeringSpecialty;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\AttachmentModerationService;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EngineerApplicationController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService,
        private readonly AttachmentModerationService $attachmentModerationService
    ) {
    }

    public function create(Request $request)
    {
        $user = $request->user();

        abort_unless(
            in_array(
                $user->role,
                ['customer', 'engineer'],
                true
            ),
            403
        );

        $hasPendingApplication =
            EngineerApplication::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'لديك طلب دفع قيد المراجعة حاليًا.'
                );
        }

        $user->loadMissing('employeeProfile');

        $lastApproved =
            EngineerApplication::query()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->latest('approved_at')
                ->first();

        $isRenewal =
            $lastApproved !== null
            && $user->employeeProfile !== null;

        $specialties =
            EngineeringSpecialty::query()
                ->orderBy('name')
                ->get();

        return view(
            'engineer-applications.create',
            compact(
                'specialties',
                'isRenewal',
                'lastApproved'
            )
        );
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless(
            in_array(
                $user->role,
                ['customer', 'engineer'],
                true
            ),
            403
        );

        $hasPendingApplication =
            EngineerApplication::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

        if ($hasPendingApplication) {
            return back()
                ->withErrors([
                    'application' =>
                        'لديك طلب دفع قيد المراجعة.',
                ])
                ->withInput();
        }

        $user->loadMissing('employeeProfile');

        $lastApproved =
            EngineerApplication::query()
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->latest('approved_at')
                ->first();

        $isRenewal =
            $lastApproved !== null
            && $user->employeeProfile !== null;

        $validated = $request->validate([
            'specialty_id' => [
                $isRenewal ? 'nullable' : 'required',
                'exists:engineering_specialties,id',
            ],

            'certificate_file' => [
                $isRenewal ? 'nullable' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'cv_file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],

            'payment_receipt' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],
        ]);

        $specialtyId =
            $validated['specialty_id']
            ?? $user->employeeProfile?->specialty_id
            ?? $lastApproved?->specialty_id;

        if (! $specialtyId) {
            return back()
                ->withErrors([
                    'specialty_id' =>
                        'يجب تحديد التخصص الهندسي.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | فحص مرفقات طلب المهندس قبل التخزين
        |--------------------------------------------------------------------------
        */

        foreach ([
            'certificate_file' =>
                'engineer_application_certificate',

            'cv_file' =>
                'engineer_application_cv',

            'payment_receipt' =>
                'engineer_application_payment_receipt',
        ] as $field => $sourceType) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $attachmentModeration =
                $this->attachmentModerationService
                    ->moderate(
                        user: $user,
                        file: $request->file($field),
                        sourceType: $sourceType,
                        sourceId: null,
                        context: [
                            'content_section' =>
                                'engineer_application',

                            'recipient_role' =>
                                'admin',

                            'application_type' =>
                                $isRenewal
                                    ? 'renewal'
                                    : 'new',
                        ]
                    );

            if (! $attachmentModeration['allowed']) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $attachmentModeration[
                            'user_message'
                        ]
                    );
            }
        }

        $certificatePath =
            $lastApproved?->certificate_file;

        $cvPath =
            $lastApproved?->cv_file;

        $receiptPath = null;

        $newCertificatePath = null;
        $newCvPath = null;

        try {
            if ($request->hasFile('certificate_file')) {
                $newCertificatePath = $request
                    ->file('certificate_file')
                    ->store(
                        'engineer-applications/certificates',
                        'public'
                    );

                $certificatePath =
                    $newCertificatePath;
            }

            if (! $certificatePath) {
                return back()
                    ->withErrors([
                        'certificate_file' =>
                            'يجب رفع الشهادة الهندسية.',
                    ])
                    ->withInput();
            }

            if ($request->hasFile('cv_file')) {
                $newCvPath = $request
                    ->file('cv_file')
                    ->store(
                        'engineer-applications/cv',
                        'public'
                    );

                $cvPath = $newCvPath;
            }

            $receiptPath = $request
                ->file('payment_receipt')
                ->store(
                    'engineer-applications/receipts',
                    'public'
                );

            EngineerApplication::create([
                'user_id' =>
                    $user->id,

                'specialty_id' =>
                    $specialtyId,

                'certificate_file' =>
                    $certificatePath,

                'cv_file' =>
                    $cvPath,

                'payment_receipt' =>
                    $receiptPath,

                'amount' =>
                    50,

                'application_type' =>
                    $isRenewal
                        ? 'renewal'
                        : 'new',

                'payment_status' =>
                    'pending',

                'status' =>
                    'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete(
                array_filter([
                    $newCertificatePath,
                    $newCvPath,
                    $receiptPath,
                ])
            );

            throw $exception;
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                $isRenewal
                    ? 'تم إرسال دفعة تجديد اشتراك المهندس.'
                    : 'تم إرسال طلب الانضمام وإيصال الدفع.'
            );
    }

    public function index()
    {
        $applications =
            EngineerApplication::query()
                ->with([
                    'user.employeeProfile',
                    'specialty',
                ])
                ->latest()
                ->paginate(15);

        return view(
            'engineer-applications.index',
            compact('applications')
        );
    }

    public function approve(
        Request $request,
        EngineerApplication $engineerApplication
    ) {
        if ($engineerApplication->status === 'approved') {
            return back()->with(
                'success',
                'تمت الموافقة على هذا الطلب سابقًا.'
            );
        }

        if ($engineerApplication->status !== 'pending') {
            return back()->with(
                'error',
                'تمت معالجة هذا الطلب سابقًا.'
            );
        }

        $validated = $request->validate([
            'membership_days' => [
                'required',
                'integer',
                'min:1',
                'max:3650',
            ],

            'admin_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'membership_days.required' =>
                'حدد عدد أيام اشتراك المهندس.',

            'membership_days.integer' =>
                'عدد أيام الاشتراك يجب أن يكون رقمًا صحيحًا.',

            'membership_days.min' =>
                'مدة الاشتراك يجب أن تكون يومًا واحدًا على الأقل.',

            'membership_days.max' =>
                'الحد الأقصى لمدة الاشتراك هو 3650 يومًا.',
        ]);

        $adminNote = trim(
            (string) ($validated['admin_note'] ?? '')
        );

        if ($adminNote !== '') {
            $moderationResult =
                $this->moderationService->moderateText(
                    user: $request->user(),
                    text: $adminNote,
                    sourceType:
                        'engineer_application_approval_note',
                    sourceId:
                        $engineerApplication->id,
                    context: [
                        'content_section' =>
                            'engineer_application_review',

                        'recipient_role' =>
                            'engineer',
                    ]
                );

            if (! $moderationResult['allowed']) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $moderationResult[
                            'user_message'
                        ]
                    );
            }
        }

        $expiresAt = DB::transaction(
            function () use (
                $validated,
                $engineerApplication,
                $adminNote
            ) {
                $application =
                    EngineerApplication::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $engineerApplication->id
                        );

                $user = User::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $application->user_id
                    );

                if (
                    $user->engineer_active_until
                    && $user->engineer_active_until->isFuture()
                ) {
                    $startsAt =
                        $user->engineer_active_until->copy();
                } else {
                    $startsAt = now();
                }

                $expiresAt = $startsAt
                    ->copy()
                    ->addDays(
                        (int) $validated['membership_days']
                    );

                $application->update([
                    'payment_status' => 'paid',
                    'status' => 'approved',

                    'membership_days' =>
                        (int) $validated['membership_days'],

                    'membership_started_at' =>
                        $startsAt,

                    'membership_expires_at' =>
                        $expiresAt,

                    'approved_at' => now(),

                    'admin_note' =>
                        $adminNote !== ''
                            ? $adminNote
                            : null,
                ]);

                $user->update([
                    'role' => 'engineer',
                    'status' => 'active',

                    'engineer_membership_status' =>
                        'active',

                    'engineer_active_until' =>
                        $expiresAt,
                ]);

                $profile = EmployeeProfile::firstOrNew([
                    'user_id' => $user->id,
                ]);

                $profile->specialty_id =
                    $application->specialty_id;

                $profile->employee_number =
                    $profile->employee_number
                    ?: sprintf(
                        'EMP-%06d',
                        $user->id
                    );

                $profile->job_title =
                    $profile->job_title
                    ?: 'مهندس';

                $profile->salary =
                    $profile->salary ?? 0;

                $profile->hire_date =
                    $profile->hire_date
                    ?: now()->toDateString();

                $profile->save();

                return $expiresAt;
            }
        );

        $user = $engineerApplication
            ->user()
            ->first();

        $user?->notify(
            new SystemNotification(
                'تم تفعيل اشتراك المهندس',
                'تم تأكيد الدفع وتفعيل حساب المهندس حتى تاريخ '
                    . $expiresAt->format('Y-m-d H:i')
                    . '.',
                '/dashboard'
            )
        );

        return back()->with(
            'success',
            'تم قبول الدفع وتفعيل المهندس لمدة '
                . $validated['membership_days']
                . ' يوم.'
        );
    }

    public function reject(
        Request $request,
        EngineerApplication $engineerApplication
    ) {
        if ($engineerApplication->status !== 'pending') {
            return back()->with(
                'error',
                'تمت معالجة هذا الطلب سابقًا.'
            );
        }

        $validated = $request->validate([
            'admin_note' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'admin_note.required' =>
                'يجب كتابة سبب رفض الطلب.',
        ]);

        $adminNote = trim(
            $validated['admin_note']
        );

        $moderationResult =
            $this->moderationService->moderateText(
                user: $request->user(),
                text: $adminNote,
                sourceType:
                    'engineer_application_rejection_note',
                sourceId:
                    $engineerApplication->id,
                context: [
                    'content_section' =>
                        'engineer_application_review',

                    'recipient_role' =>
                        'engineer',
                ]
            );

        if (! $moderationResult['allowed']) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    $moderationResult[
                        'user_message'
                    ]
                );
        }

        $engineerApplication->update([
            'payment_status' => 'rejected',
            'status' => 'rejected',
            'admin_note' => $adminNote,
        ]);

        $engineerApplication
            ->user
            ?->notify(
                new SystemNotification(
                    'تم رفض دفعة اشتراك المهندس',
                    $adminNote,
                    '/dashboard'
                )
            );

        return back()->with(
            'success',
            'تم رفض الطلب.'
        );
    }
}
