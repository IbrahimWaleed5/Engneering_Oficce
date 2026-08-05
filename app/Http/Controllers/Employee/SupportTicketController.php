<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            in_array($user->role, ['employee', 'admin'], true),
            403
        );

        $tickets = SupportTicket::query()
            ->with([
                'user:id,name,email,phone',
                'assignedEmployee:id,name',
                'latestMessage',
            ])
            ->when(
                $user->role === 'employee',
                function ($query) use ($user) {
                    $query->where(function ($builder) use ($user) {
                        $builder
                            ->where(
                                'assigned_employee_id',
                                $user->id
                            )
                            ->orWhere(function ($waiting) {
                                $waiting
                                    ->whereNull('assigned_employee_id')
                                    ->where(
                                        'support_mode',
                                        'waiting_employee'
                                    );
                            });
                    });
                }
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')
                )
            )
            ->when(
                $request->filled('priority'),
                fn ($query) => $query->where(
                    'priority',
                    $request->string('priority')
                )
            )
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request->string('search');

                    $query->where(function ($builder) use ($search) {
                        $builder
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
                                fn ($userQuery) =>
                                    $userQuery->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                            );
                    });
                }
            )
            ->orderByRaw("
                CASE priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('last_message_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'employee.support-tickets.index',
            compact('tickets')
        );
    }

    public function show(
        Request $request,
        SupportTicket $ticket
    ): View {
        $this->authorizeEmployeeAccess(
            $request,
            $ticket
        );

        $ticket->load([
            'user:id,name,email,phone',
            'assignedEmployee:id,name',
            'messages' => fn ($query) =>
                $query
                    ->with('sender:id,name')
                    ->orderBy('id'),
        ]);

        $ticket->messages()
            ->whereNull('read_at')
            ->where('sender_type', 'customer')
            ->update([
                'read_at' => now(),
            ]);

        return view(
            'employee.support-tickets.show',
            compact('ticket')
        );
    }

    public function claim(
        Request $request,
        SupportTicket $ticket
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            in_array($user->role, ['employee', 'admin'], true),
            403
        );

        if (
            $ticket->assigned_employee_id &&
            $ticket->assigned_employee_id !== $user->id &&
            $user->role !== 'admin'
        ) {
            return back()->with(
                'error',
                'هذه التذكرة مسندة إلى موظف آخر.'
            );
        }

        DB::transaction(function () use ($ticket, $user) {
            $ticket->update([
                'assigned_employee_id' => $user->id,
                'support_mode' => 'employee',
                'status' => 'in_progress',
                'first_response_at' =>
                    $ticket->first_response_at ?? now(),
                'last_message_at' => now(),
            ]);

            SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => null,
                'sender_type' => 'system',
                'message' =>
                    "استلم موظف الدعم {$user->name} التذكرة.",
                'message_type' => 'text',
                'is_internal' => false,
            ]);
        });

        return redirect()
            ->route(
                'employee.support-tickets.show',
                $ticket
            )
            ->with(
                'success',
                'تم استلام التذكرة.'
            );
    }

    public function reply(
        Request $request,
        SupportTicket $ticket
    ): RedirectResponse {
        $this->authorizeEmployeeAccess(
            $request,
            $ticket
        );

        $data = $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
            'is_internal' => [
                'nullable',
                'boolean',
            ],
        ]);

        $user = $request->user();
        $isInternal = (bool) (
            $data['is_internal'] ?? false
        );

        DB::transaction(function () use (
            $ticket,
            $user,
            $data,
            $isInternal
        ) {
            SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => $user->id,
                'sender_type' =>
                    $user->role === 'admin'
                        ? 'admin'
                        : 'employee',
                'message' => $data['message'],
                'message_type' => 'text',
                'is_internal' => $isInternal,
            ]);

            $updates = [
                'last_message_at' => now(),
            ];

            if (! $isInternal) {
                $updates['support_mode'] = 'employee';
                $updates['status'] = 'waiting_customer';
                $updates['first_response_at'] =
                    $ticket->first_response_at ?? now();
            }

            $ticket->update($updates);
        });

        return back()->with(
            'success',
            $isInternal
                ? 'تمت إضافة الملاحظة الداخلية.'
                : 'تم إرسال الرد إلى العميل.'
        );
    }

    public function resolve(
        Request $request,
        SupportTicket $ticket
    ): RedirectResponse {
        $this->authorizeEmployeeAccess(
            $request,
            $ticket
        );

        DB::transaction(function () use (
            $request,
            $ticket
        ) {
            $ticket->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'last_message_at' => now(),
            ]);

            SupportMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => $request->user()->id,
                'sender_type' => 'system',
                'message' =>
                    'تم وضع التذكرة بحالة تم الحل.',
                'message_type' => 'text',
                'is_internal' => false,
            ]);
        });

        return back()->with(
            'success',
            'تم حل التذكرة.'
        );
    }

    public function close(
        Request $request,
        SupportTicket $ticket
    ): RedirectResponse {
        $this->authorizeEmployeeAccess(
            $request,
            $ticket
        );

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'last_message_at' => now(),
        ]);

        return back()->with(
            'success',
            'تم إغلاق التذكرة.'
        );
    }

    private function authorizeEmployeeAccess(
        Request $request,
        SupportTicket $ticket
    ): void {
        $user = $request->user();

        abort_unless(
            in_array($user->role, ['employee', 'admin'], true),
            403
        );

        if ($user->role === 'admin') {
            return;
        }

        abort_unless(
            $ticket->assigned_employee_id === $user->id ||
            (
                $ticket->assigned_employee_id === null &&
                $ticket->support_mode === 'waiting_employee'
            ),
            403
        );
    }
}
