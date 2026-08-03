<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignConsultationToOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'office_id' => [
                'required',
                'integer',
                'exists:offices,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'office_id.required' =>
                'يجب اختيار المكتب الهندسي.',

            'office_id.integer' =>
                'المكتب المختار غير صحيح.',

            'office_id.exists' =>
                'المكتب المختار غير موجود.',

            'notes.string' =>
                'ملاحظات التحويل يجب أن تكون نصًا.',

            'notes.max' =>
                'ملاحظات التحويل يجب ألا تتجاوز 3000 حرف.',
        ];
    }
}
