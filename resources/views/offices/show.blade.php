<x-app-layout>
    @php
        $currentUser = auth()->user();

        $isSuspended = $office->status === 'suspended';

        $isSubscriptionActive =
            $office->subscription_status === 'active'
            && $office->subscription_ends_at
            && $office->subscription_ends_at->isFuture();

        $officeStatus = $isSuspended
            ? [
                'label' => 'مكتب موقوف',
                'class' => 'text-[#ffb4ab] border-[#93000a]/60 bg-[#93000a]/20',
                'dot' => 'bg-[#ffb4ab]',
            ]
            : [
                'label' => 'مكتب معتمد',
                'class' => 'text-[#4edea3] border-[#00a572]/40 bg-[#00a572]/15',
                'dot' => 'bg-[#4edea3]',
            ];

        $logoPath = $office->logo_path
            ? asset('storage/' . $office->logo_path)
            : null;

        $coverPath = $office->cover_path
            ? asset('storage/' . $office->cover_path)
            : null;

        $officeCode = 'ENG-' . str_pad((string) $office->id, 4, '0', STR_PAD_LEFT);

        $canRequestConsultation =
            Route::has('consultations.create')
            && in_array($currentUser?->role, ['customer', 'engineer', 'admin'], true);

        $backRoute = Route::has('engineering-offices.index')
            ? route('engineering-offices.index')
            : (Route::has('dashboard') ? route('dashboard') : url('/'));

        $footerYear = now()->year;
    @endphp

    @push('styles')
