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
                'border-orange-500/30 bg-orange-500/10 text-orange-300',

            'pending' =>
                'border-amber-500/30 bg-amber-500/10 text-amber-300',

            'in_progress' =>
                'border-blue-500/30 bg-blue-500/10 text-blue-300',

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
                    class="flex items-center gap-4 p-2 transition rounded-2xl hover:bg-white/5"
                    title="فتح صفحة المهندس"
                >

                    <div class="relative">

                        @if ($otherUser->profile_photo)

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $otherUser->profile_photo
                                ) }}"
                                alt="{{ $otherUser->name }}"
                                class="object-cover w-12 h-12 border-2 rounded-full border-blue-500/40"
                            >

                        @else

                            <div
                                class="flex items-center justify-center w-12 h-12 font-black text-white rounded-full bg-gradient-to-br from-blue-600 to-violet-600"
                            >
                                {{ mb_substr(
                                    $otherUser->name,
                                    0,
                                    1
                                ) }}
                            </div>

                        @endif

                        <span
                            class="absolute bottom-0 left-0 w-3 h-3 bg-green-400 border-2 rounded-full border-slate-950"
                        ></span>

                    </div>

                    <div>

                        <h2 class="text-xl font-black text-white">
                            المحادثة
                        </h2>

                        <p
                            class="mt-1 text-sm font-bold text-cyan-300"
                        >
                            {{ $otherUser->name }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            اضغط لعرض صفحة المهندس
                        </p>

                    </div>

                </a>

            @else

                <div class="flex items-center gap-4">

                    <div class="relative">

                        @if ($otherUser?->profile_photo)

                            <img
                                src="{{ asset(
                                    'storage/' .
                                    $otherUser->profile_photo
                                ) }}"
                                alt="{{ $otherUser->name }}"
                                class="object-cover w-12 h-12 border-2 rounded-full border-blue-500/40"
                            >

                        @else

                            <div
                                class="flex items-center justify-center w-12 h-12 font-black text-white rounded-full bg-gradient-to-br from-blue-600 to-violet-600"
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
                            class="absolute bottom-0 left-0 w-3 h-3 bg-green-400 border-2 rounded-full border-slate-950"
                        ></span>

                    </div>

                    <div>

                        <h2 class="text-xl font-black text-white">
                            المحادثة
                        </h2>

                        <p class="mt-1 text-sm text-slate-400">
                            {{ $otherUser?->name
                                ?? 'المستخدم' }}
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
        class="min-h-screen py-8 bg-gradient-to-br from-slate-950 via-[#07152d] to-slate-950"
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
                class="grid items-start grid-cols-1 gap-6 lg:grid-cols-[310px_minmax(0,1fr)]"
            >

                {{-- الشريط الجانبي --}}
                <aside class="space-y-5">

                    {{-- تفاصيل الاستشارة --}}
                    <section
                        class="p-5 border shadow-2xl rounded-3xl border-white/10 bg-slate-900/75 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-5 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-blue-500/15"
                            >
                                📄
                            </div>

                            <h3 class="text-lg font-black text-white">
                                تفاصيل الاستشارة
                            </h3>

                        </div>

                        <div class="space-y-5">

                            <div>

                                <p class="text-xs text-slate-500">
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

                                <p class="text-xs text-slate-500">
                                    عنوان الاستشارة
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white"
                                >
                                    {{ $consultation->title }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-slate-500">
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

                                <p class="mb-2 text-xs text-slate-500">
                                    الحالة
                                </p>

                                <span
                                    class="inline-flex px-3 py-1.5 text-xs font-bold border rounded-full {{ $statusClasses[$consultation->status] ?? 'border-slate-600 bg-slate-700 text-slate-200' }}"
                                >
                                    {{ $statusLabels[
                                        $consultation->status
                                    ] ?? $consultation->status }}
                                </span>

                            </div>

                            <div>

                                <p class="text-xs text-slate-500">
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

                                <p class="text-xs text-slate-500">
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
                        class="p-5 border shadow-2xl rounded-3xl border-white/10 bg-slate-900/75 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-violet-500/15"
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
                                            class="flex items-center justify-center flex-none w-10 h-10 font-bold text-white rounded-full bg-gradient-to-br from-violet-600 to-blue-600"
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

                                        <p class="text-xs text-slate-500">
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
                                    class="flex items-center justify-between gap-3 p-3 transition rounded-2xl bg-white/[0.04] hover:bg-white/[0.09] hover:ring-1 hover:ring-cyan-500/30"
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
                                                class="flex-none object-cover w-10 h-10 rounded-full ring-2 ring-cyan-500/30"
                                            >

                                        @else

                                            <div
                                                class="flex items-center justify-center flex-none w-10 h-10 font-bold text-white rounded-full bg-gradient-to-br from-cyan-600 to-blue-600"
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
                                                class="text-xs text-cyan-300"
                                            >
                                                اضغط لعرض الملف الشخصي
                                            </p>

                                        </div>

                                    </div>

                                    <span
                                        class="px-2 py-1 text-[10px] font-bold text-blue-300 rounded-lg bg-blue-500/15"
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
                                            class="flex items-center justify-center w-10 h-10 font-bold rounded-full bg-slate-700 text-slate-400"
                                        >
                                            م
                                        </div>

                                        <div>

                                            <p
                                                class="text-sm font-bold text-slate-400"
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
                            class="p-5 border shadow-2xl rounded-3xl border-white/10 bg-slate-900/75 backdrop-blur-xl"
                        >

                            <div
                                class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
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
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 font-bold transition border text-cyan-200 rounded-xl border-cyan-500/20 bg-cyan-500/10 hover:bg-cyan-500/20"
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
                                                            : 'text-slate-700' }}"
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
                                            class="inline-flex items-center justify-center gap-2 px-4 py-3 font-black text-white transition bg-yellow-600 rounded-xl hover:bg-yellow-500"
                                        >
                                            ⭐ تقييم المهندس وكتابة تعليق
                                        </a>

                                    @endif

                                @elseif (
                                    (int) $currentUser->id
                                    === (int) $consultation->customer_id
                                )

                                    <div
                                        class="p-3 text-sm text-center border rounded-xl border-white/10 bg-white/[0.04] text-slate-400"
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
                        class="p-5 border shadow-2xl rounded-3xl border-white/10 bg-slate-900/75 backdrop-blur-xl"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-cyan-500/15"
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
                                    href="{{ asset(
                                        'storage/' .
                                        $fileMessage->attachment
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-3 p-3 transition rounded-2xl bg-white/[0.04] hover:bg-white/[0.08]"
                                >

                                    <div
                                        class="flex items-center justify-center flex-none w-10 h-10 text-xs font-black text-blue-300 rounded-xl bg-blue-500/15"
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
                                            class="mt-1 text-xs text-slate-500"
                                        >
                                            {{ $fileMessage
                                                ->created_at
                                                ?->format('Y-m-d') }}
                                        </p>

                                    </div>

                                </a>

                            @empty

                                <p
                                    class="py-6 text-sm text-center text-slate-500"
                                >
                                    لا توجد ملفات مشتركة
                                </p>

                            @endforelse

                        </div>

                    </section>

                </aside>

                {{-- المحادثة --}}
                <main
                    class="overflow-hidden border shadow-2xl rounded-3xl border-white/10 bg-slate-900/70 backdrop-blur-xl"
                >

                    <div
                        id="messagesContainer"
                        class="h-[700px] p-4 overflow-y-auto sm:p-7"
                    >

                        {{-- التاريخ --}}
                        <div
                            class="flex items-center gap-4 mb-8"
                        >

                            <div class="flex-1 h-px bg-white/10"></div>

                            <span
                                class="px-4 py-2 text-xs font-bold border rounded-full text-slate-400 border-white/10 bg-slate-950/60"
                            >
                                {{ $consultation
                                    ->created_at
                                    ?->format('Y-m-d') }}
                            </span>

                            <div class="flex-1 h-px bg-white/10"></div>

                        </div>

                        <div class="space-y-7">

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
                                    class="flex items-end gap-3 {{ $isMine
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
                                                    class="object-cover border rounded-full w-11 h-11 border-cyan-500/30 ring-2 ring-cyan-500/20"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center font-black text-white border rounded-full w-11 h-11 border-cyan-500/30 bg-gradient-to-br from-cyan-600 to-emerald-600"
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
                                                    class="object-cover border rounded-full w-11 h-11 border-white/10"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center w-11 h-11 font-black text-white border rounded-full border-white/10 {{ $isMine
                                                        ? 'bg-gradient-to-br from-blue-600 to-violet-600'
                                                        : 'bg-gradient-to-br from-cyan-600 to-emerald-600' }}"
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
                                        class="w-full max-w-[80%] sm:max-w-[65%]"
                                    >

                                        <div
                                            class="p-4 shadow-xl sm:p-5 rounded-3xl
                                            {{ $isMine
                                                ? 'rounded-br-md bg-gradient-to-br from-blue-600 to-indigo-700 text-white'
                                                : 'rounded-bl-md border border-white/5 bg-slate-800/95 text-slate-100' }}"
                                        >

                                            <div
                                                class="flex items-center justify-between gap-4 mb-3"
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
                                                        class="text-sm font-black transition hover:text-cyan-300"
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
                                                        ? 'text-blue-100/70'
                                                        : 'text-slate-500' }}"
                                                >
                                                    {{ $message
                                                        ->created_at
                                                        ?->format('H:i') }}
                                                </span>

                                            </div>

                                            {{-- نص الرسالة --}}
                                            @if ($message->message)

                                                <p
                                                    class="text-sm leading-7 whitespace-pre-line sm:text-base"
                                                >
                                                    {{ $message->message }}
                                                </p>

                                            @endif

                                            {{-- المرفق --}}
                                            @if ($message->attachment)

                                                @if ($isImage)

                                                    <a
                                                        href="{{ asset(
                                                            'storage/' .
                                                            $message
                                                                ->attachment
                                                        ) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="block mt-4 overflow-hidden border rounded-2xl border-white/10"
                                                    >

                                                        <img
                                                            src="{{ asset(
                                                                'storage/' .
                                                                $message
                                                                    ->attachment
                                                            ) }}"
                                                            alt="مرفق"
                                                            class="object-cover w-full max-h-80"
                                                        >

                                                    </a>

                                                @else

                                                    <a
                                                        href="{{ asset(
                                                            'storage/' .
                                                            $message
                                                                ->attachment
                                                        ) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-white/10 bg-black/15 hover:bg-black/25"
                                                    >

                                                        <div
                                                            class="flex items-center min-w-0 gap-3"
                                                        >

                                                            <div
                                                                class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-200"
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
                                                            class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-white/10"
                                                        >
                                                            ↓
                                                        </span>

                                                    </a>

                                                @endif

                                            @endif

                                            @if ($isMine)

                                                <div
                                                    class="mt-2 text-xs text-left text-cyan-200"
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
                                        class="flex items-center justify-center w-20 h-20 mb-5 text-4xl rounded-full bg-blue-500/10"
                                    >
                                        💬
                                    </div>

                                    <h3
                                        class="text-xl font-black text-white"
                                    >
                                        لا توجد رسائل حتى الآن
                                    </h3>

                                    <p
                                        class="mt-2 text-sm text-slate-500"
                                    >
                                        ابدأ المحادثة بإرسال أول رسالة
                                    </p>

                                </div>

                            @endforelse

                        </div>

                    </div>

                    {{-- إرسال رسالة --}}
                    <div
                        class="p-4 border-t sm:p-6 border-white/10 bg-slate-950/40"
                    >

                        <form
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
                                class="relative p-3 border rounded-3xl border-white/10 bg-slate-900/80"
                            >

                                <textarea
                                    id="message"
                                    name="message"
                                    rows="3"
                                    placeholder="اكتب رسالتك هنا..."
                                    class="w-full px-4 py-3 text-sm text-white bg-transparent border-0 resize-none sm:text-base placeholder:text-slate-600 focus:ring-0"
                                >{{ old('message') }}</textarea>

                                <div
                                    class="flex flex-col gap-3 pt-3 mt-2 border-t sm:flex-row sm:items-center sm:justify-between border-white/10"
                                >

                                    <div class="flex items-center gap-3">

                                        <label
                                            for="attachment"
                                            class="flex items-center justify-center text-xl transition cursor-pointer w-11 h-11 rounded-xl text-slate-300 bg-white/5 hover:bg-white/10 hover:text-white"
                                            title="إرفاق ملف"
                                        >
                                            📎
                                        </label>

                                        <input
                                            id="attachment"
                                            type="file"
                                            name="attachment"
                                            class="hidden"
                                            accept=".pdf,.dwg,.jpg,.jpeg,.png,.webp,.zip"
                                            @change="selectFile($event)"
                                        >

                                        <span
                                            x-show="fileName"
                                            x-text="fileName"
                                            class="max-w-[230px] text-xs truncate text-slate-400"
                                        ></span>

                                    </div>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-black text-white transition shadow-lg rounded-xl bg-gradient-to-r from-blue-600 to-violet-600 hover:-translate-y-0.5"
                                    >
                                        إرسال
                                        <span>➤</span>
                                    </button>

                                </div>

                            </div>

                            <div
                                class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-slate-600"
                            >

                                <span>
                                    جميع المحادثات محفوظة وسرية 🔒
                                </span>

                                <span>
                                    الحد الأقصى للمرفق 10 ميجابايت
                                </span>

                            </div>

                        </form>

                    </div>

                </main>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                const messagesContainer =
                    document.getElementById(
                        'messagesContainer'
                    );

                if (messagesContainer) {
                    messagesContainer.scrollTop =
                        messagesContainer.scrollHeight;
                }
            }
        );
    </script>

</x-app-layout>
