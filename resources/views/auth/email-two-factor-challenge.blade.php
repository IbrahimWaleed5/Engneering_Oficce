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

    <title>التحقق بخطوتين - مكتب الوليد الهندسي</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            color: #f8fafc;
            background: #050b14;
            font-family: Tahoma, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .bg-complex {
            position: fixed;
            inset: 0;
            z-index: -3;
            background:
                radial-gradient(
                    circle at 50% 0%,
                    rgba(37, 99, 235, .16),
                    transparent 58%
                ),
                radial-gradient(
                    circle at 0% 100%,
                    rgba(15, 23, 42, .9),
                    transparent 50%
                ),
                #050b14;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: -2;
            opacity: .5;
            background-image:
                linear-gradient(
                    to right,
                    rgba(255,255,255,.03) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    to bottom,
                    rgba(255,255,255,.03) 1px,
                    transparent 1px
                );
            background-size: 40px 40px;
            mask-image: radial-gradient(circle, black, transparent 80%);
            -webkit-mask-image: radial-gradient(circle, black, transparent 80%);
        }

        .brand {
            position: absolute;
            top: 30px;
            left: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 20;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: 1px solid rgba(255,255,255,.12);
            box-shadow: 0 0 22px rgba(37,99,235,.3);
        }

        .brand-name {
            font-size: 14px;
            font-weight: 900;
            color: rgba(255,255,255,.92);
        }

        .container {
            width: 100%;
            max-width: 460px;
            padding: 90px 20px 35px;
            position: relative;
            z-index: 10;
        }

        .card {
            padding: 42px 36px;
            border-radius: 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(
                    145deg,
                    rgba(15,23,42,.76),
                    rgba(5,11,20,.9)
                );
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,.08);
            border-top-color: rgba(255,255,255,.15);
            box-shadow:
                0 30px 60px rgba(0,0,0,.58),
                inset 0 0 40px rgba(37,99,235,.08);
        }

        .top-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background:
                linear-gradient(
                    to right,
                    transparent,
                    rgba(59,130,246,.65),
                    transparent
                );
        }

        .icon-box {
            width: 82px;
            height: 82px;
            margin: 0 auto 26px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 35px;
            background: #0f172a;
            border: 1px solid rgba(59,130,246,.2);
            box-shadow:
                inset 0 2px 10px rgba(0,0,0,.3),
                0 0 30px rgba(37,99,235,.12);
        }

        h1 {
            margin: 0 0 12px;
            font-size: 29px;
            font-weight: 900;
            color: white;
        }

        .description {
            margin: 0 auto 28px;
            max-width: 350px;
            font-size: 14px;
            line-height: 1.9;
            color: #94a3b8;
        }

        .alert {
            margin-bottom: 18px;
            padding: 13px 14px;
            border-radius: 13px;
            font-size: 13px;
            line-height: 1.7;
            text-align: right;
        }

        .success {
            color: #bbf7d0;
            border: 1px solid rgba(34,197,94,.3);
            background: rgba(22,101,52,.22);
        }

        .error {
            color: #fecaca;
            border: 1px solid rgba(239,68,68,.3);
            background: rgba(127,29,29,.22);
        }

        .passkey-box {
            padding: 22px;
            border-radius: 20px;
            border: 1px solid rgba(139,92,246,.2);
            background: rgba(30,41,59,.45);
        }

        .passkey-status {
            min-height: 24px;
            margin-top: 14px;
            font-size: 13px;
            line-height: 1.7;
            color: #a5b4fc;
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 13px;
            padding: 14px 18px;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            font-weight: 900;
            transition: .25s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-passkey {
            color: white;
            background: linear-gradient(135deg, #8b5cf6, #2563eb);
            box-shadow: 0 7px 25px rgba(99,102,241,.25);
        }

        .btn-email {
            margin-top: 12px;
            color: #cbd5e1;
            border: 1px solid rgba(148,163,184,.2);
            background: rgba(15,23,42,.68);
        }

        .btn-primary {
            margin-top: 20px;
            color: white;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 6px 22px rgba(37,99,235,.28);
        }

        .otp-inputs {
            direction: ltr;
            display: flex;
            justify-content: center;
            gap: 9px;
            margin: 4px 0 8px;
        }

        .otp-input {
            direction: ltr;
            width: 49px;
            height: 57px;
            padding: 0;
            border-radius: 12px;
            text-align: center;
            outline: none;
            color: white;
            font-size: 23px;
            font-weight: 900;
            background: rgba(10,16,29,.88);
            border: 2px solid rgba(71,85,105,.5);
            transition: .2s ease;
        }

        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(37,99,235,.2);
        }

        .email-note {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 13px;
            color: #94a3b8;
            background: rgba(15,23,42,.55);
            border: 1px solid rgba(255,255,255,.06);
            font-size: 12px;
            line-height: 1.8;
            word-break: break-word;
        }

        .divider {
            height: 1px;
            margin: 22px 0;
            background: rgba(255,255,255,.07);
        }

        .secure-note {
            margin-top: 22px;
            font-size: 12px;
            color: #64748b;
        }

        @media (max-width: 560px) {
            .brand {
                top: 20px;
                left: 20px;
            }

            .brand-name {
                display: none;
            }

            .container {
                padding-left: 16px;
                padding-right: 16px;
            }

            .card {
                padding: 36px 18px;
            }

            .otp-inputs {
                gap: 6px;
            }

            .otp-input {
                width: calc((100vw - 82px) / 6);
                max-width: 46px;
                height: 54px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-complex"></div>
    <div class="bg-grid"></div>

    <header class="brand">
        <div class="brand-icon">⌁</div>

        <span class="brand-name">
            مكتب الوليد الهندسي
        </span>
    </header>

    <main class="container">
        <div class="card">
            <div class="top-line"></div>

            @if ($mode === 'passkey' && $hasPasskeys)
                <div class="icon-box">👆</div>

                <h1>تحقق باستخدام Passkey</h1>

                <p class="description">
                    استخدم بصمة الإصبع أو Face ID أو PIN الجهاز
                    لإكمال تسجيل الدخول بسرعة.
                </p>
            @else
                <div class="icon-box">🔐</div>

                <h1>التحقق بخطوتين</h1>

                <p class="description">
                    أدخل رمز التحقق المكوّن من 6 أرقام
                    الذي أرسلناه إلى بريدك الإلكتروني.
                </p>
            @endif

            @if (session('success'))
                <div class="alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert error">
                    {{ session('error') }}
                </div>
            @endif

            @error('credential')
                <div class="alert error">
                    تعذر التحقق باستخدام Passkey. يمكنك المحاولة مجددًا
                    أو استخدام رمز البريد.
                </div>
            @enderror

            @if ($mode === 'passkey' && $hasPasskeys)

                <div class="passkey-box">
                    <button
                        type="button"
                        id="passkey_button"
                        class="btn btn-passkey"
                    >
                        👆 استخدام البصمة / Passkey
                    </button>

                    <div
                        id="passkey_status"
                        class="passkey-status"
                    >
                        جارٍ تجهيز التحقق...
                    </div>

                    <form
                        method="POST"
                        action="{{ route('email-2fa.use-email') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn btn-email"
                        >
                            لا أستطيع الوصول إلى البصمة — إرسال كود للبريد
                        </button>
                    </form>
                </div>

            @else

                <div class="email-note">
                    تم إرسال الرمز إلى:
                    <strong style="color:white">
                        {{ $email }}
                    </strong>
                </div>

                @error('code')
                    <div class="alert error">
                        {{ $message }}
                    </div>
                @enderror

                <form
                    id="verify_2fa_form"
                    method="POST"
                    action="{{ route('email-2fa.verify') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="code"
                        id="full_2fa_code"
                    >

                    <div class="otp-inputs" dir="ltr">
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                type="text"
                                maxlength="1"
                                class="otp-input"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                dir="ltr"
                                {{ $i === 0 ? 'autocomplete=one-time-code autofocus' : '' }}
                                required
                            >
                        @endfor
                    </div>

                    <button
                        id="verify_button"
                        class="btn btn-primary"
                        type="submit"
                    >
                        تأكيد الدخول
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('email-2fa.resend') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn btn-email"
                    >
                        إعادة إرسال الرمز
                    </button>
                </form>

            @endif

            <div class="secure-note">
                🔒 جلسة تسجيل دخول محمية
            </div>
        </div>
    </main>

    @if ($mode === 'passkey' && $hasPasskeys)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const button =
                    document.getElementById('passkey_button');

                const status =
                    document.getElementById('passkey_status');

                let running = false;
                let autoTried = false;

                async function verifyPasskey() {
                    if (running) {
                        return;
                    }

                    if (!window.PublicKeyCredential) {
                        status.textContent =
                            'هذا المتصفح لا يدعم Passkeys. استخدم رمز البريد.';
                        return;
                    }

                    if (!window.AlwaleedPasskeys?.verify) {
                        status.textContent =
                            'تعذر تحميل Passkeys. حدّث الصفحة أو استخدم رمز البريد.';
                        return;
                    }

                    running = true;
                    button.disabled = true;
                    status.textContent =
                        'استخدم البصمة أو الوجه أو PIN الجهاز...';

                    try {
                        const response =
                            await window.AlwaleedPasskeys.verify({
                                routes: {
                                    options:
                                        "{{ route('email-2fa.passkey.options') }}",
                                    submit:
                                        "{{ route('email-2fa.passkey.verify') }}",
                                },
                            });

                        if (response?.redirect) {
                            window.location.href =
                                response.redirect;
                            return;
                        }

                        window.location.href =
                            "{{ route('dashboard') }}";
                    } catch (error) {
                        console.error(error);

                        status.textContent =
                            'لم يتم استخدام Passkey. حاول مجددًا أو اختر إرسال كود للبريد.';
                    } finally {
                        running = false;
                        button.disabled = false;
                    }
                }

                button.addEventListener(
                    'click',
                    verifyPasskey
                );

                /*
                 * محاولة تلقائية بمجرد فتح الصفحة.
                 * بعض المتصفحات قد تتطلب ضغطة من المستخدم؛
                 * لذلك يبقى الزر ظاهرًا كخيار يدوي.
                 */
                if (!autoTried) {
                    autoTried = true;

                    setTimeout(
                        verifyPasskey,
                        350
                    );
                }
            });
        </script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = Array.from(
                    document.querySelectorAll('.otp-input')
                );

                const form =
                    document.getElementById('verify_2fa_form');

                const hiddenCode =
                    document.getElementById('full_2fa_code');

                const verifyButton =
                    document.getElementById('verify_button');

                function clean(value) {
                    return String(value || '')
                        .replace(/\D/g, '');
                }

                function sync() {
                    hiddenCode.value = inputs
                        .map((input) => clean(input.value))
                        .join('');
                }

                inputs.forEach((input, index) => {
                    input.addEventListener(
                        'input',
                        function () {
                            this.value =
                                clean(this.value).slice(-1);

                            sync();

                            if (
                                this.value
                                && index < inputs.length - 1
                            ) {
                                inputs[index + 1].focus();
                                inputs[index + 1].select();
                            }
                        }
                    );

                    input.addEventListener(
                        'keydown',
                        function (event) {
                            if (
                                event.key === 'Backspace'
                                && this.value === ''
                                && index > 0
                            ) {
                                event.preventDefault();
                                inputs[index - 1].focus();
                                inputs[index - 1].select();
                            }
                        }
                    );

                    input.addEventListener(
                        'paste',
                        function (event) {
                            const clipboard =
                                event.clipboardData
                                || window.clipboardData;

                            if (!clipboard) {
                                return;
                            }

                            const pasted =
                                clean(
                                    clipboard.getData('text')
                                ).slice(0, 6);

                            if (!pasted) {
                                return;
                            }

                            event.preventDefault();

                            inputs.forEach(
                                (item) => item.value = ''
                            );

                            pasted.split('').forEach(
                                (digit, digitIndex) => {
                                    if (inputs[digitIndex]) {
                                        inputs[digitIndex].value =
                                            digit;
                                    }
                                }
                            );

                            sync();

                            const focusIndex = Math.min(
                                pasted.length,
                                inputs.length
                            ) - 1;

                            inputs[focusIndex]?.focus();
                        }
                    );
                });

                form.addEventListener(
                    'submit',
                    function (event) {
                        sync();

                        if (
                            !/^\d{6}$/.test(
                                hiddenCode.value
                            )
                        ) {
                            event.preventDefault();

                            const firstEmpty =
                                inputs.find(
                                    (input) =>
                                        input.value === ''
                                );

                            firstEmpty?.focus();
                            return;
                        }

                        verifyButton.disabled = true;
                        verifyButton.textContent =
                            'جارٍ التحقق...';
                    }
                );
            });
        </script>
    @endif
</body>
</html>
