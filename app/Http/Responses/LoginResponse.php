<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | منع الحسابات غير النشطة
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->status)
            && $user->status !== 'active'
        ) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'هذا الحساب غير نشط. تواصل مع إدارة الموقع.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | السماح بالعودة إلى رابط تأكيد البريد
        |--------------------------------------------------------------------------
        |
        | عندما يفتح المستخدم رابط التأكيد من الجوال وهو غير مسجل،
        | يحفظ Laravel الرابط داخل url.intended.
        |
        */

        $intendedUrl = (string) $request
            ->session()
            ->pull('url.intended', '');

        if ($intendedUrl !== '') {
            $intendedPath = (string) parse_url(
                $intendedUrl,
                PHP_URL_PATH
            );

            $intendedHost = parse_url(
                $intendedUrl,
                PHP_URL_HOST
            );

            $applicationHost = parse_url(
                (string) config('app.url'),
                PHP_URL_HOST
            );

            $isVerificationUrl = str_starts_with(
                $intendedPath,
                '/email/verify/'
            );

            $isApplicationHost =
                empty($intendedHost)
                || $intendedHost === $applicationHost;

            if (
                $isVerificationUrl
                && $isApplicationHost
            ) {
                return redirect()->to($intendedUrl);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | البريد غير مؤكد
        |--------------------------------------------------------------------------
        */

        if (
            method_exists($user, 'hasVerifiedEmail')
            && ! $user->hasVerifiedEmail()
        ) {
            return redirect()->route(
                'verification.notice'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | التحويل بعد تسجيل الدخول
        |--------------------------------------------------------------------------
        */

        return redirect()->route('dashboard');
    }
}
