<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewOfficeApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([
                    'approve',
                    'reject',
                ]),
            ],

            'rejection_reason' => [
                Rule::requiredIf(
                    $this->input('decision') === 'reject'
                ),
                'nullable',
                'string',
                'max:3000',
            ],

            'subscription_amount' => [
                Rule::requiredIf(
                    $this->input('decision') === 'approve'
                ),
                'nullable',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],

            'subscription_currency' => [
                Rule::requiredIf(
                    $this->input('decision') === 'approve'
                ),
                'nullable',
                'string',
                'size:3',
                Rule::in([
                    'SAR',
                    'USD',
                    'ILS',
                    'JOD',
                    'EUR',
                ]),
            ],

            'duration_value' => [
                Rule::requiredIf(
                    $this->input('decision') === 'approve'
                ),
                'nullable',
                'integer',
                'min:1',
                'max:120',
            ],

            'duration_unit' => [
                Rule::requiredIf(
                    $this->input('decision') === 'approve'
                ),
                'nullable',
                Rule::in([
                    'day',
                    'month',
                    'year',
                ]),
            ],

            'subscription_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'يجب تحديد قرار الطلب.',
            'decision.in' => 'قرار الطلب غير صحيح.',
            'rejection_reason.required' => 'سبب رفض الطلب مطلوب.',
            'rejection_reason.max' => 'سبب الرفض طويل جدًا.',
            'subscription_amount.required' => 'قيمة الاشتراك مطلوبة عند قبول المكتب.',
            'subscription_amount.numeric' => 'قيمة الاشتراك يجب أن تكون رقمًا.',
            'subscription_amount.min' => 'قيمة الاشتراك لا يمكن أن تكون سالبة.',
            'subscription_currency.required' => 'عملة الاشتراك مطلوبة.',
            'subscription_currency.in' => 'عملة الاشتراك غير مدعومة.',
            'duration_value.required' => 'مدة الاشتراك مطلوبة.',
            'duration_value.integer' => 'مدة الاشتراك يجب أن تكون عددًا صحيحًا.',
            'duration_value.min' => 'مدة الاشتراك يجب ألا تقل عن 1.',
            'duration_unit.required' => 'وحدة مدة الاشتراك مطلوبة.',
            'duration_unit.in' => 'وحدة مدة الاشتراك غير صحيحة.',
            'subscription_notes.max' => 'ملاحظات الاشتراك يجب ألا تتجاوز 2000 حرف.',
        ];
    }
}