@endpush

    <style>
        .office-profile-page {
            --surface: #0b1326;
            --surface-lowest: #060e20;
            --surface-low: #131b2e;
            --surface-container: #171f33;
            --surface-high: #222a3d;
            --surface-highest: #2d3449;
            --outline: #8c909f;
            --outline-variant: #424754;
            --primary: #adc6ff;
            --primary-container: #4d8eff;
            --on-primary-container: #00285d;
            --secondary: #4edea3;
            --secondary-container: #00a572;
            --tertiary: #ffb95f;
            --on-surface: #dae2fd;
            --on-surface-variant: #c2c6d6;
            min-height: 100vh;
            background: #020617;
            color: var(--on-surface);
            font-family: "Hanken Grotesk", "Almarai", sans-serif;
        }

        .office-profile-page .tech-font {
            font-family: "JetBrains Mono", monospace;
        }

        .office-profile-page .glass-card {
            background: #0f172a;
            border: 1px solid #1e293b;
        }

        .office-profile-page .glow-hover {
            transition:
                border-color .25s ease,
                box-shadow .25s ease,
                transform .25s ease;
        }

        .office-profile-page .glow-hover:hover {
            border-color: #4d8eff;
            box-shadow: inset 0 0 20px rgba(59, 130, 246, .15);
            transform: translateY(-2px);
        }

        .office-profile-page .tech-grid-bg {
            background-image:
                linear-gradient(to right, rgba(30, 41, 59, .3) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(30, 41, 59, .3) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .office-profile-page::-webkit-scrollbar {
            width: 8px;
        }

        .office-profile-page::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .office-profile-page::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 4px;
        }

        .office-profile-page::-webkit-scrollbar-thumb:hover {
            background: #424754;
        }

        /* تثبيت أحجام SVG ومنع الحجم الافتراضي الضخم للمتصفح */
        svg[aria-hidden="true"] {
            display: inline-block;
            max-width: 100%;
            vertical-align: middle;
        }

    </style>

    <div class="office-profile-page" dir="rtl">
        {{-- شريط علوي مطابق للتصميم المرجعي --}}
        <header class="fixed top-0 z-50 w-full border-b border-[#424754] bg-[#0b1326]/95 backdrop-blur-xl">
            <div class="mx-auto flex min-h-[72px] max-w-[1200px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <a href="{{ $backRoute }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-[#424754] bg-[#171f33] text-[#adc6ff]">
                        <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21h16M6 21V9l6-5 6 5v12M9 21v-6h6v6M9 10h.01M12 10h.01M15 10h.01"/></svg>
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-xl font-bold tracking-tight text-[#adc6ff]">
                            {{ $office->name }}
                        </p>
                        <p class="tech-font truncate text-xs text-[#c2c6d6]">
                            ملف المكتب الهندسي
                        </p>
                    </div>
                </a>

                <nav class="hidden items-center gap-6 md:flex">
                    <a href="#about" class="border-b-2 border-[#adc6ff] pb-1 text-sm font-bold text-[#adc6ff] transition hover:bg-[#222a3d]">
                        نبذة
                    </a>
                    <a href="#team" class="text-sm font-bold text-[#c2c6d6] transition hover:text-[#adc6ff]">
                        المهندسون
                    </a>
                    <a href="#contact" class="text-sm font-bold text-[#c2c6d6] transition hover:text-[#adc6ff]">
                        التواصل
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    @if ($canRequestConsultation)
                        <a
                            href="{{ route('consultations.create') }}"
                            class="hidden rounded bg-[#adc6ff] px-4 py-2 text-sm font-bold text-[#002e6a] transition hover:brightness-110 sm:inline-flex"
                        >
                            طلب استشارة
                        </a>
                    @endif

                    @if (Route::has('profile.edit'))
                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-[#424754] bg-[#171f33] text-[#c2c6d6] transition hover:border-[#adc6ff] hover:text-[#adc6ff]"
                            title="إعدادات الحساب"
                        >
                            <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <main class="pb-12 pt-[72px]">
            {{-- رسائل النظام --}}
            <div class="mx-auto max-w-[1200px] px-4 pt-6 sm:px-6">
                @if (session('success'))
                    <div class="mb-5 rounded border border-green-500/30 bg-green-500/10 p-4 text-green-100">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded border border-red-500/30 bg-red-500/10 p-4 text-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-5 rounded border border-cyan-500/30 bg-cyan-500/10 p-4 text-cyan-100">
                        {{ session('info') }}
                    </div>
                @endif
            </div>

            {{-- Hero --}}
            <section class="tech-grid-bg relative w-full border-b border-[#424754]">
                @if ($coverPath)
                    <div
                        class="absolute inset-0 bg-cover bg-center opacity-20"
                        style="background-image: url('{{ $coverPath }}')"
                    ></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-[#020617]/45 via-[#020617]/80 to-[#020617]"></div>
                @endif

                <div class="relative mx-auto max-w-[1200px] px-4 py-12 sm:px-6 md:py-20">
                    <div class="flex flex-col items-center gap-10 md:flex-row md:items-start">
                        <div class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[#424754] bg-[#060e20] md:h-48 md:w-48">
                            @if ($logoPath)
                                <img
                                    src="{{ $logoPath }}"
                                    alt="{{ $office->name }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <span class="text-6xl">🏢</span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col gap-3 text-center md:text-right">
                            <div class="flex flex-wrap items-center justify-center gap-2 md:justify-start">
                                <span class="tech-font rounded bg-[#2d3449] px-3 py-1 text-xs text-[#c2c6d6]">
                                    #{{ $officeCode }}
                                </span>

                                <span class="tech-font inline-flex items-center gap-1 rounded border px-3 py-1 text-xs {{ $officeStatus['class'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $officeStatus['dot'] }}"></span>
                                    {{ $officeStatus['label'] }}
                                </span>

                                <span class="tech-font rounded border px-3 py-1 text-xs {{ $isSubscriptionActive
                                    ? 'border-[#00a572]/40 bg-[#00a572]/15 text-[#4edea3]'
                                    : 'border-[#ca8100]/40 bg-[#ca8100]/15 text-[#ffb95f]' }}">
                                    {{ $isSubscriptionActive ? 'اشتراك فعال' : 'اشتراك غير فعال' }}
                                </span>
                            </div>

                            <h1 class="text-4xl font-bold leading-tight text-[#dae2fd] sm:text-5xl">
                                {{ $office->name }}
                            </h1>

                            <p class="max-w-3xl text-lg leading-8 text-[#c2c6d6]">
                                {{ $office->description
                                    ?: 'مكتب هندسي يقدم خدمات وحلولًا احترافية في المجالات الهندسية المختلفة.' }}
                            </p>

                            <div class="mt-3 flex flex-wrap justify-center gap-5 text-[#c2c6d6] md:justify-start">
                                <div class="flex items-center gap-2">
                                    <svg class="text-[#8c909f] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                    <span class="text-sm font-bold">
                                        {{ $office->city ?: 'مدينة غير محددة' }}
                                        @if ($office->country)
                                            — {{ $office->country }}
                                        @endif
                                    </span>
                                </div>

                                @if ($office->email)
                                    <a href="mailto:{{ $office->email }}" class="flex items-center gap-2 transition hover:text-[#adc6ff]">
                                        <svg class="text-[#8c909f] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                                        <span class="tech-font text-sm">{{ $office->email }}</span>
                                    </a>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-wrap justify-center gap-3 md:justify-start">
                                @guest
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex items-center gap-2 rounded bg-[#adc6ff] px-5 py-3 font-bold text-[#002e6a] transition hover:brightness-110"
                                    >
                                        <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                                        تسجيل الدخول لطلب الانضمام
                                    </a>
                                @else
                                    @if ($currentUser?->role === 'engineer')
                                        @if ($membership && $membership->status === 'active')
                                            <div class="inline-flex items-center gap-2 rounded border border-green-500/30 bg-green-500/10 px-5 py-3 font-bold text-green-100">
                                                <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 12 2 2 4-4"/><path d="M12 3l2.2 1.4 2.6-.1 1.1 2.4 2.2 1.4-.6 2.5.6 2.5-2.2 1.4-1.1 2.4-2.6-.1L12 21l-2.2-1.4-2.6.1-1.1-2.4-2.2-1.4.6-2.5-.6-2.5 2.2-1.4 1.1-2.4 2.6.1L12 3Z"/></svg>
                                                أنت عضو فعال في هذا المكتب
                                            </div>
                                        @elseif ($pendingApplication)
                                            <div class="inline-flex items-center gap-2 rounded border border-yellow-500/30 bg-yellow-500/10 px-5 py-3 font-bold text-yellow-100">
                                                <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h12M6 22h12M7 2c0 5 5 5 5 10s-5 5-5 10M17 2c0 5-5 5-5 10s5 5 5 10"/></svg>
                                                طلب انضمامك قيد المراجعة
                                            </div>
                                        @elseif ($canApply)
                                            <a
                                                href="{{ route('office-membership-applications.create', $office) }}"
                                                class="inline-flex items-center gap-2 rounded bg-[#adc6ff] px-5 py-3 font-bold text-[#002e6a] transition hover:brightness-110"
                                            >
                                                <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M19 8v6M16 11h6"/></svg>
                                                طلب الانضمام إلى المكتب
                                            </a>
                                        @elseif ($isSuspended)
                                            <div class="rounded border border-red-500/30 bg-red-500/10 px-5 py-3 font-bold text-red-100">
                                                المكتب موقوف ولا يستقبل طلبات انضمام
                                            </div>
                                        @elseif (! $isSubscriptionActive)
                                            <div class="rounded border border-yellow-500/30 bg-yellow-500/10 px-5 py-3 font-bold text-yellow-100">
                                                اشتراك المكتب غير فعال حاليًا
                                            </div>
                                        @else
                                            <div class="rounded border border-[#424754] bg-[#171f33] px-5 py-3 font-bold text-[#c2c6d6]">
                                                طلب الانضمام غير متاح حاليًا
                                            </div>
                                        @endif
                                    @else
                                        <div class="rounded border border-[#4d8eff]/30 bg-[#4d8eff]/10 px-5 py-3 font-bold text-[#adc6ff]">
                                            طلبات الانضمام مخصصة للمهندسين
                                        </div>
                                    @endif
                                @endguest

                                <a
                                    href="{{ $backRoute }}"
                                    class="inline-flex items-center gap-2 rounded border border-[#424754] bg-[#171f33] px-5 py-3 font-bold text-[#dae2fd] transition hover:border-[#adc6ff] hover:text-[#adc6ff]"
                                >
                                    <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                                    جميع المكاتب
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- التنبيهات --}}
            @if ($isSuspended || ! $isSubscriptionActive)
                <section class="mx-auto max-w-[1200px] px-4 pt-8 sm:px-6">
                    @if ($isSuspended)
                        <div class="rounded-lg border border-red-500/30 bg-red-500/10 p-6">
                            <h2 class="text-xl font-bold text-red-200">المكتب موقوف عن العمل</h2>
                            <p class="mt-3 leading-8 text-red-100">
                                لا يستطيع المكتب استقبال استشارات أو طلبات انضمام جديدة حاليًا.
                            </p>

                            @if ($office->suspension_reason)
                                <div class="mt-4 rounded border border-red-500/20 bg-red-950/20 p-4">
                                    <p class="text-xs font-bold text-red-200">سبب الإيقاف</p>
                                    <p class="mt-2 leading-7 text-red-100">{{ $office->suspension_reason }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="rounded-lg border border-yellow-500/30 bg-yellow-500/10 p-6">
                            <h2 class="text-xl font-bold text-yellow-200">اشتراك المكتب غير فعال</h2>
                            <p class="mt-3 leading-8 text-yellow-100">
                                يمكن مشاهدة الملف الشخصي للمكتب، لكن تقديم طلب الانضمام غير متاح حتى يتم تفعيل الاشتراك.
                            </p>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Stats --}}
            <section class="mx-auto max-w-[1200px] px-4 py-10 sm:px-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <article class="glass-card glow-hover flex items-center gap-4 rounded-lg p-5">
                        <div class="flex h-12 w-12 items-center justify-center rounded bg-[#4d8eff]/10 text-[#adc6ff]">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20a7 7 0 0 1 14 0M14 16a6 6 0 0 1 8 4"/></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-bold">{{ $office->active_members_count ?? 0 }}</p>
                            <p class="tech-font mt-1 text-sm text-[#8c909f]">أعضاء المكتب</p>
                        </div>
                    </article>

                    <article class="glass-card glow-hover flex items-center gap-4 rounded-lg p-5">
                        <div class="flex h-12 w-12 items-center justify-center rounded bg-[#00a572]/10 text-[#4edea3]">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21h16M6 21V9l6-5 6 5v12M9 21v-6h6v6M9 10h.01M12 10h.01M15 10h.01"/></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-bold">{{ $office->consultations_count ?? 0 }}</p>
                            <p class="tech-font mt-1 text-sm text-[#8c909f]">استشارات محولة</p>
                        </div>
                    </article>

                    <article class="glass-card glow-hover flex items-center gap-4 rounded-lg p-5">
                        <div class="flex h-12 w-12 items-center justify-center rounded bg-[#ca8100]/10 text-[#ffb95f]">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 12 2 2 4-4"/><path d="M12 3l2.2 1.4 2.6-.1 1.1 2.4 2.2 1.4-.6 2.5.6 2.5-2.2 1.4-1.1 2.4-2.6-.1L12 21l-2.2-1.4-2.6.1-1.1-2.4-2.2-1.4.6-2.5-.6-2.5 2.2-1.4 1.1-2.4 2.6.1L12 3Z"/></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">
                                {{ $office->status === 'active' ? 'فعال' : 'موقوف' }}
                            </p>
                            <p class="tech-font mt-1 text-sm text-[#8c909f]">حالة المكتب</p>
                        </div>
                    </article>
                </div>
            </section>

            {{-- About --}}
            <section id="about" class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6">
                <div class="mb-6 flex items-center gap-2">
                    <svg class="text-[#adc6ff] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    <h2 class="text-2xl font-bold text-[#dae2fd]">نبذة عن المكتب</h2>
                </div>

                <div class="glass-card glow-hover rounded-lg p-6 sm:p-8">
                    <p class="text-lg leading-9 text-[#c2c6d6]">
                        {{ $office->description
                            ?: 'لم يضف المكتب نبذة تعريفية حتى الآن.' }}
                    </p>
                </div>
            </section>

            {{-- Team --}}
            <section id="team" class="mx-auto max-w-[1200px] px-4 py-10 sm:px-6">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <svg class="text-[#adc6ff] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5 5L3 18v3h3l6.7-6.7a4 4 0 0 0 5-5l-2.4 2.4-3-3 2.4-2.4Z"/><path d="m15 15 6 6"/></svg>
                        <h2 class="text-2xl font-bold text-[#dae2fd]">فريق المكتب</h2>
                    </div>

                    <span class="tech-font rounded border border-[#4d8eff]/30 bg-[#4d8eff]/10 px-3 py-1 text-sm text-[#adc6ff]">
                        {{ $office->active_members_count ?? 0 }} عضو
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @forelse ($office->activeMembers as $member)
                        <a
                            href="{{ route('engineers.show', $member->user) }}"
                            class="glass-card glow-hover flex items-center gap-4 rounded-lg p-5"
                        >
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded border border-[#424754] bg-[#222a3d]">
                                @php
                                    $memberPhoto =
                                        $member->user?->profile_photo_path
                                        ?? $member->user?->profile_photo
                                        ?? null;
                                @endphp

                                @if ($memberPhoto)
                                    <img
                                        src="{{ asset('storage/' . $memberPhoto) }}"
                                        alt="{{ $member->user?->name }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <svg class="text-3xl text-[#adc6ff] shrink-0" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-lg font-bold text-[#dae2fd]">
                                    {{ $member->user?->name ?? 'مهندس غير متاح' }}
                                </p>
                                <p class="mt-1 text-sm text-[#c2c6d6]">
                                    {{ $member->position ?: 'مهندس' }}
                                </p>
                                <p class="tech-font mt-1 text-xs text-[#adc6ff]">
                                    {{ $member->specialty?->name ?: 'تخصص غير محدد' }}
                                </p>
                            </div>

                            <svg class="text-[#8c909f] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        </a>
                    @empty
                        <div class="glass-card col-span-full rounded-lg p-10 text-center">
                            <svg class="text-5xl text-[#8c909f] shrink-0" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M2 20a7 7 0 0 1 11-5.7M17 9a2.5 2.5 0 0 1 2.2 3.7M16 16a6 6 0 0 1 6 4M3 3l18 18"/></svg>
                            <p class="mt-3 text-[#c2c6d6]">
                                لا يوجد مهندسون ظاهرون في فريق المكتب حاليًا.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Contact and application status --}}
            <section id="contact" class="mx-auto max-w-[1200px] px-4 py-6 sm:px-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="glass-card rounded-lg p-6 lg:col-span-2">
                        <div class="mb-6 flex items-center gap-2">
                            <svg class="text-[#adc6ff] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M5.5 17a3.5 3.5 0 0 1 7 0M14 8h4M14 12h4"/></svg>
                            <h2 class="text-2xl font-bold">معلومات المكتب</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="rounded border border-[#1e293b] bg-[#171f33] p-4">
                                <p class="tech-font text-xs text-[#8c909f]">البريد الإلكتروني</p>
                                <p class="mt-2 break-all font-bold">{{ $office->email ?: 'غير محدد' }}</p>
                            </div>

                            <div class="rounded border border-[#1e293b] bg-[#171f33] p-4">
                                <p class="tech-font text-xs text-[#8c909f]">رقم الهاتف</p>
                                <p class="mt-2 font-bold">{{ $office->phone ?: 'غير محدد' }}</p>
                            </div>

                            <div class="rounded border border-[#1e293b] bg-[#171f33] p-4">
                                <p class="tech-font text-xs text-[#8c909f]">العنوان</p>
                                <p class="mt-2 leading-7 font-bold">{{ $office->address ?: 'غير محدد' }}</p>
                            </div>

                            <div class="rounded border border-[#1e293b] bg-[#171f33] p-4">
                                <p class="tech-font text-xs text-[#8c909f]">رقم الترخيص</p>
                                <p class="mt-2 font-bold">{{ $office->license_number ?: 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @if ($latestApplication)
                            <div class="glass-card rounded-lg p-6">
                                <p class="tech-font text-sm text-[#8c909f]">آخر طلب انضمام لك</p>

                                <p class="mt-3 text-lg font-bold">
                                    {{ match ($latestApplication->status) {
                                        'approved' => 'تم قبول الطلب',
                                        'rejected' => 'تم رفض الطلب',
                                        'cancelled' => 'تم إلغاء الطلب',
                                        default => 'قيد المراجعة',
                                    } }}
                                </p>

                                @if (
                                    $latestApplication->status === 'rejected'
                                    && $latestApplication->rejection_reason
                                )
                                    <div class="mt-4 rounded border border-red-500/20 bg-red-500/10 p-4">
                                        <p class="text-xs font-bold text-red-200">سبب الرفض</p>
                                        <p class="mt-2 text-sm leading-7 text-red-100">
                                            {{ $latestApplication->rejection_reason }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <a
                            href="{{ $backRoute }}"
                            class="flex w-full items-center justify-center gap-2 rounded border border-[#424754] bg-[#171f33] px-5 py-3 font-bold transition hover:border-[#adc6ff] hover:text-[#adc6ff]"
                        >
                            <svg class="text-[20px] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V5h9v16M14 9h5v12M8 8h2M8 12h2M8 16h2M16 12h1M16 16h1"/></svg>
                            العودة إلى جميع المكاتب
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="mt-auto border-t border-[#424754] bg-[#060e20]">
            <div class="mx-auto flex max-w-[1200px] flex-col items-center justify-between gap-4 px-4 py-8 text-center sm:px-6 md:flex-row">
                <p class="tech-font text-sm uppercase tracking-wider text-[#adc6ff]">
                    © {{ $footerYear }} {{ $office->name }}
                </p>

                <div class="flex flex-wrap justify-center gap-5 text-sm text-[#8c909f]">
                    <a href="#about" class="transition hover:text-[#adc6ff]">نبذة</a>
                    <a href="#team" class="transition hover:text-[#adc6ff]">الفريق</a>
                    <a href="#contact" class="transition hover:text-[#adc6ff]">التواصل</a>
                </div>
            </div>
        </footer>
    </div>
</x-app-layout>
