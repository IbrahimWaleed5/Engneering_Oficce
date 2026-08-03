<x-app-layout>
@php
    $user = auth()->user();
    $dashboardUrl = auth()->check() ? route('dashboard') : route('home');
    $profileUrl = auth()->check() && Route::has('profile.edit') ? route('profile.edit') : route('login');
    $notificationsUrl = auth()->check() && Route::has('notifications.index') ? route('notifications.index') : $dashboardUrl;
    $supportUrl = Route::has('support.index') ? route('support.index') : $dashboardUrl;

    $visibleWorks = method_exists($works, 'getCollection')
        ? $works->getCollection()
        : collect($works);

    $worksTotal = method_exists($works, 'total')
        ? $works->total()
        : $visibleWorks->count();

    $engineersTotal = $visibleWorks
        ->pluck('engineer_id')
        ->filter()
        ->unique()
        ->count();

    $latestWork = $visibleWorks
        ->sortByDesc('updated_at')
        ->first();
@endphp

<style>
body > div.min-h-screen > nav,
body > div.min-h-screen > header,
body > div > nav.bg-white,
body > div > header.bg-white,
body nav[data-layout-navigation],
body header[data-layout-header] {
    display: none !important;
}

.works-page {
    min-height: 100vh;
    overflow-x: hidden;
    color: #dae2fd;
    background:
        linear-gradient(rgba(11,19,38,.95), rgba(11,19,38,.97)),
        radial-gradient(circle at 15% 5%, rgba(180,197,255,.12), transparent 35%),
        radial-gradient(circle at 95% 95%, rgba(255,177,199,.10), transparent 34%),
        #0b1326;
    font-family: "Noto Sans Arabic", "Be Vietnam Pro", sans-serif;
}

.works-page::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: .08;
    background-image:
        linear-gradient(rgba(180,197,255,.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(180,197,255,.08) 1px, transparent 1px);
    background-size: 44px 44px;
}

.works-glass {
    background: rgba(23,31,51,.74);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,.07);
    box-shadow: 0 8px 32px rgba(0,0,0,.32);
}

.works-card {
    transition: transform .28s ease, border-color .28s ease, box-shadow .28s ease;
}

.works-card:hover {
    transform: translateY(-5px);
    border-color: rgba(180,197,255,.22);
    box-shadow: 0 18px 40px rgba(0,0,0,.36);
}

.works-filter.is-active {
    color: #0b1326;
    border-color: #b4c5ff;
    background: #b4c5ff;
    box-shadow: 0 8px 24px rgba(180,197,255,.18);
}

.works-mobile-menu,
.works-mobile-backdrop {
    transition: transform .3s ease, opacity .3s ease;
}

@keyframes worksFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-14px); }
}

.works-float { animation: worksFloat 6s ease-in-out infinite; }

@media (max-width: 767px) {
    .works-desktop-sidebar,
    .works-desktop-header {
        display: none !important;
    }

    .works-main {
        margin-right: 0 !important;
        padding: 5.25rem .9rem 6rem !important;
    }

    .works-hero {
        padding: 1.25rem !important;
    }

    .works-filters {
        width: 100%;
        overflow-x: auto;
        flex-wrap: nowrap !important;
        padding-bottom: .35rem;
        scrollbar-width: none;
    }

    .works-filters::-webkit-scrollbar { display: none; }

    .works-filter { flex: 0 0 auto; }

    .works-grid { grid-template-columns: 1fr !important; }

    .works-card-image { height: 15rem !important; }

    .works-empty-visual {
        width: 11rem !important;
        height: 11rem !important;
    }
}
</style>

