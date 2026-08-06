<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\EngineerReview;
use App\Notifications\SystemNotification;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\Request;

class EngineerReviewController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService
    ) {
    }

    public function create(
        Request $request,
        Consultation $consultation
    ) {
        $this->authorizeCustomerReview(
            $request,
            $consultation
        );

        if ($consultation->review()->exists()) {
            return redirect()
                ->route('consultations.mine')
                ->with(
                    'error',
                    'تم تقييم هذه الاستشارة سابقًا.'
                );
        }

        $consultation->load([
            'engineer',
            'consultationType',
        ]);

        return view(
            'engineer-reviews.create',
            compact('consultation')
        );
    }

    public function store(
        Request $request,
        Consultation $consultation
    ) {
        $this->authorizeCustomerReview(
            $request,
            $consultation
        );

        if ($consultation->review()->exists()) {
            return redirect()
                ->route('consultations.mine')
                ->with(
                    'error',
                    'تم تقييم هذه الاستشارة سابقًا.'
                );
        }

        $validated = $request->validate([
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'rating.required' =>
                'اختر عدد النجوم.',

            'rating.between' =>
                'التقييم يجب أن يكون بين نجمة وخمس نجوم.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | فحص تعليق التقييم قبل الحفظ
        |--------------------------------------------------------------------------
        */

        $comment = trim(
            (string) ($validated['comment'] ?? '')
        );

        if ($comment !== '') {
            $moderationResult =
                $this->moderationService->moderateText(
                    user: $request->user(),
                    text: $comment,
                    sourceType: 'engineer_review',
                    sourceId: $consultation->id,
                    context: [
                        'content_section' =>
                            'engineer_review',

                        'recipient_role' =>
                            'engineer',

                        'consultation_id' =>
                            $consultation->id,
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

        EngineerReview::create([
            'consultation_id' =>
                $consultation->id,

            'customer_id' =>
                $request->user()->id,

            'engineer_id' =>
                $consultation->engineer_id,

            'rating' =>
                $validated['rating'],

            'comment' =>
                $comment !== '' ? $comment : null,
        ]);

        $consultation->engineer?->notify(
            new SystemNotification(
                'وصلك تقييم جديد',
                'قام العميل بتقييمك بـ '
                    . $validated['rating']
                    . ' من 5 نجوم للاستشارة رقم '
                    . $consultation->consultation_number
                    . '.',
                '/engineers/'
                    . $consultation->engineer_id
            )
        );

        return redirect()
            ->route('consultations.mine')
            ->with(
                'success',
                'تم إرسال تقييم المهندس بنجاح.'
            );
    }

    private function authorizeCustomerReview(
        Request $request,
        Consultation $consultation
    ): void {
        abort_unless(
            (int) $consultation->customer_id
                === (int) $request->user()->id,
            403,
            'لا يمكنك تقييم هذه الاستشارة.'
        );

        abort_unless(
            $consultation->engineer_id !== null,
            403,
            'لا يوجد مهندس معيّن لهذه الاستشارة.'
        );

        abort_unless(
            $consultation->status === 'completed',
            403,
            'لا يمكن تقييم المهندس قبل اكتمال الاستشارة.'
        );

        abort_unless(
            $consultation->payment_status === 'paid',
            403,
            'لا يمكن تقييم استشارة غير مدفوعة.'
        );
    }
}
