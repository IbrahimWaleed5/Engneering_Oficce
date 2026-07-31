<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="rtl"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ config('app.name', 'مكتب الوليد الهندسي') }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap"
        rel="stylesheet"
    >

    {{-- مكتبة أرقام الهواتف الدولية --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.2/dist/css/intlTelInput.css"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        /* عرض حقل الهاتف بكامل المساحة */
        .premium-phone-field,
        .premium-phone-field .iti {
            width: 100%;
        }

        .premium-phone-field {
            position: relative;
            direction: ltr;
        }

        .premium-phone-field input[type="tel"] {
            width: 100%;
            direction: ltr;
            text-align: left;
        }

        /* زر الدولة */
        .premium-phone-field .iti__selected-country {
            padding-left: 12px;
            padding-right: 12px;
            border-radius: 0 1rem 1rem 0;
        }

        .premium-phone-field .iti__selected-country-primary {
            background: transparent;
        }

        .premium-phone-field .iti__selected-dial-code {
            color: #cbd5e1;
            font-size: 14px;
        }

        .premium-phone-field .iti__arrow {
            border-top-color: #94a3b8;
        }

        /* قائمة الدول */
        .iti__dropdown-content {
            z-index: 999999 !important;
            max-height: 320px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            background: #0f172a;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
            direction: ltr;
        }

        .iti__country-list {
            max-height: 260px;
            overflow-y: auto;
            background: #0f172a;
            color: #e2e8f0;
        }

        .iti__country {
            padding: 10px 12px;
        }

        .iti__country:hover,
        .iti__country.iti__highlight {
            background: rgba(6, 182, 212, 0.14);
        }

        .iti__country-name {
            color: #e2e8f0;
        }

        .iti__dial-code {
            color: #94a3b8;
        }

        /* بحث الدول */
        .iti__search-input {
            width: 100%;
            padding: 12px 14px;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
            outline: none;
            background: #020617;
            color: #ffffff;
            direction: ltr;
        }

        .iti__search-input::placeholder {
            color: #64748b;
        }

        /* قائمة الدول عند إضافتها إلى body */
        body > .iti {
            z-index: 999999 !important;
        }

        .phone-client-error {
            display: none;
            margin-top: 8px;
            color: #fca5a5;
            font-size: 14px;
            font-weight: 700;
        }

        .phone-client-error.active {
            display: block;
        }
    </style>
</head>

<body class="font-sans antialiased text-white bg-slate-950">

    <main
        class="flex items-center justify-center w-full min-h-screen overflow-x-hidden"
    >
        {{ $slot }}
    </main>

    {{-- مكتبة أرقام الهواتف الدولية --}}
    <script
        src="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.2/dist/js/intlTelInput.min.js"
    ></script>

</body>
</html>
