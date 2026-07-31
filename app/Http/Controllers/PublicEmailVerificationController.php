<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PublicEmailVerificationController extends Controller
{
    /**
     * تأكيد البريد من رابط موقّع بدون اشتراط تسجيل الدخول.
     */
    public function verify(
        Request $request,
        int $id,
        string $hash
    ): RedirectResponse {
        $user = User::query()->findOrFail($id);

        abort_unless(
            hash_equals(
                sha1($user->getEmailForVerification()),
                $hash
            ),
            403,
            'رابط التأكيد لا يخص هذا البريد الإلكتروني.'
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        /*
         * إذا كان الهاتف مسجلًا بحساب آخر،
         * نسجل خروجه حتى لا يظهر البريد الخطأ.
         */
        if (
            Auth::check()
            && (int) Auth::id() !== (int) $user->getKey()
        ) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()
            ->route('verification.public.success')
            ->with('verified_email', $user->email);
    }

    /**
     * صفحة نجاح التأكيد على الهاتف.
     */
    public function success(Request $request): View
    {
        return view(
            'auth.email-verified-success',
            [
                'email' => (string) $request
                    ->session()
                    ->get('verified_email', ''),
            ]
        );
    }

    /**
     * يفحص الكمبيوتر حالة البريد من قاعدة البيانات.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user()?->fresh();

        return response()
            ->json([
                'verified' => (bool) (
                    $user?->hasVerifiedEmail()
                    ?? false
                ),
            ])
            ->withHeaders([
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' => 'no-cache',
            ]);
    }
}
