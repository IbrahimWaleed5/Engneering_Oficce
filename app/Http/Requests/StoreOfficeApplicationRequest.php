<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfficeApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && in_array(
                $user->role,
                ['customer', 'engineer'],
                true
            );
    }

    public function rules(): array
    {
        return [
            'office_name' => [
                'required',
                'string',
                'max:190',
            ],

            'email' => [
                'required',
                'email',
                'max:190',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'commercial_registration' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'office_applications',
                    'commercial_registration'
                )->where(function ($query) {
                    return $query->whereIn(
                        'status',
                        ['pending', 'approved']
                    );
                }),

                Rule::unique(
                    'offices',
                    'commercial_registration'
                ),
            ],

            'license_number' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'office_applications',
                    'license_number'
                )->where(function ($query) {
                    return $query->whereIn(
                        'status',
                        ['pending', 'approved']
                    );
                }),

                Rule::unique(
                    'offices',
                    'license_number'
                ),
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'address' => [
                'required',
                'string',
                'max:2000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'commercial_registration_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],

            'license_document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'bank_transfer',
                    'wallet',
                    'other',
                ]),
            ],

            'payment_reference' => [
                'nullable',
                'string',
                'max:190',
            ],

            'payment_receipt' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],

            'terms' => [
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'office_name.required' => 'اسم المكتب مطلوب.',
            'office_name.max' => 'اسم المكتب طويل جدًا.',
            'email.required' => 'البريد الإلكتروني للمكتب مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'phone.required' => 'رقم هاتف المكتب مطلوب.',
            'commercial_registration.required' => 'رقم السجل التجاري مطلوب.',
            'commercial_registration.unique' => 'رقم السجل التجاري مستخدم في طلب أو مكتب آخر.',
            'license_number.required' => 'رقم ترخيص المكتب مطلوب.',
            'license_number.unique' => 'رقم الترخيص مستخدم في طلب أو مكتب آخر.',
            'country.required' => 'الدولة مطلوبة.',
            'city.required' => 'المدينة مطلوبة.',
            'address.required' => 'عنوان المكتب مطلوب.',
            'commercial_registration_file.required' => 'ملف السجل التجاري مطلوب.',
            'commercial_registration_file.mimes' => 'ملف السجل التجاري يجب أن يكون PDF أو صورة.',
            'commercial_registration_file.max' => 'حجم ملف السجل التجاري يجب ألا يتجاوز 10 ميجابايت.',
            'license_document.required' => 'ملف ترخيص المكتب مطلوب.',
            'license_document.mimes' => 'ملف الترخيص يجب أن يكون PDF أو صورة.',
            'license_document.max' => 'حجم ملف الترخيص يجب ألا يتجاوز 10 ميجابايت.',
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'payment_method.in' => 'طريقة الدفع غير صحيحة.',
            'payment_receipt.required' => 'إيصال دفع اشتراك المكتب مطلوب.',
            'payment_receipt.mimes' => 'إيصال الدفع يجب أن يكون PDF أو صورة.',
            'payment_receipt.max' => 'حجم إيصال الدفع يجب ألا يتجاوز 10 ميجابايت.',
            'terms.accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ];
    }
}
