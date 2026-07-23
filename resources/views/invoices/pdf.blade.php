<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $invoice->invoice_number }}
    </title>

    <style>

        @page {
            margin: 25px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            direction: rtl;
            font-family: dejavusans, sans-serif;
            color: #172033;
            font-size: 13px;
            line-height: 1.8;
            margin: 0;
            padding: 0;
        }

        .invoice {
            border: 1px solid #dbe2ea;
            padding: 28px;
            border-radius: 12px;
        }

        .header-table,
        .details-table,
        .summary-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 25%;
            text-align: right;
        }

        .office-cell {
            width: 45%;
            text-align: center;
        }

        .invoice-cell {
            width: 30%;
            text-align: left;
        }

        .logo {
            width: 80px;
            max-height: 80px;
        }

        .office-name {
            font-size: 22px;
            font-weight: bold;
            color: #0f4c81;
            margin-bottom: 3px;
        }

        .office-description {
            color: #697386;
            font-size: 11px;
        }

        .invoice-title {
            font-size: 25px;
            font-weight: bold;
            color: #0f4c81;
        }

        .invoice-number {
            margin-top: 5px;
            color: #697386;
            font-size: 11px;
        }

        .divider {
            height: 2px;
            background: #0f4c81;
            margin: 22px 0;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f4c81;
            margin-bottom: 10px;
        }

        .details-table {
            margin-bottom: 22px;
        }

        .details-table td {
            padding: 9px 10px;
            border: 1px solid #e3e8ef;
        }

        .label {
            width: 28%;
            font-weight: bold;
            background: #f4f7fa;
            color: #334155;
        }

        .value {
            width: 72%;
        }

        .summary-table {
            margin-top: 15px;
        }

        .summary-table th {
            padding: 11px;
            background: #0f4c81;
            color: white;
            border: 1px solid #0f4c81;
        }

        .summary-table td {
            padding: 12px;
            border: 1px solid #dbe2ea;
            text-align: center;
        }

        .total-row td {
            font-size: 16px;
            font-weight: bold;
            background: #edf7ff;
            color: #0f4c81;
        }

        .paid-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            background: #e7f8ef;
            color: #087443;
            font-weight: bold;
        }

        .notes {
            margin-top: 25px;
            padding: 14px;
            background: #f8fafc;
            border-right: 4px solid #0f4c81;
            color: #526071;
        }

        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #dbe2ea;
            text-align: center;
            color: #7b8794;
            font-size: 10px;
        }

    </style>

</head>

<body>

    <div class="invoice">

        <table class="header-table">

            <tr>

                <td class="logo-cell">

                   @php
    $logoCandidates = [
        public_path('images/logo.png'),
        public_path(
            'images/ChatGPT Image Jul 22, 2026, 06_15_51 PM.png'
        ),
    ];

    $logoPath = null;

    foreach ($logoCandidates as $candidate) {
        if (file_exists($candidate)) {
            $logoPath = $candidate;
            break;
        }
    }

    $logoData = null;

    if ($logoPath) {
        $extension = strtolower(
            pathinfo($logoPath, PATHINFO_EXTENSION)
        );

        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        $logoData =
            'data:'
            . $mimeType
            . ';base64,'
            . base64_encode(
                file_get_contents($logoPath)
            );
    }
@endphp

@if ($logoData)

    <img
        src="{{ $logoData }}"
        alt="شعار المكتب"
        class="logo"
    >

@else

    <div
        style="
            width: 70px;
            height: 70px;
            line-height: 70px;
            text-align: center;
            background: #0f4c81;
            color: white;
            font-size: 22px;
            font-weight: bold;
            border-radius: 50%;
        "
    >
        WO
    </div>

