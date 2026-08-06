<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportMessageController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService
    ) {
    }

    public function store(
        Request $request,
        SupportTicket $supportTicket
    ): RedirectResponse {
        $this->authorizeAccess(
            $request,
            $supportTicket
        );

        abort_if(
            $supportTicket->status === 'closed',
            422,
            'لا يمكن إرسال رسالة في تذكرة مغلقة.'
        );

        $validated = $request->validate([
            'message' => [
                'required_without:attachment',
                'nullable',
                'string',
                'max:5000',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,zip',
                'max:10240',
            ],
        ], [
            'message.required_without' =>
                'اكتب رسالة أو أرفق ملفًا.',

            'attachment.max' =>
                'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',

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
            $recipientRole = $this->getRecipientRole(
                $request,
                $supportTicket
            );

            $moderationResult =
                $this->moderationService->moderateText(
                    user: $request->user(),
                    text: $messageText,
                    sourceType: 'support_message',
                    sourceId: null,
                    context: [
                        'conversation_type' =>
                            'support_ticket',

                        'recipient_role' =>
                            $recipientRole,

                        'enforce_off_platform_for_all' =>
                            false,
                    ]
                );

            if (! $moderationResult['allowed']) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $moderationResult['user_message']
                    );
            }
        }

        $data = [
            'support_ticket_id' =>
                $supportTicket->id,

            'sender_id' =>
                $request->user()->id,

            'message' =>
                $validated['message'] ?? null,

            'message_type' =>
                'text',
        ];

        $uploadedPath = null;

        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                $uploadedPath = $file->store(
                    'support-attachments',
                    'local'
                );

                $data = array_merge($data, [
                    'message_type' =>
                        str_starts_with(
                            (string) $file->getMimeType(),
                            'image/'
                        )
                            ? 'image'
                            : 'file',

                    'attachment_path' =>
                        $uploadedPath,

                    'attachment_name' =>
                        $file->getClientOriginalName(),

                    'attachment_mime' =>
                        $file->getMimeType(),

                    'attachment_size' =>
                        $file->getSize(),
                ]);
            }

            SupportMessage::create($data);
        } catch (\Throwable $exception) {
            if ($uploadedPath) {
                Storage::disk('local')->delete(
                    $uploadedPath
                );
            }

            throw $exception;
        }

        $supportTicket->update([
            'last_message_at' => now(),

            'status' =>
                $supportTicket->status === 'open'
                    ? 'in_progress'
                    : $supportTicket->status,
        ]);

        return back()->with(
            'success',
            'تم إرسال الرسالة.'
        );
    }

    public function attachment(
        Request $request,
        SupportMessage $supportMessage
    ): StreamedResponse {
        $supportMessage->loadMissing('ticket');

        $this->authorizeAccess(
            $request,
            $supportMessage->ticket
        );

        abort_unless(
            $supportMessage->attachment_path
            && Storage::disk('local')->exists(
                $supportMessage->attachment_path
            ),
            404
        );

        return Storage::disk('local')->download(
            $supportMessage->attachment_path,
            $supportMessage->attachment_name
                ?? 'attachment'
        );
    }

    private function authorizeAccess(
        Request $request,
        SupportTicket $ticket
    ): void {
        $user = $request->user();

        abort_unless(
            $user->role === 'admin'
            || $ticket->user_id === $user->id
            || $ticket->assigned_employee_id === $user->id,
            403
        );
    }

    private function getRecipientRole(
        Request $request,
        SupportTicket $ticket
    ): ?string {
        $user = $request->user();

        if (
            (int) $ticket->user_id
            === (int) $user->id
        ) {
            return $ticket->assigned_employee_id
                ? 'employee'
                : 'admin';
        }

        if (
            (int) $ticket->assigned_employee_id
            === (int) $user->id
        ) {
            return $ticket->user?->role
                ?? 'customer';
        }

        if ($user->role === 'admin') {
            return $ticket->user?->role
                ?? 'customer';
        }

        return null;
    }
}
