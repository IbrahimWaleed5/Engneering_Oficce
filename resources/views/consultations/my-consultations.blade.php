<x-app-layout>

    <div
        class="relative py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <x-page-header
                title="استشاراتي"
                description="تابع حالة الطلب والدفع والمهندس والملفات النهائية"
                icon="📋"
            >
                <x-slot name="actions">

                    <a
                        href="{{ route('consultations.create') }}"
                        class="primary-button"
                    >
                        <span>➕</span>
                        استشارة جديدة
                    </a>

                </x-slot>
            </x-page-header>

            <x-alerts />

            <div class="grid gap-6">

                @forelse ($consultations as $consultation)

                    <article
                        class="p-6 glass-card rounded-[2rem] fade-up"
                    >

                        <div
                            class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
                        >

                            {{-- معلومات الاستشارة --}}
                            <div class="flex-1">

                                <div
                                    class="flex flex-wrap items-center gap-3"
                                >

                                    <span
                                        class="px-3 py-2 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300"
                                    >
                                        {{ $consultation->consultation_number }}
                                    </span>

                                    @if ($consultation->status === 'waiting_payment')

                                        <span
                                            class="text-orange-200 status-badge bg-orange-500/10"
                                        >
                                            بانتظار الدفع
                                        </span>

                                    @elseif ($consultation->status === 'pending')

                                        <span
                                            class="text-yellow-200 status-badge bg-yellow-500/10"
                                        >
                                            قيد المراجعة
                                        </span>

                                    @elseif ($consultation->status === 'in_progress')

                                        <span
                                            class="text-blue-200 status-badge bg-blue-500/10"
                                        >
                                            قيد التنفيذ
                                        </span>

                                    @elseif ($consultation->status === 'completed')

                                        <span
                                            class="text-green-200 status-badge bg-green-500/10"
                                        >
                                            مكتملة
                                        </span>

                                    @elseif ($consultation->status === 'cancelled')

                                        <span
                                            class="text-red-200 status-badge bg-red-500/10"
                                        >
                                            ملغاة
                                        </span>

                                    @endif

                                </div>

                                <h2
                                    class="mt-5 text-2xl font-black text-white"
                                >
                                    {{ $consultation->title }}
                                </h2>

                                <div
                                    class="flex flex-wrap mt-5 text-sm gap-x-7 gap-y-4 text-slate-400"
                                >

                                    {{-- نوع الاستشارة --}}
                                    <p class="flex items-center gap-2">

                                        <span>📐</span>

                                        {{ $consultation
                                            ->consultationType
                                            ?->name
                                            ?? 'غير محدد' }}

                                    </p>

                                    {{-- المهندس --}}
                                    @if ($consultation->engineer)

                                        <a
                                            href="{{ route(
                                                'engineers.show',
                                                $consultation->engineer
                                            ) }}"
                                            class="flex items-center gap-3 transition hover:text-cyan-300"
                                            title="فتح صفحة المهندس"
                                        >

                                            @if (
                                                $consultation
                                                    ->engineer
                                                    ->profile_photo
                                            )

                                                <img
                                                    src="{{ asset(
                                                        'storage/' .
                                                        $consultation
                                                            ->engineer
                                                            ->profile_photo
                                                    ) }}"
                                                    alt="{{ $consultation
                                                        ->engineer
                                                        ->name }}"
                                                    class="object-cover rounded-full w-9 h-9 ring-2 ring-cyan-500/30"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center text-sm font-black text-white rounded-full w-9 h-9 bg-gradient-to-br from-blue-600 to-cyan-500"
                                                >
                                                    {{ mb_substr(
                                                        $consultation
                                                            ->engineer
                                                            ->name,
                                                        0,
                                                        1
                                                    ) }}
                                                </div>

                                            @endif

                                            <span>

                                                <span
                                                    class="block font-bold text-white"
                                                >
                                                    {{ $consultation
                                                        ->engineer
                                                        ->name }}
                                                </span>

                                                <span
                                                    class="block mt-1 text-xs text-cyan-300"
                                                >
                                                    اضغط لعرض صفحة المهندس
                                                </span>

                                            </span>

                                        </a>

                                    @else

                                        <p class="flex items-center gap-2">

                                            <span>👷</span>

                                            لم يتم تعيين مهندس

                                        </p>

                                    @endif

                                    {{-- السعر --}}
                                    <p class="flex items-center gap-2">

                                        <span>💰</span>

                                        {{ number_format(
                                            $consultation->final_price,
                                            2
                                        ) }}

                                        شيكل

                                    </p>

                                    @if ($consultation->started_at)

                                        <p class="flex items-center gap-2">

                                            <span>🚀</span>

                                            بدء العمل:
                                            {{ $consultation->started_at->format('Y-m-d H:i') }}

                                        </p>

                                    @endif

                                    @if ($consultation->expected_delivery_at)

                                        <p class="flex items-center gap-2">

                                            <span>📅</span>

                                            التسليم المتوقع:
                                            {{ $consultation->expected_delivery_at->format('Y-m-d H:i') }}

                                        </p>

                                    @endif

                                    @if ($consultation->delivered_at)

                                        <p class="flex items-center gap-2 text-green-300">

                                            <span>✅</span>

                                            تم التسليم:
                                            {{ $consultation->delivered_at->format('Y-m-d H:i') }}

                                        </p>

                                    @elseif (
                                        $consultation->expected_delivery_at
                                        && now()->greaterThan($consultation->expected_delivery_at)
                                        && $consultation->status !== 'completed'
                                    )

                                        <p class="flex items-center gap-2 text-red-300">

                                            <span>⚠️</span>

                                            متأخرة عن موعد التسليم

                                        </p>

                                    @endif

                                </div>

                            </div>

                            {{-- الإجراءات --}}
                            <div
                                class="flex flex-col gap-4 lg:min-w-[280px]"
                            >

                                {{-- حالة الدفع --}}
                                <div
                                    class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white/[0.04]"
                                >

                                    <span class="text-sm text-slate-400">
                                        حالة الدفع
                                    </span>

                                    @if ($consultation->payment_status === 'unpaid')

                                        <span
                                            class="text-red-200 status-badge bg-red-500/10"
                                        >
                                            غير مدفوع
                                        </span>

                                    @elseif ($consultation->payment_status === 'pending')

                                        <span
                                            class="text-yellow-200 status-badge bg-yellow-500/10"
                                        >
                                            قيد الفحص
                                        </span>

                                    @elseif ($consultation->payment_status === 'paid')

                                        <span
                                            class="text-green-200 status-badge bg-green-500/10"
                                        >
                                            تم الدفع
                                        </span>

                                    @endif

                                </div>

                                <div
                                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-1"
                                >

                                    {{-- الدفع --}}
                                    @if ($consultation->payment_status === 'unpaid')

                                        <a
                                            href="{{ route(
                                                'payments.create',
                                                $consultation
                                            ) }}"
                                            class="primary-button"
                                        >
                                            💳 أكمل الدفع
                                        </a>

                                    @elseif ($consultation->payment_status === 'pending')

                                        <div
                                            class="px-4 py-3 text-sm font-bold text-center text-yellow-100 border rounded-xl border-yellow-500/20 bg-yellow-500/10"
                                        >
                                            الإيصال تحت المراجعة
                                        </div>

                                    @endif

                                {{-- فاتورة المكتب --}}
