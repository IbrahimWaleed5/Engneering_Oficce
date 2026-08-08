<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\EmailTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): Response
    {
        $request->session()->regenerateToken();

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(
        LoginRequest $request,
        EmailTwoFactorService $emailTwoFactorService
    ): RedirectResponse {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        if ($user?->email_two_factor_enabled) {
            $remember = $request->boolean('remember');

            $intended = session()->pull(
                'url.intended',
                route('dashboard', absolute: false)
            );

            try {
                $emailTwoFactorService->send($user);
            } catch (ValidationException $exception) {
                // يوجد رمز حديث صالح؛ نكمل لصفحة التحقق.
            }

            Auth::guard('web')->logout();

            $request->session()->put([
                'email_2fa.user_id' => $user->id,
                'email_2fa.remember' => $remember,
                'email_2fa.intended' => $intended,
            ]);

            return redirect()->route('email-2fa.challenge');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }
}
