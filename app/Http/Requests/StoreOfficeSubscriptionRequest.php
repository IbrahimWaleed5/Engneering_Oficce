<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->role === 'office_owner'
            && $user->ownedOffice()->exists();
    }

    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_reference' => [
                'nullable',
                'string',
                'max:190',
            ],

            'receipt' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' =>
                'طريقة الدفع مطلوبة.',

            'payment_method.max' =>
                'اسم طريقة الدفع طويل جدًا.',

            'payment_reference.max' =>
                'رقم أو مرجع عملية الدفع طويل جدًا.',

            'receipt.required' =>
                'إيصال دفع اشتراك المكتب مطلوب.',

            'receipt.file' =>
                'يجب رفع ملف إيصال صالح.',

            'receipt.mimes' =>
                'الإيصال يجب أن يكون PDF أو صورة.',

            'receipt.max' =>
                'حجم الإيصال يجب ألا يتجاوز 10 ميجابايت.',

            'notes.max' =>
                'الملاحظات يجب ألا تتجاوز 2000 حرف.',
        ];
    }
}