@if (
    $consultation->payment_status === 'paid'
    && $consultation->invoice
)

    <div
        class="p-4 border rounded-2xl border-cyan-500/20 bg-cyan-500/10"
    >

        <div class="flex items-center gap-3 mb-4">

            <div
                class="flex items-center justify-center text-xl border w-11 h-11 rounded-xl border-cyan-400/20 bg-cyan-500/10"
            >
                🧾
            </div>

            <div>

                <p class="font-black text-white">
                    فاتورة المكتب
                </p>

                <p class="mt-1 text-xs text-cyan-200/70">
                    فاتورة رسمية صادرة بعد اعتماد الدفع
                </p>

            </div>

        </div>

        <div class="grid gap-3">

            <a
                href="{{ route(
                    'invoices.show',
                    $consultation->invoice
                ) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 font-bold text-blue-100 transition border rounded-xl border-blue-500/20 bg-blue-500/10 hover:bg-blue-500/20"
            >
                👁️ عرض فاتورة المكتب
            </a>

            <a
                href="{{ route(
                    'invoices.download',
                    $consultation->invoice
                ) }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 font-bold text-green-100 transition border rounded-xl border-green-500/20 bg-green-500/10 hover:bg-green-500/20"
            >
                📄 تحميل الفاتورة PDF
            </a>

        </div>

    </div>

@elseif (
    $consultation->payment_status === 'paid'
    && ! $consultation->invoice
)

    <div
        class="px-4 py-3 text-sm font-bold text-center border text-slate-300 rounded-xl border-white/10 bg-white/5"
    >
        🧾 جاري تجهيز فاتورة المكتب
    </div>

