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
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' =>
                'يجب تحديد قرار الطلب.',

            'decision.in' =>
                'قرار الطلب غير صحيح.',

            'rejection_reason.required' =>
                'سبب رفض الطلب مطلوب.',

            'rejection_reason.max' =>
                'سبب الرفض طويل جدًا.',
        ];
    }
}
