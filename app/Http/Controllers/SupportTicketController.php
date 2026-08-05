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
            compact('tickets', 'setting')
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

    public function store(Request $request): RedirectResponse
    {
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
                        Str::upper(Str::random(4)),
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
            $updates['closed_at'] = null;
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

    public function escalateToAdmin(
        Request $request,
        SupportTicket $supportTicket
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            (int) $supportTicket->assigned_employee_id === (int) $user->id,
            403,
            'موظف الدعم الفني المسؤول فقط يستطيع تحويل التذكرة إلى المدير.'
        );

        abort_if(
            (bool) $supportTicket->is_escalated,
            422,
            'تم تحويل هذه التذكرة إلى المدير مسبقًا.'
        );

        $validated = $request->validate([
            'escalation_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'escalation_reason.required' =>
                'يرجى كتابة سبب تحويل المشكلة إلى المدير.',
        ]);

        $supportTicket->update([
            'is_escalated' => true,
            'escalated_by' => $user->id,
            'escalated_at' => now(),
            'escalation_reason' =>
                $validated['escalation_reason'],
            'status' => 'in_progress',
        ]);

        return back()->with(
            'success',
            'تم تحويل المشكلة إلى المدير بنجاح.'
        );
    }

    public function employeeIndex(Request $request): View
    {
        $user = $request->user();

        $setting = SupportSetting::query()->first();

        abort_unless(
            $setting
            && (int) $setting->support_employee_id === (int) $user->id,
            403
        );

        $baseQuery = SupportTicket::query()
            ->where('assigned_employee_id', $user->id);

        $allTicketsCount = (clone $baseQuery)->count();

        $openTicketsCount = (clone $baseQuery)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $resolvedTicketsCount = (clone $baseQuery)
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $urgentTicketsCount = (clone $baseQuery)
            ->where('priority', 'urgent')
            ->count();

        $ticketsQuery = (clone $baseQuery)
            ->with([
                'user:id,name,email',
                'assignedEmployee:id,name,email',
                'latestMessage.sender:id,name',
            ]);

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $ticketsQuery->where(
                function ($query) use ($search) {
                    $query
                        ->where(
                            'ticket_number',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'subject',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'user',
                            function ($userQuery) use ($search) {
                                $userQuery
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        if ($request->filled('status')) {
            $request->validate([
                'status' => [
                    'in:open,in_progress,resolved,closed',
                ],
            ]);

            $ticketsQuery->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('priority')) {
            $request->validate([
                'priority' => [
                    'in:low,medium,high,urgent',
                ],
            ]);

            $ticketsQuery->where(
                'priority',
                $request->input('priority')
            );
        }

        $tickets = $ticketsQuery
            ->latest('last_message_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view(
            'employee.support.index',
            compact(
                'tickets',
                'allTicketsCount',
                'openTicketsCount',
                'resolvedTicketsCount',
                'urgentTicketsCount'
            )
        );
    }

    private function authorizeAccess(
        Request $request,
        SupportTicket $ticket
    ): void {
        $user = $request->user();

        $isOwner =
            (int) $ticket->user_id === (int) $user->id;

        $isAssignedSupportEmployee =
            (int) $ticket->assigned_employee_id === (int) $user->id;

        $isEscalatedAdmin =
            $user->role === 'admin'
            && (bool) $ticket->is_escalated;

        abort_unless(
            $isOwner
            || $isAssignedSupportEmployee
            || $isEscalatedAdmin,
            403,
            'لا تملك صلاحية الوصول إلى هذه التذكرة.'
        );
    }

    private function authorizeEmployeeOrAdmin(
        Request $request,
        SupportTicket $ticket
    ): void {
        $user = $request->user();

        $isAssignedSupportEmployee =
            (int) $ticket->assigned_employee_id === (int) $user->id;

        $isEscalatedAdmin =
            $user->role === 'admin'
            && (bool) $ticket->is_escalated;

        abort_unless(
            $isAssignedSupportEmployee || $isEscalatedAdmin,
            403,
            'لا تملك صلاحية إدارة هذه التذكرة.'
        );
    }
}
