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
            : '#';

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $notificationsRoute = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardRoute;

        $isSuspended = $office->status === 'suspended';
        $isClosed = $office->status === 'closed';

        $isSubscriptionActive =
            $office->subscription_status === 'active'
            && $office->subscription_ends_at
            && $office->subscription_ends_at->isFuture();

        $statusLabel = match ($office->status) {
            'active' => 'مكتب فعال',
            'suspended' => 'مكتب موقوف',
            'closed' => 'مكتب مغلق',
            default => $office->status ?: 'غير محدد',
        };

        $statusClass = match ($office->status) {
            'active' => 'text-green-200 border-green-500/20 bg-green-500/10',
            'suspended' => 'text-red-200 border-red-500/20 bg-red-500/10',
            'closed' => 'text-slate-200 border-slate-500/20 bg-slate-500/10',
            default => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
        };

        $owner = $office->owner ?? null;
        $members = $office->activeMembers ?? collect();
    @endphp


    <style>
        [x-cloak] { display: none !important; }

        body.admin-office-show-menu-open {
            overflow: hidden;
        }

        .admin-office-show-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                radial-gradient(circle at 12% 12%, rgba(37, 99, 235, .16), transparent 32%),
                radial-gradient(circle at 88% 10%, rgba(131, 67, 244, .12), transparent 30%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .admin-office-show-glass {
            background: rgba(23, 31, 51, .52);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 18px 45px rgba(0, 0, 0, .18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .admin-office-show-link {
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .admin-office-show-link:hover {
            transform: translateX(-2px);
        }

        .admin-office-show-mobile-drawer {
            width: min(88vw, 390px);
        }

        .admin-office-show-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .admin-office-show-scroll::-webkit-scrollbar-track {
            background: rgba(11, 19, 38, .55);
        }

        .admin-office-show-scroll::-webkit-scrollbar-thumb {
            background: rgba(67, 70, 85, .70);
            border-radius: 999px;
        }

        @media (max-width: 1023px) {
            .admin-office-show-desktop-sidebar,
            .admin-office-show-desktop-topbar {
                display: none !important;
            }

            .admin-office-show-main {
                margin-right: 0 !important;
                padding-top: 7rem !important;
            }
        }
    </style>

    <div
        class="admin-office-show-page"
        dir="rtl"
        x-data="{ mobileMenuOpen: false }"
        x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('admin-office-show-menu-open', value))"
        @keydown.escape.window="mobileMenuOpen = false"
    >


        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-14 h-14 text-white rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] shadow-lg active:scale-95"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="min-w-0 text-center">
                    <p class="truncate text-lg font-black text-[#b4c5ff]">صرح الهندسة</p>
                    <p class="truncate text-xs text-[#c3c6d7]">تفاصيل المكتب</p>
                </div>

                <a
                    href="{{ $notificationsRoute }}"
                    class="flex items-center justify-center w-12 h-12 text-[#c3c6d7] border rounded-2xl border-white/10 bg-white/5"
                    aria-label="الإشعارات"
                >
                    🔔
                </a>
            </div>
        </header>

        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            @click="mobileMenuOpen = false"
            class="fixed inset-0 z-[80] bg-black/75 backdrop-blur-sm lg:hidden"
        ></div>

        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="admin-office-show-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden"
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
                    ✕
                </button>
            </div>

            <nav class="flex-1 p-5 space-y-3 overflow-y-auto admin-office-show-scroll">
                <a href="{{ $dashboardRoute }}" @click="mobileMenuOpen = false" class="admin-office-show-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⌂ <span>لوحة التحكم</span></a>
                <a href="{{ $consultationsRoute }}" @click="mobileMenuOpen = false" class="admin-office-show-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">📄 <span>الاستشارات</span></a>
                <a href="{{ $officesRoute }}" @click="mobileMenuOpen = false" class="flex items-center gap-4 rounded-2xl border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 px-5 py-4 font-black text-[#dbe1ff] shadow-lg shadow-blue-950/30">🏢 <span>المكاتب الهندسية</span></a>

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" @click="mobileMenuOpen = false" class="admin-office-show-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">📋 <span>طلبات إنشاء المكاتب</span></a>
                @endif

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" @click="mobileMenuOpen = false" class="admin-office-show-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">💳 <span>اشتراكات المكاتب</span></a>
                @endif

                <a href="{{ $profileRoute }}" @click="mobileMenuOpen = false" class="admin-office-show-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⚙ <span>الإعدادات</span></a>
            </nav>

            <div class="p-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#c3c6d7] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-4 font-black text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <aside class="admin-office-show-desktop-sidebar fixed right-0 top-0 z-50 hidden h-screen w-72 flex-col border-l border-white/10 bg-[#131b2e]/90 p-5 shadow-2xl backdrop-blur-xl lg:flex">
            <div class="px-3 mb-8">
                <h1 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h1>
                <p class="mt-1 text-sm text-[#c3c6d7]/65">نظام الإدارة الفاخر</p>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto admin-office-show-scroll">
                <a href="{{ $dashboardRoute }}" class="admin-office-show-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="admin-office-show-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاستشارات</a>
                <a href="{{ $officesRoute }}" class="block rounded-xl border-r-4 border-[#b4c5ff] bg-blue-600/20 px-4 py-3 text-sm font-black text-[#b4c5ff]">المكاتب الهندسية</a>

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" class="admin-office-show-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">طلبات إنشاء المكاتب</a>
                @endif

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" class="admin-office-show-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">اشتراكات المكاتب</a>
                @endif

                <a href="{{ $profileRoute }}" class="admin-office-show-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الإعدادات</a>
            </nav>

            <div class="pt-5 mt-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#8d90a0] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 font-bold text-red-200 border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <header class="admin-office-show-desktop-topbar fixed top-0 left-0 right-72 z-40 hidden h-20 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-8 backdrop-blur-xl lg:flex">
            <div>
                <h2 class="text-xl font-black text-white">تفاصيل المكتب الهندسي</h2>
                <p class="mt-1 text-xs text-[#8d90a0]">لوحة مدير النظام</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $notificationsRoute }}" class="flex items-center justify-center w-10 h-10 text-[#c3c6d7] border rounded-full border-white/10 bg-white/5">🔔</a>
                <div class="flex items-center justify-center w-10 h-10 font-black text-[#b4c5ff] border rounded-full border-white/10 bg-white/5">
                    {{ mb_substr($currentUser->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="min-h-screen px-4 admin-office-show-main pt-28 pb-14 sm:px-6 lg:mr-72 lg:px-8">
            <div class="max-w-6xl mx-auto">


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
                    <p class="mb-2 font-black">تعذر حفظ التعديل:</p>
                    <ul class="space-y-1 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">إدارة المكاتب الهندسية</p>
                    <h1 class="mt-2 text-3xl font-black text-white">تفاصيل المكتب</h1>
                    <p class="mt-2 text-slate-400">مراجعة بيانات المكتب وتحديث حالته التشغيلية.</p>
                </div>

                <a
                    href="{{ route('admin.offices.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                >
                    العودة إلى المكاتب
                </a>
            </div>

            <section class="overflow-hidden border rounded-3xl border-white/10 admin-office-show-glass">
                <div class="relative h-56 overflow-hidden sm:h-72">
                    @if ($office->cover_path)
                        <img
                            src="{{ asset('storage/' . $office->cover_path) }}"
                            alt="{{ $office->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        <div class="flex items-center justify-center w-full h-full text-7xl bg-gradient-to-br from-cyan-500/20 via-slate-900 to-slate-950">
                            🏢
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
                </div>

                <div class="relative px-6 pb-8 -mt-16 sm:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                            <div class="flex items-center justify-center flex-shrink-0 w-32 h-32 overflow-hidden text-5xl border-4 rounded-3xl border-slate-900 bg-[#222a3d]">
                                @if ($office->logo_path)
                                    <img
                                        src="{{ asset('storage/' . $office->logo_path) }}"
                                        alt="{{ $office->name }}"
                                        class="object-cover w-full h-full"
                                    >
                                @else
                                    🏢
                                @endif
                            </div>

                            <div class="pb-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-3xl font-black text-white sm:text-4xl">
                                        {{ $office->name }}
                                    </h2>

                                    <span class="px-3 py-1 text-xs font-black border rounded-full text-cyan-200 border-cyan-500/20 bg-cyan-500/10">
                                        مكتب هندسي
                                    </span>
                                </div>

                                <p class="mt-3 text-slate-300">
                                    {{ $office->city ?: 'مدينة غير محددة' }}
                                    @if ($office->country)
                                        — {{ $office->country }}
                                    @endif
                                </p>

                                <div class="flex flex-wrap gap-3 mt-4">
                                    <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                    @if ($isSubscriptionActive)
                                        <span class="inline-flex px-4 py-2 text-sm font-black text-green-200 border rounded-full border-green-500/20 bg-green-500/10">
                                            اشتراك فعال
                                        </span>
                                    @else
                                        <span class="inline-flex px-4 py-2 text-sm font-black text-yellow-200 border rounded-full border-yellow-500/20 bg-yellow-500/10">
                                            اشتراك غير فعال
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pb-2">
                            <div class="px-5 py-4 text-center border rounded-2xl border-white/10 bg-white/5">
                                <p class="text-2xl font-black text-white">
                                    {{ $office->active_members_count ?? $members->count() }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">أعضاء فعالون</p>
                            </div>

                            <div class="px-5 py-4 text-center border rounded-2xl border-white/10 bg-white/5">
                                <p class="text-2xl font-black text-white">
                                    {{ $office->consultations_count ?? 0 }}
                                </p>
                                <p class="mt-1 text-xs text-slate-400">استشارات محولة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($isSuspended && $office->suspension_reason)
                <div class="p-6 mt-8 border rounded-3xl border-red-500/30 bg-red-500/10">
                    <h3 class="text-lg font-black text-red-200">سبب إيقاف المكتب</h3>
                    <p class="mt-3 leading-8 text-red-100">{{ $office->suspension_reason }}</p>
                </div>
            @endif

            <div class="grid gap-6 mt-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <section class="p-6 border rounded-3xl border-white/10 admin-office-show-glass sm:p-8">
                        <h3 class="text-2xl font-black text-white">نبذة عن المكتب</h3>
                        <p class="mt-5 leading-9 text-slate-300">
                            {{ $office->description ?: 'لم تتم إضافة نبذة تعريفية عن المكتب.' }}
                        </p>
                    </section>

                    <section class="p-6 border rounded-3xl border-white/10 admin-office-show-glass sm:p-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-2xl font-black text-white">فريق المكتب</h3>
                                <p class="mt-2 text-slate-400">المهندسون الفعالون المسجلون في المكتب.</p>
                            </div>

                            <span class="px-4 py-2 text-sm font-black border rounded-full text-cyan-200 border-cyan-500/20 bg-cyan-500/10">
                                {{ $office->active_members_count ?? $members->count() }} عضو
                            </span>
                        </div>

                        <div class="grid gap-4 mt-7 sm:grid-cols-2">
                            @forelse ($members as $member)
                                <div class="flex items-center gap-4 p-4 border rounded-2xl border-white/10 bg-white/5">
                                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden text-xl border rounded-2xl border-white/10 bg-[#222a3d]">
                                        @if ($member->user?->profile_photo)
                                            <img
                                                src="{{ asset('storage/' . $member->user->profile_photo) }}"
                                                alt="{{ $member->user?->name ?? 'مهندس' }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            👤
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-white truncate">
                                            {{ $member->user?->name ?? 'مهندس غير متاح' }}
                                        </p>
                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $member->position ?: 'مهندس' }}
                                        </p>
                                        <p class="mt-1 text-xs text-cyan-300">
                                            {{ $member->specialty?->name ?: 'تخصص غير محدد' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center border rounded-2xl border-white/10 bg-white/5 sm:col-span-2">
                                    <p class="text-slate-400">لا يوجد أعضاء فعالون في المكتب حاليًا.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    <section class="p-6 border rounded-3xl border-white/10 admin-office-show-glass">
                        <h3 class="text-xl font-black text-white">معلومات المكتب</h3>

                        <div class="mt-6 space-y-4">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">مالك المكتب</p>
                                <p class="mt-2 font-bold text-white">
                                    {{ $owner?->name ?? 'غير محدد' }}
                                </p>
                                @if ($owner?->email)
                                    <p class="mt-1 text-sm break-all text-slate-400">{{ $owner->email }}</p>
                                @endif
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">البريد الإلكتروني</p>
                                <p class="mt-2 font-bold text-white break-all">
                                    {{ $office->email ?: 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">رقم الهاتف</p>
                                <p class="mt-2 font-bold text-white">{{ $office->phone ?: 'غير محدد' }}</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">العنوان</p>
                                <p class="mt-2 leading-7 text-white">{{ $office->address ?: 'غير محدد' }}</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">رقم الترخيص</p>
                                <p class="mt-2 font-bold text-white">{{ $office->license_number ?: 'غير محدد' }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="p-6 border rounded-3xl border-white/10 admin-office-show-glass">
                        <h3 class="text-xl font-black text-white">بيانات الاشتراك</h3>

                        <div class="mt-5 space-y-4">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">حالة الاشتراك</p>
                                <p class="mt-2 font-black {{ $isSubscriptionActive ? 'text-green-300' : 'text-yellow-300' }}">
                                    {{ $isSubscriptionActive ? 'فعال' : 'غير فعال' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">تاريخ انتهاء الاشتراك</p>
                                <p class="mt-2 font-bold text-white">
                                    {{ $office->subscription_ends_at?->format('Y-m-d') ?? 'غير محدد' }}
                                </p>
                            </div>

                            @if (Route::has('admin.office-subscriptions.index'))
                                <a
                                    href="{{ route('admin.office-subscriptions.index') }}"
                                    class="inline-flex items-center justify-center w-full px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                                >
                                    مراجعة الاشتراكات
                                </a>
                            @endif
                        </div>
                    </section>

                    @if (Route::has('admin.offices.status'))
                        <section class="p-6 border rounded-3xl border-white/10 admin-office-show-glass">
                            <h3 class="text-xl font-black text-white">تحديث حالة المكتب</h3>

                            <form
                            method="POST"
                            action="{{ route('admin.offices.status', $office) }}"
                            class="mt-5 space-y-4"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="block mb-2 text-sm font-bold text-white">
                                    الحالة الجديدة
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-[#222a3d]"
                                    required
                                >
                                    <option value="active" @selected(old('status', $office->status) === 'active')>
                                        فعال
                                    </option>
                                    <option value="suspended" @selected(old('status', $office->status) === 'suspended')>
                                        موقوف
                                    </option>
                                    <option value="closed" @selected(old('status', $office->status) === 'closed')>
                                        مغلق
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="suspension_reason" class="block mb-2 text-sm font-bold text-white">
                                    سبب الإيقاف أو الإغلاق
                                </label>

                                <textarea
                                    id="suspension_reason"
                                    name="suspension_reason"
                                    rows="4"
                                    placeholder="يُكتب السبب عند إيقاف المكتب أو إغلاقه"
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-[#222a3d]"
                                >{{ old('suspension_reason', $office->suspension_reason) }}</textarea>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center w-full px-6 py-3 font-black text-white transition rounded-xl bg-[#2563eb] hover:brightness-110"
                                onclick="return confirm('هل أنت متأكد من تحديث حالة المكتب؟')"
                            >
                                حفظ حالة المكتب
                            </button>
                            </form>
                        </section>
                    @endif

                    <a
                        href="{{ route('admin.offices.index') }}"
                        class="inline-flex items-center justify-center w-full px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        العودة إلى جميع المكاتب
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
