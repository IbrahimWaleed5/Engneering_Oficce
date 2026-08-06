<x-app-layout>
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
            'active' => 'border-amber-400/25 bg-amber-400/10 text-amber-300',
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
            'suspended_pending_review' => 'border-amber-400/25 bg-amber-400/10 text-amber-300',
            'suspended' => 'border-red-400/25 bg-red-400/10 text-red-300',
            'blocked' => 'border-red-600/30 bg-red-950/50 text-red-300',
        ];

        $decisionLabels = [
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'needs_review' => 'يحتاج مراجعة',
        ];

        $decisionClasses = [
            'approved' => 'text-emerald-300',
            'rejected' => 'text-red-300',
            'needs_review' => 'text-amber-300',
        ];

        $riskLabels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج',
        ];

        $riskClasses = [
            'low' => 'text-emerald-300',
            'medium' => 'text-amber-300',
            'high' => 'text-red-300',
            'critical' => 'text-red-400',
        ];
    @endphp

    <div class="min-h-screen px-4 py-8 text-slate-100 sm:px-6 lg:px-8" dir="rtl">
        <style>
            .moderation-detail-shell {
                --surface: #0b1326;
                --surface-lowest: #060e20;
                --surface-container: #171f33;
                --surface-high: #222a3d;
                --outline: rgba(66, 71, 84, .7);
                --primary: #adc6ff;
                --secondary: #4edea3;
                --error: #ffb4ab;
                --tertiary: #ffb95f;
            }

            .moderation-detail-card {
                background: rgba(23, 31, 51, .46);
                border: 1px solid rgba(66, 71, 84, .55);
                box-shadow: 0 4px 30px rgba(0, 0, 0, .12);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }

            .moderation-detail-card:hover {
                box-shadow: inset 0 0 15px rgba(77, 142, 255, .10);
            }

            .moderation-scroll::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            .moderation-scroll::-webkit-scrollbar-track {
                background: #060e20;
            }

            .moderation-scroll::-webkit-scrollbar-thumb {
                background: #424754;
                border-radius: 4px;
            }
        </style>

        <div class="moderation-detail-shell mx-auto max-w-[1200px]">
            {{-- رأس الصفحة --}}
            <div class="flex flex-col gap-4 mb-10 md:flex-row md:items-center md:justify-between">
                <div class="text-right">
                    <h1 class="flex flex-wrap items-center justify-start gap-3 mb-2 text-3xl font-black text-slate-100 md:text-4xl">
                        تفاصيل التحذير
                        <span class="px-3 py-1 text-sm font-black text-blue-300 rounded-md bg-slate-800">
                            #WRN-{{ str_pad((string) $warning->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </h1>

                    <p class="text-sm leading-7 text-slate-400 md:text-base">
                        مراجعة بيانات المستخدم وقرارات النظام الذكي المتعلقة بمخالفات المحتوى.
                    </p>
                </div>

                <a
                    href="{{ route('admin.moderation.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition border rounded-lg border-slate-600 text-slate-200 hover:border-blue-300 hover:text-blue-300"
                >
                    العودة لسجل التحذيرات
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                {{-- العمود الأيمن --}}
                <div class="flex flex-col gap-6 lg:col-span-4">
                    {{-- بيانات المستخدم --}}
                    <section class="p-5 transition moderation-detail-card rounded-xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-700/70">
                            <h2 class="flex items-center gap-2 text-xl font-black text-slate-100">
                                <svg class="w-6 h-6 text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 00-16 0m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                                بيانات المستخدم
                            </h2>
                        </div>

                        @if ($user)
                            <div class="flex flex-col items-center mb-5">
                                <div class="relative mb-3">
                                    <div class="flex items-center justify-center w-24 h-24 overflow-hidden text-4xl font-black text-blue-300 border-2 rounded-full shadow-lg border-slate-600 bg-slate-800">
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

                                    <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full border-2 border-[#171f33] {{ $user->status === 'active' ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                                </div>

                                <h3 class="text-lg font-black text-slate-100">
                                    {{ $user->name }}
                                </h3>

                                <span class="mt-1 text-sm text-slate-400">
                                    {{ $user->role }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between rounded-lg border border-slate-700/70 bg-[#060e20] p-3">
                                    <span class="text-xs font-bold text-slate-300">البريد:</span>
                                    <span class="max-w-[210px] truncate text-left text-xs text-slate-400" dir="ltr">
                                        {{ $user->email }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between rounded-lg border border-slate-700/70 bg-[#060e20] p-3">
                                    <span class="text-xs font-bold text-slate-300">الهاتف:</span>
                                    <span class="text-xs text-left text-slate-400" dir="ltr">
                                        {{ $user->phone ?: 'غير متوفر' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between rounded-lg border border-slate-700/70 bg-[#060e20] p-3">
                                    <span class="text-xs font-bold text-slate-300">المعرف:</span>
                                    <span class="text-xs font-black text-blue-300">
                                        USR-{{ str_pad((string) $user->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-4 mt-3 border-t border-slate-700/70">
                                    <span class="text-sm font-bold text-slate-300">
                                        عداد التحذيرات:
                                    </span>

                                    <span class="inline-flex items-center gap-2 rounded-md bg-amber-400/10 px-3 py-1.5 text-sm font-black text-amber-300">
                                        {{ $user->warnings_count ?? 0 }} / 3
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-700/70">
                                    <span class="text-sm font-bold text-slate-300">
                                        حالة الحساب:
                                    </span>

                                    <span class="rounded-lg border px-3 py-1.5 text-xs font-black {{ $accountStatusClasses[$user->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                        {{ $accountStatusLabels[$user->status] ?? $user->status }}
                                    </span>
                                </div>
                            </div>

                            @if ($user->suspension_reason)
                                <div class="p-3 mt-4 text-sm leading-7 border rounded-lg border-amber-400/20 bg-amber-400/10 text-amber-200">
                                    <strong class="block mb-1">سبب التعليق</strong>
                                    {{ $user->suspension_reason }}
                                </div>
                            @endif
                        @else
                            <div class="py-10 text-center text-slate-400">
                                هذا المستخدم لم يعد موجودًا.
                            </div>
                        @endif
                    </section>

                    {{-- بيانات التحذير --}}
                    <section class="p-5 transition moderation-detail-card rounded-xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-700/70">
                            <h2 class="flex items-center gap-2 text-xl font-black text-slate-100">
                                <svg class="w-6 h-6 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7L2.6 17a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 3.7a2 2 0 00-3.4 0z"/>
                                </svg>
                                بيانات التحذير
                            </h2>

                            <span class="rounded-full border px-3 py-1 text-xs font-black {{ $warningStatusClasses[$warning->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                {{ $warningStatusLabels[$warning->status] ?? $warning->status }}
                            </span>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-700/50">
                                <span class="font-bold text-slate-300">المصدر:</span>
                                <span class="text-slate-400">
                                    {{ $warning->issuer?->name ?? 'نظام الفحص الذكي' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pb-3 border-b border-slate-700/50">
                                <span class="font-bold text-slate-300">التاريخ:</span>
                                <span class="text-slate-400" dir="ltr">
                                    {{ $warning->created_at?->format('Y-m-d H:i') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pb-1">
                                <span class="font-bold text-slate-300">التصنيف:</span>
                                <span class="text-slate-400">
                                    {{ $warning->category ?: 'غير محدد' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                            <span class="block mb-2 text-sm font-bold text-slate-300">
                                سبب التحذير (آلي):
                            </span>

                            <p class="text-sm leading-7 text-slate-400">
                                {{ $warning->reason }}
                            </p>
                        </div>

                        @if ($warning->review_notes)
                            <div class="p-4 mt-4 border rounded-lg border-blue-400/20 bg-blue-400/10">
                                <span class="block mb-2 text-sm font-bold text-blue-200">
                                    ملاحظات الإدارة:
                                </span>

                                <p class="text-sm leading-7 text-blue-100">
                                    {{ $warning->review_notes }}
                                </p>
                            </div>
                        @endif
                    </section>
                </div>

                {{-- العمود الأيسر --}}
                <div class="flex flex-col gap-6 lg:col-span-8">
                    {{-- نتيجة فحص المحتوى --}}
                    <section class="p-5 moderation-detail-card rounded-xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-700/70">
                            <h2 class="flex items-center gap-2 text-xl font-black text-slate-100">
                                <svg class="w-6 h-6 text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V9m5 10V5m5 14v-7m5 7V3"/>
                                </svg>
                                نتيجة فحص المحتوى
                            </h2>
                        </div>

                        @if ($moderation)
                            <div class="grid grid-cols-1 gap-3 mb-6 md:grid-cols-3">
                                <div class="flex flex-col items-end rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                                    <span class="mb-2 text-xs font-bold text-slate-400">القرار الآلي</span>
                                    <strong class="flex items-center gap-2 text-lg {{ $decisionClasses[$moderation->decision] ?? 'text-slate-300' }}">
                                        {{ $decisionLabels[$moderation->decision] ?? ($moderation->decision ?: 'غير محدد') }}
                                    </strong>
                                </div>

                                <div class="flex flex-col items-end rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                                    <span class="mb-2 text-xs font-bold text-slate-400">مستوى الخطر</span>
                                    <strong class="text-lg {{ $riskClasses[$moderation->risk_level] ?? 'text-slate-300' }}">
                                        {{ $riskLabels[$moderation->risk_level] ?? ($moderation->risk_level ?: 'غير محدد') }}
                                    </strong>
                                </div>

                                <div class="flex flex-col items-end rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                                    <span class="mb-2 text-xs font-bold text-slate-400">مزود الفحص</span>
                                    <strong class="text-lg text-blue-300">
                                        {{ $moderation->provider ?: 'غير محدد' }}
                                    </strong>
                                    @if ($moderation->model)
                                        <span class="mt-1 text-xs text-slate-500">{{ $moderation->model }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-6 rounded-lg border border-slate-700/70 bg-[#171f33] p-5">
                                <h3 class="pb-3 mb-4 text-base font-black border-b border-slate-700/50 text-slate-100">
                                    تفاصيل الملف المرفق
                                </h3>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <span class="block mb-2 text-xs font-bold text-slate-400">الاسم الأصلي</span>
                                        <span class="inline-block rounded-md border border-slate-700/70 bg-[#060e20] px-3 py-2 text-sm text-slate-200" dir="ltr">
                                            {{ $moderation->original_name ?: 'غير متوفر' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="block mb-2 text-xs font-bold text-slate-400">نوع الملف (Mime)</span>
                                        <span class="inline-block rounded-md border border-slate-700/70 bg-[#060e20] px-3 py-2 text-sm text-slate-200" dir="ltr">
                                            {{ $moderation->mime_type ?: 'غير متوفر' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="block mb-2 text-xs font-bold text-slate-400">الحجم</span>
                                        <span class="inline-block rounded-md border border-slate-700/70 bg-[#060e20] px-3 py-2 text-sm text-slate-200" dir="ltr">
                                            @if ($moderation->file_size)
                                                {{ number_format($moderation->file_size / 1024 / 1024, 2) }} MB
                                            @else
                                                غير متوفر
                                            @endif
                                        </span>
                                    </div>

                                    <div>
                                        <span class="block mb-2 text-xs font-bold text-slate-400">مصدر الرفع</span>
                                        <span class="inline-block rounded-md border border-slate-700/70 bg-[#060e20] px-3 py-2 text-sm text-slate-200">
                                            {{ $moderation->source_type ?: 'غير متوفر' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if (
                                $moderation->mime_type &&
                                str_starts_with($moderation->mime_type, 'image/')
                            )
                                <div class="mb-6">
                                    <h3 class="mb-3 text-base font-black text-slate-100">
                                        معاينة الصورة
                                    </h3>

                                    <div class="flex justify-center rounded-lg border border-slate-700/70 bg-[#060e20] p-3">
                                        <img
                                            src="{{ asset('storage/' . $moderation->file_path) }}"
                                            alt="المحتوى محل المراجعة"
                                            class="max-h-[520px] max-w-full rounded-lg object-contain"
                                        >
                                    </div>
                                </div>
                            @endif

                            <div class="mb-6">
                                <h3 class="mb-3 text-base font-black text-slate-100">
                                    سبب قرار البوت
                                </h3>

                                <div class="rounded-lg border border-slate-700/70 bg-[#060e20] p-4 text-sm leading-7 text-slate-400">
                                    {{ $moderation->reason ?: 'لم يتم تسجيل سبب.' }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 mb-6 md:grid-cols-2">
                                <div>
                                    <h3 class="mb-3 text-base font-black text-slate-100">
                                        التصنيفات المكتشفة
                                    </h3>

                                    <div class="min-h-28 rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                                        @if (! empty($moderation->detected_categories))
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($moderation->detected_categories as $category)
                                                    <span class="rounded-md border border-red-400/20 bg-red-400/10 px-3 py-1.5 text-xs font-black text-red-300">
                                                        {{ is_array($category) ? json_encode($category, JSON_UNESCAPED_UNICODE) : $category }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-500">لا توجد تصنيفات مسجلة.</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h3 class="mb-3 text-base font-black text-slate-100">
                                        نسب التصنيف
                                    </h3>

                                    <div class="min-h-28 rounded-lg border border-slate-700/70 bg-[#060e20] p-4">
                                        @if (! empty($moderation->category_scores))
                                            <div class="space-y-3">
                                                @foreach ($moderation->category_scores as $key => $score)
                                                    <div class="flex items-center justify-between pb-2 border-b border-slate-700/50 last:border-0">
                                                        <span class="text-xs text-slate-400">{{ $key }}</span>
                                                        <strong class="text-xs text-blue-300">
                                                            @if (is_numeric($score))
                                                                {{ round((float) $score * 100, 2) }}%
                                                            @else
                                                                {{ is_array($score) ? json_encode($score, JSON_UNESCAPED_UNICODE) : $score }}
                                                            @endif
                                                        </strong>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-500">لا توجد نسب مسجلة.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (! empty($moderation->provider_response))
                                <div>
                                    <h3 class="mb-3 text-base font-black text-slate-100">
                                        الاستجابة التقنية الكاملة (JSON)
                                    </h3>

                                    <div class="moderation-scroll overflow-x-auto rounded-lg border border-slate-700/70 bg-[#020617] p-4 text-left" dir="ltr">
                                        <pre class="text-xs leading-6 text-blue-200 whitespace-pre-wrap">{{ json_encode(
                                            $moderation->provider_response,
                                            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                        ) }}</pre>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-5 border rounded-xl border-amber-400/20 bg-amber-400/10 text-amber-200">
                                لا توجد نتيجة فحص مرتبطة بهذا التحذير.
                            </div>
                        @endif
                    </section>

                    {{-- قرارات الإدارة --}}
                    <section class="p-5 moderation-detail-card rounded-xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-700/70">
                            <h2 class="flex items-center gap-2 text-xl font-black text-slate-100">
                                <svg class="w-6 h-6 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20h16M7 17l10-10M14 4l3 3M4 13l7 7"/>
                                </svg>
                                قرارات الإدارة
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            @if (in_array($warning->status, ['active', 'appealed'], true))
                                <div class="relative overflow-hidden rounded-lg border border-emerald-400/30 bg-[#171f33] p-5">
                                    <h3 class="flex items-center justify-end gap-2 mb-3 text-lg font-black text-emerald-300">
                                        تأكيد التحذير
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </h3>

                                    <form method="POST" action="{{ route('admin.moderation.confirm', $warning) }}">
                                        @csrf
                                        @method('PATCH')

                                        <label class="block mb-2 text-xs font-bold text-slate-400">
                                            ملاحظات الإدارة (اختياري)
                                        </label>

                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            maxlength="3000"
                                            placeholder="أضف ملاحظات داخلية..."
                                            class="mb-4 w-full rounded-lg border border-slate-700 bg-[#020617] p-3 text-sm text-slate-100 outline-none placeholder:text-slate-600 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            class="flex items-center justify-center w-full gap-2 py-3 text-sm font-black transition rounded-lg bg-emerald-400 text-emerald-950 hover:bg-emerald-300"
                                        >
                                            تأكيد واعتماد
                                        </button>
                                    </form>
                                </div>

                                <div class="relative overflow-hidden rounded-lg border border-red-400/30 bg-[#171f33] p-5">
                                    <h3 class="flex items-center justify-end gap-2 mb-3 text-lg font-black text-red-300">
                                        إلغاء التحذير
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                            <path stroke-linecap="round" stroke-width="1.8" d="M8 8l8 8m0-8l-8 8"/>
                                        </svg>
                                    </h3>

                                    <form method="POST" action="{{ route('admin.moderation.cancel', $warning) }}">
                                        @csrf
                                        @method('PATCH')

                                        <label class="block mb-2 text-xs font-bold text-slate-400">
                                            سبب الإلغاء (مطلوب)
                                        </label>

                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            maxlength="3000"
                                            required
                                            placeholder="اذكر سبب رفض قرار النظام الذكي..."
                                            class="mb-4 w-full rounded-lg border border-red-400/40 bg-[#020617] p-3 text-sm text-slate-100 outline-none placeholder:text-slate-600 focus:border-red-400 focus:ring-1 focus:ring-red-400"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            class="flex items-center justify-center w-full gap-2 py-3 text-sm font-black text-red-300 transition border border-red-300 rounded-lg hover:bg-red-300 hover:text-red-950"
                                        >
                                            تجاهل وإلغاء
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($user && $user->status === 'suspended_pending_review')
                                <div class="relative overflow-hidden rounded-lg border border-blue-400/30 bg-[#171f33] p-5">
                                    <h3 class="mb-3 text-lg font-black text-blue-300">
                                        إعادة تفعيل الحساب
                                    </h3>

                                    <form method="POST" action="{{ route('admin.moderation.reactivate', $user) }}">
                                        @csrf
                                        @method('PATCH')

                                        <label class="block mb-2 text-xs font-bold text-slate-400">
                                            ملاحظات القرار (مطلوب)
                                        </label>

                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            maxlength="3000"
                                            required
                                            class="mb-4 w-full rounded-lg border border-blue-400/30 bg-[#020617] p-3 text-sm text-slate-100 outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            class="w-full py-3 text-sm font-black transition bg-blue-400 rounded-lg text-blue-950 hover:bg-blue-300"
                                        >
                                            إعادة تفعيل الحساب
                                        </button>
                                    </form>
                                </div>

                                <div class="relative overflow-hidden rounded-lg border border-amber-400/30 bg-[#171f33] p-5">
                                    <h3 class="mb-3 text-lg font-black text-amber-300">
                                        تثبيت تعليق الحساب
                                    </h3>

                                    <form method="POST" action="{{ route('admin.moderation.keep-suspended', $user) }}">
                                        @csrf
                                        @method('PATCH')

                                        <label class="block mb-2 text-xs font-bold text-slate-400">
                                            سبب تثبيت التعليق (مطلوب)
                                        </label>

                                        <textarea
                                            name="review_notes"
                                            rows="3"
                                            maxlength="3000"
                                            required
                                            class="mb-4 w-full rounded-lg border border-amber-400/30 bg-[#020617] p-3 text-sm text-slate-100 outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            class="w-full py-3 text-sm font-black transition rounded-lg bg-amber-400 text-amber-950 hover:bg-amber-300"
                                        >
                                            تثبيت التعليق
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if (
                                ! in_array($warning->status, ['active', 'appealed'], true)
                                && (
                                    ! $user
                                    || $user->status !== 'suspended_pending_review'
                                )
                            )
                                <div class="p-5 text-center text-blue-200 border md:col-span-2 rounded-xl border-blue-400/20 bg-blue-400/10">
                                    تمت مراجعة هذه الحالة ولا توجد إجراءات متاحة حاليًا.
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
