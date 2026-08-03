<x-app-layout>
    @php
        $isOperational =
            $office->status === 'active'
            && $office->subscription_status === 'active'
            && $office->subscription_ends_at
            && $office->subscription_ends_at->isFuture();

        $officeStatus = match ($office->status) {
            'active' => [
                'label' => 'مكتب فعال',
                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
            ],

            'suspended' => [
                'label' => 'مكتب موقوف عن العمل',
                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
            ],

            'closed' => [
                'label' => 'مكتب مغلق',
                'class' => 'text-slate-200 border-white/10 bg-white/5',
            ],

            default => [
                'label' => 'قيد المراجعة',
                'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
            ],
        };

        $subscriptionStatus = match ($office->subscription_status) {
            'active' => [
                'label' => 'اشتراك فعال',
                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
            ],

            'expired' => [
                'label' => 'اشتراك منتهي',
                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
            ],

            'cancelled' => [
                'label' => 'اشتراك ملغي',
                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
            ],

            default => [
                'label' => 'بانتظار تفعيل الاشتراك',
                'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
            ],
        };
    @endphp

    <style>
        .office-dashboard-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', sans-serif;
        }

        .office-dashboard-glass {
            background: rgba(23, 31, 51, .55);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .06);
        }

        .office-dashboard-card {
            transition:
                transform .25s ease,
                box-shadow .25s ease,
                border-color .25s ease;
        }

        .office-dashboard-card:hover {
            transform: translateY(-3px);
            border-color: rgba(180, 197, 255, .22);
            box-shadow: 0 15px 35px rgba(0, 0, 0, .18);
        }

        @media (max-width: 1023px) {
            .office-dashboard-sidebar {
                display: none !important;
            }

            .office-dashboard-main {
                margin-right: 0 !important;
            }

            .office-dashboard-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="office-dashboard-page" dir="rtl">

        <aside class="office-dashboard-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655]/10 bg-[#131b2e]/90 p-4 shadow-xl backdrop-blur-xl">
            <div class="px-4 mb-10">
                <h1 class="text-2xl font-black tracking-tight text-[#b4c5ff]">
                    CreativeHome
                </h1>

                <p class="mt-1 text-sm text-[#c3c6d7] opacity-60">
                    Engineering Office
                </p>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ route('office.dashboard') }}"
                    class="flex items-center gap-3 rounded-xl bg-[#2563eb]/20 px-4 py-3 font-bold text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>لوحة المكتب</span>
                </a>

                <a
                    href="{{ route('office.consultations.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>استشارات المكتب</span>
                </a>

                <a
                    href="{{ route('office.members.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>أعضاء المكتب</span>
                </a>

                <a
                    href="{{ route('office-membership-applications.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M6 2h9l5 5v15H6z"/>
                        <path d="M14 2v6h6M9 13h6M9 17h6"/>
                    </svg>

                    <span>طلبات الانضمام</span>

                    @if (($statistics['pending_applications'] ?? 0) > 0)
                        <span class="mr-auto rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-black text-white">
                            {{ $statistics['pending_applications'] }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('office.subscription') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>

                    <span>الاشتراك الشهري</span>
                </a>

                <a
                    href="{{ route('office.profile') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-white/5 hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>إعدادات المكتب</span>
                </a>
            </nav>

            <div class="pt-6 mt-auto space-y-2 border-t border-[#434655]/10">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-[#c3c6d7] transition hover:text-white"
                >
                    <span>حسابي</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-3 text-[#c3c6d7] transition hover:text-red-300"
                    >
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <header class="office-dashboard-topbar fixed top-0 left-0 right-64 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#060e20]/60 px-6 backdrop-blur-md">
            <div>
                <h2 class="text-xl font-black text-[#dae2fd]">
                    {{ $office->name }}
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden text-sm text-[#c3c6d7] sm:block">
                    {{ auth()->user()->name }}
                </span>

                <div class="flex items-center justify-center w-10 h-10 overflow-hidden font-black text-white border rounded-xl border-[#b4c5ff]/20 bg-gradient-to-br from-blue-600 to-purple-600">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <main class="min-h-screen px-6 pt-24 pb-12 office-dashboard-main lg:mr-64">
            <div class="mx-auto max-w-[1700px] space-y-8">

                @if (session('success'))
                    <div class="p-4 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-bold text-[#b4c5ff]">
                            لوحة المكتب الهندسي
                        </p>

                        <h1 class="mt-2 text-3xl font-black text-[#dae2fd]">
                            مرحبًا بك في {{ $office->name }}
                        </h1>

                        <p class="mt-3 text-[#c3c6d7]">
                            إدارة الاستشارات والأعضاء وطلبات الانضمام والاشتراك.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $officeStatus['class'] }}">
                            {{ $officeStatus['label'] }}
                        </span>

                        <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $subscriptionStatus['class'] }}">
                            {{ $subscriptionStatus['label'] }}
                        </span>
                    </div>
                </section>

                @if ($office->status === 'suspended')
                    <section class="p-6 border rounded-3xl border-red-500/30 bg-red-500/10">
                        <h2 class="text-xl font-black text-red-200">
                            المكتب موقوف عن العمل
                        </h2>

                        <p class="mt-3 leading-8 text-red-100">
                            لا يمكن استقبال استشارات أو طلبات انضمام جديدة حتى يعيد مدير النظام تفعيل المكتب.
                        </p>

                        @if ($office->suspension_reason)
                            <p class="p-4 mt-4 leading-7 text-red-100 border rounded-2xl border-red-500/20 bg-red-950/20">
                                {{ $office->suspension_reason }}
                            </p>
                        @endif
                    </section>
                @elseif (! $isOperational)
                    <section class="flex flex-col gap-4 p-6 border rounded-3xl border-yellow-500/20 bg-yellow-500/10 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black text-yellow-200">
                                الاشتراك غير فعال
                            </h2>

                            <p class="mt-2 leading-7 text-yellow-100">
                                ارفع إيصال الاشتراك الشهري لتفعيل جميع خصائص المكتب.
                            </p>
                        </div>

                        <a
                            href="{{ route('office.subscription') }}"
                            class="inline-flex items-center justify-center px-6 py-3 font-black text-white transition bg-yellow-600 rounded-xl hover:bg-yellow-500"
                        >
                            إدارة الاشتراك
                        </a>
                    </section>
                @endif

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="flex items-center justify-between p-5 office-dashboard-glass office-dashboard-card rounded-2xl">
                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">
                                أعضاء فعالون
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-[#dae2fd]">
                                {{ $statistics['active_members'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-blue-500/10">
                            👥
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 office-dashboard-glass office-dashboard-card rounded-2xl">
                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">
                                طلبات انضمام معلقة
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-yellow-300">
                                {{ $statistics['pending_applications'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-yellow-500/10">
                            📄
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 office-dashboard-glass office-dashboard-card rounded-2xl">
                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">
                                استشارات قيد التنفيذ
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-blue-300">
                                {{ $statistics['in_progress_consultations'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-blue-500/10">
                            🛠️
                        </div>
                    </article>

                    <article class="flex items-center justify-between p-5 office-dashboard-glass office-dashboard-card rounded-2xl">
                        <div>
                            <p class="text-xs font-bold text-[#8d90a0]">
                                استشارات مكتملة
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-green-300">
                                {{ $statistics['completed_consultations'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-green-500/10">
                            ✅
                        </div>
                    </article>
                </section>

                <section class="grid gap-6 xl:grid-cols-3">
                    <div class="space-y-6 xl:col-span-2">

                        <div class="overflow-hidden office-dashboard-glass rounded-3xl">
                            <div class="flex items-center justify-between gap-4 p-6 border-b border-[#434655]/10">
                                <div>
                                    <h2 class="text-2xl font-black text-[#dae2fd]">
                                        أحدث الاستشارات
                                    </h2>

                                    <p class="mt-2 text-sm text-[#c3c6d7]">
                                        آخر الاستشارات المحولة إلى المكتب.
                                    </p>
                                </div>

                                <a
                                    href="{{ route('office.consultations.index') }}"
                                    class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-bold text-white transition hover:brightness-110"
                                >
                                    عرض الكل
                                </a>
                            </div>

                            <div class="divide-y divide-[#434655]/10">
                                @forelse ($latestConsultations as $consultation)
                                    @php
                                        $consultationStatus = match ($consultation->status) {
                                            'completed' => [
                                                'label' => 'مكتملة',
                                                'class' => 'text-green-300 bg-green-500/10',
                                            ],

                                            'in_progress' => [
                                                'label' => 'قيد التنفيذ',
                                                'class' => 'text-blue-300 bg-blue-500/10',
                                            ],

                                            'cancelled' => [
                                                'label' => 'ملغاة',
                                                'class' => 'text-red-300 bg-red-500/10',
                                            ],

                                            default => [
                                                'label' => 'قيد الانتظار',
                                                'class' => 'text-yellow-300 bg-yellow-500/10',
                                            ],
                                        };
                                    @endphp

                                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-black text-[#dae2fd]">
                                                {{ $consultation->title }}
                                            </p>

                                            <p class="mt-1 text-sm text-[#8d90a0]">
                                                {{ $consultation->customer?->name ?? 'عميل غير معروف' }}
                                                —
                                                {{ $consultation->consultationType?->name ?? 'نوع غير محدد' }}
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            <span class="px-3 py-1 text-xs font-black rounded-full {{ $consultationStatus['class'] }}">
                                                {{ $consultationStatus['label'] }}
                                            </span>

                                            <span class="text-sm text-[#c3c6d7]">
                                                {{ $consultation->engineer?->name ?? 'بدون مهندس' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-10 text-center text-[#c3c6d7]">
                                        لا توجد استشارات محولة إلى المكتب بعد.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="overflow-hidden office-dashboard-glass rounded-3xl">
                            <div class="flex items-center justify-between gap-4 p-6 border-b border-[#434655]/10">
                                <div>
                                    <h2 class="text-2xl font-black text-[#dae2fd]">
                                        أحدث طلبات الانضمام
                                    </h2>

                                    <p class="mt-2 text-sm text-[#c3c6d7]">
                                        آخر المهندسين الذين تقدموا للمكتب.
                                    </p>
                                </div>

                                <a
                                    href="{{ route('office-membership-applications.index') }}"
                                    class="rounded-xl border border-[#434655]/30 bg-[#222a3d] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#31394d]"
                                >
                                    عرض الكل
                                </a>
                            </div>

                            <div class="divide-y divide-[#434655]/10">
                                @forelse ($latestApplications as $application)
                                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-black text-[#dae2fd]">
                                                {{ $application->engineer?->name ?? 'مهندس غير موجود' }}
                                            </p>

                                            <p class="mt-1 text-sm text-[#8d90a0]">
                                                {{ $application->specialty?->name ?? 'تخصص غير محدد' }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1 text-xs font-black text-yellow-300 rounded-full bg-yellow-500/10">
                                                {{ match ($application->status) {
                                                    'approved' => 'مقبول',
                                                    'rejected' => 'مرفوض',
                                                    default => 'قيد المراجعة',
                                                } }}
                                            </span>

                                            <a
                                                href="{{ route('office-membership-applications.show', $application) }}"
                                                class="text-sm font-bold text-[#b4c5ff] hover:text-white"
                                            >
                                                عرض الطلب
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-10 text-center text-[#c3c6d7]">
                                        لا توجد طلبات انضمام حتى الآن.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="p-6 office-dashboard-glass rounded-3xl">
                            <h2 class="text-xl font-black text-[#dae2fd]">
                                حالة الاشتراك
                            </h2>

                            <div class="mt-5 space-y-4">
                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-[#8d90a0]">
                                        القيمة الشهرية
                                    </p>

                                    <p class="mt-2 text-xl font-black text-white">
                                        {{ number_format(
                                            (float) ($office->monthly_subscription_amount ?? 1000),
                                            2
                                        ) }}
                                        {{ $office->subscription_currency ?? 'SAR' }}
                                    </p>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-[#8d90a0]">
                                        نهاية الاشتراك
                                    </p>

                                    <p class="mt-2 font-black text-white">
                                        {{ $office->subscription_ends_at?->format('Y-m-d') ?? 'غير مفعل' }}
                                    </p>
                                </div>

                                @if ($latestSubscription)
                                    <div class="p-4 rounded-2xl bg-white/5">
                                        <p class="text-xs text-[#8d90a0]">
                                            آخر طلب اشتراك
                                        </p>

                                        <p class="mt-2 font-black text-white">
                                            {{ match ($latestSubscription->status) {
                                                'active' => 'فعال',
                                                'under_review' => 'قيد المراجعة',
                                                'rejected' => 'مرفوض',
                                                'expired' => 'منتهي',
                                                default => 'بانتظار الإيصال',
                                            } }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <a
                                href="{{ route('office.subscription') }}"
                                class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#2563eb] px-5 py-3 font-black text-white transition hover:brightness-110"
                            >
                                إدارة الاشتراك
                            </a>
                        </div>

                        <div class="p-6 office-dashboard-glass rounded-3xl">
                            <h2 class="text-xl font-black text-[#dae2fd]">
                                معلومات المكتب
                            </h2>

                            <div class="mt-5 space-y-4 text-sm">
                                <div>
                                    <p class="text-[#8d90a0]">
                                        مالك المكتب
                                    </p>

                                    <p class="mt-1 font-bold text-white">
                                        {{ $office->owner?->name ?? 'غير معروف' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[#8d90a0]">
                                        البريد الإلكتروني
                                    </p>

                                    <p class="mt-1 font-bold text-white break-all">
                                        {{ $office->email }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[#8d90a0]">
                                        الموقع
                                    </p>

                                    <p class="mt-1 font-bold text-white">
                                        {{ $office->city ?: 'غير محدد' }}

                                        @if ($office->country)
                                            —
                                            {{ $office->country }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('office.profile') }}"
                                class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-[#434655]/30 bg-[#222a3d] px-5 py-3 font-bold text-white transition hover:bg-[#31394d]"
                            >
                                تعديل بيانات المكتب
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
