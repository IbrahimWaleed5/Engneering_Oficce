<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Consultation;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * عرض جميع الدفعات للمدير.
     */
    public function index(Request $request)
    {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $payments = Payment::with([
            'consultation.consultationType',
            'customer',
        ])
            ->latest()
            ->get();

        return view(
            'payments.index',
            compact('payments')
        );
    }

    /**
     * عرض صفحة إرسال إيصال الدفع.
     */
    public function create(
        Request $request,
        Consultation $consultation
    ) {
        abort_unless(
            $consultation->customer_id === $request->user()->id
            || $request->user()->role === 'admin',
            403
        );

        if ($consultation->payment_status === 'paid') {
            return redirect('/my-consultations')
                ->with(
                    'success',
                    'هذه الاستشارة مدفوعة بالفعل.'
                );
        }

        if ($consultation->payment_status === 'pending') {
            return redirect('/my-consultations')
                ->with(
                    'success',
                    'تم إرسال الإيصال سابقًا وهو قيد الفحص.'
                );
        }

        return view(
            'payments.create',
            compact('consultation')
        );
    }

    /**
     * حفظ إيصال الدفع.
     */
    public function store(
        Request $request,
        Consultation $consultation
    ) {
        abort_unless(
            $consultation->customer_id === $request->user()->id
            || $request->user()->role === 'admin',
            403
        );

        if ($consultation->payment_status !== 'unpaid') {
            return redirect('/my-consultations')
                ->with(
                    'success',
                    'تم إرسال عملية دفع لهذه الاستشارة سابقًا.'
                );
        }

        $validated = $request->validate([
            'payment_method' => [
                'required',
                'in:cash,card,bank,wallet',
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'receipt_image' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
        ]);

        $receiptPath = $request
            ->file('receipt_image')
            ->store(
                'payment-receipts',
                'public'
            );

        Payment::create([
            'consultation_id' => $consultation->id,
            'customer_id' => $consultation->customer_id,
            'amount' => $consultation->final_price,
            'payment_method' => $validated['payment_method'],
            'transaction_reference' =>
                $validated['transaction_reference'] ?? null,
            'receipt_image' => $receiptPath,
            'status' => 'pending',
        ]);

        $consultation->update([
            'payment_status' => 'pending',
            'status' => 'waiting_payment',
        ]);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new SystemNotification(
                    title: 'إيصال دفع جديد',
                    message: 'تم رفع إيصال دفع للاستشارة رقم '
                        . $consultation->consultation_number
                        . '.',
                    url: '/payments',
                    sendMail: false,
                    buttonText: 'عرض الدفعات'
                )
            );
        }

        return redirect('/my-consultations')
            ->with(
                'success',
                'تم إرسال إيصال الدفع وبانتظار مراجعة المدير.'
            );
    }

    /**
     * تأكيد الدفعة من المدير.
     */
    public function confirm(
        Request $request,
        Payment $payment
    ) {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        if ($payment->status === 'completed') {
            return redirect()
                ->route('payments.index')
                ->with(
                    'success',
                    'تم تأكيد هذه الدفعة سابقًا.'
                );
        }

        if ($payment->status !== 'pending') {
            return redirect()
                ->route('payments.index')
                ->with(
                    'error',
                    'لا يمكن تأكيد هذه الدفعة.'
                );
        }

        $payment->load([
            'consultation.customer',
            'consultation.engineer',
            'customer',
        ]);

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $consultation = $payment->consultation;

        $consultation->update([
            'payment_status' => 'paid',
            'status' => 'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | إشعار المهندس
        |--------------------------------------------------------------------------
        */

        if ($consultation->engineer) {
            $consultation->engineer->notify(
                new SystemNotification(
                    title: 'طلب استشارة جديد',
                    message: 'قام العميل '
                        . ($consultation->customer?->name ?? 'عميل')
                        . ' بإرسال طلب الاستشارة رقم '
                        . $consultation->consultation_number
                        . ' إليك.',
                    url: '/engineer/consultations',
                    sendMail: true,
                    buttonText: 'عرض الاستشارة'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | إشعار العميل
        |--------------------------------------------------------------------------
        */

        $customer = $payment->customer
            ?? $consultation->customer;

        if ($customer) {
            $customer->notify(
                new SystemNotification(
                    title: 'تم قبول الدفع',
                    message: 'تم قبول دفعتك للاستشارة رقم '
                        . $consultation->consultation_number
                        . '، وسيتم البدء بإجراءات الاستشارة.',
                    url: '/my-consultations',
                    sendMail: true,
                    buttonText: 'متابعة الاستشارة'
                )
            );
        }

        return redirect()
            ->route('payments.index')
            ->with(
                'success',
                'تم تأكيد الدفع وتفعيل الاستشارة بنجاح.'
            );
    }
    public function reject(
    Request $request,
    Payment $payment
) {
    abort_unless(
        $request->user()->role === 'admin',
        403
    );

    $validated = $request->validate([
        'rejection_reason' => [
            'required',
            'string',
            'max:1000',
        ],
    ]);

    if ($payment->status !== 'pending') {
        return redirect()
            ->route('payments.index')
            ->with(
                'error',
                'تمت مراجعة هذه الدفعة سابقًا.'
            );
    }

    $payment->load([
        'consultation.customer',
        'customer',
    ]);

    $payment->update([
        'status' => 'rejected',
        'rejection_reason' => $validated['rejection_reason'],
    ]);

    $payment->consultation->update([
        'payment_status' => 'unpaid',
        'status' => 'waiting_payment',
    ]);

    $customer = $payment->customer
        ?? $payment->consultation->customer;

    if ($customer) {
        $customer->notify(
            new SystemNotification(
                title: 'تم رفض إيصال الدفع',
                message: 'تم رفض إيصال دفع الاستشارة رقم '
                    . $payment->consultation->consultation_number
                    . '. السبب: '
                    . $validated['rejection_reason'],
                url: route(
                    'payments.create',
                    $payment->consultation
                ),
                sendMail: true,
                buttonText: 'إعادة رفع الإيصال'
            )
        );
    }

    return redirect()
        ->route('payments.index')
        ->with(
            'success',
            'تم رفض الدفعة وإبلاغ العميل.'
        );
}
}
