<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewOfficeMembershipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user
            ->managedOfficeMemberships()
            ->exists();
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

            'position' => [
                Rule::requiredIf(
                    $this->input('decision') === 'approve'
                ),
                'nullable',
                'string',
                'max:150',
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
                'قرار مراجعة الطلب غير صحيح.',

            'position.required' =>
                'المسمى الوظيفي مطلوب عند قبول المهندس.',

            'position.max' =>
                'المسمى الوظيفي يجب ألا يتجاوز 150 حرفًا.',

            'rejection_reason.required' =>
                'سبب رفض طلب المهندس مطلوب.',

            'rejection_reason.max' =>
                'سبب الرفض يجب ألا يتجاوز 3000 حرف.',
        ];
    }
}
