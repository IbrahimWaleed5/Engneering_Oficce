<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
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

        // الصورة ملف، لذلك لا نمررها إلى fill.
        unset($validated['profile_photo']);

        $user->fill($validated);

        /*
        |--------------------------------------------------------------------------
        | إعادة التحقق من البريد عند تغييره
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
        | حفظ المستخدم
        |--------------------------------------------------------------------------
        */

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | حذف الصورة القديمة بعد نجاح الحفظ
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
         * نحذف الصورة بعد نجاح حذف المستخدم.
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
