<x-app-layout>
    @php
        $currentUser = auth()->user();

        $dashboardUrl = route('dashboard');
        $notificationsUrl = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardUrl;
        $profileUrl = Route::has('profile.edit')
            ? route('profile.edit')
            : $dashboardUrl;
        $supportUrl = Route::has('support.index')
            ? route('support.index')
            : $dashboardUrl;

        $paymentAmount = number_format(
            (float) $consultation->final_price,
            2
        );

        $consultationNumber =
            $consultation->consultation_number
            ?? $consultation->number
            ?? ('CONS-' . $consultation->id);
    @endphp

    <style>
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800,
        body nav[data-layout-navigation],
        body header[data-layout-header] {
            display: none !important;
        }

        .payment-page {
            min-height: 100vh;
            color: #dae2fd;
            background:
                linear-gradient(
                    rgba(11, 19, 38, .94),
                    rgba(11, 19, 38, .98)
                ),
                radial-gradient(
                    circle at 35% 15%,
                    rgba(37, 99, 235, .13),
                    transparent 38%
                ),
                #0b1326;
            font-family:
                "Be Vietnam Pro",
                "Noto Sans Arabic",
                sans-serif;
        }

        .payment-page::before {
            position: fixed;
            inset: 0;
            z-index: 0;
            content: "";
            pointer-events: none;
            opacity: .09;
            background-image:
                linear-gradient(
                    rgba(180, 197, 255, .08) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(180, 197, 255, .08) 1px,
                    transparent 1px
                );
            background-size: 38px 38px;
        }

        .payment-glass {
            border: 1px solid rgba(180, 197, 255, .1);
            background: rgba(19, 27, 46, .72);
            backdrop-filter: blur(16px);
        }

        .payment-glow {
            border: 1px solid rgba(37, 99, 235, .4);
            box-shadow: 0 0 15px rgba(37, 99, 235, .2);
        }

        .payment-method-card {
            transition:
                transform .2s ease,
                border-color .2s ease,
                background-color .2s ease;
        }

        .payment-method-card:hover {
            transform: translateY(-2px);
            background: rgba(34, 42, 61, .9);
        }

        .payment-method-card.is-selected {
            border-color: rgba(180, 197, 255, .75);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .12);
        }

        .payment-upload-zone.is-dragging {
            border-color: #2563eb;
            background: rgba(37, 99, 235, .08);
        }

        .payment-step-line {
            position: absolute;
            top: 2rem;
            right: 1rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: linear-gradient(
                to bottom,
                #2563eb 0%,
                #434655 100%
            );
        }

        .payment-neon {
            text-shadow:
                0 0 8px rgba(180, 197, 255, .6);
        }

        @media (max-width: 767px) {
            .payment-desktop-header,
            .payment-sidebar {
                display: none !important;
            }

            .payment-main {
                padding-top: 1.25rem !important;
                padding-bottom: 6rem !important;
            }
        }
    </style>

    <div class="relative overflow-x-hidden payment-page" dir="rtl">
        {{-- الشريط العلوي --}}
        <header
            class="payment-desktop-header fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/85 px-6 backdrop-blur-xl"
        >
            <a
                href="{{ $dashboardUrl }}"
                class="flex items-center gap-3"
            >
                <img
                    src="{{ asset('images/Mainlogo.png') }}"
                    alt="مكتب الوليد الهندسي"
                    class="object-contain w-10 h-10 rounded-xl"
                >

                <span class="hidden font-black text-[#b4c5ff] md:block">
                    مكتب الوليد الهندسي
                </span>
            </a>

            <div class="flex items-center gap-4">
                <a
                    href="{{ $notificationsUrl }}"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                    title="الإشعارات"
                >
                    <span class="text-xl">🔔</span>
                </a>

                <a
                    href="{{ $profileUrl }}"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                    title="الإعدادات"
                >
                    <span class="text-xl">⚙️</span>
                </a>

                <a
                    href="{{ $profileUrl }}"
                    class="flex items-center justify-center overflow-hidden font-black text-white bg-blue-600 rounded-full h-9 w-9"
                    title="الحساب"
                >
                    @if ($currentUser?->profile_photo)
                        <img
                            src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                            alt="{{ $currentUser->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        {{ mb_substr($currentUser?->name ?? 'و', 0, 1) }}
                    @endif
                </a>
            </div>
        </header>

        <main
            class="relative z-10 flex flex-col gap-4 px-4 pt-24 pb-12 mx-auto payment-main max-w-7xl md:flex-row md:px-6"
        >
            {{-- الشريط الجانبي --}}
            <aside
                class="p-5 payment-sidebar payment-glass h-fit w-72 shrink-0 rounded-2xl"
            >
                <h3 class="mb-5 text-xs font-black uppercase tracking-[.22em] text-[#b4c5ff]">
                    مسار الطلب
                </h3>

                <div class="relative space-y-0">
                    <div class="relative flex gap-4 pb-8">
                        <div class="relative z-10 flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-full shrink-0">
                            ✓
                        </div>

                        <div>
                            <p class="text-xs font-black text-white">
                                إضافة التفاصيل
                            </p>
                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                تم اكتمال الطلب
                            </p>
                        </div>

                        <div class="payment-step-line"></div>
                    </div>

                    <div class="relative flex gap-4 pb-8">
                        <div class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-[#b4c5ff] bg-[#0b1326]">
                            <span class="h-2 w-2 rounded-full bg-[#b4c5ff]"></span>
                        </div>

                        <div>
                            <p class="payment-neon text-xs font-black text-[#b4c5ff]">
                                رفع الإيصال
                            </p>
                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                بانتظار التحميل
                            </p>
                        </div>
                    </div>

                    <div class="relative flex gap-4 pb-8">
                        <div class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-[#434655] bg-[#171f33]">
                            <span class="h-2 w-2 rounded-full bg-[#434655]"></span>
                        </div>

                        <div>
                            <p class="text-xs font-black text-[#c3c6d7]">
                                مراجعة المدير
                            </p>
                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                قيد الانتظار
                            </p>
                        </div>
                    </div>

                    <div class="relative flex gap-4">
                        <div class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-[#434655] bg-[#171f33]">
                            <span class="h-2 w-2 rounded-full bg-[#434655]"></span>
                        </div>

                        <div>
                            <p class="text-xs font-black text-[#c3c6d7]">
                                بدء الاستشارة
                            </p>
                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                قيد الانتظار
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-8 border-t border-white/10">
                    <div class="p-4 border rounded-xl border-blue-400/20 bg-blue-500/10">
                        <div class="mb-2 flex items-center gap-2 text-[#b4c5ff]">
                            <span>🎧</span>
                            <span class="text-xs font-black">
                                تحتاج مساعدة؟
                            </span>
                        </div>

                        <p class="mb-4 text-sm leading-7 text-[#c3c6d7]">
                            موظف الدعم متاح للرد على استفساراتك.
                        </p>

                        <a
                            href="{{ $supportUrl }}"
                            class="block w-full rounded-lg bg-blue-600 py-2 text-center text-xs font-black text-white transition hover:scale-[1.03]"
                        >
                            تحدث مع الدعم
                        </a>
                    </div>
                </div>
            </aside>

            {{-- المحتوى --}}
            <div class="flex-1 min-w-0 space-y-4">
                @if (session('success'))
                    <div class="p-4 text-green-200 border rounded-xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 text-red-200 border rounded-xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 text-red-200 border rounded-xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- ملخص الدفع --}}
                <section class="p-6 payment-glass payment-glow rounded-2xl md:p-8">
                    <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
                        <div>
                            <h1 class="text-3xl font-black text-white payment-neon">
                                إتمام عملية الدفع
                            </h1>

                            <p class="mt-2 text-[#c3c6d7]">
                                الرجاء إكمال الدفع لرفع حالة طلبك.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-[#2d3449] px-6 py-4 text-left">
                            <p class="mb-1 text-xs font-black uppercase text-[#c3c6d7]">
                                المبلغ المطلوب
                            </p>

                            <p class="text-3xl font-black text-[#b4c5ff]">
                                {{ $paymentAmount }}
                                <span class="text-xs">شيكل</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 rounded-xl border border-white/10 bg-[#131b2e]/60 p-4 md:grid-cols-2">
                        <div class="flex items-center gap-3">
                            <span class="text-[#b4c5ff]">🎟️</span>

                            <div>
                                <p class="text-xs text-[#c3c6d7]">
                                    رقم الاستشارة
                                </p>

                                <p class="text-xs font-black text-white">
                                    {{ $consultationNumber }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-pink-300">⏳</span>

                            <div>
                                <p class="text-xs text-[#c3c6d7]">
                                    الحالة الحالية
                                </p>

                                <p class="text-xs font-black text-pink-300">
                                    بانتظار إثبات الدفع
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <form
                    id="payment-form"
                    method="POST"
                    action="{{ route('payments.store', $consultation) }}"
                    enctype="multipart/form-data"
                    class="space-y-4"
                >
                    @csrf

                    <input
                        id="payment_method"
                        type="hidden"
                        name="payment_method"
                        value="{{ old('payment_method', 'bank') }}"
                    >

                    {{-- طرق الدفع --}}
                    <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <button
                            type="button"
                            data-payment-method="bank"
                            class="p-5 text-right payment-method-card payment-glass rounded-2xl"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/20 text-[#b4c5ff]">
                                        🏦
                                    </div>

                                    <h4 class="text-xl font-black text-white">
                                        تحويل بنكي
                                    </h4>
                                </div>

                                <span class="rounded-full bg-[#b4c5ff]/10 px-3 py-1 text-[10px] font-black text-[#b4c5ff]">
                                    موصى به
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between rounded-lg bg-[#0b1326]/50 p-3">
                                    <span class="text-xs text-[#c3c6d7]">
                                        اسم البنك
                                    </span>
                                    <span class="text-xs font-black text-white">
                                        مصرف الراجحي
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3 rounded-lg bg-[#0b1326]/50 p-3">
                                    <span class="text-xs text-[#c3c6d7]">
                                        رقم الآيبان
                                    </span>
                                    <span class="text-xs font-black text-left text-white break-all select-all">
                                        SA82 8000 0000 0000 0000 0000
                                    </span>
                                </div>

                                <div class="flex justify-between rounded-lg bg-[#0b1326]/50 p-3">
                                    <span class="text-xs text-[#c3c6d7]">
                                        اسم الحساب
                                    </span>
                                    <span class="text-xs font-black text-white">
                                        مكتب الوليد الهندسي
                                    </span>
                                </div>
                            </div>
                        </button>

                        <button
                            type="button"
                            data-payment-method="wallet"
                            class="p-5 text-right border-l-4 payment-method-card payment-glass rounded-2xl border-l-purple-500"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex items-center justify-center w-10 h-10 text-purple-300 rounded-xl bg-purple-500/20">
                                    📱
                                </div>

                                <h4 class="text-xl font-black text-white">
                                    المحافظ الإلكترونية
                                </h4>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-4 border rounded-xl border-white/10">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600 text-[10px] font-black text-white">
                                            STC
                                        </div>

                                        <span class="text-xs font-black text-white">
                                            STC Pay
                                        </span>
                                    </div>

                                    <span class="text-xs text-white select-all">
                                        0550000000
                                    </span>
                                </div>

                                <div class="flex items-center justify-between p-4 border opacity-50 cursor-not-allowed rounded-xl border-white/10">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#c3c6d7] text-[9px] font-black text-[#171f33]">
                                            Apple
                                        </div>

                                        <span class="text-xs font-black text-white">
                                            Apple Pay
                                        </span>
                                    </div>

                                    <span class="text-xs text-[#c3c6d7]">
                                        غير متوفر حاليًا
                                    </span>
                                </div>
                            </div>
                        </button>
                    </section>

                    {{-- رقم العملية --}}
                    <section class="p-6 payment-glass rounded-2xl">
                        <label
                            for="transaction_reference"
                            class="block mb-3 text-xs font-black text-white"
                        >
                            رقم العملية أو التحويل
                        </label>

                        <input
                            id="transaction_reference"
                            type="text"
                            name="transaction_reference"
                            value="{{ old('transaction_reference') }}"
                            placeholder="أدخل رقم العملية إن وجد"
                            class="w-full rounded-xl border border-white/10 bg-[#060e20] px-4 py-3 text-white placeholder:text-[#8d90a0] focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </section>

                    {{-- رفع الإيصال --}}
                    <section class="p-6 payment-glass rounded-2xl md:p-8">
                        <h3 class="mb-4 text-xs font-black text-white">
                            تحميل صورة الإيصال
                        </h3>

                        <label
                            id="payment-upload-zone"
                            for="receipt_image"
                            class="payment-upload-zone group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#434655] p-10 text-center transition hover:border-blue-500 hover:bg-blue-500/5"
                        >
                            <div
                                id="payment-upload-icon"
                                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#2d3449] text-3xl transition group-hover:scale-110"
                            >
                                ☁️
                            </div>

                            <p
                                id="payment-upload-title"
                                class="mb-2 text-xl font-black text-white"
                            >
                                اسحب الملف هنا أو اضغط للتحميل
                            </p>

                            <p
                                id="payment-upload-help"
                                class="text-sm text-[#c3c6d7]"
                            >
                                JPG، PNG، WEBP أو PDF بحد أقصى 5MB
                            </p>

                            <img
                                id="payment-receipt-preview"
                                src=""
                                alt="معاينة الإيصال"
                                class="hidden object-contain mt-5 max-h-72 rounded-xl"
                            >

                            <input
                                id="receipt_image"
                                type="file"
                                name="receipt_image"
                                accept=".jpg,.jpeg,.png,.webp,.pdf"
                                required
                                class="hidden"
                            >
                        </label>

                        <div class="flex items-center gap-4 p-4 mt-4 border rounded-xl border-pink-500/20 bg-pink-500/10">
                            <span class="text-pink-300">ℹ️</span>

                            <p class="text-sm text-pink-100/90">
                                يرجى التأكد من وضوح رقم العملية وتاريخ التحويل
                                في صورة الإيصال لتسريع الاعتماد.
                            </p>
                        </div>
                    </section>

                    {{-- الأزرار --}}
                    <div class="flex flex-col-reverse items-stretch justify-between gap-3 pt-4 sm:flex-row">
                        <a
                            href="{{ route('consultations.mine') }}"
                            class="rounded-xl border border-[#434655] px-8 py-3 text-center text-xs font-black text-white transition hover:bg-[#171f33]"
                        >
                            إلغاء الطلب
                        </a>

                        <button
                            id="payment-submit-button"
                            type="submit"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-pink-600 px-12 py-3 text-xs font-black text-white shadow-lg transition hover:scale-[1.03]"
                        >
                            إرسال للمراجعة
                        </button>
                    </div>
                </form>
            </div>
        </main>

        {{-- شريط الجوال --}}
        <nav
            class="fixed inset-x-0 bottom-0 z-50 flex h-16 items-center justify-around border-t border-white/10 bg-[#2d3449]/90 px-4 backdrop-blur-lg md:hidden"
        >
            <a
                href="{{ $dashboardUrl }}"
                class="flex flex-col items-center justify-center text-[#c3c6d7]"
            >
                <span>⌂</span>
                <span class="text-[10px]">الرئيسية</span>
            </a>

            <label
                for="receipt_image"
                class="flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-purple-600 text-white shadow-[0_0_10px_rgba(131,67,244,.5)]"
            >
                📎
            </label>

            <a
                href="{{ $supportUrl }}"
                class="flex flex-col items-center justify-center text-[#c3c6d7]"
            >
                <span>💬</span>
                <span class="text-[10px]">المحادثة</span>
            </a>

            <a
                href="{{ $profileUrl }}"
                class="flex flex-col items-center justify-center text-[#c3c6d7]"
            >
                <span>⚙️</span>
                <span class="text-[10px]">الإعدادات</span>
            </a>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const methodInput =
                document.getElementById('payment_method');

            const methodCards =
                Array.from(
                    document.querySelectorAll(
                        '[data-payment-method]'
                    )
                );

            function selectMethod(value) {
                if (! methodInput) {
                    return;
                }

                methodInput.value = value;

                methodCards.forEach(function (card) {
                    card.classList.toggle(
                        'is-selected',
                        card.dataset.paymentMethod === value
                    );
                });
            }

            methodCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    selectMethod(card.dataset.paymentMethod);
                });
            });

            selectMethod(
                methodInput?.value || 'bank'
            );

            const fileInput =
                document.getElementById('receipt_image');

            const uploadZone =
                document.getElementById('payment-upload-zone');

            const uploadIcon =
                document.getElementById('payment-upload-icon');

            const uploadTitle =
                document.getElementById('payment-upload-title');

            const uploadHelp =
                document.getElementById('payment-upload-help');

            const preview =
                document.getElementById('payment-receipt-preview');

            let previewUrl = null;

            function applyFile(file) {
                if (! file) {
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'application/pdf'
                ];

                if (! allowedTypes.includes(file.type)) {
                    alert(
                        'اختر ملفًا بصيغة JPG أو PNG أو WEBP أو PDF.'
                    );

                    if (fileInput) {
                        fileInput.value = '';
                    }

                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('حجم الملف يجب ألا يتجاوز 5MB.');

                    if (fileInput) {
                        fileInput.value = '';
                    }

                    return;
                }

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                if (uploadIcon) {
                    uploadIcon.textContent = '✅';
                }

                if (uploadTitle) {
                    uploadTitle.textContent =
                        'تم اختيار الملف بنجاح';
                }

                if (uploadHelp) {
                    uploadHelp.textContent = file.name;
                    uploadHelp.classList.add(
                        'font-black',
                        'text-[#b4c5ff]'
                    );
                }

                if (
                    preview
                    && file.type.startsWith('image/')
                ) {
                    previewUrl = URL.createObjectURL(file);
                    preview.src = previewUrl;
                    preview.classList.remove('hidden');
                } else if (preview) {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
            }

            fileInput?.addEventListener(
                'change',
                function () {
                    applyFile(fileInput.files?.[0]);
                }
            );

            [
                'dragenter',
                'dragover'
            ].forEach(function (eventName) {
                uploadZone?.addEventListener(
                    eventName,
                    function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        uploadZone.classList.add(
                            'is-dragging'
                        );
                    }
                );
            });

            [
                'dragleave',
                'drop'
            ].forEach(function (eventName) {
                uploadZone?.addEventListener(
                    eventName,
                    function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        uploadZone.classList.remove(
                            'is-dragging'
                        );
                    }
                );
            });

            uploadZone?.addEventListener(
                'drop',
                function (event) {
                    const file =
                        event.dataTransfer?.files?.[0];

                    if (! file || ! fileInput) {
                        return;
                    }

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    fileInput.files = transfer.files;

                    applyFile(file);
                }
            );

            const paymentForm =
                document.getElementById('payment-form');

            const submitButton =
                document.getElementById(
                    'payment-submit-button'
                );

            paymentForm?.addEventListener(
                'submit',
                function () {
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent =
                            'جاري الإرسال...';
                        submitButton.classList.add(
                            'opacity-60',
                            'cursor-not-allowed'
                        );
                    }
                }
            );
        });
    </script>
</x-app-layout>
