<x-app-layout>
    @php
        $search = $search ?? request('search', '');
        $status = $status ?? request('status', '');

        $statistics = $statistics ?? [
            'all' => isset($offices) ? $offices->total() : 0,
            'active' => 0,
            'suspended' => 0,
        ];

        $navItems = array_values(array_filter([
            [
                'label' => 'لوحة التحكم',
                'route' => Route::has('dashboard') ? 'dashboard' : null,
                'icon' => 'dashboard',
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'label' => 'طلبات إنشاء المكاتب',
                'route' => Route::has('admin.office-applications.index') ? 'admin.office-applications.index' : null,
                'icon' => 'applications',
                'active' => request()->routeIs('admin.office-applications.*'),
            ],
            [
                'label' => 'اشتراكات المكاتب',
                'route' => Route::has('admin.office-subscriptions.index') ? 'admin.office-subscriptions.index' : null,
                'icon' => 'payments',
                'active' => request()->routeIs('admin.office-subscriptions.*'),
            ],
            [
                'label' => 'المكاتب الهندسية',
                'route' => Route::has('admin.offices.index') ? 'admin.offices.index' : null,
                'icon' => 'office',
                'active' => request()->routeIs('admin.offices.*'),
            ],
            [
                'label' => 'جميع الاستشارات',
                'route' => Route::has('consultations.index') ? 'consultations.index' : null,
                'icon' => 'consultations',
                'active' => request()->routeIs('consultations.*'),
            ],
            [
                'label' => 'إعدادات الحساب',
                'route' => Route::has('profile.edit') ? 'profile.edit' : null,
                'icon' => 'settings',
                'active' => request()->routeIs('profile.*'),
            ],
        ], fn ($item) => filled($item['route'])));
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        .admin-offices-shell {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                radial-gradient(circle at 10% 10%, rgba(37, 99, 235, .16), transparent 30%),
                radial-gradient(circle at 90% 15%, rgba(131, 67, 244, .11), transparent 30%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .luxury-glass {
            background: rgba(45, 52, 73, .40);
            border: 1px solid rgba(255, 255, 255, .10);
            box-shadow: 0 18px 45px rgba(0, 0, 0, .18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .luxury-card {
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .luxury-card:hover {
            transform: translateY(-5px);
            border-color: rgba(180, 197, 255, .28);
            box-shadow: 0 22px 55px rgba(0, 0, 0, .28);
        }

        .admin-nav-link {
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .admin-nav-link:hover {
            transform: translateX(-2px);
        }

        .admin-offices-scrollbar::-webkit-scrollbar { width: 7px; }
        .admin-offices-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .admin-offices-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(180, 197, 255, .24);
            border-radius: 999px;
        }
    </style>

    <div
        x-data="{ mobileMenuOpen: false }"
        x-on:keydown.escape.window="mobileMenuOpen = false"
        class="admin-offices-shell"
        dir="rtl"
    >
        {{-- Desktop sidebar --}}
        <aside class="fixed top-0 right-0 z-50 hidden w-72 h-screen p-5 border-l lg:flex lg:flex-col border-white/10 bg-[#131b2e]/90 shadow-2xl backdrop-blur-xl">
            <div class="px-3 mb-7">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">صرح الهندسة</h1>
                <p class="mt-1 text-sm text-[#c3c6d7]/65">نظام الإدارة الفاخر</p>
            </div>

            @if (Route::has('admin.office-applications.index'))
                <a
                    href="{{ route('admin.office-applications.index') }}"
                    class="flex items-center justify-center gap-3 px-4 py-3 mb-6 font-black text-white transition rounded-xl bg-gradient-to-l from-blue-600 to-violet-600 hover:brightness-110"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/>
                    </svg>
                    مراجعة طلبات المكاتب
                </a>
            @endif

            <nav class="flex-1 pr-1 space-y-2 overflow-y-auto admin-offices-scrollbar">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="admin-nav-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold
                            {{ $item['active']
                                ? 'border-r-4 border-[#b4c5ff] bg-blue-600/20 text-[#b4c5ff]'
                                : 'text-[#c3c6d7] hover:bg-white/5 hover:text-white' }}"
                    >
                        @include('components.admin-office-nav-icon', ['name' => $item['icon']])
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="pt-5 mt-5 border-t border-white/10">
                <div class="flex items-center gap-3 px-3 mb-4">
                    <div class="flex items-center justify-center w-11 h-11 font-black text-[#b4c5ff] border rounded-full border-white/10 bg-white/5">
                        {{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-[#8d90a0]">مدير النظام</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center justify-center w-full gap-2 px-4 py-3 font-bold text-red-200 transition border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                    >
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 z-[70] bg-black/70 backdrop-blur-sm lg:hidden"
            x-on:click="mobileMenuOpen = false"
        ></div>

        {{-- Mobile drawer --}}
        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 z-[80] flex h-screen w-[88%] max-w-sm flex-col border-l border-white/10 bg-[#10192c]/98 p-5 shadow-2xl backdrop-blur-2xl lg:hidden"
        >
            <div class="flex items-center justify-between pb-5 border-b border-white/10">
                <div>
                    <h2 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h2>
                    <p class="mt-1 text-sm text-[#c3c6d7]/65">قائمة الإدارة</p>
                </div>

                <button
                    type="button"
                    x-on:click="mobileMenuOpen = false"
                    class="flex items-center justify-center text-white transition border w-11 h-11 rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 py-6 space-y-3 overflow-y-auto admin-offices-scrollbar">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        x-on:click="mobileMenuOpen = false"
                        class="flex items-center gap-4 rounded-2xl px-5 py-4 text-base font-black transition
                            {{ $item['active']
                                ? 'border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 text-[#dbe1ff] shadow-lg shadow-blue-950/30'
                                : 'border border-transparent text-[#c3c6d7] hover:border-white/10 hover:bg-white/5 hover:text-white' }}"
                    >
                        <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-white/5">
                            @include('components.admin-office-nav-icon', ['name' => $item['icon']])
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="pt-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-xs text-[#8d90a0]">{{ auth()->user()->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-5 py-4 font-black text-red-100 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                    >
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- Top bar --}}
        <header class="fixed top-0 right-0 left-0 z-40 flex h-20 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-4 backdrop-blur-xl sm:px-6 lg:right-72 lg:px-8">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    x-on:click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-12 h-12 text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10 lg:hidden"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div>
                    <h2 class="text-lg font-black text-white sm:text-xl">إدارة المكاتب الهندسية</h2>
                    <p class="hidden mt-1 text-xs text-[#8d90a0] sm:block">لوحة مدير النظام</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if (Route::has('dashboard'))
                    <a
                        href="{{ route('dashboard') }}"
                        class="hidden px-4 py-2 text-sm font-bold text-[#c3c6d7] transition rounded-xl hover:bg-white/5 hover:text-white sm:inline-flex"
                    >
                        الرئيسية
                    </a>
                @endif

                <div class="flex items-center justify-center w-10 h-10 font-black text-[#b4c5ff] border rounded-full border-white/10 bg-white/5">
                    {{ mb_substr(auth()->user()->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="min-h-screen px-4 pt-28 pb-14 sm:px-6 lg:mr-72 lg:px-8">
            <div class="mx-auto max-w-7xl">
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

                <section class="mb-9">
                    <p class="text-sm font-black text-[#b4c5ff]">إدارة النظام</p>
                    <h1 class="mt-2 text-3xl font-black text-white sm:text-4xl">جميع المكاتب الهندسية</h1>
                    <p class="mt-3 max-w-3xl leading-7 text-[#c3c6d7]">
                        استعرض المكاتب الهندسية، راجع بياناتها، وتابع حالة التفعيل والاشتراك.
                    </p>
                </section>

                <section class="grid gap-5 mb-8 sm:grid-cols-2 xl:grid-cols-3">
                    <article class="luxury-glass rounded-2xl p-6 border-[#b4c5ff]/20">
                        <p class="text-sm font-black tracking-wide text-[#c3c6d7]">إجمالي المكاتب</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-[#b4c5ff]">{{ $statistics['all'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 text-[#b4c5ff] rounded-2xl bg-blue-500/10">🏢</span>
                        </div>
                    </article>

                    <article class="p-6 luxury-glass rounded-2xl border-green-500/20">
                        <p class="text-sm font-black tracking-wide text-[#c3c6d7]">مكاتب فعالة</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-green-300">{{ $statistics['active'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 text-green-300 rounded-2xl bg-green-500/10">✓</span>
                        </div>
                    </article>

                    <article class="p-6 luxury-glass rounded-2xl border-amber-500/20 sm:col-span-2 xl:col-span-1">
                        <p class="text-sm font-black tracking-wide text-[#c3c6d7]">مكاتب موقوفة</p>
                        <div class="flex items-end justify-between mt-6">
                            <span class="text-4xl font-black text-amber-300">{{ $statistics['suspended'] ?? 0 }}</span>
                            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-amber-500/10">⏸</span>
                        </div>
                    </article>
                </section>

                <section class="p-5 mb-8 luxury-glass rounded-2xl sm:p-6">
                    <form method="GET" action="{{ route('admin.offices.index') }}" class="grid gap-4 md:grid-cols-[1fr_230px_auto]">
                        <div>
                            <label for="search" class="block mb-2 text-sm font-black text-[#c3c6d7]">البحث</label>
                            <div class="relative">
                                <input
                                    id="search"
                                    name="search"
                                    type="search"
                                    value="{{ $search }}"
                                    placeholder="اسم المكتب، المدينة، الدولة..."
                                    class="w-full rounded-xl border border-[#434655] bg-[#2d3449] px-4 py-3 pl-11 text-white placeholder:text-[#8d90a0] focus:border-[#b4c5ff] focus:ring-[#b4c5ff]"
                                >
                                <svg class="absolute w-5 h-5 left-4 top-3.5 text-[#8d90a0]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                                    <path d="m21 21-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block mb-2 text-sm font-black text-[#c3c6d7]">حالة المكتب</label>
                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-xl border border-[#434655] bg-[#2d3449] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع الحالات</option>
                                <option value="active" @selected($status === 'active')>فعال</option>
                                <option value="suspended" @selected($status === 'suspended')>موقوف</option>
                                <option value="closed" @selected($status === 'closed')>مغلق</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center flex-1 px-6 py-3 font-black text-[#00174b] transition rounded-xl bg-[#b4c5ff] hover:bg-[#dbe1ff] md:flex-none"
                            >
                                بحث
                            </button>

                            @if ($search !== '' || $status !== '')
                                <a
                                    href="{{ route('admin.offices.index') }}"
                                    class="inline-flex items-center justify-center px-5 py-3 font-bold text-[#c3c6d7] transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10 hover:text-white"
                                >
                                    مسح
                                </a>
                            @endif
                        </div>
                    </form>
                </section>

                <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($offices as $office)
                        @php
                            $isSuspended = $office->status === 'suspended';
                            $isClosed = $office->status === 'closed';

                            $isSubscriptionActive =
                                $office->subscription_status === 'active'
                                && $office->subscription_ends_at
                                && $office->subscription_ends_at->isFuture();

                            $officeStatus = match ($office->status) {
                                'active' => [
                                    'label' => 'مكتب فعال',
                                    'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                                ],
                                'suspended' => [
                                    'label' => 'مكتب موقوف',
                                    'class' => 'text-amber-200 border-amber-500/20 bg-amber-500/10',
                                ],
                                'closed' => [
                                    'label' => 'مكتب مغلق',
                                    'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                                ],
                                default => [
                                    'label' => $office->status ?: 'غير محدد',
                                    'class' => 'text-slate-200 border-white/10 bg-white/5',
                                ],
                            };
                        @endphp

                        <article class="overflow-hidden luxury-card luxury-glass rounded-3xl">
                            <div class="relative h-44 overflow-hidden bg-gradient-to-br from-[#17213a] to-[#0f1729]">
                                @if ($office->cover_path)
                                    <img
                                        src="{{ asset('storage/' . $office->cover_path) }}"
                                        alt="{{ $office->name }}"
                                        class="object-cover w-full h-full"
                                    >
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-6xl">🏢</div>
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326] via-transparent to-transparent"></div>

                                <span class="absolute px-3 py-1 text-xs font-black border rounded-full top-4 left-4 {{ $officeStatus['class'] }}">
                                    {{ $officeStatus['label'] }}
                                </span>
                            </div>

                            <div class="relative px-6 pb-6">
                                <div class="flex items-end gap-4 -mt-10">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-[#171f33] bg-[#1f2940] text-3xl font-black text-[#b4c5ff]">
                                        @if ($office->logo_path)
                                            <img
                                                src="{{ asset('storage/' . $office->logo_path) }}"
                                                alt="{{ $office->name }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            {{ mb_substr($office->name, 0, 1) }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 pb-1">
                                        <h2 class="text-xl font-black text-white truncate">{{ $office->name }}</h2>
                                        <p class="mt-1 text-xs text-[#8d90a0]">
                                            {{ $office->city ?: 'مدينة غير محددة' }}
                                            @if ($office->country)
                                                — {{ $office->country }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-5">
                                    <span class="px-3 py-1 text-xs font-black border rounded-full {{ $isSubscriptionActive
                                        ? 'text-green-200 border-green-500/20 bg-green-500/10'
                                        : 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10' }}">
                                        {{ $isSubscriptionActive ? 'الاشتراك فعال' : 'الاشتراك غير فعال' }}
                                    </span>
                                </div>

                                <p class="mt-5 min-h-[72px] text-sm leading-7 text-[#c3c6d7]">
                                    {{ \Illuminate\Support\Str::limit(
                                        $office->description ?: 'لا توجد نبذة مضافة عن المكتب حتى الآن.',
                                        140
                                    ) }}
                                </p>

                                <div class="grid grid-cols-2 gap-3 mt-5">
                                    <div class="p-4 text-center rounded-2xl bg-white/[0.04]">
                                        <p class="text-xl font-black text-white">{{ $office->active_members_count ?? 0 }}</p>
                                        <p class="mt-1 text-xs text-[#8d90a0]">أعضاء فعالون</p>
                                    </div>

                                    <div class="p-4 text-center rounded-2xl bg-white/[0.04]">
                                        <p class="text-xl font-black text-white">{{ $office->consultations_count ?? 0 }}</p>
                                        <p class="mt-1 text-xs text-[#8d90a0]">استشارات</p>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('admin.offices.show', $office) }}"
                                    class="inline-flex items-center justify-center w-full px-5 py-3 mt-6 font-black text-[#00174b] transition rounded-xl bg-[#b4c5ff] hover:bg-[#dbe1ff]"
                                >
                                    إدارة المكتب
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="p-12 text-center luxury-glass rounded-3xl md:col-span-2 xl:col-span-3">
                            <div class="flex items-center justify-center w-24 h-24 mx-auto text-5xl border rounded-3xl border-white/10 bg-white/5">
                                🏢
                            </div>
                            <h2 class="mt-6 text-2xl font-black text-white">لا توجد مكاتب مطابقة</h2>
                            <p class="mt-3 text-[#c3c6d7]">جرّب تغيير كلمة البحث أو حالة المكتب.</p>

                            <a
                                href="{{ route('admin.offices.index') }}"
                                class="inline-flex items-center justify-center px-6 py-3 mt-7 font-black text-[#b4c5ff] transition border rounded-xl border-[#b4c5ff]/30 hover:bg-[#b4c5ff]/10"
                            >
                                إعادة ضبط البحث
                            </a>
                        </div>
                    @endforelse
                </section>

                @if ($offices->hasPages())
                    <div class="p-5 mt-8 luxury-glass rounded-2xl">
                        {{ $offices->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
