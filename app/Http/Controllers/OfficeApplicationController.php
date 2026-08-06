<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfficeApplicationRequest;
use App\Models\OfficeApplication;
use App\Services\AttachmentModerationService;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OfficeApplicationController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService,
        private readonly AttachmentModerationService $attachmentModerationService
    ) {
    }

    public function create(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->ownedOffice()->exists()) {
            return redirect()
                ->route('office.dashboard')
                ->with(
                    'info',
                    'لديك مكتب مسجل بالفعل.'
                );
        }

        $pendingApplication = OfficeApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingApplication) {
            return redirect()
                ->route('office-applications.status')
                ->with(
                    'info',
                    'لديك طلب مكتب قيد المراجعة بالفعل.'
                );
        }

        return view('office-applications.create');
    }

    public function store(
        StoreOfficeApplicationRequest $request
    ): RedirectResponse {
        $user = $request->user();

        $hasPendingApplication = OfficeApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApplication) {
            return redirect()
                ->route('office-applications.status')
                ->with(
                    'info',
                    'لديك طلب مكتب قيد المراجعة بالفعل.'
                );
        }

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | فحص البيانات النصية العامة قبل رفع الملفات
        |--------------------------------------------------------------------------
        */

        $contentToModerate = collect([
            'office_name' =>
                $validated['office_name'] ?? null,

            'country' =>
                $validated['country'] ?? null,

            'city' =>
                $validated['city'] ?? null,

            'address' =>
                $validated['address'] ?? null,

            'notes' =>
                $validated['notes'] ?? null,
        ])
            ->filter(
                fn ($value) =>
                    is_string($value)
                    && trim($value) !== ''
            )
            ->map(
                fn ($value, $field) =>
                    $field . ': ' . trim($value)
            )
            ->implode("\n");

        if ($contentToModerate !== '') {
            $moderationResult =
                $this->moderationService->moderateText(
                    user: $user,
                    text: $contentToModerate,
                    sourceType:
                        'office_application',
                    sourceId: null,
                    context: [
                        'content_section' =>
                            'office_application',

                        'recipient_role' =>
                            'admin',
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

        /*
        |--------------------------------------------------------------------------
        | فحص مرفقات طلب المكتب قبل التخزين
        |--------------------------------------------------------------------------
        */

        $attachments = [
            'commercial_registration_file' =>
                'office_application_commercial_registration',

            'license_document' =>
                'office_application_license_document',

            'payment_receipt' =>
                'office_application_payment_receipt',
        ];

        foreach (
            $attachments
            as $field => $sourceType
        ) {
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
                                'office_application',

                            'recipient_role' =>
                                'admin',
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

        $baseDirectory =
            'office-applications/' . $user->id;

        $commercialRegistrationPath = null;
        $licenseDocumentPath = null;
        $paymentReceiptPath = null;

        try {
            $commercialRegistrationPath = $request
                ->file(
                    'commercial_registration_file'
                )
                ->store(
                    $baseDirectory
                    . '/commercial-registration'
                );

            $licenseDocumentPath = $request
                ->file('license_document')
                ->store(
                    $baseDirectory . '/licenses'
                );

            $paymentReceiptPath = $request
                ->file('payment_receipt')
                ->store(
                    $baseDirectory
                    . '/payment-receipts'
                );

            OfficeApplication::create([
                'user_id' =>
                    $user->id,

                'office_name' =>
                    $validated['office_name'],

                'email' =>
                    $validated['email'],

                'phone' =>
                    $validated['phone'],

                'commercial_registration' =>
                    $validated[
                        'commercial_registration'
                    ],

                'license_number' =>
                    $validated['license_number'],

                'country' =>
                    $validated['country'],

                'city' =>
                    $validated['city'],

                'address' =>
                    $validated['address'],

                'notes' =>
                    $validated['notes'] ?? null,

                'commercial_registration_path' =>
                    $commercialRegistrationPath,

                'license_document_path' =>
                    $licenseDocumentPath,

                'payment_method' =>
                    $validated['payment_method'],

                'payment_reference' =>
                    $validated['payment_reference']
                        ?? null,

                'payment_receipt_path' =>
                    $paymentReceiptPath,

                'paid_at' =>
                    now(),

                'status' =>
                    'pending',
            ]);
        } catch (\Throwable $exception) {
            Storage::delete(
                array_filter([
                    $commercialRegistrationPath,
                    $licenseDocumentPath,
                    $paymentReceiptPath,
                ])
            );

            throw $exception;
        }

        return redirect()
            ->route('office-applications.status')
            ->with(
                'success',
                'تم إرسال طلب المكتب وإيصال اشتراك 300 دولار بنجاح، وسيقوم مدير النظام بمراجعتهما.'
            );
    }

    public function status(): View
    {
        $application = OfficeApplication::query()
            ->where(
                'user_id',
                request()->user()->id
            )
            ->with('reviewer')
            ->latest()
            ->first();

        return view(
            'office-applications.status',
            compact('application')
        );
    }
}
