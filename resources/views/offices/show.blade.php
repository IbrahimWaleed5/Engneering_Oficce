<x-app-layout>
    @php
        $currentUser = auth()->user();

        $statusLabel = match ($office->status) {
            'active' => 'مكتب فعال',
            'suspended' => 'مكتب موقوف',
            default => 'غير فعال',
        };

        $statusClass = match ($office->status) {
            'active' => 'border-green-500/20 bg-green-500/10 text-green-300',
            'suspended' => 'border-amber-500/20 bg-amber-500/10 text-amber-300',
            default => 'border-white/10 bg-white/5 text-slate-300',
        };
    @endphp

    <style>
        .office-profile-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .office-profile-glass {
            background: rgba(23, 31, 51, .55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .06);
        }

        @media (max-width: 1023px) {
            .office-profile-sidebar {
                display: none !important;
            }

            .office-profile-main {
                margin-right: 0 !important;
            }

            .office-profile-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="office-profile-page" dir="rtl">
        <aside class="office-profile-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#131b2e]/90 p-4 shadow-xl backdrop-blur-xl">
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

        <header class="office-profile-topbar fixed top-0 left-0 right-64 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#060e20]/60 px-6 backdrop-blur-md">
            <a href="{{ route('engineering-offices.index') }}" class="text-sm font-bold text-[#b4c5ff]">
                العودة إلى دليل المكاتب
            </a>

            @auth
                <div class="text-sm text-[#c3c6d7]">{{ $currentUser->name }}</div>
            @else
                <a href="{{ route('login') }}" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-bold text-white">
                    تسجيل الدخول
                </a>
            @endauth
        </header>

        <main class="min-h-screen px-6 pt-24 pb-12 office-profile-main lg:mr-64">
            <div class="mx-auto max-w-[1450px] space-y-8">
                @if ($office->status === 'suspended')
                    <div class="p-4 border rounded-2xl border-amber-500/20 bg-amber-500/10 text-amber-100">
                        هذا المكتب موقوف حاليًا، ويمكن الاطلاع على ملفه فقط دون تقديم طلب انضمام جديد.
                    </div>
                @endif

                <section class="overflow-hidden office-profile-glass rounded-3xl">
                    <div class="relative h-64 overflow-hidden bg-gradient-to-br from-[#17213a] to-[#0f1729]">
                        @if ($office->cover_path)
                            <img
                                src="{{ asset('storage/' . $office->cover_path) }}"
                                alt="{{ $office->name }}"
                                class="object-cover w-full h-full"
                            >
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-[#0b1326] via-[#0b1326]/30 to-transparent"></div>
                    </div>

                    <div class="relative px-6 pb-8 md:px-10">
                        <div class="flex flex-col gap-5 -mt-16 md:flex-row md:items-end md:justify-between">
                            <div class="flex flex-col gap-5 md:flex-row md:items-end">
                                <div class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-3xl border-4 border-[#131b2e] bg-[#1f2940] text-4xl font-black text-[#b4c5ff] shadow-2xl">
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

                                <div class="pb-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h1 class="text-3xl font-black text-white">{{ $office->name }}</h1>
                                        <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <p class="mt-3 text-sm text-[#c3c6d7]">
                                        {{ $office->city ?: 'مدينة غير محددة' }}
                                        @if ($office->country)
                                            — {{ $office->country }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @guest
                                    <a href="{{ route('login') }}" class="rounded-xl bg-[#2563eb] px-5 py-3 font-bold text-white">
                                        تسجيل الدخول للتقديم
                                    </a>
                                @else
                                    @if ($currentUser->role === 'engineer')
                                        @if ($membership && $membership->status === 'active')
                                            <span class="px-5 py-3 font-bold text-green-300 border rounded-xl border-green-500/20 bg-green-500/10">
                                                أنت عضو فعال في المكتب
                                            </span>
                                        @elseif ($pendingApplication)
                                            <span class="px-5 py-3 font-bold border rounded-xl border-amber-500/20 bg-amber-500/10 text-amber-300">
                                                طلبك قيد المراجعة
                                            </span>
                                        @elseif ($canApply)
                                            <a
                                                href="{{ route('office-membership-applications.create', $office) }}"
                                                class="rounded-xl bg-[#2563eb] px-5 py-3 font-bold text-white transition hover:brightness-110"
                                            >
                                                طلب الانضمام للمكتب
                                            </a>
                                        @else
                                            <span class="rounded-xl border border-white/10 bg-white/5 px-5 py-3 font-bold text-[#8d90a0]">
                                                التقديم غير متاح حاليًا
                                            </span>
                                        @endif
                                    @else
                                        <span class="rounded-xl border border-white/10 bg-white/5 px-5 py-3 font-bold text-[#8d90a0]">
                                            الملف متاح للعرض فقط
                                        </span>
                                    @endif
                                @endguest
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
                    <div class="space-y-6">
                        <article class="office-profile-glass rounded-3xl p-7">
                            <h2 class="text-2xl font-black text-white">عن المكتب</h2>
                            <p class="mt-4 whitespace-pre-line leading-8 text-[#c3c6d7]">
                                {{ $office->description ?: 'لا يوجد وصف مضاف لهذا المكتب حتى الآن.' }}
                            </p>
                        </article>

                        <article class="office-profile-glass rounded-3xl p-7">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-2xl font-black text-white">فريق المكتب</h2>
                                    <p class="mt-2 text-sm text-[#8d90a0]">
                                        الأعضاء الفعالون المسجلون في المكتب
                                    </p>
                                </div>

                                <span class="rounded-full bg-[#b4c5ff]/10 px-3 py-1 text-sm font-black text-[#b4c5ff]">
                                    {{ $office->active_members_count ?? 0 }}
                                </span>
                            </div>

                            <div class="grid gap-4 mt-6 md:grid-cols-2">
                                @forelse ($office->activeMembers as $member)
                                    <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-[#2563eb] to-[#7c3aed] font-black text-white">
                                                @if ($member->user?->profile_photo)
                                                    <img
                                                        src="{{ asset('storage/' . $member->user->profile_photo) }}"
                                                        alt="{{ $member->user?->name }}"
                                                        class="object-cover w-full h-full"
                                                    >
                                                @else
                                                    {{ mb_substr($member->user?->name ?? 'م', 0, 1) }}
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <p class="font-black text-white truncate">
                                                    {{ $member->user?->name ?? 'عضو غير معروف' }}
                                                </p>

                                                <p class="mt-1 text-xs text-[#8d90a0]">
                                                    {{ $member->specialty?->name ?? 'تخصص غير محدد' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full rounded-2xl border border-white/5 bg-white/[0.03] p-8 text-center text-[#8d90a0]">
                                        لا يوجد أعضاء فعالون ظاهرون حاليًا.
                                    </div>
                                @endforelse
                            </div>
                        </article>
                    </div>

                    <aside class="space-y-6">
                        <article class="p-6 office-profile-glass rounded-3xl">
                            <h3 class="text-xl font-black text-white">معلومات المكتب</h3>

                            <div class="mt-5 space-y-4 text-sm">
                                <div>
                                    <p class="text-[#8d90a0]">مالك المكتب</p>
                                    <p class="mt-1 font-bold text-[#dae2fd]">{{ $office->owner?->name ?? 'غير معروف' }}</p>
                                </div>

                                <div>
                                    <p class="text-[#8d90a0]">البريد الإلكتروني</p>
                                    <p class="mt-1 break-all font-bold text-[#dae2fd]">{{ $office->email ?: 'غير متوفر' }}</p>
                                </div>

                                <div>
                                    <p class="text-[#8d90a0]">رقم الهاتف</p>
                                    <p class="mt-1 font-bold text-[#dae2fd]">{{ $office->phone ?: 'غير متوفر' }}</p>
                                </div>

                                <div>
                                    <p class="text-[#8d90a0]">العنوان</p>
                                    <p class="mt-1 leading-6 font-bold text-[#dae2fd]">{{ $office->address ?: 'غير متوفر' }}</p>
                                </div>
                            </div>
                        </article>

                        <article class="p-6 office-profile-glass rounded-3xl">
                            <h3 class="text-xl font-black text-white">إحصائيات</h3>

                            <div class="grid grid-cols-2 gap-3 mt-5">
                                <div class="rounded-2xl bg-white/[0.03] p-4 text-center">
                                    <p class="text-2xl font-black text-white">{{ $office->active_members_count ?? 0 }}</p>
                                    <p class="mt-1 text-xs text-[#8d90a0]">أعضاء فعالون</p>
                                </div>

                                <div class="rounded-2xl bg-white/[0.03] p-4 text-center">
                                    <p class="text-2xl font-black text-white">{{ $office->consultations_count ?? 0 }}</p>
                                    <p class="mt-1 text-xs text-[#8d90a0]">استشارات</p>
                                </div>
                            </div>
                        </article>

                        @if ($latestApplication && $latestApplication->status === 'rejected')
                            <article class="p-6 border rounded-3xl border-red-500/20 bg-red-500/10">
                                <h3 class="font-black text-red-200">آخر طلب انضمام</h3>
                                <p class="mt-3 text-sm leading-7 text-red-100">
                                    تم رفض طلبك السابق.
                                    @if ($latestApplication->rejection_reason)
                                        السبب: {{ $latestApplication->rejection_reason }}
                                    @endif
                                </p>
                            </article>
                        @endif
                    </aside>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
