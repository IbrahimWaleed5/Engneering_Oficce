<x-app-layout>
    @php
        $currentUser = auth()->user();

        $dashboardRoute = Route::has('office.dashboard')
            ? route('office.dashboard')
            : route('dashboard');

        $publicOfficeRoute = Route::has('engineering-offices.show')
            ? route('engineering-offices.show', $office)
            : $dashboardRoute;

        $subscriptionRoute = Route::has('office.subscription')
            ? route('office.subscription')
            : $dashboardRoute;

        $membersRoute = Route::has('office.members.index')
            ? route('office.members.index')
            : $dashboardRoute;

        $consultationsRoute = Route::has('office.consultations.index')
            ? route('office.consultations.index')
            : $dashboardRoute;

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : $dashboardRoute;

        $officeProfileRoute = Route::has('office.profile.edit')
            ? route('office.profile.edit')
            : (Route::has('office.profile')
                ? route('office.profile')
                : url('/office/profile'));
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        body.office-profile-menu-open {
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

        .office-profile-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                linear-gradient(rgba(11,19,38,.92), rgba(11,19,38,.97)),
                radial-gradient(circle at 11% 12%, rgba(37,99,235,.18), transparent 32%),
                radial-gradient(circle at 90% 8%, rgba(131,67,244,.13), transparent 28%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .office-profile-blueprint {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: .12;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(180,197,255,.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(180,197,255,.055) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        .office-profile-glass {
            background: linear-gradient(145deg, rgba(23,31,51,.72), rgba(11,19,38,.82));
            border: 1px solid rgba(180,197,255,.14);
            box-shadow: 0 8px 32px rgba(0,0,0,.30), inset 0 1px 0 rgba(255,255,255,.04);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .office-profile-sidebar {
            background: linear-gradient(180deg, rgba(19,27,46,.97), rgba(11,19,38,.97));
            border-left: 1px solid rgba(180,197,255,.10);
            box-shadow: -10px 0 30px rgba(0,0,0,.45);
            backdrop-filter: blur(16px);
        }

        .office-profile-input {
            width: 100%;
            border: 1px solid rgba(180,197,255,.20);
            border-radius: .85rem;
            background: rgba(11,19,38,.62);
            color: #dae2fd;
            padding: .9rem 1rem;
            outline: none;
            transition: all .25s ease;
        }

        .office-profile-input:focus {
            border-color: #b4c5ff;
            box-shadow: 0 0 15px rgba(37,99,235,.35), inset 0 0 8px rgba(37,99,235,.16);
            background: rgba(23,31,51,.80);
        }

        .office-profile-upload {
            border: 2px dashed rgba(141,144,160,.42);
            background: rgba(23,31,51,.24);
            transition: all .25s ease;
        }

        .office-profile-upload:hover {
            border-color: #b4c5ff;
            background: rgba(180,197,255,.05);
        }

        .office-profile-nav-link {
            position: relative;
            overflow: hidden;
            transition: .25s ease;
        }

        .office-profile-nav-link::before {
            content: "";
            position: absolute;
            inset-block: 0;
            right: 0;
            width: 3px;
            background: #b4c5ff;
            transform: scaleY(0);
            transform-origin: top;
            transition: transform .25s ease;
        }

        .office-profile-nav-link:hover::before,
        .office-profile-nav-link.active::before {
            transform: scaleY(1);
        }

        .office-profile-gradient-text {
            background: linear-gradient(to left, #b4c5ff, #d2bbff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .office-profile-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .office-profile-scroll::-webkit-scrollbar-track {
            background: rgba(11,19,38,.5);
        }

        .office-profile-scroll::-webkit-scrollbar-thumb {
            background: rgba(180,197,255,.2);
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .office-profile-desktop-sidebar {
                display: none !important;
            }

            .office-profile-main {
                margin-right: 0 !important;
                padding-top: 6rem !important;
            }
        }

        @media (max-width: 640px) {
            .office-profile-main {
                padding-left: .85rem !important;
                padding-right: .85rem !important;
            }

            .office-profile-card {
                padding: 1rem !important;
            }

            .office-profile-title {
                font-size: 1.8rem !important;
                line-height: 2.3rem !important;
            }

            .office-profile-actions {
                position: sticky;
                bottom: 0;
                z-index: 30;
                margin-inline: -.85rem;
                padding: 1rem;
                border-top: 1px solid rgba(180,197,255,.12);
                background: rgba(11,19,38,.94);
                backdrop-filter: blur(18px);
            }
        }
    </style>

    <div
        class="office-profile-page"
        dir="rtl"
        x-data="{
            mobileMenuOpen: false,
            logoName: '',
            coverName: '',
            submitting: false
        }"
        x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('office-profile-menu-open', value))"
        @keydown.escape.window="mobileMenuOpen = false"
    >
        <div class="office-profile-blueprint"></div>

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
                    <p class="text-lg font-black truncate office-profile-gradient-text">
                        مكتب الوليد
                    </p>
                    <p class="truncate text-[11px] text-[#8d90a0]">
                        الملف الشخصي للمكتب
                    </p>
                </div>

                <a
                    href="{{ $profileRoute }}"
                    class="flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 font-black text-[#b4c5ff]"
                    aria-label="الملف الشخصي"
                >
                    {{ mb_substr($currentUser->name ?? 'م', 0, 1) }}
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
                    <h2 class="text-2xl font-black office-profile-gradient-text">مكتب الوليد</h2>
                    <p class="mt-1 text-xs text-[#8d90a0]">Engineering Excellence</p>
                </div>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center text-white border h-11 w-11 rounded-xl border-white/10 bg-white/5"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-5 space-y-2 overflow-y-auto office-profile-scroll">
                <a href="{{ $dashboardRoute }}" class="office-profile-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">لوحة المكتب</a>
                <a href="{{ $consultationsRoute }}" class="office-profile-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاستشارات</a>
                <a href="{{ $membersRoute }}" class="office-profile-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">فريق العمل</a>
                <a href="{{ $officeProfileRoute }}" class="office-profile-nav-link active flex items-center gap-3 rounded-xl bg-[#2563eb]/25 px-4 py-3 font-black text-[#dbe1ff]">الملف الشخصي</a>
                <a href="{{ $subscriptionRoute }}" class="office-profile-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاشتراك</a>
            </nav>
        </aside>

        {{-- القائمة الجانبية --}}
        <aside class="fixed top-0 right-0 z-50 flex-col hidden h-screen px-6 py-8 office-profile-sidebar office-profile-desktop-sidebar w-72 lg:flex">
            <div class="flex flex-col items-center pb-8 mb-10 border-b border-white/10">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-[#b4c5ff]/25 bg-[#171f33] text-[#b4c5ff] shadow-[0_0_22px_rgba(37,99,235,.18)]">
                    @if ($office->logo_path)
                        <img
                            src="{{ asset('storage/' . $office->logo_path) }}"
                            alt="{{ $office->name }}"
                            class="object-cover w-full h-full rounded-2xl"
                        >
                    @else
                        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                        </svg>
                    @endif
                </div>

                <h1 class="text-lg font-black text-center text-white">{{ $office->name }}</h1>
                <p class="mt-2 text-[10px] uppercase tracking-[.2em] text-[#b4c5ff]">Engineering Excellence</p>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto office-profile-scroll">
                <a href="{{ $dashboardRoute }}" class="office-profile-nav-link flex items-center gap-4 rounded-xl p-4 text-[#c3c6d7] hover:bg-[#2d3449]/60 hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    لوحة المكتب
                </a>

                <a href="{{ $consultationsRoute }}" class="office-profile-nav-link flex items-center gap-4 rounded-xl p-4 text-[#c3c6d7] hover:bg-[#2d3449]/60 hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>
                    </svg>
                    الاستشارات
                </a>

                <a href="{{ $membersRoute }}" class="office-profile-nav-link flex items-center gap-4 rounded-xl p-4 text-[#c3c6d7] hover:bg-[#2d3449]/60 hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20c0-4 2.5-6 6-6s6 2 6 6M14 15c3 0 5 1.5 5 5"/>
                    </svg>
                    فريق العمل
                </a>

                <a href="{{ $officeProfileRoute }}" class="office-profile-nav-link active flex items-center gap-4 rounded-xl border border-[#b4c5ff]/25 bg-gradient-to-l from-[#2563eb]/65 to-[#2563eb]/15 p-4 font-black text-white shadow-[0_0_20px_rgba(37,99,235,.18)]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 21h16M6 21V8l6-4 6 4v13"/>
                    </svg>
                    الملف الشخصي
                </a>

                <a href="{{ $subscriptionRoute }}" class="office-profile-nav-link flex items-center gap-4 rounded-xl p-4 text-[#c3c6d7] hover:bg-[#2d3449]/60 hover:text-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>
                    الاشتراك
                </a>
            </nav>

            <div class="pt-6 mt-auto border-t border-white/10">
                <a href="{{ $profileRoute }}" class="flex items-center justify-center rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] transition hover:bg-white/5 hover:text-white">
                    إعدادات الحساب
                </a>
            </div>
        </aside>

        {{-- المحتوى --}}
        <main class="relative z-10 min-h-screen px-4 pt-12 pb-16 office-profile-main lg:mr-72 lg:px-10">
            <div class="mx-auto max-w-7xl">
                @if (session('success'))
                    <div class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <h2 class="mb-3 font-black">توجد أخطاء في البيانات:</h2>
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-col gap-6 mb-10 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[.2em] text-[#b4c5ff]">إعدادات المكتب</p>
                        <h1 class="mt-3 text-4xl font-black text-white office-profile-title">الملف الشخصي للمكتب</h1>
                        <p class="mt-3 max-w-2xl leading-8 text-[#c3c6d7]">
                            إعدادات المكتب والبيانات الأساسية التي تظهر للعملاء في صفحة المكتب العامة.
                            تأكد من تحديث بياناتك بانتظام.
                        </p>
                    </div>

                    <div class="flex w-full gap-3 md:w-auto">
                        <a href="{{ $publicOfficeRoute }}" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-5 py-3 text-center font-black text-[#c3c6d7] transition hover:border-[#b4c5ff]/40 hover:text-white md:flex-none">
                            عرض صفحة المكتب
                        </a>

                        <a href="{{ $dashboardRoute }}" class="flex-1 rounded-xl border border-white/10 bg-[#222a3d] px-5 py-3 text-center font-black text-white transition hover:bg-[#31394d] md:flex-none">
                            لوحة المكتب
                        </a>
                    </div>
                </div>

                {{-- ملخص المكتب --}}
                <section class="flex flex-col items-center gap-8 p-8 mb-10 overflow-hidden office-profile-glass rounded-2xl md:flex-row md:items-start">
                    <div class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[#b4c5ff]/30 bg-[#171f33] p-3 shadow-[0_0_30px_rgba(37,99,235,.15)]">
                        @if ($office->logo_path)
                            <img src="{{ asset('storage/' . $office->logo_path) }}" alt="{{ $office->name }}" class="object-contain w-full h-full">
                        @else
                            <svg class="h-16 w-16 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1 text-center md:text-right">
                        <div class="flex flex-wrap items-center justify-center gap-3 md:justify-start">
                            <h2 class="text-2xl font-black text-white">{{ $office->name }}</h2>

                            @if ($office->status === 'active')
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#b4c5ff]/20 bg-[#b4c5ff]/10 px-3 py-1 text-xs font-black text-[#b4c5ff]">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="m8 12 2.5 2.5L16.5 8.5"/>
                                    </svg>
                                    موثق
                                </span>
                            @endif
                        </div>

                        <p class="mt-3 text-[#c3c6d7]">
                            ترخيص رقم: {{ $office->license_number ?: 'غير محدد' }}
                            —
                            {{ $office->city ?: 'غير محددة' }}، {{ $office->country ?: 'غير محددة' }}
                        </p>

                        <div class="grid grid-cols-2 gap-4 mt-6 md:grid-cols-4">
                            <div class="p-4 border rounded-xl border-white/10 bg-black/10">
                                <div class="text-xs text-[#8d90a0]">حالة المكتب</div>
                                <div class="mt-2 text-lg font-black text-[#b4c5ff]">
                                    {{ match ($office->status) {
                                        'active' => 'فعال',
                                        'suspended' => 'موقوف',
                                        'closed' => 'مغلق',
                                        default => 'قيد المراجعة',
                                    } }}
                                </div>
                            </div>

                            <div class="p-4 border rounded-xl border-white/10 bg-black/10">
                                <div class="text-xs text-[#8d90a0]">حالة الاشتراك</div>
                                <div class="mt-2 text-lg font-black text-[#b4c5ff]">
                                    {{ match ($office->subscription_status) {
                                        'active' => 'فعال',
                                        'pending' => 'قيد المراجعة',
                                        'expired' => 'منتهي',
                                        default => 'غير مفعل',
                                    } }}
                                </div>
                            </div>

                            <div class="p-4 border rounded-xl border-white/10 bg-black/10">
                                <div class="text-xs text-[#8d90a0]">المدينة</div>
                                <div class="mt-2 text-lg font-black text-[#b4c5ff]">{{ $office->city ?: '—' }}</div>
                            </div>

                            <div class="p-4 border rounded-xl border-white/10 bg-black/10">
                                <div class="text-xs text-[#8d90a0]">العملة</div>
                                <div class="mt-2 text-lg font-black text-[#b4c5ff]">{{ $office->subscription_currency ?: 'USD' }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                @if ($office->status === 'suspended')
                    <section class="p-6 mb-8 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <h2 class="text-xl font-black text-red-200">المكتب موقوف عن العمل</h2>
                        <p class="mt-3 leading-8 text-red-100">
                            تستطيع تعديل بيانات المكتب، لكن المكتب لا يستقبل طلبات انضمام أو استشارات جديدة أثناء الإيقاف.
                        </p>

                        @if ($office->suspension_reason)
                            <div class="p-4 mt-4 border rounded-xl border-red-500/20 bg-red-950/20">
                                <p class="text-sm font-black text-red-200">سبب الإيقاف</p>
                                <p class="mt-2 leading-7 text-red-100">{{ $office->suspension_reason }}</p>
                            </div>
                        @endif
                    </section>
                @endif

                <form
                    method="POST"
                    action="{{ route('office.profile.update') }}"
                    enctype="multipart/form-data"
                    class="space-y-10"
                    @submit="submitting = true"
                >
                    @csrf
                    @method('PATCH')

                    {{-- الصور --}}
                    <section class="p-6 office-profile-glass office-profile-card rounded-2xl">
                        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-white/10">
                            <svg class="h-8 w-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <circle cx="9" cy="10" r="2"/>
                                <path d="m5 18 5-5 3 3 2-2 4 4"/>
                            </svg>
                            <h2 class="text-2xl font-black text-white">الصور التعريفية</h2>
                        </div>

                        <div class="grid gap-8 md:grid-cols-3">
                            <div>
                                <label class="mb-4 block text-sm font-black text-[#c3c6d7]">شعار المكتب</label>

                                <label
                                    for="logo"
                                    class="relative flex flex-col items-center justify-center h-56 p-6 overflow-hidden text-center cursor-pointer office-profile-upload rounded-2xl"
                                >
                                    @if ($office->logo_path)
                                        <img src="{{ asset('storage/' . $office->logo_path) }}" alt="{{ $office->name }}" class="absolute inset-0 object-contain w-full h-full p-6 opacity-55">
                                    @endif

                                    <div class="relative z-10 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 bg-[#222a3d] text-[#b4c5ff] shadow-lg">
                                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M12 16V4M7 9l5-5 5 5M4 20h16"/>
                                        </svg>
                                    </div>

                                    <span class="relative z-10 mt-4 font-black text-white">تحديث الشعار</span>
                                    <span class="relative z-10 mt-2 text-xs text-[#8d90a0]">JPG أو PNG أو WEBP</span>
                                    <span x-cloak x-show="logoName" x-text="logoName" class="relative z-10 mt-3 rounded-lg bg-[#b4c5ff]/10 px-3 py-2 text-xs font-black text-[#b4c5ff]"></span>
                                </label>

                                <input
                                    id="logo"
                                    name="logo"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="hidden"
                                    @change="logoName = $event.target.files[0]?.name || ''"
                                >

                                @if ($office->logo_path)
                                    <label class="flex items-center gap-2 mt-3 text-sm text-red-200">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-[#434655] bg-[#0b1326] text-red-500 focus:ring-red-500">
                                        حذف الشعار الحالي
                                    </label>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-4 block text-sm font-black text-[#c3c6d7]">صورة الغلاف</label>

                                <label
                                    for="cover"
                                    class="relative flex flex-col items-center justify-center h-56 overflow-hidden text-center cursor-pointer office-profile-upload rounded-2xl"
                                >
                                    @if ($office->cover_path)
                                        <img src="{{ asset('storage/' . $office->cover_path) }}" alt="{{ $office->name }}" class="absolute inset-0 object-cover w-full h-full opacity-45">
                                    @endif

                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326]/90 via-[#0b1326]/40 to-transparent"></div>

                                    <div class="relative z-10 flex flex-col items-center rounded-xl border border-white/10 bg-[#0b1326]/55 px-6 py-4 backdrop-blur-md">
                                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                                            <path d="m5 18 5-5 3 3 2-2 4 4"/>
                                        </svg>

                                        <span class="mt-2 font-black text-white">تغيير صورة الغلاف</span>
                                        <span x-cloak x-show="coverName" x-text="coverName" class="mt-3 rounded-lg bg-[#b4c5ff]/10 px-3 py-2 text-xs font-black text-[#b4c5ff]"></span>
                                    </div>
                                </label>

                                <input
                                    id="cover"
                                    name="cover"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="hidden"
                                    @change="coverName = $event.target.files[0]?.name || ''"
                                >

                                @if ($office->cover_path)
                                    <label class="flex items-center gap-2 mt-3 text-sm text-red-200">
                                        <input type="checkbox" name="remove_cover" value="1" class="rounded border-[#434655] bg-[#0b1326] text-red-500 focus:ring-red-500">
                                        حذف صورة الغلاف الحالية
                                    </label>
                                @endif
                            </div>
                        </div>
                    </section>

                    {{-- البيانات الأساسية --}}
                    <section class="p-6 office-profile-glass office-profile-card rounded-2xl">
                        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-white/10">
                            <svg class="h-8 w-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M4 21h16M6 21V8l6-4 6 4v13"/>
                            </svg>
                            <h2 class="text-2xl font-black text-white">البيانات الأساسية</h2>
                        </div>

                        <div class="grid gap-x-8 gap-y-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="name" class="mb-2 block text-sm font-black text-[#c3c6d7]">اسم المكتب</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $office->name) }}" required maxlength="200" class="office-profile-input">
                            </div>

                            <div>
                                <label for="phone" class="mb-2 block text-sm font-black text-[#c3c6d7]">رقم الهاتف</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone', $office->phone) }}" maxlength="30" dir="ltr" class="text-left office-profile-input">
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-sm font-black text-[#c3c6d7]">البريد الإلكتروني</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $office->email) }}" required maxlength="255" dir="ltr" class="text-left office-profile-input">
                            </div>

                            <div>
                                <label for="commercial_registration" class="mb-2 block text-sm font-black text-[#c3c6d7]">رقم السجل التجاري</label>
                                <input id="commercial_registration" name="commercial_registration" type="text" value="{{ old('commercial_registration', $office->commercial_registration) }}" maxlength="100" class="office-profile-input">
                            </div>

                            <div>
                                <label for="license_number" class="mb-2 block text-sm font-black text-[#c3c6d7]">رقم الترخيص</label>
                                <input id="license_number" name="license_number" type="text" value="{{ old('license_number', $office->license_number) }}" maxlength="100" class="office-profile-input">
                            </div>
                        </div>
                    </section>

                    {{-- الموقع --}}
                    <section class="p-6 office-profile-glass office-profile-card rounded-2xl">
                        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-white/10">
                            <svg class="h-8 w-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/>
                                <circle cx="12" cy="9" r="2"/>
                            </svg>
                            <h2 class="text-2xl font-black text-white">الموقع والعنوان</h2>
                        </div>

                        <div class="grid gap-x-8 gap-y-6 md:grid-cols-2">
                            <div>
                                <label for="country" class="mb-2 block text-sm font-black text-[#c3c6d7]">الدولة</label>
                                <input id="country" name="country" type="text" value="{{ old('country', $office->country) }}" maxlength="100" class="office-profile-input">
                            </div>

                            <div>
                                <label for="city" class="mb-2 block text-sm font-black text-[#c3c6d7]">المدينة</label>
                                <input id="city" name="city" type="text" value="{{ old('city', $office->city) }}" maxlength="100" class="office-profile-input">
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="mb-2 block text-sm font-black text-[#c3c6d7]">العنوان التفصيلي</label>
                                <textarea id="address" name="address" rows="4" maxlength="1000" class="resize-none office-profile-input">{{ old('address', $office->address) }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- النبذة --}}
                    <section class="p-6 office-profile-glass office-profile-card rounded-2xl">
                        <div class="flex items-center gap-3 pb-6 mb-8 border-b border-white/10">
                            <svg class="h-8 w-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 11v5M12 8h.01"/>
                            </svg>
                            <div>
                                <h2 class="text-2xl font-black text-white">نبذة عن المكتب</h2>
                                <p class="mt-2 text-sm leading-7 text-[#8d90a0]">اكتب تعريفًا واضحًا عن خدمات المكتب وخبراته وتخصصاته الهندسية.</p>
                            </div>
                        </div>

                        <textarea id="description" name="description" rows="8" maxlength="5000" placeholder="اكتب نبذة تعريفية عن المكتب..." class="leading-8 resize-y office-profile-input">{{ old('description', $office->description) }}</textarea>
                    </section>

                    {{-- الأزرار --}}
                    <div class="flex flex-col justify-end gap-4 office-profile-actions sm:flex-row">
                        <a href="{{ $dashboardRoute }}" class="rounded-xl border border-white/10 bg-white/5 px-8 py-4 text-center font-black text-[#c3c6d7] transition hover:bg-white/10 hover:text-white">
                            إلغاء
                        </a>

                        <button
                            type="submit"
                            :disabled="submitting"
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-[#b4c5ff] to-[#0053db] px-8 py-4 font-black text-[#00174b] shadow-[0_0_20px_rgba(37,99,235,.38)] transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 4h11l3 3v13H5V4Z"/>
                                <path d="M8 4v6h8V4M9 20v-6h6v6"/>
                            </svg>

                            <span x-show="!submitting">حفظ التغييرات</span>
                            <span x-cloak x-show="submitting">جارٍ الحفظ...</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>
