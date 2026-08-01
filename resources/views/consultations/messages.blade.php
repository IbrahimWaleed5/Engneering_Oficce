<x-app-layout>

    @php
        $currentUser = auth()->user();

        $messages = $consultation->messages
            ?->sortBy('created_at')
            ?? collect();

        $otherUser = $currentUser->role === 'customer'
            ? $consultation->engineer
            : $consultation->customer;

        $statusLabels = [
            'waiting_payment' => 'بانتظار الدفع',
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
        ];

        $statusClasses = [
            'waiting_payment' =>
                'border-[#FF6B5B]/30 bg-[#FF6B5B]/10 text-[#FF6B5B]',

            'pending' =>
                'border-amber-500/30 bg-amber-500/10 text-amber-300',

            'in_progress' =>
                'border-[#8B7CF6]/30 bg-[#8B7CF6]/10 text-[#C9BFFF]',

            'completed' =>
                'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',

            'cancelled' =>
                'border-red-500/30 bg-red-500/10 text-red-300',
        ];

        $canReviewEngineer =
            (int) $currentUser->id
                === (int) $consultation->customer_id
            && $consultation->engineer_id
            && $consultation->status === 'completed'
            && $consultation->payment_status === 'paid';
    @endphp

    <x-slot name="header">

        <div
            class="flex flex-wrap items-center justify-between gap-4"
        >

            {{-- المستخدم الآخر في المحادثة --}}
            @if (
                $otherUser
                && $otherUser->role === 'engineer'
            )

                <a
                    href="{{ route(
                        'engineers.show',
                        $otherUser
                    ) }}"
                    class="flex items-center min-w-0 gap-3 p-2 transition rounded-2xl sm:gap-4 hover:bg-white/5"
                    title="فتح صفحة المهندس"
                >

                    <div class="relative flex-none">

                        @if ($otherUser->profile_photo)

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $otherUser->profile_photo
                                ) }}"
                                alt="{{ $otherUser->name }}"
                                class="object-cover w-10 h-10 border-2 rounded-full sm:w-12 sm:h-12 border-[#FF6B5B]/40"
                            >

                        @else

                            <div
                                class="flex items-center justify-center w-10 h-10 font-black text-white rounded-full sm:w-12 sm:h-12 bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]"
                            >
                                {{ mb_substr(
                                    $otherUser->name,
                                    0,
                                    1
                                ) }}
                            </div>

                        @endif

                        <span
                            class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-[#3A4A66] border-[#0A1220]"
                        ></span>

                    </div>

                    <div class="min-w-0">

                        <h2 class="text-lg font-black text-white truncate sm:text-xl">
                            المحادثة
                        </h2>

                        <p
                            class="mt-1 text-sm font-bold text-[#FF6B5B] truncate"
                        >
                            {{ $otherUser->name }}
                        </p>

                        <p class="mt-1 text-xs text-[#6B7A93]">
                            <span class="presence-status">غير متصل</span>
                        </p>

                    </div>

                </a>

            @else

                <div class="flex items-center min-w-0 gap-3 sm:gap-4">

                    <div class="relative flex-none">

                        @if ($otherUser?->profile_photo)

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $otherUser->profile_photo
                                ) }}"
                                alt="{{ $otherUser->name }}"
                                class="object-cover w-10 h-10 border-2 rounded-full sm:w-12 sm:h-12 border-[#FF6B5B]/40"
                            >

                        @else

                            <div
                                class="flex items-center justify-center w-10 h-10 font-black text-white rounded-full sm:w-12 sm:h-12 bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]"
                            >
                                {{ mb_substr(
                                    $otherUser?->name
                                        ?? 'م',
                                    0,
                                    1
                                ) }}
                            </div>

                        @endif

                        <span
                            class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-[#3A4A66] border-[#0A1220]"
                        ></span>

                    </div>

                    <div class="min-w-0">

                        <h2 class="text-lg font-black text-white truncate sm:text-xl">
                            المحادثة
                        </h2>

                        <p class="mt-1 text-sm text-[#9FADC7] truncate">
                            {{ $otherUser?->name
                                ?? 'المستخدم' }}
                        </p>

                        <p class="mt-1 text-xs text-[#6B7A93]">
                            <span class="presence-status">غير متصل</span>
                        </p>

                    </div>

                </div>

            @endif

            <a
                href="{{ url()->previous() !== url()->current()
                    ? url()->previous()
                    : route('dashboard') }}"
                class="secondary-button"
            >
                ← رجوع
            </a>

        </div>

    </x-slot>

    <div
        class="min-h-screen py-3 sm:py-8 bg-gradient-to-br from-[#0A1220] via-[#111B2E] to-[#0A1220]"
        dir="rtl"
    >

        <div
            class="px-4 mx-auto max-w-[1500px] sm:px-6 lg:px-8"
        >

            {{-- رسائل النجاح --}}
            @if (session('success'))

                <div
                    class="p-4 mb-6 border text-emerald-200 rounded-2xl border-emerald-500/30 bg-emerald-500/10"
                >
                    {{ session('success') }}
                </div>

            @endif

            {{-- الأخطاء --}}
            @if ($errors->any())

                <div
                    class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/30 bg-red-500/10"
                >

                    <ul class="space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div
                class="grid items-start grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_360px]"
            >

                {{-- الشريط الجانبي --}}
                <aside id="consultationDetailsPanel" class="hidden space-y-5 xl:block xl:order-2">

                    {{-- تفاصيل الاستشارة --}}
                    <section
                        class="p-5 border shadow-2xl rounded-3xl border-[#25344C] bg-[#111B2E]/85 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-5 border-b border-[#25344C]"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-[#FF6B5B]/15"
                            >
                                📄
                            </div>

                            <h3 class="text-lg font-black text-white">
                                تفاصيل الاستشارة
                            </h3>

                        </div>

                        <div class="space-y-5">

                            <div>

                                <p class="text-xs text-[#6B7A93]">
                                    رقم الاستشارة
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white break-all"
                                >
                                    {{ $consultation
                                        ->consultation_number }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-[#6B7A93]">
                                    عنوان الاستشارة
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white"
                                >
                                    {{ $consultation->title }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-[#6B7A93]">
                                    نوع الاستشارة
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white"
                                >
                                    {{ $consultation
                                        ->consultationType
                                        ?->name
                                        ?? 'غير محدد' }}
                                </p>

                            </div>

                            <div>

                                <p class="mb-2 text-xs text-[#6B7A93]">
                                    الحالة
                                </p>

                                <span
                                    class="inline-flex px-3 py-1.5 text-xs font-bold border rounded-full {{ $statusClasses[$consultation->status] ?? 'border-[#25344C] bg-[#16233A] text-[#9FADC7]' }}"
                                >
                                    {{ $statusLabels[
                                        $consultation->status
                                    ] ?? $consultation->status }}
                                </span>

                            </div>

                            <div>

                                <p class="text-xs text-[#6B7A93]">
                                    حالة الدفع
                                </p>

                                @if (
                                    $consultation->payment_status
                                    === 'paid'
                                )

                                    <span
                                        class="inline-flex px-3 py-1.5 mt-2 text-xs font-bold border rounded-full border-emerald-500/30 bg-emerald-500/10 text-emerald-300"
                                    >
                                        تم الدفع
                                    </span>

                                @elseif (
                                    $consultation->payment_status
                                    === 'pending'
                                )

                                    <span
                                        class="inline-flex px-3 py-1.5 mt-2 text-xs font-bold border rounded-full border-yellow-500/30 bg-yellow-500/10 text-yellow-300"
                                    >
                                        قيد المراجعة
                                    </span>

                                @else

                                    <span
                                        class="inline-flex px-3 py-1.5 mt-2 text-xs font-bold text-red-300 border rounded-full border-red-500/30 bg-red-500/10"
                                    >
                                        غير مدفوع
                                    </span>

                                @endif

                            </div>

                            <div>

                                <p class="text-xs text-[#6B7A93]">
                                    تاريخ الإنشاء
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white"
                                >
                                    {{ $consultation
                                        ->created_at
                                        ?->format('Y-m-d H:i') }}
                                </p>

                            </div>

                        </div>

                    </section>

                    {{-- المشاركون --}}
                    <section
                        class="p-5 border shadow-2xl rounded-3xl border-[#25344C] bg-[#111B2E]/85 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-[#25344C]"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-[#8B7CF6]/15"
                            >
                                👥
                            </div>

                            <h3 class="text-lg font-black text-white">
                                المشاركون
                            </h3>

                        </div>

                        <div class="space-y-3">

                            {{-- العميل --}}
                            <div
                                class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-white/[0.04]"
                            >

                                <div
                                    class="flex items-center min-w-0 gap-3"
                                >

                                    @if (
                                        $consultation
                                            ->customer
                                            ?->profile_photo
                                    )

                                        <img
                                            src="{{ asset(
                                                'storage/' .
                                                $consultation
                                                    ->customer
                                                    ->profile_photo
                                            ) }}"
                                            alt="{{ $consultation
                                                ->customer
                                                ->name }}"
                                            class="flex-none object-cover w-10 h-10 rounded-full"
                                        >

                                    @else

                                        <div
                                            class="flex items-center justify-center flex-none w-10 h-10 font-bold text-white rounded-full bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]"
                                        >
                                            {{ mb_substr(
                                                $consultation
                                                    ->customer
                                                    ?->name
                                                    ?? 'ع',
                                                0,
                                                1
                                            ) }}
                                        </div>

                                    @endif

                                    <div class="min-w-0">

                                        <p
                                            class="text-sm font-bold text-white truncate"
                                        >
                                            {{ $consultation
                                                ->customer
                                                ?->name
                                                ?? 'غير محدد' }}
                                        </p>

                                        <p class="text-xs text-[#6B7A93]">
                                            العميل
                                        </p>

                                    </div>

                                </div>

                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-lg bg-emerald-500/15 text-emerald-300"
                                >
                                    عميل
                                </span>

                            </div>

                            {{-- المهندس مع رابط صفحته --}}
                            @if ($consultation->engineer)

                                <a
                                    href="{{ route(
                                        'engineers.show',
                                        $consultation->engineer
                                    ) }}"
                                    class="flex items-center justify-between gap-3 p-3 transition rounded-2xl bg-white/[0.04] hover:bg-white/[0.09] hover:ring-1 hover:ring-[#8B7CF6]/30"
                                    title="فتح صفحة المهندس"
                                >

                                    <div
                                        class="flex items-center min-w-0 gap-3"
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
                                                class="flex-none object-cover w-10 h-10 rounded-full ring-2 ring-[#8B7CF6]/30"
                                            >

                                        @else

                                            <div
                                                class="flex items-center justify-center flex-none w-10 h-10 font-bold text-white rounded-full bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]"
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

                                        <div class="min-w-0">

                                            <p
                                                class="text-sm font-bold text-white truncate"
                                            >
                                                {{ $consultation
                                                    ->engineer
                                                    ->name }}
                                            </p>

                                            <p
                                                class="text-xs text-[#C9BFFF]"
                                            >
                                                اضغط لعرض الملف الشخصي
                                            </p>

                                        </div>

                                    </div>

                                    <span
                                        class="px-2 py-1 text-[10px] font-bold text-[#C9BFFF] rounded-lg bg-[#8B7CF6]/15"
                                    >
                                        مهندس
                                    </span>

                                </a>

                            @else

                                <div
                                    class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-white/[0.04]"
                                >

                                    <div
                                        class="flex items-center gap-3"
                                    >

                                        <div
                                            class="flex items-center justify-center w-10 h-10 font-bold rounded-full bg-[#16233A] text-[#9FADC7]"
                                        >
                                            م
                                        </div>

                                        <div>

                                            <p
                                                class="text-sm font-bold text-[#9FADC7]"
                                            >
                                                لم يتم تعيين مهندس
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </section>

                    {{-- صفحة المهندس والتقييم --}}
                    @if ($consultation->engineer)

                        <section
                            class="p-5 border shadow-2xl rounded-3xl border-[#25344C] bg-[#111B2E]/85 backdrop-blur-xl"
                        >

                            <div
                                class="flex items-center gap-3 pb-4 mb-4 border-b border-[#25344C]"
                            >

                                <div
                                    class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-yellow-500/15"
                                >
                                    ⭐
                                </div>

                                <h3 class="text-lg font-black text-white">
                                    صفحة المهندس والتقييم
                                </h3>

                            </div>

                            <div class="grid gap-3">

                                <a
                                    href="{{ route(
                                        'engineers.show',
                                        $consultation->engineer
                                    ) }}"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 font-bold transition border text-[#C9BFFF] rounded-xl border-[#8B7CF6]/30 bg-[#8B7CF6]/10 hover:bg-[#8B7CF6]/20"
                                >
                                    👤 فتح صفحة المهندس
                                </a>

                                @if ($canReviewEngineer)

                                    @if ($consultation->review)

                                        <div
                                            class="p-4 text-center border rounded-xl border-yellow-500/20 bg-yellow-500/10"
                                        >

                                            <p
                                                class="font-black text-yellow-300"
                                            >
                                                ⭐ تم تقييم المهندس
                                            </p>

                                            <div
                                                class="flex items-center justify-center gap-1 mt-3"
                                            >

                                                @for (
                                                    $star = 1;
                                                    $star <= 5;
                                                    $star++
                                                )

                                                    <span
                                                        class="text-xl {{ $star <= $consultation->review->rating
                                                            ? 'text-yellow-400'
                                                            : 'text-[#25344C]' }}"
                                                    >
                                                        ★
                                                    </span>

                                                @endfor

                                            </div>

                                            <p
                                                class="mt-2 text-sm font-bold text-yellow-200"
                                            >
                                                {{ $consultation
                                                    ->review
                                                    ->rating }}/5
                                            </p>

                                        </div>

                                    @else

                                        <a
                                            href="{{ route(
                                                'engineer-reviews.create',
                                                $consultation
                                            ) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-3 font-black text-white transition rounded-xl bg-gradient-to-r from-[#FF6B5B] to-[#8B7CF6] hover:brightness-110"
                                        >
                                            ⭐ تقييم المهندس وكتابة تعليق
                                        </a>

                                    @endif

                                @elseif (
                                    (int) $currentUser->id
                                    === (int) $consultation->customer_id
                                )

                                    <div
                                        class="p-3 text-sm text-center border rounded-xl border-[#25344C] bg-white/[0.04] text-[#9FADC7]"
                                    >
                                        يظهر التقييم بعد اكتمال
                                        الاستشارة وتأكيد الدفع.
                                    </div>

                                @endif

                            </div>

                        </section>

                    @endif

                    {{-- الملفات المشتركة --}}
                    <section
                        class="p-5 border shadow-2xl rounded-3xl border-[#25344C] bg-[#111B2E]/85 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-[#25344C]"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-[#8B7CF6]/15"
                            >
                                📁
                            </div>

                            <h3 class="text-lg font-black text-white">
                                الملفات المشتركة
                            </h3>

                        </div>

                        <div class="space-y-3">

                            @forelse (
                                $messages
                                    ->whereNotNull('attachment')
                                    ->take(5)
                                as $fileMessage
                            )

                                @php
                                    $fileExtension = strtolower(
                                        pathinfo(
                                            $fileMessage->attachment,
                                            PATHINFO_EXTENSION
                                        )
                                    );
                                @endphp

                                <a
                                    href="{{ route(
                                        'consultations.messages.attachment',
                                        [
                                            $consultation,
                                            $fileMessage,
                                        ]
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-3 p-3 transition rounded-2xl bg-white/[0.04] hover:bg-white/[0.08]"
                                >

                                    <div
                                        class="flex items-center justify-center flex-none w-10 h-10 text-xs font-black text-[#C9BFFF] rounded-xl bg-[#8B7CF6]/15"
                                    >
                                        {{ strtoupper(
                                            $fileExtension
                                                ?: 'FILE'
                                        ) }}
                                    </div>

                                    <div class="min-w-0">

                                        <p
                                            class="text-sm font-bold text-white truncate"
                                        >
                                            {{ basename(
                                                $fileMessage
                                                    ->attachment
                                            ) }}
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-[#6B7A93]"
                                        >
                                            {{ $fileMessage
                                                ->created_at
                                                ?->format('Y-m-d') }}
                                        </p>

                                    </div>

                                </a>

                            @empty

                                <p
                                    class="py-6 text-sm text-center text-[#6B7A93]"
                                >
                                    لا توجد ملفات مشتركة
                                </p>

                            @endforelse

                        </div>

                    </section>

                </aside>

                {{-- المحادثة --}}
                <main
                    class="overflow-hidden border shadow-[0_28px_90px_rgba(0,0,0,.6)] rounded-[28px] border-[#25344C] bg-[#0A1220]/90 backdrop-blur-2xl xl:order-1"
                >

                    <div
                        class="sticky top-0 z-30 flex items-center justify-between gap-4 px-4 py-3 border-b sm:px-5 border-[#25344C] bg-[#0A1220]/95 backdrop-blur-2xl"
                    >
                        <div class="flex items-center min-w-0 gap-3">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a
                                    href="{{ route('engineers.show', $otherUser) }}"
                                    class="relative flex-none group"
                                    title="فتح الملف الشخصي للمهندس"
                                >
                                    @if ($otherUser->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . $otherUser->profile_photo) }}"
                                            alt="{{ $otherUser->name }}"
                                            class="object-cover transition border-2 rounded-full w-11 h-11 border-[#8B7CF6]/40 ring-2 ring-[#8B7CF6]/10 group-hover:scale-105 group-hover:ring-[#8B7CF6]/40"
                                        >
                                    @else
                                        <div
                                            class="flex items-center justify-center font-black text-white transition border-2 rounded-full w-11 h-11 border-[#8B7CF6]/40 bg-gradient-to-br from-[#8B7CF6] to-[#FF6B5B] group-hover:scale-105"
                                        >
                                            {{ mb_substr($otherUser->name, 0, 1) }}
                                        </div>
                                    @endif

                                    <span
                                        class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-[#3A4A66] border-[#0A1220]"
                                    ></span>
                                </a>
                            @else
                                <div class="relative flex-none">
                                    @if ($otherUser?->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . $otherUser->profile_photo) }}"
                                            alt="{{ $otherUser?->name }}"
                                            class="object-cover border-2 rounded-full w-11 h-11 border-[#8B7CF6]/30"
                                        >
                                    @else
                                        <div
                                            class="flex items-center justify-center font-black text-white rounded-full w-11 h-11 bg-gradient-to-br from-[#8B7CF6] to-[#FF6B5B]"
                                        >
                                            {{ mb_substr($otherUser?->name ?? 'م', 0, 1) }}
                                        </div>
                                    @endif

                                    <span
                                        class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-[#3A4A66] border-[#0A1220]"
                                    ></span>
                                </div>
                            @endif

                            <div class="min-w-0">
                                @if ($otherUser && $otherUser->role === 'engineer')
                                    <a
                                        href="{{ route('engineers.show', $otherUser) }}"
                                        class="block font-black text-white truncate transition hover:text-[#FF6B5B]"
                                    >
                                        {{ $otherUser->name }}
                                    </a>
                                @else
                                    <p class="font-black text-white truncate">
                                        {{ $otherUser?->name ?? 'المستخدم' }}
                                    </p>
                                @endif

                                <p class="mt-0.5 text-xs text-[#6B7A93]">
                                    <span class="presence-status">غير متصل</span>
                                    <span id="headerTypingStatus" class="hidden text-[#8B7CF6]">
                                        · يكتب الآن...
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a
                                    href="{{ route('engineers.show', $otherUser) }}"
                                    class="inline-flex items-center justify-center flex-none w-11 h-11 text-lg transition border rounded-full border-[#25344C] bg-white/5 text-[#9FADC7] hover:bg-[#FF6B5B]/15 hover:text-[#FF6B5B]"
                                    title="الملف الشخصي للمهندس"
                                >
                                    👤
                                </a>
                            @endif

                            <button
                                type="button"
                                id="toggleConsultationDetails"
                                class="inline-flex items-center justify-center flex-none w-11 h-11 text-lg transition border rounded-full border-[#25344C] bg-white/5 text-[#9FADC7] hover:bg-[#8B7CF6]/15 hover:text-[#8B7CF6]"
                                title="تفاصيل الاستشارة"
                            >
                                ⓘ
                            </button>
                        </div>
                    </div>

                    <div
                        id="messagesContainer"
                        class="h-[calc(100dvh-260px)] min-h-[360px] p-4 overflow-y-auto scroll-smooth sm:h-[680px] sm:p-6 bg-[radial-gradient(circle_at_top,_rgba(139,124,246,.10),_transparent_34%)]"
                    >

                        {{-- التاريخ --}}
                        <div
                            class="flex items-center gap-4 mb-8"
                        >

                            <div class="flex-1 h-px bg-[#25344C]"></div>

                            <span
                                class="px-4 py-2 text-xs font-bold border rounded-full text-[#9FADC7] border-[#25344C] bg-[#0A1220]/60"
                            >
                                {{ $consultation
                                    ->created_at
                                    ?->format('Y-m-d') }}
                            </span>

                            <div class="flex-1 h-px bg-[#25344C]"></div>

                        </div>

                        <div id="messagesList" class="space-y-3">

                            @forelse ($messages as $message)

                                @php
                                    $isMine =
                                        (int) $message->sender_id
                                        === (int) auth()->id();

                                    $sender = $message->sender;

                                    $extension = $message->attachment
                                        ? strtolower(
                                            pathinfo(
                                                $message->attachment,
                                                PATHINFO_EXTENSION
                                            )
                                        )
                                        : null;

                                    $isImage = in_array(
                                        $extension,
                                        [
                                            'jpg',
                                            'jpeg',
                                            'png',
                                            'gif',
                                            'webp',
                                        ],
                                        true
                                    );

                                    $senderIsEngineer =
                                        $sender
                                        && $sender->role
                                            === 'engineer';
                                @endphp

                                <div
                                    data-message-id="{{ $message->id }}"
                                    class="flex items-end gap-2 {{ $isMine
                                        ? 'flex-row-reverse justify-start'
                                        : 'justify-start' }}"
                                >

                                    {{-- صورة المرسل --}}
                                    @if ($senderIsEngineer)

                                        <a
                                            href="{{ route(
                                                'engineers.show',
                                                $sender
                                            ) }}"
                                            class="flex-none transition hover:scale-105"
                                            title="فتح صفحة المهندس"
                                        >

                                            @if ($sender->profile_photo)

                                                <img
                                                    src="{{ asset(
                                                        'storage/' .
                                                        $sender
                                                            ->profile_photo
                                                    ) }}"
                                                    alt="{{ $sender->name }}"
                                                    class="object-cover w-8 h-8 border rounded-full border-[#8B7CF6]/30 ring-2 ring-[#8B7CF6]/15"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-[#8B7CF6]/30 bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]"
                                                >
                                                    {{ mb_substr(
                                                        $sender->name,
                                                        0,
                                                        1
                                                    ) }}
                                                </div>

                                            @endif

                                        </a>

                                    @else

                                        <div class="flex-none">

                                            @if ($sender?->profile_photo)

                                                <img
                                                    src="{{ asset(
                                                        'storage/' .
                                                        $sender
                                                            ->profile_photo
                                                    ) }}"
                                                    alt="{{ $sender->name }}"
                                                    class="object-cover w-8 h-8 border rounded-full border-[#25344C]"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-[#25344C] {{ $isMine
                                                        ? 'bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]'
                                                        : 'bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]' }}"
                                                >
                                                    {{ mb_substr(
                                                        $sender?->name
                                                            ?? 'م',
                                                        0,
                                                        1
                                                    ) }}
                                                </div>

                                            @endif

                                        </div>

                                    @endif

                                    <div
                                        class="w-auto max-w-[84%] sm:max-w-[58%]"
                                    >

                                        <div
                                            class="px-4 py-2.5 shadow-lg rounded-[20px]
                                            {{ $isMine
                                                ? 'rounded-br-md bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6] text-white ring-1 ring-white/10'
                                                : 'rounded-bl-md border border-[#25344C] bg-[#16233A]/85 text-[#F4F1FA] backdrop-blur-xl' }}"
                                        >

                                            <div
                                                class="flex items-center justify-between gap-4 mb-1.5"
                                            >

                                                @if (
                                                    ! $isMine
                                                    && $senderIsEngineer
                                                )

                                                    <a
                                                        href="{{ route(
                                                            'engineers.show',
                                                            $sender
                                                        ) }}"
                                                        class="text-sm font-black transition hover:text-[#8B7CF6]"
                                                        title="فتح صفحة المهندس"
                                                    >
                                                        {{ $sender->name }}
                                                    </a>

                                                @else

                                                    <p
                                                        class="text-sm font-black"
                                                    >
                                                        {{ $isMine
                                                            ? 'أنت'
                                                            : (
                                                                $sender?->name
                                                                ?? 'المستخدم'
                                                            ) }}
                                                    </p>

                                                @endif

                                                <span
                                                    class="text-[11px] {{ $isMine
                                                        ? 'text-white/70'
                                                        : 'text-[#6B7A93]' }}"
                                                >
                                                    {{ $message
                                                        ->created_at
                                                        ?->format('H:i') }}
                                                </span>

                                            </div>

                                            {{-- نص الرسالة --}}
                                            @if ($message->message)

                                                <p
                                                    class="text-sm leading-6 whitespace-pre-line sm:text-[15px]"
                                                >
                                                    {{ $message->message }}
                                                </p>

                                            @endif

                                            {{-- المرفق --}}
                                            @if ($message->attachment)

                                                @if ($isImage)

                                                    <a
                                                        href="{{ route(
                                                            'consultations.messages.attachment',
                                                            [
                                                                $consultation,
                                                                $message,
                                                            ]
                                                        ) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-block mt-3 overflow-hidden border rounded-2xl border-[#25344C] bg-black/10"
                                                    >

                                                        <img
                                                            src="{{ route(
                                                                'consultations.messages.attachment',
                                                                [
                                                                    $consultation,
                                                                    $message,
                                                                ]
                                                            ) }}"
                                                            alt="مرفق"
                                                            class="object-cover w-auto max-w-[220px] sm:max-w-[280px] max-h-56"
                                                        >

                                                    </a>

                                                @else

                                                    <a
                                                        href="{{ route(
                                                            'consultations.messages.attachment',
                                                            [
                                                                $consultation,
                                                                $message,
                                                            ]
                                                        ) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-[#25344C] bg-black/15 hover:bg-black/25"
                                                    >

                                                        <div
                                                            class="flex items-center min-w-0 gap-3"
                                                        >

                                                            <div
                                                                class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-[#8B7CF6]/20 text-[#D9D2FF]"
                                                            >
                                                                {{ strtoupper(
                                                                    $extension
                                                                        ?: 'FILE'
                                                                ) }}
                                                            </div>

                                                            <div
                                                                class="min-w-0"
                                                            >

                                                                <p
                                                                    class="text-sm font-bold truncate"
                                                                >
                                                                    {{ basename(
                                                                        $message
                                                                            ->attachment
                                                                    ) }}
                                                                </p>

                                                                <p
                                                                    class="mt-1 text-xs opacity-60"
                                                                >
                                                                    اضغط لفتح الملف
                                                                </p>

                                                            </div>

                                                        </div>

                                                        <span
                                                            class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-[#25344C]"
                                                        >
                                                            ↓
                                                        </span>

                                                    </a>

                                                @endif

                                            @endif

                                            @if ($isMine)

                                                <div
                                                    class="mt-2 text-xs text-left text-white/70"
                                                >
                                                    ✓✓
                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <div
                                    class="flex flex-col items-center justify-center h-[500px] text-center"
                                >

                                    <div
                                        class="flex items-center justify-center w-20 h-20 mb-5 text-4xl rounded-full bg-[#8B7CF6]/10"
                                    >
                                        💬
                                    </div>

                                    <h3
                                        class="text-xl font-black text-white"
                                    >
                                        لا توجد رسائل حتى الآن
                                    </h3>

                                    <p
                                        class="mt-2 text-sm text-[#6B7A93]"
                                    >
                                        ابدأ المحادثة بإرسال أول رسالة
                                    </p>

                                </div>

                            @endforelse

                        </div>

                        <div
                            id="typingIndicator"
                            class="hidden mt-5 text-sm font-bold text-[#8B7CF6]"
                        >
                            يكتب الآن...
                        </div>

                    </div>

                    {{-- إرسال رسالة --}}
                    <div
                        class="sticky bottom-0 z-20 p-3 border-t sm:p-4 border-[#25344C] bg-[#0A1220]/95 backdrop-blur-2xl"
                    >

                        <form
                            id="chatForm"
                            method="POST"
                            action="{{ route(
                                'consultations.messages.store',
                                $consultation
                            ) }}"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            x-data="{
                                fileName: '',

                                selectFile(event) {
                                    this.fileName =
                                        event.target.files[0]
                                            ? event.target
                                                .files[0]
                                                .name
                                            : '';
                                }
                            }"
                        >
                            @csrf

                            <div
                                class="relative flex items-end gap-2 p-2 border shadow-xl rounded-[24px] border-[#25344C] bg-[#111B2E]/95"
                            >

                                <textarea
                                    id="message"
                                    name="message"
                                    rows="1"
                                    placeholder="اكتب رسالتك هنا..."
                                    class="flex-1 w-full px-3 py-3 text-sm text-white bg-transparent border-0 resize-none sm:text-base placeholder:text-[#6B7A93] focus:ring-0 min-h-[48px] max-h-32"
                                >{{ old('message') }}</textarea>

                                <div
                                    class="flex items-center flex-none gap-2"
                                >

                                    <div class="flex items-center gap-3">

                                        <label
                                            for="attachment"
                                            class="flex items-center justify-center text-xl transition cursor-pointer w-11 h-11 rounded-xl text-[#9FADC7] bg-white/5 hover:bg-white/10 hover:text-white"
                                            title="إرفاق ملف"
                                        >
                                            📎
                                        </label>

                                        <input
                                            id="attachment"
                                            type="file"
                                            name="attachment"
                                            class="hidden"
                                            accept=".pdf,.dwg,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx,.zip"
                                            @change="selectFile($event)"
                                        >

                                        <span
                                            x-show="fileName"
                                            x-text="fileName"
                                            class="max-w-[110px] text-xs truncate text-[#6B7A93] sm:max-w-[230px]"
                                        ></span>

                                    </div>

                                    <button
                                        id="sendButton"
                                        type="submit"
                                        aria-label="إرسال"
                                        class="inline-flex items-center justify-center flex-none text-lg text-white transition rounded-full shadow-lg w-11 h-11 bg-gradient-to-r from-[#FF6B5B] to-[#8B7CF6] hover:scale-105"
                                    >
                                        <span aria-hidden="true">➤</span>
                                    </button>

                                </div>

                            </div>

                            <div
                                class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-[#6B7A93]"
                            >

                                <span>
                                    جميع المحادثات محفوظة وسرية 🔒
                                </span>

                                <span>
                                    الحد الأقصى للمرفق 20 ميجابايت
                                </span>

                            </div>

                        </form>

                    </div>

                </main>

            </div>

        </div>

    </div>


    <div
        id="consultationDetailsBackdrop"
        class="fixed inset-0 z-40 hidden bg-[#0A1220]/70 backdrop-blur-sm xl:hidden"
    ></div>

    <div
        id="consultationDetailsDrawer"
        class="fixed inset-y-0 right-0 z-50 hidden w-[92%] max-w-sm p-4 overflow-y-auto border-l shadow-2xl border-[#25344C] bg-[#0A1220] xl:hidden"
        dir="rtl"
    >
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-black text-white">تفاصيل الاستشارة</h3>
                <p class="mt-1 text-xs text-[#6B7A93]">
                    {{ $consultation->consultation_number }}
                </p>
            </div>

            <button
                type="button"
                id="closeConsultationDetails"
                class="inline-flex items-center justify-center w-10 h-10 text-xl border rounded-full border-[#25344C] bg-white/5 text-[#9FADC7]"
            >
                ×
            </button>
        </div>

        <div class="space-y-4">
            <div class="p-4 border rounded-2xl border-[#25344C] bg-white/[0.04]">
                <p class="text-xs text-[#6B7A93]">عنوان الاستشارة</p>
                <p class="mt-2 font-bold text-white">{{ $consultation->title }}</p>
            </div>

            <div class="p-4 border rounded-2xl border-[#25344C] bg-white/[0.04]">
                <p class="text-xs text-[#6B7A93]">نوع الاستشارة</p>
                <p class="mt-2 font-bold text-white">
                    {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                </p>
            </div>

            <div class="p-4 border rounded-2xl border-[#25344C] bg-white/[0.04]">
                <p class="text-xs text-[#6B7A93]">الحالة</p>
                <p class="mt-2 font-bold text-white">
                    {{ $statusLabels[$consultation->status] ?? $consultation->status }}
                </p>
            </div>

            @if ($consultation->engineer)
                <a
                    href="{{ route('engineers.show', $consultation->engineer) }}"
                    class="flex items-center gap-3 p-4 transition border rounded-2xl border-[#8B7CF6]/25 bg-[#8B7CF6]/10 hover:bg-[#8B7CF6]/15"
                >
                    @if ($consultation->engineer->profile_photo)
                        <img
                            src="{{ asset('storage/' . $consultation->engineer->profile_photo) }}"
                            alt="{{ $consultation->engineer->name }}"
                            class="object-cover w-12 h-12 rounded-full ring-2 ring-[#8B7CF6]/30"
                        >
                    @else
                        <div
                            class="flex items-center justify-center w-12 h-12 font-black text-white rounded-full bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]"
                        >
                            {{ mb_substr($consultation->engineer->name, 0, 1) }}
                        </div>
                    @endif

                    <div>
                        <p class="font-black text-white">
                            {{ $consultation->engineer->name }}
                        </p>
                        <p class="mt-1 text-xs text-[#C9BFFF]">
                            فتح الملف الشخصي للمهندس
                        </p>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const consultationId = @json($consultation->id);
            const currentUserId = @json(auth()->id());
            const form = document.getElementById('chatForm');
            const textarea = document.getElementById('message');
            const attachment = document.getElementById('attachment');
            const sendButton = document.getElementById('sendButton');
            const messagesContainer =
                document.getElementById('messagesContainer');
            const messagesList =
                document.getElementById('messagesList');
            const typingIndicator =
                document.getElementById('typingIndicator');
            const presenceDots =
                document.querySelectorAll('.presence-dot');
            const presenceStatuses =
                document.querySelectorAll('.presence-status');
            const headerTypingStatus =
                document.getElementById('headerTypingStatus');
            const toggleConsultationDetails =
                document.getElementById('toggleConsultationDetails');
            const consultationDetailsDrawer =
                document.getElementById('consultationDetailsDrawer');
            const consultationDetailsBackdrop =
                document.getElementById('consultationDetailsBackdrop');
            const closeConsultationDetails =
                document.getElementById('closeConsultationDetails');

            let channel = null;
            let typingTimer = null;

            const openDetails = () => {
                consultationDetailsDrawer?.classList.remove('hidden');
                consultationDetailsBackdrop?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeDetails = () => {
                consultationDetailsDrawer?.classList.add('hidden');
                consultationDetailsBackdrop?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            toggleConsultationDetails?.addEventListener('click', openDetails);
            closeConsultationDetails?.addEventListener('click', closeDetails);
            consultationDetailsBackdrop?.addEventListener('click', closeDetails);

            const scrollToBottom = () => {
                if (messagesContainer) {
                    messagesContainer.scrollTop =
                        messagesContainer.scrollHeight;
                }
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const setPresence = (online) => {
                presenceDots.forEach((dot) => {
                    dot.classList.toggle('bg-green-400', online);
                    dot.classList.toggle('bg-[#3A4A66]', !online);
                });

                presenceStatuses.forEach((status) => {
                    status.textContent = online
                        ? 'نشط الآن'
                        : 'غير متصل';

                    status.classList.toggle(
                        'text-green-400',
                        online
                    );
                });
            };

            const messageExists = (id) => {
                return document.querySelector(
                    `[data-message-id="${id}"]`
                ) !== null;
            };

            const appendMessage = (message, mine) => {
                if (!messagesList || messageExists(message.id)) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.dataset.messageId = message.id;
                wrapper.className =
                    `flex items-end gap-3 ${
                        mine
                            ? 'flex-row-reverse justify-start'
                            : 'justify-start'
                    }`;

                const initial = escapeHtml(
                    (message.sender_name || 'م').charAt(0)
                );

                let avatar = `
                    <div class="flex-none">
                        ${
                            message.sender_profile_photo_url
                                ? `<img
                                    src="${escapeHtml(
                                        message.sender_profile_photo_url
                                    )}"
                                    alt="${escapeHtml(
                                        message.sender_name
                                    )}"
                                    class="object-cover w-8 h-8 border rounded-full border-[#25344C]"
                                >`
                                : `<div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-[#25344C] ${
                                        mine
                                            ? 'bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]'
                                            : 'bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]'
                                    }"
                                >${initial}</div>`
                        }
                    </div>
                `;

                let attachmentHtml = '';

                if (message.attachment_url) {
                    if (message.attachment_is_image) {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block mt-3 overflow-hidden border rounded-2xl border-[#25344C] bg-black/10"
                            >
                                <img
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                    alt="مرفق"
                                    class="object-cover w-auto max-w-[220px] sm:max-w-[280px] max-h-56"
                                >
                            </a>
                        `;
                    } else {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-[#25344C] bg-black/15 hover:bg-black/25"
                            >
                                <div class="flex items-center min-w-0 gap-3">
                                    <div
                                        class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-[#8B7CF6]/20 text-[#D9D2FF]"
                                    >
                                        ${escapeHtml(
                                            (
                                                message.attachment_extension
                                                || 'FILE'
                                            ).toUpperCase()
                                        )}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate">
                                            ${escapeHtml(
                                                message.attachment_name
                                            )}
                                        </p>
                                        <p class="mt-1 text-xs opacity-60">
                                            اضغط لفتح الملف
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-[#25344C]"
                                >↓</span>
                            </a>
                        `;
                    }
                }

                wrapper.innerHTML = `
                    ${avatar}

                    <div class="w-auto max-w-[84%] sm:max-w-[58%]">
                        <div
                            class="px-4 py-2.5 shadow-lg rounded-[20px] ${
                                mine
                                    ? 'rounded-br-md bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6] text-white ring-1 ring-white/10'
                                    : 'rounded-bl-md border border-[#25344C] bg-[#16233A]/85 text-[#F4F1FA] backdrop-blur-xl'
                            }"
                        >
                            <div
                                class="flex items-center justify-between gap-4 mb-1.5"
                            >
                                <p class="text-sm font-black">
                                    ${
                                        mine
                                            ? 'أنت'
                                            : escapeHtml(
                                                message.sender_name
                                            )
                                    }
                                </p>

                                <span
                                    class="text-[11px] ${
                                        mine
                                            ? 'text-white/70'
                                            : 'text-[#6B7A93]'
                                    }"
                                >
                                    ${escapeHtml(message.time)}
                                </span>
                            </div>

                            ${
                                message.body
                                    ? `<p
                                        class="text-sm leading-6 whitespace-pre-line sm:text-[15px]"
                                    >${escapeHtml(message.body)}</p>`
                                    : ''
                            }

                            ${attachmentHtml}

                            ${
                                mine
                                    ? `<div
                                        class="mt-2 text-xs text-left text-white/70"
                                    >✓</div>`
                                    : ''
                            }
                        </div>
                    </div>
                `;

                messagesList.appendChild(wrapper);
                scrollToBottom();
            };

            scrollToBottom();

            if (window.Echo) {
                channel = window.Echo.join(
                    `consultation.${consultationId}`
                )
                    .here((users) => {
                        setPresence(
                            users.some(
                                (user) =>
                                    Number(user.id)
                                    !== Number(currentUserId)
                            )
                        );
                    })
                    .joining((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(true);
                        }
                    })
                    .leaving((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(false);
                        }
                    })
                    .listen(
                        '.consultation.message.sent',
                        (event) => {
                            appendMessage(
                                event.message,
                                Number(event.message.sender_id)
                                    === Number(currentUserId)
                            );
                        }
                    )
                    .listenForWhisper(
                        'typing',
                        (event) => {
                            if (
                                Number(event.user_id)
                                === Number(currentUserId)
                            ) {
                                return;
                            }

                            typingIndicator?.classList.remove(
                                'hidden'
                            );
                            headerTypingStatus?.classList.remove(
                                'hidden'
                            );

                            clearTimeout(typingTimer);

                            typingTimer = setTimeout(() => {
                                typingIndicator?.classList.add(
                                    'hidden'
                                );
                                headerTypingStatus?.classList.add(
                                    'hidden'
                                );
                            }, 1500);
                        }
                    );
            }

            textarea?.addEventListener('input', () => {
                channel?.whisper('typing', {
                    user_id: currentUserId,
                });
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();

                const hasMessage =
                    textarea?.value.trim().length > 0;
                const hasAttachment =
                    attachment?.files?.length > 0;

                if (!hasMessage && !hasAttachment) {
                    return;
                }

                sendButton.disabled = true;
                sendButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );

                try {
                    const formData = new FormData(form);

                    const response = await window.axios.post(
                        form.action,
                        formData,
                        {
                            headers: {
                                Accept: 'application/json',
                                'Content-Type':
                                    'multipart/form-data',
                            },
                        }
                    );

                    appendMessage(
                        response.data.message,
                        true
                    );

                    form.reset();

                    if (window.Alpine) {
                        form.dispatchEvent(
                            new Event(
                                'reset',
                                { bubbles: true }
                            )
                        );
                    }
                } catch (error) {
                    const errors =
                        error.response?.data?.errors;

                    const message = errors
                        ? Object.values(errors)
                            .flat()
                            .join('\n')
                        : 'تعذر إرسال الرسالة. حاول مرة أخرى.';

                    alert(message);
                } finally {
                    sendButton.disabled = false;
                    sendButton.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );
                }
            });
        });
    </script>

</x-app-layout>