<div class="relative works-page" dir="rtl">
    {{-- Desktop sidebar --}}
    <aside class="works-desktop-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-white/10 bg-[#171f33] px-4 py-8 shadow-xl">
        <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 px-2 mb-10">
            <img src="{{ asset('images/Mainlogo.png') }}" alt="مكتب الوليد" class="object-contain h-11 w-11 rounded-xl">
            <div>
                <h1 class="text-xl font-black text-[#b4c5ff]">مكتب الوليد</h1>
                <p class="text-xs text-[#c3c6d7]">الاستشارات الهندسية</p>
            </div>
        </a>

        <nav class="flex-1 space-y-2">
            <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]">
                <span>▦</span><span>لوحة التحكم</span>
            </a>

            <a href="{{ route('engineer.works.public') }}" class="flex items-center gap-3 rounded-lg bg-blue-500/15 p-3 font-black text-[#b4c5ff]">
                <span>🏗️</span><span>المشاريع</span>
            </a>

            @auth
                @if ($user->role === 'engineer')
                    <a href="{{ route('engineer.works.mine') }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]">
                        <span>▱</span><span>أعمالي</span>
                    </a>
                @endif
            @endauth

            <a href="{{ route('consultations.create') }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]">
                <span>＋</span><span>طلب استشارة</span>
            </a>

            <a href="{{ $profileUrl }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-[#b4c5ff]">
                <span>⚙️</span><span>الإعدادات</span>
            </a>
        </nav>

        <div class="pt-6 space-y-2 border-t border-white/10">
            @auth
                @if ($user->role === 'engineer')
                    <a href="{{ route('engineer.works.create') }}" class="mb-5 block w-full rounded-xl bg-gradient-to-r from-pink-300 to-purple-300 px-4 py-3 text-center font-black text-[#0b1326]">
                        إضافة عمل جديد
                    </a>
                @endif
            @endauth

            <a href="{{ $supportUrl }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7] hover:text-[#b4c5ff]">
                <span>?</span><span>الدعم الفني</span>
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full gap-3 p-3 text-red-300 rounded-lg hover:bg-red-500/10">
                        <span>↪</span><span>تسجيل الخروج</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 rounded-lg p-3 text-[#c3c6d7]">
                    <span>⇥</span><span>تسجيل الدخول</span>
                </a>
            @endauth
        </div>
    </aside>

    {{-- Desktop header --}}
    <header class="works-desktop-header fixed left-0 right-64 top-0 z-40 flex h-20 items-center justify-between border-b border-white/10 bg-[#0b1326]/82 px-5 backdrop-blur-xl">
        <div class="flex items-center gap-6">
            <h2 class="text-xl font-black text-[#b4c5ff]">مكتبة أعمال المهندسين</h2>

            <div class="relative hidden lg:block">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#c3c6d7]">🔍</span>
                <input id="works-search-desktop" type="search" placeholder="البحث عن عمل، مهندس، أو قسم..." class="w-80 rounded-full border-0 bg-[#171f33] py-2 pl-10 pr-4 text-sm text-white placeholder:text-[#8d90a0] focus:ring-2 focus:ring-[#b4c5ff]">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ $notificationsUrl }}" class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] hover:bg-white/5">🔔</a>
            <a href="{{ $profileUrl }}" class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] hover:bg-white/5">◷</a>

            @auth
                <a href="{{ $profileUrl }}" class="flex items-center gap-3 rounded-full border border-white/10 bg-[#222a3d] px-2 py-1">
                    <div class="text-left">
                        <p class="text-xs font-black text-white">{{ $user->name }}</p>
                        <p class="mt-1 text-[10px] text-[#c3c6d7]">{{ $user->role === 'engineer' ? 'مهندس' : 'مستخدم' }}</p>
                    </div>

                    <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full border border-[#b4c5ff]/30 bg-blue-600 font-black text-white">
                        @if ($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                        @else
                            {{ mb_substr($user->name, 0, 1) }}
                        @endif
                    </div>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 font-black text-white bg-blue-600 rounded-xl">تسجيل الدخول</a>
            @endauth
        </div>
    </header>

    {{-- Mobile header --}}
    <header class="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/92 px-4 backdrop-blur-xl md:hidden">
        <button id="works-mobile-open" type="button" class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-white/5">☰</button>

        <div class="flex items-center gap-2">
            <img src="{{ asset('images/Mainlogo.png') }}" alt="مكتب الوليد" class="object-contain h-9 w-9 rounded-xl">
            <span class="text-sm font-black text-white">مكتبة الأعمال</span>
        </div>

        <a href="{{ $notificationsUrl }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5">🔔</a>
    </header>

    <div id="works-mobile-backdrop" class="works-mobile-backdrop fixed inset-0 z-[80] hidden bg-black/70 opacity-0 md:hidden"></div>

    <aside id="works-mobile-menu" class="works-mobile-menu pointer-events-none fixed right-0 top-0 z-[90] flex h-dvh w-[min(88vw,360px)] translate-x-full flex-col bg-[#171f33] p-5 opacity-0 shadow-2xl md:hidden" aria-hidden="true">
        <div class="flex items-center justify-between pb-4 border-b border-white/10">
            <div>
                <h2 class="font-black text-[#b4c5ff]">مكتب الوليد</h2>
                <p class="mt-1 text-xs text-[#c3c6d7]">الاستشارات الهندسية</p>
            </div>
            <button id="works-mobile-close" type="button" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5">✕</button>
        </div>

        <nav class="mt-6 space-y-2">
            <a href="{{ $dashboardUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">لوحة التحكم</a>
            <a href="{{ route('engineer.works.public') }}" class="block px-4 py-3 text-blue-200 rounded-xl bg-blue-500/15">مكتبة الأعمال</a>

            @auth
                @if ($user->role === 'engineer')
                    <a href="{{ route('engineer.works.mine') }}" class="block px-4 py-3 rounded-xl bg-white/5">أعمالي</a>
                    <a href="{{ route('engineer.works.create') }}" class="block px-4 py-3 text-purple-200 rounded-xl bg-purple-500/15">إضافة عمل جديد</a>
                @endif
            @endauth

            <a href="{{ route('consultations.create') }}" class="block px-4 py-3 rounded-xl bg-white/5">طلب استشارة</a>
            <a href="{{ $supportUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">الدعم الفني</a>
            <a href="{{ $profileUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">الإعدادات</a>
        </nav>

        @auth
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="w-full px-4 py-3 font-black text-red-300 rounded-xl bg-red-500/10">تسجيل الخروج</button>
            </form>
        @endauth
    </aside>

    {{-- Main --}}
    <main class="relative z-10 flex flex-col min-h-screen px-6 pt-24 pb-12 mr-64 works-main">
        <div class="w-full mx-auto space-y-8 max-w-7xl">
            <div class="relative md:hidden">
                <span class="absolute -translate-y-1/2 right-4 top-1/2">🔍</span>
                <input id="works-search-mobile" type="search" placeholder="ابحث عن عمل أو مهندس..." class="w-full rounded-2xl border border-white/10 bg-[#171f33] py-3 pl-4 pr-12 text-white placeholder:text-[#8d90a0]">
            </div>

            <section class="relative p-8 overflow-hidden works-hero works-glass rounded-3xl">
                <div class="relative z-10 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#b4c5ff]/20 bg-[#b4c5ff]/10 px-3 py-1">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-[#b4c5ff]"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#b4c5ff]">مساحة الإبداع الهندسي</span>
                        </div>

                        <h1 class="mb-4 text-3xl font-black text-white">أرشيف المشاريع الرقمي</h1>
                        <p class="leading-8 text-[#c3c6d7]">هنا تجتمع خبرات المهندسين. تصفح الأعمال المنشورة والمخططات والتصاميم المعتمدة.</p>
                    </div>

                    <div class="flex flex-wrap gap-2 works-filters">
                        <button type="button" data-work-filter="all" class="works-filter is-active rounded-full border border-white/10 bg-[#171f33] px-5 py-2 text-sm font-black">الكل</button>
                        <button type="button" data-work-filter="architecture" class="works-filter rounded-full border border-white/10 bg-[#171f33] px-5 py-2 text-sm">معماري</button>
                        <button type="button" data-work-filter="structural" class="works-filter rounded-full border border-white/10 bg-[#171f33] px-5 py-2 text-sm">إنشائي</button>
                        <button type="button" data-work-filter="interior" class="works-filter rounded-full border border-white/10 bg-[#171f33] px-5 py-2 text-sm">داخلي</button>
                        <button type="button" data-work-filter="electrical" class="works-filter rounded-full border border-white/10 bg-[#171f33] px-5 py-2 text-sm">كهربائي</button>
                    </div>
                </div>
            </section>

            @if (session('success'))
                <div id="works-success-alert" class="flex items-center justify-between p-4 border-r-4 border-green-500 works-glass rounded-xl bg-green-500/5">
                    <p class="font-bold text-green-100">{{ session('success') }}</p>
                    <button type="button" data-dismiss-alert="works-success-alert">✕</button>
                </div>
            @endif

            @if (session('error'))
                <div id="works-error-alert" class="flex items-center justify-between p-4 border-r-4 border-red-500 works-glass rounded-xl bg-red-500/5">
                    <p class="font-bold text-red-100">{{ session('error') }}</p>
                    <button type="button" data-dismiss-alert="works-error-alert">✕</button>
                </div>
            @endif

            <section id="works-grid" class="grid gap-6 works-grid md:grid-cols-2 xl:grid-cols-3">
                @forelse ($works as $work)
                    @php
                        $searchText = mb_strtolower(
                            ($work->title ?? '') . ' ' .
                            ($work->engineer?->name ?? '') . ' ' .
                            ($work->location ?? '') . ' ' .
                            ($work->project_type ?? '')
                        );

                        $projectType = mb_strtolower($work->project_type ?? '');
                        $filterType = 'other';

                        if (str_contains($projectType, 'معمار') || str_contains($projectType, 'architect')) {
                            $filterType = 'architecture';
                        } elseif (str_contains($projectType, 'إنشائ') || str_contains($projectType, 'struct')) {
                            $filterType = 'structural';
                        } elseif (str_contains($projectType, 'داخل') || str_contains($projectType, 'interior')) {
                            $filterType = 'interior';
                        } elseif (str_contains($projectType, 'كهرب') || str_contains($projectType, 'electric')) {
                            $filterType = 'electrical';
                        }
                    @endphp

                    <article class="overflow-hidden works-card works-glass group rounded-3xl" data-work-card data-work-search="{{ $searchText }}" data-work-type="{{ $filterType }}">
                        <div class="relative overflow-hidden works-card-image h-72">
                            @if ($work->coverImage)
                                <img src="{{ asset('storage/' . $work->coverImage->image_path) }}" alt="{{ $work->title }}" class="object-cover w-full h-full transition duration-700 group-hover:scale-110">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#222a3d] to-[#0b1326] text-7xl">🏢</div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326] via-transparent to-transparent"></div>

                            <div class="absolute flex flex-wrap gap-2 right-4 top-4">
                                @if ($work->project_type)
                                    <span class="rounded-full border border-white/10 bg-[#0b1326]/75 px-3 py-2 text-xs font-black backdrop-blur-xl">{{ $work->project_type }}</span>
                                @endif

                                @if ($work->completion_year)
                                    <span class="rounded-full border border-white/10 bg-[#0b1326]/75 px-3 py-2 text-xs font-black backdrop-blur-xl">{{ $work->completion_year }}</span>
                                @endif
                            </div>

                            <div class="absolute inset-x-5 bottom-5">
                                <h2 class="text-2xl font-black text-white">{{ $work->title }}</h2>
                                <div class="mt-3 flex items-center gap-2 text-sm text-[#c3c6d7]">
                                    <span>📍</span><span>{{ $work->location ?? 'الموقع غير محدد' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center min-w-0 gap-3">
                                    @if ($work->engineer?->profile_photo)
                                        <img src="{{ asset('storage/' . $work->engineer->profile_photo) }}" alt="{{ $work->engineer->name }}" class="h-12 w-12 shrink-0 rounded-full border border-[#b4c5ff]/20 object-cover">
                                    @else
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#b4c5ff]/20 bg-gradient-to-br from-blue-600 to-cyan-500 font-black text-white">
                                            {{ mb_substr($work->engineer?->name ?? 'م', 0, 1) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="font-black text-white truncate">{{ $work->engineer?->name ?? 'مهندس المكتب' }}</p>
                                        <p class="mt-1 text-xs text-[#c3c6d7]">مهندس معتمد في المنصة</p>

                                        @if ($work->engineer?->employeeProfile?->specialty)
                                            <span class="inline-flex px-3 py-1 mt-2 text-xs font-black border rounded-full border-cyan-500/20 bg-cyan-500/10 text-cyan-300">
                                                {{ $work->engineer->employeeProfile->specialty->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 font-black text-yellow-300"><span>★</span><span>5.0</span></div>
                            </div>

                            @if ($work->description)
                                <p class="mt-5 line-clamp-3 leading-7 text-[#c3c6d7]">{{ $work->description }}</p>
                            @endif

                            <div class="grid grid-cols-1 gap-3 mt-6 sm:grid-cols-2">
                                <a href="{{ route('engineer.works.show', $work) }}" class="flex items-center justify-center rounded-xl bg-[#b4c5ff] px-5 py-3 font-black text-[#0b1326] hover:opacity-90">عرض التفاصيل</a>

                                @auth
                                    @if ($user->role === 'customer' && $work->engineer)
                                        <a href="{{ route('consultations.create-for-engineer', $work->engineer) }}" class="flex items-center justify-center px-5 py-3 font-black text-white border rounded-xl border-white/10 bg-white/5 hover:bg-white/10">طلب المهندس</a>
                                    @elseif ($user->role === 'engineer')
                                        <span class="flex cursor-not-allowed items-center justify-center rounded-xl border border-white/10 bg-white/5 px-5 py-3 font-black text-[#c3c6d7] opacity-60">مكتبة عامة</span>
                                    @else
                                        <a href="{{ route('login') }}" class="flex items-center justify-center px-5 py-3 font-black text-white border rounded-xl border-white/10 bg-white/5">سجّل للطلب</a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center justify-center px-5 py-3 font-black text-white border rounded-xl border-white/10 bg-white/5">سجّل للطلب</a>
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full flex min-h-[450px] items-center justify-center">
                        <div class="w-full max-w-md space-y-6 text-center">
                            <div class="relative flex items-center justify-center w-64 h-64 mx-auto works-empty-visual">
                                <div class="absolute inset-0 animate-pulse rounded-full bg-[#b4c5ff]/20 blur-[60px]"></div>
                                <div class="relative z-10 works-float">
                                    <div class="works-glass flex h-48 w-48 rotate-12 items-center justify-center rounded-3xl border border-[#b4c5ff]/30 text-7xl">🏗️</div>
                                    <div class="absolute flex items-center justify-center w-32 h-32 text-5xl border works-glass -bottom-4 -right-4 -rotate-12 rounded-2xl border-pink-300/30">✏️</div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-2xl font-black text-white">لا توجد أعمال منشورة حاليًا</h2>
                                <p class="mt-3 leading-7 text-[#c3c6d7]">ستظهر هنا أعمال المهندسين بعد مراجعتها واعتمادها من الإدارة.</p>
                            </div>

                            @auth
                                @if ($user->role === 'engineer')
                                    <a href="{{ route('engineer.works.create') }}" class="mx-auto inline-flex items-center gap-3 rounded-2xl bg-[#b4c5ff] px-8 py-4 font-black text-[#0b1326] hover:scale-[1.03]">
                                        <span>＋</span><span>إضافة عمل جديد</span>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforelse
            </section>

            <div id="works-no-filter-results" class="hidden p-12 text-center works-glass rounded-3xl">
                <div class="mb-4 text-5xl">🔍</div>
                <h2 class="text-xl font-black text-white">لا توجد نتائج مطابقة</h2>
                <p class="mt-3 text-[#c3c6d7]">غيّر عبارة البحث أو اختر قسمًا آخر.</p>
            </div>

            @if (method_exists($works, 'hasPages') && $works->hasPages())
                <div class="mt-8">{{ $works->links() }}</div>
            @endif

            <section class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="flex items-center gap-4 p-5 works-glass rounded-2xl">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#b4c5ff]/10 text-[#b4c5ff]">▥</div>
                    <div><p class="text-xs text-[#c3c6d7]">إجمالي المشاريع</p><p class="mt-1 text-xl font-black text-white">{{ $worksTotal }} مشروع</p></div>
                </div>

                <div class="flex items-center gap-4 p-5 works-glass rounded-2xl">
                    <div class="flex items-center justify-center w-12 h-12 text-pink-300 rounded-xl bg-pink-300/10">✓</div>
                    <div><p class="text-xs text-[#c3c6d7]">المهندسون الظاهرون</p><p class="mt-1 text-xl font-black text-white">{{ $engineersTotal }} مهندس</p></div>
                </div>

                <div class="flex items-center gap-4 p-5 works-glass rounded-2xl">
                    <div class="flex items-center justify-center w-12 h-12 text-purple-300 rounded-xl bg-purple-300/10">◷</div>
                    <div><p class="text-xs text-[#c3c6d7]">آخر تحديث</p><p class="mt-1 text-xl font-black text-white">{{ $latestWork?->updated_at?->diffForHumans() ?? 'لا يوجد' }}</p></div>
                </div>
            </section>
        </div>
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-50 flex h-16 items-center justify-around border-t border-white/10 bg-[#2d3449]/92 px-3 backdrop-blur-lg md:hidden">
        <a href="{{ $dashboardUrl }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]"><span>⌂</span><span class="text-[9px]">الرئيسية</span></a>
        <a href="{{ route('engineer.works.public') }}" class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white shadow-[0_0_12px_rgba(37,99,235,.5)]">🏗️</a>
        <a href="{{ route('consultations.create') }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]"><span>＋</span><span class="text-[9px]">استشارة</span></a>
        <a href="{{ $profileUrl }}" class="flex flex-col items-center gap-1 text-[#c3c6d7]"><span>⚙️</span><span class="text-[9px]">الإعدادات</span></a>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const desktopSearch = document.getElementById('works-search-desktop');
    const mobileSearch = document.getElementById('works-search-mobile');
    const filterButtons = Array.from(document.querySelectorAll('[data-work-filter]'));
    const cards = Array.from(document.querySelectorAll('[data-work-card]'));
    const noResults = document.getElementById('works-no-filter-results');

    let activeFilter = 'all';

    function searchValue() {
        return (desktopSearch?.value || mobileSearch?.value || '')
            .trim()
            .toLocaleLowerCase('ar');
    }

    function applyFilters() {
        const query = searchValue();
        let visible = 0;

        cards.forEach(function (card) {
            const text = (card.dataset.workSearch || '').toLocaleLowerCase('ar');
            const type = card.dataset.workType || 'other';

            const matchesSearch = !query || text.includes(query);
            const matchesType = activeFilter === 'all' || type === activeFilter;
            const show = matchesSearch && matchesType;

            card.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        noResults?.classList.toggle(
            'hidden',
            visible !== 0 || cards.length === 0
        );
    }

    function sync(source, target) {
        if (target) target.value = source.value;
        applyFilters();
    }

    desktopSearch?.addEventListener('input', function () {
        sync(desktopSearch, mobileSearch);
    });

    mobileSearch?.addEventListener('input', function () {
        sync(mobileSearch, desktopSearch);
    });

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activeFilter = button.dataset.workFilter || 'all';

            filterButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });

            applyFilters();
        });
    });

    document.querySelectorAll('[data-dismiss-alert]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById(button.dataset.dismissAlert)?.remove();
        });
    });

    const openButton = document.getElementById('works-mobile-open');
    const closeButton = document.getElementById('works-mobile-close');
    const mobileMenu = document.getElementById('works-mobile-menu');
    const backdrop = document.getElementById('works-mobile-backdrop');

    function openMenu() {
        if (!mobileMenu || !backdrop) return;

        backdrop.classList.remove('hidden');

        requestAnimationFrame(function () {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');

            mobileMenu.classList.remove(
                'translate-x-full',
                'opacity-0',
                'pointer-events-none'
            );

            mobileMenu.classList.add('translate-x-0', 'opacity-100');
        });

        mobileMenu.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeMenu() {
        if (!mobileMenu || !backdrop) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');

        mobileMenu.classList.remove('translate-x-0', 'opacity-100');
        mobileMenu.classList.add(
            'translate-x-full',
            'opacity-0',
            'pointer-events-none'
        );

        mobileMenu.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');

        setTimeout(function () {
            backdrop.classList.add('hidden');
        }, 300);
    }

    openButton?.addEventListener('click', openMenu);
    closeButton?.addEventListener('click', closeMenu);
    backdrop?.addEventListener('click', closeMenu);

    mobileMenu?.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMenu();
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeMenu();
    });

    applyFilters();
});
</script>
</x-app-layout>
