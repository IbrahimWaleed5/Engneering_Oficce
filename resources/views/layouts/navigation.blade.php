@php
    $user = auth()->user();
    $role = $user?->role;
    $unreadNotifications = $user
        ? $user->unreadNotifications()->count()
        : 0;

    $homeLink = $user
        ? route('dashboard')
        : route('home');

    $navItemBase = 'group relative inline-flex items-center gap-2 rounded-2xl px-3.5 py-2.5 text-sm font-bold transition-all duration-200';
    $navItemIdle = 'text-slate-300 hover:-translate-y-0.5 hover:bg-white/[0.07] hover:text-white';
    $navItemActive = 'bg-gradient-to-l from-cyan-500 to-blue-600 text-white shadow-lg shadow-cyan-500/20 ring-1 ring-cyan-300/20';

    $mobileItemBase = 'flex w-full items-center gap-3 rounded-2xl border px-4 py-3.5 text-right text-sm font-bold transition-all duration-200';
    $mobileItemIdle = 'border-white/[0.07] bg-white/[0.03] text-slate-200 hover:border-cyan-400/20 hover:bg-cyan-500/10 hover:text-white';
    $mobileItemActive = 'border-cyan-400/25 bg-gradient-to-l from-cyan-500/25 to-blue-600/25 text-white shadow-lg shadow-cyan-500/10';
@endphp

<nav
    id="main-navigation"
    class="sticky top-0 z-50 border-b border-white/[0.07] bg-slate-950/75 shadow-[0_12px_50px_rgba(2,6,23,0.35)] backdrop-blur-2xl"
    dir="rtl"
