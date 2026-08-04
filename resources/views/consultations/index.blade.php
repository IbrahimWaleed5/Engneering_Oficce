<x-app-layout>
    @php
        $currentUser = auth()->user();

        $dashboardRoute = Route::has('dashboard')
            ? route('dashboard')
            : url('/dashboard');

        $consultationsRoute = Route::has('consultations.index')
            ? route('consultations.index')
            : url('/consultations');

        $officesRoute = Route::has('admin.offices.index')
            ? route('admin.offices.index')
            : (
                Route::has('engineering-offices.index')
                    ? route('engineering-offices.index')
                    : url('/engineering-offices')
            );

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $notificationsRoute = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardRoute;

        $totalConsultations = $consultations->total();

        $unpaidCount = $consultations
            ->getCollection()
            ->where('payment_status', 'unpaid')
            ->count();

        $inProgressCount = $consultations
            ->getCollection()
            ->where('status', 'in_progress')
            ->count();

        $completedCount = $consultations
            ->getCollection()
            ->where('status', 'completed')
            ->count();

        $cancelledCount = $consultations
            ->getCollection()
            ->where('status', 'cancelled')
            ->count();

        $totalAmount = $consultations
            ->getCollection()
            ->sum('final_price');
    @endphp

    <style>
        .consultations-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .consultations-glass {
            background: rgba(23, 31, 51, .4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .consultations-glass-hover {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .consultations-glass-hover:hover {
            transform: scale(1.02);
            border-color: rgba(180, 197, 255, .25);
            box-shadow: 0 0 20px rgba(37, 99, 235, .15);
        }

        .consultations-table-row {
            transition: background-color .2s ease;
        }

        .consultations-table-row:hover {
            background: rgba(45, 52, 73, .4);
        }

        .consultations-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .consultations-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .consultations-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 4px;
        }

        .consultations-scroll::-webkit-scrollbar-thumb:hover {
            background: #434655;
        }

        [x-cloak] {
            display: none !important;
        }

        body.consultations-menu-open {
            overflow: hidden;
        }

        .consultations-mobile-drawer {
            width: min(88vw, 390px);
        }

        .consultations-nav-link {
            transition: transform .2s ease, background-color .2s ease, color .2s ease;
        }

        .consultations-nav-link:hover {
            transform: translateX(-2px);
        }

        @media (min-width: 1024px) {
            .consultations-sidebar {
                width: 18rem !important;
            }

            .consultations-main {
                margin-right: 18rem !important;
            }

            .consultations-topbar {
                right: 18rem !important;
            }
        }

        @media (max-width: 1023px) {
            .consultations-sidebar {
                display: none !important;
            }

            .consultations-main {
                margin-right: 0 !important;
            }

            .consultations-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div
        class="consultations-page"
        dir="rtl"
        x-data="{ mobileMenuOpen: false }"
        x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('consultations-menu-open', value))"
        @keydown.escape.window="mobileMenuOpen = false"
    >
        {{-- شريط الجوال --}}
        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    aria-label="فتح القائمة"
                    class="flex items-center justify-center w-14 h-14 text-white transition rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] shadow-lg active:scale-95"
                >
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="min-w-0 text-center">
                    <p class="truncate text-lg font-black text-[#b4c5ff]">صرح الهندسة</p>
                    <p class="truncate text-xs text-[#c3c6d7]">إدارة الاستشارات</p>
                </div>

                <a
                    href="{{ $notificationsRoute }}"
                    class="flex items-center justify-center w-12 h-12 text-[#c3c6d7] border rounded-2xl border-white/10 bg-white/5"
                    aria-label="الإشعارات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m10-2H5l1.5-2V9a5.5 5.5 0 0 1 11 0v4L19 15ZM10 20h4"/>
                    </svg>
                </a>
            </div>
        </header>

        {{-- خلفية قائمة الجوال --}}
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            @click="mobileMenuOpen = false"
            class="fixed inset-0 z-[80] bg-black/75 backdrop-blur-sm lg:hidden"
        ></div>

        {{-- قائمة الجوال الفخمة --}}
        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="consultations-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden"
        >
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <div>
                    <h2 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h2>
                    <p class="mt-1 text-sm text-[#c3c6d7]">قائمة إدارة النظام</p>
                </div>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-12 h-12 text-white border rounded-2xl border-white/10 bg-white/5"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-5 space-y-3 overflow-y-auto consultations-scroll">
                <a href="{{ $dashboardRoute }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">⌂</span>
                    <span>لوحة التحكم</span>
                </a>

                <a href="{{ $consultationsRoute }}" @click="mobileMenuOpen = false" class="flex items-center gap-4 rounded-2xl border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 px-5 py-4 font-black text-[#dbe1ff] shadow-lg shadow-blue-950/30">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">📄</span>
                    <span>الاستشارات</span>
                </a>

                @if ($currentUser->role === 'admin' && Route::has('admin.offices.index'))
                    <a href="{{ route('admin.offices.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">🏢</span>
                        <span>المكاتب الهندسية</span>
                    </a>
                @endif

                @if ($currentUser->role === 'admin' && Route::has('admin.office-applications.index'))
                    <a href="{{ route('admin.office-applications.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">📋</span>
                        <span>طلبات إنشاء المكاتب</span>
                    </a>
                @endif

                @if ($currentUser->role === 'admin' && Route::has('admin.office-subscriptions.index'))
                    <a href="{{ route('admin.office-subscriptions.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">💳</span>
                        <span>اشتراكات المكاتب</span>
                    </a>
                @endif

                @if (Route::has('users.index'))
                    <a href="{{ route('users.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">👥</span>
                        <span>المستخدمون</span>
                    </a>
                @endif

                @if (Route::has('payments.index'))
                    <a href="{{ route('payments.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">💰</span>
                        <span>الدفعات</span>
                    </a>
                @endif

                @if (Route::has('conversations.index'))
                    <a href="{{ route('conversations.index') }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">💬</span>
                        <span>المحادثات</span>
                    </a>
                @endif

                <a href="{{ $profileRoute }}" @click="mobileMenuOpen = false" class="consultations-nav-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">⚙</span>
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="p-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#c3c6d7] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-4 font-black text-red-100 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- القائمة الجانبية --}}
        <aside class="consultations-sidebar fixed right-0 top-0 z-50 flex h-screen w-72 flex-col border-l border-[#434655]/10 bg-[#131b2e]/90 p-4 shadow-xl backdrop-blur-xl">
            <div class="px-4 mb-10">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">
                    صرح الهندسة
                </h1>

                <p class="text-sm text-[#c3c6d7] opacity-60">
                    نظام الإدارة الفاخر
                </p>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ $dashboardRoute }}"
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
                    href="{{ $consultationsRoute }}"
                    class="flex items-center gap-3 rounded-xl bg-[#2563eb]/20 px-4 py-3 font-bold text-[#b4c5ff] shadow-[0_0_15px_rgba(37,99,235,.1)]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>الاستشارات</span>
                </a>

                @if ($currentUser->role === 'admin' && Route::has('admin.offices.index'))
                    <a
                        href="{{ route('admin.offices.index') }}"
                        class="consultations-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white"
                    >
                        <span class="flex items-center justify-center w-5 h-5">🏢</span>
                        <span>المكاتب الهندسية</span>
                    </a>
                @endif

                @if ($currentUser->role === 'admin' && Route::has('admin.office-applications.index'))
                    <a
                        href="{{ route('admin.office-applications.index') }}"
                        class="consultations-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white"
                    >
                        <span class="flex items-center justify-center w-5 h-5">📋</span>
                        <span>طلبات إنشاء المكاتب</span>
                    </a>
                @endif

                @if ($currentUser->role === 'admin' && Route::has('admin.office-subscriptions.index'))
                    <a
                        href="{{ route('admin.office-subscriptions.index') }}"
                        class="consultations-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white"
                    >
                        <span class="flex items-center justify-center w-5 h-5">💳</span>
                        <span>اشتراكات المكاتب</span>
                    </a>
                @endif


                @if (Route::has('users.index'))
                    <a
                        href="{{ route('users.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="9" cy="8" r="3"/>
                            <circle cx="17" cy="9" r="2.5"/>
                            <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                        </svg>

                        <span>المستخدمون</span>
                    </a>
                @endif

                @if (Route::has('payments.index'))
                    <a
                        href="{{ route('payments.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path d="M3 10h18M7 15h4"/>
                        </svg>

                        <span>الدفعات</span>
                    </a>
                @endif

                @if (Route::has('conversations.index'))
                    <a
                        href="{{ route('conversations.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:scale-[1.02] hover:bg-white/5 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>

                        <span>المحادثات</span>
                    </a>
                @endif

                <a
                    href="{{ $profileRoute }}"
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
                    href="{{ $profileRoute }}"
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
        <header class="consultations-topbar fixed hidden lg:flex top-0 left-0 right-72 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#060e20]/60 px-6 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-black text-[#dae2fd]">
                    سجل الاستشارات
                </h2>

                <div class="h-6 w-px bg-[#434655]/30"></div>

                <div class="relative hidden md:block">
                    <svg class="absolute w-5 h-5 -translate-y-1/2 left-3 top-1/2 text-[#8d90a0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        id="consultationsLiveSearch"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="بحث عن استشارة..."
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

                @if (Route::has('conversations.index'))
                    <a
                        href="{{ route('conversations.index') }}"
                        class="flex items-center justify-center p-2 transition rounded-full hover:bg-white/5"
                        title="المحادثات"
                    >
                        <svg class="w-5 h-5 text-[#dae2fd]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>
                    </a>
                @endif

                <div class="flex items-center gap-3 pr-4 border-r border-[#434655]/20">
                    <div class="text-left">
                        <p class="text-xs font-bold leading-tight text-[#dae2fd]">
                            {{ $currentUser->name }}
                        </p>

                        <p class="text-[11px] text-[#c3c6d7] opacity-60">
                            {{ $currentUser->role === 'admin' ? 'مشرف النظام' : 'مستخدم النظام' }}
                        </p>
                    </div>

                    <a
                        href="{{ $profileRoute }}"
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

        <main class="min-h-screen px-4 pb-12 pt-28 consultations-main sm:px-6 lg:mr-72 lg:px-8 lg:pt-24">
            <div class="mx-auto max-w-[1700px] space-y-8">
                {{-- الرسائل --}}
                @if (session('success'))
                    <div class="p-4 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- رأس الصفحة --}}
                <section class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <h1 class="text-3xl font-black text-[#dae2fd]">
                            إدارة الاستشارات
                        </h1>

                        <p class="mt-2 text-sm text-[#c3c6d7]">
                            متابعة جميع الطلبات، الدفع، المهندسين وحالة التنفيذ.
                        </p>
                    </div>

                    @if (Route::has('consultations.create'))
                        <a
                            href="{{ route('consultations.create') }}"
                            class="flex items-center gap-2 rounded-xl bg-[#2563eb] px-5 py-3 font-bold text-white shadow-lg shadow-blue-500/20 transition hover:brightness-110 active:scale-95"
                        >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        استشارة جديدة
                    </a>
                    @endif
                </section>

                {{-- الإحصائيات --}}
                <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <article class="flex items-center justify-between p-5 consultations-glass consultations-glass-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                جميع الاستشارات
                            </p>

                            <h3 class="text-3xl font-black text-[#dae2fd]">
                                {{ $totalConsultations }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <rect x="5" y="3" width="14" height="18" rx="2"/>
                                <path d="M8 8h8M8 12h8M8 16h5"/>
                            </svg>
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 consultations-glass consultations-glass-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                بانتظار الدفع
                            </p>

                            <h3 class="text-3xl font-black text-[#d2bbff]">
                                {{ $unpaidCount }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#d2bbff]/10 text-[#d2bbff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="M3 10h18M7 15h4"/>
                            </svg>
                        </div>
                    </article>

                    <article class="consultations-glass consultations-glass-hover flex items-center justify-between rounded-2xl border border-[#b4c5ff]/20 bg-[#b4c5ff]/5 p-5 shadow-[0_0_10px_rgba(180,197,255,.1)]">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#b4c5ff]">
                                قيد التنفيذ
                            </p>

                            <h3 class="text-3xl font-black text-[#b4c5ff]">
                                {{ $inProgressCount }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/20 text-[#b4c5ff]">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                            </svg>
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 consultations-glass consultations-glass-hover rounded-2xl">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-[#8d90a0]">
                                مكتملة
                            </p>

                            <h3 class="text-3xl font-black text-green-300">
                                {{ $completedCount }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-green-300 rounded-xl bg-green-500/10">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </div>
                    </article>

                    <article class="consultations-glass consultations-glass-hover relative flex items-center justify-between overflow-hidden rounded-2xl border border-[#2563eb]/20 bg-[#2563eb]/10 p-5">
                        <div class="relative z-10">
                            <p class="mb-1 text-xs font-bold uppercase text-[#eeefff]">
                                إجمالي قيمة الصفحة
                            </p>

                            <h3 class="text-2xl font-black text-[#eeefff]">
                                {{ number_format($totalAmount, 2) }}
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

                {{-- البحث والفلاتر --}}
                <section
                    id="consultationFilterPanel"
                    class="{{ request()->hasAny(['search', 'status', 'engineer_id', 'office_id', 'date_from', 'date_to']) ? '' : 'hidden' }} p-6 consultations-glass rounded-3xl"
                >
                    <form
                        method="GET"
                        action="{{ route('consultations.index') }}"
                        class="grid gap-5 md:grid-cols-2 xl:grid-cols-4"
                    >
                        <div class="md:col-span-2">
                            <label for="search" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                اسم العميل أو رقم الاستشارة
                            </label>

                            <input
                                id="search"
                                name="search"
                                type="text"
                                value="{{ request('search') }}"
                                placeholder="مثال: CONS-123 أو اسم العميل"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white placeholder:text-[#8d90a0] focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label for="status" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                حالة الاستشارة
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع الحالات</option>
                                <option value="waiting_payment" @selected(request('status') === 'waiting_payment')>بانتظار الدفع</option>
                                <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                                <option value="in_progress" @selected(request('status') === 'in_progress')>قيد التنفيذ</option>
                                <option value="completed" @selected(request('status') === 'completed')>مكتملة</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
                            </select>
                        </div>

                        <div>
                            <label for="engineer_id" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                المهندس
                            </label>

                            <select
                                id="engineer_id"
                                name="engineer_id"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع المهندسين</option>

                                @foreach ($engineers as $engineer)
                                    <option
                                        value="{{ $engineer->id }}"
                                        @selected((string) request('engineer_id') === (string) $engineer->id)
                                    >
                                        {{ $engineer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="office_id" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                المكتب الهندسي
                            </label>

                            <select
                                id="office_id"
                                name="office_id"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع المكاتب</option>

                                @foreach ($offices as $office)
                                    <option
                                        value="{{ $office->id }}"
                                        @selected((string) request('office_id') === (string) $office->id)
                                    >
                                        {{ $office->name }}
                                        —
                                        {{ match ($office->status) {
                                            'active' => 'فعال',
                                            'suspended' => 'موقوف',
                                            'closed' => 'مغلق',
                                            default => 'غير فعال',
                                        } }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_from" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                من تاريخ
                            </label>

                            <input
                                id="date_from"
                                name="date_from"
                                type="date"
                                value="{{ request('date_from') }}"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label for="date_to" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                إلى تاريخ
                            </label>

                            <input
                                id="date_to"
                                name="date_to"
                                type="date"
                                value="{{ request('date_to') }}"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div class="flex flex-wrap items-end gap-3 md:col-span-2">
                            <button
                                type="submit"
                                class="rounded-xl bg-[#2563eb] px-5 py-3 font-bold text-white transition hover:brightness-110"
                            >
                                تطبيق الفلاتر
                            </button>

                            <a
                                href="{{ route('consultations.index') }}"
                                class="rounded-xl border border-[#434655] px-5 py-3 font-bold text-[#c3c6d7] transition hover:bg-[#2d3449]"
                            >
                                مسح الفلاتر
                            </a>
                        </div>
                    </form>
                </section>

                {{-- الجدول --}}
                <section class="relative overflow-hidden shadow-2xl consultations-glass rounded-3xl">
                    <div class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-[#434655]/10">
                        <div>
                            <h4 class="text-2xl font-bold text-[#dae2fd]">
                                سجل الاستشارات
                            </h4>

                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                جميع طلبات الاستشارة وحالاتها الحالية
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button
                                id="toggleConsultationFilters"
                                type="button"
                                aria-controls="consultationFilterPanel"
                                aria-expanded="{{ request()->hasAny(['search', 'status', 'engineer_id', 'office_id', 'date_from', 'date_to']) ? 'true' : 'false' }}"
                                class="flex items-center gap-2 rounded-xl border border-[#434655]/20 bg-[#222a3d] px-4 py-2 text-[#dae2fd] transition hover:bg-[#31394d]"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/>
                                </svg>

                                <span class="text-xs font-bold">تصفية</span>
                            </button>

                            @if (Route::has('consultations.create'))
                                <a
                                    href="{{ route('consultations.create') }}"
                                    class="flex items-center gap-2 rounded-xl bg-[#2563eb] px-4 py-2 text-white shadow-lg transition hover:brightness-110 active:scale-95"
                                >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14M5 12h14"/>
                                </svg>

                                <span class="text-xs font-bold">إضافة استشارة</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto consultations-scroll">
                        <table class="w-full text-right">
                            <thead>
                                <tr class="bg-[#131b2e]/50">
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">رقم الاستشارة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">العميل</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">عنوان الطلب</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">المهندس</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">المكتب الهندسي</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">السعر</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">الدفع</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">الحالة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-[#8d90a0]">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#434655]/10">
                                @forelse ($consultations as $consultation)
                                    @php
                                        $canOpenChat =
                                            (int) auth()->id() === (int) $consultation->customer_id
                                            || (int) auth()->id() === (int) $consultation->engineer_id
                                            || in_array(
                                                auth()->user()->role,
                                                ['admin', 'employee'],
                                                true
                                            );

                                        $searchText = strtolower(
                                            ($consultation->consultation_number ?? '') . ' ' .
                                            ($consultation->title ?? '') . ' ' .
                                            ($consultation->customer?->name ?? '') . ' ' .
                                            ($consultation->customer?->email ?? '') . ' ' .
                                            ($consultation->engineer?->name ?? '') . ' ' .
                                            ($consultation->assignedOffice?->name ?? '')
                                        );
                                    @endphp

                                    <tr
                                        data-consultation-row
                                        data-search="{{ $searchText }}"
                                        class="consultations-table-row"
                                    >
                                        <td class="px-6 py-5">
                                            <span class="rounded-lg bg-[#b4c5ff]/10 px-3 py-1 text-xs font-bold text-[#b4c5ff]">
                                                {{ $consultation->consultation_number }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-tr from-[#b4c5ff] to-[#d2bbff] text-[10px] font-bold text-[#002a78]">
                                                    {{ mb_substr($consultation->customer?->name ?? 'ع', 0, 1) }}
                                                </div>

                                                <div>
                                                    <p class="text-xs font-bold text-[#dae2fd]">
                                                        {{ $consultation->customer?->name ?? 'غير معروف' }}
                                                    </p>

                                                    <p class="text-[10px] text-[#8d90a0]">
                                                        {{ $consultation->customer?->email ?? '—' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <p class="max-w-[240px] truncate text-sm font-bold text-[#dae2fd]">
                                                {{ $consultation->title }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-[#8d90a0]">
                                                {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-5 text-sm text-[#c3c6d7]">
                                            {{ $consultation->engineer?->name ?? 'غير معيّن' }}
                                        </td>

                                        <td class="px-6 py-5">
                                            @if ($consultation->assignedOffice)
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center justify-center w-8 h-8 text-purple-300 rounded-lg shrink-0 bg-purple-500/10">
                                                        <svg
                                                            class="w-4 h-4"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="1.9"
                                                        >
                                                            <path d="M3 21h18"/>
                                                            <path d="M5 21V7l7-4 7 4v14"/>
                                                            <path d="M9 21v-5h6v5"/>
                                                            <path d="M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
                                                        </svg>
                                                    </div>

                                                    <div class="min-w-0">
                                                        <p
                                                            class="max-w-[170px] truncate text-xs font-bold text-[#dae2fd]"
                                                            title="{{ $consultation->assignedOffice->name }}"
                                                        >
                                                            {{ $consultation->assignedOffice->name }}
                                                        </p>

                                                        <p class="mt-1 text-[10px]">
                                                            @if ($consultation->assignedOffice->status === 'active')
                                                                <span class="text-green-300">مكتب فعال</span>
                                                            @elseif ($consultation->assignedOffice->status === 'suspended')
                                                                <span class="text-amber-300">مكتب موقوف</span>
                                                            @elseif ($consultation->assignedOffice->status === 'closed')
                                                                <span class="text-red-300">مكتب مغلق</span>
                                                            @else
                                                                <span class="text-[#8d90a0]">غير فعال</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-[#434655]/20 bg-[#2d3449]/40 px-3 py-1 text-[10px] font-bold text-[#8d90a0]">
                                                    غير محولة لمكتب
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="text-sm font-bold text-[#dae2fd]">
                                                {{ number_format($consultation->final_price, 2) }}
                                            </span>

                                            <span class="mr-1 text-[11px] text-[#8d90a0]">₪</span>
                                        </td>

                                        <td class="px-6 py-5">
                                            @if ($consultation->payment_status === 'paid')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-[11px] font-bold text-green-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                                    مدفوع
                                                </span>
                                            @elseif ($consultation->payment_status === 'pending')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[11px] font-bold text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    قيد الفحص
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-[11px] font-bold text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                    غير مدفوع
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            @if ($consultation->status === 'waiting_payment')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-500/10 px-3 py-1 text-[11px] font-bold text-orange-300">
                                                    بانتظار الدفع
                                                </span>
                                            @elseif ($consultation->status === 'pending')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-500/10 px-3 py-1 text-[11px] font-bold text-yellow-300">
                                                    قيد المراجعة
                                                </span>
                                            @elseif ($consultation->status === 'in_progress')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/10 px-3 py-1 text-[11px] font-bold text-blue-300">
                                                    قيد التنفيذ
                                                </span>
                                            @elseif ($consultation->status === 'completed')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-500/10 px-3 py-1 text-[11px] font-bold text-green-300">
                                                    مكتملة
                                                </span>
                                            @elseif ($consultation->status === 'cancelled')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/10 px-3 py-1 text-[11px] font-bold text-red-300">
                                                    ملغاة
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex flex-wrap gap-2">
                                                @if (
                                                    in_array($currentUser->role, ['admin', 'employee'], true)
                                                    && Route::has('consultations.assign.form')
                                                    && ! in_array($consultation->status, ['completed', 'cancelled'], true)
                                                )
                                                    <a
                                                        href="{{ route('consultations.assign.form', $consultation) }}"
                                                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#2d3449] text-[#b4c5ff] transition hover:bg-[#2563eb] hover:text-white"
                                                        title="{{ $consultation->engineer ? 'تغيير المهندس' : 'تعيين مهندس' }}"
                                                    >
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <circle cx="9" cy="8" r="3"/>
                                                        <path d="M3 20a6 6 0 0 1 12 0M18 8v6M15 11h6"/>
                                                    </svg>
                                                </a>
                                                @endif

@if ($currentUser->role === 'admin' && Route::has('admin.consultation-office.form'))
    <a
        href="{{ route(
            'admin.consultation-office.form',
            $consultation
        ) }}"
        class="flex items-center justify-center text-purple-300 transition rounded-lg w-9 h-9 bg-purple-500/10 hover:bg-purple-600 hover:text-white"
        title="{{ $consultation->assigned_office_id
            ? 'تغيير المكتب الهندسي'
            : 'تحويل إلى مكتب هندسي' }}"
    >
        <svg
            class="w-4 h-4"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.9"
        >
            <path d="M3 21h18"/>
            <path d="M5 21V7l7-4 7 4v14"/>
            <path d="M9 21v-5h6v5"/>
            <path d="M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
        </svg>
    </a>

    @if (
        Route::has('admin.consultation-office.unassign')
        && $consultation->assigned_office_id
        && ! in_array(
            $consultation->status,
            ['completed', 'cancelled'],
            true
        )
    )
        <form
            method="POST"
            action="{{ route(
                'admin.consultation-office.unassign',
                $consultation
            ) }}"
            class="inline-flex"
            onsubmit="return confirm('هل أنت متأكد من إلغاء تحويل هذه الاستشارة من المكتب الهندسي؟ سيتم أيضًا إزالة المهندس المعيّن من المكتب.')"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="flex items-center justify-center text-red-300 transition rounded-lg w-9 h-9 bg-red-500/10 hover:bg-red-600 hover:text-white"
                title="إلغاء تحويل المكتب الهندسي"
                aria-label="إلغاء تحويل المكتب الهندسي"
            >
                <svg
                    class="w-4 h-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18 6 6 18M6 6l12 12"
                    />
                </svg>
            </button>
        </form>
    @endif
@endif
                                                @if ($consultation->customer_file)
                                                    <a
                                                        href="{{ asset('storage/' . $consultation->customer_file) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center justify-center transition rounded-lg w-9 h-9 text-cyan-300 bg-cyan-500/10 hover:bg-cyan-500/20"
                                                        title="ملف العميل"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M6 2h9l5 5v15H6z"/>
                                                            <path d="M14 2v6h6M9 13h6M9 17h6"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    $canOpenChat
                                                    && $consultation->payment_status === 'paid'
                                                    && $consultation->engineer_id
                                                )
                                                    <a
                                                        href="{{ route('consultations.messages.index', $consultation) }}"
                                                        class="flex items-center justify-center text-green-300 transition rounded-lg w-9 h-9 bg-green-500/10 hover:bg-green-500/20"
                                                        title="المحادثة"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-16 text-center text-[#c3c6d7]">
                                            لا توجد استشارات حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($consultations->hasPages())
                        <div class="p-6 border-t border-[#434655]/10">
                            {{ $consultations->withQueryString()->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const liveSearch =
                document.getElementById(
                    'consultationsLiveSearch'
                );

            const rows =
                Array.from(
                    document.querySelectorAll(
                        '[data-consultation-row]'
                    )
                );

            const formSearch =
                document.getElementById('search');

            const applyLiveSearch = () => {
                const query =
                    (liveSearch?.value || '')
                        .trim()
                        .toLowerCase();

                rows.forEach((row) => {
                    const searchText =
                        (
                            row.dataset.search || ''
                        ).toLowerCase();

                    row.classList.toggle(
                        'hidden',
                        query !== ''
                        && !searchText.includes(query)
                    );
                });
            };

            liveSearch?.addEventListener(
                'input',
                function () {
                    if (formSearch) {
                        formSearch.value =
                            liveSearch.value;
                    }

                    applyLiveSearch();
                }
            );

            formSearch?.addEventListener(
                'input',
                function () {
                    if (liveSearch) {
                        liveSearch.value =
                            formSearch.value;
                    }

                    applyLiveSearch();
                }
            );

            const filterButton =
                document.getElementById(
                    'toggleConsultationFilters'
                );

            const filterPanel =
                document.getElementById(
                    'consultationFilterPanel'
                );

            filterButton?.addEventListener(
                'click',
                function () {
                    filterPanel?.classList.toggle(
                        'hidden'
                    );

                    const isOpen =
                        !filterPanel
                            ?.classList
                            .contains('hidden');

                    filterButton.setAttribute(
                        'aria-expanded',
                        isOpen ? 'true' : 'false'
                    );
                }
            );
            document
                .querySelectorAll('form')
                .forEach((form) => {
                    form.addEventListener(
                        'submit',
                        function () {
                            const submitButton =
                                form.querySelector(
                                    'button[type="submit"]'
                                );

                            if (!submitButton) {
                                return;
                            }

                            submitButton.disabled = true;
                            submitButton.classList.add(
                                'opacity-60',
                                'cursor-not-allowed'
                            );
                        }
                    );
                });
        });
    </script>
</x-app-layout>
