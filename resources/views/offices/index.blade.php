<x-app-layout>
    @php
        $currentUser = auth()->user();
        $search = $search ?? request('search', '');
        $status = $status ?? request('status', '');

        $statistics = $statistics ?? [
            'all' => isset($offices) ? $offices->total() : 0,
            'active' => 0,
            'suspended' => 0,
        ];

        $indexRoute = $currentUser?->role === 'admin'
            && Route::has('admin.offices.index')
                ? 'admin.offices.index'
                : 'engineering-offices.index';

        $canOpenApplications =
            $currentUser?->role === 'engineer'
            && Route::has('office-membership-applications.mine');

        $profilePhoto = $currentUser?->profile_photo_path
            ?? $currentUser?->profile_photo
            ?? null;
    @endphp

    @push('styles')
        <link
            href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
            rel="stylesheet"
        >
    @endpush

    <style>
        .office-design {
            --primary-fixed-dim: #adc6ff;
            --surface-container-low: #131b2e;
            --on-primary-fixed-variant: #004395;
            --on-surface: #dae2fd;
            --secondary: #4edea3;
            --tertiary: #ffb95f;
            --outline: #8c909f;
            --outline-variant: #424754;
            --surface-container-lowest: #060e20;
            --primary: #adc6ff;
            --surface: #0b1326;
            --surface-container: #171f33;
            --primary-container: #4d8eff;
            --error: #ffb4ab;
            --surface-container-high: #222a3d;
            --surface-container-highest: #2d3449;
            min-height: 100vh;
            color: var(--on-surface);
            background: var(--surface);
            font-family: "Hanken Grotesk", "Almarai", sans-serif;
        }

        .office-design .tech-font {
            font-family: "JetBrains Mono", monospace;
        }

        .office-design .glass-panel {
            background: rgba(23, 31, 51, .60);
            border: 1px solid rgba(66, 71, 84, .40);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .office-design .office-card {
            background: var(--surface-container-low);
            border: 1px solid var(--outline-variant);
            transition: border-color .25s ease, transform .25s ease, box-shadow .25s ease;
        }

        .office-design .office-card:hover {
            transform: translateY(-4px);
            border-color: rgba(173, 198, 255, .55);
            box-shadow: 0 20px 45px rgba(0, 0, 0, .25);
        }

        .office-design .material-symbols-outlined {
            font-variation-settings:
                "FILL" 0,
                "wght" 400,
                "GRAD" 0,
                "opsz" 24;
        }
    </style>

    <div class="office-design" dir="rtl">
        <div class="flex min-h-screen">
            {{-- Sidebar --}}
            <aside class="fixed right-0 top-0 z-30 hidden h-screen w-64 flex-col border-l border-[#424754] bg-[#171f33] px-4 pb-6 pt-24 lg:flex">
                <nav class="flex flex-col flex-1 gap-2">
                    @if (Route::has('home'))
                        <a href="{{ route('home') }}" class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c2c6d6] transition hover:bg-[#222a3d] hover:text-[#adc6ff]">
                            <span class="material-symbols-outlined text-[20px]">home</span>
                            <span class="font-bold">الرئيسية</span>
                        </a>
                    @endif

                    @if (Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c2c6d6] transition hover:bg-[#222a3d] hover:text-[#adc6ff]">
                            <span class="material-symbols-outlined text-[20px]">dashboard</span>
                            <span class="font-bold">لوحة التحكم</span>
                        </a>
                    @endif

                    @if (Route::has('engineer.works.public'))
                        <a href="{{ route('engineer.works.public') }}" class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c2c6d6] transition hover:bg-[#222a3d] hover:text-[#adc6ff]">
                            <span class="material-symbols-outlined text-[20px]">library_books</span>
                            <span class="font-bold">المكتبة الهندسية</span>
                        </a>
                    @endif

                    @if (Route::has('consultations.mine'))
                        <a href="{{ route('consultations.mine') }}" class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c2c6d6] transition hover:bg-[#222a3d] hover:text-[#adc6ff]">
                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                            <span class="font-bold">طلباتي</span>
                        </a>
                    @endif

                    <a href="{{ route($indexRoute) }}" class="flex items-center gap-4 rounded-lg bg-[#00a572]/80 px-4 py-3 font-bold text-[#00311f] transition hover:brightness-110">
                        <span class="material-symbols-outlined text-[20px]">domain</span>
                        <span>المكاتب</span>
                    </a>

                    @if ($canOpenApplications)
                        <a href="{{ route('office-membership-applications.mine') }}" class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c2c6d6] transition hover:bg-[#222a3d] hover:text-[#adc6ff]">
                            <span class="material-symbols-outlined text-[20px]">fact_check</span>
                            <span class="font-bold">طلبات انضمامي</span>
                        </a>
                    @endif
                </nav>
            </aside>

            {{-- Header --}}
            <header class="fixed inset-x-0 top-0 z-40 border-b border-[#424754] bg-[#0b1326]/95 backdrop-blur-xl lg:right-64">
                <div class="mx-auto flex h-20 max-w-[1200px] items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg border border-[#424754] bg-[#222a3d]">
                            <span class="material-symbols-outlined text-[#adc6ff]">architecture</span>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-[#dae2fd]">مكتب الوليد الهندسي</p>
                            <p class="tech-font text-xs text-[#c2c6d6]">منصة الاستشارات الهندسية</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('notifications.index'))
                            <a href="{{ route('notifications.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full transition hover:bg-[#222a3d]">
                                <span class="material-symbols-outlined">notifications</span>
                            </a>
                        @endif

                        <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" class="flex items-center gap-3 rounded-full border border-[#424754] bg-[#131b2e] px-3 py-1.5 transition hover:bg-[#222a3d]">
                            <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-[#424754] bg-[#222a3d]">
                                @if ($profilePhoto)
                                    <img src="{{ asset('storage/' . $profilePhoto) }}" alt="{{ $currentUser?->name }}" class="object-cover w-full h-full">
                                @else
                                    <span class="font-black text-[#adc6ff]">{{ mb_substr($currentUser?->name ?? 'م', 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-bold">{{ $currentUser?->name }}</p>
                                <p class="tech-font text-xs text-[#adc6ff]">
                                    {{ match ($currentUser?->role) {
                                        'admin' => 'مدير النظام',
                                        'engineer' => 'مهندس',
                                        'office_owner' => 'مالك مكتب',
                                        default => 'مستخدم',
                                    } }}
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="w-full px-4 pb-12 pt-28 sm:px-6 lg:mr-64">
                <div class="mx-auto max-w-[1200px]">
                    @if (session('success'))
                        <div class="p-4 mb-6 text-green-100 border rounded-xl border-green-500/30 bg-green-500/10">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-6 text-red-100 border rounded-xl border-red-500/30 bg-red-500/10">
                            {{ session('error') }}
                        </div>
                    @endif

                    <section class="mb-10">
                        <p class="tech-font mb-2 text-sm font-bold text-[#adc6ff]">
                            {{ $currentUser?->role === 'admin' ? 'إدارة النظام' : 'دليل المكاتب' }}
                        </p>
                        <h1 class="text-3xl font-bold text-[#dae2fd] sm:text-4xl">المكاتب الهندسية</h1>
                        <p class="mt-3 text-lg text-[#c2c6d6]">
                            استعرض المكاتب الهندسية، راجع بياناتها، وتابع حالة التفعيل والإيقاف والاشتراك.
                        </p>
                    </section>

                    <section class="grid grid-cols-1 gap-4 mb-10 md:grid-cols-3">
                        <article class="p-5 text-center glass-panel rounded-xl">
                            <p class="text-4xl font-bold tech-font">{{ $statistics['all'] ?? 0 }}</p>
                            <p class="mt-2 text-sm text-[#c2c6d6]">جميع المكاتب</p>
                        </article>
                        <article class="p-5 text-center glass-panel rounded-xl border-green-500/20">
                            <p class="tech-font text-4xl font-bold text-[#4edea3]">{{ $statistics['active'] ?? 0 }}</p>
                            <p class="mt-2 text-sm text-[#c2c6d6]">فعالة</p>
                        </article>
                        <article class="p-5 text-center glass-panel rounded-xl">
                            <p class="tech-font text-4xl font-bold text-[#ffb95f]">{{ $statistics['suspended'] ?? 0 }}</p>
                            <p class="mt-2 text-sm text-[#c2c6d6]">موقوفة</p>
                        </article>
                    </section>

                    <section class="p-6 mb-10 glass-panel rounded-xl">
                        <form method="GET" action="{{ route($indexRoute) }}" class="flex flex-col items-end gap-4 md:flex-row">
                            <div class="flex-1 w-full">
                                <label for="search" class="mb-2 block text-sm font-bold text-[#c2c6d6]">بحث</label>
                                <input
                                    id="search"
                                    name="search"
                                    type="search"
                                    value="{{ $search }}"
                                    placeholder="اسم المكتب، المدينة أو الدولة"
                                    class="h-12 w-full rounded-lg border border-[#424754] bg-[#060e20] px-4 text-right text-[#dae2fd] placeholder:text-[#8c909f] focus:border-[#adc6ff] focus:ring-[#adc6ff]"
                                >
                            </div>

                            <div class="w-full md:w-64">
                                <label for="status" class="mb-2 block text-sm font-bold text-[#c2c6d6]">الحالة</label>
                                <select
                                    id="status"
                                    name="status"
                                    class="h-12 w-full rounded-lg border border-[#424754] bg-[#060e20] px-4 text-[#dae2fd] focus:border-[#adc6ff] focus:ring-[#adc6ff]"
                                >
                                    <option value="">جميع الحالات</option>
                                    <option value="active" @selected($status === 'active')>فعال</option>
                                    <option value="suspended" @selected($status === 'suspended')>موقوف</option>
                                    <option value="closed" @selected($status === 'closed')>مغلق</option>
                                </select>
                            </div>

                            <div class="flex w-full h-12 gap-2 md:w-auto">
                                <button type="submit" class="min-w-[100px] rounded-lg bg-[#4d8eff] px-6 font-bold text-[#00285d] transition hover:brightness-110">
                                    تطبيق
                                </button>
                                <a href="{{ route($indexRoute) }}" class="inline-flex min-w-[100px] items-center justify-center rounded-lg border border-[#424754] bg-[#131b2e] px-6 font-bold transition hover:bg-[#222a3d]">
                                    مسح
                                </a>
                            </div>
                        </form>
                    </section>

                    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @forelse ($offices as $office)
                            @php
                                $isOwner =
                                    $currentUser?->role === 'office_owner'
                                    && (int) $currentUser?->id === (int) $office->owner_user_id;

                                $viewProfile =
                                    in_array($currentUser?->role, ['admin', 'engineer'], true);

                                $buttonRoute = $isOwner
                                    ? (Route::has('office.profile') ? route('office.profile') : '#')
                                    : ($viewProfile && Route::has('engineering-offices.show')
                                        ? route('engineering-offices.show', $office)
                                        : '#');

                                $buttonText = $isOwner
                                    ? 'إدارة المكتب'
                                    : 'عرض الملف الشخصي';

                                $buttonIcon = $isOwner ? 'settings' : 'visibility';
                            @endphp

                            <article class="flex overflow-hidden office-card rounded-xl">
                                <div class="flex flex-col w-full">
                                    <div class="relative h-32 border-b border-[#424754] bg-[#2d3449]">
                                        @if ($office->cover_path)
                                            <img src="{{ asset('storage/' . $office->cover_path) }}" alt="{{ $office->name }}" class="object-cover w-full h-full">
                                            <div class="absolute inset-0 bg-[#0b1326]/35"></div>
                                        @endif

                                        <div class="absolute right-4 top-4 flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold backdrop-blur-md
                                            {{ $office->status === 'active'
                                                ? 'border-green-500/30 bg-green-500/10 text-[#4edea3]'
                                                : 'border-amber-500/30 bg-amber-500/10 text-[#ffb95f]' }}">
                                            <span class="h-2 w-2 rounded-full {{ $office->status === 'active' ? 'bg-[#4edea3]' : 'bg-[#ffb95f]' }}"></span>
                                            {{ $office->status === 'active' ? 'مكتب فعال' : 'مكتب موقوف' }}
                                        </div>

                                        <div class="absolute -bottom-8 left-1/2 flex h-16 w-16 -translate-x-1/2 items-center justify-center overflow-hidden rounded-xl border border-[#424754] bg-[#0b1326] p-1 shadow-md">
                                            @if ($office->logo_path)
                                                <img src="{{ asset('storage/' . $office->logo_path) }}" alt="{{ $office->name }}" class="object-cover w-full h-full rounded-lg">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center rounded-lg bg-[#4d8eff]/20 text-xl font-bold text-[#adc6ff]">
                                                    {{ mb_substr($office->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center flex-1 px-6 pt-12 pb-5 text-center">
                                        <h2 class="text-xl font-bold">{{ $office->name }}</h2>
                                        <p class="mt-1 flex items-center justify-center gap-1 text-xs text-[#c2c6d6]">
                                            <span class="material-symbols-outlined text-[16px]">location_on</span>
                                            {{ $office->city ?: 'مدينة غير محددة' }}
                                            @if ($office->country)
                                                — {{ $office->country }}
                                            @endif
                                        </p>

                                        <div class="mt-6 w-full border-t border-[#424754]/50 pt-4">
                                            <p class="mb-4 text-right text-xs text-[#c2c6d6]">
                                                المالك:
                                                <span class="mr-1 font-bold text-[#dae2fd]">{{ $office->owner?->name ?? 'غير معروف' }}</span>
                                            </p>

                                            <div class="flex gap-2">
                                                <div class="flex-1 rounded-lg border border-[#424754]/30 bg-[#171f33] p-3">
                                                    <p class="text-xl font-bold tech-font">{{ $office->active_members_count ?? 0 }}</p>
                                                    <p class="mt-1 text-xs text-[#c2c6d6]">أعضاء فعالون</p>
                                                </div>
                                                <div class="flex-1 rounded-lg border border-[#424754]/30 bg-[#171f33] p-3">
                                                    <p class="text-xl font-bold tech-font">{{ $office->consultations_count ?? 0 }}</p>
                                                    <p class="mt-1 text-xs text-[#c2c6d6]">استشارات</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($isOwner || $viewProfile)
                                        <div class="px-4 pb-4">
                                            <a href="{{ $buttonRoute }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#4d8eff] py-3 font-bold text-[#00285d] transition hover:brightness-110">
                                                <span class="material-symbols-outlined text-[18px]">{{ $buttonIcon }}</span>
                                                {{ $buttonText }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="text-center glass-panel col-span-full rounded-xl p-14">
                                <span class="material-symbols-outlined text-6xl text-[#adc6ff]">domain_disabled</span>
                                <h2 class="mt-4 text-xl font-bold">لا توجد مكاتب مطابقة</h2>
                                <p class="mt-2 text-[#c2c6d6]">جرّب تغيير البحث أو حالة المكتب.</p>
                            </div>
                        @endforelse
                    </section>

                    @if ($offices->hasPages())
                        <div class="p-5 mt-8 glass-panel rounded-xl">
                            {{ $offices->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
