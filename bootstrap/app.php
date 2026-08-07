<?php

use App\Http\Middleware\EnsureUserIsNotSuspended;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cookie;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(
        function (Middleware $middleware): void {
            $middleware->trustProxies(at: '*');

            $middleware->alias([
                'role' =>
                    RoleMiddleware::class,

                'office.operational' =>
                    \App\Http\Middleware\EnsureOfficeOperational::class,

                'not.suspended' =>
                    EnsureUserIsNotSuspended::class,
            ]);
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions): void {
            /*
             * لا نعرض صفحة 419 للمستخدم.
             *
             * إذا انتهت جلسة CSRF أو كانت هناك Cookie قديمة على الهاتف،
             * نمسح Cookie الجلسة القديمة ونرجع لصفحة تسجيل الدخول
             * برسالة واضحة.
             */
            $exceptions->render(
                function (
                    TokenMismatchException $exception,
                    Request $request
                ) {
                    if (
                        $request->expectsJson()
                        || $request->ajax()
                    ) {
                        return response()->json([
                            'success' => false,
                            'session_expired' => true,
                            'message' =>
                                'انتهت صلاحية الجلسة. حدّث الصفحة وحاول مرة أخرى.',
                        ], 419);
                    }

                    $response = redirect()
                        ->route('login')
                        ->with(
                            'error',
                            'انتهت صلاحية الجلسة وتم تحديثها. سجّل الدخول مرة أخرى.'
                        );

                    $response->withCookie(
                        Cookie::forget(
                            config('session.cookie'),
                            config('session.path', '/'),
                            config('session.domain')
                        )
                    );

                    return $response;
                }
            );
        }
    )
    ->create();
