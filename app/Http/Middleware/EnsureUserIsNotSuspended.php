<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    /**
     * المسارات المسموح بها للحساب المعلّق.
     */
    private const ALLOWED_ROUTE_PATTERNS = [
        'logout',
        'moderation.appeal.*',
    ];

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $this->isSuspended($user->status)) {
            return $next($request);
        }

        if ($this->isAllowedRoute($request)) {
            return $next($request);
        }

        if (
            $request->expectsJson()
            || $request->is('api/*')
        ) {
            return new JsonResponse([
                'success' => false,
                'account_suspended' => true,
                'message' =>
                    'حسابك معلّق بانتظار المراجعة. يمكنك تقديم طعن من صفحة الاعتراض.',
                'appeal_url' =>
                    route('moderation.appeal.create'),
            ], 403);
        }

        return new RedirectResponse(
            route('moderation.appeal.create')
        );
    }

    private function isSuspended(
        ?string $status
    ): bool {
        return in_array(
            $status,
            [
                'suspended',
                'suspended_pending_review',
            ],
            true
        );
    }

    private function isAllowedRoute(
        Request $request
    ): bool {
        $routeName = $request->route()?->getName();

        if (! is_string($routeName)) {
            return false;
        }

        foreach (
            self::ALLOWED_ROUTE_PATTERNS
            as $pattern
        ) {
            if (Str::is($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }
}
