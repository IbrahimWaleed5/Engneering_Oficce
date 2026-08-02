<x-app-layout>
    @php
        $currentUser = auth()->user();

        $allTicketsCount = $tickets->total();

        $openTicketsCount = $tickets->getCollection()
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        $resolvedTicketsCount = $tickets->getCollection()
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $urgentTicketsCount = $tickets->getCollection()
            ->where('priority', 'urgent')
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
.support-employee-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background-color: #0b1326;
            background-image:
                linear-gradient(rgba(11, 19, 38, .94), rgba(11, 19, 38, .98)),
                radial-gradient(circle at 40% 15%, rgba(37, 99, 235, .12), transparent 35%);
            font-family: "Noto Sans Arabic", "Be Vietnam Pro", sans-serif;
        }

        .support-employee-page::before {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            content: "";
            opacity: .12;
            background-image:
                linear-gradient(rgba(180, 197, 255, .07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(180, 197, 255, .07) 1px, transparent 1px);
            background-size: 38px 38px;
        }

        .support-glass {
            border: 1px solid rgba(255, 255, 255, .06);
            background: rgba(23, 31, 51, .68);
            backdrop-filter: blur(14px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, .24);
        }

        .support-ticket-card {
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .support-ticket-card:hover {
            transform: translateY(-2px);
            border-color: rgba(37, 99, 235, .35);
            box-shadow: 0 0 24px rgba(37, 99, 235, .12);
        }

        .support-scroll::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        .support-scroll::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: #2d3449;
        }

        @media (max-width: 1023px) {
            .support-employee-sidebar {
                display: none !important;
            }

            .support-employee-main {
                margin-right: 0 !important;
            }

            .support-employee-topbar {
                right: 0 !important;
            }
        }

        @media (max-width: 640px) {
            .support-employee-main {
                padding: 5.5rem .75rem 2rem !important;
            }

            .support-employee-topbar {
                height: 4.5rem !important;
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .support-employee-hero {
                padding: 1.25rem !important;
            }

            .support-ticket-actions {
                width: 100% !important;
                flex-direction: column !important;
            }

            .support-ticket-actions > * {
                width: 100% !important;
            }
        }
    </style>

    <div class="support-employee-page"
        dir="rtl"
    >
        {{-- الشريط العلوي المخصص --}}
        <header class="support-employee-topbar fixed left-0 right-64 top-0 z-40 flex h-20 items-center justify-between border-b border-white/10 bg-[#0b1326]/85 px-6 backdrop-blur-xl">
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    id="support-mobile-menu-open"
                    class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-white/5 lg:hidden"
                    title="فتح القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="relative hidden sm:block">
                    <svg class="absolute w-5 h-5 -translate-y-1/2 right-4 top-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <form
                        method="GET"
                        action="{{ route('employee.support.index') }}"
                        class="relative"
                    >
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="بحث في التذاكر..."
                            class="w-72 rounded-full border border-white/10 bg-[#060e20] py-2.5 pr-11 pl-4 text-sm text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >

                        @if (request()->filled('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        @if (request()->filled('priority'))
                            <input type="hidden" name="priority" value="{{ request('priority') }}">
                        @endif
                    </form>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                    class="relative flex items-center justify-center w-10 h-10 transition rounded-full text-slate-300 hover:bg-white/5 hover:text-blue-300"
                    title="الإشعارات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                    <span class="absolute w-2 h-2 bg-pink-400 rounded-full right-2 top-2"></span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center justify-center w-10 h-10 transition rounded-full text-slate-300 hover:bg-white/5 hover:text-blue-300"
                    title="الإعدادات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                    </svg>
                </a>

                <div class="relative">
                    <button
                        type="button"
                        id="support-profile-menu-button"
                        class="flex items-center gap-3"
                    >
                        <div class="hidden text-left sm:block">
                            <p class="text-sm font-bold text-white">{{ $currentUser->name }}</p>
                            <p class="text-[10px] text-slate-400">موظف الدعم الفني</p>
                        </div>

                        <div class="flex items-center justify-center overflow-hidden font-black text-white border-2 rounded-full h-11 w-11 border-blue-500/30 bg-blue-500/10">
                            @if ($currentUser->profile_photo)
                                <img
                                    src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                    alt="{{ $currentUser->name }}"
                                    class="object-cover w-full h-full"
                                >
                            @else
                                {{ mb_substr($currentUser->name, 0, 1) }}
                            @endif
                        </div>
                    </button>

                    <div
                                                                                                                        id="support-profile-menu" class="absolute left-0 mt-3 hidden w-52 overflow-hidden rounded-xl border border-white/10 bg-[#131b2e] shadow-2xl"
                    >
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-white hover:bg-white/5">
                            الصفحة الشخصية
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-3 text-sm text-right text-red-300 hover:bg-red-500/10">
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- القائمة الجانبية --}}
        <aside class="support-employee-sidebar fixed inset-y-0 right-0 z-50 flex w-64 flex-col border-l border-white/10 bg-[#131b2e]/85 px-4 py-6 backdrop-blur-xl">
            <div class="flex flex-col items-center mb-10">
                <img
                    src="{{ asset('images/Mainlogo.png') }}"
                    alt="شعار المكتب"
                    class="object-contain w-16 h-16 mb-4 shadow-lg rounded-xl"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                <div style="display:none" class="items-center justify-center w-16 h-16 mb-4 text-2xl font-black text-blue-300 rounded-xl bg-blue-500/10">
                    و
                </div>

                <h1 class="text-center text-xl font-black text-[#b4c5ff]">
                    مكتب الوليد الهندسي
                </h1>

                <p class="mt-1 text-xs text-slate-400">
                    نظام إدارة الدعم
                </p>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-xl text-slate-300 hover:bg-white/5 hover:text-white"
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
                    href="{{ route('employee.support.index') }}"
                    class="flex items-center gap-3 px-4 py-3 font-bold text-blue-300 border-r-4 border-blue-500 rounded-xl bg-blue-500/20"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                    </svg>
                    تذاكر الدعم
                </a>

                <a
                    href="{{ route('support.index') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-xl text-slate-300 hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 12a8 8 0 0 1 16 0v5a3 3 0 0 1-3 3h-2v-7h5"/>
                        <path d="M4 12v5a3 3 0 0 0 3 3h2v-7H4"/>
                    </svg>
                    مركز الدعم
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-xl text-slate-300 hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                    </svg>
                    الإعدادات
                </a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-white/10">
                @csrf
                <button type="submit" class="flex items-center w-full gap-3 px-4 py-3 text-red-300 transition rounded-xl hover:bg-red-500/10">
                    تسجيل الخروج
                </button>
            </form>
        </aside>

        {{-- قائمة الجوال --}}
        <div
            id="support-mobile-backdrop" class="fixed inset-0 z-[90] hidden bg-black/70 lg:hidden"
        ></div>

        <aside
                        id="support-mobile-menu" class="fixed right-0 top-0 z-[100] hidden h-screen w-72 flex-col bg-[#0b1326] p-5 lg:hidden"
        >
            <div class="flex items-center justify-between">
                <h2 class="font-black text-white">لوحة الدعم الفني</h2>
                <button type="button" id="support-mobile-menu-close" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5">✕</button>
            </div>

            <nav class="mt-8 space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl bg-white/5">لوحة التحكم</a>
                <a href="{{ route('employee.support.index') }}" class="block px-4 py-3 text-blue-300 rounded-xl bg-blue-500/20">تذاكر الدعم</a>
                <a href="{{ route('support.index') }}" class="block px-4 py-3 rounded-xl bg-white/5">مركز الدعم</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="relative z-10 min-h-screen px-6 pb-12 support-employee-main pt-28 lg:mr-64">
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

                {{-- العنوان --}}
                <section class="flex flex-col items-start justify-between gap-5 p-8 border support-employee-hero rounded-3xl border-white/5 bg-gradient-to-l from-blue-600/20 to-transparent md:flex-row md:items-center">
                    <div>
                        <h1 class="text-3xl font-black text-[#b4c5ff] sm:text-4xl">
                            تذاكر الدعم الفني
                        </h1>

                        <p class="max-w-2xl mt-3 leading-7 text-slate-300">
                            مرحبًا بك في مركز إدارة الدعم. تابع التذاكر المسندة إليك،
                            افتح المحادثات، ورد على المستخدمين، وحدّث حالة كل طلب.
                        </p>
                    </div>

                    <a
                        href="{{ route('support.create') }}"
                        class="inline-flex items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-7 py-4 font-black text-white shadow-lg shadow-blue-500/20 transition hover:scale-[1.03]"
                    >
                        <span>＋</span>
                        إنشاء تذكرة جديدة
                    </a>
                </section>

                {{-- الإحصائيات --}}
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="flex items-center justify-between p-6 border-r-4 support-glass rounded-2xl border-r-blue-500">
                        <div>
                            <p class="text-xs font-bold tracking-wider uppercase text-slate-400">إجمالي التذاكر</p>
                            <h3 class="mt-2 text-3xl font-black text-white">{{ $allTicketsCount }}</h3>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 text-blue-300 rounded-xl bg-blue-500/10">📊</div>
                    </article>

                    <article class="flex items-center justify-between p-6 border-r-4 support-glass rounded-2xl border-r-pink-400">
                        <div>
                            <p class="text-xs font-bold tracking-wider uppercase text-slate-400">تذاكر مفتوحة</p>
                            <h3 class="mt-2 text-3xl font-black text-white">{{ $openTicketsCount }}</h3>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 text-pink-300 rounded-xl bg-pink-500/10">⏳</div>
                    </article>

                    <article class="flex items-center justify-between p-6 border-r-4 support-glass rounded-2xl border-r-green-500">
                        <div>
                            <p class="text-xs font-bold tracking-wider uppercase text-slate-400">تم حلها</p>
                            <h3 class="mt-2 text-3xl font-black text-white">{{ $resolvedTicketsCount }}</h3>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 text-green-300 rounded-xl bg-green-500/10">✅</div>
                    </article>

                    <article class="flex items-center justify-between p-6 border-r-4 support-glass rounded-2xl border-r-red-500">
                        <div>
                            <p class="text-xs font-bold tracking-wider uppercase text-slate-400">حالات عاجلة</p>
                            <h3 class="mt-2 text-3xl font-black text-red-300">{{ $urgentTicketsCount }}</h3>
                        </div>
                        <div class="flex items-center justify-center w-12 h-12 text-red-300 rounded-xl bg-red-500/10">⚠️</div>
                    </article>
                </section>

                {{-- البحث والفلاتر --}}
                <section class="space-y-4">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="text-2xl font-black text-white">
                            أحدث التذاكر
                        </h2>

                        <form
                            method="GET"
                            action="{{ route('employee.support.index') }}"
                            class="flex flex-wrap gap-3"
                        >
                            <input
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="بحث بالرقم أو العميل..."
                                class="rounded-xl border border-white/10 bg-[#222a3d] px-4 py-3 text-sm text-white placeholder:text-slate-500"
                            >

                            <select
                                name="status"
                                class="rounded-xl border border-white/10 bg-[#222a3d] px-4 py-3 text-sm text-white"
                            >
                                <option value="">كل الحالات</option>
                                <option value="open" @selected(request('status') === 'open')>مفتوحة</option>
                                <option value="in_progress" @selected(request('status') === 'in_progress')>قيد المعالجة</option>
                                <option value="resolved" @selected(request('status') === 'resolved')>محلولة</option>
                                <option value="closed" @selected(request('status') === 'closed')>مغلقة</option>
                            </select>

                            <select
                                name="priority"
                                class="rounded-xl border border-white/10 bg-[#222a3d] px-4 py-3 text-sm text-white"
                            >
                                <option value="">كل الأولويات</option>
                                <option value="urgent" @selected(request('priority') === 'urgent')>عاجلة جدًا</option>
                                <option value="high" @selected(request('priority') === 'high')>مرتفعة</option>
                                <option value="medium" @selected(request('priority') === 'medium')>متوسطة</option>
                                <option value="low" @selected(request('priority') === 'low')>منخفضة</option>
                            </select>

                            <button
                                type="submit"
                                class="px-5 py-3 text-sm font-black text-white bg-blue-600 rounded-xl hover:bg-blue-500"
                            >
                                تطبيق
                            </button>

                            <a
                                href="{{ route('employee.support.index') }}"
                                class="rounded-xl border border-white/10 bg-[#222a3d] px-5 py-3 text-sm font-bold text-slate-300 hover:bg-[#31394d]"
                            >
                                مسح
                            </a>
                        </form>
                    </div>

                    <div class="space-y-4">
                        @forelse ($tickets as $ticket)
                            <article
                                class="p-6 support-ticket-card support-glass rounded-2xl"
                            >
                                <div class="flex flex-col gap-6 md:flex-row md:items-center">
                                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-white/5 bg-[#2d3449]/60 text-center text-xs font-black text-blue-300">
                                        {{ \Illuminate\Support\Str::limit($ticket->ticket_number, 10, '') }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span class="rounded-md border px-2 py-1 text-[10px] font-black
                                                {{
                                                    $ticket->priority === 'urgent'
                                                        ? 'border-red-500/20 bg-red-500/10 text-red-300'
                                                        : ($ticket->priority === 'high'
                                                            ? 'border-orange-500/20 bg-orange-500/10 text-orange-300'
                                                            : ($ticket->priority === 'medium'
                                                                ? 'border-yellow-500/20 bg-yellow-500/10 text-yellow-300'
                                                                : 'border-slate-500/20 bg-slate-500/10 text-slate-300'))
                                                }}"
                                            >
                                                {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                                            </span>

                                            <span class="rounded-md border px-2 py-1 text-[10px] font-black
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
                                        </div>

                                        <h3 class="text-xl font-black text-white truncate">
                                            {{ $ticket->subject }}
                                        </h3>

                                        <div class="flex flex-wrap mt-4 text-sm gap-x-6 gap-y-2 text-slate-400">
                                            <span>👤 {{ $ticket->user?->name ?? 'مستخدم غير معروف' }}</span>
                                            <span>✉️ {{ $ticket->user?->email ?? '—' }}</span>
                                            <span>🕒 {{ optional($ticket->last_message_at)->diffForHumans() ?? $ticket->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 support-ticket-actions shrink-0">
                                        <a
                                            href="{{ route('support.show', $ticket) }}"
                                            class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 hover:bg-white/5"
                                        >
                                            فتح المحادثة
                                        </a>

                                        <a
                                            href="{{ route('support.show', $ticket) }}"
                                            class="inline-flex items-center justify-center gap-2 px-6 py-3 font-black text-white transition bg-blue-600 shadow-lg rounded-xl shadow-blue-500/10 hover:bg-blue-500"
                                        >
                                            عرض التفاصيل
                                            <span>←</span>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="text-center support-glass rounded-2xl p-14">
                                <div class="text-6xl">🎧</div>
                                <h3 class="mt-5 text-xl font-black text-white">لا توجد تذاكر مسندة إليك</h3>
                                <p class="mt-2 text-slate-400">ستظهر هنا التذاكر التي يعيّنها المدير لك.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($tickets->hasPages())
                        <div class="p-4 support-glass rounded-2xl">
                            {{ $tickets->withQueryString()->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>

        <a
            href="{{ route('dashboard') }}"
            class="fixed bottom-6 left-6 z-50 flex h-14 w-14 items-center justify-center rounded-full border border-blue-400/30 bg-[#31394d]/90 text-2xl text-blue-300 shadow-2xl backdrop-blur-md transition hover:scale-110"
            title="العودة إلى لوحة التحكم"
        >
            ⌂
        </a>
    </div>
</x-app-layout>
