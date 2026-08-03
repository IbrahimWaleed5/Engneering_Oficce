<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficeOperational
{
    /**
     * السماح بالدخول فقط عندما يكون المكتب فعالًا
     * واشتراكه الشهري ساريًا.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        abort_unless(
            $user !== null,
            401,
            'يجب تسجيل الدخول.'
        );

        $membership = $user
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->whereIn('office_role', [
                'owner',
                'manager',
            ])
            ->first();

        abort_unless(
            $membership !== null,
            403,
            'ليس لديك صلاحية إدارة مكتب هندسي.'
        );

        $office = $membership->office;

        abort_unless(
            $office !== null,
            404,
            'لم يتم العثور على المكتب.'
        );

        if ($office->status === 'suspended') {
            return redirect()
                ->route('office.dashboard')
                ->with(
                    'error',
                    'المكتب موقوف عن العمل ولا يمكن تنفيذ هذا الإجراء.'
                );
        }

        if ($office->status === 'closed') {
            return redirect()
                ->route('office.dashboard')
                ->with(
                    'error',
                    'المكتب مغلق ولا يمكن تنفيذ هذا الإجراء.'
                );
        }

        if ($office->status !== 'active') {
            return redirect()
                ->route('office.dashboard')
                ->with(
                    'error',
                    'المكتب غير فعال حاليًا.'
                );
        }

        $subscriptionIsActive =
            $office->subscription_status === 'active'
            && $office->subscription_ends_at !== null
            && $office->subscription_ends_at->isFuture();

        if (! $subscriptionIsActive) {
            return redirect()
                ->route('office.subscription')
                ->with(
                    'error',
                    'يجب تفعيل الاشتراك الشهري قبل تنفيذ هذا الإجراء.'
                );
        }

        /*
        | إتاحة المكتب والعضوية للكنترولر والـ View
        | دون الحاجة إلى تنفيذ استعلام جديد.
        */
        $request->attributes->set(
            'managed_office',
            $office
        );

        $request->attributes->set(
            'office_manager_membership',
            $membership
        );

        return $next($request);
    }
}
