<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        التحقق بخطوتين - مكتب الوليد الهندسي
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;
            overflow: hidden;

            color: #f8fafc;

            background: #050b14;

            font-family:
                'Hanken Grotesk',
                Tahoma,
                Arial,
                sans-serif;

            -webkit-font-smoothing: antialiased;
        }

        /* ================================
           Background
        ================================= */

        .bg-complex {
            position: fixed;
            inset: 0;

            background-color: #050b14;

            background-image:
                radial-gradient(
                    circle at 50% 0%,
                    rgba(37, 99, 235, 0.15) 0%,
                    transparent 60%
                ),
                radial-gradient(
                    circle at 0% 100%,
                    rgba(15, 23, 42, 0.8) 0%,
                    transparent 50%
                );

            z-index: -3;
        }

        .bg-grid {
            position: fixed;
            inset: 0;

            background-image:
                linear-gradient(
                    to right,
                    rgba(255, 255, 255, 0.03) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    to bottom,
                    rgba(255, 255, 255, 0.03) 1px,
                    transparent 1px
                );

            background-size: 40px 40px;

            opacity: 0.5;

            z-index: -2;

            mask-image:
                radial-gradient(
                    circle at center,
                    black,
                    transparent 80%
                );

            -webkit-mask-image:
                radial-gradient(
                    circle at center,
                    black,
                    transparent 80%
                );
        }

        .background-glow {
            position: fixed;

            width: 700px;
            height: 700px;

            border-radius: 9999px;

            background:
                rgba(37, 99, 235, 0.08);

            filter: blur(130px);

            top: 50%;
            left: 50%;

            transform:
                translate(-50%, -50%);

            pointer-events: none;

            z-index: -1;
        }

        /* ================================
           Brand
        ================================= */

        .brand-header {
            position: absolute;

            top: 32px;
            left: 32px;

            display: flex;
            align-items: center;

            gap: 12px;

            z-index: 20;

            opacity: 0.9;

            transition:
                opacity 0.25s ease;
        }

        .brand-header:hover {
            opacity: 1;
        }

        .brand-logo {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #3b82f6,
                    #2563eb
                );

            border:
                1px solid
                rgba(255, 255, 255, 0.1);

            box-shadow:
                0 0 20px
                rgba(37, 99, 235, 0.3);
        }

        .brand-logo svg {
            width: 22px;
            height: 22px;

            color: white;

            filter:
                drop-shadow(
                    0 2px 3px
                    rgba(0, 0, 0, 0.25)
                );
        }

        .brand-name {
            font-size: 14px;
            font-weight: 800;

            letter-spacing: 0.08em;

            color:
                rgba(255, 255, 255, 0.9);
        }

        /* ================================
           Main Container
        ================================= */

        .main-container {
            width: 100%;

            max-width: 440px;

            padding:
                30px 24px;

            position: relative;

            z-index: 10;
        }

        /* ================================
           Glass Card
        ================================= */

        .glass-card {
            width: 100%;

            padding:
                48px 40px;

            border-radius: 24px;

            position: relative;

            overflow: hidden;

            text-align: center;

            background:
                linear-gradient(
                    145deg,
                    rgba(15, 23, 42, 0.7) 0%,
                    rgba(5, 11, 20, 0.85) 100%
                );

            backdrop-filter:
                blur(24px);

            -webkit-backdrop-filter:
                blur(24px);

            border:
                1px solid
                rgba(255, 255, 255, 0.08);

            border-top:
                1px solid
                rgba(255, 255, 255, 0.15);

            border-left:
                1px solid
                rgba(255, 255, 255, 0.1);

            box-shadow:
                0 30px 60px
                rgba(0, 0, 0, 0.6),

                0 0 40px
                rgba(37, 99, 235, 0.1)
                inset,

                0 0 0 1px
                rgba(0, 0, 0, 0.5);
        }

        .card-top-line {
            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 1px;

            background:
                linear-gradient(
                    to right,
                    transparent,
                    rgba(59, 130, 246, 0.5),
                    transparent
                );
        }

        /* ================================
           Security Icon
        ================================= */

        .security-icon {
            width: 80px;
            height: 80px;

            margin:
                0 auto 32px;

            border-radius: 16px;

            display: flex;
            align-items: center;
            justify-content: center;

            position: relative;

            background:
                #0f172a;

            border:
                1px solid
                rgba(30, 41, 59, 0.6);

            box-shadow:
                inset 0 2px 10px
                rgba(0, 0, 0, 0.35);
        }

        .security-icon::before {
            content: '';

            position: absolute;

            inset: 8px;

            border-radius: 14px;

            background:
                rgba(37, 99, 235, 0.12);

            filter:
                blur(18px);
        }

        .security-icon svg {
            position: relative;

            width: 36px;
            height: 36px;

            color: #3b82f6;

            filter:
                drop-shadow(
                    0 0 10px
                    rgba(59, 130, 246, 0.6)
                );
        }

        /* ================================
           Typography
        ================================= */

        .title {
            margin:
                0 0 14px;

            color: #ffffff;

            font-size: 30px;
            font-weight: 800;

            letter-spacing:
                -0.025em;
        }

        .description {
            max-width: 340px;

            margin:
                0 auto 32px;

            color: #94a3b8;

            font-size: 14px;

            font-weight: 500;

            line-height: 1.9;
        }

        /* ================================
           Alerts
        ================================= */

        .alert {
            width: 100%;

            padding:
                13px 14px;

            margin-bottom:
                20px;

            border-radius:
                12px;

            font-size:
                13px;

            line-height:
                1.7;

            text-align:
                right;
        }

        .alert-success {
            color: #bbf7d0;

            background:
                rgba(20, 83, 45, 0.3);

            border:
                1px solid
                rgba(21, 128, 61, 0.5);
        }

        .alert-error {
            color: #fecaca;

            background:
                rgba(127, 29, 29, 0.25);

            border:
                1px solid
                rgba(220, 38, 38, 0.35);
        }

        /* ================================
           OTP
        ================================= */

        .otp-wrapper {
            position: relative;

            width: 100%;

            margin-bottom:
                26px;
        }

        .otp-inputs {
            width: 100%;

            display: flex;

            direction: ltr;

            justify-content:
                center;

            gap: 10px;
        }

        .otp-input {
            width: 50px;
            height: 56px;

            padding: 0;

            text-align: center;

            color: #ffffff;

            font-family: inherit;

            font-size: 23px;
            font-weight: 800;

            border-radius: 12px;

            outline: none;

            background:
                rgba(10, 16, 29, 0.8);

            border:
                2px solid
                rgba(71, 85, 105, 0.5);

            box-shadow:
                inset 0 2px 10px
                rgba(0, 0, 0, 0.3);

            transition:
                border-color 0.25s ease,
                background 0.25s ease,
                box-shadow 0.25s ease,
                transform 0.25s ease;
        }

        .otp-input:hover {
            border-color:
                rgba(100, 116, 139, 0.8);
        }

        .otp-input:focus {
            border-color:
                #3b82f6;

            background:
                rgba(15, 23, 42, 0.9);

            box-shadow:
                inset 0 2px 10px
                rgba(0, 0, 0, 0.3),

                0 0 20px
                rgba(37, 99, 235, 0.2);

            transform:
                translateY(-1px);
        }

        .otp-glow {
            position: absolute;

            left: 50%;
            bottom: -8px;

            width: 75%;
            height: 4px;

            transform:
                translateX(-50%);

            border-radius:
                9999px;

            background:
                rgba(37, 99, 235, 0.2);

            filter:
                blur(7px);
        }

        /* ================================
           Buttons
        ================================= */

        .actions {
            display: flex;
            flex-direction: column;

            gap: 14px;

            margin-top:
                8px;
        }

        .btn {
            width: 100%;

            border-radius:
                12px;

            cursor: pointer;

            font-family:
                inherit;

            transition:
                all 0.3s
                cubic-bezier(
                    0.4,
                    0,
                    0.2,
                    1
                );
        }

        .btn-primary {
            padding:
                15px 18px;

            color: #ffffff;

            font-size:
                15px;

            font-weight:
                800;

            letter-spacing:
                0.01em;

            background:
                linear-gradient(
                    135deg,
                    #3b82f6 0%,
                    #1d4ed8 100%
                );

            border:
                1px solid
                rgba(255, 255, 255, 0.1);

            box-shadow:
                0 4px 15px
                rgba(37, 99, 235, 0.3),

                inset 0 1px 0
                rgba(255, 255, 255, 0.2);
        }

        .btn-primary:hover {
            transform:
                translateY(-2px);

            background:
                linear-gradient(
                    135deg,
                    #60a5fa 0%,
                    #2563eb 100%
                );

            box-shadow:
                0 8px 25px
                rgba(37, 99, 235, 0.5),

                inset 0 1px 0
                rgba(255, 255, 255, 0.3);
        }

        .btn-primary:active {
            transform:
                translateY(0);
        }

        .btn-secondary {
            padding:
                13px 18px;

            color:
                #94a3b8;

            font-size:
                14px;

            font-weight:
                700;

            background:
                linear-gradient(
                    180deg,
                    rgba(30, 41, 59, 0.4) 0%,
                    rgba(15, 23, 42, 0.6) 100%
                );

            border:
                1px solid
                rgba(71, 85, 105, 0.6);

            box-shadow:
                0 4px 6px
                rgba(0, 0, 0, 0.2);
        }

        .btn-secondary:hover {
            color: #ffffff;

            background:
                linear-gradient(
                    180deg,
                    rgba(51, 65, 85, 0.6) 0%,
                    rgba(30, 41, 59, 0.8) 100%
                );

            border-color:
                rgba(148, 163, 184, 0.6);
        }

        .btn:disabled {
            opacity: 0.55;

            cursor:
                not-allowed;

            transform:
                none;
        }

        /* ================================
           Footer
        ================================= */

        .security-note {
            margin-top:
                26px;

            padding-top:
                20px;

            border-top:
                1px solid
                rgba(255, 255, 255, 0.06);

            display: flex;
            justify-content: center;
            align-items: center;

            gap: 7px;

            color: #64748b;

            font-size: 12px;
            font-weight: 600;
        }

        .security-dot {
            width: 6px;
            height: 6px;

            border-radius:
                9999px;

            background:
                #22c55e;

            box-shadow:
                0 0 10px
                rgba(34, 197, 94, 0.6);
        }

        /* ================================
           Mobile
        ================================= */

        @media (
            max-width: 600px
        ) {
            body {
                align-items:
                    center;

                overflow-y:
                    auto;
            }

            .brand-header {
                top: 20px;
                left: 20px;
            }

            .brand-name {
                display: none;
            }

            .main-container {
                padding:
                    90px 18px 30px;
            }

            .glass-card {
                padding:
                    38px 20px;
            }

            .title {
                font-size:
                    27px;
            }

            .otp-inputs {
                gap: 7px;
            }

            .otp-input {
                width:
                    calc(
                        (100vw - 96px) / 6
                    );

                max-width:
                    47px;

                height:
                    54px;

                font-size:
                    21px;
            }
        }

        @media (
            max-width: 380px
        ) {
            .glass-card {
                padding-left:
                    15px;

                padding-right:
                    15px;
            }

            .otp-inputs {
                gap: 5px;
            }

            .otp-input {
                width:
                    calc(
                        (100vw - 82px) / 6
                    );

                height:
                    50px;
            }
        }
    </style>
