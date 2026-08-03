<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    'active',
                    'suspended',
                    'closed',
                ]),
            ],

            'reason' => [
                Rule::requiredIf(
                    in_array(
                        $this->input('status'),
                        ['suspended', 'closed'],
                        true
                    )
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
            'status.required' =>
                'يجب تحديد حالة المكتب.',

            'status.in' =>
                'حالة المكتب المختارة غير صحيحة.',

            'reason.required' =>
                'يجب كتابة سبب إيقاف أو إغلاق المكتب.',

            'reason.max' =>
                'سبب الإجراء يجب ألا يتجاوز 3000 حرف.',
        ];
    }
}
