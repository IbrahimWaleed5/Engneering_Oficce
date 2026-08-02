<x-app-layout>
    @php
        $currentUser = auth()->user();

        $allPayments = $payments->count();

        $pendingPayments = $payments
            ->where('status', 'pending')
            ->count();

        $completedPayments = $payments
            ->where('status', 'completed')
            ->count();

        $rejectedPayments = $payments
            ->where('status', 'rejected')
            ->count();

        $completedAmount = $payments
            ->where('status', 'completed')
            ->sum('amount');

        $paymentMethodLabels = [
            'cash' => 'نقدي',
            'card' => 'بطاقة بنكية',
            'bank' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية',
        ];
    @endphp

    <style>
        .payments-design-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .payments-glass-card {
            background: rgba(23, 31, 51, .4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .payments-card-hover {
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .payments-card-hover:hover {
            transform: scale(1.02);
            border-color: rgba(180, 197, 255, .25);
            box-shadow: 0 0 20px rgba(37, 99, 235, .12);
        }

        .payments-row:hover {
            background: rgba(45, 52, 73, .4);
        }

        .payments-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .payments-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .payments-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 4px;
        }

        .payments-scroll::-webkit-scrollbar-thumb:hover {
            background: #434655;
        }

        @media (max-width: 1023px) {
            .payments-sidebar {
                display: none !important;
            }

            .payments-main {
                margin-right: 0 !important;
            }

            .payments-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="payments-design-page" dir="rtl">
        {{-- القائمة الجانبية --}}
        <aside class="payments-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#131b2e]/90 p-4 shadow-xl backdrop-blur-xl">
            <div class="px-4 mb-10">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">
                    CreativeHome
                </h1>

                <p class="text-sm text-[#c3c6d7] opacity-60">
                    Engineering Office
                </p>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>لوحة التحكم</span>
                </a>

                <a
                    href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>الاستشارات</span>
                </a>

                <a
                    href="{{ Route::has('users.index') ? route('users.index') : route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>المستخدمون</span>
                </a>

                <a
                    href="{{ route('payments.index') }}"
                    class="flex items-center gap-3 rounded-xl bg-[#2563eb]/20 px-4 py-3 font-bold text-[#b4c5ff] shadow-[0_0_15px_rgba(37,99,235,.1)]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>

                    <span>الدفعات</span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-6 mt-auto space-y-2 border-t border-[#434655]/10">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9.7 9a2.5 2.5 0 1 1 3.5 2.3c-.8.35-1.2.8-1.2 1.7M12 17h.01"/>
                    </svg>

                    <span>الدعم</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-3 text-[#c3c6d7] transition hover:text-red-300"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>
                        </svg>

                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- الشريط العلوي --}}
        <header class="payments-topbar fixed top-0 left-0 right-64 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#060e20]/60 px-6 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-black text-[#dae2fd]">
                    سجل الدفعات
                </h2>

                <div class="h-6 w-px bg-[#434655]/30"></div>

                <div class="relative hidden md:block">
                    <svg class="absolute w-5 h-5 -translate-y-1/2 left-3 top-1/2 text-[#8d90a0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        id="paymentsLiveSearch"
                        type="search"
                        placeholder="بحث عن معاملة..."
                        class="w-64 rounded-full border-0 bg-[#131b2e] py-2 pr-4 pl-10 text-sm text-white placeholder:text-[#8d90a0] focus:ring-1 focus:ring-[#b4c5ff]"
                    >
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                    class="flex items-center justify-center p-2 transition rounded-full hover:bg-white/5"
                    title="الإشعارات"
                >
                    <svg class="w-5 h-5 text-[#dae2fd]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                </a>

                <a
                    href="{{ Route::has('conversations.index') ? route('conversations.index') : route('dashboard') }}"
                    class="flex items-center justify-center p-2 transition rounded-full hover:bg-white/5"
                    title="المحادثات"
                >
                    <svg class="w-5 h-5 text-[#dae2fd]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                    </svg>
                </a>

                <div class="flex items-center gap-3 pr-4 border-r border-[#434655]/20">
                    <div class="hidden text-left sm:block">
                        <p class="text-xs font-bold leading-tight text-[#dae2fd]">
                            {{ $currentUser->name }}
                        </p>

                        <p class="text-[11px] text-[#c3c6d7] opacity-60">
                            مشرف النظام
                        </p>
                    </div>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center justify-center w-10 h-10 overflow-hidden border rounded-xl border-[#b4c5ff]/20"
                    >
                        @if ($currentUser->profile_photo)
                            <img
                                src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                alt="{{ $currentUser->name }}"
                                class="object-cover w-full h-full"
                            >
                        @else
                            <span class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-purple-600">
                                {{ mb_substr($currentUser->name, 0, 1) }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </header>

        <main class="min-h-screen px-6 pt-24 pb-12 payments-main lg:mr-64">
            <div class="mx-auto max-w-[1700px] space-y-8">
                {{-- الرسائل --}}
                @if (session('success'))
                    <div class="p-4 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- الإحصائيات --}}
                <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <article class="flex items-center justify-between p-5 payments-glass-card payments-card-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                جميع الدفعات
                            </p>

                            <h3 class="text-3xl font-black text-[#dae2fd]">
                                {{ $allPayments }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="M3 10h18M7 15h4"/>
                            </svg>
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 payments-glass-card payments-card-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                قيد المراجعة
                            </p>

                            <h3 class="text-3xl font-black text-[#d2bbff]">
                                {{ $pendingPayments }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#d2bbff]/10 text-[#d2bbff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>
                    </article>

                    <article class="payments-glass-card payments-card-hover flex items-center justify-between rounded-2xl border border-[#b4c5ff]/20 bg-[#b4c5ff]/5 p-5 shadow-[0_0_10px_rgba(180,197,255,.1)]">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#b4c5ff]">
                                الدفعات المقبولة
                            </p>

                            <h3 class="text-3xl font-black text-[#b4c5ff]">
                                {{ $completedPayments }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/20 text-[#b4c5ff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 payments-glass-card payments-card-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                الدفعات المرفوضة
                            </p>

                            <h3 class="text-3xl font-black text-red-300">
                                {{ $rejectedPayments }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-red-300 rounded-xl bg-red-500/10">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M8 8l8 8M16 8l-8 8"/>
                            </svg>
                        </div>
                    </article>

                    <article class="payments-glass-card payments-card-hover relative flex items-center justify-between overflow-hidden rounded-2xl border border-[#2563eb]/20 bg-[#2563eb]/10 p-5">
                        <div class="relative z-10">
                            <p class="mb-1 text-xs font-bold uppercase text-[#eeefff]">
                                إجمالي المقبول
                            </p>

                            <h3 class="text-2xl font-black text-[#eeefff]">
                                {{ number_format($completedAmount, 2) }}
                                <span class="text-sm font-normal">₪</span>
                            </h3>
                        </div>

                        <div class="relative z-10 flex items-center justify-center w-12 h-12 text-white rounded-xl bg-[#2563eb]/30">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M4 7h16v11H4zM7 7V4h10v3"/>
                                <circle cx="12" cy="12.5" r="2"/>
                            </svg>
                        </div>
                    </article>
                </section>

                {{-- الفلاتر --}}
                <section id="paymentFilterPanel" class="hidden p-6 payments-glass-card rounded-3xl">
                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="paymentStatusFilter" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                حالة الدفعة
                            </label>

                            <select
                                id="paymentStatusFilter"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">جميع الحالات</option>
                                <option value="pending">قيد المراجعة</option>
                                <option value="completed">مقبولة</option>
                                <option value="rejected">مرفوضة</option>
                            </select>
                        </div>

                        <div>
                            <label for="paymentMethodFilter" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                طريقة الدفع
                            </label>

                            <select
                                id="paymentMethodFilter"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">جميع الطرق</option>
                                <option value="cash">نقدي</option>
                                <option value="card">بطاقة بنكية</option>
                                <option value="bank">تحويل بنكي</option>
                                <option value="wallet">محفظة إلكترونية</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                id="resetPaymentFilters"
                                type="button"
                                class="w-full rounded-xl bg-[#2d3449] px-5 py-3 font-bold text-[#dae2fd] transition hover:bg-[#31394d]"
                            >
                                مسح الفلاتر
                            </button>
                        </div>
                    </div>
                </section>

                {{-- الجدول --}}
                <section class="relative overflow-hidden shadow-2xl payments-glass-card rounded-3xl">
                    <div class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-[#434655]/10">
                        <div>
                            <h4 class="text-2xl font-bold text-[#dae2fd]">
                                سجل المعاملات
                            </h4>

                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                جميع طلبات الدفع المرسلة من العملاء للمراجعة
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button
                                id="togglePaymentFilters"
                                type="button"
                                class="flex items-center gap-2 rounded-xl border border-[#434655]/20 bg-[#222a3d] px-4 py-2 text-[#dae2fd] transition hover:bg-[#31394d]"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/>
                                </svg>

                                <span class="text-xs font-bold">تصفية</span>
                            </button>

                            <a
                                href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                                class="flex items-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2 text-white shadow-lg transition hover:brightness-110 active:scale-95"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>

                                <span class="text-xs font-bold">إضافة دفعة</span>
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto payments-scroll">
                        <table class="w-full min-w-[1200px] text-right">
                            <thead>
                                <tr class="bg-[#131b2e]/50">
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">رقم المعاملة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">العميل</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">المبلغ</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">طريقة الدفع</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">الحالة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">تاريخ الإرسال</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#434655]/10">
                                @forelse ($payments as $payment)
                                    @php
                                        $receiptUrl = $payment->receipt_image
                                            ? route('payments.receipt', $payment)
                                            : null;

                                        $isPdf = $payment->receipt_image
                                            ? str_ends_with(
                                                strtolower($payment->receipt_image),
                                                '.pdf'
                                            )
                                            : false;

                                        $paymentSearchText = strtolower(
                                            ($payment->consultation?->consultation_number ?? '') . ' ' .
                                            ($payment->customer?->name ?? '') . ' ' .
                                            ($payment->customer?->email ?? '') . ' ' .
                                            ($payment->transaction_reference ?? '') . ' ' .
                                            ($paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method)
                                        );
                                    @endphp

                                    <tr
                                        data-payment-row
                                        data-search="{{ $paymentSearchText }}"
                                        data-status="{{ $payment->status }}"
                                        data-method="{{ $payment->payment_method }}"
                                        class="payments-row transition-colors {{ $payment->status === 'rejected' ? 'bg-red-500/[0.03]' : '' }}"
                                    >
                                        <td class="px-6 py-5">
                                            <span class="rounded-lg bg-[#b4c5ff]/10 px-3 py-1 text-xs font-bold text-[#b4c5ff]">
                                                {{ $payment->consultation?->consultation_number ?? '—' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-[#b4c5ff] to-[#d2bbff] text-[10px] font-bold text-[#002a78]">
                                                    {{ mb_substr($payment->customer?->name ?? 'ع', 0, 1) }}
                                                </div>

                                                <div>
                                                    <p class="text-xs font-bold text-[#dae2fd]">
                                                        {{ $payment->customer?->name ?? 'عميل غير معروف' }}
                                                    </p>

                                                    <p class="text-[10px] text-[#8d90a0]">
                                                        {{ $payment->customer?->email ?? '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="text-sm font-bold text-[#dae2fd]">
                                                {{ number_format($payment->amount, 2) }}
                                            </span>

                                            <span class="mr-1 text-[11px] text-[#8d90a0]">₪</span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2 text-sm text-[#dae2fd]">
                                                <svg class="w-5 h-5 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                                    <path d="M3 10h18M7 15h4"/>
                                                </svg>

                                                <span>
                                                    {{ $paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            @if ($payment->status === 'pending')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[11px] font-bold text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    قيد المراجعة
                                                </span>
                                            @elseif ($payment->status === 'completed')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-[11px] font-bold text-green-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                                    مقبولة
                                                </span>
                                            @elseif ($payment->status === 'rejected')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-[11px] font-bold text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                    مرفوضة
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-white/5 px-3 py-1 text-[11px] font-bold text-[#c3c6d7]">
                                                    {{ $payment->status }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <p class="text-sm text-[#dae2fd]">
                                                {{ $payment->created_at?->format('Y-m-d') }}
                                            </p>

                                            <p class="text-[10px] text-[#8d90a0]">
                                                {{ $payment->created_at?->format('H:i') }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-2">
                                                @if ($receiptUrl)
                                                    <a
                                                        href="{{ $receiptUrl }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#b4c5ff]/10 text-[#b4c5ff] transition hover:bg-[#b4c5ff]/20"
                                                        title="{{ $isPdf ? 'عرض ملف PDF' : 'عرض الإيصال' }}"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                                            <circle cx="12" cy="12" r="2.5"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    $payment->status === 'completed'
                                                    && $payment->invoice
                                                )
                                                    <a
                                                        href="{{ route('invoices.download', $payment->invoice) }}"
                                                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-white/5 text-[#c3c6d7] transition hover:bg-white/10"
                                                        title="تحميل PDF"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    auth()->user()->role === 'admin'
                                                    && $payment->status === 'pending'
                                                )
                                                    <form
                                                        method="POST"
                                                        action="{{ route('payments.confirm', $payment) }}"
                                                        data-confirm-payment
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="flex items-center justify-center text-green-300 transition rounded-lg w-9 h-9 bg-green-500/10 hover:bg-green-500/20"
                                                            title="قبول الدفعة"
                                                        >
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="m5 12 4 4L19 6"/>
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <button
                                                        type="button"
                                                        data-open-payment-reject
                                                        data-reject-url="{{ route('payments.reject', $payment) }}"
                                                        data-customer-name="{{ $payment->customer?->name ?? 'العميل' }}"
                                                        class="flex items-center justify-center text-red-300 transition rounded-lg w-9 h-9 bg-red-500/10 hover:bg-red-500/20"
                                                        title="رفض الدفعة"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M6 18 18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                @elseif ($payment->status === 'completed')
                                                    <span class="inline-flex items-center px-3 py-2 text-xs font-bold text-green-300 rounded-lg bg-green-500/10">
                                                        تم القبول
                                                    </span>
                                                @elseif ($payment->status === 'rejected')
                                                    <button
                                                        type="button"
                                                        data-show-rejection
                                                        data-rejection-reason="{{ $payment->rejection_reason ?: 'لم يتم تحديد السبب' }}"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-bold text-red-300 rounded-lg bg-red-500/10 hover:bg-red-500/20"
                                                    >
                                                        سبب الرفض
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center text-[#c3c6d7]">
                                            لا توجد دفعات حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (
                        method_exists($payments, 'hasPages')
                        && $payments->hasPages()
                    )
                        <div class="p-6 border-t border-[#434655]/10">
                            {{ $payments->withQueryString()->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>

        {{-- نافذة رفض الدفعة --}}
        <div
            id="paymentRejectModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-[#060e20]/90 p-5 backdrop-blur-xl"
        >
            <div
                id="paymentRejectPanel"
                class="payments-glass-card w-full max-w-xl rounded-[2rem] p-7 shadow-2xl"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-center text-red-300 w-14 h-14 rounded-2xl bg-red-500/10">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M12 3 2 21h20L12 3Z"/>
                                <path d="M12 9v5M12 18h.01"/>
                            </svg>
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            رفض الدفعة
                        </h2>

                        <p class="mt-2 text-sm text-[#c3c6d7]">
                            العميل:
                            <span id="paymentRejectCustomer" class="font-bold text-white"></span>
                        </p>
                    </div>

                    <button
                        type="button"
                        data-close-payment-modal
                        class="flex items-center justify-center text-white transition border rounded-full w-11 h-11 border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        ✕
                    </button>
                </div>

                <form
                    id="paymentRejectForm"
                    method="POST"
                    action=""
                    class="mt-7"
                >
                    @csrf
                    @method('PATCH')

                    <label class="block mb-2 text-sm font-bold text-[#dae2fd]">
                        سبب رفض الإيصال
                    </label>

                    <textarea
                        name="rejection_reason"
                        rows="5"
                        required
                        placeholder="اكتب سبب رفض الدفعة..."
                        class="w-full px-4 py-4 text-white transition border outline-none resize-none rounded-2xl border-white/10 bg-[#060e20]/60 placeholder:text-[#c3c6d7]/50 focus:border-red-400 focus:ring-2 focus:ring-red-400/10"
                    ></textarea>

                    <div class="grid gap-3 mt-6 sm:grid-cols-2">
                        <button
                            type="button"
                            data-close-payment-modal
                            class="px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            إلغاء
                        </button>

                        <button
                            type="submit"
                            class="px-5 py-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                        >
                            تأكيد رفض الدفعة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- نافذة عرض سبب الرفض --}}
        <div
            id="rejectionReasonModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-[#060e20]/90 p-5 backdrop-blur-xl"
        >
            <div class="payments-glass-card w-full max-w-lg rounded-[2rem] p-7 shadow-2xl">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-white">
                        سبب رفض الدفعة
                    </h2>

                    <button
                        type="button"
                        data-close-reason-modal
                        class="flex items-center justify-center text-white transition border rounded-full w-11 h-11 border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        ✕
                    </button>
                </div>

                <p
                    id="rejectionReasonText"
                    class="mt-6 leading-8 text-red-200"
                ></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput =
                document.getElementById(
                    'paymentsLiveSearch'
                );

            const statusFilter =
                document.getElementById(
                    'paymentStatusFilter'
                );

            const methodFilter =
                document.getElementById(
                    'paymentMethodFilter'
                );

            const resetFilters =
                document.getElementById(
                    'resetPaymentFilters'
                );

            const filterPanel =
                document.getElementById(
                    'paymentFilterPanel'
                );

            const toggleFilters =
                document.getElementById(
                    'togglePaymentFilters'
                );

            const rows =
                Array.from(
                    document.querySelectorAll(
                        '[data-payment-row]'
                    )
                );

            const applyFilters = () => {
                const query =
                    (searchInput?.value || '')
                        .trim()
                        .toLowerCase();

                const status =
                    statusFilter?.value || 'all';

                const method =
                    methodFilter?.value || 'all';

                rows.forEach((row) => {
                    const matchesSearch =
                        query === ''
                        || (
                            row.dataset.search || ''
                        ).includes(query);

                    const matchesStatus =
                        status === 'all'
                        || row.dataset.status === status;

                    const matchesMethod =
                        method === 'all'
                        || row.dataset.method === method;

                    row.classList.toggle(
                        'hidden',
                        !(
                            matchesSearch
                            && matchesStatus
                            && matchesMethod
                        )
                    );
                });
            };

            searchInput?.addEventListener(
                'input',
                applyFilters
            );

            statusFilter?.addEventListener(
                'change',
                applyFilters
            );

            methodFilter?.addEventListener(
                'change',
                applyFilters
            );

            toggleFilters?.addEventListener(
                'click',
                function () {
                    filterPanel?.classList.toggle(
                        'hidden'
                    );
                }
            );

            resetFilters?.addEventListener(
                'click',
                function () {
                    if (searchInput) {
                        searchInput.value = '';
                    }

                    if (statusFilter) {
                        statusFilter.value = 'all';
                    }

                    if (methodFilter) {
                        methodFilter.value = 'all';
                    }

                    applyFilters();
                }
            );

            document
                .querySelectorAll(
                    '[data-confirm-payment]'
                )
                .forEach((form) => {
                    form.addEventListener(
                        'submit',
                        function (event) {
                            const confirmed =
                                window.confirm(
                                    'هل أنت متأكد من قبول الدفعة؟'
                                );

                            if (!confirmed) {
                                event.preventDefault();
                                return;
                            }

                            const button =
                                form.querySelector(
                                    'button[type="submit"]'
                                );

                            if (button) {
                                button.disabled = true;
                                button.classList.add(
                                    'opacity-50',
                                    'cursor-not-allowed'
                                );
                            }
                        }
                    );
                });

            const rejectModal =
                document.getElementById(
                    'paymentRejectModal'
                );

            const rejectPanel =
                document.getElementById(
                    'paymentRejectPanel'
                );

            const rejectForm =
                document.getElementById(
                    'paymentRejectForm'
                );

            const rejectCustomer =
                document.getElementById(
                    'paymentRejectCustomer'
                );

            const openRejectModal = (button) => {
                if (
                    !rejectModal
                    || !rejectForm
                    || !rejectCustomer
                ) {
                    return;
                }

                rejectForm.action =
                    button.dataset.rejectUrl || '';

                rejectCustomer.textContent =
                    button.dataset.customerName || '';

                rejectModal.classList.remove('hidden');
                rejectModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };

            const closeRejectModal = () => {
                rejectModal?.classList.add('hidden');
                rejectModal?.classList.remove('flex');
                document.body.style.overflow = '';
            };

            document
                .querySelectorAll(
                    '[data-open-payment-reject]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        function () {
                            openRejectModal(button);
                        }
                    );
                });

            document
                .querySelectorAll(
                    '[data-close-payment-modal]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        closeRejectModal
                    );
                });

            rejectModal?.addEventListener(
                'click',
                function (event) {
                    if (event.target === rejectModal) {
                        closeRejectModal();
                    }
                }
            );

            rejectPanel?.addEventListener(
                'click',
                function (event) {
                    event.stopPropagation();
                }
            );

            rejectForm?.addEventListener(
                'submit',
                function () {
                    const button =
                        rejectForm.querySelector(
                            'button[type="submit"]'
                        );

                    if (button) {
                        button.disabled = true;
                        button.textContent =
                            'جاري الرفض...';

                        button.classList.add(
                            'opacity-60',
                            'cursor-not-allowed'
                        );
                    }
                }
            );

            const reasonModal =
                document.getElementById(
                    'rejectionReasonModal'
                );

            const reasonText =
                document.getElementById(
                    'rejectionReasonText'
                );

            const closeReasonModal = () => {
                reasonModal?.classList.add('hidden');
                reasonModal?.classList.remove('flex');
                document.body.style.overflow = '';
            };

            document
                .querySelectorAll(
                    '[data-show-rejection]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        function () {
                            if (!reasonModal || !reasonText) {
                                return;
                            }

                            reasonText.textContent =
                                button.dataset
                                    .rejectionReason || '';

                            reasonModal.classList.remove(
                                'hidden'
                            );

                            reasonModal.classList.add(
                                'flex'
                            );

                            document.body.style.overflow =
                                'hidden';
                        }
                    );
                });

            document
                .querySelectorAll(
                    '[data-close-reason-modal]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        closeReasonModal
                    );
                });

            reasonModal?.addEventListener(
                'click',
                function (event) {
                    if (event.target === reasonModal) {
                        closeReasonModal();
                    }
                }
            );

            document.addEventListener(
                'keydown',
                function (event) {
                    if (event.key === 'Escape') {
                        closeRejectModal();
                        closeReasonModal();
                    }
                }
            );
        });
    </script>
</x-app-layout>
