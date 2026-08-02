<?php

namespace App\Http\Controllers;

use App\Models\SupportSetting;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim(
            (string) $request->input('q', '')
        );

        $tickets = SupportTicket::query()
            ->with([
                'user:id,name,email',
                'assignedEmployee:id,name,email',
                'latestMessage.sender:id,name',
            ])
            ->when(
                $request->filled('status'),
                fn (Builder $query) => $query->where(
                    'status',
                    $request->string('status')->toString()
                )
            )
            ->when(
                $search !== '',
                function (Builder $query) use ($search) {
                    $query->where(
                        function (Builder $builder) use ($search) {
                            $builder
                                ->where(
                                    'ticket_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'subject',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'user',
                                    function (Builder $userQuery) use ($search) {
                                        $userQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'assignedEmployee',
                                    function (Builder $employeeQuery) use ($search) {
                                        $employeeQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->latest('last_message_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.support.index',
            compact('tickets')
        );
    }

    public function settings(Request $request): View
    {
        $this->authorizeAdmin($request);

        $setting = SupportSetting::current();

        $employees = User::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
                'role',
                'status',
            ]);

        return view(
            'admin.support.settings',
            compact('setting', 'employees')
        );
    }

    public function updateSettings(
        Request $request
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'support_employee_id' => [
                'required',
                'exists:users,id',
            ],
        ]);

        $employee = User::query()
            ->whereKey(
                $validated['support_employee_id']
            )
            ->firstOrFail();

        $setting = SupportSetting::current();

        $oldEmployeeId =
            $setting->support_employee_id;

        $setting->update([
            'support_employee_id' => $employee->id,
            'updated_by' => $request->user()->id,
        ]);

        if (
            $oldEmployeeId
            && $oldEmployeeId !== $employee->id
        ) {
            SupportTicket::query()
                ->where(
                    'assigned_employee_id',
                    $oldEmployeeId
                )
                ->whereIn(
                    'status',
                    [
                        'open',
                        'in_progress',
                    ]
                )
                ->update([
                    'assigned_employee_id' =>
                        $employee->id,
                ]);
        }

        return back()->with(
            'success',
            'تم تعيين موظف الدعم الفني بنجاح.'
        );
    }

    private function authorizeAdmin(
        Request $request
    ): void {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );
    }
}
