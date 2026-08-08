<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailTwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('email_2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.email-two-factor-challenge');
    }

    public function store(Request $request, EmailTwoFactorService $service): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::findOrFail(
            (int) $request->session()->get('email_2fa.user_id')
        );

        if (! $service->verify($user, $data['code'])) {
            return back()->withErrors([
                'code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ]);
        }

        $remember = (bool) $request->session()->pull('email_2fa.remember', false);
        $intended = $request->session()->pull(
            'email_2fa.intended',
            route('dashboard', absolute: false)
        );

        $request->session()->forget('email_2fa.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->to($intended);
    }

    public function resend(Request $request, EmailTwoFactorService $service): RedirectResponse
    {
        $userId = (int) $request->session()->get('email_2fa.user_id');
        abort_unless($userId > 0, 403);

        $service->send(User::findOrFail($userId));

        return back()->with('success', 'تم إرسال رمز تحقق جديد.');
    }
}
