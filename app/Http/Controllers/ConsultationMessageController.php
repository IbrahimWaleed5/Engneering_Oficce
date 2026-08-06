<?php

namespace App\Http\Controllers;

use App\Events\ConsultationMessageSent;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Notifications\SystemNotification;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConsultationMessageController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService
    ) {
    }

    public function index(
        Request $request,
        Consultation $consultation
    ) {
        $conversation = $consultation->conversation;

        if (! $conversation) {
            return back()->with(
                'error',
                'لم يتم إنشاء محادثة لهذه الاستشارة بعد.'
            );
        }

        return redirect()->route(
            'conversations.show',
            $conversation
        );
    }

    public function store(
        Request $request,
        Consultation $consultation
    ): RedirectResponse|JsonResponse {
        $this->authorize(
            'sendMessage',
            $consultation
        );

        $validated = $request->validate([
            'message' => [
                'nullable',
                'string',
                'max:5000',
                'required_without:attachment',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp,dwg,doc,docx,xls,xlsx,zip',
                'max:20480',
                'required_without:message',
            ],
        ], [
            'message.required_without' =>
                'اكتب رسالة أو أرفق ملفًا.',

            'attachment.required_without' =>
                'اكتب رسالة أو أرفق ملفًا.',

            'attachment.max' =>
                'حجم الملف يجب ألا يتجاوز 20 ميجابايت.',

            'attachment.mimes' =>
                'نوع الملف المرفق غير مسموح.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | فحص النص قبل رفع الملف أو حفظ الرسالة
        |--------------------------------------------------------------------------
        */

        $messageText = trim(
            (string) ($validated['message'] ?? '')
        );

        if ($messageText !== '') {
            $recipient = $this->getRecipient(
                $request,
                $consultation
            );

            $moderationResult =
                $this->moderationService->moderateText(
                    user: $request->user(),
                    text: $messageText,
                    sourceType: 'consultation_message',
                    sourceId: null,
                    context: [
                        'conversation_type' =>
                            'consultation',

                        'recipient_role' =>
                            $recipient?->role,
                    ]
                );

            if (! $moderationResult['allowed']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'decision' =>
                            $moderationResult['decision'],
                        'risk_level' =>
                            $moderationResult['risk_level'],
                        'category' =>
                            $moderationResult['category'],
                        'warning_issued' =>
                            $moderationResult['warning_issued'],
                        'account_suspended' =>
                            $moderationResult['account_suspended'],
                        'message' =>
                            $moderationResult['user_message'],
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $moderationResult['user_message']
                    );
            }
        }

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request
                ->file('attachment')
                ->store(
                    'consultation-messages',
                    'private'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | حفظ الرسالة
        |--------------------------------------------------------------------------
        |
        | إذا فشل حفظ الرسالة، يتم حذف المرفق الذي رُفع قبل الحفظ.
        |
        */

        try {
            $message = ConsultationMessage::create([
                'consultation_id' =>
                    $consultation->id,

                'sender_id' =>
                    $request->user()->id,

                'message' =>
                    $validated['message'] ?? null,

                'attachment' =>
                    $attachmentPath,
            ]);

            $message->load('sender');
        } catch (\Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('private')
                    ->delete($attachmentPath);
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | البث الفوري
        |--------------------------------------------------------------------------
        |
        | فشل Reverb لا يجب أن يمنع حفظ الرسالة أو إرجاع نجاح للمستخدم.
        |
        */

        try {
            broadcast(
                new ConsultationMessageSent($message)
            )->toOthers();
        } catch (\Throwable $exception) {
            Log::error(
                'Realtime message broadcast failed',
                [
                    'consultation_id' =>
                        $consultation->id,

                    'message_id' =>
                        $message->id,

                    'error' =>
                        $exception->getMessage(),
                ]
            );
        }

        $consultation->load([
            'customer',
            'engineer',
        ]);

        $recipient = $this->getRecipient(
            $request,
            $consultation
        );

        if ($recipient) {
            try {
                $recipient->notify(
                    new SystemNotification(
                        'رسالة جديدة في الاستشارة',
                        'وصلتك رسالة جديدة في الاستشارة رقم '
                            . $consultation->consultation_number
                            . ' من '
                            . $request->user()->name
                            . '.',
                        route(
                            'consultations.messages.index',
                            $consultation
                        )
                    )
                );
            } catch (\Throwable $exception) {
                Log::error(
                    'Consultation message notification failed',
                    [
                        'consultation_id' =>
                            $consultation->id,

                        'message_id' =>
                            $message->id,

                        'recipient_id' =>
                            $recipient->id,

                        'error' =>
                            $exception->getMessage(),
                    ]
                );
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' =>
                    (new ConsultationMessageSent($message))
                        ->broadcastWith()['message'],
            ], 201);
        }

        return back()->with(
            'success',
            'تم إرسال الرسالة.'
        );
    }

    private function getRecipient(
        Request $request,
        Consultation $consultation
    ): ?\App\Models\User {
        $user = $request->user();

        if (
            (int) $user->id
            === (int) $consultation->customer_id
        ) {
            return $consultation->engineer;
        }

        if (
            (int) $user->id
            === (int) $consultation->engineer_id
        ) {
            return $consultation->customer;
        }

        return null;
    }
}