@endif

                                    {{-- الملف النهائي --}}
                                    @if ($consultation->engineer_file)

                                        <a
                                            href="{{ asset(
                                                'storage/' .
                                                $consultation
                                                    ->engineer_file
                                            ) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="secondary-button"
                                        >
                                            📥 تحميل الملف النهائي
                                        </a>

                                    @endif

                                    {{-- المحادثة --}}
                                    @if (
                                        $consultation->payment_status === 'paid'
                                        && $consultation->engineer_id
                                    )

                                        <a
                                            href="{{ route(
                                                'consultations.messages.index',
                                                $consultation
                                            ) }}"
                                            class="secondary-button"
                                        >
                                            💬 المحادثة مع المهندس
                                        </a>

                                    @endif

                                    {{-- صفحة المهندس --}}
                                    @if ($consultation->engineer)

                                        <a
                                            href="{{ route(
                                                'engineers.show',
                                                $consultation->engineer
                                            ) }}"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-3 font-bold transition border text-cyan-200 rounded-xl border-cyan-500/20 bg-cyan-500/10 hover:bg-cyan-500/20"
                                        >
                                            👤 صفحة المهندس
                                        </a>

                                    @endif

                                    {{-- التقييم --}}
                                    {{-- رأي وتقييم العميل --}}
@if (
    $consultation->status === 'completed'
    && $consultation->payment_status === 'paid'
)

    @if ($consultation->review)

        <div
            class="px-5 py-4 border rounded-2xl border-yellow-500/20 bg-yellow-500/10"
        >

            <div
                class="flex flex-wrap items-center justify-between gap-3"
            >

                <p class="font-black text-yellow-300">
                    ⭐ تقييمك للخدمة
                </p>

                @if ($consultation->review->status === 'pending')

                    <span
                        class="px-3 py-1 text-xs font-bold text-yellow-200 rounded-full bg-yellow-500/10"
                    >
                        قيد مراجعة الإدارة
                    </span>

                @elseif ($consultation->review->status === 'approved')

                    <span
                        class="px-3 py-1 text-xs font-bold text-green-200 rounded-full bg-green-500/10"
                    >
                        تم اعتماد الرأي
                    </span>

                @elseif ($consultation->review->status === 'rejected')

                    <span
                        class="px-3 py-1 text-xs font-bold text-red-200 rounded-full bg-red-500/10"
                    >
                        لم يتم اعتماد الرأي
                    </span>

                @endif

            </div>

            {{-- النجوم --}}
            <div
                class="flex items-center gap-1 mt-3"
            >

                @for ($star = 1; $star <= 5; $star++)

                    <span
                        class="text-2xl {{ $star <= $consultation->review->rating
                            ? 'text-yellow-400'
                            : 'text-slate-700' }}"
                    >
                        ★
                    </span>

                @endfor

                <span class="mr-2 text-sm text-slate-400">
                    {{ $consultation->review->rating }}/5
                </span>

            </div>

            {{-- ملاحظة العميل --}}
            <div
                class="p-3 mt-4 border rounded-xl border-white/10 bg-black/10"
            >

                <p class="mb-2 text-xs font-bold text-slate-400">
                    رأيك وملاحظاتك
                </p>

                <p
                    class="text-sm leading-7 whitespace-pre-line text-slate-200"
                >
                    {{ $consultation->review->comment }}
                </p>

            </div>

        </div>

    @else

        <a
            href="{{ route(
                'reviews.create',
                $consultation
            ) }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 font-black text-white transition bg-yellow-600 rounded-xl hover:bg-yellow-500"
        >
            ⭐ أضف تقييمك وملاحظاتك
        </a>

    @endif

@endif

                                </div>

                            </div>

                        </div>

                    </article>

                @empty

                    <div
                        class="p-14 text-center glass-panel rounded-[2rem]"
                    >

                        <div class="mb-5 text-6xl">
                            📭
                        </div>

                        <h2
                            class="text-2xl font-black text-white"
                        >
                            لا توجد استشارات حتى الآن
                        </h2>

                        <p class="mt-3 text-slate-400">
                            أرسل أول طلب استشارة وابدأ متابعة مشروعك.
                        </p>

                        <a
                            href="{{ route('consultations.create') }}"
                            class="mt-7 primary-button"
                        >
                            إنشاء استشارة
                        </a>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>
