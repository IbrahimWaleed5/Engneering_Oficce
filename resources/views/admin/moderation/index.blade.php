<x-app-layout>
    <div
        class="min-h-screen px-4 py-8 text-slate-100 sm:px-6 lg:px-8"
        dir="rtl"
        x-data="{ activeModal: null }"
        @keydown.escape.window="activeModal = null"
    >
        <style>
            .moderation-shell {
                --m-primary: #3b82f6;
                --m-surface: #0b1326;
                --m-panel: rgba(19, 30, 54, 0.72);
                --m-panel-high: rgba(26, 40, 70, 0.76);
                --m-border: rgba(255, 255, 255, 0.08);
                --m-muted: #94a3b8;
            }

            .moderation-glass {
                background: var(--m-panel);
                border: 1px solid var(--m-border);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }

            .moderation-glass:hover {
                border-color: rgba(255, 255, 255, 0.14);
            }

            .moderation-stat {
                position: relative;
                overflow: hidden;
            }

            .moderation-stat::before {
                position: absolute;
                top: 0;
                right: 0;
                left: 0;
                height: 2px;
                content: "";
                opacity: 0;
                background: linear-gradient(
                    90deg,
                    transparent,
                    var(--stat-color, #3b82f6),
                    transparent
                );
                transition: opacity .25s ease;
            }

            .moderation-stat:hover::before {
                opacity: 1;
            }

            .moderation-scroll::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .moderation-scroll::-webkit-scrollbar-track {
                background: rgba(11, 19, 38, .5);
            }

            .moderation-scroll::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: rgba(148, 163, 184, .3);
            }

            @media print {
                nav,
                header,
                .no-print,
                .moderation-filters,
                .moderation-actions {
                    display: none !important;
                }

                body,
                .moderation-shell {
                    background: #fff !important;
                    color: #111827 !important;
                }

                .moderation-glass {
                    border: 1px solid #d1d5db !important;
                    background: #fff !important;
                    box-shadow: none !important;
                }

                .moderation-shell * {
                    color: #111827 !important;
                }
            }
        </style>

        <div class="moderation-shell relative mx-auto max-w-[1600px]">
            <div
                class="pointer-events-none absolute -top-40 left-1/4 -z-10 h-[600px] w-[600px] rounded-full bg-blue-500/5 blur-[120px]"
            ></div>

            {{-- رأس الصفحة --}}
            <section class="flex flex-col gap-5 mb-8 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="mb-2 text-3xl font-black tracking-tight text-white">
                        سجل التحذيرات والمخالفات
                    </h1>

                    <p class="text-sm font-medium text-slate-400">
                        مراجعة مخالفات المحتوى ومتابعة تحذيرات المستخدمين بدقة
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 no-print">
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/10 bg-slate-900/70 px-4 py-2.5 text-sm font-bold text-slate-200 transition hover:border-blue-400/40 hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7 7-7m-7 7h18"/>
                        </svg>
                        العودة إلى لوحة التحكم
                    </a>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-400/20 bg-blue-500/10 px-4 py-2.5 text-sm font-bold text-blue-300 shadow-[0_0_15px_rgba(59,130,246,.15)] transition hover:bg-blue-500 hover:text-white hover:shadow-[0_0_20px_rgba(59,130,246,.35)]"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0l-4-4m4 4l4-4M5 19h14"/>
                        </svg>
                        تصدير التقرير
                    </button>
                </div>
            </section>

            {{-- التنبيهات --}}
            @if (session('success'))
                <div class="px-5 py-4 mb-6 border rounded-xl border-emerald-500/25 bg-emerald-500/10 text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="px-5 py-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="px-5 py-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- الإحصائيات --}}
            <section class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-5">
                <article
                    class="p-6 text-center moderation-glass moderation-stat rounded-xl"
                    style="--stat-color:#3b82f6"
                >
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 text-blue-400 rounded-full bg-blue-500/10">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7L2.6 17a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 3.7a2 2 0 00-3.4 0z"/>
                        </svg>
                    </div>
                    <strong class="block mb-1 text-4xl font-black text-white">
                        {{ number_format($statistics['all_warnings']) }}
                    </strong>
                    <span class="text-xs font-bold tracking-wide text-slate-400">
                        جميع التحذيرات
                    </span>
                </article>

                <article
                    class="p-6 text-center moderation-glass moderation-stat rounded-xl"
                    style="--stat-color:#eab308"
                >
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 text-yellow-400 rounded-full bg-yellow-500/10">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 4l6 6m-1-7l2 2-9 9-4 1 1-4 9-9zM5 19h14"/>
                        </svg>
                    </div>
                    <strong class="block mb-1 text-4xl font-black text-white">
                        {{ number_format($statistics['active_warnings']) }}
                    </strong>
                    <span class="text-xs font-bold tracking-wide text-slate-400">
                        التحذيرات الفعالة
                    </span>
                </article>

                <article
                    class="p-6 text-center moderation-glass moderation-stat rounded-xl"
                    style="--stat-color:#60a5fa"
                >
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-sky-500/10 text-sky-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <strong class="block mb-1 text-4xl font-black text-white">
                        {{ number_format($statistics['pending_reviews']) }}
                    </strong>
                    <span class="text-xs font-bold tracking-wide text-slate-400">
                        بانتظار المراجعة
                    </span>
                </article>

                <article
                    class="p-6 text-center moderation-glass moderation-stat rounded-xl"
                    style="--stat-color:#ef4444"
                >
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 text-red-400 rounded-full bg-red-500/10">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="M7.5 7.5l9 9"/>
                        </svg>
                    </div>
                    <strong class="block mb-1 text-4xl font-black text-white">
                        {{ number_format($statistics['rejected_content']) }}
                    </strong>
                    <span class="text-xs font-bold tracking-wide text-slate-400">
                        محتوى مرفوض
                    </span>
                </article>

                <article
                    class="p-6 text-center moderation-glass moderation-stat rounded-xl"
                    style="--stat-color:#94a3b8"
                >
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-slate-400/10 text-slate-300">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M9.5 11a4 4 0 100-8 4 4 0 000 8zm6.5 2l5 5m0-5l-5 5"/>
                        </svg>
                    </div>
                    <strong class="block mb-1 text-4xl font-black text-white">
                        {{ number_format($statistics['suspended_accounts']) }}
                    </strong>
                    <span class="text-xs font-bold tracking-wide text-slate-400">
                        حسابات معلقة
                    </span>
                </article>
            </section>

            {{-- البحث والفلاتر --}}
            <section class="p-6 mb-8 moderation-glass moderation-filters rounded-xl">
                <form
                    method="GET"
                    action="{{ route('admin.moderation.index') }}"
                    class="space-y-5"
                >
                    <div class="relative">
                        <svg class="absolute w-5 h-5 -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                            <path stroke-linecap="round" stroke-width="1.8" d="M20 20l-4-4"/>
                        </svg>

                        <input
                            type="text"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="البحث بالاسم، البريد الإلكتروني، الهاتف أو سبب التحذير..."
                            class="w-full py-3 pl-4 pr-12 text-sm text-white transition border rounded-lg outline-none border-white/10 bg-slate-800/60 placeholder:text-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label class="block mb-2 text-xs font-bold tracking-wide text-slate-400">
                                حالة التحذير
                            </label>
                            <select
                                name="status"
                                class="w-full rounded-lg border border-white/10 bg-slate-800/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">الكل</option>
                                <option value="active" @selected(($filters['status'] ?? '') === 'active')>فعال</option>
                                <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>مؤكد</option>
                                <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>ملغي</option>
                                <option value="appealed" @selected(($filters['status'] ?? '') === 'appealed')>اعتراض</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-bold tracking-wide text-slate-400">
                                حالة الحساب
                            </label>
                            <select
                                name="account_status"
                                class="w-full rounded-lg border border-white/10 bg-slate-800/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">الكل</option>
                                <option value="active" @selected(($filters['account_status'] ?? '') === 'active')>نشط</option>
                                <option value="inactive" @selected(($filters['account_status'] ?? '') === 'inactive')>غير نشط</option>
                                <option value="suspended_pending_review" @selected(($filters['account_status'] ?? '') === 'suspended_pending_review')>معلق للمراجعة</option>
                                <option value="suspended" @selected(($filters['account_status'] ?? '') === 'suspended')>معلق</option>
                                <option value="blocked" @selected(($filters['account_status'] ?? '') === 'blocked')>محظور</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-bold tracking-wide text-slate-400">
                                نوع المخالفة
                            </label>
                            <select
                                name="category"
                                class="w-full rounded-lg border border-white/10 bg-slate-800/70 px-4 py-2.5 text-sm text-white outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">الكل</option>
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category }}"
                                        @selected(($filters['category'] ?? '') === $category)
                                    >
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button
                                type="submit"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-black text-white shadow-[0_0_20px_rgba(59,130,246,.30)] transition hover:bg-blue-600"
                            >
                                تطبيق
                            </button>

                            <a
                                href="{{ route('admin.moderation.index') }}"
                                class="flex items-center justify-center rounded-lg border border-white/10 bg-slate-800 px-4 py-2.5 text-sm font-black text-white transition hover:bg-slate-700"
                            >
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </section>

            {{-- الجدول --}}
            <section class="overflow-hidden moderation-glass rounded-xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <div>
                        <h2 class="font-black text-white">سجل التحذيرات</h2>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ number_format($warnings->total()) }} نتيجة
                        </p>
                    </div>
                </div>

                @if ($warnings->isEmpty())
                    <div class="flex min-h-[360px] flex-col items-center justify-center px-6 py-20 text-center">
                        <div class="flex items-center justify-center w-20 h-20 mb-5 text-blue-400 border rounded-full shadow-lg border-white/10 bg-slate-800/70">
                            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="9" stroke-width="1.6"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12l2.5 2.5L16 9"/>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-black text-white">
                            لا توجد تحذيرات حاليًا
                        </h3>
                        <p class="text-sm text-slate-400">
                            لم يتم تسجيل أي مخالفة مطابقة لمعايير البحث المحددة.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto moderation-scroll">
                        <table class="w-full min-w-[1100px] text-right">
                            <thead class="border-b border-white/10 bg-slate-800/65">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">المستخدم</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">نوع المخالفة</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">رقم التحذير</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">حالة التحذير</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">حالة الحساب</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">المصدر</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-400">التاريخ</th>
                                    <th class="px-6 py-4 text-xs font-bold text-center text-slate-400">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/5">
                                @foreach ($warnings as $warning)
                                    @php
                                        $user = $warning->user;
                                        $moderation = $warning->moderation;

                                        $warningStatusLabels = [
                                            'active' => 'فعال',
                                            'confirmed' => 'مؤكد',
                                            'cancelled' => 'ملغي',
                                            'appealed' => 'اعتراض',
                                        ];

                                        $warningStatusClasses = [
                                            'active' => 'border-yellow-400/25 bg-yellow-400/10 text-yellow-300',
                                            'confirmed' => 'border-red-400/25 bg-red-400/10 text-red-300',
                                            'cancelled' => 'border-slate-400/25 bg-slate-400/10 text-slate-300',
                                            'appealed' => 'border-sky-400/25 bg-sky-400/10 text-sky-300',
                                        ];

                                        $accountStatusLabels = [
                                            'active' => 'نشط',
                                            'inactive' => 'غير نشط',
                                            'suspended_pending_review' => 'معلق للمراجعة',
                                            'suspended' => 'معلق',
                                            'blocked' => 'محظور',
                                        ];

                                        $accountStatusClasses = [
                                            'active' => 'border-emerald-400/25 bg-emerald-400/10 text-emerald-300',
                                            'inactive' => 'border-slate-400/25 bg-slate-400/10 text-slate-300',
                                            'suspended_pending_review' => 'border-yellow-400/25 bg-yellow-400/10 text-yellow-300',
                                            'suspended' => 'border-red-400/25 bg-red-400/10 text-red-300',
                                            'blocked' => 'border-red-600/30 bg-red-950/50 text-red-300',
                                        ];
                                    @endphp

                                    <tr class="transition hover:bg-white/[0.025]">
                                        <td class="px-6 py-4">
                                            @if ($user)
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center justify-center flex-none overflow-hidden font-black text-blue-300 border rounded-lg h-11 w-11 border-white/10 bg-slate-800">
                                                        @if ($user->profile_photo)
                                                            <img
                                                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                                                alt="{{ $user->name }}"
                                                                class="object-cover w-full h-full"
                                                            >
                                                        @else
                                                            {{ mb_substr($user->name, 0, 1) }}
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-white">{{ $user->name }}</div>
                                                        <div class="mt-1 text-xs text-slate-400">{{ $user->email }}</div>
                                                        @if ($user->phone)
                                                            <div class="mt-1 text-xs text-slate-500">{{ $user->phone }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-500">مستخدم محذوف</span>
                                            @endif
                                        </td>

                                        <td class="max-w-[320px] px-6 py-4">
                                            <div class="font-bold text-white">
                                                {{ $warning->category ?: 'غير محدد' }}
                                            </div>
                                            <div class="mt-1 text-xs leading-6 text-slate-400">
                                                {{ \Illuminate\Support\Str::limit($warning->reason, 100) }}
                                            </div>
                                            @if ($moderation?->risk_level)
                                                <span class="mt-2 inline-flex rounded-md border border-orange-400/20 bg-orange-400/10 px-2 py-1 text-[11px] font-bold text-orange-300">
                                                    الخطورة: {{ $moderation->risk_level }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-lg border border-blue-400/25 bg-blue-400/10 px-3 py-1.5 text-sm font-black text-blue-300">
                                                {{ $warning->warning_number }} / 3
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-lg border px-3 py-1.5 text-xs font-black {{ $warningStatusClasses[$warning->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                                {{ $warningStatusLabels[$warning->status] ?? $warning->status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($user)
                                                <span class="inline-flex rounded-lg border px-3 py-1.5 text-xs font-black {{ $accountStatusClasses[$user->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                                    {{ $accountStatusLabels[$user->status] ?? $user->status }}
                                                </span>
                                                <div class="mt-2 text-xs text-slate-500">
                                                    {{ $user->warnings_count }} تحذير
                                                </div>
                                            @else
                                                <span class="text-slate-500">—</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-bold text-slate-300">
                                                {{ $warning->issued_by_type }}
                                            </span>
                                            @if ($warning->issuer)
                                                <div class="mt-2 text-xs text-slate-500">
                                                    {{ $warning->issuer->name }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-200">
                                                {{ $warning->created_at?->format('Y-m-d') }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $warning->created_at?->format('H:i') }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 moderation-actions">
                                            <div class="flex flex-wrap justify-center gap-2">
                                                <a
                                                    href="{{ route('admin.moderation.show', $warning) }}"
                                                    class="px-3 py-2 text-xs font-black text-blue-300 transition border rounded-lg border-blue-400/25 bg-blue-400/10 hover:bg-blue-500 hover:text-white"
                                                >
                                                    التفاصيل
                                                </a>

                                                @if (in_array($warning->status, ['active', 'appealed'], true))
                                                    <button
                                                        type="button"
                                                        @click="activeModal = 'confirm-{{ $warning->id }}'"
                                                        class="px-3 py-2 text-xs font-black transition border rounded-lg border-emerald-400/25 bg-emerald-400/10 text-emerald-300 hover:bg-emerald-500 hover:text-white"
                                                    >
                                                        تأكيد
                                                    </button>

                                                    <button
                                                        type="button"
                                                        @click="activeModal = 'cancel-{{ $warning->id }}'"
                                                        class="px-3 py-2 text-xs font-black text-red-300 transition border rounded-lg border-red-400/25 bg-red-400/10 hover:bg-red-500 hover:text-white"
                                                    >
                                                        إلغاء
                                                    </button>
                                                @endif

                                                @if ($user && $user->status === 'suspended_pending_review')
                                                    <button
                                                        type="button"
                                                        @click="activeModal = 'reactivate-{{ $warning->id }}'"
                                                        class="px-3 py-2 text-xs font-black transition border rounded-lg border-emerald-400/25 bg-emerald-400/10 text-emerald-300 hover:bg-emerald-500 hover:text-white"
                                                    >
                                                        إعادة التفعيل
                                                    </button>

                                                    <button
                                                        type="button"
                                                        @click="activeModal = 'suspend-{{ $warning->id }}'"
                                                        class="px-3 py-2 text-xs font-black text-red-300 transition border rounded-lg border-red-400/25 bg-red-400/10 hover:bg-red-500 hover:text-white"
                                                    >
                                                        تثبيت التعليق
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- نافذة تأكيد التحذير --}}
                                    <template x-teleport="body">
                                        <div
                                            x-show="activeModal === 'confirm-{{ $warning->id }}'"
                                            x-transition.opacity
                                            x-cloak
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/85 p-4"
                                            @click.self="activeModal = null"
                                        >
                                            <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#131e36] p-6 text-right shadow-2xl" dir="rtl">
                                                <form method="POST" action="{{ route('admin.moderation.confirm', $warning) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="flex items-center justify-between mb-5">
                                                        <h3 class="text-xl font-black text-white">تأكيد التحذير</h3>
                                                        <button type="button" @click="activeModal = null" class="text-2xl text-slate-400 hover:text-white">×</button>
                                                    </div>

                                                    <p class="mb-4 text-sm leading-7 text-slate-300">
                                                        سيتم تثبيت التحذير رقم
                                                        <strong class="text-white">{{ $warning->warning_number }}</strong>
                                                        على حساب المستخدم.
                                                    </p>

                                                    <label class="block mb-2 text-sm font-bold text-slate-300">ملاحظات المراجعة</label>
                                                    <textarea
                                                        name="review_notes"
                                                        rows="4"
                                                        maxlength="3000"
                                                        class="w-full p-3 text-white border outline-none rounded-xl border-white/10 bg-slate-800/80 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                    ></textarea>

                                                    <div class="flex justify-end gap-3 mt-6">
                                                        <button type="button" @click="activeModal = null" class="rounded-lg border border-white/10 px-4 py-2.5 text-sm font-bold text-slate-300 hover:bg-white/5">تراجع</button>
                                                        <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-600">تأكيد التحذير</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- نافذة إلغاء التحذير --}}
                                    <template x-teleport="body">
                                        <div
                                            x-show="activeModal === 'cancel-{{ $warning->id }}'"
                                            x-transition.opacity
                                            x-cloak
                                            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/85 p-4"
                                            @click.self="activeModal = null"
                                        >
                                            <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#131e36] p-6 text-right shadow-2xl" dir="rtl">
                                                <form method="POST" action="{{ route('admin.moderation.cancel', $warning) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="flex items-center justify-between mb-5">
                                                        <h3 class="text-xl font-black text-white">إلغاء التحذير</h3>
                                                        <button type="button" @click="activeModal = null" class="text-2xl text-slate-400 hover:text-white">×</button>
                                                    </div>

                                                    <div class="p-4 mb-4 text-sm leading-7 text-yellow-200 border rounded-xl border-yellow-400/20 bg-yellow-400/10">
                                                        سيتم إلغاء التحذير وإعادة حساب عدد مخالفات المستخدم.
                                                    </div>

                                                    <label class="block mb-2 text-sm font-bold text-slate-300">سبب إلغاء التحذير</label>
                                                    <textarea
                                                        name="review_notes"
                                                        rows="4"
                                                        maxlength="3000"
                                                        required
                                                        class="w-full p-3 text-white border outline-none rounded-xl border-white/10 bg-slate-800/80 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                    ></textarea>

                                                    <div class="flex justify-end gap-3 mt-6">
                                                        <button type="button" @click="activeModal = null" class="rounded-lg border border-white/10 px-4 py-2.5 text-sm font-bold text-slate-300 hover:bg-white/5">تراجع</button>
                                                        <button type="submit" class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-black text-white hover:bg-red-600">إلغاء التحذير</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>

                                    @if ($user)
                                        {{-- إعادة التفعيل --}}
                                        <template x-teleport="body">
                                            <div
                                                x-show="activeModal === 'reactivate-{{ $warning->id }}'"
                                                x-transition.opacity
                                                x-cloak
                                                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/85 p-4"
                                                @click.self="activeModal = null"
                                            >
                                                <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#131e36] p-6 text-right shadow-2xl" dir="rtl">
                                                    <form method="POST" action="{{ route('admin.moderation.reactivate', $user) }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="flex items-center justify-between mb-5">
                                                            <h3 class="text-xl font-black text-white">إعادة تفعيل الحساب</h3>
                                                            <button type="button" @click="activeModal = null" class="text-2xl text-slate-400 hover:text-white">×</button>
                                                        </div>

                                                        <p class="mb-4 text-sm leading-7 text-slate-300">
                                                            سيتم إعادة تفعيل حساب:
                                                            <strong class="text-white">{{ $user->name }}</strong>
                                                        </p>

                                                        <label class="block mb-2 text-sm font-bold text-slate-300">ملاحظات القرار</label>
                                                        <textarea
                                                            name="review_notes"
                                                            rows="4"
                                                            maxlength="3000"
                                                            required
                                                            class="w-full p-3 text-white border outline-none rounded-xl border-white/10 bg-slate-800/80 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                        ></textarea>

                                                        <div class="flex justify-end gap-3 mt-6">
                                                            <button type="button" @click="activeModal = null" class="rounded-lg border border-white/10 px-4 py-2.5 text-sm font-bold text-slate-300 hover:bg-white/5">تراجع</button>
                                                            <button type="submit" class="rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-600">إعادة التفعيل</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- تثبيت التعليق --}}
                                        <template x-teleport="body">
                                            <div
                                                x-show="activeModal === 'suspend-{{ $warning->id }}'"
                                                x-transition.opacity
                                                x-cloak
                                                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/85 p-4"
                                                @click.self="activeModal = null"
                                            >
                                                <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#131e36] p-6 text-right shadow-2xl" dir="rtl">
                                                    <form method="POST" action="{{ route('admin.moderation.keep-suspended', $user) }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="flex items-center justify-between mb-5">
                                                            <h3 class="text-xl font-black text-white">تثبيت تعليق الحساب</h3>
                                                            <button type="button" @click="activeModal = null" class="text-2xl text-slate-400 hover:text-white">×</button>
                                                        </div>

                                                        <div class="p-4 mb-4 text-sm leading-7 text-red-200 border rounded-xl border-red-400/20 bg-red-400/10">
                                                            سيبقى المستخدم غير قادر على استخدام المنصة.
                                                        </div>

                                                        <label class="block mb-2 text-sm font-bold text-slate-300">سبب تثبيت التعليق</label>
                                                        <textarea
                                                            name="review_notes"
                                                            rows="4"
                                                            maxlength="3000"
                                                            required
                                                            class="w-full p-3 text-white border outline-none rounded-xl border-white/10 bg-slate-800/80 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                        ></textarea>

                                                        <div class="flex justify-end gap-3 mt-6">
                                                            <button type="button" @click="activeModal = null" class="rounded-lg border border-white/10 px-4 py-2.5 text-sm font-bold text-slate-300 hover:bg-white/5">تراجع</button>
                                                            <button type="submit" class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-black text-white hover:bg-red-600">تثبيت التعليق</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </template>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($warnings->hasPages())
                        <div class="px-6 py-4 border-t border-white/10">
                            {{ $warnings->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
