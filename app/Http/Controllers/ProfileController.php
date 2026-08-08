<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\AttachmentModerationService;
use App\Services\EmailTwoFactorService;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService,
        private readonly AttachmentModerationService $attachmentModerationService
    ) {
    }

    /**
     * عرض صفحة البيانات الشخصية.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * عرض صفحة تغيير كلمة المرور.
     */
    public function editPassword(
        Request $request
    ): View {
        return view('profile.password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * عرض صفحة الأمان وتسجيل الدخول.
     */
    public function security(
        Request $request
    ): View {
        return view('profile.security', [
            'user' => $request->user(),
        ]);
    }

    /**
     * عرض صفحة حذف الحساب.
     */
    public function deleteAccount(
        Request $request
    ): View {
        return view('profile.delete', [
            'user' => $request->user(),
        ]);
    }

    /**
     * إرسال رمز لتفعيل التحقق بخطوتين عبر البريد.
     */
    public function enableEmailTwoFactor(
        Request $request,
        EmailTwoFactorService $emailTwoFactorService
    ): RedirectResponse {
        $request->validateWithBag(
            'emailTwoFactor',
            [
                'current_password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'current_password.required' =>
                    'أدخل كلمة المرور الحالية أولًا.',
            ]
        );

        $user = $request->user();

        if (
            ! Hash::check(
                (string) $request->input('current_password'),
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'current_password' =>
                    'كلمة المرور الحالية غير صحيحة.',
            ])->errorBag('emailTwoFactor');
        }

        if ($user->email_two_factor_enabled) {
            return Redirect::route('profile.security')
                ->with(
                    'security-success',
                    'التحقق بخطوتين مفعّل بالفعل.'
                );
        }

        $emailTwoFactorService->send($user);

        $request->session()->put(
            'email_2fa_setup_user_id',
            $user->id
        );

        return Redirect::route('profile.security')
            ->with(
                'email_2fa_setup_pending',
                true
            )
            ->with(
                'security-success',
                'تم إرسال رمز التحقق إلى بريدك الإلكتروني.'
            );
    }

    /**
     * تأكيد رمز التفعيل وتشغيل Email 2FA.
     */
    public function confirmEmailTwoFactor(
        Request $request,
        EmailTwoFactorService $emailTwoFactorService
    ): RedirectResponse {
        $request->validateWithBag(
            'emailTwoFactor',
            [
                'code' => [
                    'required',
                    'digits:6',
                ],
            ],
            [
                'code.required' =>
                    'أدخل رمز التحقق.',
                'code.digits' =>
                    'رمز التحقق يجب أن يتكون من 6 أرقام.',
            ]
        );

        $user = $request->user();

        if (
            (int) $request->session()->get(
                'email_2fa_setup_user_id'
            ) !== (int) $user->id
        ) {
            return Redirect::route('profile.security')
                ->with(
                    'error',
                    'انتهت جلسة التفعيل. ابدأ عملية التفعيل من جديد.'
                );
        }

        if (
            ! $emailTwoFactorService->verify(
                $user,
                (string) $request->input('code')
            )
        ) {
            return Redirect::route('profile.security')
                ->withErrors(
                    [
                        'code' =>
                            'رمز التحقق غير صحيح أو منتهي الصلاحية.',
                    ],
                    'emailTwoFactor'
                )
                ->with(
                    'email_2fa_setup_pending',
                    true
                );
        }

        $user->forceFill([
            'email_two_factor_enabled' => true,
            'email_two_factor_verified_at' => now(),
        ])->save();

        $request->session()->forget(
            'email_2fa_setup_user_id'
        );

        return Redirect::route('profile.security')
            ->with(
                'security-success',
                'تم تفعيل التحقق بخطوتين عبر البريد الإلكتروني بنجاح.'
            );
    }

    /**
     * إعادة إرسال رمز تفعيل Email 2FA.
     */
    public function resendEmailTwoFactor(
        Request $request,
        EmailTwoFactorService $emailTwoFactorService
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            (int) $request->session()->get(
                'email_2fa_setup_user_id'
            ) === (int) $user->id,
            403
        );

        $emailTwoFactorService->send($user);

        return Redirect::route('profile.security')
            ->with(
                'email_2fa_setup_pending',
                true
            )
            ->with(
                'security-success',
                'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني.'
            );
    }

    /**
     * تعطيل التحقق بخطوتين عبر البريد.
     */
    public function disableEmailTwoFactor(
        Request $request
    ): RedirectResponse {
        $request->validateWithBag(
            'emailTwoFactorDisable',
            [
                'current_password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'current_password.required' =>
                    'أدخل كلمة المرور الحالية للتأكيد.',
            ]
        );

        $user = $request->user();

        if (
            ! Hash::check(
                (string) $request->input('current_password'),
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'current_password' =>
                    'كلمة المرور الحالية غير صحيحة.',
            ])->errorBag('emailTwoFactorDisable');
        }

        $user->forceFill([
            'email_two_factor_enabled' => false,
            'email_two_factor_verified_at' => null,
        ])->save();

        $request->session()->forget(
            'email_2fa_setup_user_id'
        );

        return Redirect::route('profile.security')
            ->with(
                'security-success',
                'تم تعطيل التحقق بخطوتين عبر البريد الإلكتروني.'
            );
    }

    /**
     * تحديث البيانات الشخصية.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $user = $request->user();

        $validated = $request->validated();

        unset($validated['profile_photo']);

        $excludedModerationFields = [
            'email',
            'email_confirmation',
            'password',
            'password_confirmation',
            'current_password',
        ];

        $contentToModerate = collect($validated)
            ->except($excludedModerationFields)
            ->filter(
                fn ($value) =>
                    is_string($value)
                    && trim($value) !== ''
            )
            ->map(
                fn ($value, $field) =>
                    $field . ': ' . trim($value)
            )
            ->implode("\n");

        if ($contentToModerate !== '') {
            $moderationResult =
                $this->moderationService->moderateText(
                    user: $user,
                    text: $contentToModerate,
                    sourceType: 'user_profile',
                    sourceId: $user->id,
                    context: [
                        'content_section' => 'profile',
                        'recipient_role' => 'public',
                    ]
                );

            if (! $moderationResult['allowed']) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $moderationResult['user_message']
                    );
            }
        }

        if ($request->hasFile('profile_photo')) {
            $attachmentModeration =
                $this->attachmentModerationService
                    ->moderate(
                        user: $user,
                        file: $request->file(
                            'profile_photo'
                        ),
                        sourceType:
                            'user_profile_photo',
                        sourceId: $user->id,
                        context: [
                            'content_section' =>
                                'profile_photo',
                            'recipient_role' =>
                                'public',
                        ]
                    );

            if (! $attachmentModeration['allowed']) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        $attachmentModeration[
                            'user_message'
                        ]
                    );
            }
        }

        $user->fill($validated);

        /*
         * تغيير البريد يلغي التحقق من البريد
         * ويعطّل Email 2FA حتى لا يبقى مربوطًا ببريد سابق.
         */
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->email_two_factor_enabled = false;
            $user->email_two_factor_verified_at = null;

            $request->session()->forget(
                'email_2fa_setup_user_id'
            );
        }

        $oldPhoto = $user->profile_photo;
        $newPhoto = null;

        try {
            if ($request->hasFile('profile_photo')) {
                $newPhoto = $request
                    ->file('profile_photo')
                    ->store(
                        'profile-photos',
                        'public'
                    );

                $user->profile_photo = $newPhoto;
            }

            $user->save();
        } catch (\Throwable $exception) {
            if (
                $newPhoto
                && Storage::disk('public')->exists(
                    $newPhoto
                )
            ) {
                Storage::disk('public')->delete(
                    $newPhoto
                );
            }

            throw $exception;
        }

        if (
            $newPhoto
            && $oldPhoto
            && $oldPhoto !== $newPhoto
            && Storage::disk('public')->exists(
                $oldPhoto
            )
        ) {
            Storage::disk('public')->delete(
                $oldPhoto
            );
        }

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with(
                'success',
                'تم حفظ البيانات الشخصية بنجاح.'
            );
    }

    /**
     * حذف الحساب.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {
        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ],
            [
                'password.required' =>
                    'يجب إدخال كلمة المرور للتأكيد.',
                'password.current_password' =>
                    'كلمة المرور التي أدخلتها غير صحيحة.',
            ]
        );

        $user = $request->user();
        $profilePhoto = $user->profile_photo;

        Auth::logout();

        $user->delete();

        if (
            $profilePhoto
            && Storage::disk('public')
                ->exists($profilePhoto)
        ) {
            Storage::disk('public')
                ->delete($profilePhoto);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')
            ->with(
                'success',
                'تم حذف الحساب بنجاح.'
            );
    }
}
