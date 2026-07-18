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
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        /*
         * نملأ البيانات النصية فقط.
         * لا نمرر profile_photo إلى fill لأنها ملف وليست نصًا.
         */
        $user->fill(
            $request->safe()->except('profile_photo')
        );

        /*
         * إذا تغيّر البريد الإلكتروني نلغي حالة التحقق منه.
         */
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        /*
         * حفظ الصورة الشخصية الجديدة.
         */
        if ($request->hasFile('profile_photo')) {

            /*
             * حذف الصورة القديمة من storage إذا كانت موجودة.
             */
            if (
                $user->profile_photo
                && Storage::disk('public')->exists($user->profile_photo)
            ) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            /*
             * تخزين الصورة الجديدة داخل:
             * storage/app/public/profile-photos
             */
            $profilePhotoPath = $request
                ->file('profile_photo')
                ->store('profile-photos', 'public');

            $user->profile_photo = $profilePhotoPath;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();

        /*
         * حذف الصورة الشخصية عند حذف الحساب.
         */
        if (
            $user->profile_photo
            && Storage::disk('public')->exists($user->profile_photo)
        ) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
