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

        $consultationCollection = method_exists($consultations, 'getCollection')
            ? $consultations->getCollection()
            : collect($consultations);

        $totalConsultations = method_exists($consultations, 'total')
            ? $consultations->total()
            : $consultationCollection->count();

        $reviewCount = $consultationCollection
            ->whereIn('status', ['pending'])
            ->count();

        $waitingPaymentCount = $consultationCollection
            ->where('status', 'waiting_payment')
            ->count();

        $completedCount = $consultationCollection
            ->where('status', 'completed')
            ->count();
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

        .consultations-my-page {
            min-height: 100vh;
            color: #dae2fd;
            background:
                linear-gradient(
                    rgba(11, 19, 38, .95),
                    rgba(11, 19, 38, .97)
                ),
                radial-gradient(
                    circle at 45% 15%,
                    rgba(37, 99, 235, .14),
                    transparent 42%
                ),
                #0b1326;
            font-family:
                "Noto Kufi Arabic",
                "Be Vietnam Pro",
                sans-serif;
        }

        .consultations-my-page::before {
            position: fixed;
            inset: 0;
            z-index: 0;
            content: "";
            pointer-events: none;
            opacity: .08;
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
            background-size: 42px 42px;
        }

        .consultations-glass {
            border: 1px solid rgba(255, 255, 255, .08);
            background: rgba(23, 31, 51, .72);
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .28);
        }

        .consultations-neon {
            box-shadow: 0 0 15px rgba(37, 99, 235, .35);
        }

        .consultations-card {
            transition:
                transform .25s ease,
                border-color .25s ease,
                box-shadow .25s ease;
        }

        .consultations-card:hover {
            transform: translateY(-3px);
            border-color: rgba(180, 197, 255, .2);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .34);
        }

        .consultations-mobile-menu {
            transition:
                transform .3s ease,
                opacity .3s ease;
        }

        .consultations-mobile-backdrop {
            transition: opacity .3s ease;
        }

        @media (max-width: 767px) {
            .consultations-desktop-nav,
            .consultations-sidebar {
                display: none !important;
            }

            .consultations-main {
                margin-right: 0 !important;
                padding:
                    5.75rem .85rem
                    6.5rem !important;
            }

            .consultations-header {
                align-items: stretch !important;
            }

            .consultations-header-action {
                width: 100% !important;
            }

            .consultations-stat-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
            }

            .consultations-card-main {
                padding: 1rem !important;
            }

            .consultations-meta-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
                gap: .85rem !important;
            }

            .consultations-actions {
                width: 100% !important;
            }

            .consultations-card-footer {
                padding:
                    .9rem 1rem !important;
            }
        }

        @media (max-width: 420px) {
            .consultations-stat-grid {
                grid-template-columns:
                    1fr !important;
            }

            .consultations-meta-grid {
                grid-template-columns:
                    1fr !important;
            }
        }
    </style>

    <div class="relative overflow-x-hidden consultations-my-page" dir="rtl">
        {{-- Top Navigation --}}
        <nav
            class="consultations-desktop-nav fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/85 px-6 backdrop-blur-xl"
        >
            <div class="flex items-center gap-4">
                <a
                    href="{{ $dashboardUrl }}"
                    class="flex items-center gap-3"
                >
                    <img
                        src="{{ asset('images/Mainlogo.png') }}"
                        alt="مكتب الوليد"
                        class="object-contain w-10 h-10 rounded-xl"
                    >

                    <span class="hidden font-black text-[#b4c5ff] md:block">
                        مكتب الوليد
                    </span>
                </a>

                <form
                    method="GET"
                    action="{{ route('consultations.mine') }}"
                    class="hidden items-center rounded-full border border-white/5 bg-[#131b2e] px-4 md:flex"
                >
                    <span class="ml-2 text-[#c3c6d7]">⌕</span>

                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="بحث عن معاملة..."
                        class="w-64 border-0 bg-transparent py-2 text-sm text-white placeholder:text-[#8d90a0] focus:ring-0"
                    >
                </form>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ $notificationsUrl }}"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                    title="الإشعارات"
                >
                    🔔
                </a>

                <a
                    href="{{ $profileUrl }}"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                    title="الإعدادات"
                >
                    ⚙️
                </a>

                <a
                    href="{{ $profileUrl }}"
                    class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-[#b4c5ff]/30 bg-blue-600 font-black text-white"
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
        </nav>

        {{-- Sidebar --}}
        <aside
            class="consultations-sidebar fixed right-0 top-0 z-40 flex h-screen w-64 flex-col border-l border-white/5 bg-[#171f33] px-4 pb-8 pt-20 shadow-xl"
        >
            <div class="px-4 mb-8">
                <span class="block text-xl font-black text-[#b4c5ff]">
                    مكتب الوليد
                </span>

                <span class="mt-1 block text-xs text-[#c3c6d7]/70">
                    الاستشارات الهندسية
                </span>
            </div>

            <nav class="flex flex-col flex-1 gap-1">
                <a
                    href="{{ $dashboardUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <span>▦</span>
                    <span class="text-xs font-black">لوحة التحكم</span>
                </a>

                <a
                    href="{{ route('consultations.mine') }}"
                    class="flex items-center gap-3 rounded-lg border-r-2 border-[#b4c5ff] bg-[#b4c5ff]/5 px-4 py-3 text-[#b4c5ff]"
                >
                    <span>🎟️</span>
                    <span class="text-xs font-black">استشاراتي</span>
                </a>

                <a
                    href="{{ route('consultations.create') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <span>🏗️</span>
                    <span class="text-xs font-black">استشارة جديدة</span>
                </a>

                <a
                    href="{{ route('engineer.works.public') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <span>▱</span>
                    <span class="text-xs font-black">مكتبة المهندسين</span>
                </a>
            </nav>

            <div class="flex flex-col gap-1 pt-4 border-t border-white/5">
                <a
                    href="{{ $supportUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <span>?</span>
                    <span class="text-xs font-black">الدعم الفني</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-3 text-red-300 transition rounded-lg hover:bg-red-500/10"
                    >
                        <span>↪</span>
                        <span class="text-xs font-black">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile Header --}}
        <header
            class="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/90 px-4 backdrop-blur-xl md:hidden"
        >
            <button
                id="consultations-mobile-open"
                type="button"
                class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-white/5"
                aria-label="فتح القائمة"
            >
                ☰
            </button>

            <div class="flex items-center gap-2">
                <img
                    src="{{ asset('images/Mainlogo.png') }}"
                    alt="الشعار"
                    class="object-contain h-9 w-9 rounded-xl"
                >

                <span class="text-sm font-black text-white">
                    استشاراتي
                </span>
            </div>

            <a
                href="{{ $notificationsUrl }}"
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5"
            >
                🔔
            </a>
        </header>

        {{-- Mobile Menu --}}
        <div
            id="consultations-mobile-backdrop"
            class="consultations-mobile-backdrop fixed inset-0 z-[80] hidden bg-black/70 opacity-0 md:hidden"
        ></div>

        <aside
            id="consultations-mobile-menu"
            class="consultations-mobile-menu pointer-events-none fixed right-0 top-0 z-[90] flex h-dvh w-[min(88vw,360px)] translate-x-full flex-col bg-[#131b2e] p-5 opacity-0 shadow-2xl md:hidden"
            aria-hidden="true"
        >
            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                <div>
                    <h2 class="font-black text-white">
                        مكتب الوليد
                    </h2>
                    <p class="mt-1 text-xs text-[#c3c6d7]">
                        الاستشارات الهندسية
                    </p>
                </div>

                <button
                    id="consultations-mobile-close"
                    type="button"
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5"
                >
                    ✕
                </button>
            </div>

            <nav class="mt-6 space-y-2">
                <a href="{{ $dashboardUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">لوحة التحكم</a>
                <a href="{{ route('consultations.mine') }}" class="block px-4 py-3 text-blue-200 rounded-xl bg-blue-500/15">استشاراتي</a>
                <a href="{{ route('consultations.create') }}" class="block px-4 py-3 rounded-xl bg-white/5">استشارة جديدة</a>
                <a href="{{ route('engineer.works.public') }}" class="block px-4 py-3 rounded-xl bg-white/5">مكتبة المهندسين</a>
                <a href="{{ $supportUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">الدعم الفني</a>
                <a href="{{ $profileUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">إعدادات الحساب</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="w-full px-4 py-3 font-black text-red-300 rounded-xl bg-red-500/10">
                    تسجيل الخروج
                </button>
            </form>
        </aside>

        {{-- Main --}}
        <main class="relative z-10 px-6 pt-24 pb-12 mr-64 consultations-main">
            <div class="mx-auto max-w-7xl">
                {{-- Header --}}
                <header class="flex flex-col justify-between gap-6 mb-8 consultations-header md:flex-row md:items-center">
                    <div class="flex items-center gap-3">
                        <div class="consultations-glass consultations-neon flex h-14 w-14 items-center justify-center rounded-xl text-2xl text-[#b4c5ff]">
                            📋
                        </div>

                        <div>
                            <h1 class="text-3xl font-black text-white">
                                استشاراتي
                            </h1>

                            <p class="mt-2 text-sm text-[#c3c6d7]">
                                تابع حالة الطلب، الدفع، والمخططات الهندسية النهائية.
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('consultations.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 font-black text-white transition consultations-header-action consultations-neon rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 hover:opacity-90"
                    >
                        <span>＋</span>
                        <span>استشارة جديدة</span>
                    </a>
                </header>

                {{-- Stats --}}
                <div class="grid grid-cols-1 gap-4 mb-8 consultations-stat-grid sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-center gap-4 p-5 consultations-glass rounded-xl">
                        <div class="flex items-center justify-center w-12 h-12 text-blue-300 rounded-lg bg-blue-500/10">▤</div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-[#c3c6d7]">إجمالي الطلبات</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $totalConsultations }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-5 consultations-glass rounded-xl">
                        <div class="flex items-center justify-center w-12 h-12 text-pink-300 rounded-lg bg-pink-500/10">⌛</div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-[#c3c6d7]">قيد المراجعة</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $reviewCount }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-5 consultations-glass rounded-xl">
                        <div class="flex items-center justify-center w-12 h-12 text-purple-300 rounded-lg bg-purple-500/10">💳</div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-[#c3c6d7]">بانتظار الدفع</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $waitingPaymentCount }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-5 consultations-glass rounded-xl">
                        <div class="flex items-center justify-center w-12 h-12 text-green-300 rounded-lg bg-green-500/10">✓</div>
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-[#c3c6d7]">الطلبات المكتملة</div>
                            <div class="mt-1 text-2xl font-black text-white">{{ $completedCount }}</div>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                @if (session('success'))
                    <div
                        id="consultations-success-alert"
                        class="flex items-center justify-between p-4 mb-8 border-l-4 border-green-500 consultations-glass rounded-xl bg-green-500/5"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-green-400">✅</span>
                            <p class="text-sm font-bold text-green-100">
                                {{ session('success') }}
                            </p>
                        </div>

                        <button
                            type="button"
                            data-dismiss-alert="consultations-success-alert"
                            class="text-[#c3c6d7] transition hover:text-white"
                        >
                            ✕
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        id="consultations-error-alert"
                        class="flex items-center justify-between p-4 mb-8 border-l-4 border-red-500 consultations-glass rounded-xl bg-red-500/5"
                    >
                        <p class="text-sm font-bold text-red-100">
                            {{ session('error') }}
                        </p>

                        <button
                            type="button"
                            data-dismiss-alert="consultations-error-alert"
                            class="text-[#c3c6d7] transition hover:text-white"
                        >
                            ✕
                        </button>
                    </div>
                @endif

                <section class="space-y-4">
                    <h2 class="px-1 text-xs font-black uppercase text-[#c3c6d7]">
                        الاستشارات النشطة
                    </h2>

                    @forelse ($consultations as $consultation)
                        <article class="overflow-hidden consultations-card consultations-glass rounded-2xl">
                            <div class="p-5 consultations-card-main md:p-8">
                                <div class="flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
                                    <div class="flex-1 min-w-0 space-y-4">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="rounded-full bg-blue-500/20 px-3 py-1 text-xs font-black text-[#b4c5ff]">
                                                {{ $consultation->consultation_number }}
                                            </span>

                                            @switch($consultation->status)
                                                @case('waiting_payment')
                                                    <span class="px-3 py-1 text-xs font-black text-pink-200 rounded-full bg-pink-500/20">بانتظار الدفع</span>
                                                    @break
                                                @case('pending')
                                                    <span class="px-3 py-1 text-xs font-black text-yellow-200 rounded-full bg-yellow-500/20">قيد المراجعة</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="px-3 py-1 text-xs font-black text-blue-200 rounded-full bg-blue-500/20">قيد التنفيذ</span>
                                                    @break
                                                @case('completed')
                                                    <span class="px-3 py-1 text-xs font-black text-green-200 rounded-full bg-green-500/20">مكتملة</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="px-3 py-1 text-xs font-black text-red-200 rounded-full bg-red-500/20">ملغاة</span>
                                                    @break
                                            @endswitch
                                        </div>

                                        <h3 class="text-xl font-black text-white break-words md:text-2xl">
                                            {{ $consultation->title }}
                                        </h3>

                                        <div class="grid grid-cols-2 gap-6 consultations-meta-grid md:grid-cols-4">
                                            <div class="flex items-start gap-2">
                                                <span class="text-blue-300">📐</span>
                                                <div class="min-w-0">
                                                    <span class="block text-[10px] text-[#c3c6d7]">القسم</span>
                                                    <span class="block mt-1 text-sm font-black text-white break-words">
                                                        {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-2">
                                                <span class="text-blue-300">👷</span>
                                                <div class="min-w-0">
                                                    <span class="block text-[10px] text-[#c3c6d7]">المهندس</span>

                                                    @if ($consultation->engineer)
                                                        <a
                                                            href="{{ route('engineers.show', $consultation->engineer) }}"
                                                            class="block mt-1 text-sm font-black text-white break-words transition hover:text-cyan-300"
                                                        >
                                                            {{ $consultation->engineer->name }}
                                                        </a>
                                                    @else
                                                        <span class="mt-1 block text-sm italic text-[#c3c6d7]/60">
                                                            لم يتم التعيين بعد
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-2">
                                                <span class="text-blue-300">💵</span>
                                                <div>
                                                    <span class="block text-[10px] text-[#c3c6d7]">المبلغ</span>
                                                    <span class="block mt-1 text-sm font-black text-white">
                                                        ${{ number_format((float) $consultation->final_price, 2) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-2">
                                                <span class="text-blue-300">📅</span>
                                                <div>
                                                    <span class="block text-[10px] text-[#c3c6d7]">التاريخ</span>
                                                    <span class="block mt-1 text-sm font-black text-white">
                                                        {{ $consultation->created_at?->format('Y-m-d') ?? '—' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col w-full gap-3 consultations-actions lg:w-64">
                                        <div class="rounded-xl border border-white/5 bg-[#131b2e] p-4">
                                            <div class="flex items-center justify-between text-xs text-[#c3c6d7]">
                                                <span>حالة الدفع</span>

                                                @switch($consultation->payment_status)
                                                    @case('unpaid')
                                                        <span class="font-black text-red-300">غير مدفوع</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="font-black text-pink-300">قيد الفحص</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="font-black text-green-300">تم الدفع</span>
                                                        @break
                                                @endswitch
                                            </div>

                                            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/5">
                                                <div
                                                    class="h-full rounded-full
                                                        {{ $consultation->payment_status === 'paid'
                                                            ? 'w-full bg-green-400'
                                                            : ($consultation->payment_status === 'pending'
                                                                ? 'w-[55%] bg-pink-400'
                                                                : 'w-[18%] bg-red-400') }}"
                                                ></div>
                                            </div>
                                        </div>

                                        @if ($consultation->payment_status === 'unpaid')
                                            <a
                                                href="{{ route('payments.create', $consultation) }}"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 hover:opacity-90"
                                            >
                                                💳 أكمل الدفع
                                            </a>
                                        @elseif ($consultation->payment_status === 'pending')
                                            <div class="w-full px-4 py-3 text-sm font-black text-center text-yellow-100 border rounded-xl border-yellow-500/20 bg-yellow-500/10">
                                                🧾 الإيصال تحت المراجعة
                                            </div>
                                        @endif

                                        @if ($consultation->payment_status === 'paid' && $consultation->invoice)
                                            <a
                                                href="{{ route('invoices.show', $consultation->invoice) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-blue-100 transition border rounded-xl border-blue-500/20 bg-blue-500/10 hover:bg-blue-500/20"
                                            >
                                                👁️ عرض فاتورة المكتب
                                            </a>

                                            <a
                                                href="{{ route('invoices.download', $consultation->invoice) }}"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-green-100 transition border rounded-xl border-green-500/20 bg-green-500/10 hover:bg-green-500/20"
                                            >
                                                📄 تحميل الفاتورة PDF
                                            </a>
                                        @elseif ($consultation->payment_status === 'paid' && ! $consultation->invoice)
                                            <div class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm font-black text-[#c3c6d7]">
                                                🧾 جاري تجهيز فاتورة المكتب
                                            </div>
                                        @endif

                                        @if ($consultation->engineer_file)
                                            <a
                                                href="{{ asset('storage/' . $consultation->engineer_file) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                                            >
                                                📥 تحميل الملف النهائي
                                            </a>
                                        @endif

                                        @if ($consultation->payment_status === 'paid' && $consultation->engineer_id)
                                            <a
                                                href="{{ route('consultations.messages.index', $consultation) }}"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                                            >
                                                💬 المحادثة مع المهندس
                                            </a>
                                        @endif

                                        @if ($consultation->engineer)
                                            <a
                                                href="{{ route('engineers.show', $consultation->engineer) }}"
                                                class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black transition border rounded-xl border-cyan-500/20 bg-cyan-500/10 text-cyan-200 hover:bg-cyan-500/20"
                                            >
                                                👤 صفحة المهندس
                                            </a>
                                        @endif

                                        @if (
                                            $consultation->status === 'completed'
                                            && $consultation->payment_status === 'paid'
                                        )
                                            @if ($consultation->review)
                                                <div class="p-4 border rounded-xl border-yellow-500/20 bg-yellow-500/10">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="font-black text-yellow-300">⭐ تقييمك</span>
                                                        <span class="text-xs text-yellow-100">
                                                            {{ $consultation->review->rating }}/5
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <a
                                                    href="{{ route('reviews.create', $consultation) }}"
                                                    class="flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition bg-yellow-600 rounded-xl hover:bg-yellow-500"
                                                >
                                                    ⭐ أضف تقييمك
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="consultations-card-footer flex flex-wrap items-center gap-5 border-t border-white/5 bg-white/5 px-8 py-4 text-xs text-[#c3c6d7]">
                                <div class="flex items-center gap-2">
                                    <span>◷</span>
                                    <span>
                                        آخر تحديث:
                                        {{ $consultation->updated_at?->diffForHumans() ?? '—' }}
                                    </span>
                                </div>

                                @if ($consultation->started_at)
                                    <div class="flex items-center gap-2">
                                        <span>🚀</span>
                                        <span>
                                            بدء العمل:
                                            {{ $consultation->started_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                @endif

                                @if ($consultation->expected_delivery_at)
                                    <div class="flex items-center gap-2">
                                        <span>📅</span>
                                        <span>
                                            التسليم المتوقع:
                                            {{ $consultation->expected_delivery_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                @endif

                                @if ($consultation->delivered_at)
                                    <div class="flex items-center gap-2 text-green-300">
                                        <span>✅</span>
                                        <span>
                                            تم التسليم:
                                            {{ $consultation->delivered_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                @elseif (
                                    $consultation->expected_delivery_at
                                    && now()->greaterThan($consultation->expected_delivery_at)
                                    && $consultation->status !== 'completed'
                                )
                                    <div class="flex items-center gap-2 text-red-300">
                                        <span>⚠️</span>
                                        <span>متأخرة عن موعد التسليم</span>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="p-12 text-center consultations-glass rounded-2xl">
                            <div class="mb-4 text-6xl">📭</div>

                            <h2 class="text-2xl font-black text-white">
                                لا توجد استشارات حتى الآن
                            </h2>

                            <p class="mt-3 text-[#c3c6d7]">
                                أرسل أول طلب استشارة وابدأ متابعة مشروعك.
                            </p>

                            <a
                                href="{{ route('consultations.create') }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 font-black text-white mt-7 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600"
                            >
                                إنشاء استشارة
                            </a>
                        </div>
                    @endforelse
                </section>

                @if (method_exists($consultations, 'hasPages') && $consultations->hasPages())
                    <div class="mt-8">
                        {{ $consultations->links() }}
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 mt-8 lg:grid-cols-3">
                    <div class="consultations-glass flex min-h-[260px] flex-col items-center justify-center rounded-2xl border-dashed p-6 text-center lg:col-span-2">
                        <div class="flex items-center justify-center w-16 h-16 mb-4 text-3xl rounded-full bg-white/5">
                            🏗️
                        </div>

                        <h4 class="text-xl font-black text-white">
                            هل تحتاج إلى استشارة أخرى؟
                        </h4>

                        <p class="mt-3 max-w-md text-sm leading-7 text-[#c3c6d7]">
                            يمكنك البدء بطلب استشارة جديدة في أي وقت.
                            مهندسونا جاهزون لتحويل رؤيتك إلى واقع.
                        </p>

                        <a
                            href="{{ route('consultations.create') }}"
                            class="mt-5 rounded-xl border border-[#b4c5ff]/30 px-8 py-2 font-black text-[#b4c5ff] transition hover:bg-[#b4c5ff]/5"
                        >
                            ابدأ الآن
                        </a>
                    </div>

                    <div class="p-6 consultations-glass rounded-2xl">
                        <h4 class="font-black text-[#b4c5ff]">
                            اختصارات مهمة
                        </h4>

                        <div class="mt-5 space-y-3">
                            <a href="{{ $supportUrl }}" class="block px-4 py-3 transition rounded-xl bg-white/5 hover:bg-white/10">
                                🎧 الدعم الفني
                            </a>

                            <a href="{{ $profileUrl }}" class="block px-4 py-3 transition rounded-xl bg-white/5 hover:bg-white/10">
                                ⚙️ إعدادات الحساب
                            </a>

                            <a href="{{ route('engineer.works.public') }}" class="block px-4 py-3 transition rounded-xl bg-white/5 hover:bg-white/10">
                                👷 مكتبة المهندسين
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- Mobile Bottom Navigation --}}
        <nav
            class="fixed inset-x-0 bottom-0 z-50 flex h-16 items-center justify-around border-t border-white/10 bg-[#2d3449]/90 px-3 backdrop-blur-lg md:hidden"
        >
            <a href="{{ $dashboardUrl }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]">
                <span>⌂</span>
                <span class="text-[9px]">الرئيسية</span>
            </a>

            <a href="{{ route('consultations.mine') }}" class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-600 text-white shadow-[0_0_10px_rgba(131,67,244,.5)]">
                🎟️
            </a>

            <a href="{{ $supportUrl }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]">
                <span>💬</span>
                <span class="text-[9px]">الدعم</span>
            </a>

            <a href="{{ $profileUrl }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]">
                <span>⚙️</span>
                <span class="text-[9px]">الإعدادات</span>
            </a>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document
                .querySelectorAll('[data-dismiss-alert]')
                .forEach(function (button) {
                    button.addEventListener('click', function () {
                        const target =
                            document.getElementById(
                                button.dataset.dismissAlert
                            );

                        target?.remove();
                    });
                });

            const openButton =
                document.getElementById(
                    'consultations-mobile-open'
                );

            const closeButton =
                document.getElementById(
                    'consultations-mobile-close'
                );

            const mobileMenu =
                document.getElementById(
                    'consultations-mobile-menu'
                );

            const backdrop =
                document.getElementById(
                    'consultations-mobile-backdrop'
                );

            function openMenu() {
                if (! mobileMenu || ! backdrop) {
                    return;
                }

                backdrop.classList.remove('hidden');

                requestAnimationFrame(function () {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');

                    mobileMenu.classList.remove(
                        'translate-x-full',
                        'opacity-0',
                        'pointer-events-none'
                    );

                    mobileMenu.classList.add(
                        'translate-x-0',
                        'opacity-100'
                    );
                });

                mobileMenu.setAttribute('aria-hidden', 'false');
                document.body.classList.add('overflow-hidden');
            }

            function closeMenu() {
                if (! mobileMenu || ! backdrop) {
                    return;
                }

                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');

                mobileMenu.classList.remove(
                    'translate-x-0',
                    'opacity-100'
                );

                mobileMenu.classList.add(
                    'translate-x-full',
                    'opacity-0',
                    'pointer-events-none'
                );

                mobileMenu.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');

                window.setTimeout(function () {
                    backdrop.classList.add('hidden');
                }, 300);
            }

            openButton?.addEventListener('click', openMenu);
            closeButton?.addEventListener('click', closeMenu);
            backdrop?.addEventListener('click', closeMenu);

            mobileMenu
                ?.querySelectorAll('a')
                .forEach(function (link) {
                    link.addEventListener('click', closeMenu);
                });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    closeMenu();
                }
            });
        });
    </script>
</x-app-layout>
