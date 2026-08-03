<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeMembershipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->role === 'engineer'
            && $user->hasActiveEngineerMembership();
    }

    public function rules(): array
    {
        return [
            'specialty_id' => [
                'required',
                'exists:engineering_specialties,id',
            ],

            'requested_position' => [
                'nullable',
                'string',
                'max:150',
            ],

            'years_of_experience' => [
                'nullable',
                'integer',
                'min:0',
                'max:60',
            ],

            'cv' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:10240',
            ],

            'certificate' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:10240',
            ],

            'message' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'specialty_id.required' =>
                'يجب اختيار التخصص الهندسي.',

            'specialty_id.exists' =>
                'التخصص المختار غير موجود.',

            'requested_position.max' =>
                'المسمى الوظيفي يجب ألا يتجاوز 150 حرفًا.',

            'years_of_experience.integer' =>
                'سنوات الخبرة يجب أن تكون رقمًا صحيحًا.',

            'years_of_experience.min' =>
                'سنوات الخبرة لا يمكن أن تكون أقل من صفر.',

            'years_of_experience.max' =>
                'سنوات الخبرة غير صحيحة.',

            'cv.required' =>
                'يجب رفع السيرة الذاتية.',

            'cv.file' =>
                'ملف السيرة الذاتية غير صالح.',

            'cv.mimes' =>
                'السيرة الذاتية يجب أن تكون PDF أو Word.',

            'cv.max' =>
                'حجم السيرة الذاتية يجب ألا يتجاوز 10 ميجابايت.',

            'certificate.required' =>
                'يجب رفع الشهادة.',

            'certificate.file' =>
                'ملف الشهادة غير صالح.',

            'certificate.mimes' =>
                'الشهادة يجب أن تكون PDF أو صورة.',

            'certificate.max' =>
                'حجم الشهادة يجب ألا يتجاوز 10 ميجابايت.',

            'message.max' =>
                'الرسالة يجب ألا تتجاوز 3000 حرف.',
        ];
    }
}