@endif

                </td>

                <td class="office-cell">

                    <div class="office-name">
                        {{ $invoice->office_name }}
                    </div>

                    <div class="office-description">
                        للاستشارات والخدمات الهندسية
                    </div>

                </td>

                <td class="invoice-cell">

                    <div class="invoice-title">
                        فاتورة
                    </div>

                    <div class="invoice-number">
                        {{ $invoice->invoice_number }}
                    </div>

                </td>

            </tr>

        </table>

        <div class="divider"></div>

        <div class="section-title">
            معلومات الفاتورة
        </div>

        <table class="details-table">

            <tr>

                <td class="label">
                    رقم الفاتورة
                </td>

                <td class="value">
                    {{ $invoice->invoice_number }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    تاريخ الإصدار
                </td>

                <td class="value">
                    {{ $invoice->issued_at
                        ?->format('Y-m-d H:i') }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    حالة الدفع
                </td>

                <td class="value">

                    <span class="paid-badge">
                        مدفوعة
                    </span>

                </td>

            </tr>

            <tr>

                <td class="label">
                    اسم العميل
                </td>

                <td class="value">
                    {{ $invoice->customer_name }}
                </td>

            </tr>

        </table>

        <div class="section-title">
            تفاصيل الاستشارة
        </div>

        <table class="details-table">

            <tr>

                <td class="label">
                    رقم الاستشارة
                </td>

                <td class="value">
                    {{ $invoice->consultation_number }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    نوع الخدمة
                </td>

                <td class="value">
                    {{ $invoice->service_name }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    المهندس
                </td>

                <td class="value">
                    {{ $invoice->engineer_name
                        ?? 'لم يتم تعيين مهندس' }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    طريقة الدفع
                </td>

                <td class="value">

                    @switch($invoice->payment_method)

                        @case('cash')
                            نقدًا
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
                            {{ $invoice->payment_method }}

                    @endswitch

                </td>

            </tr>

            @if (
                $invoice->payment
                ?->transaction_reference
            )

                <tr>

                    <td class="label">
                        رقم العملية
                    </td>

                    <td class="value">
                        {{ $invoice
                            ->payment
                            ->transaction_reference }}
                    </td>

                </tr>

            @endif

        </table>

        <div class="section-title">
            الملخص المالي
        </div>

        <table class="summary-table">

            <thead>

                <tr>

                    <th>
                        الخدمة
                    </th>

                    <th>
                        الكمية
                    </th>

                    <th>
                        السعر
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>
                        {{ $invoice->service_name }}
                    </td>

                    <td>
                        1
                    </td>

                    <td>
                        {{ number_format(
                            (float) $invoice->amount,
                            2
                        ) }}
                        ₪
                    </td>

                </tr>

                <tr class="total-row">

                    <td colspan="2">
                        المجموع الإجمالي
                    </td>

                    <td>
                        {{ number_format(
                            (float) $invoice->amount,
                            2
                        ) }}
                        ₪
                    </td>

                </tr>

            </tbody>

        </table>

        <div class="notes">
            هذه الفاتورة صادرة إلكترونيًا بعد تأكيد عملية الدفع.
        </div>


        <table style="width:100%; margin-top:30px;">
            <tr>
                <td style="width:50%; text-align:center; vertical-align:bottom;">
                    <div style="
                        display:inline-block;
                        width:105px;
                        height:105px;
                        border:3px double #0f4c81;
                        border-radius:50%;
                        text-align:center;
                        color:#0f4c81;
                        font-weight:bold;
                        padding-top:24px;
                        line-height:1.5;
                    ">
                        مكتب الوليد<br>
                        الهندسي<br>
                        ختم معتمد
                    </div>
                </td>
               <td style="width:50%; text-align:center; vertical-align:bottom;">

    @php
        $signaturePath = public_path(
            'images/sign.png'
        );
    @endphp

    @if (file_exists($signaturePath))

        <img
            src="{{ $signaturePath }}"
            alt="توقيع الإدارة"
            style="
                width:190px;
                max-height:90px;
                object-fit:contain;
                margin-bottom:4px;
            "
        >

    @endif

    <div style="
        width:190px;
        margin:0 auto;
        border-top:1px solid #64748b;
        padding-top:7px;
        color:#475569;
        font-size:10px;
    ">
        توقيع الإدارة
    </div>

</td>
            </tr>
        </table>

        <div class="footer">

            <p>
                شكرًا لاختياركم
                {{ $invoice->office_name }}
            </p>

            <p>
                تم إصدار الفاتورة بتاريخ
                {{ $invoice->issued_at
                    ?->format('Y-m-d') }}
            </p>

        </div>

    </div>

</body>

</html>
