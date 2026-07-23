<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'consultation.engineer',
            'customer',
            'invoice',
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
            (int) $consultation->customer_id
                === (int) $request->user()->id
            || $request->user()->role === 'admin',
            403
        );

        if ($consultation->payment_status === 'paid') {
            return redirect()
                ->route('consultations.mine')
                ->with(
                    'success',
                    'هذه الاستشارة مدفوعة بالفعل.'
                );
        }

        if ($consultation->payment_status === 'pending') {
            return redirect()
                ->route('consultations.mine')
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
            (int) $consultation->customer_id
                === (int) $request->user()->id
            || $request->user()->role === 'admin',
            403
        );

        if ($consultation->payment_status !== 'unpaid') {
            return redirect()
                ->route('consultations.mine')
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
                'max:512000',
            ],
        ]);

        $receiptPath = $request
            ->file('receipt_image')
            ->store(
                'payment-receipts',
                'public'
            );

        Payment::create([
            'consultation_id' =>
                $consultation->id,

            'customer_id' =>
                $consultation->customer_id,

            'amount' =>
                $consultation->final_price,

            'payment_method' =>
                $validated['payment_method'],

            'transaction_reference' =>
                $validated['transaction_reference']
                ?? null,

            'receipt_image' =>
                $receiptPath,

            'status' =>
                'pending',
        ]);

        $consultation->update([
            'payment_status' => 'pending',
            'status' => 'waiting_payment',
        ]);

        $admins = User::query()
            ->where('role', 'admin')
            ->get();

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

        return redirect()
            ->route('consultations.mine')
            ->with(
                'success',
                'تم إرسال إيصال الدفع وبانتظار مراجعة المدير.'
            );
    }

    /**
     * تأكيد الدفعة وإنشاء الفاتورة.
     */
    public function confirm(
        Request $request,
        Payment $payment
    ) {
        abort_unless(
            $request->user()->role === 'admin',
            403
        );

        $payment->load([
            'consultation.consultationType',
            'consultation.customer',
            'consultation.engineer',
            'customer',
            'invoice',
        ]);

        /*
         * في حال تم تأكيد الدفعة سابقًا لكن لم تنشأ
         * لها فاتورة، يتم إنشاء الفاتورة الآن.
         */
        if ($payment->status === 'completed') {
            $invoice = $this->createInvoice(
                $payment
            );

            return redirect()
                ->route('payments.index')
                ->with(
                    'success',
                    'تم تأكيد الدفعة سابقًا. رقم الفاتورة: '
                        . $invoice->invoice_number
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

        $invoice = DB::transaction(
            function () use ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                $consultation =
                    $payment->consultation;

                $consultation->update([
                    'payment_status' => 'paid',
                    'status' => 'pending',
                ]);

                return $this->createInvoice(
                    $payment->fresh([
                        'consultation.consultationType',
                        'consultation.customer',
                        'consultation.engineer',
                        'customer',
                    ])
                );
            }
        );

        $consultation =
            $payment->consultation;

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
                        . (
                            $consultation
                                ->customer
                                ?->name
                            ?? 'عميل'
                        )
                        . ' بإرسال طلب الاستشارة رقم '
                        . $consultation
                            ->consultation_number
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
                    title: 'تم قبول الدفع وإصدار الفاتورة',
                    message: 'تم قبول دفعتك للاستشارة رقم '
                        . $consultation
                            ->consultation_number
                        . '. رقم الفاتورة: '
                        . $invoice->invoice_number
                        . '.',
                    url: route(
                        'invoices.download',
                        $invoice
                    ),
                    sendMail: true,
                    buttonText: 'تحميل الفاتورة'
                )
            );
        }

        return redirect()
            ->route('payments.index')
            ->with(
                'success',
                'تم تأكيد الدفع وإصدار الفاتورة رقم '
                    . $invoice->invoice_number
                    . '.'
            );
    }

    /**
     * رفض الدفعة.
     */
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
            'rejection_reason' =>
                $validated['rejection_reason'],
        ]);

        $payment->consultation->update([
            'payment_status' => 'unpaid',
            'status' => 'waiting_payment',
        ]);

        $customer = $payment->customer
            ?? $payment
                ->consultation
                ->customer;

        if ($customer) {
            $customer->notify(
                new SystemNotification(
                    title: 'تم رفض إيصال الدفع',
                    message: 'تم رفض إيصال دفع الاستشارة رقم '
                        . $payment
                            ->consultation
                            ->consultation_number
                        . '. السبب: '
                        . $validated[
                            'rejection_reason'
                        ],
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

    /**
     * إنشاء فاتورة واحدة لكل دفعة.
     */
    private function createInvoice(
    Payment $payment
): Invoice {
    $payment->loadMissing([
        'consultation.consultationType',
        'consultation.customer',
        'consultation.engineer',
        'customer',
    ]);

    $consultation = $payment->consultation;

    return Invoice::firstOrCreate(
        [
            'payment_id' => $payment->id,
        ],
        [
            'invoice_number' =>
                'INV-'
                . now()->format('Ymd')
                . '-'
                . str_pad(
                    (string) $payment->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),

            'consultation_id' =>
                $consultation->id,

            'customer_id' =>
                $payment->customer_id,

            'consultation_number' =>
                $consultation->consultation_number,

            'customer_name' =>
                $consultation->customer?->name
                ?? $payment->customer?->name
                ?? 'عميل',

            'service_name' =>
                $consultation->consultationType?->name
                ?? 'استشارة هندسية',

            'engineer_name' =>
                $consultation->engineer?->name,

            'amount' =>
                $payment->amount,

            'total' =>
                $payment->amount,

            'payment_method' =>
                $payment->payment_method,

            'currency' =>
                'ILS',

            'office_name' =>
                'مكتب الوليد الهندسي',

            'issued_at' =>
                $payment->paid_at
                ?? now(),
        ]
    );
}
}
