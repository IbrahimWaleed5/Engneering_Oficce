<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficeOwner
{
    /**
     * السماح فقط لمالك المكتب الفعال.
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

        $ownerMembership = $user
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->where(
                'office_role',
                'owner'
            )
            ->first();

        abort_unless(
            $ownerMembership !== null,
            403,
            'هذا الإجراء مخصص لمالك المكتب فقط.'
        );

        abort_unless(
            $ownerMembership->office !== null,
            404,
            'المكتب الهندسي غير موجود.'
        );

        $request->attributes->set(
            'managed_office',
            $ownerMembership->office
        );

        $request->attributes->set(
            'office_owner_membership',
            $ownerMembership
        );

        return $next($request);
    }
}
