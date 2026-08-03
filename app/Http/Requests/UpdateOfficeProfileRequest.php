<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user
            ->managedOfficeMemberships()
            ->where('status', 'active')
            ->whereIn('office_role', [
                'owner',
                'manager',
            ])
            ->exists();
    }

    public function rules(): array
    {
        $membership = $this->user()
            ?->managedOfficeMemberships()
            ->where('status', 'active')
            ->whereIn('office_role', [
                'owner',
                'manager',
            ])
            ->first();

        $officeId = $membership?->office_id;

        return [
            'name' => [
                'required',
                'string',
                'max:200',

                Rule::unique(
                    'offices',
                    'name'
                )->ignore($officeId),
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'offices',
                    'email'
                )->ignore($officeId),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'commercial_registration' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'offices',
                    'commercial_registration'
                )->ignore($officeId),
            ],

            'license_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'offices',
                    'license_number'
                )->ignore($officeId),
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'cover' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:6144',
            ],

            'remove_logo' => [
                'nullable',
                'boolean',
            ],

            'remove_cover' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'اسم المكتب مطلوب.',

            'name.string' =>
                'اسم المكتب يجب أن يكون نصًا.',

            'name.max' =>
                'اسم المكتب يجب ألا يتجاوز 200 حرف.',

            'name.unique' =>
                'يوجد مكتب آخر مسجل بهذا الاسم.',

            'email.required' =>
                'البريد الإلكتروني للمكتب مطلوب.',

            'email.email' =>
                'صيغة البريد الإلكتروني غير صحيحة.',

            'email.max' =>
                'البريد الإلكتروني طويل جدًا.',

            'email.unique' =>
                'البريد الإلكتروني مستخدم في مكتب آخر.',

            'phone.max' =>
                'رقم الهاتف يجب ألا يتجاوز 30 حرفًا.',

            'commercial_registration.max' =>
                'رقم السجل التجاري يجب ألا يتجاوز 100 حرف.',

            'commercial_registration.unique' =>
                'رقم السجل التجاري مستخدم في مكتب آخر.',

            'license_number.max' =>
                'رقم الترخيص يجب ألا يتجاوز 100 حرف.',

            'license_number.unique' =>
                'رقم الترخيص مستخدم في مكتب آخر.',

            'country.max' =>
                'اسم الدولة يجب ألا يتجاوز 100 حرف.',

            'city.max' =>
                'اسم المدينة يجب ألا يتجاوز 100 حرف.',

            'address.max' =>
                'العنوان يجب ألا يتجاوز 1000 حرف.',

            'description.max' =>
                'نبذة المكتب يجب ألا تتجاوز 5000 حرف.',

            'logo.image' =>
                'شعار المكتب يجب أن يكون صورة.',

            'logo.mimes' =>
                'الشعار يجب أن يكون JPG أو PNG أو WEBP.',

            'logo.max' =>
                'حجم الشعار يجب ألا يتجاوز 4 ميجابايت.',

            'cover.image' =>
                'غلاف المكتب يجب أن يكون صورة.',

            'cover.mimes' =>
                'الغلاف يجب أن يكون JPG أو PNG أو WEBP.',

            'cover.max' =>
                'حجم الغلاف يجب ألا يتجاوز 6 ميجابايت.',
        ];
    }
}
