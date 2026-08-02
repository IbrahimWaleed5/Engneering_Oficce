<x-app-layout>
    @php
        $currentUser = auth()->user();

        $totalTickets = $tickets->total();

        $openTickets = \App\Models\SupportTicket::query()
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $closedTickets = \App\Models\SupportTicket::query()
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $urgentTickets = \App\Models\SupportTicket::query()
            ->where('priority', 'urgent')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $statusLabels = [
            'open' => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'محلولة',
            'closed' => 'مغلقة',
        ];

        $priorityLabels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'مرتفعة',
            'urgent' => 'عاجلة جدًا',
        ];
    @endphp

    <style>
        .admin-support-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background-color: #0b1326;
            background-image:
                radial-gradient(
                    circle at 50% 50%,
                    rgba(37, 99, 235, .05) 0%,
                    transparent 50%
                ),
                linear-gradient(
                    rgba(255, 255, 255, .02) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, .02) 1px,
                    transparent 1px
                );
            background-size:
                100% 100%,
                40px 40px,
                40px 40px;
            font-family:
                'Be Vietnam Pro',
                'Noto Sans Arabic',
                sans-serif;
        }

        .admin-support-glass {
            background: rgba(23, 31, 51, .6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(141, 144, 160, .1);
        }

        .admin-support-card {
            border: 1px solid rgba(180, 197, 255, .2);
            transition:
                border-color .3s ease,
                box-shadow .3s ease,
                transform .3s ease;
        }

        .admin-support-card:hover {
            border-color: rgba(37, 99, 235, .8);
            box-shadow: 0 0 20px rgba(37, 99, 235, .2);
            transform: translateY(-2px);
        }

        .admin-support-scroll::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        .admin-support-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .admin-support-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 999px;
        }

        [x-cloak] {
            display: none !important;
        }

        /*
         * إخفاء الـ Navbar/Header الأصلي القادم من x-app-layout
         * في هذه الصفحة فقط، مع إبقاء الشريط والقائمة المخصصين للصفحة.
         */
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


        @media (max-width: 1023px) {
            .admin-support-sidebar {
                display: none !important;
            }

            .admin-support-main {
                margin-right: 0 !important;
                padding-right: 1rem !important;
                padding-left: 1rem !important;
            }

            .admin-support-topbar {
                right: 0 !important;
            }
        }

        @media (max-width: 640px) {
            .admin-support-main {
                padding-top: 6rem !important;
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .admin-support-topbar {
                height: 4.5rem !important;
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .admin-support-heading {
                font-size: 1.6rem !important;
            }

            .admin-support-actions {
                width: 100% !important;
                flex-direction: column !important;
            }

            .admin-support-actions > * {
                width: 100% !important;
            }
        }
    </style>

    <div
        x-data="{ mobileMenuOpen: false }"
        class="admin-support-page"
        dir="rtl"
    >
        {{-- الشريط العلوي --}}
        <header
            class="admin-support-topbar fixed left-0 right-64 top-0 z-40 flex h-20 items-center justify-between border-b border-[#434655]/10 bg-[#0b1326]/85 px-6 backdrop-blur-md"
        >
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-white/5 lg:hidden"
                    title="فتح القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-12 h-12 overflow-hidden border rounded-xl border-blue-400/20 bg-blue-500/10">
                        @if ($currentUser->profile_photo)
                            <img
                                src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                alt="{{ $currentUser->name }}"
                                class="object-cover w-full h-full"
                            >
                        @else
                            <span class="font-black text-blue-300">
                                و
                            </span>
                        @endif
                    </div>

                    <div class="hidden sm:block">
                        <span class="block text-xl font-black tracking-tight text-[#b4c5ff]">
                            مكتب الوليد الهندسي
                        </span>

                        <span class="text-[10px] uppercase tracking-widest text-[#8d90a0]">
                            Engineering Office
                        </span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <form
                    method="GET"
                    action="{{ route('admin.support.index') }}"
                    class="relative hidden md:block"
                >
                    <input
                        type="hidden"
                        name="status"
                        value="{{ request('status') }}"
                    >

                    <input
                        name="q"
                        value="{{ request('q') }}"
                        type="search"
                        placeholder="بحث باسم الحساب أو الإيميل..."
                        class="w-72 rounded-full border border-[#434655]/20 bg-[#060e20]/60 py-2.5 pr-11 pl-4 text-sm text-white placeholder:text-[#8d90a0] focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >

                    <svg
                        class="absolute right-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#8d90a0]"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>
                </form>

                <a
                    href="{{ route('admin.support.settings') }}"
                    class="hidden items-center gap-2 rounded-xl bg-[#2563eb] px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:scale-[1.02] sm:flex"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    إعداد موظف الدعم
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center justify-center w-10 h-10 overflow-hidden font-black text-white border-2 border-blue-500 rounded-full bg-blue-500/10"
                    title="الملف الشخصي"
                >
                    @if ($currentUser->profile_photo)
                        <img
                            src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                            alt="{{ $currentUser->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        {{ mb_substr($currentUser->name, 0, 1) }}
                    @endif
                </a>
            </div>
        </header>

        {{-- القائمة الجانبية --}}
        <aside
            class="admin-support-sidebar admin-support-glass fixed right-0 top-20 z-50 flex h-[calc(100vh-5rem)] w-64 flex-col gap-2 border-l border-[#434655]/10 p-4"
        >
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 rounded-xl bg-blue-500/20 px-4 py-3 font-bold text-[#b4c5ff]"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                </svg>

                لوحة التحكم
            </a>

            <a
                href="{{ route('admin.support.index') }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                </svg>

                التذاكر
            </a>

            <a
                href="{{ route('admin.support.settings') }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="9" cy="8" r="3"/>
                    <circle cx="17" cy="9" r="2.5"/>
                    <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                </svg>

                موظف الدعم
            </a>

            <a
                href="{{ Route::has('users.index') ? route('users.index') : route('dashboard') }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="7" r="3"/>
                    <path d="M5 21a7 7 0 0 1 14 0"/>
                </svg>

                المستخدمون
            </a>

            <div class="mt-auto border-t border-[#434655]/10 pt-4">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    الإعدادات
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-3 transition rounded-xl text-red-300/80 hover:bg-red-500/10 hover:text-red-300"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>
                        </svg>

                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- قائمة الجوال --}}
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 z-[90] bg-black/70 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition
            class="fixed right-0 top-0 z-[100] flex h-screen w-72 flex-col bg-[#0b1326] p-5 lg:hidden"
        >
            <div class="flex items-center justify-between">
                <h2 class="font-black text-white">
                    إدارة الدعم الفني
                </h2>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/5"
                >
                    ✕
                </button>
            </div>

            <nav class="mt-8 space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl bg-white/5">لوحة التحكم</a>
                <a href="{{ route('admin.support.index') }}" class="block px-4 py-3 text-blue-300 rounded-xl bg-blue-500/20">التذاكر</a>
                <a href="{{ route('admin.support.settings') }}" class="block px-4 py-3 rounded-xl bg-white/5">موظف الدعم</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="min-h-screen px-6 pb-12 admin-support-main pt-28 lg:mr-64">
            <div class="mx-auto space-y-8 max-w-7xl">
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

                {{-- إحصائيات --}}
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="admin-support-card admin-support-glass flex items-center gap-4 rounded-[20px] p-5">
                        <div class="flex items-center justify-center w-12 h-12 text-blue-400 rounded-xl bg-blue-500/20">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M4 5h16v14H4zM7 9h10M7 13h6"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">إجمالي التذاكر</p>
                            <h3 class="text-3xl font-black">{{ $totalTickets }}</h3>
                        </div>
                    </article>

                    <article class="admin-support-card admin-support-glass flex items-center gap-4 rounded-[20px] p-5">
                        <div class="flex items-center justify-center w-12 h-12 text-pink-300 rounded-xl bg-pink-500/20">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">تذاكر مفتوحة</p>
                            <h3 class="text-3xl font-black">{{ $openTickets }}</h3>
                        </div>
                    </article>

                    <article class="admin-support-card admin-support-glass flex items-center gap-4 rounded-[20px] p-5">
                        <div class="flex items-center justify-center w-12 h-12 text-purple-300 rounded-xl bg-purple-500/20">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">تذاكر مغلقة</p>
                            <h3 class="text-3xl font-black">{{ $closedTickets }}</h3>
                        </div>
                    </article>

                    <article class="admin-support-card admin-support-glass flex items-center gap-4 rounded-[20px] bg-red-500/[0.03] p-5">
                        <div class="flex items-center justify-center w-12 h-12 text-red-300 rounded-xl bg-red-500/20">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M12 3 2 21h20L12 3Z"/>
                                <path d="M12 9v5M12 18h.01"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">عاجل جدًا</p>
                            <h3 class="text-3xl font-black text-red-300">{{ $urgentTickets }}</h3>
                        </div>
                    </article>
                </section>

                {{-- البحث والفلاتر --}}
                <section class="p-5 admin-support-glass rounded-3xl">
                    <form
                        method="GET"
                        action="{{ route('admin.support.index') }}"
                        class="grid gap-4 lg:grid-cols-[1fr_240px_auto_auto]"
                    >
                        <div class="relative">
                            <input
                                name="q"
                                value="{{ request('q') }}"
                                type="search"
                                placeholder="ابحث باسم الحساب أو البريد الإلكتروني أو رقم التذكرة..."
                                class="w-full rounded-xl border border-[#434655]/20 bg-[#060e20]/60 py-3 pr-11 pl-4 text-white placeholder:text-[#8d90a0] focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >

                            <svg
                                class="absolute right-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#8d90a0]"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3.5-3.5"/>
                            </svg>
                        </div>

                        <select
                            name="status"
                            class="rounded-xl border border-[#434655]/20 bg-[#060e20]/60 px-4 py-3 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">كل الحالات</option>

                            @foreach ($statusLabels as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(request('status') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <button
                            type="submit"
                            class="rounded-xl bg-[#2563eb] px-6 py-3 font-bold text-white transition hover:bg-blue-500"
                        >
                            تطبيق
                        </button>

                        <a
                            href="{{ route('admin.support.index') }}"
                            class="rounded-xl border border-[#434655]/30 bg-[#222a3d] px-6 py-3 text-center font-bold text-[#c3c6d7] transition hover:bg-[#31394d]"
                        >
                            مسح
                        </a>
                    </form>
                </section>

                {{-- الجدول --}}
                <section class="space-y-4">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="flex items-center gap-2 text-2xl font-bold admin-support-heading">
                            <svg class="h-6 w-6 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>
                            </svg>

                            طلبات الدعم الفني الأخيرة
                        </h2>

                        <div class="flex gap-2 admin-support-actions">
                            <a
                                href="{{ route('admin.support.settings') }}"
                                class="rounded-xl bg-[#2563eb] px-5 py-3 text-center font-bold text-white transition hover:bg-blue-500"
                            >
                                إعداد موظف الدعم
                            </a>
                        </div>
                    </div>

                    <div class="admin-support-glass overflow-hidden rounded-[24px] shadow-2xl">
                        <div class="overflow-x-auto admin-support-scroll">
                            <table class="w-full min-w-[1100px] border-collapse text-right">
                                <thead class="border-b border-[#434655]/10 bg-[#2d3449]/40">
                                    <tr>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">رقم التذكرة</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">الموضوع</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">المستخدم</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">موظف الدعم</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">الأولوية</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">الحالة</th>
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-[#8d90a0]">الإجراء</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-[#434655]/10">
                                    @forelse ($tickets as $ticket)
                                        <tr class="transition hover:bg-white/[0.03]">
                                            <td class="px-6 py-5 font-bold text-[#b4c5ff]">
                                                {{ $ticket->ticket_number }}
                                            </td>

                                            <td class="px-6 py-5">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-white">
                                                        {{ $ticket->subject }}
                                                    </span>

                                                    <span class="mt-1 text-[10px] text-[#8d90a0]">
                                                        {{ $ticket->updated_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="px-6 py-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center justify-center text-xs font-bold text-blue-300 rounded-full h-9 w-9 bg-blue-500/10">
                                                        {{ mb_substr($ticket->user?->name ?? 'م', 0, 1) }}
                                                    </div>

                                                    <div class="min-w-0">
                                                        <p class="text-sm font-bold text-white truncate">
                                                            {{ $ticket->user?->name ?? 'مستخدم غير معروف' }}
                                                        </p>

                                                        <p class="truncate text-[10px] text-[#8d90a0]">
                                                            {{ $ticket->user?->email ?? '—' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-6 py-5">
                                                @if ($ticket->assignedEmployee)
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex items-center justify-center text-xs font-bold text-purple-300 rounded-full h-9 w-9 bg-purple-500/10">
                                                            {{ mb_substr($ticket->assignedEmployee->name, 0, 1) }}
                                                        </div>

                                                        <div>
                                                            <p class="text-sm font-bold text-white">
                                                                {{ $ticket->assignedEmployee->name }}
                                                            </p>

                                                            <p class="text-[10px] text-[#8d90a0]">
                                                                {{ $ticket->assignedEmployee->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="rounded-full bg-white/5 px-3 py-1 text-xs text-[#8d90a0]">
                                                        غير معيّن
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-5">
                                                <span
                                                    class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold
                                                    {{
                                                        $ticket->priority === 'urgent'
                                                            ? 'border-red-500/20 bg-red-500/10 text-red-300'
                                                            : ($ticket->priority === 'high'
                                                                ? 'border-orange-500/20 bg-orange-500/10 text-orange-300'
                                                                : ($ticket->priority === 'medium'
                                                                    ? 'border-blue-500/20 bg-blue-500/10 text-blue-300'
                                                                    : 'border-slate-500/20 bg-slate-500/10 text-slate-300'))
                                                    }}"
                                                >
                                                    {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-5">
                                                <span
                                                    class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold
                                                    {{
                                                        $ticket->status === 'open'
                                                            ? 'border-blue-500/20 bg-blue-500/10 text-blue-300'
                                                            : ($ticket->status === 'in_progress'
                                                                ? 'border-amber-500/20 bg-amber-500/10 text-amber-300'
                                                                : ($ticket->status === 'resolved'
                                                                    ? 'border-green-500/20 bg-green-500/10 text-green-300'
                                                                    : 'border-slate-500/20 bg-slate-500/10 text-slate-300'))
                                                    }}"
                                                >
                                                    {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-5">
                                                <a
                                                    href="{{ route('support.show', $ticket) }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-full text-[#b4c5ff] transition hover:bg-[#b4c5ff]/10"
                                                    title="فتح التذكرة"
                                                >
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="M14 3h7v7M21 3l-9 9M5 5h5M5 5v14h14v-5"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-20 text-center">
                                                <div class="flex items-center justify-center w-20 h-20 mx-auto text-blue-300 rounded-full bg-blue-500/10">
                                                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                                                    </svg>
                                                </div>

                                                <h3 class="mt-5 text-xl font-bold text-white">
                                                    لا توجد تذاكر حاليًا
                                                </h3>

                                                <p class="mt-2 text-[#8d90a0]">
                                                    لم يتم العثور على نتائج مطابقة للبحث أو الفلتر.
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($tickets->hasPages())
                            <div class="border-t border-[#434655]/10 bg-[#060e20]/20 px-6 py-4">
                                {{ $tickets->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
