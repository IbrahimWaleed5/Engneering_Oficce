<x-app-layout>
    <style>
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800 {
            display: none !important;
        }

        .office-team-page {
            min-height: 100vh;
            background: #020617;
            color: #dae2fd;
            font-family: "Hanken Grotesk", "Almarai", system-ui, sans-serif;
        }

        .office-team-glass {
            background: rgba(15, 23, 42, .72);
            border: 1px solid #1e293b;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .office-team-stat {
            min-height: 8rem;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .office-team-stat:hover {
            transform: translateY(-2px);
        }

        .office-team-stat.pending {
            box-shadow: inset 0 -2px 0 rgba(251, 191, 36, .55), inset 0 0 20px rgba(251, 191, 36, .08);
        }

        .office-team-stat.approved {
            box-shadow: inset 0 -2px 0 rgba(16, 185, 129, .55), inset 0 0 20px rgba(16, 185, 129, .08);
        }

        .office-team-stat.rejected {
            box-shadow: inset 0 -2px 0 rgba(225, 29, 72, .55), inset 0 0 20px rgba(225, 29, 72, .08);
        }

        .office-team-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .office-team-table th,
        .office-team-table td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #1e293b;
        }

        .office-team-table th {
            background: rgba(30, 41, 59, .5);
            color: #8c909f;
            font-size: .875rem;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .office-team-table tbody tr {
            transition: background .2s ease;
        }

        .office-team-table tbody tr:hover {
            background: rgba(30, 41, 59, .3);
        }

        .office-team-table tbody tr:last-child td {
            border-bottom: none;
        }

        .office-team-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .office-team-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .office-team-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 999px;
        }

        @media (max-width: 768px) {
            .office-team-table-card {
                display: none;
            }

            .office-team-mobile-list {
                display: grid !important;
            }
        }

        .apex-office-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
            overflow-x: hidden;
            background: #0b1326;
        }

        .apex-side-nav {
            width: 16rem;
            min-height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            z-index: 60;
            display: flex;
            flex-direction: column;
            border-left: 1px solid #424754;
            background: #171f33;
            transition: transform .25s ease;
        }

        .apex-main-area {
            min-height: 100vh;
            width: 100%;
            margin-right: 16rem;
            display: flex;
            flex-direction: column;
        }

        .apex-topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #424754;
            background: rgba(11, 19, 38, .96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .apex-side-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            border-radius: .5rem;
            padding: 1rem;
            color: #c2c6d6;
            font-weight: 700;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .apex-side-link:hover {
            background: #222a3d;
            color: #adc6ff;
        }

        .apex-side-link:active {
            transform: scale(.98);
        }

        .apex-side-link.active {
            background: #00a572;
            color: #00311f;
        }

        .apex-mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 55;
            background: rgba(2, 6, 23, .72);
            backdrop-filter: blur(3px);
        }

        @media (max-width: 767px) {
            .apex-side-nav {
                transform: translateX(100%);
            }

            .apex-side-nav.open {
                transform: translateX(0);
            }

            .apex-mobile-overlay.open {
                display: block;
            }

            .apex-main-area {
                margin-right: 0;
            }
        }

    </style>

    <div class="office-team-page" dir="rtl">
        <div class="apex-office-shell">
            <div id="apexMobileOverlay" class="apex-mobile-overlay"></div>

            <aside id="apexSideNav" class="apex-side-nav">
                <div class="flex items-center gap-4 border-b border-[#424754] p-6">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#4d8eff] font-black text-[#00285d]">
                        {{ mb_substr($office->name, 0, 1) }}
                    </div>

                    <div class="min-w-0">
                        <h2 class="truncate text-lg font-black text-[#adc6ff]">
                            {{ $office->name }}
                        </h2>
                        <p class="truncate text-xs text-[#c2c6d6]">
                            إدارة المكتب الهندسي
                        </p>
                    </div>
                </div>

                <div class="p-4">
                    @if (Route::has('office.members.index'))
                        <a
                            href="{{ route('office.members.index') }}"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#adc6ff] px-4 py-3 font-black text-[#002e6a] transition hover:brightness-110"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="8" r="3"/>
                                <path d="M2 20a7 7 0 0 1 14 0M19 8v6M16 11h6"/>
                            </svg>
                            أعضاء المكتب
                        </a>
                    @endif
                </div>

                <nav class="flex-1 space-y-2 overflow-y-auto px-3 py-2">
                    <a href="{{ route('office.dashboard') }}" class="apex-side-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        نظرة عامة
                    </a>

                    <a href="{{ route('office-membership-applications.index') }}" class="apex-side-link active">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="10" cy="8" r="3"/>
                            <path d="M3 20a7 7 0 0 1 14 0M16 8h5M18.5 5.5v5"/>
                        </svg>
                        طلبات الانضمام
                    </a>

                    @if (Route::has('office.members.index'))
                        <a href="{{ route('office.members.index') }}" class="apex-side-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M2 20a7 7 0 0 1 14 0M14 16a6 6 0 0 1 8 4"/>
                            </svg>
                            فريق المكتب
                        </a>
                    @endif

                    @if (Route::has('office.consultations.index'))
                        <a href="{{ route('office.consultations.index') }}" class="apex-side-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 4h16v12H7l-3 3V4Z"/>
                                <path d="M8 8h8M8 12h5"/>
                            </svg>
                            استشارات المكتب
                        </a>
                    @endif

                    @if (Route::has('office.profile'))
                        <a href="{{ route('office.profile') }}" class="apex-side-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 21h16M6 21V9l6-5 6 5v12M9 21v-6h6v6"/>
                            </svg>
                            ملف المكتب
                        </a>
                    @endif
                </nav>

                <div class="space-y-2 border-t border-[#424754] p-3">
                    @if (Route::has('support.index'))
                        <a href="{{ route('support.index') }}" class="apex-side-link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9.5 9a2.5 2.5 0 1 1 4 2c-1 .7-1.5 1.2-1.5 2.5M12 17h.01"/>
                            </svg>
                            الدعم الفني
                        </a>
                    @endif
                </div>
            </aside>

            <div class="apex-main-area">
                <header class="apex-topbar px-4 sm:px-6">
                    <div class="flex items-center gap-3 md:hidden">
                        <button
                            type="button"
                            id="apexMenuButton"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-[#424754] bg-[#171f33] text-[#dae2fd]"
                            aria-label="فتح القائمة"
                        >
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16"/>
                            </svg>
                        </button>

                        <span class="font-black text-[#dae2fd]">إدارة فريق المكتب</span>
                    </div>

                    <div class="hidden md:block"></div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('notifications.index'))
                            <a
                                href="{{ route('notifications.index') }}"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-[#171f33] text-[#dae2fd] transition hover:bg-[#222a3d] hover:text-[#adc6ff]"
                                aria-label="الإشعارات"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                                </svg>
                            </a>
                        @endif

                        @if (Route::has('office.profile'))
                            <a
                                href="{{ route('office.profile') }}"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-[#171f33] text-[#dae2fd] transition hover:bg-[#222a3d] hover:text-[#adc6ff]"
                                aria-label="إعدادات المكتب"
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </header>

                <main class="mx-auto flex w-full max-w-[1200px] flex-col gap-8 px-4 py-10 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-xl border border-green-500/20 bg-green-500/10 p-4 text-green-100">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Header --}}
            <section class="flex flex-col items-start justify-between gap-6 border-b border-[#1e293b] pb-6 md:flex-row md:items-end">
                <div>
                    <a
                        href="{{ route('office.dashboard') }}"
                        class="mb-4 inline-flex items-center gap-2 text-sm font-bold text-[#adc6ff] transition hover:text-white"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        العودة إلى لوحة المكتب
                    </a>

                    <h1 class="flex items-center gap-3 text-3xl font-black text-[#dae2fd] sm:text-4xl">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="text-[#adc6ff]">
                            <circle cx="9" cy="8" r="3"/>
                            <circle cx="17" cy="9" r="2.5"/>
                            <path d="M3 20c0-4 2.5-6 6-6s6 2 6 6M14 15c3 0 5 1.5 5 5"/>
                        </svg>
                        إدارة فريق المكتب
                    </h1>

                    <p class="mt-3 max-w-2xl leading-8 text-[#c2c6d6]">
                        نظرة شاملة على طلبات الانضمام إلى {{ $office->name }} ومتابعة حالة المتقدمين.
                    </p>
                </div>

                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                    <button
                        type="button"
                        id="toggleOfficeTeamFilters"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#424754] bg-[#1e293b] px-5 py-3 font-bold text-[#adc6ff] transition hover:bg-[#2d3449]"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 5h16M7 12h10M10 19h4"/>
                        </svg>
                        تصفية
                    </button>

                    @if (Route::has('office.members.index'))
                        <a
                            href="{{ route('office.members.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#adc6ff] px-5 py-3 font-black text-[#002e6a] shadow-[0_0_15px_rgba(77,142,255,.3)] transition hover:brightness-110"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="8" r="3"/>
                                <path d="M2 20a7 7 0 0 1 14 0M19 8v6M16 11h6"/>
                            </svg>
                            أعضاء المكتب
                        </a>
                    @endif
                </div>
            </section>

            {{-- Filter panel --}}
            <section id="officeTeamFilters" class="office-team-glass hidden rounded-xl p-5">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label for="officeTeamSearch" class="mb-2 block text-sm font-bold text-[#c2c6d6]">
                            بحث
                        </label>

                        <div class="relative">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8c909f]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3.5-3.5"/>
                            </svg>

                            <input
                                id="officeTeamSearch"
                                type="search"
                                placeholder="بحث بالاسم أو البريد أو التخصص..."
                                class="w-full rounded-lg border border-[#1e293b] bg-[#020617] py-3 pl-4 pr-11 text-[#dae2fd] placeholder:text-[#8c909f] focus:border-[#adc6ff] focus:ring-[#adc6ff]"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="officeTeamStatus" class="mb-2 block text-sm font-bold text-[#c2c6d6]">
                            الحالة
                        </label>

                        <select
                            id="officeTeamStatus"
                            class="w-full rounded-lg border border-[#1e293b] bg-[#020617] px-4 py-3 text-[#dae2fd] focus:border-[#adc6ff] focus:ring-[#adc6ff]"
                        >
                            <option value="all">جميع الحالات</option>
                            <option value="pending">قيد المراجعة</option>
                            <option value="approved">مقبول</option>
                            <option value="rejected">مرفوض</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- Stats --}}
            <section class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <article class="office-team-glass office-team-stat pending relative flex flex-col justify-between overflow-hidden rounded-xl p-6">
                    <div class="flex items-start justify-between">
                        <span class="text-[#c2c6d6]">قيد المراجعة</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fcd34d" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 2h12M6 22h12M7 2c0 5 5 5 5 10s-5 5-5 10M17 2c0 5-5 5-5 10s5 5 5 10"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-black text-white">{{ $statistics['pending'] ?? 0 }}</div>
                </article>

                <article class="office-team-glass office-team-stat approved relative flex flex-col justify-between overflow-hidden rounded-xl p-6">
                    <div class="flex items-start justify-between">
                        <span class="text-[#c2c6d6]">طلبات مقبولة</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m8 12 2.5 2.5L16.5 8.5"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-black text-white">{{ $statistics['approved'] ?? 0 }}</div>
                </article>

                <article class="office-team-glass office-team-stat rejected relative flex flex-col justify-between overflow-hidden rounded-xl p-6">
                    <div class="flex items-start justify-between">
                        <span class="text-[#c2c6d6]">طلبات مرفوضة</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fb7185" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m8 8 8 8M16 8l-8 8"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-black text-white">{{ $statistics['rejected'] ?? 0 }}</div>
                </article>
            </section>

            {{-- Table --}}
            <section class="office-team-glass office-team-table-card overflow-hidden rounded-xl">
                <div class="flex flex-col gap-4 border-b border-[#1e293b] bg-[rgba(15,23,42,.9)] p-6 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-black text-[#dae2fd]">قائمة المتقدمين</h2>

                    <div class="relative w-full sm:w-72">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8c909f]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            id="officeTeamTableSearch"
                            type="search"
                            placeholder="بحث بالاسم أو التخصص..."
                            class="w-full rounded-lg border border-[#1e293b] bg-[#020617] py-2.5 pl-4 pr-10 text-sm text-[#dae2fd] placeholder:text-[#8c909f] focus:border-[#adc6ff] focus:ring-[#adc6ff]"
                        >
                    </div>
                </div>

                <div class="office-team-scroll overflow-x-auto">
                    <table class="office-team-table w-full min-w-[1050px] text-sm">
                        <thead>
                            <tr>
                                <th class="w-1/3">المهندس / بيانات التواصل</th>
                                <th>التخصص</th>
                                <th>الدور المطلوب</th>
                                <th>الخبرة</th>
                                <th>حالة الطلب</th>
                                <th>تاريخ التقديم</th>
                                <th class="text-center">إجراء</th>
                            </tr>
                        </thead>

                        <tbody id="officeTeamRows">
                            @forelse ($applications as $application)
                                @php
                                    $statusData = match ($application->status) {
                                        'approved' => [
                                            'label' => 'مقبول',
                                            'class' => 'border-green-500/20 bg-green-500/10 text-green-200',
                                            'dot' => 'bg-green-400',
                                        ],
                                        'rejected' => [
                                            'label' => 'مرفوض',
                                            'class' => 'border-red-500/20 bg-red-500/10 text-red-200',
                                            'dot' => 'bg-red-400',
                                        ],
                                        'cancelled' => [
                                            'label' => 'ملغي',
                                            'class' => 'border-white/10 bg-white/5 text-slate-300',
                                            'dot' => 'bg-slate-400',
                                        ],
                                        default => [
                                            'label' => 'قيد المراجعة',
                                            'class' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-200',
                                            'dot' => 'bg-yellow-300',
                                        ],
                                    };

                                    $engineerPhoto =
                                        $application->engineer?->profile_photo_path
                                        ?? $application->engineer?->profile_photo
                                        ?? null;

                                    $searchText = mb_strtolower(
                                        implode(' ', [
                                            $application->engineer?->name,
                                            $application->engineer?->email,
                                            $application->engineer?->phone,
                                            $application->specialty?->name,
                                            $application->requested_position,
                                        ])
                                    );
                                @endphp

                                <tr
                                    data-office-team-row
                                    data-status="{{ $application->status }}"
                                    data-search="{{ $searchText }}"
                                >
                                    <td>
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-full border-2 border-[#1e293b] bg-[#222a3d]">
                                                @if ($engineerPhoto)
                                                    <img
                                                        src="{{ asset('storage/' . $engineerPhoto) }}"
                                                        alt="{{ $application->engineer?->name }}"
                                                        class="h-full w-full object-cover"
                                                    >
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-[#adc6ff]">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <circle cx="12" cy="8" r="4"/>
                                                            <path d="M4 21a8 8 0 0 1 16 0"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex min-w-0 flex-col">
                                                <span class="truncate font-black text-[#dae2fd]">
                                                    {{ $application->engineer?->name ?? 'مستخدم غير موجود' }}
                                                </span>

                                                <span class="mt-1 truncate text-xs text-[#c2c6d6]">
                                                    {{ $application->engineer?->email }}
                                                </span>

                                                @if ($application->engineer?->phone)
                                                    <span class="mt-1 text-xs text-[#8c909f]" dir="ltr">
                                                        {{ $application->engineer->phone }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="flex items-center gap-2 font-bold text-[#dae2fd]">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4edea3" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="m13 2-8 12h7l-1 8 8-12h-7z"/>
                                            </svg>
                                            {{ $application->specialty?->name ?? 'غير محدد' }}
                                        </div>
                                    </td>

                                    <td class="text-[#c2c6d6]">
                                        {{ $application->requested_position ?: 'غير محدد' }}
                                    </td>

                                    <td>
                                        <span class="inline-flex rounded-md border border-[#424754] bg-[#1e293b] px-3 py-1 text-sm text-[#c2c6d6]">
                                            {{ $application->years_of_experience !== null
                                                ? $application->years_of_experience . ' سنة'
                                                : 'غير محددة' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-black {{ $statusData['class'] }}">
                                            <span class="h-2 w-2 rounded-full {{ $statusData['dot'] }}"></span>
                                            {{ $statusData['label'] }}
                                        </span>
                                    </td>

                                    <td class="text-[#c2c6d6]">
                                        {{ $application->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="text-center">
                                        <a
                                            href="{{ route('office-membership-applications.show', $application) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-400/30 bg-blue-500/10 px-4 py-2 font-bold text-[#adc6ff] transition hover:bg-blue-500/20"
                                        >
                                            عرض الطلب
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-14 text-center text-[#8c909f]">
                                        لا توجد طلبات انضمام إلى المكتب حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-[#1e293b] bg-[rgba(15,23,42,.5)] p-4 sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-sm text-[#c2c6d6]">
                        عرض {{ $applications->count() }} من {{ $applications->total() }} نتيجة
                    </span>

                    @if ($applications->hasPages())
                        <div>
                            {{ $applications->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </section>

            {{-- Mobile cards --}}
            <section class="office-team-mobile-list hidden gap-4">
                @forelse ($applications as $application)
                    @php
                        $statusData = match ($application->status) {
                            'approved' => [
                                'label' => 'مقبول',
                                'class' => 'border-green-500/20 bg-green-500/10 text-green-200',
                            ],
                            'rejected' => [
                                'label' => 'مرفوض',
                                'class' => 'border-red-500/20 bg-red-500/10 text-red-200',
                            ],
                            'cancelled' => [
                                'label' => 'ملغي',
                                'class' => 'border-white/10 bg-white/5 text-slate-300',
                            ],
                            default => [
                                'label' => 'قيد المراجعة',
                                'class' => 'border-yellow-500/20 bg-yellow-500/10 text-yellow-200',
                            ],
                        };

                        $engineerPhoto =
                            $application->engineer?->profile_photo_path
                            ?? $application->engineer?->profile_photo
                            ?? null;

                        $searchText = mb_strtolower(
                            implode(' ', [
                                $application->engineer?->name,
                                $application->engineer?->email,
                                $application->specialty?->name,
                                $application->requested_position,
                            ])
                        );
                    @endphp

                    <article
                        data-office-team-row
                        data-status="{{ $application->status }}"
                        data-search="{{ $searchText }}"
                        class="office-team-glass rounded-xl p-5"
                    >
                        <div class="flex items-start gap-3">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full border border-[#334155] bg-[#222a3d]">
                                @if ($engineerPhoto)
                                    <img src="{{ asset('storage/' . $engineerPhoto) }}" alt="{{ $application->engineer?->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-[#adc6ff]">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M4 21a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="truncate font-black text-white">{{ $application->engineer?->name ?? 'مستخدم غير موجود' }}</h2>
                                <p class="mt-1 truncate text-xs text-[#c2c6d6]">{{ $application->engineer?->email }}</p>
                            </div>

                            <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-black {{ $statusData['class'] }}">
                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-[#171f33] p-3">
                                <p class="text-xs text-[#8c909f]">التخصص</p>
                                <p class="mt-1 font-bold">{{ $application->specialty?->name ?? 'غير محدد' }}</p>
                            </div>

                            <div class="rounded-lg bg-[#171f33] p-3">
                                <p class="text-xs text-[#8c909f]">الخبرة</p>
                                <p class="mt-1 font-bold">
                                    {{ $application->years_of_experience !== null
                                        ? $application->years_of_experience . ' سنة'
                                        : 'غير محددة' }}
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('office-membership-applications.show', $application) }}"
                            class="mt-4 flex w-full items-center justify-center rounded-lg bg-[#adc6ff] px-4 py-3 font-black text-[#002e6a]"
                        >
                            عرض الطلب
                        </a>
                    </article>
                @empty
                    <div class="office-team-glass rounded-xl p-10 text-center text-[#8c909f]">
                        لا توجد طلبات انضمام إلى المكتب حتى الآن.
                    </div>
                @endforelse
            </section>
                </main>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterButton = document.getElementById('toggleOfficeTeamFilters');
            const filterPanel = document.getElementById('officeTeamFilters');
            const searchInput = document.getElementById('officeTeamSearch');
            const tableSearch = document.getElementById('officeTeamTableSearch');
            const statusSelect = document.getElementById('officeTeamStatus');
            const rows = Array.from(document.querySelectorAll('[data-office-team-row]'));
            const menuButton = document.getElementById('apexMenuButton');
            const sideNav = document.getElementById('apexSideNav');
            const mobileOverlay = document.getElementById('apexMobileOverlay');

            const toggleSideNav = () => {
                sideNav?.classList.toggle('open');
                mobileOverlay?.classList.toggle('open');
            };

            menuButton?.addEventListener('click', toggleSideNav);
            mobileOverlay?.addEventListener('click', toggleSideNav);

            filterButton?.addEventListener('click', function () {
                filterPanel?.classList.toggle('hidden');
            });

            const normalize = (value) =>
                (value || '').toString().trim().toLowerCase();

            const applyFilters = () => {
                const query = normalize(searchInput?.value || tableSearch?.value);
                const status = statusSelect?.value || 'all';

                rows.forEach((row) => {
                    const rowSearch = normalize(row.dataset.search);
                    const rowStatus = row.dataset.status || '';

                    const matchesSearch =
                        query === '' || rowSearch.includes(query);

                    const matchesStatus =
                        status === 'all' || rowStatus === status;

                    row.classList.toggle(
                        'hidden',
                        !(matchesSearch && matchesStatus)
                    );
                });
            };

            searchInput?.addEventListener('input', function () {
                if (tableSearch) {
                    tableSearch.value = this.value;
                }
                applyFilters();
            });

            tableSearch?.addEventListener('input', function () {
                if (searchInput) {
                    searchInput.value = this.value;
                }
                applyFilters();
            });

            statusSelect?.addEventListener('change', applyFilters);
        });
    </script>
</x-app-layout>
