<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportSetting;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
{
    $user = $request->user();

    $setting = SupportSetting::query()
        ->with('supportEmployee:id,name,email,role,status')
        ->first();

    $tickets = SupportTicket::query()
        ->visibleTo($user)
        ->with([
            'user:id,name,email',
            'assignedEmployee:id,name,email',
            'latestMessage.sender:id,name',
        ])
        ->latest('last_message_at')
        ->latest()
        ->paginate(12);

    return view(
        'support.index',
        compact(
            'tickets',
            'setting'
        )
    );
}

    public function create(): View|RedirectResponse
    {
        $setting = SupportSetting::query()
            ->with('supportEmployee:id,name,status,role')
            ->first();

        if (! $setting?->supportEmployee) {
            return redirect()
                ->route('support.index')
                ->with(
                    'error',
                    'خدمة الدعم الفني غير متاحة حاليًا.'
                );
        }

        return view(
            'support.create',
            compact('setting')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'priority' => [
                'required',
                'in:low,medium,high,urgent',
            ],
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

        $setting = SupportSetting::query()
            ->with('supportEmployee')
            ->first();

        if (! $setting?->supportEmployee) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'لم يعيّن المدير موظف الدعم الفني بعد.'
                );
        }

        $ticket = DB::transaction(
            function () use (
                $request,
                $validated,
                $setting
            ) {
                $ticket = SupportTicket::create([
                    'ticket_number' =>
                        'SUP-' .
                        now()->format('YmdHis') .
                        '-' .
                        Str::upper(
                            Str::random(4)
                        ),
                    'user_id' => $request->user()->id,
                    'assigned_employee_id' =>
                        $setting->support_employee_id,
                    'subject' => $validated['subject'],
                    'priority' => $validated['priority'],
                    'status' => 'open',
                    'last_message_at' => now(),
                ]);

                $messageData = [
                    'support_ticket_id' => $ticket->id,
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

                    $messageData = array_merge(
                        $messageData,
                        [
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
                        ]
                    );
                }

                SupportMessage::create($messageData);

                return $ticket;
            }
        );

        return redirect()
            ->route('support.show', $ticket)
            ->with(
                'success',
                'تم فتح تذكرة الدعم بنجاح.'
            );
    }

    public function show(
        Request $request,
        SupportTicket $supportTicket
    ): View {
        $this->authorizeAccess(
            $request,
            $supportTicket
        );

        $supportTicket->load([
            'user:id,name,email,role',
            'assignedEmployee:id,name,email,role',
            'messages' => fn ($query) =>
                $query
                    ->with('sender:id,name,role')
                    ->oldest(),
        ]);

        SupportMessage::query()
            ->where(
                'support_ticket_id',
                $supportTicket->id
            )
            ->where(
                'sender_id',
                '!=',
                $request->user()->id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return view(
            'support.show',
            compact('supportTicket')
        );
    }

    public function updateStatus(
        Request $request,
        SupportTicket $supportTicket
    ): RedirectResponse {
        $this->authorizeEmployeeOrAdmin(
            $request,
            $supportTicket
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:open,in_progress,resolved,closed',
            ],
        ]);

        $updates = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'resolved') {
            $updates['resolved_at'] = now();
        }

        if ($validated['status'] === 'closed') {
            $updates['closed_at'] = now();
        }

        if (
            in_array(
                $validated['status'],
                ['open', 'in_progress'],
                true
            )
        ) {
            $updates['resolved_at'] = null;
            $updates['closed_at'] = null;
        }

        $supportTicket->update($updates);

        return back()->with(
            'success',
            'تم تحديث حالة التذكرة.'
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

    private function authorizeEmployeeOrAdmin(
        Request $request,
        SupportTicket $ticket
    ): void {
        $user = $request->user();

        abort_unless(
            $user->role === 'admin'
            || $ticket->assigned_employee_id === $user->id,
            403
        );
    }
    public function employeeIndex(Request $request): View
{
    $user = $request->user();

    abort_unless(
        $user->role === 'employee',
        403
    );

    $setting = SupportSetting::query()->first();

    abort_unless(
        $setting
        && $setting->support_employee_id === $user->id,
        403
    );

    $tickets = SupportTicket::query()
        ->where('assigned_employee_id', $user->id)
        ->with([
            'user:id,name,email',
            'assignedEmployee:id,name,email',
            'latestMessage.sender:id,name',
        ])
        ->latest('last_message_at')
        ->latest()
        ->paginate(12);

    return view(
        'employee.support.index',
        compact('tickets')
    );
}
}
