<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\User;

class SupportAssignmentService
{
    public function assignEmployee(
        SupportTicket $ticket
    ): ?User {
        $employee = User::query()
            ->where('role', 'employee')
            ->where('status', 'active')
            ->withCount([
                'assignedSupportTickets as active_tickets_count'
                    => function ($query) {
                        $query->whereIn('status', [
                            'open',
                            'in_progress',
                            'waiting_customer',
                        ]);
                    },
            ])
            ->orderBy('active_tickets_count')
            ->orderBy('id')
            ->first();

        if (! $employee) {
            $ticket->update([
                'support_mode' => 'waiting_employee',
                'assigned_employee_id' => null,
                'transferred_to_employee_at' => now(),
            ]);

            return null;
        }

        $ticket->update([
            'assigned_employee_id' => $employee->id,
            'support_mode' => 'employee',
            'status' => 'in_progress',
            'transferred_to_employee_at' => now(),
        ]);

        return $employee;
    }
}
