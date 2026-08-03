<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficeManager
{
    /**
     * السماح فقط لمالك المكتب أو مديره الفعّال.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'يجب تسجيل الدخول أولًا.'
                );
        }

        $managerMembership = $user
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->whereIn(
                'office_role',
                [
                    'owner',
                    'manager',
                ]
            )
            ->first();

        abort_unless(
            $managerMembership !== null,
            403,
            'هذا الإجراء مخصص لمالك المكتب أو مديره فقط.'
        );

        abort_unless(
            $managerMembership->office !== null,
            404,
            'المكتب الهندسي غير موجود.'
        );

        $request->attributes->set(
            'managed_office',
            $managerMembership->office
        );

        $request->attributes->set(
            'office_manager_membership',
            $managerMembership
        );

        return $next($request);
    }
}
