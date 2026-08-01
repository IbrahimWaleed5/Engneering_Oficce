<?php

namespace App\Http\Controllers;

use App\Events\ConsultationMessageSent;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Notifications\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConsultationMessageController extends Controller
{
    public function index(
        Consultation $consultation
    ): View {
        $this->authorize(
            'viewConversation',
            $consultation
        );

        $consultation->load([
            'customer',
            'engineer',
            'consultationType',
            'messages.sender',
        ]);

        return view(
            'consultations.messages',
            compact('consultation')
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