>
    {{-- إضاءة خفيفة أعلى الناف --}}
    <div
        class="absolute inset-x-0 top-0 h-px pointer-events-none bg-gradient-to-l from-transparent via-cyan-400/70 to-transparent"
    ></div>

    <div class="relative mx-auto max-w-[1500px] px-3 sm:px-5 lg:px-7">
        <div class="flex h-[76px] items-center justify-between gap-3">

            {{-- الشعار والروابط الرئيسية --}}
            <div class="flex items-center min-w-0 gap-3 xl:gap-6">
                <a
                    href="{{ $homeLink }}"
                    class="flex items-center min-w-0 gap-3 outline-none group rounded-2xl focus-visible:ring-2 focus-visible:ring-cyan-400/70"
                    aria-label="مكتب الوليد الهندسي"
                >
                    <div class="relative shrink-0">
                        <div
                            class="absolute transition -inset-1 rounded-2xl bg-gradient-to-br from-cyan-400/35 to-blue-600/35 opacity-70 blur group-hover:opacity-100"
                        ></div>

                        <div
                            class="relative flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl border border-cyan-300/20 bg-slate-900/90 shadow-xl shadow-cyan-950/40 sm:h-[52px] sm:w-[52px]"
                        >
                            <img
                                src="{{ asset('images/Mainlogo.png') }}"
                                alt="شعار مكتب الوليد الهندسي"
                                class="h-full w-full object-contain p-1.5 transition duration-300 group-hover:scale-105"
                            >
                        </div>
                    </div>

                    <div class="hidden min-w-0 sm:block">
                        <p class="truncate text-[15px] font-black tracking-tight text-white xl:text-base">
                            مكتب الوليد الهندسي
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.9)]"></span>
                            <p class="truncate text-[11px] font-semibold text-slate-400">
                                منصة الاستشارات الهندسية
                            </p>
                        </div>
                    </div>
                </a>

                {{-- روابط سطح المكتب --}}
                <div class="hidden items-center gap-1.5 lg:flex">
                    <a
                        href="{{ $homeLink }}"
                        class="{{ $navItemBase }} {{ request()->routeIs('dashboard', 'home') ? $navItemActive : $navItemIdle }}"
                    >
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6" />
                        </svg>
                        <span>لوحة التحكم</span>
                    </a>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="{{ $navItemBase }} {{ request()->routeIs('engineer.works.public', 'engineer.works.show') ? $navItemActive : $navItemIdle }}"
                    >
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16M6 20V8l6-4 6 4v12M9 11h1m4 0h1M9 15h1m4 0h1" />
                        </svg>
                        <span>مكتبة المهندسين</span>
                    </a>

                    @auth
                        @if ($role === 'customer')
                            <a
                                href="{{ route('consultations.mine') }}"
                                class="{{ $navItemBase }} {{ request()->routeIs('consultations.mine', 'consultations.messages.*', 'payments.create') ? $navItemActive : $navItemIdle }}"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                                <span>استشاراتي</span>
                            </a>
                        @endif

                        @if ($role === 'engineer')
                            <a
                                href="{{ route('engineer.consultations') }}"
                                class="{{ $navItemBase }} {{ request()->routeIs('engineer.consultations') ? $navItemActive : $navItemIdle }}"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h4M7 3h7l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h5" />
                                </svg>
                                <span>طلباتي</span>
                            </a>

                            <a
                                href="{{ route('engineer.works.mine') }}"
                                class="{{ $navItemBase }} {{ request()->routeIs('engineer.works.mine', 'engineer.works.create') ? $navItemActive : $navItemIdle }}"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16M7 20v-9h10v9M9 11V7h6v4M8 7l4-4 4 4" />
                                </svg>
                                <span>أعمالي</span>
                            </a>
                        @endif

                        @if ($role === 'admin')
                            <a
                                href="{{ route('consultations.index') }}"
                                class="{{ $navItemBase }} {{ request()->routeIs('consultations.index', 'consultations.assign.*') ? $navItemActive : $navItemIdle }}"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 9h6M9 13h4M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                </svg>
                                <span>الاستشارات</span>
                            </a>

                            <a
                                href="{{ route('payments.index') }}"
                                class="{{ $navItemBase }} {{ request()->routeIs('payments.*') ? $navItemActive : $navItemIdle }}"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2zM7 15h4" />
                                </svg>
                                <span>الدفعات</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- الطرف الأيسر في سطح المكتب --}}
            <div class="flex items-center gap-2 shrink-0">
                @auth
                    {{-- الإشعارات --}}
                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative hidden h-11 w-11 items-center justify-center rounded-2xl border border-white/[0.08] bg-white/[0.04] text-slate-300 transition hover:-translate-y-0.5 hover:border-cyan-400/25 hover:bg-cyan-500/10 hover:text-white lg:flex"
                        title="الإشعارات"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 10-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
                        </svg>

                        @if ($unreadNotifications > 0)
                            <span
                                class="absolute -left-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-slate-950 bg-rose-500 px-1 text-[10px] font-black text-white shadow-lg shadow-rose-500/30"
                            >
                                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                            </span>
                        @endif
                    </a>

                    {{-- قائمة الحساب على سطح المكتب --}}
                    <div
                        id="account-menu-wrapper"
                        class="relative hidden lg:block"
                    >
                        <button
                            id="account-menu-button"
                            type="button"
                            class="flex items-center gap-3 rounded-2xl border border-white/[0.08] bg-white/[0.04] p-1.5 pl-3 text-right transition hover:border-cyan-400/25 hover:bg-white/[0.07] focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60"
                            aria-expanded="false"
                            aria-controls="account-menu"
                        >
                            <div class="relative shrink-0">
                                @if ($user->profile_photo)
                                    <img
                                        src="{{ asset('storage/' . $user->profile_photo) }}"
                                        alt="{{ $user->name }}"
                                        class="object-cover w-10 h-10 border rounded-xl border-cyan-300/25"
                                    >
                                @else
                                    <div
                                        class="flex items-center justify-center w-10 h-10 text-base font-black text-white border rounded-xl border-cyan-300/25 bg-gradient-to-br from-cyan-500 to-blue-600"
                                    >
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                @endif

                                <span
                                    class="absolute -bottom-0.5 -left-0.5 h-3 w-3 rounded-full border-2 border-slate-950 bg-emerald-400"
                                ></span>
                            </div>

                            <div class="hidden max-w-32 xl:block">
                                <p class="text-xs font-black text-white truncate">
                                    {{ $user->name }}
                                </p>
                                <p class="mt-0.5 truncate text-[10px] font-semibold text-slate-400">
                                    @switch($role)
                                        @case('admin') مدير النظام @break
                                        @case('engineer') مهندس @break
                                        @case('employee') موظف @break
                                        @default عميل
                                    @endswitch
                                </p>
                            </div>

                            <svg
                                id="account-menu-chevron"
                                class="w-4 h-4 transition text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            id="account-menu"
                            class="absolute left-0 mt-3 hidden w-72 origin-top-left overflow-hidden rounded-3xl border border-white/[0.09] bg-slate-950/95 p-2 opacity-0 scale-95 shadow-2xl shadow-black/40 backdrop-blur-2xl transition duration-150"
                        >
                            <div class="mb-2 rounded-2xl border border-white/[0.07] bg-white/[0.04] p-4">
                                <p class="text-sm font-black text-white truncate">
                                    {{ $user->name }}
                                </p>
                                <p class="mt-1 text-xs truncate text-slate-400">
                                    {{ $user->email }}
                                </p>

                                @if ($role === 'engineer' && $user->employeeProfile?->specialty)
                                    <div class="inline-flex items-center gap-2 px-3 py-2 mt-3 text-xs font-bold rounded-xl bg-cyan-500/10 text-cyan-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>
                                        {{ $user->employeeProfile->specialty->name }}
                                    </div>
                                @endif
                            </div>

                            <a
                                href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-200 transition hover:bg-white/[0.06] hover:text-white"
                            >
                                <span class="flex items-center justify-center h-9 w-9 rounded-xl bg-cyan-500/10 text-cyan-300">👤</span>
                                <span>إعدادات الحساب</span>
                            </a>

                            @if ($role === 'engineer')
                                <a
                                    href="{{ route('engineers.show', $user) }}"
                                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-200 transition hover:bg-white/[0.06] hover:text-white"
                                >
                                    <span class="flex items-center justify-center h-9 w-9 rounded-xl bg-amber-500/10 text-amber-300">⭐</span>
                                    <span>صفحتي العامة</span>
                                </a>

                                <a
                                    href="{{ route('engineer.specialty.edit') }}"
                                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-200 transition hover:bg-white/[0.06] hover:text-white"
                                >
                                    <span class="flex items-center justify-center h-9 w-9 rounded-xl bg-violet-500/10 text-violet-300">🎓</span>
                                    <span>التخصص والنبذة</span>
                                </a>
                            @endif

                            @if ($role === 'admin')
                                <a
                                    href="{{ route('users.index') }}"
                                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-slate-200 transition hover:bg-white/[0.06] hover:text-white"
                                >
                                    <span class="flex items-center justify-center text-blue-300 h-9 w-9 rounded-xl bg-blue-500/10">⚙️</span>
                                    <span>إدارة المستخدمين</span>
                                </a>
                            @endif

                            <div class="my-2 h-px bg-white/[0.07]"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex items-center w-full gap-3 px-4 py-3 text-sm font-black text-right transition rounded-2xl text-rose-300 hover:bg-rose-500/10 hover:text-rose-200"
                                >
                                    <span class="flex items-center justify-center h-9 w-9 rounded-xl bg-rose-500/10">🚪</span>
                                    <span>تسجيل الخروج</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="items-center hidden gap-2 lg:flex">
                        <a
                            href="{{ route('login') }}"
                            class="rounded-2xl px-4 py-2.5 text-sm font-black text-slate-200 transition hover:bg-white/[0.06] hover:text-white"
                        >
                            تسجيل الدخول
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="rounded-2xl bg-gradient-to-l from-cyan-500 to-blue-600 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-cyan-500/20 transition hover:-translate-y-0.5"
                        >
                            إنشاء حساب
                        </a>
                    </div>
                @endauth

                {{-- زر قائمة الجوال --}}
                <button
                    id="mobile-menu-open"
                    type="button"
                    class="relative flex h-11 w-11 items-center justify-center overflow-hidden rounded-2xl border border-white/[0.09] bg-white/[0.05] text-white shadow-lg transition hover:border-cyan-400/30 hover:bg-cyan-500/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/70 lg:hidden"
                    aria-label="فتح القائمة"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <span class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-blue-600/10"></span>
                    <svg class="relative w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- طبقة خلفية لقائمة الجوال --}}
    <div
        id="mobile-menu-backdrop"
        class="fixed inset-0 z-[60] hidden bg-slate-950/75 opacity-0 backdrop-blur-sm transition-opacity duration-300 lg:hidden"
        aria-hidden="true"
    ></div>

    {{-- قائمة الجوال الجانبية --}}
    <aside
        id="mobile-menu"
        class="pointer-events-none fixed right-0 top-0 z-[70] flex h-dvh w-[min(90vw,390px)] translate-x-full flex-col border-l border-white/[0.08] bg-slate-950/95 opacity-0 shadow-2xl shadow-black/50 backdrop-blur-2xl transition duration-300 lg:hidden"
        aria-label="قائمة التنقل للجوال"
        aria-hidden="true"
    >
        {{-- رأس القائمة --}}
        <div class="relative overflow-hidden border-b border-white/[0.07] p-4">
            <div class="absolute w-48 h-48 rounded-full pointer-events-none -right-20 -top-20 bg-cyan-500/15 blur-3xl"></div>

            <div class="relative flex items-center justify-between gap-3">
                <div class="flex items-center min-w-0 gap-3">
                    <div class="flex items-center justify-center w-12 h-12 overflow-hidden border shrink-0 rounded-2xl border-cyan-300/20 bg-slate-900">
                        <img
                            src="{{ asset('images/Mainlogo.png') }}"
                            alt="شعار مكتب الوليد الهندسي"
                            class="h-full w-full object-contain p-1.5"
                        >
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-black text-white truncate">
                            مكتب الوليد الهندسي
                        </p>
                        <p class="mt-1 truncate text-[11px] font-semibold text-slate-400">
                            منصة الاستشارات الهندسية
                        </p>
                    </div>
                </div>

                <button
                    id="mobile-menu-close"
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-white/[0.08] bg-white/[0.05] text-slate-300 transition hover:bg-rose-500/10 hover:text-rose-300"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            @auth
                <div class="relative mt-4 flex items-center gap-3 rounded-2xl border border-white/[0.07] bg-white/[0.04] p-3">
                    @if ($user->profile_photo)
                        <img
                            src="{{ asset('storage/' . $user->profile_photo) }}"
                            alt="{{ $user->name }}"
                            class="object-cover w-12 h-12 border shrink-0 rounded-2xl border-cyan-300/25"
                        >
                    @else
                        <div class="flex items-center justify-center w-12 h-12 text-lg font-black text-white shrink-0 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-white truncate">
                            {{ $user->name }}
                        </p>
                        <p class="mt-1 text-xs truncate text-slate-400">
                            {{ $user->email }}
                        </p>
                    </div>

                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,0.8)]"></span>
                </div>
            @endauth
        </div>

        {{-- روابط القائمة --}}
        <div class="flex-1 px-4 py-5 space-y-5 overflow-y-auto overscroll-contain">
            <div>
                <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                    القائمة الرئيسية
                </p>

                <div class="space-y-2">
                    <a
                        href="{{ $homeLink }}"
                        class="{{ $mobileItemBase }} {{ request()->routeIs('dashboard', 'home') ? $mobileItemActive : $mobileItemIdle }}"
                    >
                        <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-cyan-500/10 text-cyan-300">🏠</span>
                        <span>لوحة التحكم</span>
                    </a>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="{{ $mobileItemBase }} {{ request()->routeIs('engineer.works.public', 'engineer.works.show') ? $mobileItemActive : $mobileItemIdle }}"
                    >
                        <span class="flex items-center justify-center w-10 h-10 text-blue-300 shrink-0 rounded-xl bg-blue-500/10">👷</span>
                        <span>مكتبة المهندسين</span>
                    </a>
                </div>
            </div>

            @auth
                <div>
                    <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                        حسابي
                    </p>

                    <div class="space-y-2">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="{{ $mobileItemBase }} {{ request()->routeIs('profile.*') ? $mobileItemActive : $mobileItemIdle }}"
                        >
                            <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-violet-500/10 text-violet-300">👤</span>
                            <span>إعدادات الحساب</span>
                        </a>

                        <a
                            href="{{ route('notifications.index') }}"
                            class="{{ $mobileItemBase }} {{ request()->routeIs('notifications.*') ? $mobileItemActive : $mobileItemIdle }}"
                        >
                            <span class="relative flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-amber-500/10 text-amber-300">
                                🔔
                                @if ($unreadNotifications > 0)
                                    <span class="absolute -left-1 -top-1 flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-black text-white">
                                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                                    </span>
                                @endif
                            </span>
                            <span>الإشعارات</span>
                        </a>
                    </div>
                </div>

                @if ($role === 'customer')
                    <div>
                        <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                            خدمات العميل
                        </p>

                        <div class="space-y-2">
                            <a
                                href="{{ route('consultations.mine') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('consultations.mine', 'consultations.messages.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-emerald-500/10 text-emerald-300">📋</span>
                                <span>استشاراتي</span>
                            </a>

                            <a
                                href="{{ route('consultations.create') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('consultations.create') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-cyan-500/10 text-cyan-300">➕</span>
                                <span>طلب استشارة جديدة</span>
                            </a>
                        </div>
                    </div>
                @endif

                @if ($role === 'engineer')
                    <div>
                        <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                            لوحة المهندس
                        </p>

                        <div class="space-y-2">
                            <a
                                href="{{ route('engineer.consultations') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('engineer.consultations') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-cyan-500/10 text-cyan-300">📐</span>
                                <span>استشارات المهندس</span>
                            </a>

                            <a
                                href="{{ route('engineer.works.mine') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('engineer.works.mine', 'engineer.works.create') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 text-blue-300 shrink-0 rounded-xl bg-blue-500/10">🏗️</span>
                                <span>أعمالي</span>
                            </a>

                            <a
                                href="{{ route('engineers.show', $user) }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('engineers.show') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-amber-500/10 text-amber-300">⭐</span>
                                <span>صفحتي العامة</span>
                            </a>

                            <a
                                href="{{ route('engineer.specialty.edit') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('engineer.specialty.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-violet-500/10 text-violet-300">🎓</span>
                                <span>التخصص والنبذة</span>
                            </a>
                        </div>
                    </div>
                @endif

                @if ($role === 'admin')
                    <div>
                        <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                            إدارة النظام
                        </p>

                        <div class="space-y-2">
                            <a
                                href="{{ route('consultations.index') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('consultations.index', 'consultations.assign.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-cyan-500/10 text-cyan-300">📋</span>
                                <span>جميع الاستشارات</span>
                            </a>

                            <a
                                href="{{ route('payments.index') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('payments.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-emerald-500/10 text-emerald-300">💳</span>
                                <span>إدارة الدفعات</span>
                            </a>

                            <a
                                href="{{ route('employees.index') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('employees.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 text-blue-300 shrink-0 rounded-xl bg-blue-500/10">👥</span>
                                <span>الموظفون</span>
                            </a>

                            <a
                                href="{{ route('users.index') }}"
                                    class="{{ $mobileItemBase }} {{ request()->routeIs('users.*') ? $mobileItemActive : $mobileItemIdle }}"
                            >
                                <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-violet-500/10 text-violet-300">⚙️</span>
                                <span>إدارة المستخدمين</span>
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <div>
                    <p class="mb-2 px-2 text-[10px] font-black uppercase tracking-[0.25em] text-slate-500">
                        الحساب
                    </p>

                    <div class="space-y-2">
                        <a
                            href="{{ route('login') }}"
                            class="{{ $mobileItemBase }} {{ $mobileItemIdle }}"
                        >
                            <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-cyan-500/10 text-cyan-300">🔑</span>
                            <span>تسجيل الدخول</span>
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="{{ $mobileItemBase }} border-cyan-400/25 bg-gradient-to-l from-cyan-500/25 to-blue-600/25 text-white"
                        >
                            <span class="flex items-center justify-center w-10 h-10 shrink-0 rounded-xl bg-white/10">✨</span>
                            <span>إنشاء حساب جديد</span>
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        {{-- أسفل القائمة --}}
        @auth
            <div class="border-t border-white/[0.07] p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl border border-rose-400/20 bg-rose-500/10 px-4 py-3.5 text-sm font-black text-rose-200 transition hover:bg-rose-500/15 hover:text-white"
                    >
                        <span>🚪</span>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const accountWrapper = document.getElementById('account-menu-wrapper');
            const accountButton = document.getElementById('account-menu-button');
            const accountMenu = document.getElementById('account-menu');
            const accountChevron = document.getElementById('account-menu-chevron');

            const mobileOpenButton = document.getElementById('mobile-menu-open');
            const mobileCloseButton = document.getElementById('mobile-menu-close');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileBackdrop = document.getElementById('mobile-menu-backdrop');

            function openAccountMenu() {
                if (!accountMenu || !accountButton) return;

                accountMenu.classList.remove('hidden');

                requestAnimationFrame(function () {
                    accountMenu.classList.remove('opacity-0', 'scale-95');
                    accountMenu.classList.add('opacity-100', 'scale-100');
                });

                accountButton.setAttribute('aria-expanded', 'true');
                accountChevron?.classList.add('rotate-180');
            }

            function closeAccountMenu() {
                if (!accountMenu || !accountButton) return;

                accountMenu.classList.remove('opacity-100', 'scale-100');
                accountMenu.classList.add('opacity-0', 'scale-95');
                accountButton.setAttribute('aria-expanded', 'false');
                accountChevron?.classList.remove('rotate-180');

                window.setTimeout(function () {
                    if (accountButton.getAttribute('aria-expanded') === 'false') {
                        accountMenu.classList.add('hidden');
                    }
                }, 150);
            }

            function toggleAccountMenu(event) {
                event.preventDefault();
                event.stopPropagation();

                if (!accountMenu || !accountButton) return;

                const isOpen = accountButton.getAttribute('aria-expanded') === 'true';
                isOpen ? closeAccountMenu() : openAccountMenu();
            }

            function openMobileMenu() {
                if (!mobileMenu || !mobileBackdrop || !mobileOpenButton) return;

                mobileBackdrop.classList.remove('hidden');
                mobileMenu.classList.remove('pointer-events-none');
                mobileMenu.setAttribute('aria-hidden', 'false');
                mobileOpenButton.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');

                requestAnimationFrame(function () {
                    mobileBackdrop.classList.remove('opacity-0');
                    mobileBackdrop.classList.add('opacity-100');
                    mobileMenu.classList.remove('translate-x-full', 'opacity-0');
                    mobileMenu.classList.add('translate-x-0', 'opacity-100');
                });
            }

            function closeMobileMenu() {
                if (!mobileMenu || !mobileBackdrop || !mobileOpenButton) return;

                mobileBackdrop.classList.remove('opacity-100');
                mobileBackdrop.classList.add('opacity-0');
                mobileMenu.classList.remove('translate-x-0', 'opacity-100');
                mobileMenu.classList.add('translate-x-full', 'opacity-0', 'pointer-events-none');
                mobileMenu.setAttribute('aria-hidden', 'true');
                mobileOpenButton.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');

                window.setTimeout(function () {
                    if (mobileMenu.getAttribute('aria-hidden') === 'true') {
                        mobileBackdrop.classList.add('hidden');
                    }
                }, 300);
            }

            accountButton?.addEventListener('click', toggleAccountMenu);

            document.addEventListener('click', function (event) {
                if (
                    accountWrapper
                    && !accountWrapper.contains(event.target)
                ) {
                    closeAccountMenu();
                }
            });

            mobileOpenButton?.addEventListener('click', function (event) {
                event.preventDefault();
                openMobileMenu();
            });

            mobileCloseButton?.addEventListener('click', closeMobileMenu);
            mobileBackdrop?.addEventListener('click', closeMobileMenu);

            mobileMenu?.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeMobileMenu);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAccountMenu();
                    closeMobileMenu();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) {
                    closeMobileMenu();
                }
            });
        });
    </script>

</nav>
