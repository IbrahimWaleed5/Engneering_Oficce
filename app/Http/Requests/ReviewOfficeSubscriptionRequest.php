<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewOfficeSubscriptionRequest extends FormRequest
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

            'notes' => [
                'nullable',
                'string',
                'max:2000',
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
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'يجب تحديد قرار الاشتراك.',
            'decision.in' => 'قرار مراجعة الاشتراك غير صحيح.',
            'rejection_reason.required' => 'سبب رفض الاشتراك مطلوب.',
            'rejection_reason.max' => 'سبب الرفض يجب ألا يتجاوز 3000 حرف.',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 2000 حرف.',
            'duration_value.required' => 'مدة الاشتراك مطلوبة عند الاعتماد.',
            'duration_value.integer' => 'مدة الاشتراك يجب أن تكون عددًا صحيحًا.',
            'duration_value.min' => 'مدة الاشتراك يجب ألا تقل عن 1.',
            'duration_unit.required' => 'وحدة مدة الاشتراك مطلوبة.',
            'duration_unit.in' => 'وحدة مدة الاشتراك غير صحيحة.',
        ];
    }
}
