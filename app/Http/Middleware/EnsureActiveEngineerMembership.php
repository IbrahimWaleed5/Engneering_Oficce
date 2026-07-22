<?php

namespace App\Http\Middleware;

use App\Notifications\SystemNotification;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveEngineerMembership
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        abort_unless(
            $user->role === 'engineer',
            403
        );

        if ($user->hasActiveEngineerMembership()) {
            return $next($request);
        }

        if (
            $user->engineer_membership_status === 'active'
        ) {
            $user->update([
                'engineer_membership_status' =>
                    'inactive',
            ]);

            $user->notify(
                new SystemNotification(
                    'انتهى اشتراك المهندس',
                    'انتهت مدة تفعيل حسابك كمهندس. ارفع دفعة جديدة لإعادة التفعيل.',
                    '/dashboard'
                )
            );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'error',
                'حساب المهندس غير نشط. يجب تجديد الاشتراك.'
            );
    }
}
