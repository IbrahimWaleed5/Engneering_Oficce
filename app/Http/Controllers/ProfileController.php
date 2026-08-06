<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService
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
     * تحديث البيانات الشخصية.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | البيانات النصية
        |--------------------------------------------------------------------------
        */

        $validated = $request->validated();

        // الصورة ملف وليست قيمة نصية.
        unset($validated['profile_photo']);

        /*
        |--------------------------------------------------------------------------
        | فحص المحتوى النصي العام قبل رفع الصورة أو الحفظ
        |--------------------------------------------------------------------------
        */

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
                        'content_section' =>
                            'profile',

                        'recipient_role' =>
                            'public',
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

        $user->fill($validated);

        /*
        |--------------------------------------------------------------------------
        | إلغاء التحقق عند تغيير البريد
        |--------------------------------------------------------------------------
        */

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        /*
        |--------------------------------------------------------------------------
        | رفع الصورة الجديدة
        |--------------------------------------------------------------------------
        */

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

            /*
            |--------------------------------------------------------------------------
            | حفظ البيانات
            |--------------------------------------------------------------------------
            */

            $user->save();
        } catch (\Throwable $exception) {
            if (
                $newPhoto
                && Storage::disk('public')->exists($newPhoto)
            ) {
                Storage::disk('public')->delete($newPhoto);
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | حذف الصورة القديمة
        |--------------------------------------------------------------------------
        */

        if (
            $newPhoto
            && $oldPhoto
            && $oldPhoto !== $newPhoto
            && Storage::disk('public')->exists($oldPhoto)
        ) {
            Storage::disk('public')->delete($oldPhoto);
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

        /*
         * حذف صورة الحساب من التخزين.
         */
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
