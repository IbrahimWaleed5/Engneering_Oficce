<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * عرض جميع الدفعات للمدير فقط.
     */
    public function index(Request $request): View
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
     * عرض صفحة رفع إيصال الدفع للعميل صاحب الاستشارة فقط.
     */
    public function create(
        Request $request,
        Consultation $consultation
    ): View|RedirectResponse {
        $this->authorizeCustomerConsultation(
            $request,
            $consultation
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
                    'تم إرسال الإيصال سابقًا وهو قيد فحص المدير.'
                );
        }

        return view(
            'payments.create',
            compact('consultation')
        );
    }

    /**
     * حفظ إيصال الدفع في التخزين الخاص.
     */
    public function store(
        Request $request,
        Consultation $consultation
    ): RedirectResponse {
        $this->authorizeCustomerConsultation(
            $request,
            $consultation
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
                'max:20480',
            ],
        ], [
            'payment_method.required' =>
                'يرجى اختيار طريقة الدفع.',

            'payment_method.in' =>
                'طريقة الدفع المختارة غير صحيحة.',

            'receipt_image.required' =>
                'يرجى رفع صورة أو ملف إيصال الدفع.',

            'receipt_image.mimes' =>
                'يجب أن يكون الإيصال صورة أو ملف PDF.',

            'receipt_image.max' =>
                'حجم الإيصال يجب ألا يتجاوز 20 ميجابايت.',
        ]);

        $receiptPath = $request
            ->file('receipt_image')
            ->store(
                'payment-receipts',
                'private'
            );

        try {
            DB::transaction(
                function () use (
                    $consultation,
                    $validated,
                    $receiptPath
                ): void {
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
                }
            );
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Storage::disk('private')
                ->delete($receiptPath);

            throw $exception;
        }

        $admins = User::query()
            ->where('role', 'admin')
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new SystemNotification(
                    title: 'إيصال دفع جديد',
                    message: 'تم رفع إيصال دفع للاستشارة رقم '
                        . $consultation->consultation_number
                        . ' وبانتظار المراجعة اليدوية.',
                    url: route('payments.index'),
                    sendMail: false,
                    buttonText: 'مراجعة الدفعة'
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
     * تأكيد الدفعة يدويًا بواسطة المدير وإنشاء الفاتورة.
     */
    public function confirm(
        Payment $payment
    ): RedirectResponse {
        $this->authorize(
            'confirm',
            $payment
        );

        $payment->load([
            'consultation.consultationType',
            'consultation.customer',
            'consultation.engineer',
            'customer',
            'invoice',
        ]);

        /*
         * إذا كانت الدفعة مؤكدة سابقًا ولم تُنشأ فاتورة،
         * يتم إنشاء الفاتورة بدون تكرار تأكيد الدفعة.
         */
        if ($payment->status === 'completed') {
            $invoice = $this->createInvoice(
                $payment
            );

            $this->createConsultationConversation(
                $payment->consultation
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
                    'لا يمكن تأكيد هذه الدفعة لأنها تمت مراجعتها سابقًا.'
                );
        }

        $invoice = DB::transaction(
            function () use ($payment): Invoice {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'rejection_reason' => null,
                ]);

                $consultation =
                    $payment->consultation;

                $consultation->update([
                    'payment_status' => 'paid',
                    'status' => 'pending',
                ]);

                $this->createConsultationConversation(
                    $consultation
                );

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
            $payment->fresh([
                'consultation.customer',
                'consultation.engineer',
                'customer',
            ])->consultation;

        if ($consultation->engineer) {
            $consultation->engineer->notify(
                new SystemNotification(
                    title: 'طلب استشارة جديد',
                    message: 'قام العميل '
                        . ($consultation->customer?->name ?? 'عميل')
                        . ' بإرسال طلب الاستشارة رقم '
                        . $consultation->consultation_number
                        . ' إليك.',
                    url: route('engineer.consultations'),
                    sendMail: true,
                    buttonText: 'عرض الاستشارة'
                )
            );
        }

        $customer = $payment->customer
            ?? $consultation->customer;

        if ($customer) {
            $customer->notify(
                new SystemNotification(
                    title: 'تم قبول الدفع وإصدار الفاتورة',
                    message: 'راجع المدير إيصال دفعتك وقام بقبوله '
                        . 'للاستشارة رقم '
                        . $consultation->consultation_number
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
                'تم تأكيد الدفع بعد المراجعة وإصدار الفاتورة رقم '
                    . $invoice->invoice_number
                    . '.'
            );
    }

    /**
     * رفض الدفعة يدويًا بواسطة المدير.
     */
    public function reject(
        Request $request,
        Payment $payment
    ): RedirectResponse {
        $this->authorize(
            'reject',
            $payment
        );

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'rejection_reason.required' =>
                'يرجى كتابة سبب رفض الدفعة.',
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

        DB::transaction(
            function () use (
                $payment,
                $validated
            ): void {
                $payment->update([
                    'status' => 'rejected',
                    'paid_at' => null,
                    'rejection_reason' =>
                        $validated['rejection_reason'],
                ]);

                $payment->consultation->update([
                    'payment_status' => 'unpaid',
                    'status' => 'waiting_payment',
                ]);
            }
        );

        $customer = $payment->customer
            ?? $payment->consultation->customer;

        if ($customer) {
            $customer->notify(
                new SystemNotification(
                    title: 'تم رفض إيصال الدفع',
                    message: 'راجع المدير إيصال دفع الاستشارة رقم '
                        . $payment->consultation->consultation_number
                        . ' ورفضه. السبب: '
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
                'تم رفض الدفعة وإبلاغ العميل بالسبب.'
            );
    }

    /**
     * إنشاء محادثة الاستشارة بعد تأكيد الدفع.
     */
    private function createConsultationConversation(
        Consultation $consultation
    ): Conversation {
        $conversation = Conversation::firstOrCreate(
            [
                'type' => 'consultation',
                'consultation_id' => $consultation->id,
            ],
            [
                'created_by' => auth()->id(),
                'last_message_at' => null,
            ]
        );

        $participantIds = array_values(
            array_unique(
                array_filter([
                    $consultation->customer_id,
                    $consultation->engineer_id,
                ])
            )
        );

        foreach ($participantIds as $participantId) {
            $conversation
                ->participants()
                ->syncWithoutDetaching([
                    $participantId => [
                        'last_read_at' => null,
                        'is_muted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
        }

        return $conversation;
    }

    /**
     * إنشاء فاتورة واحدة فقط لكل دفعة.
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

    /**
     * العميل وحده يستطيع رفع دفعة لاستشارته.
     */
    private function authorizeCustomerConsultation(
        Request $request,
        Consultation $consultation
    ): void {
        abort_unless(
            $request->user()->role === 'customer'
            && (int) $consultation->customer_id
                === (int) $request->user()->id,
            403
        );
    }
}
