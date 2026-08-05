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

        .office-avatar-editor {
            position: relative;
            width: min(78vw, 520px);
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 50%;
            background:
                linear-gradient(45deg, #172033 25%, transparent 25%),
                linear-gradient(-45deg, #172033 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #172033 75%),
                linear-gradient(-45deg, transparent 75%, #172033 75%),
                #0b1326;
            background-size: 24px 24px;
            background-position: 0 0, 0 12px, 12px -12px, -12px 0;
            border: 4px solid rgba(180,197,255,.35);
            box-shadow: 0 0 0 9999px rgba(0,0,0,.42);
            cursor: grab;
            touch-action: none;
            user-select: none;
        }

        .office-avatar-editor:active {
            cursor: grabbing;
        }

        .office-avatar-editor img {
            position: absolute;
            max-width: none;
            pointer-events: none;
            user-select: none;
            transform-origin: center center;
        }

        .office-avatar-modal {
            background: rgba(2,6,23,.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .office-avatar-range {
            accent-color: #4d8eff;
        }

    </style>

    <div
        class="office-profile-page"
        dir="rtl"
        x-data="{
            mobileMenuOpen: false,
            logoName: '',
            coverName: '',
            avatarEditorOpen: false,
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
                                <label class="mb-4 block text-sm font-black text-[#c3c6d7]">
                                    صورة الملف الشخصي للمكتب
                                </label>

                                <div class="relative mx-auto flex h-56 w-56 items-center justify-center">
                                    <div class="h-56 w-56 overflow-hidden rounded-full border-4 border-[#b4c5ff]/30 bg-[#171f33] shadow-[0_0_35px_rgba(37,99,235,.20)]">
                                        @if ($office->logo_path)
                                            <img
                                                id="officeAvatarCurrentPreview"
                                                src="{{ asset('storage/' . $office->logo_path) }}"
                                                alt="{{ $office->name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <div id="officeAvatarEmptyPreview" class="flex h-full w-full items-center justify-center text-[#b4c5ff]">
                                                <svg class="h-20 w-20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <path d="M4 21h16M6 21V8l6-4 6 4v13M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/>
                                                </svg>
                                            </div>
                                            <img
                                                id="officeAvatarCurrentPreview"
                                                src=""
                                                alt=""
                                                class="hidden h-full w-full object-cover"
                                            >
                                        @endif
                                    </div>

                                    <button
                                        type="button"
                                        id="openOfficeAvatarEditor"
                                        class="absolute bottom-2 left-2 flex h-12 w-12 items-center justify-center rounded-full border-4 border-[#131b2e] bg-[#4d8eff] text-[#00285d] shadow-xl transition hover:scale-105 hover:brightness-110"
                                        aria-label="تعديل صورة المكتب"
                                        title="تعديل صورة المكتب"
                                    >
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="mt-4 text-center text-sm leading-7 text-[#8d90a0]">
                                    اختر الصورة ثم حرّكها وكبّرها وحدد الجزء الذي سيظهر، مثل نظام فيسبوك.
                                </p>

                                <input
                                    id="logoSourceInput"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="hidden"
                                >

                                <input
                                    id="logo"
                                    name="logo"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="hidden"
                                >

                                @if ($office->logo_path)
                                    <label class="mt-4 flex items-center justify-center gap-2 text-sm text-red-200">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-[#434655] bg-[#0b1326] text-red-500 focus:ring-red-500">
                                        حذف الصورة الحالية
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

        {{-- محرر صورة الملف الشخصي مثل فيسبوك --}}
        <div
            id="officeAvatarEditorModal"
            class="office-avatar-modal fixed inset-0 z-[120] hidden items-center justify-center overflow-y-auto p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="officeAvatarEditorTitle"
        >
            <div class="w-full max-w-2xl rounded-3xl border border-[#b4c5ff]/20 bg-[#131b2e] p-5 shadow-2xl sm:p-7">
                <div class="mb-6 flex items-center justify-between gap-4 border-b border-white/10 pb-5">
                    <div>
                        <h2 id="officeAvatarEditorTitle" class="text-2xl font-black text-white">
                            تعديل صورة الملف الشخصي
                        </h2>
                        <p class="mt-2 text-sm text-[#8d90a0]">
                            اسحب الصورة لتغيير مكانها، واستخدم شريط التكبير لتحديد الجزء الظاهر.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="closeOfficeAvatarEditor"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white transition hover:bg-white/10"
                        aria-label="إغلاق"
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <div class="flex justify-center overflow-hidden py-3">
                    <div id="officeAvatarEditor" class="office-avatar-editor">
                        <img id="officeAvatarEditorImage" src="" alt="معاينة صورة المكتب">
                    </div>
                </div>

                <div class="mt-7">
                    <div class="mb-2 flex items-center justify-between text-sm font-bold text-[#c3c6d7]">
                        <span>تصغير</span>
                        <span>تكبير</span>
                    </div>

                    <input
                        id="officeAvatarZoom"
                        type="range"
                        min="1"
                        max="3"
                        step="0.01"
                        value="1"
                        class="office-avatar-range h-2 w-full cursor-pointer"
                    >
                </div>

                <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        id="cancelOfficeAvatarEditor"
                        class="rounded-xl border border-white/10 bg-white/5 px-6 py-3 font-black text-white transition hover:bg-white/10"
                    >
                        إلغاء
                    </button>

                    <button
                        type="button"
                        id="saveOfficeAvatarCrop"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-[#b4c5ff] to-[#4d8eff] px-6 py-3 font-black text-[#00174b] shadow-[0_0_20px_rgba(37,99,235,.35)] transition hover:-translate-y-0.5"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 4h11l3 3v13H5V4Z"/>
                            <path d="M8 4v6h8V4M9 20v-6h6v6"/>
                        </svg>
                        حفظ موضع الصورة
                    </button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('officeAvatarEditorModal');
                const openButton = document.getElementById('openOfficeAvatarEditor');
                const closeButton = document.getElementById('closeOfficeAvatarEditor');
                const cancelButton = document.getElementById('cancelOfficeAvatarEditor');
                const saveButton = document.getElementById('saveOfficeAvatarCrop');
                const sourceInput = document.getElementById('logoSourceInput');
                const finalInput = document.getElementById('logo');
                const editor = document.getElementById('officeAvatarEditor');
                const image = document.getElementById('officeAvatarEditorImage');
                const zoomInput = document.getElementById('officeAvatarZoom');
                const preview = document.getElementById('officeAvatarCurrentPreview');
                const emptyPreview = document.getElementById('officeAvatarEmptyPreview');

                if (
                    !modal || !openButton || !sourceInput || !finalInput
                    || !editor || !image || !zoomInput || !saveButton
                ) {
                    return;
                }

                let sourceUrl = null;
                let naturalWidth = 0;
                let naturalHeight = 0;
                let baseScale = 1;
                let zoom = 1;
                let offsetX = 0;
                let offsetY = 0;
                let dragging = false;
                let startX = 0;
                let startY = 0;
                let startOffsetX = 0;
                let startOffsetY = 0;

                const editorSize = () => editor.clientWidth;

                const clampOffsets = () => {
                    const size = editorSize();
                    const renderedWidth = naturalWidth * baseScale * zoom;
                    const renderedHeight = naturalHeight * baseScale * zoom;
                    const minX = Math.min(0, size - renderedWidth);
                    const minY = Math.min(0, size - renderedHeight);

                    offsetX = Math.min(0, Math.max(minX, offsetX));
                    offsetY = Math.min(0, Math.max(minY, offsetY));
                };

                const render = () => {
                    clampOffsets();

                    image.style.width =
                        (naturalWidth * baseScale * zoom) + 'px';
                    image.style.height =
                        (naturalHeight * baseScale * zoom) + 'px';
                    image.style.left = offsetX + 'px';
                    image.style.top = offsetY + 'px';
                };

                const centerImage = () => {
                    const size = editorSize();
                    const renderedWidth = naturalWidth * baseScale * zoom;
                    const renderedHeight = naturalHeight * baseScale * zoom;

                    offsetX = (size - renderedWidth) / 2;
                    offsetY = (size - renderedHeight) / 2;

                    render();
                };

                const openModal = () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                };

                openButton.addEventListener('click', function () {
                    sourceInput.click();
                });

                sourceInput.addEventListener('change', function () {
                    const file = this.files && this.files[0];

                    if (!file) {
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        alert('يرجى اختيار ملف صورة صالح.');
                        this.value = '';
                        return;
                    }

                    if (sourceUrl) {
                        URL.revokeObjectURL(sourceUrl);
                    }

                    sourceUrl = URL.createObjectURL(file);
                    image.onload = function () {
                        naturalWidth = image.naturalWidth;
                        naturalHeight = image.naturalHeight;

                        const size = editorSize();
                        baseScale = Math.max(
                            size / naturalWidth,
                            size / naturalHeight
                        );

                        zoom = 1;
                        zoomInput.value = '1';
                        centerImage();
                        openModal();
                    };

                    image.src = sourceUrl;
                });

                zoomInput.addEventListener('input', function () {
                    const size = editorSize();
                    const previousRenderedWidth =
                        naturalWidth * baseScale * zoom;
                    const previousRenderedHeight =
                        naturalHeight * baseScale * zoom;
                    const centerRatioX =
                        (size / 2 - offsetX) / previousRenderedWidth;
                    const centerRatioY =
                        (size / 2 - offsetY) / previousRenderedHeight;

                    zoom = Number(this.value);

                    const newRenderedWidth =
                        naturalWidth * baseScale * zoom;
                    const newRenderedHeight =
                        naturalHeight * baseScale * zoom;

                    offsetX = size / 2 - centerRatioX * newRenderedWidth;
                    offsetY = size / 2 - centerRatioY * newRenderedHeight;

                    render();
                });

                const pointerDown = (event) => {
                    dragging = true;
                    editor.setPointerCapture(event.pointerId);
                    startX = event.clientX;
                    startY = event.clientY;
                    startOffsetX = offsetX;
                    startOffsetY = offsetY;
                };

                const pointerMove = (event) => {
                    if (!dragging) {
                        return;
                    }

                    offsetX = startOffsetX + event.clientX - startX;
                    offsetY = startOffsetY + event.clientY - startY;
                    render();
                };

                const pointerUp = (event) => {
                    dragging = false;

                    if (editor.hasPointerCapture(event.pointerId)) {
                        editor.releasePointerCapture(event.pointerId);
                    }
                };

                editor.addEventListener('pointerdown', pointerDown);
                editor.addEventListener('pointermove', pointerMove);
                editor.addEventListener('pointerup', pointerUp);
                editor.addEventListener('pointercancel', pointerUp);

                const createCroppedFile = async () => {
                    const outputSize = 900;
                    const canvas = document.createElement('canvas');
                    canvas.width = outputSize;
                    canvas.height = outputSize;

                    const context = canvas.getContext('2d');
                    const size = editorSize();
                    const scaleToCanvas = outputSize / size;

                    context.imageSmoothingEnabled = true;
                    context.imageSmoothingQuality = 'high';

                    context.drawImage(
                        image,
                        offsetX * scaleToCanvas,
                        offsetY * scaleToCanvas,
                        naturalWidth * baseScale * zoom * scaleToCanvas,
                        naturalHeight * baseScale * zoom * scaleToCanvas
                    );

                    const blob = await new Promise((resolve) => {
                        canvas.toBlob(resolve, 'image/jpeg', 0.92);
                    });

                    if (!blob) {
                        throw new Error('تعذر تجهيز الصورة.');
                    }

                    return new File(
                        [blob],
                        'office-profile-' + Date.now() + '.jpg',
                        { type: 'image/jpeg' }
                    );
                };

                saveButton.addEventListener('click', async function () {
                    try {
                        saveButton.disabled = true;
                        saveButton.classList.add('opacity-60');

                        const croppedFile = await createCroppedFile();
                        const transfer = new DataTransfer();
                        transfer.items.add(croppedFile);
                        finalInput.files = transfer.files;

                        const previewUrl = URL.createObjectURL(croppedFile);
                        preview.src = previewUrl;
                        preview.classList.remove('hidden');

                        if (emptyPreview) {
                            emptyPreview.classList.add('hidden');
                        }

                        closeModal();
                    } catch (error) {
                        alert(error.message || 'حدث خطأ أثناء تجهيز الصورة.');
                    } finally {
                        saveButton.disabled = false;
                        saveButton.classList.remove('opacity-60');
                    }
                });

                closeButton?.addEventListener('click', closeModal);
                cancelButton?.addEventListener('click', closeModal);

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });

                window.addEventListener('resize', function () {
                    if (!modal.classList.contains('hidden') && naturalWidth) {
                        baseScale = Math.max(
                            editorSize() / naturalWidth,
                            editorSize() / naturalHeight
                        );
                        centerImage();
                    }
                });
            });
        </script>

    </div>
</x-app-layout>
