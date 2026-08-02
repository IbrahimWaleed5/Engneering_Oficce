<x-app-layout>
    @php
        $currentUser = auth()->user();

        $totalConsultations = $consultations->count();
        $pendingConsultations = $consultations->where('status', 'pending')->count();
        $inProgressConsultations = $consultations->where('status', 'in_progress')->count();
        $completedConsultations = $consultations->where('status', 'completed')->count();
    @endphp

    <style>
        .engineer-consultations-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .engineer-consultations-glass {
            background: rgba(23, 31, 51, .6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
            transition: all .3s ease;
        }

        .engineer-consultations-glass:hover {
            border-color: rgba(180, 197, 255, .3);
            box-shadow: 0 0 20px rgba(37, 99, 235, .15);
        }

        .engineer-primary-gradient {
            background: linear-gradient(135deg, #2563eb, #8343f4);
        }

        .engineer-neon-text {
            background: linear-gradient(90deg, #b4c5ff, #ffb1c7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .engineer-scroll::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        .engineer-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .engineer-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .engineer-consultations-sidebar {
                display: none !important;
            }

            .engineer-consultations-main {
                margin-right: 0 !important;
            }

            .engineer-consultations-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="engineer-consultations-page" dir="rtl">
        {{-- القائمة الجانبية --}}
        <aside class="engineer-consultations-sidebar fixed right-0 top-0 z-50 hidden h-screen w-64 flex-col border-l border-white/10 bg-[#131b2e]/80 px-4 py-6 backdrop-blur-xl lg:flex">
            <div class="flex items-center gap-3 px-2 mb-10">
                <div class="flex items-center justify-center w-10 h-10 text-white shadow-lg engineer-primary-gradient rounded-xl">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-xl font-bold text-[#b4c5ff]">
                        CreativeHome
                    </h1>

                    <p class="text-xs text-[#c3c6d7] opacity-70">
                        لوحة التحكم الاحترافية
                    </p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>لوحة القيادة</span>
                </a>

                <a
                    href="{{ request()->url() }}"
                    class="flex items-center gap-3 rounded-lg border-r-4 border-[#b4c5ff] bg-[#2563eb]/20 px-4 py-3 text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="7" r="3"/>
                        <path d="M5 21a7 7 0 0 1 14 0M8 12h8M9 3h6"/>
                    </svg>

                    <span>استشاراتي</span>
                </a>

                @if (Route::has('engineer.works.index'))
                    <a
                        href="{{ route('engineer.works.index') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                        </svg>

                        <span>أعمالي</span>
                    </a>
                @endif

                @if (Route::has('conversations.index'))
                    <a
                        href="{{ route('conversations.index') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>

                        <span>المحادثات</span>
                    </a>
                @endif

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-6 mt-auto border-t border-white/5">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9.7 9a2.5 2.5 0 1 1 3.5 2.3c-.8.35-1.2.8-1.2 1.7M12 17h.01"/>
                    </svg>

                    <span>المساعدة</span>
                </a>
            </div>
        </aside>

        {{-- الشريط العلوي --}}
        <header class="engineer-consultations-topbar fixed top-0 left-0 right-0 z-40 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/60 px-6 backdrop-blur-md lg:right-64">
            <div class="flex items-center gap-6">
                <h2 class="text-xl font-black tracking-tight text-[#b4c5ff]">
                    نظام CreativeHome الهندسي
                </h2>

                <div class="relative hidden lg:block">
                    <svg class="absolute w-5 h-5 -translate-y-1/2 right-3 top-1/2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        id="engineerConsultationSearch"
                        type="search"
                        placeholder="بحث في الاستشارات..."
                        class="w-64 rounded-full border border-white/5 bg-[#2d3449]/50 py-2 pr-10 pl-4 text-sm text-white placeholder:text-[#c3c6d7]/50 focus:ring-1 focus:ring-[#b4c5ff]"
                    >
                </div>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('notifications.index'))
                    <a
                        href="{{ route('notifications.index') }}"
                        class="flex items-center justify-center w-10 h-10 rounded-lg text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                        title="الإشعارات"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>
                        </svg>
                    </a>
                @endif

                @if (Route::has('conversations.index'))
                    <a
                        href="{{ route('conversations.index') }}"
                        class="flex items-center justify-center w-10 h-10 rounded-lg text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]"
                        title="الرسائل"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-xs font-bold text-[#dae2fd]">
                            {{ $currentUser->name }}
                        </p>

                        <p class="text-[10px] text-[#c3c6d7]">
                            مهندس
                        </p>
                    </div>

                    <div class="w-10 h-10 overflow-hidden border-2 rounded-full border-[#b4c5ff]/20">
                        @if ($currentUser->profile_photo)
                            <img
                                src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                alt="{{ $currentUser->name }}"
                                class="object-cover w-full h-full"
                            >
                        @else
                            <div class="flex items-center justify-center w-full h-full font-bold text-white engineer-primary-gradient">
                                {{ mb_substr($currentUser->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </a>
            </div>
        </header>

        <main class="min-h-screen engineer-consultations-main lg:mr-64">
            <div class="px-4 pt-24 pb-12 mx-auto max-w-[1500px] sm:px-6 lg:px-8">
                {{-- الرسائل --}}
                @if (session('success'))
                    <div class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- العنوان --}}
                <section class="flex flex-col items-start justify-between gap-6 mb-10 md:flex-row md:items-end">
                    <div>
                        <div class="flex items-center gap-2 mb-2 text-xs">
                            <a href="{{ route('dashboard') }}" class="text-[#c3c6d7] transition hover:text-[#b4c5ff]">
                                لوحة القيادة
                            </a>

                            <span class="text-white/20">/</span>

                            <span class="font-bold text-[#b4c5ff]">
                                استشاراتي الهندسية
                            </span>
                        </div>

                        <h1 class="text-3xl font-black tracking-tight text-[#dae2fd]">
                            استشاراتي الهندسية
                        </h1>

                        <p class="mt-2 text-[#c3c6d7] opacity-80">
                            تابع المشاريع المعينة لك وارفع الملفات النهائية.
                        </p>
                    </div>

                    <button
                        id="toggleEngineerFilters"
                        type="button"
                        aria-controls="engineerFilterPanel"
                        aria-expanded="false"
                        class="flex items-center justify-center w-12 h-12 border rounded-xl border-white/10 bg-[#222a3d] text-[#dae2fd] transition hover:bg-[#31394d]"
                        title="التصفية"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/>
                        </svg>
                    </button>
                </section>

                {{-- الإحصائيات --}}
                <section class="grid grid-cols-1 gap-6 mb-12 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="relative p-6 overflow-hidden engineer-consultations-glass rounded-2xl">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-[#b4c5ff]/10 blur-2xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/20 text-[#b4c5ff]">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                                        <path d="M8 8h8M8 12h8M8 16h5"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="mb-1 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                إجمالي الاستشارات
                            </p>

                            <h3 class="text-4xl font-black text-[#dae2fd]">
                                {{ $totalConsultations }}
                            </h3>
                        </div>
                    </article>

                    <article class="engineer-consultations-glass relative overflow-hidden rounded-2xl border-r-4 border-r-[#d2bbff] p-6">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-[#d2bbff]/10 blur-2xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#d2bbff]/20 text-[#d2bbff]">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="mb-1 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                جاهزة للبدء
                            </p>

                            <h3 class="text-4xl font-black text-[#dae2fd]">
                                {{ $pendingConsultations }}
                            </h3>
                        </div>
                    </article>

                    <article class="engineer-consultations-glass relative overflow-hidden rounded-2xl border-r-4 border-r-[#ffb1c7] p-6">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-[#ffb1c7]/10 blur-2xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#ffb1c7]/20 text-[#ffb1c7]">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="3"/>
                                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="mb-1 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                قيد التنفيذ
                            </p>

                            <h3 class="text-4xl font-black text-[#dae2fd]">
                                {{ $inProgressConsultations }}
                            </h3>
                        </div>
                    </article>

                    <article class="engineer-consultations-glass relative overflow-hidden rounded-2xl border-r-4 border-r-[#2563eb] p-6">
                        <div class="absolute w-24 h-24 rounded-full -right-4 -top-4 bg-[#2563eb]/10 blur-2xl"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#2563eb]/20 text-[#b4c5ff]">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="m8 12 2.5 2.5L16 9"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="mb-1 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                مكتملة
                            </p>

                            <h3 class="text-4xl font-black text-[#dae2fd]">
                                {{ $completedConsultations }}
                            </h3>
                        </div>
                    </article>
                </section>

                {{-- فلتر الحالة --}}
                <section id="engineerFilterPanel" class="hidden p-5 mb-8 engineer-consultations-glass rounded-2xl">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="min-w-[220px] flex-1">
                            <label for="engineerStatusFilter" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                حالة الاستشارة
                            </label>

                            <select
                                id="engineerStatusFilter"
                                class="w-full rounded-xl border border-white/10 bg-[#131b2e] px-4 py-3 text-white focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">جميع الحالات</option>
                                <option value="pending">جاهزة للبدء</option>
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="completed">مكتملة</option>
                            </select>
                        </div>

                        <button
                            id="resetEngineerFilters"
                            type="button"
                            class="rounded-xl bg-[#2d3449] px-5 py-3 font-bold text-[#dae2fd] transition hover:bg-[#31394d]"
                        >
                            مسح الفلتر
                        </button>
                    </div>
                </section>

                {{-- عنوان القائمة --}}
                <div class="flex items-center justify-between px-4 mb-6">
                    <div class="flex items-center gap-4">
                        <h4 class="text-2xl font-bold text-[#dae2fd]">
                            الاستشارات الواردة
                        </h4>
                    </div>

                    <div id="engineerVisibleCount" class="text-sm text-[#c3c6d7]">
                        عرض {{ $totalConsultations }} استشارة
                    </div>
                </div>

                {{-- البطاقات --}}
                <section class="space-y-6">
                    @forelse ($consultations as $consultation)
                        @php
                            $searchText = strtolower(
                                ($consultation->consultation_number ?? '') . ' ' .
                                ($consultation->title ?? '') . ' ' .
                                ($consultation->description ?? '') . ' ' .
                                ($consultation->customer?->name ?? '') . ' ' .
                                ($consultation->customer?->email ?? '') . ' ' .
                                ($consultation->consultationType?->name ?? '')
                            );
                        @endphp

                        <article
                            data-engineer-consultation
                            data-status="{{ $consultation->status }}"
                            data-search="{{ $searchText }}"
                            class="p-6 transition-transform duration-300 engineer-consultations-glass rounded-3xl hover:-translate-y-1 lg:p-8"
                        >
                            <div class="flex flex-col gap-8 xl:flex-row">
                                {{-- ملخص العميل --}}
                                <div class="flex-shrink-0 xl:w-80 xl:border-l xl:border-white/5 xl:pl-8">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="flex items-center justify-center w-20 h-20 overflow-hidden border-2 rounded-2xl border-[#b4c5ff] bg-[#222a3d] shadow-2xl shadow-blue-500/20">
                                            @if ($consultation->customer?->profile_photo)
                                                <img
                                                    src="{{ asset('storage/' . $consultation->customer->profile_photo) }}"
                                                    alt="{{ $consultation->customer->name }}"
                                                    class="object-cover w-full h-full"
                                                >
                                            @else
                                                <span class="text-2xl font-black text-[#b4c5ff]">
                                                    {{ mb_substr($consultation->customer?->name ?? 'ع', 0, 1) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div>
                                            <h5 class="text-xl font-bold text-[#dae2fd]">
                                                {{ $consultation->customer?->name ?? 'عميل غير معروف' }}
                                            </h5>

                                            <p class="text-xs font-bold text-[#b4c5ff]">
                                                {{ $consultation->consultation_number }}
                                            </p>

                                            <div class="mt-2 inline-flex rounded-md border border-[#ffb1c7]/20 bg-[#be0062]/20 px-2 py-1 text-[10px] font-black text-[#ffb1c7]">
                                                {{ $consultation->consultationType?->name ?? 'استشارة هندسية' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="rounded-2xl border border-white/5 bg-[#131b2e] p-4">
                                            <p class="mb-2 text-[10px] font-bold uppercase text-[#c3c6d7]">
                                                الملفات والمرفقات
                                            </p>

                                            <div class="space-y-2">
                                                @if ($consultation->customer_file)
                                                    <a
                                                        href="{{ asset('storage/' . $consultation->customer_file) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center gap-2 text-xs text-[#dae2fd] transition hover:text-[#b4c5ff]"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M6 2h9l5 5v15H6z"/>
                                                            <path d="M14 2v6h6"/>
                                                        </svg>

                                                        تحميل ملف العميل
                                                    </a>
                                                @else
                                                    <p class="text-xs text-[#8d90a0]">
                                                        لا يوجد ملف للعميل.
                                                    </p>
                                                @endif

                                                @if (
                                                    $consultation->payment_status === 'paid'
                                                    && $consultation->engineer_id
                                                )
                                                    <a
                                                        href="{{ route('consultations.messages.index', $consultation) }}"
                                                        class="flex items-center gap-2 text-xs text-[#dae2fd] transition hover:text-[#b4c5ff]"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                                                        </svg>

                                                        فتح المحادثة
                                                    </a>
                                                @endif

                                                @if ($consultation->engineer_file)
                                                    <a
                                                        href="{{ asset('storage/' . $consultation->engineer_file) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center gap-2 text-xs text-[#dae2fd] transition hover:text-[#b4c5ff]"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>
                                                        </svg>

                                                        عرض الملف النهائي
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- تفاصيل الاستشارة --}}
                                <div class="flex-1">
                                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                                        <div>
                                            <div class="flex items-center gap-2 mb-4">
                                                <svg class="w-5 h-5 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <rect x="5" y="3" width="14" height="18" rx="2"/>
                                                    <path d="M8 8h8M8 12h8M8 16h5"/>
                                                </svg>

                                                <h6 class="text-xs font-bold tracking-widest uppercase text-[#c3c6d7]">
                                                    بيانات الاستشارة
                                                </h6>
                                            </div>

                                            <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                                                <div class="col-span-2">
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        عنوان الاستشارة
                                                    </p>

                                                    <p class="text-sm font-bold text-[#dae2fd]">
                                                        {{ $consultation->title }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        البريد الإلكتروني
                                                    </p>

                                                    <p class="text-sm text-[#dae2fd] break-all">
                                                        {{ $consultation->customer?->email ?? '—' }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        نوع الاستشارة
                                                    </p>

                                                    <p class="text-sm text-[#dae2fd]">
                                                        {{ $consultation->consultationType?->name ?? '—' }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        تاريخ الطلب
                                                    </p>

                                                    <p class="text-sm text-[#dae2fd]">
                                                        {{ $consultation->created_at?->format('Y-m-d') }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        حالة الدفع
                                                    </p>

                                                    <p class="text-sm font-bold {{ $consultation->payment_status === 'paid' ? 'text-green-300' : 'text-amber-300' }}">
                                                        {{ $consultation->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                                                    </p>
                                                </div>

                                                <div class="col-span-2">
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        وصف الاستشارة
                                                    </p>

                                                    <p class="text-sm leading-7 text-[#dae2fd]">
                                                        {{ \Illuminate\Support\Str::limit($consultation->description, 220) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="relative overflow-hidden rounded-3xl border border-[#b4c5ff]/10 bg-[#b4c5ff]/5 p-6">
                                            <div class="absolute top-0 left-0 w-24 h-24 rounded-full engineer-primary-gradient opacity-5 blur-3xl"></div>

                                            <div class="relative z-10 flex items-center gap-2 mb-4">
                                                <svg class="w-5 h-5 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <circle cx="12" cy="12" r="9"/>
                                                    <path d="m8 12 2.5 2.5L16 9"/>
                                                </svg>

                                                <h6 class="text-xs font-bold tracking-widest uppercase text-[#c3c6d7]">
                                                    حالة التنفيذ
                                                </h6>
                                            </div>

                                            <div class="relative z-10 grid grid-cols-2 gap-y-5">
                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        الحالة الحالية
                                                    </p>

                                                    @if ($consultation->status === 'pending')
                                                        <p class="text-sm font-bold text-amber-300">
                                                            جاهزة للبدء
                                                        </p>
                                                    @elseif ($consultation->status === 'in_progress')
                                                        <p class="text-sm font-bold text-blue-300">
                                                            قيد التنفيذ
                                                        </p>
                                                    @elseif ($consultation->status === 'completed')
                                                        <p class="text-sm font-bold text-green-300">
                                                            مكتملة
                                                        </p>
                                                    @else
                                                        <p class="text-sm font-bold text-[#dae2fd]">
                                                            {{ $consultation->status }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div>
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        السعر
                                                    </p>

                                                    <p class="text-sm font-bold text-[#b4c5ff]">
                                                        {{ number_format($consultation->final_price, 2) }}
                                                        ₪
                                                    </p>
                                                </div>

                                                <div class="col-span-2 pt-4 mt-2 border-t border-[#b4c5ff]/10">
                                                    <p class="mb-1 text-[10px] text-[#c3c6d7]">
                                                        آخر تحديث
                                                    </p>

                                                    <p class="text-sm text-[#b4c5ff]">
                                                        {{ $consultation->updated_at?->format('Y-m-d H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الإجراءات --}}
                                    <div class="flex flex-wrap items-center justify-between gap-4 pt-6 mt-8 border-t border-white/5">
                                        <p class="text-[11px] text-[#c3c6d7]">
                                            تابع العمل وارفع الملف النهائي عند الانتهاء.
                                        </p>

                                        <div class="flex flex-wrap gap-3">
                                            @if (
                                                $consultation->payment_status === 'paid'
                                                && $consultation->engineer_id
                                            )
                                                <a
                                                    href="{{ route('consultations.messages.index', $consultation) }}"
                                                    class="rounded-xl border border-[#b4c5ff]/30 px-6 py-2.5 text-xs font-bold text-[#b4c5ff] transition hover:bg-[#b4c5ff]/5"
                                                >
                                                    مراسلة العميل
                                                </a>
                                            @endif

                                            @if ($consultation->status !== 'completed')
                                                <button
                                                    type="button"
                                                    data-toggle-upload="{{ $consultation->id }}"
                                                    class="engineer-primary-gradient rounded-xl px-8 py-2.5 text-xs font-bold text-white transition hover:shadow-lg hover:shadow-blue-500/30 active:scale-95"
                                                >
                                                    رفع الملف النهائي
                                                </button>
                                            @elseif ($consultation->engineer_file)
                                                <a
                                                    href="{{ asset('storage/' . $consultation->engineer_file) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="rounded-xl bg-[#d2bbff] px-8 py-2.5 text-xs font-bold text-[#25005a] transition hover:shadow-lg hover:shadow-purple-500/20 active:scale-95"
                                                >
                                                    عرض الملف النهائي
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- رفع الملف --}}
                                    @if ($consultation->status !== 'completed')
                                        <div
                                            id="uploadPanel{{ $consultation->id }}"
                                            class="hidden pt-6 mt-6 border-t border-white/10"
                                        >
                                            <form
                                                method="POST"
                                                action="{{ route('consultations.upload-engineer-file', $consultation) }}"
                                                enctype="multipart/form-data"
                                                data-upload-form
                                            >
                                                @csrf

                                                <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
                                                    <label class="flex items-center gap-4 p-4 transition border-2 border-dashed cursor-pointer rounded-2xl border-white/10 bg-white/[0.03] hover:border-[#b4c5ff]/30">
                                                        <input
                                                            type="file"
                                                            name="engineer_file"
                                                            class="hidden"
                                                            required
                                                            accept=".pdf,.zip,.rar,.dwg,.jpg,.jpeg,.png"
                                                            data-file-input
                                                        >

                                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                                <path d="M6 2h9l5 5v15H6z"/>
                                                                <path d="M14 2v6h6M12 18v-7M9 14l3-3 3 3"/>
                                                            </svg>
                                                        </div>

                                                        <div>
                                                            <p class="font-bold text-white">
                                                                اختر الملف النهائي
                                                            </p>

                                                            <p class="mt-1 text-xs text-[#c3c6d7]">
                                                                PDF، ZIP، DWG أو صور المشروع
                                                            </p>

                                                            <p
                                                                class="hidden mt-2 text-sm font-bold text-[#b4c5ff]"
                                                                data-file-name
                                                            ></p>
                                                        </div>
                                                    </label>

                                                    <button
                                                        type="submit"
                                                        class="px-10 py-3 font-bold text-white transition engineer-primary-gradient rounded-xl hover:shadow-lg hover:shadow-blue-500/20"
                                                    >
                                                        إرسال الملف
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center p-14 engineer-consultations-glass rounded-3xl">
                            <div class="flex items-center justify-center w-20 h-20 mx-auto rounded-2xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M4 5h16v14H4z"/>
                                    <path d="m4 7 8 6 8-6"/>
                                </svg>
                            </div>

                            <h2 class="mt-5 text-2xl font-black text-white">
                                لا توجد استشارات معينة لك
                            </h2>

                            <p class="mt-3 text-[#c3c6d7]">
                                ستظهر هنا الاستشارات بعد تعيينها لك من الإدارة.
                            </p>
                        </div>
                    @endforelse
                </section>
            </div>
        </main>

        <div class="fixed inset-0 pointer-events-none -z-10">
            <div class="absolute top-[-10%] right-[-5%] h-[40%] w-[40%] rounded-full bg-[#b4c5ff]/5 blur-[120px]"></div>
            <div class="absolute bottom-[-10%] left-[-5%] h-[35%] w-[35%] rounded-full bg-[#d2bbff]/5 blur-[100px]"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput =
                document.getElementById(
                    'engineerConsultationSearch'
                );

            const statusFilter =
                document.getElementById(
                    'engineerStatusFilter'
                );

            const filterPanel =
                document.getElementById(
                    'engineerFilterPanel'
                );

            const toggleFilters =
                document.getElementById(
                    'toggleEngineerFilters'
                );

            const resetFilters =
                document.getElementById(
                    'resetEngineerFilters'
                );

            const visibleCount =
                document.getElementById(
                    'engineerVisibleCount'
                );

            const cards =
                Array.from(
                    document.querySelectorAll(
                        '[data-engineer-consultation]'
                    )
                );

            const applyFilters = () => {
                const query =
                    (searchInput?.value || '')
                        .trim()
                        .toLowerCase();

                const status =
                    statusFilter?.value || 'all';

                let count = 0;

                cards.forEach((card) => {
                    const matchesSearch =
                        query === ''
                        || (
                            card.dataset.search || ''
                        ).includes(query);

                    const matchesStatus =
                        status === 'all'
                        || card.dataset.status === status;

                    const visible =
                        matchesSearch
                        && matchesStatus;

                    card.classList.toggle(
                        'hidden',
                        !visible
                    );

                    if (visible) {
                        count++;
                    }
                });

                if (visibleCount) {
                    visibleCount.textContent =
                        `عرض ${count} استشارة`;
                }
            };

            searchInput?.addEventListener(
                'input',
                applyFilters
            );

            statusFilter?.addEventListener(
                'change',
                applyFilters
            );

            toggleFilters?.addEventListener(
                'click',
                function () {
                    filterPanel?.classList.toggle(
                        'hidden'
                    );

                    const isOpen =
                        filterPanel
                        && !filterPanel.classList.contains(
                            'hidden'
                        );

                    toggleFilters.setAttribute(
                        'aria-expanded',
                        isOpen ? 'true' : 'false'
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

                    applyFilters();
                }
            );

            document
                .querySelectorAll(
                    '[data-toggle-upload]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        function () {
                            const id =
                                button.dataset.toggleUpload;

                            const panel =
                                document.getElementById(
                                    `uploadPanel${id}`
                                );

                            panel?.classList.toggle(
                                'hidden'
                            );

                            if (
                                panel
                                && !panel.classList.contains(
                                    'hidden'
                                )
                            ) {
                                panel.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center',
                                });
                            }
                        }
                    );
                });

            document
                .querySelectorAll(
                    '[data-file-input]'
                )
                .forEach((input) => {
                    input.addEventListener(
                        'change',
                        function () {
                            const fileName =
                                input
                                    .closest('label')
                                    ?.querySelector(
                                        '[data-file-name]'
                                    );

                            if (!fileName) {
                                return;
                            }

                            const file =
                                input.files?.[0];

                            fileName.textContent =
                                file
                                    ? file.name
                                    : '';

                            fileName.classList.toggle(
                                'hidden',
                                !file
                            );
                        }
                    );
                });

            document
                .querySelectorAll(
                    '[data-upload-form]'
                )
                .forEach((form) => {
                    form.addEventListener(
                        'submit',
                        function () {
                            const button =
                                form.querySelector(
                                    'button[type="submit"]'
                                );

                            if (!button) {
                                return;
                            }

                            button.disabled = true;
                            button.textContent =
                                'جاري الرفع...';

                            button.classList.add(
                                'opacity-60',
                                'cursor-not-allowed'
                            );
                        }
                    );
                });
        });
    </script>
</x-app-layout>
