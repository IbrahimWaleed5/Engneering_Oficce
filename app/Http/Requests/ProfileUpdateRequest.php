<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
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
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)
                    ->ignore($this->user()->id),
            ],

            'phone' => [
                'required',
                'string',
                'max:25',
                Rule::unique(User::class)
                    ->ignore($this->user()->id),
            ],

            'country_code' => [
                'nullable',
                'string',
                'size:2',
            ],

            'dial_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=200,min_height=200',
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
                'البريد الإلكتروني غير صحيح.',

            'email.unique' =>
                'البريد الإلكتروني مستخدم مسبقًا.',

            'phone.required' =>
                'رقم الهاتف مطلوب.',

            'phone.unique' =>
                'رقم الهاتف مستخدم مسبقًا.',

            'profile_photo.image' =>
                'الملف المختار يجب أن يكون صورة.',

            'profile_photo.mimes' =>
                'الصورة يجب أن تكون JPG أو PNG أو WEBP.',

            'profile_photo.max' =>
                'حجم الصورة يجب ألا يتجاوز 5MB.',

            'profile_photo.dimensions' =>
                'يجب ألا تقل أبعاد الصورة عن 200×200.',
        ];
    }
}
