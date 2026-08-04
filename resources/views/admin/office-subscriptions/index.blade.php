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
            : '#';

        $subscriptionsRoute = Route::has('admin.office-subscriptions.index')
            ? route('admin.office-subscriptions.index')
            : url('/admin/office-subscriptions');

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $notificationsRoute = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardRoute;
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        body.office-subscriptions-menu-open {
            overflow: hidden;
        }

        .office-subscriptions-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                radial-gradient(circle at 12% 14%, rgba(37, 99, 235, .16), transparent 32%),
                radial-gradient(circle at 88% 10%, rgba(131, 67, 244, .12), transparent 30%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .office-subscriptions-glass {
            background: rgba(23, 31, 51, .52);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 18px 45px rgba(0, 0, 0, .18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .office-subscriptions-card {
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .office-subscriptions-card:hover {
            transform: translateY(-4px);
            border-color: rgba(180, 197, 255, .24);
            box-shadow: 0 22px 55px rgba(0, 0, 0, .26);
        }

        .office-subscriptions-link {
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .office-subscriptions-link:hover {
            transform: translateX(-2px);
        }

        .office-subscriptions-mobile-drawer {
            width: min(88vw, 390px);
        }

        .office-subscriptions-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .office-subscriptions-scroll::-webkit-scrollbar-track {
            background: rgba(11, 19, 38, .55);
        }

        .office-subscriptions-scroll::-webkit-scrollbar-thumb {
            background: rgba(67, 70, 85, .70);
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .office-subscriptions-desktop-sidebar,
            .office-subscriptions-desktop-topbar {
                display: none !important;
            }

            .office-subscriptions-main {
                margin-right: 0 !important;
                padding-top: 7rem !important;
            }
        }
    </style>

    <div
        class="office-subscriptions-page"
        dir="rtl"
        x-data="{ mobileMenuOpen: false }"
        x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('office-subscriptions-menu-open', value))"
        @keydown.escape.window="mobileMenuOpen = false"
    >
        {{-- شريط الجوال --}}
        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-14 h-14 text-white transition rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] shadow-lg active:scale-95"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="min-w-0 text-center">
                    <p class="truncate text-lg font-black text-[#b4c5ff]">صرح الهندسة</p>
                    <p class="truncate text-xs text-[#c3c6d7]">اشتراكات المكاتب</p>
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
            class="office-subscriptions-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden"
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

            <nav class="flex-1 p-5 space-y-3 overflow-y-auto office-subscriptions-scroll">
                <a href="{{ $dashboardRoute }}" @click="mobileMenuOpen = false" class="office-subscriptions-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">⌂</span>
                    <span>لوحة التحكم</span>
                </a>

                <a href="{{ $consultationsRoute }}" @click="mobileMenuOpen = false" class="office-subscriptions-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">📄</span>
                    <span>الاستشارات</span>
                </a>

                <a href="{{ $officesRoute }}" @click="mobileMenuOpen = false" class="office-subscriptions-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">🏢</span>
                    <span>المكاتب الهندسية</span>
                </a>

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" @click="mobileMenuOpen = false" class="office-subscriptions-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">📋</span>
                        <span>طلبات إنشاء المكاتب</span>
                    </a>
                @endif

                <a href="{{ $subscriptionsRoute }}" @click="mobileMenuOpen = false" class="flex items-center gap-4 rounded-2xl border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 px-5 py-4 font-black text-[#dbe1ff] shadow-lg shadow-blue-950/30">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">💳</span>
                    <span>اشتراكات المكاتب</span>
                </a>

                <a href="{{ $profileRoute }}" @click="mobileMenuOpen = false" class="office-subscriptions-link flex items-center gap-4 rounded-2xl border border-transparent px-5 py-4 font-black text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white">
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

        {{-- القائمة الجانبية للكمبيوتر --}}
        <aside class="office-subscriptions-desktop-sidebar fixed right-0 top-0 z-50 hidden h-screen w-72 flex-col border-l border-white/10 bg-[#131b2e]/90 p-5 shadow-2xl backdrop-blur-xl lg:flex">
            <div class="px-3 mb-8">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">صرح الهندسة</h1>
                <p class="mt-1 text-sm text-[#c3c6d7]/65">نظام الإدارة الفاخر</p>
            </div>

            <nav class="flex-1 pr-1 space-y-2 overflow-y-auto office-subscriptions-scroll">
                <a href="{{ $dashboardRoute }}" class="office-subscriptions-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">
                    <span>لوحة التحكم</span>
                </a>

                <a href="{{ $consultationsRoute }}" class="office-subscriptions-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">
                    <span>الاستشارات</span>
                </a>

                <a href="{{ $officesRoute }}" class="office-subscriptions-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">
                    <span>المكاتب الهندسية</span>
                </a>

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" class="office-subscriptions-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">
                        <span>طلبات إنشاء المكاتب</span>
                    </a>
                @endif

                <a href="{{ $subscriptionsRoute }}" class="flex items-center gap-3 rounded-xl border-r-4 border-[#b4c5ff] bg-blue-600/20 px-4 py-3 text-sm font-black text-[#b4c5ff]">
                    <span>اشتراكات المكاتب</span>
                </a>

                <a href="{{ $profileRoute }}" class="office-subscriptions-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-5 mt-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#8d90a0] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 font-bold text-red-200 transition border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- الشريط العلوي للكمبيوتر --}}
        <header class="office-subscriptions-desktop-topbar fixed top-0 left-0 right-72 z-40 hidden h-20 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-8 backdrop-blur-xl lg:flex">
            <div>
                <h2 class="text-xl font-black text-white">اشتراكات المكاتب الهندسية</h2>
                <p class="mt-1 text-xs text-[#8d90a0]">لوحة مدير النظام</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $notificationsRoute }}" class="flex items-center justify-center w-10 h-10 text-[#c3c6d7] transition border rounded-full border-white/10 bg-white/5 hover:text-white">
                    🔔
                </a>

                <div class="flex items-center justify-center w-10 h-10 font-black text-[#b4c5ff] border rounded-full border-white/10 bg-white/5">
                    {{ mb_substr($currentUser->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="min-h-screen px-4 office-subscriptions-main pt-28 pb-14 sm:px-6 lg:mr-72 lg:px-8">
            <div class="mx-auto max-w-[1700px]">
                @if (session('success'))
                    <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="mb-9">
                    <p class="text-sm font-black text-[#b4c5ff]">إدارة المكاتب</p>
                    <h1 class="mt-2 text-3xl font-black text-white sm:text-4xl">اشتراكات المكاتب الهندسية</h1>
                    <p class="mt-3 max-w-3xl leading-7 text-[#c3c6d7]">
                        مراجعة وإدارة إيصالات الاشتراكات الشهرية للمكاتب المسجلة.
                    </p>
                </section>

                <section class="grid gap-5 mb-8 sm:grid-cols-2 xl:grid-cols-3">
                    <article class="p-6 office-subscriptions-card office-subscriptions-glass rounded-2xl border-yellow-500/20">
                        <p class="text-sm font-black text-[#c3c6d7]">قيد المراجعة</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-yellow-300">{{ $statistics['under_review'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 text-yellow-300 rounded-2xl bg-yellow-500/10">⏳</span>
                        </div>
                    </article>

                    <article class="p-6 office-subscriptions-card office-subscriptions-glass rounded-2xl border-green-500/20">
                        <p class="text-sm font-black text-[#c3c6d7]">اشتراكات فعالة</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-green-300">{{ $statistics['active'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 text-green-300 rounded-2xl bg-green-500/10">✓</span>
                        </div>
                    </article>

                    <article class="p-6 office-subscriptions-card office-subscriptions-glass rounded-2xl border-red-500/20 sm:col-span-2 xl:col-span-1">
                        <p class="text-sm font-black text-[#c3c6d7]">اشتراكات مرفوضة</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-red-300">{{ $statistics['rejected'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 text-red-300 rounded-2xl bg-red-500/10">✕</span>
                        </div>
                    </article>
                </section>

                <section class="overflow-hidden office-subscriptions-glass rounded-3xl">
                    <div class="flex flex-col gap-3 p-6 border-b border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-black text-white">سجل الاشتراكات</h2>
                            <p class="mt-1 text-sm text-[#c3c6d7]">جميع إيصالات الاشتراك وحالات المراجعة.</p>
                        </div>

                        <a
                            href="{{ $subscriptionsRoute }}"
                            class="inline-flex items-center justify-center px-5 py-3 font-black text-[#b4c5ff] transition border rounded-xl border-[#b4c5ff]/30 hover:bg-[#b4c5ff]/10"
                        >
                            تحديث القائمة
                        </a>
                    </div>

                    <div class="overflow-x-auto office-subscriptions-scroll">
                        <table class="w-full min-w-[1250px] text-sm">
                            <thead class="bg-[#2d3449]/45 text-[#c3c6d7]">
                                <tr>
                                    <th class="p-4 text-right">اسم المكتب</th>
                                    <th class="p-4 text-right">المالك</th>
                                    <th class="p-4 text-right">القيمة</th>
                                    <th class="p-4 text-right">طريقة الدفع</th>
                                    <th class="p-4 text-right">المرجع</th>
                                    <th class="p-4 text-right">الحالة</th>
                                    <th class="p-4 text-right">تاريخ الدفع</th>
                                    <th class="p-4 text-right">الإجراء</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/5">
                                @forelse ($subscriptions as $subscription)
                                    @php
                                        $statusData = match ($subscription->status) {
                                            'active' => [
                                                'label' => 'فعال',
                                                'class' => 'text-green-200 bg-green-500/10 border-green-500/20',
                                            ],
                                            'under_review' => [
                                                'label' => 'قيد المراجعة',
                                                'class' => 'text-yellow-200 bg-yellow-500/10 border-yellow-500/20',
                                            ],
                                            'expired' => [
                                                'label' => 'منتهي',
                                                'class' => 'text-slate-300 bg-white/5 border-white/10',
                                            ],
                                            'rejected' => [
                                                'label' => 'مرفوض',
                                                'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                            ],
                                            'cancelled' => [
                                                'label' => 'ملغي',
                                                'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                            ],
                                            default => [
                                                'label' => 'بانتظار الدفع',
                                                'class' => 'text-slate-300 bg-white/5 border-white/10',
                                            ],
                                        };
                                    @endphp

                                    <tr class="align-top transition hover:bg-white/[0.03]">
                                        <td class="p-4">
                                            <div class="font-black text-white">
                                                {{ $subscription->office?->name ?? 'مكتب غير موجود' }}
                                            </div>

                                            <div class="mt-1 text-xs text-[#8d90a0]">
                                                حالة المكتب:
                                                {{ match ($subscription->office?->status) {
                                                    'active' => 'فعال',
                                                    'suspended' => 'موقوف عن العمل',
                                                    'closed' => 'مغلق',
                                                    'rejected' => 'مرفوض',
                                                    default => 'قيد المراجعة',
                                                } }}
                                            </div>
                                        </td>

                                        <td class="p-4">
                                            <div class="font-bold text-white">
                                                {{ $subscription->office?->owner?->name ?? 'غير معروف' }}
                                            </div>
                                            <div class="mt-1 text-xs text-[#8d90a0]">
                                                {{ $subscription->office?->owner?->email }}
                                            </div>
                                        </td>

                                        <td class="p-4 font-black text-white">
                                            {{ number_format((float) $subscription->amount, 2) }}
                                            {{ $subscription->currency }}
                                        </td>

                                        <td class="p-4 text-[#c3c6d7]">
                                            {{ match ($subscription->payment_method) {
                                                'bank_transfer' => 'تحويل بنكي',
                                                'wallet' => 'محفظة إلكترونية',
                                                'cash' => 'دفع نقدي',
                                                'other' => 'طريقة أخرى',
                                                default => $subscription->payment_method ?: 'غير محددة',
                                            } }}
                                        </td>

                                        <td class="p-4 text-[#c3c6d7]">
                                            {{ $subscription->payment_reference ?: '—' }}
                                        </td>

                                        <td class="p-4">
                                            <span class="inline-flex px-3 py-1 text-xs font-black border rounded-full {{ $statusData['class'] }}">
                                                {{ $statusData['label'] }}
                                            </span>
                                        </td>

                                        <td class="p-4 text-[#c3c6d7]">
                                            {{ $subscription->paid_at?->format('Y-m-d H:i') ?? '—' }}
                                        </td>

                                        <td class="p-4">
                                            <div class="min-w-[310px] space-y-4">
                                                @if ($subscription->receipt_path && Route::has('office-subscriptions.receipt'))
                                                    <a
                                                        href="{{ route('office-subscriptions.receipt', $subscription) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center justify-center w-full px-4 py-2 font-bold transition border text-cyan-100 rounded-xl border-cyan-500/20 bg-cyan-500/10 hover:bg-cyan-500/20"
                                                    >
                                                        عرض الإيصال
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-bold border text-[#8d90a0] rounded-xl border-white/10 bg-white/5">
                                                        لا يوجد إيصال
                                                    </span>
                                                @endif

                                                @if (
                                                    $subscription->status === 'under_review'
                                                    && Route::has('admin.office-subscriptions.review')
                                                )
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.office-subscriptions.review', $subscription) }}"
                                                        class="p-4 border rounded-2xl border-green-500/20 bg-green-500/5"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <input type="hidden" name="decision" value="approve">

                                                        <label class="block mb-2 text-xs font-bold text-[#c3c6d7]">
                                                            ملاحظات الاعتماد
                                                        </label>

                                                        <textarea
                                                            name="notes"
                                                            rows="2"
                                                            class="w-full px-3 py-2 text-sm text-white border rounded-xl border-white/10 bg-[#222a3d]"
                                                            placeholder="ملاحظات اختيارية"
                                                        ></textarea>

                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('هل أنت متأكد من اعتماد اشتراك هذا المكتب لمدة شهر؟')"
                                                            class="w-full px-4 py-2 mt-3 font-black text-white transition bg-green-600 rounded-xl hover:bg-green-500"
                                                        >
                                                            اعتماد الاشتراك
                                                        </button>
                                                    </form>

                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.office-subscriptions.review', $subscription) }}"
                                                        class="p-4 border rounded-2xl border-red-500/20 bg-red-500/5"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <input type="hidden" name="decision" value="reject">

                                                        <label class="block mb-2 text-xs font-bold text-red-200">
                                                            سبب الرفض
                                                        </label>

                                                        <textarea
                                                            name="rejection_reason"
                                                            rows="3"
                                                            required
                                                            class="w-full px-3 py-2 text-sm text-white border rounded-xl border-white/10 bg-[#222a3d]"
                                                            placeholder="اكتب سبب رفض الإيصال"
                                                        ></textarea>

                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('هل أنت متأكد من رفض إيصال الاشتراك؟')"
                                                            class="w-full px-4 py-2 mt-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                                                        >
                                                            رفض الإيصال
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="text-xs leading-6 text-[#8d90a0]">
                                                        @if ($subscription->approved_at)
                                                            تمت المراجعة:
                                                            {{ $subscription->approved_at->format('Y-m-d H:i') }}
                                                        @else
                                                            لا يوجد إجراء متاح.
                                                        @endif

                                                        @if ($subscription->approver)
                                                            <div class="mt-1">
                                                                بواسطة: {{ $subscription->approver->name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    @if (
                                        $subscription->status === 'rejected'
                                        && $subscription->rejection_reason
                                    )
                                        <tr>
                                            <td colspan="8" class="p-4 border-b border-red-500/10 bg-red-500/5">
                                                <span class="font-black text-red-200">سبب الرفض:</span>
                                                <span class="text-red-100">{{ $subscription->rejection_reason }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-14">
                                            <div class="flex items-center justify-center w-24 h-24 mx-auto text-5xl border rounded-3xl border-white/10 bg-white/5">
                                                📦
                                            </div>
                                            <h3 class="mt-6 text-2xl font-black text-white">لا توجد اشتراكات مسجلة حاليًا</h3>
                                            <p class="mt-3 text-[#c3c6d7]">
                                                ستظهر إيصالات الاشتراك الجديدة هنا فور رفعها.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                @if ($subscriptions->hasPages())
                    <div class="p-5 mt-8 office-subscriptions-glass rounded-2xl">
                        {{ $subscriptions->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
