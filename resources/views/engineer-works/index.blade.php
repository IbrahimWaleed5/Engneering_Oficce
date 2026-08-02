<x-app-layout>
    @php
        $currentUser = auth()->user();

        $totalWorks = $works->count();

        $pendingWorks = $works
            ->where('status', 'pending')
            ->count();

        $approvedWorks = $works
            ->where('status', 'approved')
            ->count();

        $rejectedWorks = $works
            ->where('status', 'rejected')
            ->count();
    @endphp

    <style>
        .works-review-page {
            --surface: #0b1326;
            --surface-lowest: #060e20;
            --surface-low: #131b2e;
            --surface-container: #171f33;
            --surface-high: #222a3d;
            --surface-highest: #2d3449;
            --primary: #b4c5ff;
            --primary-container: #2563eb;
            --secondary: #ffb1c7;
            --secondary-container: #be0062;
            --tertiary: #d2bbff;
            --tertiary-container: #8343f4;
            --text: #dae2fd;
            --muted: #c3c6d7;
            --outline: #8d90a0;
            --outline-variant: #434655;

            min-height: 100vh;
            color: var(--text);
            background: var(--surface);
            font-family: 'Almarai', 'Be Vietnam Pro', sans-serif;
        }

        .works-glass {
            background: rgba(19, 27, 46, .6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .works-premium-gradient {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0b1326 0%, #171f33 100%);
        }

        .works-premium-gradient::after {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            pointer-events: none;
            content: '';
            background: radial-gradient(circle at center, rgba(37, 99, 235, .1) 0%, transparent 50%);
        }

        .works-glow-hover {
            transition: box-shadow .25s ease, transform .25s ease, border-color .25s ease;
        }

        .works-glow-hover:hover {
            border-color: rgba(180, 197, 255, .35);
            box-shadow: 0 0 20px rgba(37, 99, 235, .2);
            transform: translateY(-2px);
        }

        .works-custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .works-custom-scrollbar::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .works-custom-scrollbar::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }

        .works-custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #434655;
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .works-sidebar {
                display: none !important;
            }

            .works-main {
                margin-right: 0 !important;
            }
        }
    </style>

    <div
        class="works-review-page"
        dir="rtl"
    >
        {{-- خلفية زخرفية --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
            <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-[#b4c5ff]/5 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] -right-[10%] h-[50%] w-[50%] rounded-full bg-[#ffb1c7]/5 blur-[120px]"></div>
        </div>

        {{-- القائمة الجانبية --}}
        <aside class="works-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#131b2e] p-4 shadow-lg backdrop-blur-xl">
            <div class="flex items-center gap-3 px-2 mb-10">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#b4c5ff] text-[#002a78]">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 20h16M6 20V9l6-5 6 5v11M9 20v-6h6v6"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-xl font-bold leading-tight text-[#b4c5ff]">
                        CreativeHome
                    </h1>

                    <p class="text-xs text-[#c3c6d7] opacity-70">
                        مكتب هندسي
                    </p>
                </div>
            </div>

            <nav class="flex-grow space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition rounded-lg hover:bg-[#2d3449]/50"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>نظرة عامة</span>
                </a>

                <a
                    href="{{ Route::has('users.index') ? route('users.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition rounded-lg hover:bg-[#2d3449]/50"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>المستخدمون</span>
                </a>

                <a
                    href="{{ Route::has('consultations.index') ? route('consultations.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition rounded-lg hover:bg-[#2d3449]/50"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>المشاريع</span>
                </a>

                <a
                    href="#worksTable"
                    class="flex items-center gap-3 rounded-lg bg-[#b4c5ff]/10 px-4 py-3 font-bold text-[#b4c5ff] shadow-[0_0_15px_rgba(37,99,235,.3)]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="7" r="3"/>
                        <path d="M5 21a7 7 0 0 1 14 0M8 12h8M9 3h6"/>
                    </svg>

                    <span>أعمال المهندسين</span>
                </a>

                <a
                    href="{{ Route::has('payments.index') ? route('payments.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition rounded-lg hover:bg-[#2d3449]/50"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>

                    <span>المدفوعات</span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition rounded-lg hover:bg-[#2d3449]/50"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-6 mt-auto border-t border-[#434655]/10">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-10 h-10 overflow-hidden border rounded-full border-[#b4c5ff]/20">
                        @if ($currentUser->profile_photo)
                            <img
                                src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                alt="{{ $currentUser->name }}"
                                class="object-cover w-full h-full"
                            >
                        @else
                            <div class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-purple-600">
                                {{ mb_substr($currentUser->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate">
                            {{ $currentUser->name }}
                        </p>

                        <p class="text-xs text-[#c3c6d7] truncate">
                            مدير النظام
                        </p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mr-auto">
                        @csrf

                        <button
                            type="submit"
                            class="text-[#c3c6d7] transition hover:text-red-300"
                            title="تسجيل الخروج"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="min-h-screen works-main lg:mr-64">
            {{-- الشريط العلوي --}}
            <header class="relative z-40 flex items-center justify-between h-16 px-8 border-b border-[#434655]/10 bg-[#0b1326]/80 backdrop-blur-md">
                <div class="flex items-center gap-6">
                    <div class="relative hidden group md:block">
                        <svg class="absolute w-5 h-5 -translate-y-1/2 right-3 top-1/2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            id="worksSearchInput"
                            type="text"
                            placeholder="البحث عن عمل هندسي..."
                            class="w-64 rounded-lg border-0 bg-[#2d3449] py-2 pr-10 pl-4 text-sm text-white focus:ring-2 focus:ring-[#b4c5ff]/50"
                        >
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a
                        href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                        class="relative p-2 transition rounded-full hover:bg-[#2d3449]"
                        title="الإشعارات"
                    >
                        <svg class="w-5 h-5 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>
                        </svg>

                        <span class="absolute w-2 h-2 rounded-full top-2 right-2 bg-[#ffb1c7]"></span>
                    </a>

                    <a
                        href="{{ route('dashboard') }}"
                        class="p-2 transition rounded-full hover:bg-[#2d3449]"
                        title="مساعدة"
                    >
                        <svg class="w-5 h-5 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M9.7 9a2.5 2.5 0 1 1 3.5 2.3c-.8.35-1.2.8-1.2 1.7M12 17h.01"/>
                        </svg>
                    </a>

                    <div class="h-8 w-px bg-[#434655]/20 mx-2"></div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#b4c5ff]">
                            المقر الرئيسي
                        </span>

                        <svg class="w-4 h-4 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="2.5"/>
                        </svg>
                    </div>
                </div>
            </header>

            <div class="works-custom-scrollbar h-[calc(100vh-64px)] overflow-y-auto p-8 space-y-8">
                {{-- الرسائل --}}
                @if (session('success'))
                    <div class="flex items-center gap-3 p-5 border rounded-2xl border-emerald-500/20 bg-emerald-500/10 text-emerald-200">
                        <div class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-emerald-500/15">
                            ✓
                        </div>

                        <div>
                            <p class="font-black">
                                تمت العملية بنجاح
                            </p>

                            <p class="mt-1 text-sm text-emerald-200/80">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-5 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <p class="font-black text-red-200">
                            يرجى تصحيح الأخطاء التالية
                        </p>

                        <div class="mt-3 space-y-2 text-sm text-red-200/90">
                            @foreach ($errors->all() as $error)
                                <p>• {{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- بطاقة العنوان --}}
                <section class="relative flex flex-col items-center justify-between gap-8 p-10 works-premium-gradient works-glass rounded-3xl md:flex-row">
                    <div class="z-10 space-y-4 text-right">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#b4c5ff]/20 bg-[#b4c5ff]/10 px-3 py-1 text-[#b4c5ff]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>

                            <span class="text-[10px] font-bold tracking-widest uppercase">
                                نظام مراجعة الجودة
                            </span>
                        </div>

                        <h2 class="text-3xl font-black text-[#dae2fd]">
                            مراجعة أعمال المهندسين
                        </h2>

                        <p class="max-w-xl text-[#c3c6d7]">
                            راجع الأعمال المضافة، وافق عليها أو ارفضها أو احذفها نهائيًا.
                            تأكد من مطابقة جميع المخططات للمعايير الهندسية المعتمدة لدى المكتب.
                        </p>
                    </div>

                    <div class="z-10 flex gap-6">
                        <div class="works-glass works-glow-hover flex min-w-[240px] items-center gap-6 rounded-2xl border-l-4 border-l-[#b4c5ff] p-6">
                            <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-[#2563eb] text-[#eeefff]">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>
                                    <path d="m4 8 5-4 6 5 5-4"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm text-[#c3c6d7]">
                                    إجمالي الأعمال
                                </p>

                                <h3 class="mt-1 text-4xl font-black text-[#dae2fd]">
                                    {{ $totalWorks }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- الجدول --}}
                <section
                    id="worksTable"
                    class="works-glass flex min-h-[600px] flex-col overflow-hidden rounded-3xl"
                >
                    <div class="flex flex-col gap-4 border-b border-[#434655]/10 bg-[#222a3d]/30 px-8 py-6 md:flex-row md:items-center md:justify-between">
                        <h3 class="flex items-center gap-3 text-2xl font-bold">
                            <svg class="w-6 h-6 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M8 6h13M8 12h13M8 18h13"/>
                                <circle cx="3.5" cy="6" r=".5" fill="currentColor"/>
                                <circle cx="3.5" cy="12" r=".5" fill="currentColor"/>
                                <circle cx="3.5" cy="18" r=".5" fill="currentColor"/>
                            </svg>

                            سجل مراجعة الأعمال
                        </h3>

                        <div class="flex flex-wrap gap-3">
                            <select
                                id="worksStatusFilter"
                                class="rounded-lg border-0 bg-[#2d3449] px-4 py-2 text-sm font-bold text-[#dae2fd] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">كل الحالات</option>
                                <option value="pending">قيد المراجعة</option>
                                <option value="approved">مقبول</option>
                                <option value="rejected">مرفوض</option>
                            </select>

                            <a
                                href="{{ route('engineer.works.public') }}"
                                class="flex items-center gap-2 rounded-lg bg-[#2d3449] px-4 py-2 text-sm font-bold text-[#dae2fd] transition hover:bg-[#31394d]"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>
                                </svg>

                                عرض المكتبة العامة
                            </a>
                        </div>
                    </div>

                    <div class="relative flex-grow overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="sticky top-0 z-10 bg-[#131b2e]/95 backdrop-blur-md">
                                <tr class="text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                    <th class="px-8 py-5 border-b border-[#434655]/10">الصورة</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">العنوان</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">المهندس</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">النوع</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">الموقع</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">الحالة</th>
                                    <th class="px-8 py-5 border-b border-[#434655]/10">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#434655]/10">
                                @forelse ($works as $work)
                                    @php
                                        $searchText = strtolower(
                                            ($work->title ?? '') . ' ' .
                                            ($work->engineer?->name ?? '') . ' ' .
                                            ($work->location ?? '') . ' ' .
                                            ($work->project_type ?? '') . ' ' .
                                            ($work->description ?? '')
                                        );
                                    @endphp

                                    <tr
                                        data-work-row
                                        data-status="{{ $work->status }}"
                                        data-search="{{ $searchText }}"
                                        class="transition hover:bg-[#2563eb]/5"
                                    >
                                        <td class="px-8 py-4">
                                            <div class="w-16 h-12 overflow-hidden border rounded-xl border-white/10 bg-[#060e20]">
                                                @if ($work->coverImage)
                                                    <img
                                                        src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                                        alt="{{ $work->title }}"
                                                        class="object-cover w-full h-full"
                                                    >
                                                @else
                                                    <div class="flex items-center justify-center w-full h-full text-[#b4c5ff]">
                                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-8 py-4">
                                            <p class="font-bold text-[#dae2fd]">
                                                {{ $work->title }}
                                            </p>

                                            <p class="mt-1 text-xs text-[#c3c6d7]">
                                                {{ $work->created_at?->format('Y-m-d') }}
                                            </p>
                                        </td>

                                        <td class="px-8 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 overflow-hidden border rounded-full border-[#b4c5ff]/20">
                                                    @if ($work->engineer?->profile_photo)
                                                        <img
                                                            src="{{ asset('storage/' . $work->engineer->profile_photo) }}"
                                                            alt="{{ $work->engineer->name }}"
                                                            class="object-cover w-full h-full"
                                                        >
                                                    @else
                                                        <div class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-cyan-500">
                                                            {{ mb_substr($work->engineer?->name ?? 'م', 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <p class="text-sm font-bold text-[#dae2fd]">
                                                        {{ $work->engineer?->name ?? 'مهندس غير محدد' }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-[#b4c5ff]">
                                                        {{
                                                            $work
                                                                ->engineer
                                                                ?->employeeProfile
                                                                ?->specialty
                                                                ?->name
                                                            ?? 'التخصص غير محدد'
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-4 text-sm text-[#c3c6d7]">
                                            {{ $work->project_type ?? '—' }}
                                        </td>

                                        <td class="px-8 py-4 text-sm text-[#c3c6d7]">
                                            {{ $work->location ?? '—' }}
                                        </td>

                                        <td class="px-8 py-4">
                                            @if ($work->status === 'approved')
                                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-[11px] font-bold text-emerald-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                    مقبول
                                                </span>
                                            @elseif ($work->status === 'rejected')
                                                <span class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-[11px] font-bold text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                    مرفوض
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-2 rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[11px] font-bold text-amber-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                    قيد المراجعة
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-8 py-4">
                                            <div class="flex items-center gap-2">
                                                <a
                                                    href="{{ route('engineer.works.show', $work) }}"
                                                    class="flex items-center justify-center w-9 h-9 text-[#b4c5ff] transition rounded-lg bg-[#b4c5ff]/10 hover:bg-[#b4c5ff]/20"
                                                    title="عرض العمل"
                                                >
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/>
                                                        <circle cx="12" cy="12" r="2.5"/>
                                                    </svg>
                                                </a>

                                                @if ($work->status !== 'approved')
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.engineer-works.approve', $work) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            onclick="return confirm('هل تريد الموافقة على هذا العمل؟')"
                                                            class="flex items-center justify-center transition rounded-lg w-9 h-9 text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20"
                                                            title="موافقة"
                                                        >
                                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="m5 12 4 4L19 6"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($work->status !== 'rejected')
                                                    <button
                                                        type="button"
                                                        data-open-reject
                                                        data-reject-url="{{ route('admin.engineer-works.reject', $work) }}"
                                                        data-reject-title="{{ $work->title }}"
                                                        class="flex items-center justify-center text-red-300 transition rounded-lg w-9 h-9 bg-red-500/10 hover:bg-red-500/20"
                                                        title="رفض"
                                                    >
                                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M6 18 18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-24" colspan="7">
                                            <div class="flex flex-col items-center justify-center space-y-6 text-center">
                                                <div class="relative">
                                                    <div class="absolute inset-0 rounded-full w-32 h-32 bg-[#b4c5ff]/5 animate-pulse"></div>

                                                    <div class="relative flex items-center justify-center w-32 h-32 rounded-full bg-[#b4c5ff]/10">
                                                        <svg class="w-14 h-14 text-[#b4c5ff]/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                                            <path d="M4 5h16v14H4z"/>
                                                            <path d="m4 7 8 6 8-6"/>
                                                            <path d="M8 3h8"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <div class="space-y-2">
                                                    <h4 class="text-2xl font-bold text-[#dae2fd]">
                                                        لا توجد أعمال للمراجعة
                                                    </h4>

                                                    <p class="max-w-sm text-[#c3c6d7]">
                                                        جميع الأعمال مكتملة حاليًا. ستظهر أعمال المهندسين الجديدة هنا فور إضافتها للنظام.
                                                    </p>
                                                </div>

                                                <button
                                                    type="button"
                                                    onclick="window.location.reload()"
                                                    class="mt-4 flex items-center gap-2 rounded-xl bg-[#b4c5ff] px-8 py-3 font-bold text-[#002a78] shadow-lg shadow-blue-500/20 transition hover:scale-105 active:scale-95"
                                                >
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="M20 6v5h-5M4 18v-5h5"/>
                                                        <path d="M18 9a7 7 0 0 0-12-2M6 15a7 7 0 0 0 12 2"/>
                                                    </svg>

                                                    تحديث البيانات
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between px-8 py-4 border-t border-[#434655]/10 text-xs font-bold text-[#c3c6d7]">
                        <div>
                            عرض {{ $totalWorks }} من {{ $totalWorks }} عنصر
                        </div>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                disabled
                                class="flex items-center justify-center w-8 h-8 rounded-lg cursor-not-allowed bg-[#2d3449]/50 opacity-50"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                disabled
                                class="flex items-center justify-center w-8 h-8 rounded-lg cursor-not-allowed bg-[#2d3449]/50 opacity-50"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </section>

                {{-- البطاقات السفلية --}}
                <section class="grid grid-cols-1 gap-8 pb-12 md:grid-cols-3">
                    <article class="works-glass works-glow-hover flex flex-col gap-4 rounded-3xl border-t-2 border-t-[#b4c5ff]/20 p-8">
                        <svg class="w-8 h-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>

                        <h4 class="text-lg font-bold">
                            أعمال قيد الانتظار
                        </h4>

                        <p class="text-sm text-[#c3c6d7]">
                            يوجد حاليًا {{ $pendingWorks }} عمل بانتظار المراجعة الفنية.
                        </p>
                    </article>

                    <article class="works-glass works-glow-hover flex flex-col gap-4 rounded-3xl border-t-2 border-t-[#ffb1c7]/20 p-8">
                        <svg class="w-8 h-8 text-[#ffb1c7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M12 3 2 21h20L12 3Z"/>
                            <path d="M12 9v5M12 18h.01"/>
                        </svg>

                        <h4 class="text-lg font-bold">
                            أعمال مرفوضة
                        </h4>

                        <p class="text-sm text-[#c3c6d7]">
                            عدد الأعمال المرفوضة حاليًا هو {{ $rejectedWorks }}.
                        </p>
                    </article>

                    <article class="works-glass works-glow-hover flex flex-col gap-4 rounded-3xl border-t-2 border-t-[#d2bbff]/20 p-8">
                        <svg class="w-8 h-8 text-[#d2bbff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="m8 12 2.5 2.5L16 9"/>
                        </svg>

                        <h4 class="text-lg font-bold">
                            الأعمال المعتمدة
                        </h4>

                        <p class="text-sm text-[#c3c6d7]">
                            تم اعتماد {{ $approvedWorks }} عمل ونشره في النظام.
                        </p>
                    </article>
                </section>
            </div>
        </main>

        {{-- نافذة الرفض --}}
        <div
            id="rejectModal"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-[#060e20]/90 p-5 backdrop-blur-xl"
        >
            <div
                id="rejectModalPanel"
                class="works-glass w-full max-w-xl rounded-[2rem] p-7 shadow-2xl"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-center text-red-300 w-14 h-14 rounded-2xl bg-red-500/10">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M12 3 2 21h20L12 3Z"/>
                                <path d="M12 9v5M12 18h.01"/>
                            </svg>
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            رفض العمل
                        </h2>

                        <p class="mt-2 text-sm text-[#c3c6d7]">
                            العمل:
                            <span
                                id="rejectModalTitle"
                                class="font-bold text-white"
                            ></span>
                        </p>
                    </div>

                    <button
                        type="button"
                        data-close-reject
                        class="flex items-center justify-center text-white transition border rounded-full w-11 h-11 border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        ✕
                    </button>
                </div>

                <form
                    id="rejectForm"
                    method="POST"
                    action=""
                    class="mt-7"
                >
                    @csrf
                    @method('PATCH')

                    <label class="block mb-2 text-sm font-bold text-[#dae2fd]">
                        سبب رفض العمل
                    </label>

                    <textarea
                        name="admin_note"
                        rows="5"
                        required
                        placeholder="اكتب للمهندس سبب رفض العمل والتعديلات المطلوبة..."
                        class="w-full px-4 py-4 text-white transition border outline-none resize-none rounded-2xl border-white/10 bg-[#060e20]/60 placeholder:text-[#c3c6d7]/50 focus:border-red-400 focus:ring-2 focus:ring-red-400/10"
                    ></textarea>

                    <div class="grid gap-3 mt-6 sm:grid-cols-2">
                        <button
                            type="button"
                            data-close-reject
                            class="px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            إلغاء
                        </button>

                        <button
                            type="submit"
                            class="px-5 py-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                        >
                            تأكيد رفض العمل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('worksSearchInput');
            const statusFilter = document.getElementById('worksStatusFilter');
            const rows = Array.from(document.querySelectorAll('[data-work-row]'));

            const applyFilters = () => {
                const query = (searchInput?.value || '').trim().toLowerCase();
                const status = statusFilter?.value || 'all';

                rows.forEach((row) => {
                    const rowStatus = row.dataset.status || '';
                    const rowSearch = (row.dataset.search || '').toLowerCase();

                    const matchesStatus =
                        status === 'all' || rowStatus === status;

                    const matchesSearch =
                        query === '' || rowSearch.includes(query);

                    row.classList.toggle(
                        'hidden',
                        !(matchesStatus && matchesSearch)
                    );
                });
            };

            searchInput?.addEventListener('input', applyFilters);
            statusFilter?.addEventListener('change', applyFilters);

            const modal = document.getElementById('rejectModal');
            const modalPanel = document.getElementById('rejectModalPanel');
            const rejectForm = document.getElementById('rejectForm');
            const rejectTitle = document.getElementById('rejectModalTitle');

            const openRejectModal = (button) => {
                if (!modal || !rejectForm || !rejectTitle) {
                    return;
                }

                rejectForm.action = button.dataset.rejectUrl || '';
                rejectTitle.textContent =
                    button.dataset.rejectTitle || '';

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.body.style.overflow = 'hidden';
            };

            const closeRejectModal = () => {
                if (!modal) {
                    return;
                }

                modal.classList.add('hidden');
                modal.classList.remove('flex');

                document.body.style.overflow = '';
            };

            document
                .querySelectorAll('[data-open-reject]')
                .forEach((button) => {
                    button.addEventListener('click', function () {
                        openRejectModal(button);
                    });
                });

            document
                .querySelectorAll('[data-close-reject]')
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        closeRejectModal
                    );
                });

            modal?.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeRejectModal();
                }
            });

            modalPanel?.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeRejectModal();
                }
            });
        });
    </script>

</x-app-layout>
