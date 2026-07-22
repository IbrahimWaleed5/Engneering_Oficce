<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class ConsultationMessageController extends Controller
{
    public function index(
        Request $request,
        Consultation $consultation
    ) {
        $this->authorizeConversation(
            $request,
            $consultation
        );

        abort_if(
            $consultation->payment_status !== 'paid',
            403,
            'لا يمكن فتح المحادثة قبل تأكيد الدفع.'
        );

        $consultation->load([
            'customer',
            'engineer',
            'consultationType',
            'messages.sender',
        ]);

        return view(
            'consultations.messages',
            compact('consultation')
        );
    }

    public function store(
        Request $request,
        Consultation $consultation
    ) {
        $this->authorizeConversation(
            $request,
            $consultation
        );

        abort_if(
            $consultation->payment_status !== 'paid',
            403,
            'لا يمكن إرسال الرسائل قبل تأكيد الدفع.'
        );

        $validated = $request->validate([
            'message' => [
                'nullable',
                'string',
                'max:5000',
                'required_without:attachment',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp,dwg,doc,docx,xls,xlsx,zip',
                'max:102400',
                'required_without:message',
            ],
        ], [
            'message.required_without' =>
                'اكتب رسالة أو أرفق ملفًا.',

            'attachment.required_without' =>
                'اكتب رسالة أو أرفق ملفًا.',

            'attachment.max' =>
                'حجم الملف يجب ألا يتجاوز 100 ميجابايت.',

            'attachment.mimes' =>
                'نوع الملف المرفق غير مسموح.',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request
                ->file('attachment')
                ->store(
                    'consultation-messages',
                    'public'
                );
        }

        ConsultationMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $request->user()->id,
            'message' => $validated['message'] ?? null,
            'attachment' => $attachmentPath,
        ]);

        $consultation->load([
            'customer',
            'engineer',
        ]);

        $recipient = $this->getRecipient(
            $request,
            $consultation
        );

        if ($recipient) {
            $recipient->notify(
                new SystemNotification(
                    'رسالة جديدة في الاستشارة',
                    'وصلتك رسالة جديدة في الاستشارة رقم '
                        . $consultation->consultation_number
                        . ' من '
                        . $request->user()->name
                        . '.',
                    route(
                        'consultations.messages.index',
                        $consultation
                    )
                )
            );
        }

        return back()->with(
            'success',
            'تم إرسال الرسالة.'
        );
    }

    private function authorizeConversation(
        Request $request,
        Consultation $consultation
    ): void {
        $user = $request->user();

        $isCustomer =
            (int) $consultation->customer_id
            === (int) $user->id;

        $isAssignedEngineer =
            (int) $consultation->engineer_id
            === (int) $user->id;

        $isManagement = in_array(
            $user->role,
            [
                'admin',
                'employee',
            ],
            true
        );

        abort_unless(
            $isCustomer
            || $isAssignedEngineer
            || $isManagement,
            403,
            'ليس لديك صلاحية لدخول هذه المحادثة.'
        );
    }

    private function getRecipient(
        Request $request,
        Consultation $consultation
    ) {
        $user = $request->user();

        if (
            (int) $user->id
            === (int) $consultation->customer_id
        ) {
            return $consultation->engineer;
        }

        if (
            (int) $user->id
            === (int) $consultation->engineer_id
        ) {
            return $consultation->customer;
        }

        return null;
    }
}
