<x-app-layout>
    @php
        $latestSubscription = $latestSubscription ?? null;

        $statusData = $application
            ? match ($application->status) {
                'approved' => [
                    'title' => 'تم قبول طلب المكتب',
                    'description' => 'تمت الموافقة على طلب تسجيل المكتب الهندسي. يمكنك متابعة حالة الاشتراك من التفاصيل أدناه.',
                    'icon' => 'check_circle',
                    'iconClass' => 'text-emerald-300',
                    'ringClass' => 'shadow-[0_0_40px_rgba(52,211,153,0.22)]',
                    'noticeTitle' => 'تمت الموافقة',
                    'noticeText' => 'تم إنشاء المكتب وربطه بحسابك. راجع تفاصيل الاشتراك وحالة اعتماد إيصال الدفع.',
                    'noticeClass' => 'border-emerald-400/20 bg-emerald-500/10',
                    'noticeTextClass' => 'text-emerald-200',
                ],

                'rejected' => [
                    'title' => 'تم رفض طلب المكتب',
                    'description' => 'تعذر قبول طلب تسجيل المكتب الهندسي. راجع سبب الرفض الموضح أدناه.',
                    'icon' => 'cancel',
                    'iconClass' => 'text-red-300',
                    'ringClass' => 'shadow-[0_0_40px_rgba(248,113,113,0.22)]',
                    'noticeTitle' => 'الطلب مرفوض',
                    'noticeText' => 'يمكنك مراجعة سبب الرفض وتصحيح البيانات قبل تقديم طلب جديد عند إتاحة ذلك.',
                    'noticeClass' => 'border-red-400/20 bg-red-500/10',
                    'noticeTextClass' => 'text-red-200',
                ],

                'cancelled' => [
                    'title' => 'تم إلغاء الطلب',
                    'description' => 'طلب تسجيل المكتب الهندسي ملغي حاليًا.',
                    'icon' => 'block',
                    'iconClass' => 'text-slate-300',
                    'ringClass' => 'shadow-[0_0_40px_rgba(148,163,184,0.18)]',
                    'noticeTitle' => 'الطلب ملغي',
                    'noticeText' => 'لا توجد مراجعة نشطة لهذا الطلب في الوقت الحالي.',
                    'noticeClass' => 'border-white/10 bg-white/5',
                    'noticeTextClass' => 'text-slate-300',
                ],

                default => [
                    'title' => 'الطلب قيد المراجعة',
                    'description' => 'تم استلام طلب تسجيل المكتب الهندسي بنجاح. فريق المراجعة يقوم حاليًا بالتدقيق في البيانات والمستندات وإيصال الدفع.',
                    'icon' => 'hourglass_empty',
                    'iconClass' => 'text-[#b4c5ff]',
                    'ringClass' => 'shadow-[0_0_40px_rgba(37,99,235,0.2)]',
                    'noticeTitle' => 'تحديث قريب',
                    'noticeText' => 'سيقوم المدير المختص بمراجعة الطلب وإيصال الدفع في أقرب وقت، وستظهر النتيجة فور تحديث الحالة.',
                    'noticeClass' => 'border-[#d2bbff]/20 bg-[#222a3d]/50',
                    'noticeTextClass' => 'text-[#d2bbff]',
                ],
            }
            : null;

        $subscriptionStatus = $latestSubscription?->status;

        $subscriptionStatusLabel = match ($subscriptionStatus) {
            'active' => 'فعال',
            'under_review' => 'قيد مراجعة الدفع',
            'rejected' => 'إيصال مرفوض',
            'expired' => 'منتهي',
            'pending' => 'بانتظار المراجعة',
            default => 'غير متاح',
        };
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }

        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800 {
            display: none !important;
        }

        .office-status-page {
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            color: #dae2fd;
            background:
                linear-gradient(rgba(11,19,38,.88), rgba(11,19,38,.94)),
                radial-gradient(circle at 15% 18%, rgba(37,99,235,.22), transparent 32%),
                radial-gradient(circle at 85% 82%, rgba(131,67,244,.16), transparent 30%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .office-status-blueprint {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: .16;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(180,197,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(180,197,255,.06) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.95), transparent 95%);
        }

        @keyframes office-status-pulse {
            0%, 100% {
                opacity: .48;
                transform: scale(1);
            }

            50% {
                opacity: .78;
                transform: scale(1.05);
            }
        }

        .office-status-glow {
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 9999px;
            pointer-events: none;
            z-index: 0;
            background: radial-gradient(circle, rgba(37,99,235,.16) 0%, rgba(11,19,38,0) 70%);
            animation: office-status-pulse 8s ease-in-out infinite;
        }

        .office-status-glass {
            background: rgba(45,52,73,.40);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(180,197,255,.10);
            box-shadow: 0 8px 32px rgba(0,0,0,.37);
        }

        .office-status-gold {
            position: relative;
        }

        .office-status-gold::before {
            content: "";
            position: absolute;
            inset: 0;
            padding: 1px;
            border-radius: inherit;
            pointer-events: none;
            background: linear-gradient(135deg, rgba(210,187,255,.4), rgba(0,0,0,0) 50%, rgba(210,187,255,.1));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }

        .office-status-icon {
            font-variation-settings: 'FILL' 1;
        }

        @media (max-width: 640px) {
            .office-status-main {
                padding: 1rem !important;
            }

            .office-status-card {
                padding: 1.25rem !important;
            }

            .office-status-brand {
                font-size: 1.8rem !important;
                line-height: 2.2rem !important;
            }

            .office-status-title {
                font-size: 1.45rem !important;
                line-height: 2rem !important;
            }

            .office-status-icon-wrap {
                width: 6.5rem !important;
                height: 6.5rem !important;
            }
        }
    </style>

    <div class="office-status-page" dir="rtl">
        <div class="office-status-blueprint"></div>
        <div class="office-status-glow right-[8%] top-16"></div>
        <div class="office-status-glow bottom-16 left-[8%]" style="animation-delay:4s"></div>

        <main class="relative z-10 flex flex-col items-center justify-center min-h-screen p-6 office-status-main md:p-8">
            <div class="flex flex-col items-center w-full max-w-3xl gap-8">
                <div class="mb-1 text-center">
                    <p class="text-xs font-black uppercase tracking-[.28em] text-[#8d90a0]">
                        منصة الاستشارات الهندسية
                    </p>

                    <h1 class="office-status-brand mt-2 text-3xl font-black tracking-tight text-[#b4c5ff] sm:text-4xl">
                        مكتب الوليد الهندسي
                    </h1>
                </div>

                @foreach (['success' => 'green', 'info' => 'cyan', 'error' => 'red'] as $messageType => $color)
                    @if (session($messageType))
                        <div class="w-full rounded-2xl border p-4 text-{{ $color }}-100 border-{{ $color }}-500/20 bg-{{ $color }}-500/10">
                            {{ session($messageType) }}
                        </div>
                    @endif
                @endforeach

                @if (! $application)
                    <section class="relative flex flex-col items-center w-full p-8 overflow-hidden office-status-card office-status-glass office-status-gold rounded-2xl">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-[#b4c5ff]/10 to-transparent"></div>

                        <div class="office-status-icon-wrap relative mb-5 flex h-32 w-32 items-center justify-center rounded-full bg-[#171f33] shadow-[0_0_40px_rgba(37,99,235,.2)]">
                            <span class="office-status-icon material-symbols-outlined text-6xl text-[#b4c5ff]">
                                corporate_fare
                            </span>
                        </div>

                        <div class="relative z-10 text-center">
                            <h2 class="text-2xl font-black text-white office-status-title">
                                لا يوجد لديك طلب مكتب
                            </h2>

                            <p class="mx-auto mt-3 max-w-md leading-8 text-[#c3c6d7]">
                                يمكنك تقديم طلب جديد لتسجيل مكتب هندسي داخل النظام.
                            </p>

                            <a
                                href="{{ route('office-applications.create') }}"
                                class="mt-7 inline-flex items-center gap-2 rounded-full border border-[#b4c5ff]/25 bg-[#2563eb] px-8 py-4 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-[#0053db]"
                            >
                                <span class="text-xl office-status-icon material-symbols-outlined">add_business</span>
                                تقديم طلب مكتب
                            </a>
                        </div>
                    </section>
                @else
                    <section class="relative flex flex-col items-center w-full p-8 overflow-hidden office-status-card office-status-glass office-status-gold rounded-2xl">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-[#b4c5ff]/10 to-transparent"></div>

                        <div class="office-status-icon-wrap {{ $statusData['ringClass'] }} relative mb-5 flex h-32 w-32 items-center justify-center rounded-full bg-[#171f33]">
                            <span class="office-status-icon material-symbols-outlined {{ $statusData['iconClass'] }} text-6xl {{ $application->status === 'pending' ? 'animate-pulse' : '' }}">
                                {{ $statusData['icon'] }}
                            </span>
                        </div>

                        <div class="relative z-10 text-center">
                            <h2 class="text-2xl font-black text-white office-status-title sm:text-3xl">
                                {{ $statusData['title'] }}
                            </h2>

                            <p class="mt-2 text-lg font-black text-[#b4c5ff]">
                                {{ $application->office_name }}
                            </p>

                            <p class="mx-auto mt-4 max-w-xl leading-8 text-[#c3c6d7]">
                                {{ $statusData['description'] }}
                            </p>
                        </div>

                        <div class="{{ $statusData['noticeClass'] }} relative z-10 mt-7 flex w-full max-w-xl items-start gap-4 rounded-xl border p-4 text-right">
                            <span class="office-status-icon material-symbols-outlined {{ $statusData['noticeTextClass'] }} mt-0.5">
                                info
                            </span>

                            <div>
                                <h3 class="text-xs font-black uppercase tracking-[.14em] {{ $statusData['noticeTextClass'] }}">
                                    {{ $statusData['noticeTitle'] }}
                                </h3>

                                <p class="mt-2 text-sm leading-7 text-[#c3c6d7]">
                                    {{ $statusData['noticeText'] }}
                                </p>
                            </div>
                        </div>

                        @if ($application->status === 'rejected' && $application->rejection_reason)
                            <div class="relative z-10 w-full max-w-xl p-4 mt-5 text-right border rounded-xl border-red-400/20 bg-red-500/10">
                                <h3 class="font-black text-red-200">سبب الرفض</h3>
                                <p class="mt-2 leading-8 text-red-100">
                                    {{ $application->rejection_reason }}
                                </p>
                            </div>
                        @endif

                        @if ($application->status === 'approved' && $latestSubscription)
                            <div class="relative z-10 w-full max-w-xl p-4 mt-5 text-right border rounded-xl border-emerald-400/20 bg-emerald-500/10">
                                <h3 class="font-black text-emerald-200">تفاصيل الاشتراك</h3>

                                <div class="grid gap-3 mt-4 sm:grid-cols-3">
                                    <div class="p-3 border rounded-lg border-white/10 bg-black/10">
                                        <span class="block text-xs text-emerald-100/70">القيمة</span>
                                        <strong class="block mt-1 text-white">
                                            {{ number_format((float) $latestSubscription->amount, 2) }}
                                            {{ $latestSubscription->currency }}
                                        </strong>
                                    </div>

                                    <div class="p-3 border rounded-lg border-white/10 bg-black/10">
                                        <span class="block text-xs text-emerald-100/70">المدة</span>
                                        <strong class="block mt-1 text-white">
                                            {{ $latestSubscription->durationLabel() }}
                                        </strong>
                                    </div>

                                    <div class="p-3 border rounded-lg border-white/10 bg-black/10">
                                        <span class="block text-xs text-emerald-100/70">الحالة</span>
                                        <strong class="block mt-1 text-white">
                                            {{ $subscriptionStatusLabel }}
                                        </strong>
                                    </div>
                                </div>

                                @if (Route::has('office.subscription'))
                                    <a
                                        href="{{ route('office.subscription') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 mt-5 font-black text-white transition rounded-full bg-emerald-600 hover:bg-emerald-500"
                                    >
                                        <span class="office-status-icon material-symbols-outlined">payments</span>
                                        فتح صفحة الاشتراك
                                    </a>
                                @endif
                            </div>
                        @endif
                    </section>

                    <section class="grid w-full grid-cols-1 gap-4 md:grid-cols-2">
                        <article class="office-status-glass flex items-center gap-4 rounded-xl p-4 transition hover:border-[#b4c5ff]/30 hover:bg-[#31394d]/10">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#434655]/30 bg-[#222a3d]">
                                <span class="office-status-icon material-symbols-outlined text-[#b4c5ff]">tag</span>
                            </div>

                            <div class="min-w-0">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-[#c3c6d7]">
                                    رقم الطلب
                                </span>

                                <span class="block mt-1 font-black text-white truncate">
                                    REQ-{{ str_pad((string) $application->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </article>

                        <article class="office-status-glass flex items-center gap-4 rounded-xl p-4 transition hover:border-[#b4c5ff]/30 hover:bg-[#31394d]/10">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#434655]/30 bg-[#222a3d]">
                                <span class="office-status-icon material-symbols-outlined text-[#b4c5ff]">event</span>
                            </div>

                            <div class="min-w-0">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-[#c3c6d7]">
                                    تاريخ التقديم
                                </span>

                                <span class="block mt-1 font-black text-white">
                                    {{ $application->created_at?->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        </article>

                        <article class="office-status-glass flex items-center gap-4 rounded-xl p-4 transition hover:border-[#b4c5ff]/30 hover:bg-[#31394d]/10">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#434655]/30 bg-[#222a3d]">
                                <span class="office-status-icon material-symbols-outlined text-[#b4c5ff]">update</span>
                            </div>

                            <div class="min-w-0">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-[#c3c6d7]">
                                    آخر تحديث
                                </span>

                                <span class="block mt-1 font-black text-white">
                                    {{ $application->updated_at?->diffForHumans() }}
                                </span>
                            </div>
                        </article>

                        <article class="office-status-glass flex items-center gap-4 rounded-xl p-4 transition hover:border-[#b4c5ff]/30 hover:bg-[#31394d]/10">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#434655]/30 bg-[#222a3d]">
                                <span class="office-status-icon material-symbols-outlined text-[#b4c5ff]">location_city</span>
                            </div>

                            <div class="min-w-0">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-[#c3c6d7]">
                                    المدينة
                                </span>

                                <span class="block mt-1 font-black text-white truncate">
                                    {{ $application->city ?: 'غير محددة' }}
                                </span>
                            </div>
                        </article>
                    </section>

                    @if ($application->reviewer)
                        <section class="w-full p-4 text-center office-status-glass rounded-xl">
                            <span class="text-xs text-[#8d90a0]">تمت المراجعة بواسطة</span>
                            <p class="mt-1 font-black text-white">{{ $application->reviewer->name }}</p>
                        </section>
                    @endif
                @endif

                <div>
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-[#434655]/50 bg-[#2d3449]/30 px-8 py-4 text-xs font-black text-white transition hover:bg-[#2d3449]/50 hover:shadow-[0_0_20px_rgba(67,70,85,.4)]"
                    >
                        <span class="text-sm office-status-icon material-symbols-outlined">arrow_back</span>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
