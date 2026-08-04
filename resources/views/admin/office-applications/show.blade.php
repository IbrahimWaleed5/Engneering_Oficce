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
            : url('/admin/offices');

        $applicationsRoute = Route::has('admin.office-applications.index')
            ? route('admin.office-applications.index')
            : url('/admin/office-applications');

        $subscriptionsRoute = Route::has('admin.office-subscriptions.index')
            ? route('admin.office-subscriptions.index')
            : '#';

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $notificationsRoute = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardRoute;

        $statusData = match ($application->status) {
            'approved' => [
                'label' => 'مقبول',
                'class' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-200',
            ],

            'rejected' => [
                'label' => 'مرفوض',
                'class' => 'border-red-400/30 bg-red-500/10 text-red-200',
            ],

            'cancelled' => [
                'label' => 'ملغي',
                'class' => 'border-white/10 bg-white/5 text-slate-300',
            ],

            default => [
                'label' => 'قيد المراجعة',
                'class' => 'border-[#b4c5ff]/30 bg-[#2563eb]/15 text-[#b4c5ff]',
            ],
        };
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }

        body.admin-office-request-menu-open {
            overflow: hidden;
        }

        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800 {
            display: none !important;
        }

        .admin-office-request-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                linear-gradient(rgba(11,19,38,.91), rgba(11,19,38,.96)),
                radial-gradient(circle at 10% 15%, rgba(37,99,235,.20), transparent 30%),
                radial-gradient(circle at 88% 8%, rgba(131,67,244,.14), transparent 28%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .admin-office-request-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: .12;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(180,197,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(180,197,255,.06) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        .admin-office-request-glass {
            background: rgba(19,27,46,.46);
            border: 1px solid rgba(141,144,160,.12);
            box-shadow: 0 18px 55px rgba(0,0,0,.26);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .admin-office-request-input {
            width: 100%;
            border: 1px solid rgba(67,70,85,.62);
            border-radius: .75rem;
            background: rgba(6,14,32,.68);
            color: #dae2fd;
            padding: .78rem .9rem;
            outline: none;
            transition: .2s ease;
        }

        .admin-office-request-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
        }

        .admin-office-request-link {
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .admin-office-request-link:hover {
            transform: translateX(-2px);
        }

        .admin-office-request-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .admin-office-request-scroll::-webkit-scrollbar-track {
            background: rgba(11,19,38,.55);
        }

        .admin-office-request-scroll::-webkit-scrollbar-thumb {
            background: rgba(67,70,85,.74);
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .admin-office-request-sidebar,
            .admin-office-request-desktop-topbar {
                display: none !important;
            }

            .admin-office-request-main {
                margin-right: 0 !important;
                padding-top: 6.5rem !important;
            }
        }

        @media (max-width: 640px) {
            .admin-office-request-main {
                padding-left: .85rem !important;
                padding-right: .85rem !important;
            }

            .admin-office-request-card {
                padding: 1rem !important;
            }

            .admin-office-request-title {
                font-size: 1.75rem !important;
                line-height: 2.25rem !important;
            }
        }
    </style>

    <div
        class="admin-office-request-page"
        dir="rtl"
        x-data="{ mobileMenuOpen: false }"
        x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('admin-office-request-menu-open', value))"
        @keydown.escape.window="mobileMenuOpen = false"
    >
        <div class="admin-office-request-grid"></div>

        {{-- شريط الجوال --}}
        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-[#b4c5ff]/25 bg-[#2563eb] text-white"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="min-w-0 text-center">
                    <p class="truncate text-base font-black text-[#b4c5ff]">مكتب الوليد الهندسي</p>
                    <p class="truncate text-[11px] text-[#8d90a0]">تفاصيل طلب المكتب</p>
                </div>

                <a
                    href="{{ $notificationsRoute }}"
                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-[#c3c6d7]"
                    aria-label="الإشعارات"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

        {{-- قائمة الجوال --}}
        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed right-0 top-0 z-[90] flex h-dvh w-[min(88vw,390px)] flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden"
        >
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <div>
                    <h2 class="text-xl font-black text-[#b4c5ff]">مكتب الوليد الهندسي</h2>
                    <p class="mt-1 text-xs text-[#8d90a0]">لوحة إدارة النظام</p>
                </div>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center text-white border h-11 w-11 rounded-xl border-white/10 bg-white/5"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-5 space-y-2 overflow-y-auto admin-office-request-scroll">
                <a href="{{ $dashboardRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاستشارات</a>
                <a href="{{ $officesRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">المكاتب الهندسية</a>
                <a href="{{ $applicationsRoute }}" class="flex items-center gap-3 rounded-xl bg-[#2563eb] px-4 py-3 font-black text-white">طلبات إنشاء المكاتب</a>

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">اشتراكات المكاتب</a>
                @endif

                <a href="{{ $profileRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">الإعدادات</a>
            </nav>

            <div class="p-5 border-t border-white/10">
                <p class="font-black text-white">{{ $currentUser->name }}</p>
                <p class="mt-1 break-all text-xs text-[#8d90a0]">{{ $currentUser->email }}</p>
            </div>
        </aside>

        {{-- القائمة الجانبية --}}
        <aside class="admin-office-request-sidebar fixed right-0 top-0 z-50 hidden h-screen w-64 flex-col border-l border-white/10 bg-[#131b2e]/92 px-4 pb-6 pt-20 shadow-2xl backdrop-blur-xl lg:flex">
            <div class="px-4 mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2563eb] text-white">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-sm font-black text-[#b4c5ff]">مكتب الوليد</h2>
                        <p class="text-[10px] uppercase tracking-wider text-[#8d90a0]">Engineering Excellence</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto admin-office-request-scroll">
                <a href="{{ $dashboardRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-[#2d3449] hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    لوحة التحكم
                </a>

                <a href="{{ $consultationsRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-[#2d3449] hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>
                    </svg>
                    الاستشارات
                </a>

                <a href="{{ $officesRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-[#2d3449] hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                    </svg>
                    المكاتب الهندسية
                </a>

                <a href="{{ $applicationsRoute }}" class="flex items-center gap-3 rounded-xl bg-[#2563eb] px-4 py-3 text-sm font-black text-white shadow-[0_0_15px_rgba(37,99,235,.28)]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10a2 2 0 0 1 2 2v16H5V5a2 2 0 0 1 2-2ZM9 8h6M9 12h6M9 16h4"/>
                    </svg>
                    طلبات المكاتب
                </a>

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-[#2d3449] hover:text-white">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path d="M3 10h18M7 15h4"/>
                        </svg>
                        الاشتراكات
                    </a>
                @endif
            </nav>

            <div class="pt-5 mt-auto border-t border-white/5">
                <a href="{{ $profileRoute }}" class="admin-office-request-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-[#2d3449] hover:text-white">
                    الإعدادات
                </a>
            </div>
        </aside>

        {{-- الشريط العلوي --}}
        <header class="admin-office-request-desktop-topbar fixed left-0 right-64 top-0 z-40 hidden h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/82 px-6 backdrop-blur-xl lg:flex">
            <div class="flex items-center gap-3">
                <h1 class="text-lg font-black text-[#b4c5ff]">مكتب الوليد الهندسي</h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $notificationsRoute }}" class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-white/5 hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                    </svg>
                </a>

                <a href="{{ $profileRoute }}" class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-white/10 bg-white/5 font-black text-[#b4c5ff]">
                    {{ mb_substr($currentUser->name ?? 'م', 0, 1) }}
                </a>
            </div>
        </header>

        {{-- المحتوى --}}
        <main class="relative z-10 min-h-screen px-4 pt-24 pb-16 admin-office-request-main lg:mr-64 lg:px-6">
            <div class="max-w-6xl mx-auto space-y-8">
                @if (session('success'))
                    <div class="p-4 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-5 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- رأس الصفحة --}}
                <header class="flex flex-col gap-5 pb-5 border-b border-white/10 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="admin-office-request-title text-3xl font-black tracking-tight text-[#b4c5ff] sm:text-4xl">
                            طلب مكتب هندسي
                        </h1>

                        <div class="mt-3 flex flex-wrap items-center gap-3 text-[#c3c6d7]">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V8l6-4 6 4v13"/>
                                </svg>
                                {{ $application->office_name }}
                            </span>

                            <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black {{ $statusData['class'] }}">
                                {{ $statusData['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="hidden h-24 w-24 items-center justify-center rounded-xl border border-white/10 bg-[#171f33]/70 text-[#b4c5ff] sm:flex">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                        </svg>
                    </div>
                </header>

                {{-- البيانات الأساسية --}}
                <section class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    @php
                        $metaItems = [
                            ['label' => 'رقم الهاتف', 'value' => $application->phone ?: 'غير محدد', 'type' => 'phone'],
                            ['label' => 'البريد الإلكتروني', 'value' => $application->email, 'type' => 'mail'],
                            ['label' => 'رقم الترخيص', 'value' => $application->license_number, 'type' => 'license'],
                            ['label' => 'السجل التجاري', 'value' => $application->commercial_registration, 'type' => 'register'],
                            ['label' => 'الدولة', 'value' => $application->country ?: 'غير محددة', 'type' => 'country'],
                            ['label' => 'المدينة', 'value' => $application->city ?: 'غير محددة', 'type' => 'city'],
                            ['label' => 'تاريخ التقديم', 'value' => $application->created_at?->format('Y-m-d H:i'), 'type' => 'date'],
                            ['label' => 'آخر تحديث', 'value' => $application->updated_at?->format('Y-m-d H:i'), 'type' => 'update'],
                        ];
                    @endphp

                    @foreach ($metaItems as $item)
                        <article class="admin-office-request-glass rounded-xl p-5 transition hover:bg-[#2d3449]/40">
                            <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-wider text-[#8d90a0]">
                                <svg class="h-4 w-4 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    @switch($item['type'])
                                        @case('phone')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h3l1.5 4-2 1.5a15 15 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2C10.7 19 5 13.3 5 6a3 3 0 0 1 2-3Z"/>
                                            @break
                                        @case('mail')
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <path d="m3 7 9 6 9-6"/>
                                            @break
                                        @case('license')
                                            <rect x="5" y="4" width="14" height="16" rx="2"/>
                                            <path d="M9 8h6M9 12h6M9 16h4"/>
                                            @break
                                        @case('register')
                                            <path d="M4 21h16M6 21V8l6-4 6 4v13"/>
                                            @break
                                        @case('country')
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                                            @break
                                        @case('city')
                                            <path d="M4 21V8l6-4v17M10 10l6-3v14M16 12h4v9"/>
                                            @break
                                        @case('date')
                                            <rect x="4" y="5" width="16" height="15" rx="2"/>
                                            <path d="M8 3v4m8-4v4M4 10h16"/>
                                            @break
                                        @default
                                            <path d="M3 12a9 9 0 1 0 3-6.7L3 8"/>
                                            <path d="M3 4v4h4M12 7v5l3 2"/>
                                    @endswitch
                                </svg>

                                {{ $item['label'] }}
                            </div>

                            <p class="mt-2 font-bold text-white break-words">
                                {{ $item['value'] }}
                            </p>
                        </article>
                    @endforeach
                </section>

                {{-- العنوان والملاحظات --}}
                <section class="space-y-4">
                    <article class="relative p-5 overflow-hidden admin-office-request-glass rounded-xl">
                        <div class="pointer-events-none absolute inset-y-0 right-0 w-1/3 bg-gradient-to-l from-[#2563eb]/10 to-transparent"></div>

                        <h2 class="relative flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#b4c5ff]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 6l7-3 4 2 7-3v16l-7 3-4-2-7 3V6Z"/>
                                <path d="M10 3v16M14 5v16"/>
                            </svg>
                            عنوان المكتب
                        </h2>

                        <p class="relative mt-3 leading-8 text-white">
                            {{ $application->address }}
                        </p>
                    </article>

                    @if ($application->notes)
                        <article class="p-5 admin-office-request-glass rounded-xl">
                            <h2 class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#b4c5ff]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M6 3h9l4 4v14H6V3Z"/>
                                    <path d="M14 3v5h5M9 12h6M9 16h6"/>
                                </svg>
                                نبذة المكتب والملاحظات
                            </h2>

                            <p class="mt-3 leading-8 text-[#c3c6d7]">
                                {{ $application->notes }}
                            </p>
                        </article>
                    @endif
                </section>

                {{-- المرفقات --}}
                <section>
                    <h2 class="flex items-center gap-2 mb-4 text-xl font-black text-white">
                        <svg class="h-6 w-6 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 7h7l2 2h9v10H3V7Z"/>
                        </svg>
                        المرفقات
                    </h2>

                    <div class="grid gap-4 md:grid-cols-3">
                        <article class="flex items-center gap-4 p-4 admin-office-request-glass rounded-xl">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#2d3449] text-[#b4c5ff]">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M6 3h9l4 4v14H6V3Z"/>
                                    <path d="M14 3v5h5"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="font-bold text-white">ملف السجل التجاري</h3>
                                <p class="mt-1 text-xs text-[#8d90a0]">مرفق مع الطلب</p>
                            </div>
                        </article>

                        <article class="flex items-center gap-4 p-4 admin-office-request-glass rounded-xl">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#2d3449] text-[#b4c5ff]">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"/>
                                    <path d="m8.5 12 2.2 2.2 4.8-5"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="font-bold text-white">ملف ترخيص المكتب</h3>
                                <p class="mt-1 text-xs text-[#8d90a0]">مرفق مع الطلب</p>
                            </div>
                        </article>

                        <article class="flex items-center gap-4 p-4 admin-office-request-glass rounded-xl">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#2d3449] text-[#b4c5ff]">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="M3 10h18M7 15h4"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="font-bold text-white">إيصال الدفع</h3>
                                <p class="mt-1 text-xs text-[#8d90a0]">
                                    {{ $application->payment_reference ?: 'بدون رقم مرجعي' }}
                                </p>
                            </div>
                        </article>
                    </div>
                </section>

                {{-- إجراءات القرار --}}
                @if ($application->status === 'pending')
                    <section class="grid gap-6 pt-6 border-t border-white/10 lg:grid-cols-2">
                        {{-- الرفض --}}
                        <form
                            method="POST"
                            action="{{ route('admin.office-applications.review', $application) }}"
                            class="relative p-5 overflow-hidden border admin-office-request-glass rounded-2xl border-red-500/25"
                        >
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="decision" value="reject">

                            <div class="absolute inset-y-0 right-0 w-1 bg-red-400"></div>

                            <h2 class="flex items-center gap-2 text-xl font-black text-red-300">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m9 9 6 6m0-6-6 6"/>
                                </svg>
                                رفض الطلب
                            </h2>

                            <label for="rejection_reason" class="mt-5 block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                سبب الرفض
                            </label>

                            <textarea
                                id="rejection_reason"
                                name="rejection_reason"
                                rows="5"
                                required
                                class="mt-2 resize-none admin-office-request-input"
                                placeholder="اكتب سبب رفض طلب المكتب..."
                            >{{ old('rejection_reason') }}</textarea>

                            <button
                                type="submit"
                                onclick="return confirm('هل أنت متأكد من رفض الطلب؟')"
                                class="flex items-center justify-center w-full gap-2 px-5 py-3 mt-5 font-black text-red-200 transition border border-red-400 rounded-xl hover:bg-red-500/10"
                            >
                                رفض الطلب
                            </button>
                        </form>

                        {{-- القبول --}}
                        <form
                            method="POST"
                            action="{{ route('admin.office-applications.review', $application) }}"
                            class="admin-office-request-glass relative overflow-hidden rounded-2xl border border-[#2563eb]/30 p-5 shadow-[0_0_20px_rgba(37,99,235,.18)]"
                        >
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="decision" value="approve">

                            <div class="absolute inset-y-0 right-0 w-1 bg-[#2563eb]"></div>

                            <h2 class="flex items-center gap-2 text-xl font-black text-[#dbe1ff]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m8 12 2.5 2.5L16.5 8.5"/>
                                </svg>
                                قبول المكتب
                            </h2>

                            <div class="grid gap-4 mt-5 sm:grid-cols-2">
                                <div>
                                    <label for="subscription_currency" class="block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                        العملة
                                    </label>

                                    <select
                                        id="subscription_currency"
                                        name="subscription_currency"
                                        required
                                        class="mt-2 admin-office-request-input"
                                    >
                                        @foreach (['USD', 'SAR', 'ILS', 'JOD', 'EUR'] as $currency)
                                            <option value="{{ $currency }}" @selected(old('subscription_currency', 'USD') === $currency)>
                                                {{ $currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="subscription_amount" class="block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                        قيمة الاشتراك
                                    </label>

                                    <input
                                        id="subscription_amount"
                                        name="subscription_amount"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('subscription_amount', 300) }}"
                                        required
                                        class="mt-2 admin-office-request-input"
                                    >
                                </div>

                                <div>
                                    <label for="duration_unit" class="block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                        وحدة المدة
                                    </label>

                                    <select
                                        id="duration_unit"
                                        name="duration_unit"
                                        required
                                        class="mt-2 admin-office-request-input"
                                    >
                                        <option value="month" @selected(old('duration_unit', 'month') === 'month')>شهر</option>
                                        <option value="year" @selected(old('duration_unit') === 'year')>سنة</option>
                                        <option value="day" @selected(old('duration_unit') === 'day')>يوم</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="duration_value" class="block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                        مقدار المدة
                                    </label>

                                    <input
                                        id="duration_value"
                                        name="duration_value"
                                        type="number"
                                        min="1"
                                        max="120"
                                        value="{{ old('duration_value', 1) }}"
                                        required
                                        class="mt-2 admin-office-request-input"
                                    >
                                </div>
                            </div>

                            <label for="subscription_notes" class="mt-4 block text-xs font-black uppercase tracking-wider text-[#c3c6d7]">
                                ملاحظات الاعتماد
                            </label>

                            <textarea
                                id="subscription_notes"
                                name="subscription_notes"
                                rows="3"
                                class="mt-2 resize-none admin-office-request-input"
                                placeholder="ملاحظات داخلية اختيارية..."
                            >{{ old('subscription_notes') }}</textarea>

                            <button
                                type="submit"
                                onclick="return confirm('هل أنت متأكد من قبول المكتب وإنشائه؟')"
                                class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-[#2563eb] to-[#0053db] px-5 py-3 font-black text-white shadow-[0_0_20px_rgba(37,99,235,.35)] transition hover:brightness-110"
                            >
                                قبول وإنشاء المكتب
                            </button>
                        </form>
                    </section>
                @endif

                @if ($application->status === 'rejected' && $application->rejection_reason)
                    <section class="p-5 border admin-office-request-glass rounded-xl border-red-500/20 bg-red-500/10">
                        <h2 class="font-black text-red-200">سبب رفض الطلب</h2>
                        <p class="mt-2 leading-8 text-red-100">{{ $application->rejection_reason }}</p>
                    </section>
                @endif

                @if ($application->reviewer)
                    <section class="p-5 admin-office-request-glass rounded-xl">
                        <p class="text-xs text-[#8d90a0]">تمت مراجعة الطلب بواسطة</p>
                        <p class="mt-2 font-black text-white">{{ $application->reviewer->name }}</p>

                        @if ($application->reviewed_at)
                            <p class="mt-1 text-sm text-[#8d90a0]">{{ $application->reviewed_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </section>
                @endif

                <div>
                    <a
                        href="{{ $applicationsRoute }}"
                        class="inline-flex items-center gap-2 px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                        </svg>
                        العودة إلى جميع الطلبات
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
