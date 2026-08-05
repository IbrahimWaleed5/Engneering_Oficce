<x-app-layout>
    <style>
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800 {
            display: none !important;
        }

        .my-office-applications-page {
            min-height: 100vh;
            background: #0b0f19;
            color: #f3f4f6;
            font-family: "Alexandria", "Almarai", system-ui, sans-serif;
        }

        .my-office-applications-glass {
            background: rgba(31, 41, 55, .40);
            border: 1px solid rgba(255, 255, 255, .05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .20);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .my-office-applications-card {
            position: relative;
            overflow: hidden;
            transition:
                border-color .25s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        .my-office-applications-card:hover {
            transform: translateY(-2px);
            border-color: rgba(75, 85, 99, .80);
            box-shadow: 0 16px 40px rgba(0, 0, 0, .28);
        }

        .my-office-applications-detail {
            border: 1px solid rgba(55, 65, 81, .30);
            border-radius: .75rem;
            background: rgba(17, 24, 39, .50);
            padding: 1rem;
        }

        .my-office-applications-status {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            align-self: flex-start;
            border-radius: 9999px;
            border-width: 1px;
            padding: .375rem .75rem;
            font-size: .875rem;
            font-weight: 600;
        }
    </style>

    <div class="my-office-applications-page p-6 md:p-12" dir="rtl">
        <main class="mx-auto max-w-6xl space-y-10">
            @if (session('success'))
                <div class="rounded-xl border border-green-500/20 bg-green-500/10 p-4 text-green-100">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="rounded-xl border border-cyan-500/20 bg-cyan-500/10 p-4 text-cyan-100">
                    {{ session('info') }}
                </div>
            @endif

            <header class="flex flex-col justify-between gap-6 border-b border-gray-700/50 pb-6 md:flex-row md:items-end">
                <div class="space-y-2">
                    <span class="mb-1 block text-sm font-medium uppercase tracking-wide text-[#38bdf8]">
                        المكاتب الهندسية
                    </span>

                    <h1 class="text-3xl font-bold text-white md:text-4xl">
                        طلبات انضمامي إلى المكاتب
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm text-[#9ca3af] md:text-base">
                        تابع حالة طلبات الانضمام التي أرسلتها إلى المكاتب الهندسية.
                    </p>
                </div>

                <div>
                    <a
                        href="{{ route('engineering-offices.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-800 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:border-gray-500 hover:bg-gray-700 hover:shadow"
                    >
                        عرض جميع المكاتب
                    </a>
                </div>
            </header>

            <section class="space-y-6">
                @forelse ($applications as $application)
                    @php
                        $statusData = match ($application->status) {
                            'approved' => [
                                'label' => 'تم قبول الطلب',
                                'class' => 'bg-green-500/10 border-green-500/20 text-green-400',
                                'icon' => 'approved',
                            ],
                            'rejected' => [
                                'label' => 'تم رفض الطلب',
                                'class' => 'bg-red-500/10 border-red-500/20 text-red-400',
                                'icon' => 'rejected',
                            ],
                            'cancelled' => [
                                'label' => 'تم إلغاء الطلب',
                                'class' => 'bg-slate-500/10 border-slate-500/20 text-slate-300',
                                'icon' => 'cancelled',
                            ],
                            default => [
                                'label' => 'قيد المراجعة',
                                'class' => 'bg-yellow-500/10 border-yellow-500/20 text-yellow-500',
                                'icon' => 'pending',
                            ],
                        };

                        $officeLogo = $application->office?->logo_path
                            ? asset('storage/' . $application->office->logo_path)
                            : null;
                    @endphp

                    <article class="my-office-applications-glass my-office-applications-card group rounded-2xl p-6 md:p-8">
                        <div class="absolute -right-24 -top-24 h-48 w-48 rounded-full bg-[#38bdf8]/10 blur-3xl transition-all group-hover:bg-[#38bdf8]/20"></div>

                        <div class="relative z-10 flex flex-col gap-8">
                            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-700 bg-gray-800">
                                        @if ($officeLogo)
                                            <img
                                                src="{{ $officeLogo }}"
                                                alt="{{ $application->office?->name }}"
                                                class="h-full w-full object-cover"
                                            >
                                        @else
                                            <svg class="h-8 w-8 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16M19 21h2M19 21h-5M5 21H3M5 21h5M9 7h1M9 11h1M14 7h1M14 11h1M10 21v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="mb-1 flex flex-wrap items-center gap-3">
                                            <h2 class="text-xl font-bold text-white">
                                                {{ $application->office?->name ?? 'مكتب غير موجود' }}
                                            </h2>

                                            <span class="inline-flex items-center rounded border border-[#38bdf8]/20 bg-gray-800 px-2 py-0.5 text-xs font-medium text-[#38bdf8]">
                                                مكتب هندسي
                                            </span>
                                        </div>

                                        <p class="flex items-center gap-2 text-sm text-[#9ca3af]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M8 7V3M16 7V3M7 11h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                                            </svg>
                                            تاريخ الطلب:
                                            {{ $application->created_at?->format('Y-m-d H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="my-office-applications-status {{ $statusData['class'] }}">
                                    @if ($statusData['icon'] === 'pending')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 8v4l3 3"/>
                                            <circle cx="12" cy="12" r="9"/>
                                        </svg>
                                    @elseif ($statusData['icon'] === 'approved')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="m8 12 2.5 2.5L16.5 8.5"/>
                                        </svg>
                                    @elseif ($statusData['icon'] === 'rejected')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="m8 8 8 8M16 8l-8 8"/>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="m8 8 8 8M16 8l-8 8"/>
                                        </svg>
                                    @endif

                                    {{ $statusData['label'] }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="my-office-applications-detail">
                                    <span class="mb-1 block text-xs text-[#9ca3af]">التخصص</span>
                                    <strong class="text-base font-semibold text-white">
                                        {{ $application->specialty?->name ?? 'غير محدد' }}
                                    </strong>
                                </div>

                                <div class="my-office-applications-detail">
                                    <span class="mb-1 block text-xs text-[#9ca3af]">المسمى المطلوب</span>
                                    <strong class="text-base font-semibold text-white">
                                        {{ $application->requested_position ?: 'غير محدد' }}
                                    </strong>
                                </div>

                                <div class="my-office-applications-detail">
                                    <span class="mb-1 block text-xs text-[#9ca3af]">سنوات الخبرة</span>
                                    <strong class="text-base font-semibold text-white">
                                        {{ $application->years_of_experience !== null
                                            ? $application->years_of_experience . ' سنة'
                                            : 'غير محددة' }}
                                    </strong>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @if ($application->message)
                                    <div class="rounded-xl border border-gray-700/30 bg-gray-800/30 p-5">
                                        <h3 class="mb-2 text-sm font-medium text-[#9ca3af]">
                                            رسالتك إلى المكتب
                                        </h3>

                                        <p class="text-sm leading-relaxed text-white">
                                            {{ $application->message }}
                                        </p>
                                    </div>
                                @endif

                                @if (
                                    $application->status === 'rejected'
                                    && $application->rejection_reason
                                )
                                    <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-5">
                                        <h3 class="font-bold text-red-300">سبب الرفض</h3>
                                        <p class="mt-2 text-sm leading-7 text-red-100">
                                            {{ $application->rejection_reason }}
                                        </p>
                                    </div>
                                @endif

                                @if ($application->status === 'approved')
                                    <div class="rounded-xl border border-green-500/20 bg-green-500/10 p-5">
                                        <h3 class="font-bold text-green-300">أصبحت عضوًا في المكتب</h3>
                                        <p class="mt-2 text-sm leading-7 text-green-100">
                                            تم قبول طلبك وإضافتك إلى فريق المكتب الهندسي.
                                        </p>
                                    </div>
                                @endif

                                <div class="flex flex-col justify-end gap-3 sm:flex-row sm:items-center">
                                    @if ($application->reviewer)
                                        <div class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-gray-300">
                                            تمت المراجعة بواسطة:
                                            <span class="mr-1 font-bold text-white">
                                                {{ $application->reviewer->name }}
                                            </span>
                                        </div>
                                    @endif

                                    @if ($application->office)
                                        <a
                                            href="{{ route('engineering-offices.show', $application->office) }}"
                                            class="rounded-lg bg-[#38bdf8] px-8 py-2.5 text-center font-bold text-[#0b0f19] shadow-[0_0_15px_rgba(56,189,248,.20)] transition duration-300 hover:bg-[#2ba5df] hover:shadow-[0_0_20px_rgba(56,189,248,.40)]"
                                        >
                                            عرض المكتب
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="my-office-applications-glass rounded-2xl p-10 text-center">
                        <svg class="mx-auto h-16 w-16 text-[#9ca3af]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 2h8l4 4v16H6z"/>
                            <path d="M14 2v5h5M9 13h6M9 17h6M9 9h2"/>
                        </svg>

                        <h2 class="mt-5 text-2xl font-bold text-white">
                            لا توجد طلبات انضمام
                        </h2>

                        <p class="mt-3 text-[#9ca3af]">
                            لم ترسل أي طلب انضمام إلى مكتب هندسي حتى الآن.
                        </p>

                        <a
                            href="{{ route('engineering-offices.index') }}"
                            class="mt-6 inline-flex items-center justify-center rounded-lg bg-[#38bdf8] px-6 py-3 font-bold text-[#0b0f19] transition hover:bg-[#2ba5df]"
                        >
                            استعراض المكاتب
                        </a>
                    </div>
                @endforelse
            </section>

            @if ($applications->hasPages())
                <div>
                    {{ $applications->withQueryString()->links() }}
                </div>
            @endif
        </main>
    </div>
</x-app-layout>
