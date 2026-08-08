<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailTwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Passkeys\Actions\GenerateVerificationOptions;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Laravel\Passkeys\Support\WebAuthn;

class EmailTwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $hasPasskeys = $user->passkeys()->exists();

        /*
         * لو Session تقول passkey لكن لم يعد للحساب Passkey،
         * ننتقل تلقائياً إلى البريد.
         */
        if (
            $request->session()->get('email_2fa.mode') === 'passkey'
            && ! $hasPasskeys
        ) {
            $request->session()->put('email_2fa.mode', 'email');
        }

        return view('auth.email-two-factor-challenge', [
            'mode' => $request->session()->get(
                'email_2fa.mode',
                $hasPasskeys ? 'passkey' : 'email'
            ),
            'hasPasskeys' => $hasPasskeys,
            'email' => $user->email,
        ]);
    }

    /**
     * خيارات WebAuthn الخاصة بالمستخدم الذي نجح بكلمة المرور.
     * مهم: الخيارات مربوطة بهذا المستخدم وليس بأي Passkey عشوائي.
     */
    public function passkeyOptions(
        Request $request,
        GenerateVerificationOptions $generate
    ): JsonResponse {
        $user = $this->pendingUserOrFail($request);

        abort_unless(
            $request->session()->get('email_2fa.mode') === 'passkey',
            403
        );

        abort_unless($user->passkeys()->exists(), 404);

        $options = $generate($user);

        $request->session()->put(
            'passkey.verification_options',
            WebAuthn::toJson($options)
        );

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    /**
     * التحقق من الـPasskey بدون قبول Passkey لحساب آخر.
     */
    public function verifyPasskey(
        PasskeyVerificationRequest $request,
        VerifyPasskey $verify
    ): JsonResponse {
        $user = $this->pendingUserOrFail($request);

        abort_unless(
            $request->session()->get('email_2fa.mode') === 'passkey',
            403
        );

        /*
         * تمرير $user هنا يجعل التحقق User-Bound.
         * أي Passkey لا يخص هذا المستخدم سيتم رفضه.
         */
        $verify(
            $request->credential(),
            $request->verificationOptions(),
            $user
        );

        $remember = (bool) $request->session()->pull(
            'email_2fa.remember',
            false
        );

        $intended = $request->session()->pull(
            'email_2fa.intended',
            route('dashboard', absolute: false)
        );

        $this->clearPendingChallenge($request);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return response()->json([
            'redirect' => $intended,
        ]);
    }

    /**
     * المستخدم لا يستطيع الوصول إلى البصمة / Face ID / PIN.
     * هنا فقط نرسل رمز البريد.
     */
    public function useEmail(
        Request $request,
        EmailTwoFactorService $service
    ): RedirectResponse {
        $user = $this->pendingUserOrFail($request);

        try {
            $service->send($user);
        } catch (ValidationException $exception) {
            /*
             * يوجد رمز حديث صالح.
             * لا نمنع المستخدم من الانتقال إلى شاشة إدخال الكود.
             */
        }

        $request->session()->put('email_2fa.mode', 'email');

        return redirect()
            ->route('email-2fa.challenge')
            ->with('success', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
    }

    /**
     * التحقق من Email OTP.
     */
    public function store(
        Request $request,
        EmailTwoFactorService $service
    ): RedirectResponse {
        abort_unless(
            $request->session()->get('email_2fa.mode') === 'email',
            403
        );

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $this->pendingUserOrFail($request);

        if (! $service->verify($user, $data['code'])) {
            return back()->withErrors([
                'code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.',
            ]);
        }

        $remember = (bool) $request->session()->pull(
            'email_2fa.remember',
            false
        );

        $intended = $request->session()->pull(
            'email_2fa.intended',
            route('dashboard', absolute: false)
        );

        $this->clearPendingChallenge($request);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->to($intended);
    }

    public function resend(
        Request $request,
        EmailTwoFactorService $service
    ): RedirectResponse {
        $user = $this->pendingUserOrFail($request);

        $request->session()->put('email_2fa.mode', 'email');

        $service->send($user);

        return back()->with(
            'success',
            'تم إرسال رمز تحقق جديد.'
        );
    }

    private function pendingUser(Request $request): ?User
    {
        $userId = (int) $request->session()->get('email_2fa.user_id');

        if ($userId <= 0) {
            return null;
        }

        return User::find($userId);
    }

    private function pendingUserOrFail(Request $request): User
    {
        $user = $this->pendingUser($request);

        abort_unless($user, 403);

        return $user;
    }

    private function clearPendingChallenge(Request $request): void
    {
        $request->session()->forget([
            'email_2fa.user_id',
            'email_2fa.mode',
            'passkey.verification_options',
        ]);
    }
}
