<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',

                Rule::unique(
                    User::class,
                    'email'
                )->ignore(
                    $this->user()->id
                ),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'الاسم الكامل مطلوب.',

            'email.required' =>
                'البريد الإلكتروني مطلوب.',

            'email.email' =>
                'صيغة البريد الإلكتروني غير صحيحة.',

            'email.unique' =>
                'هذا البريد الإلكتروني مستخدم مسبقًا.',

            'profile_photo.image' =>
                'يجب أن يكون الملف صورة.',

            'profile_photo.mimes' =>
                'الصورة يجب أن تكون JPG أو JPEG أو PNG أو WEBP.',

            'profile_photo.max' =>
                'حجم الصورة يجب ألا يتجاوز 2MB.',
        ];
    }
}
