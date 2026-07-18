<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>إدارة الدفعات</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1300px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, .08);
        }

        h1 {
            margin-top: 0;
            color: #1f2937;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: right;
            vertical-align: middle;
        }

        th {
            background: #f3f4f6;
        }

        .receipt {
            max-width: 90px;
            max-height: 90px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .completed {
            background: #dcfce7;
            color: #166534;
        }

        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn {
            border: none;
            padding: 8px 12px;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .reject-form {
            margin-top: 8px;
        }

        .reject-form textarea {
            width: 200px;
            min-height: 55px;
            padding: 7px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>إدارة الدفعات</h1>

    @if(session('success'))
        <div class="message success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="message error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="message error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table>
        <thead>
        <tr>
            <th>رقم الاستشارة</th>
            <th>العميل</th>
            <th>المبلغ</th>
            <th>طريقة الدفع</th>
            <th>رقم العملية</th>
            <th>الإيصال</th>
            <th>الحالة</th>
            <th>تاريخ الإرسال</th>
            <th>الإجراءات</th>
        </tr>
        </thead>

        <tbody>

        @forelse($payments as $payment)

            <tr>
                <td>
                    {{ $payment->consultation?->consultation_number ?? '-' }}
                </td>

                <td>
                    {{ $payment->customer?->name ?? '-' }}
                </td>

                <td>
                    {{ number_format($payment->amount, 2) }}
                    شيكل
                </td>

                <td>
                    @switch($payment->payment_method)
                        @case('cash')
                            نقدي
                            @break

                        @case('card')
                            بطاقة
                            @break

                        @case('bank')
                            تحويل بنكي
                            @break

                        @case('wallet')
                            محفظة إلكترونية
                            @break

                        @default
                            {{ $payment->payment_method }}
                    @endswitch
                </td>

                <td>
                    {{ $payment->transaction_reference ?? '-' }}
                </td>

                <td>
                    @if($payment->receipt_image)

                        @if(
                            str_ends_with(
                                strtolower($payment->receipt_image),
                                '.pdf'
                            )
                        )
                            <a
                                href="{{ asset('storage/' . $payment->receipt_image) }}"
                                target="_blank"
                            >
                                عرض ملف PDF
                            </a>
                        @else
                            <a
                                href="{{ asset('storage/' . $payment->receipt_image) }}"
                                target="_blank"
                            >
                                <img
                                    src="{{ asset('storage/' . $payment->receipt_image) }}"
                                    class="receipt"
                                    alt="إيصال الدفع"
                                >
                            </a>
                        @endif

                    @else
                        لا يوجد
                    @endif
                </td>

                <td>
                    @if($payment->status === 'pending')
                        <span class="badge pending">
                            قيد المراجعة
                        </span>
                    @elseif($payment->status === 'completed')
                        <span class="badge completed">
                            مقبولة
                        </span>
                    @elseif($payment->status === 'rejected')
                        <span class="badge rejected">
                            مرفوضة
                        </span>
                    @else
                        {{ $payment->status }}
                    @endif
                </td>

                <td>
                    {{ $payment->created_at?->format('Y-m-d H:i') }}
                </td>

                <td>
                    @if($payment->status === 'pending')

                        <form
                            action="{{ route('payments.confirm', $payment) }}"
                            method="POST"
                            style="display:inline-block"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-success"
                                onclick="return confirm('هل أنت متأكد من قبول الدفعة؟')"
                            >
                                قبول
                            </button>
                        </form>

                        <form
                            action="{{ route('payments.reject', $payment) }}"
                            method="POST"
                            class="reject-form"
                        >
                            @csrf
                            @method('PATCH')

                            <textarea
                                name="rejection_reason"
                                placeholder="اكتب سبب رفض الإيصال"
                                required
                            ></textarea>

                            <br>

                            <button
                                type="submit"
                                class="btn btn-danger"
                                onclick="return confirm('هل أنت متأكد من رفض الدفعة؟')"
                            >
                                رفض
                            </button>
                        </form>

                    @elseif($payment->status === 'completed')

                        <span>
                            تم القبول
                            @if($payment->paid_at)
                                بتاريخ
                                {{ $payment->paid_at->format('Y-m-d H:i') }}
                            @endif
                        </span>

                    @elseif($payment->status === 'rejected')

                        <strong>سبب الرفض:</strong>

                        <br>

                        {{ $payment->rejection_reason ?? 'لم يتم تحديد السبب' }}

                    @endif
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9">
                    لا توجد دفعات حتى الآن.
                </td>
            </tr>

        @endforelse

        </tbody>
    </table>

</div>

</body>
</html>

