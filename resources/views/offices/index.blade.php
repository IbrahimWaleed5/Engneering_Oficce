<x-app-layout>
    @php
        $currentUser = auth()->user();
    @endphp

    <style>
        .offices-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .offices-glass {
            background: rgba(23, 31, 51, .55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .06);
        }

        .office-card {
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .office-card:hover {
            transform: translateY(-4px);
            border-color: rgba(180, 197, 255, .22);
            box-shadow: 0 18px 45px rgba(0, 0, 0, .24);
        }

        @media (max-width: 1023px) {
            .offices-sidebar {
                display: none !important;
            }

            .offices-main {
                margin-right: 0 !important;
            }

            .offices-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="offices-page" dir="rtl">
        <aside class="offices-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#131b2e]/90 p-4 shadow-xl backdrop-blur-xl">
            <div class="px-4 mb-10">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">CreativeHome</h1>
                <p class="text-sm text-[#c3c6d7] opacity-60">Engineering Office</p>
            </div>

            <nav class="flex-1 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white">
                    <span>لوحة التحكم</span>
                </a>

                <a href="{{ route('engineering-offices.index') }}" class="flex items-center gap-3 rounded-xl bg-[#2563eb]/20 px-4 py-3 font-bold text-[#b4c5ff]">
                    <span>المكاتب الهندسية</span>
                </a>

                @auth
                    @if ($currentUser?->role === 'engineer' && Route::has('office-membership-applications.mine'))
                        <a href="{{ route('office-membership-applications.mine') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white">
                            <span>طلبات انضمامي</span>
                        </a>
                    @endif
                @endauth

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white">
                    <span>الإعدادات</span>
                </a>
            </nav>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="pt-6 mt-auto border-t border-[#434655]/10">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 text-right text-[#c3c6d7] transition hover:text-red-300">
                        تسجيل الخروج
                    </button>
                </form>
            @endauth
        </aside>

        <header class="offices-topbar fixed top-0 left-0 right-64 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#060e20]/60 px-6 backdrop-blur-md">
            <div>
                <h2 class="text-2xl font-black text-[#dae2fd]">دليل المكاتب الهندسية</h2>
            </div>

            @auth
                <div class="text-sm text-[#c3c6d7]">
                    {{ $currentUser->name }}
                </div>
            @else
                <a href="{{ route('login') }}" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-bold text-white">
                    تسجيل الدخول
                </a>
            @endauth
        </header>

        <main class="min-h-screen px-6 pt-24 pb-12 offices-main lg:mr-64">
            <div class="mx-auto max-w-[1500px] space-y-8">
                <section class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-bold text-[#b4c5ff]">استكشف المكاتب</p>
                        <h1 class="mt-2 text-3xl font-black text-white">المكاتب الهندسية المعتمدة</h1>
                        <p class="mt-3 max-w-2xl leading-7 text-[#c3c6d7]">
                            تصفح المكاتب الهندسية، اطّلع على تخصصاتها وأعضائها وحالة عملها الحالية.
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="px-5 py-4 text-center offices-glass rounded-2xl">
                            <p class="text-2xl font-black text-white">{{ $statistics['all'] ?? 0 }}</p>
                            <p class="mt-1 text-xs text-[#8d90a0]">جميع المكاتب</p>
                        </div>

                        <div class="px-5 py-4 text-center offices-glass rounded-2xl">
                            <p class="text-2xl font-black text-green-300">{{ $statistics['active'] ?? 0 }}</p>
                            <p class="mt-1 text-xs text-[#8d90a0]">فعالة</p>
                        </div>

                        <div class="px-5 py-4 text-center offices-glass rounded-2xl">
                            <p class="text-2xl font-black text-amber-300">{{ $statistics['suspended'] ?? 0 }}</p>
                            <p class="mt-1 text-xs text-[#8d90a0]">موقوفة</p>
                        </div>
                    </div>
                </section>

                <section class="p-6 offices-glass rounded-3xl">
                    <form method="GET" action="{{ route('engineering-offices.index') }}" class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
                        <div>
                            <label for="search" class="mb-2 block text-sm font-bold text-[#c3c6d7]">بحث</label>
                            <input
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                type="search"
                                placeholder="اسم المكتب، المدينة أو الدولة"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white placeholder:text-[#8d90a0] focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-bold text-[#c3c6d7]">الحالة</label>
                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#131b2e] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="">جميع الحالات</option>
                                <option value="active" @selected($status === 'active')>فعال</option>
                                <option value="suspended" @selected($status === 'suspended')>موقوف</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 font-bold text-white transition hover:brightness-110">
                                تطبيق
                            </button>

                            <a href="{{ route('engineering-offices.index') }}" class="rounded-xl border border-[#434655] px-5 py-3 font-bold text-[#c3c6d7] transition hover:bg-[#2d3449]">
                                مسح
                            </a>
                        </div>
                    </form>
                </section>

                <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($offices as $office)
                        <article class="overflow-hidden office-card offices-glass rounded-3xl">
                            <div class="relative h-40 overflow-hidden bg-gradient-to-br from-[#17213a] to-[#0f1729]">
                                @if ($office->cover_path)
                                    <img
                                        src="{{ asset('storage/' . $office->cover_path) }}"
                                        alt="{{ $office->name }}"
                                        class="object-cover w-full h-full"
                                    >
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326]/95 via-transparent to-transparent"></div>

                                <div class="absolute top-4 left-4">
                                    @if ($office->status === 'active')
                                        <span class="px-3 py-1 text-xs font-black text-green-300 border rounded-full border-green-500/20 bg-green-500/10">
                                            مكتب فعال
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-black border rounded-full border-amber-500/20 bg-amber-500/10 text-amber-300">
                                            مكتب موقوف
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="relative px-6 pb-6">
                                <div class="flex items-end gap-4 -mt-10">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-[#131b2e] bg-[#1f2940] text-2xl font-black text-[#b4c5ff]">
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
                                        <h3 class="text-xl font-black text-white truncate">{{ $office->name }}</h3>
                                        <p class="mt-1 text-xs text-[#8d90a0]">
                                            {{ $office->city ?: 'مدينة غير محددة' }}
                                            @if ($office->country)
                                                — {{ $office->country }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-5 line-clamp-3 min-h-[72px] text-sm leading-6 text-[#c3c6d7]">
                                    {{ $office->description ?: 'لا يوجد وصف متاح لهذا المكتب حتى الآن.' }}
                                </p>

                                <div class="grid grid-cols-2 gap-3 mt-5">
                                    <div class="rounded-2xl bg-white/[0.03] p-3 text-center">
                                        <p class="text-xl font-black text-white">{{ $office->active_members_count ?? 0 }}</p>
                                        <p class="mt-1 text-xs text-[#8d90a0]">أعضاء فعالون</p>
                                    </div>

                                    <div class="rounded-2xl bg-white/[0.03] p-3 text-center">
                                        <p class="text-xl font-black text-white">{{ $office->consultations_count ?? 0 }}</p>
                                        <p class="mt-1 text-xs text-[#8d90a0]">استشارات</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-3 mt-5">
                                    <div class="text-xs text-[#8d90a0]">
                                        المالك:
                                        <span class="font-bold text-[#c3c6d7]">{{ $office->owner?->name ?? 'غير معروف' }}</span>
                                    </div>

                                    <a
                                        href="{{ route('engineering-offices.show', $office) }}"
                                        class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:brightness-110"
                                    >
                                        عرض المكتب
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="offices-glass col-span-full rounded-3xl p-14 text-center text-[#c3c6d7]">
                            لا توجد مكاتب مطابقة لخيارات البحث الحالية.
                        </div>
                    @endforelse
                </section>

                @if ($offices->hasPages())
                    <div class="p-5 offices-glass rounded-2xl">
                        {{ $offices->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