</head>

<body>

    {{-- الخلفية --}}
    <div class="bg-complex"></div>
    <div class="bg-grid"></div>
    <div class="background-glow"></div>


    {{-- اسم المنصة --}}
    <header class="brand-header">

        <div class="brand-logo">

            <svg
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                viewBox="0 0 24 24"
            >
                <polygon
                    points="12 2 2 7 12 12 22 7 12 2"
                ></polygon>

                <polyline
                    points="2 17 12 22 22 17"
                ></polyline>

                <polyline
                    points="2 12 12 17 22 12"
                ></polyline>
            </svg>

        </div>

        <span class="brand-name">
            مكتب الوليد الهندسي
        </span>

    </header>


    <main class="main-container">

        <div class="glass-card">

            <div class="card-top-line"></div>


            {{-- أيقونة القفل --}}
            <div class="security-icon">

                <svg
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    viewBox="0 0 24 24"
                >
                    <rect
                        height="11"
                        rx="2"
                        ry="2"
                        width="18"
                        x="3"
                        y="11"
                    ></rect>

                    <path
                        d="M7 11V7a5 5 0 0 1 10 0v4"
                    ></path>
                </svg>

            </div>


            <h1 class="title">
                التحقق بخطوتين
            </h1>


            <p class="description">
                أرسلنا رمزاً من 6 أرقام إلى بريدك الإلكتروني.
                يرجى إدخاله أدناه للمتابعة.
            </p>


            {{-- رسالة نجاح --}}
            @if (session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif


            {{-- رسالة خطأ --}}
            @if (session('error'))

                <div class="alert alert-error">
                    {{ session('error') }}
                </div>

            @endif


            @error('code')

                <div class="alert alert-error">
                    {{ $message }}
                </div>

            @enderror


            {{-- نموذج تأكيد الكود --}}
            <form
                id="verify_2fa_form"
                method="POST"
                action="{{ route('email-2fa.verify') }}"
            >

                @csrf


                {{-- هذا هو الحقل الحقيقي المرسل إلى Laravel --}}
                <input
                    type="hidden"
                    name="code"
                    id="full_2fa_code"
                    value=""
                >


                <div class="otp-wrapper">

                    <div
                        class="otp-inputs"
                        dir="ltr"
                    >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            autocomplete="one-time-code"
                            aria-label="الرقم الأول"
                            required
                            autofocus
                        >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            aria-label="الرقم الثاني"
                            required
                        >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            aria-label="الرقم الثالث"
                            required
                        >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            aria-label="الرقم الرابع"
                            required
                        >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            aria-label="الرقم الخامس"
                            required
                        >

                        <input
                            type="text"
                            maxlength="1"
                            class="otp-input"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            aria-label="الرقم السادس"
                            required
                        >

                    </div>


                    <div class="otp-glow"></div>

                </div>


                <div class="actions">

                    <button
                        id="verify_button"
                        class="btn btn-primary"
                        type="submit"
                    >
                        تأكيد الدخول
                    </button>

                </div>

            </form>


            {{-- إعادة إرسال الكود --}}
            <form
                method="POST"
                action="{{ route('email-2fa.resend') }}"
                style="margin-top: 14px;"
            >

                @csrf

                <button
                    class="btn btn-secondary"
                    type="submit"
                >
                    إعادة إرسال الرمز
                </button>

            </form>


            <div class="security-note">

                <span class="security-dot"></span>

                <span>
                    جلسة تسجيل دخول محمية
                </span>

            </div>

        </div>

    </main>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const inputs =
                    Array.from(
                        document.querySelectorAll(
                            '.otp-input'
                        )
                    );

                const form =
                    document.getElementById(
                        'verify_2fa_form'
                    );

                const hiddenCode =
                    document.getElementById(
                        'full_2fa_code'
                    );

                const verifyButton =
                    document.getElementById(
                        'verify_button'
                    );


                /**
                 * السماح بالأرقام فقط.
                 */
                const cleanValue = (value) => {
                    return value.replace(
                        /\D/g,
                        ''
                    );
                };


                /**
                 * تجميع الخانات الست.
                 */
                const getFullCode = () => {
                    return inputs
                        .map(
                            input =>
                                cleanValue(
                                    input.value
                                )
                        )
                        .join('');
                };


                /**
                 * تحديث الحقل المخفي.
                 */
                const syncCode = () => {

                    hiddenCode.value =
                        getFullCode();

                };


                inputs.forEach(
                    (input, index) => {

                        input.addEventListener(
                            'input',
                            function (event) {

                                const value =
                                    cleanValue(
                                        event.target.value
                                    );

                                event.target.value =
                                    value.slice(
                                        -1
                                    );

                                syncCode();


                                if (
                                    event.target.value
                                    &&
                                    index <
                                        inputs.length - 1
                                ) {

                                    inputs[
                                        index + 1
                                    ].focus();

                                    inputs[
                                        index + 1
                                    ].select();

                                }

                            }
                        );


                        input.addEventListener(
                            'keydown',
                            function (event) {

                                if (
                                    event.key ===
                                        'Backspace'
                                    &&
                                    !input.value
                                    &&
                                    index > 0
                                ) {

                                    event.preventDefault();

                                    inputs[
                                        index - 1
                                    ].focus();

                                    inputs[
                                        index - 1
                                    ].value = '';

                                    syncCode();

                                }


                                if (
                                    event.key ===
                                        'ArrowLeft'
                                    &&
                                    index <
                                        inputs.length - 1
                                ) {

                                    inputs[
                                        index + 1
                                    ].focus();

                                }


                                if (
                                    event.key ===
                                        'ArrowRight'
                                    &&
                                    index > 0
                                ) {

                                    inputs[
                                        index - 1
                                    ].focus();

                                }

                            }
                        );


                        /**
                         * لصق كود 6 أرقام كامل.
                         */
                        input.addEventListener(
                            'paste',
                            function (event) {

                                const pastedText =
                                    cleanValue(
                                        (
                                            event.clipboardData
                                            ||
                                            window.clipboardData
                                        ).getData(
                                            'text'
                                        )
                                    );

                                if (
                                    pastedText.length <
                                        2
                                ) {
                                    return;
                                }

                                event.preventDefault();


                                const digits =
                                    pastedText
                                        .slice(0, 6)
                                        .split('');


                                digits.forEach(
                                    (
                                        digit,
                                        digitIndex
                                    ) => {

                                        if (
                                            inputs[
                                                digitIndex
                                            ]
                                        ) {

                                            inputs[
                                                digitIndex
                                            ].value =
                                                digit;

                                        }

                                    }
                                );


                                syncCode();


                                const focusIndex =
                                    Math.min(
                                        digits.length,
                                        inputs.length
                                    ) - 1;


                                if (
                                    inputs[
                                        focusIndex
                                    ]
                                ) {

                                    inputs[
                                        focusIndex
                                    ].focus();

                                }

                            }
                        );

                    }
                );


                /**
                 * عند الإرسال نجمع الكود
                 * داخل name="code".
                 */
                form.addEventListener(
                    'submit',
                    function (event) {

                        syncCode();

                        const code =
                            hiddenCode.value;


                        if (
                            !/^\d{6}$/.test(
                                code
                            )
                        ) {

                            event.preventDefault();

                            const firstEmpty =
                                inputs.find(
                                    input =>
                                        !input.value
                                );

                            if (firstEmpty) {

                                firstEmpty.focus();

                            }

                            return;

                        }


                        verifyButton.disabled =
                            true;

                        verifyButton.textContent =
                            'جارٍ التحقق...';

                    }
                );

            }
        );
    </script>

</body>
</html>
