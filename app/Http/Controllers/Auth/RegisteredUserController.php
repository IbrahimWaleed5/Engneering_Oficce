<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
   public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'country_code' => [
            'required',
            'string',
            'size:2',
        ],

        'dial_code' => [
            'required',
            'string',
            'max:8',
        ],

        'phone' => [
            'required',
            'string',
            'max:25',
        ],

        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            'unique:' . User::class,
        ],

        'password' => [
            'required',
            'confirmed',
            Rules\Password::defaults(),
        ],

        'profile_photo' => [
            'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
        'privacy_accepted' => [
    'required',
    'accepted',
],

'terms_accepted' => [
    'required',
    'accepted',
],
    ]);

    $countryCode = strtoupper($validated['country_code']);

    try {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $parsedPhone = $phoneUtil->parse(
            $validated['phone'],
            $countryCode
        );

        if (! $phoneUtil->isValidNumber($parsedPhone)) {
            throw ValidationException::withMessages([
                'phone' => 'رقم الهاتف غير صحيح بالنسبة للدولة المختارة.',
            ]);
        }

        $internationalPhone = $phoneUtil->format(
            $parsedPhone,
            PhoneNumberFormat::E164
        );
    } catch (NumberParseException) {
        throw ValidationException::withMessages([
            'phone' => 'تعذر قراءة رقم الهاتف. تأكد من الدولة والرقم.',
        ]);
    }

    if (
        User::query()
            ->where('phone', $internationalPhone)
            ->exists()
    ) {
        throw ValidationException::withMessages([
            'phone' => 'رقم الهاتف مستخدم في حساب آخر.',
        ]);
    }

    $profilePhotoPath = $request
        ->file('profile_photo')
        ->store('profile-photos', 'public');

    $user = User::create([
        'name' => $validated['name'],
        'country_code' => $countryCode,
        'dial_code' => $validated['dial_code'],
        'phone' => $internationalPhone,
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'profile_photo' => $profilePhotoPath,
        'role' => 'customer',
        'status' => 'active',
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect()->route('verification.notice');
}
}
