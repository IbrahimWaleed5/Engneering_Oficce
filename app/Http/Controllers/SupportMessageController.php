<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportMessageController extends Controller
{
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
        ]);

        $data = [
            'support_ticket_id' => $supportTicket->id,
            'sender_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
            'message_type' => 'text',
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            $path = $file->store(
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
                'attachment_path' => $path,
                'attachment_name' =>
                    $file->getClientOriginalName(),
                'attachment_mime' =>
                    $file->getMimeType(),
                'attachment_size' =>
                    $file->getSize(),
            ]);
        }

        SupportMessage::create($data);

        $supportTicket->update([
            'last_message_at' => now(),
            'status' =>
                $supportTicket->status === 'open'
                    ? 'in_progress'
                    : $supportTicket->status,
        ]);

        return back();
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
}
