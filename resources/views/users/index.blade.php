<x-app-layout>
    @php
        $currentUser = auth()->user();

        $roles = [
            'admin' => [
                'label' => 'مدير',
                'class' => 'bg-[#d2bbff]/10 text-[#d2bbff] border-[#d2bbff]/20',
            ],
            'engineer' => [
                'label' => 'مهندس',
                'class' => 'bg-[#b4c5ff]/10 text-[#b4c5ff] border-[#b4c5ff]/20',
            ],
            'employee' => [
                'label' => 'موظف',
                'class' => 'bg-amber-500/10 text-amber-300 border-amber-400/20',
            ],
            'customer' => [
                'label' => 'عميل',
                'class' => 'bg-[#ffb1c7]/10 text-[#ffb1c7] border-[#ffb1c7]/20',
            ],
        ];
    @endphp

    <style>
        .users-design-page {
            --surface: #0b1326;
            --surface-lowest: #060e20;
            --surface-low: #131b2e;
            --surface-container: #171f33;
            --surface-high: #222a3d;
            --surface-highest: #2d3449;
            --primary: #b4c5ff;
            --primary-container: #2563eb;
            --secondary: #ffb1c7;
            --secondary-container: #be0062;
            --tertiary: #d2bbff;
            --tertiary-container: #8343f4;
            --text: #dae2fd;
            --muted: #c3c6d7;
            --outline: #8d90a0;
            --outline-variant: #434655;
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--text);
            background: var(--surface);
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .users-glass-card {
            background: rgba(23, 31, 51, .4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .users-neon-hover {
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }

        .users-neon-hover:hover {
            border-color: rgba(180, 197, 255, .4);
            box-shadow: 0 0 15px -5px rgba(180, 197, 255, .2);
            transform: translateY(-2px);
        }

        .users-custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .users-custom-scrollbar::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .users-custom-scrollbar::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }

        .users-status-active {
            box-shadow: 0 0 12px rgba(34, 197, 94, .22);
        }

        .users-row {
            transition: background-color .2s ease, transform .2s ease;
        }

        .users-row:hover {
            transform: scale(1.002);
        }

        @media (max-width: 1023px) {
            .users-desktop-sidebar {
                display: none !important;
            }

            .users-main {
                margin-right: 0 !important;
            }

            .users-topbar {
                right: 0 !important;
                width: 100% !important;
            }
        }
    </style>

    <div class="users-design-page" dir="rtl">
        <div class="fixed inset-0 pointer-events-none -z-10 opacity-30">
            <div class="absolute w-96 h-96 rounded-full -top-40 -right-32 bg-blue-600/20 blur-[130px]"></div>
            <div class="absolute rounded-full -bottom-40 -left-32 w-96 h-96 bg-purple-600/20 blur-[140px]"></div>
        </div>

        {{-- القائمة الجانبية --}}
        <aside class="users-desktop-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#171f33]/80 px-4 py-6 shadow-lg backdrop-blur-xl">
            <div class="flex items-center gap-3 px-2 mb-10">
                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-tr from-[#b4c5ff] to-[#d2bbff] rounded-lg">
                    <svg class="w-6 h-6 text-[#00174b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 20h16M6 20V9l6-5 6 5v11M9 20v-6h6v6"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-xl font-bold leading-tight text-[#b4c5ff]">
                        CreativeHome
                    </h1>

                    <p class="text-[11px] text-[#c3c6d7]">
                        Engineering Office
                    </p>
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 px-4 py-3 text-[#c3c6d7] transition rounded-xl hover:bg-white/5 hover:text-[#b4c5ff]"
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
                    href="{{ route('users.index') }}"
                    class="flex items-center gap-4 rounded-xl border-r-2 border-[#b4c5ff] bg-[#2563eb]/10 px-4 py-3 text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>المستخدمون</span>
                </a>

                <a
                    href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                    class="flex items-center gap-4 px-4 py-3 text-[#c3c6d7] transition rounded-xl hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>الاستشارات</span>
                </a>

                <a
                    href="{{ Route::has('payments.index') ? route('payments.index') : route('dashboard') }}"
                    class="flex items-center gap-4 px-4 py-3 text-[#c3c6d7] transition rounded-xl hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>

                    <span>الدفعات</span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-4 px-4 py-3 text-[#c3c6d7] transition rounded-xl hover:bg-white/5 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-6 mt-auto border-t border-[#434655]/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-4 px-4 py-3 text-red-300 transition rounded-xl hover:bg-red-500/10"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>
                        </svg>

                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-h-screen users-main lg:mr-64">
            {{-- الشريط العلوي --}}
            <header class="users-topbar fixed left-0 right-64 top-0 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#0b1326]/50 px-6 backdrop-blur-md">
                <form
                    method="GET"
                    action="{{ route('users.index') }}"
                    class="hidden w-full max-w-md md:block"
                >
                    <div class="relative">
                        <svg class="absolute w-5 h-5 -translate-y-1/2 right-3 top-1/2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="ابحث عن العملاء أو المهندسين..."
                            class="w-full rounded-xl border border-[#434655]/20 bg-[#131b2e] py-2.5 pr-10 pl-4 text-sm text-white placeholder:text-[#c3c6d7]/60 focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                        >

                        @if (request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif

                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                    </div>
                </form>

                <div class="flex items-center gap-6 mr-auto">
                    <div class="flex items-center gap-2">
                        <a
                            href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                            class="flex items-center justify-center w-10 h-10 rounded-full text-[#c3c6d7] hover:bg-white/5"
                            title="الإشعارات"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                <path d="M10 21h4"/>
                            </svg>
                        </a>

                        <a
                            href="{{ route('dashboard') }}"
                            class="flex items-center justify-center w-10 h-10 rounded-full text-[#c3c6d7] hover:bg-white/5"
                            title="التطبيقات"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <rect x="4" y="4" width="5" height="5" rx="1"/>
                                <rect x="15" y="4" width="5" height="5" rx="1"/>
                                <rect x="4" y="15" width="5" height="5" rx="1"/>
                                <rect x="15" y="15" width="5" height="5" rx="1"/>
                            </svg>
                        </a>
                    </div>

                    <div class="w-px h-8 bg-[#434655]/20"></div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-bold text-[#dae2fd]">
                                {{ $currentUser->name }}
                            </p>

                            <p class="text-[10px] text-[#c3c6d7]">
                                لوحة تحكم المدير
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
                                <div class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-purple-600">
                                    {{ mb_substr($currentUser->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="px-4 pt-24 pb-10 mx-auto space-y-8 max-w-[1600px] sm:px-6 lg:px-8">
                {{-- رسائل النظام --}}
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

                {{-- عنوان الصفحة --}}
                <section class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                    <div class="space-y-1">
                        <nav class="flex items-center gap-2 mb-2 text-[11px] text-[#c3c6d7]">
                            <a href="{{ route('dashboard') }}" class="hover:text-[#b4c5ff]">
                                لوحة التحكم
                            </a>

                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>

                            <span class="text-[#b4c5ff]">
                                إدارة المستخدمين
                            </span>
                        </nav>

                        <h2 class="text-3xl font-black text-[#dae2fd]">
                            إدارة المستخدمين
                        </h2>

                        <p class="text-[#c3c6d7] opacity-80">
                            متابعة وإدارة جميع حسابات المهندسين والعملاء والموظفين.
                        </p>
                    </div>

                    <a
                        href="{{ route('users.create') }}"
                        class="flex items-center gap-2 px-6 py-3 text-xs font-bold text-white transition-all shadow-lg rounded-xl bg-gradient-to-r from-[#ff007a] to-[#8e44ad] hover:shadow-purple-500/20 active:scale-95"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        إضافة مستخدم جديد
                    </a>
                </section>

                {{-- الإحصائيات --}}
                <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article class="flex items-center gap-5 p-5 users-glass-card users-neon-hover rounded-2xl">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="9" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#c3c6d7]">
                                جميع المستخدمين
                            </p>

                            <p class="mt-1 text-3xl font-black leading-none text-white">
                                {{ $statistics['all'] }}
                            </p>
                        </div>
                    </article>

                    <article class="flex items-center gap-5 p-5 users-glass-card users-neon-hover rounded-2xl">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#d2bbff]/10 text-[#d2bbff]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="7" r="3"/>
                                <path d="M5 21a7 7 0 0 1 14 0M8 12h8M9 3h6"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#c3c6d7]">
                                المهندسون
                            </p>

                            <p class="mt-1 text-3xl font-black leading-none text-white">
                                {{ $statistics['engineers'] }}
                            </p>
                        </div>
                    </article>

                    <article class="flex items-center gap-5 p-5 users-glass-card users-neon-hover rounded-2xl">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#be0062]/10 text-[#ffb1c7]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21a8 8 0 0 1 16 0"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#c3c6d7]">
                                العملاء
                            </p>

                            <p class="mt-1 text-3xl font-black leading-none text-white">
                                {{ $statistics['customers'] }}
                            </p>
                        </div>
                    </article>

                    <article class="flex items-center gap-5 p-5 users-glass-card users-neon-hover rounded-2xl">
                        <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-[#8d90a0]/10 text-[#8d90a0]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m7 7 10 10"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-[#c3c6d7]">
                                الحسابات غير النشطة
                            </p>

                            <p class="mt-1 text-3xl font-black leading-none text-white">
                                {{ $statistics['inactive'] }}
                            </p>
                        </div>
                    </article>
                </section>

                {{-- البحث والفلاتر --}}
                <section class="p-6 space-y-6 users-glass-card rounded-2xl">
                    <form
                        method="GET"
                        action="{{ route('users.index') }}"
                        class="grid items-end grid-cols-1 gap-4 lg:grid-cols-12"
                    >
                        <div class="space-y-2 lg:col-span-5">
                            <label for="search" class="px-1 text-xs font-bold text-[#dae2fd]/70">
                                البحث
                            </label>

                            <div class="relative">
                                <input
                                    id="search"
                                    name="search"
                                    type="text"
                                    value="{{ request('search') }}"
                                    placeholder="الاسم، البريد الإلكتروني، أو رقم الهاتف..."
                                    class="w-full rounded-xl border border-[#434655]/20 bg-[#131b2e] py-3 pr-11 pl-4 text-right text-sm text-white placeholder:text-[#c3c6d7]/50 focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                                >

                                <svg class="absolute w-5 h-5 -translate-y-1/2 right-4 top-1/2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <circle cx="10" cy="8" r="3"/>
                                    <path d="M4 20a6 6 0 0 1 12 0M17 11l4 4M21 11l-4 4"/>
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label for="role" class="px-1 text-xs font-bold text-[#dae2fd]/70">
                                الدور
                            </label>

                            <select
                                id="role"
                                name="role"
                                class="w-full rounded-xl border border-[#434655]/20 bg-[#131b2e] px-4 py-3 text-sm text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع الأدوار</option>
                                <option value="engineer" @selected(request('role') === 'engineer')>مهندس</option>
                                <option value="customer" @selected(request('role') === 'customer')>عميل</option>
                                <option value="employee" @selected(request('role') === 'employee')>موظف</option>
                                <option value="admin" @selected(request('role') === 'admin')>مدير</option>
                            </select>
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label for="status" class="px-1 text-xs font-bold text-[#dae2fd]/70">
                                الحالة
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-xl border border-[#434655]/20 bg-[#131b2e] px-4 py-3 text-sm text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع الحالات</option>
                                <option value="active" @selected(request('status') === 'active')>نشط</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                            </select>
                        </div>

                        <div class="flex gap-3 lg:col-span-3">
                            <button
                                type="submit"
                                class="flex-1 rounded-xl bg-[#b4c5ff] py-3 text-xs font-bold text-[#002a78] transition hover:brightness-110 active:scale-95"
                            >
                                تطبيق البحث
                            </button>

                            <a
                                href="{{ route('users.index') }}"
                                class="rounded-xl bg-[#2d3449]/30 px-5 py-3 text-xs font-bold text-[#c3c6d7] transition hover:bg-[#2d3449]/50"
                            >
                                مسح
                            </a>
                        </div>
                    </form>
                </section>

                {{-- جدول المستخدمين --}}
                <section class="overflow-hidden users-glass-card rounded-2xl">
                    <div class="overflow-x-auto users-custom-scrollbar">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-[#2d3449]/30">
                                    <th class="px-6 py-5 text-xs font-bold text-[#c3c6d7] border-b border-[#434655]/10">
                                        المستخدم
                                    </th>

                                    <th class="px-6 py-5 text-xs font-bold text-[#c3c6d7] border-b border-[#434655]/10">
                                        الهاتف
                                    </th>

                                    <th class="px-6 py-5 text-xs font-bold text-center text-[#c3c6d7] border-b border-[#434655]/10">
                                        الدور
                                    </th>

                                    <th class="px-6 py-5 text-xs font-bold text-center text-[#c3c6d7] border-b border-[#434655]/10">
                                        الحالة
                                    </th>

                                    <th class="px-6 py-5 text-xs font-bold text-[#c3c6d7] border-b border-[#434655]/10">
                                        تاريخ التسجيل
                                    </th>

                                    <th class="px-6 py-5 text-xs font-bold text-center text-[#c3c6d7] border-b border-[#434655]/10">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#434655]/10">
                                @forelse ($users as $user)
                                    @php
                                        $role = $roles[$user->role] ?? [
                                            'label' => $user->role,
                                            'class' => 'bg-slate-500/10 text-slate-300 border-slate-400/20',
                                        ];
                                    @endphp

                                    <tr class="users-row hover:bg-[#2563eb]/5 {{ $user->id === auth()->id() ? 'bg-[#2563eb]/5' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 overflow-hidden rounded-full {{ $user->id === auth()->id() ? 'border-2 border-[#b4c5ff]' : '' }}">
                                                    @if ($user->profile_photo)
                                                        <img
                                                            src="{{ asset('storage/' . $user->profile_photo) }}"
                                                            alt="{{ $user->name }}"
                                                            class="object-cover w-full h-full"
                                                        >
                                                    @else
                                                        <div class="flex items-center justify-center w-full h-full font-bold text-white bg-[#8343f4]">
                                                            {{ mb_substr($user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <p class="text-xs font-bold text-[#dae2fd]">
                                                        {{ $user->name }}

                                                        @if ($user->id === auth()->id())
                                                            <span class="text-[#b4c5ff]">(أنت)</span>
                                                        @endif
                                                    </p>

                                                    @if (
                                                        $user->role === 'engineer'
                                                        && $user->employeeProfile?->specialty
                                                    )
                                                        <p class="mt-1 text-[11px] text-[#b4c5ff]">
                                                            {{ $user->employeeProfile->specialty->name }}
                                                        </p>
                                                    @else
                                                        <p class="mt-1 text-[11px] text-[#c3c6d7]">
                                                            {{ $user->email }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm font-medium text-[#dae2fd]">
                                            {{ $user->phone ?: '—' }}
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex px-3 py-1 text-[11px] font-bold border rounded-full {{ $role['class'] }}">
                                                {{ $role['label'] }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if ($user->status === 'active')
                                                <span class="users-status-active inline-flex items-center gap-1.5 rounded-full border border-green-500/20 bg-green-500/10 px-3 py-1 text-[11px] font-bold text-green-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                                    نشط
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-[11px] font-bold text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                    غير نشط
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-sm text-[#c3c6d7]">
                                            {{ $user->created_at?->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                @if (
                                                    $user->id !== auth()->id()
                                                    && in_array(
                                                        $user->role,
                                                        ['engineer', 'customer', 'employee'],
                                                        true
                                                    )
                                                )
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.conversations.start', $user) }}"
                                                        data-submit-once
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="flex items-center justify-center w-8 h-8 text-green-400 transition rounded-lg bg-green-500/10 hover:bg-green-500/20"
                                                            title="محادثة"
                                                        >
                                                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                                <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <a
                                                    href="{{ route('users.edit', $user) }}"
                                                    class="flex items-center justify-center w-8 h-8 text-[#b4c5ff] transition rounded-lg bg-[#b4c5ff]/10 hover:bg-[#b4c5ff]/20"
                                                    title="{{ $user->id === auth()->id() ? 'تعديل الملف الشخصي' : 'تعديل' }}"
                                                >
                                                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="m14 4 6 6L8 22H2v-6L14 4Z"/>
                                                        <path d="m12 6 6 6"/>
                                                    </svg>
                                                </a>

                                                @if ($user->id !== auth()->id())
                                                    <form
                                                        method="POST"
                                                        action="{{ route('users.destroy', $user) }}"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                                        data-submit-once
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="flex items-center justify-center w-8 h-8 text-red-300 transition rounded-lg bg-red-500/10 hover:bg-red-500/20"
                                                            title="حذف"
                                                        >
                                                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                                <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-14 text-center text-[#c3c6d7]">
                                            لا يوجد مستخدمون مطابقون للبحث.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- الترقيم --}}
                    @if ($users->hasPages())
                        <div class="flex items-center justify-between gap-4 px-6 py-4 border-t border-[#434655]/10 bg-[#2d3449]/20">
                            <p class="text-[11px] text-[#c3c6d7]">
                                عرض
                                {{ $users->firstItem() }}
                                -
                                {{ $users->lastItem() }}
                                من أصل
                                {{ $users->total() }}
                                مستخدم
                            </p>

                            <div>
                                {{ $users->onEachSide(1)->links() }}
                            </div>
                        </div>
                    @else
                        <div class="px-6 py-4 border-t border-[#434655]/10 bg-[#2d3449]/20">
                            <p class="text-[11px] text-[#c3c6d7]">
                                عرض {{ $users->count() }} من أصل {{ $users->count() }} مستخدم
                            </p>
                        </div>
                    @endif
                </section>
            </div>

            <footer class="p-6 mt-10 text-center border-t border-[#434655]/10">
                <p class="text-[11px] text-[#c3c6d7] opacity-50">
                    © {{ now()->year }} CreativeHome Engineering Office. جميع الحقوق محفوظة.
                </p>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document
                .querySelectorAll('[data-submit-once]')
                .forEach((form) => {
                    form.addEventListener('submit', function () {
                        const button =
                            form.querySelector(
                                'button[type="submit"]'
                            );

                        if (!button) {
                            return;
                        }

                        button.disabled = true;
                        button.classList.add(
                            'opacity-50',
                            'cursor-not-allowed'
                        );
                    });
                });
        });
    </script>

</x-app-layout>
