<x-app-layout>

<style>
:root{
    --bg:#100a18;
    --panel:#1c132a;
    --panel2:#241735;
    --border:#4c335e;
    --muted:#8d799f;
    --text:#fff8ff;
    --accent1:#ff716f;
    --accent2:#8b5cf6;
}
.ref-shell{min-height:100vh;background:#100a18;color:var(--text)}
.ref-topbar{border:1px solid var(--border);background:#1c132a;border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.25)}
.ref-grid{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:18px;align-items:start}
.ref-card{background:#1c132a;border:1px solid var(--border);border-radius:20px;box-shadow:0 14px 45px rgba(0,0,0,.2)}
.ref-card-title{display:flex;align-items:center;gap:9px;font-weight:900;color:#fff}
.ref-card-title:before{content:"";width:6px;height:18px;border-radius:999px;background:#ff716f}
.ref-chat{overflow:hidden;background:#1c132a;border:1px solid var(--border);border-radius:20px;box-shadow:0 18px 55px rgba(0,0,0,.25)}
.ref-chat-header{padding:20px 24px;border-bottom:1px solid var(--border);background:#21152f}
.ref-messages{height:470px;overflow-y:auto;padding:26px;background:#1c132a;scrollbar-width:thin;scrollbar-color:#6f5b7f transparent}
.ref-date{width:max-content;margin:0 auto 22px;padding:8px 18px;border-radius:999px;background:#281b39;color:var(--muted);font-size:12px}
.ref-message{display:flex;margin-bottom:15px}
.ref-message.mine{justify-content:flex-start}
.ref-message.theirs{justify-content:flex-end}
.ref-bubble{max-width:72%;padding:13px 16px;border-radius:16px;font-size:14px;line-height:1.8;box-shadow:0 10px 24px rgba(0,0,0,.15)}
.ref-bubble.mine{background:linear-gradient(135deg,var(--accent1),var(--accent2));color:#fff;border-bottom-left-radius:5px}
.ref-bubble.theirs{background:#120d1b;color:#fff;border:1px solid #2c203b;border-bottom-right-radius:5px}
.ref-meta{margin-top:6px;font-size:10px;color:#826e93}
.ref-compose{padding:16px 22px;border-top:1px solid var(--border);background:#1c132a}
.ref-compose-box{display:flex;align-items:center;gap:10px;background:#241735;border:1px solid #39264b;border-radius:999px;padding:7px}
.ref-input{flex:1;background:transparent;border:0;color:#fff;resize:none;min-height:44px;max-height:110px;padding:10px 14px}
.ref-attach{width:44px;height:44px;border-radius:14px;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;color:#cbbcd7}
.ref-send{width:46px;height:46px;border-radius:999px;background:linear-gradient(135deg,var(--accent1),var(--accent2));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;box-shadow:0 10px 26px rgba(139,92,246,.3)}
.ref-mini-row{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.06)}
.ref-mini-row:last-child{border-bottom:0}
@media(max-width:1100px){.ref-grid{grid-template-columns:1fr}.ref-sidebar{display:none}.ref-messages{height:62vh}}
@media(max-width:640px){.ref-topbar,.ref-chat{border-radius:0;border-left:0;border-right:0}.ref-bubble{max-width:84%}}
</style>


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
                            class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-slate-500 border-slate-950"
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
                            <span class="presence-status">غير متصل</span>
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
                            class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-slate-500 border-slate-950"
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

                        <p class="mt-1 text-xs text-slate-500">
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
        class="py-6 ref-shell sm:py-8"
        dir="rtl"
    >

        <div
            class="px-4 mx-auto max-w-[1320px] sm:px-6 lg:px-8"
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

            <div class="flex items-center justify-between px-5 py-4 mb-5 ref-topbar">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg w-7 h-7 bg-gradient-to-br from-pink-400 to-violet-500"></div>
                    <h1 class="text-xl font-black text-white">منصة الرسائل</h1>
                </div>
                <div class="px-4 py-2 text-sm font-bold rounded-full bg-violet-500/10 text-violet-200">🌐 العربية</div>
            </div>

            <div class="ref-grid">

                {{-- الشريط الجانبي --}}
                <aside id="consultationDetailsPanel" class="space-y-4 ref-sidebar">

                    {{-- تفاصيل الاستشارة --}}
                    <section
                        class="p-5 ref-card"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-5 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-blue-500/15"
                            >
                                📄
                            </div>

                            <h3 class="text-base ref-card-title">
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
                        class="p-5 ref-card"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-violet-500/15"
                            >
                                👥
                            </div>

                            <h3 class="text-base ref-card-title">
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
                            class="p-5 ref-card"
                        >

                            <div
                                class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
                            >

                                <div
                                    class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-yellow-500/15"
                                >
                                    ⭐
                                </div>

                                <h3 class="text-base ref-card-title">
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
                        class="p-5 ref-card"
                    >

                        <div
                            class="flex items-center gap-3 pb-4 mb-4 border-b border-white/10"
                        >

                            <div
                                class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-cyan-500/15"
                            >
                                📁
                            </div>

                            <h3 class="text-base ref-card-title">
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
                    class="ref-chat"
                >

                    <div
                        class="flex items-center justify-between gap-4 ref-chat-header"
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
                                            class="object-cover transition border-2 rounded-full w-11 h-11 border-cyan-400/40 ring-2 ring-cyan-500/10 group-hover:scale-105 group-hover:ring-cyan-400/40"
                                        >
                                    @else
                                        <div
                                            class="flex items-center justify-center font-black text-white transition border-2 rounded-full w-11 h-11 border-cyan-400/40 bg-gradient-to-br from-cyan-500 to-violet-600 group-hover:scale-105"
                                        >
                                            {{ mb_substr($otherUser->name, 0, 1) }}
                                        </div>
                                    @endif

                                    <span
                                        class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-slate-500 border-slate-950"
                                    ></span>
                                </a>
                            @else
                                <div class="relative flex-none">
                                    @if ($otherUser?->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . $otherUser->profile_photo) }}"
                                            alt="{{ $otherUser?->name }}"
                                            class="object-cover border-2 rounded-full w-11 h-11 border-violet-400/30"
                                        >
                                    @else
                                        <div
                                            class="flex items-center justify-center font-black text-white rounded-full w-11 h-11 bg-gradient-to-br from-violet-500 to-blue-600"
                                        >
                                            {{ mb_substr($otherUser?->name ?? 'م', 0, 1) }}
                                        </div>
                                    @endif

                                    <span
                                        class="absolute bottom-0 left-0 w-3 h-3 border-2 rounded-full presence-dot bg-slate-500 border-slate-950"
                                    ></span>
                                </div>
                            @endif

                            <div class="min-w-0">
                                @if ($otherUser && $otherUser->role === 'engineer')
                                    <a
                                        href="{{ route('engineers.show', $otherUser) }}"
                                        class="block font-black text-white truncate transition hover:text-cyan-300"
                                    >
                                        {{ $otherUser->name }}
                                    </a>
                                @else
                                    <p class="font-black text-white truncate">
                                        {{ $otherUser?->name ?? 'المستخدم' }}
                                    </p>
                                @endif

                                <p class="mt-0.5 text-xs text-slate-500">
                                    <span class="presence-status">غير متصل</span>
                                    <span id="headerTypingStatus" class="hidden text-cyan-300">
                                        · يكتب الآن...
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a
                                    href="{{ route('engineers.show', $otherUser) }}"
                                    class="inline-flex items-center justify-center w-10 h-10 text-lg transition border rounded-full border-white/10 bg-white/5 text-slate-300 hover:bg-cyan-500/15 hover:text-cyan-300"
                                    title="الملف الشخصي للمهندس"
                                >
                                    👤
                                </a>
                            @endif

                            <button
                                type="button"
                                id="toggleConsultationDetails"
                                class="inline-flex items-center justify-center w-10 h-10 text-lg transition border rounded-full border-white/10 bg-white/5 text-slate-300 hover:bg-violet-500/15 hover:text-violet-300"
                                title="تفاصيل الاستشارة"
                            >
                                ⓘ
                            </button>
                        </div>
                    </div>

                    <div
                        id="messagesContainer"
                        class="ref-messages"
                    >

                        {{-- التاريخ --}}
                        <div
                            class="mb-5"
                        >



                            <span
                                class="ref-date"
                            >
                                {{ $consultation
                                    ->created_at
                                    ?->format('Y-m-d') }}
                            </span>



                        </div>

                        <div id="messagesList">

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
                                    class="ref-message {{ $isMine ? 'mine' : 'theirs' }}"
                                >

                                    {{-- صورة المرسل --}}
                                    @if ($senderIsEngineer)

                                        <a
                                            href="{{ route(
                                                'engineers.show',
                                                $sender
                                            ) }}"
                                            class="hidden"
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
                                                    class="object-cover w-8 h-8 border rounded-full border-cyan-500/30 ring-2 ring-cyan-500/15"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-cyan-500/30 bg-gradient-to-br from-cyan-600 to-emerald-600"
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

                                        <div class="hidden">

                                            @if ($sender?->profile_photo)

                                                <img
                                                    src="{{ asset(
                                                        'storage/' .
                                                        $sender
                                                            ->profile_photo
                                                    ) }}"
                                                    alt="{{ $sender->name }}"
                                                    class="object-cover w-8 h-8 border rounded-full border-white/10"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-white/10 {{ $isMine
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
                                        class=""
                                    >

                                        <div
                                            class="ref-bubble {{ $isMine ? 'mine' : 'theirs' }}"
                                        >

                                            <div
                                                class="hidden"
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
                                                        class="inline-block mt-3 overflow-hidden border rounded-2xl border-white/10 bg-black/10"
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
                                                            class="object-cover w-auto max-w-[230px] sm:max-w-[300px] max-h-52 rounded-xl"
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
                                                    class="text-left ref-meta"
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

                        <div
                            id="typingIndicator"
                            class="hidden mt-5 text-sm font-bold text-cyan-300"
                        >
                            يكتب الآن...
                        </div>

                    </div>

                    {{-- إرسال رسالة --}}
                    <div
                        class="ref-compose"
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
                                class="ref-compose-box"
                            >

                                <textarea
                                    id="message"
                                    name="message"
                                    rows="1"
                                    placeholder="اكتب رسالتك هنا..."
                                    class="ref-input"
                                >{{ old('message') }}</textarea>

                                <div
                                    class="flex items-center flex-none gap-2"
                                >

                                    <div class="flex items-center gap-3">

                                        <label
                                            for="attachment"
                                            class="cursor-pointer ref-attach"
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
                                            class="max-w-[230px] text-xs truncate text-slate-400"
                                        ></span>

                                    </div>

                                    <button
                                        id="sendButton"
                                        type="submit"
                                        class="transition ref-send hover:scale-105"
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
        class="fixed inset-0 z-40 hidden bg-slate-950/70 backdrop-blur-sm xl:hidden"
    ></div>

    <div
        id="consultationDetailsDrawer"
        class="fixed inset-y-0 right-0 z-50 hidden w-[92%] max-w-sm p-4 overflow-y-auto border-l shadow-2xl border-white/10 bg-slate-950 xl:hidden"
        dir="rtl"
    >
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-base ref-card-title">تفاصيل الاستشارة</h3>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $consultation->consultation_number }}
                </p>
            </div>

            <button
                type="button"
                id="closeConsultationDetails"
                class="inline-flex items-center justify-center w-10 h-10 text-xl border rounded-full border-white/10 bg-white/5 text-slate-300"
            >
                ×
            </button>
        </div>

        <div class="space-y-4">
            <div class="p-4 border rounded-2xl border-white/10 bg-white/[0.04]">
                <p class="text-xs text-slate-500">عنوان الاستشارة</p>
                <p class="mt-2 font-bold text-white">{{ $consultation->title }}</p>
            </div>

            <div class="p-4 border rounded-2xl border-white/10 bg-white/[0.04]">
                <p class="text-xs text-slate-500">نوع الاستشارة</p>
                <p class="mt-2 font-bold text-white">
                    {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                </p>
            </div>

            <div class="p-4 border rounded-2xl border-white/10 bg-white/[0.04]">
                <p class="text-xs text-slate-500">الحالة</p>
                <p class="mt-2 font-bold text-white">
                    {{ $statusLabels[$consultation->status] ?? $consultation->status }}
                </p>
            </div>

            @if ($consultation->engineer)
                <a
                    href="{{ route('engineers.show', $consultation->engineer) }}"
                    class="flex items-center gap-3 p-4 transition border rounded-2xl border-cyan-500/20 bg-cyan-500/10 hover:bg-cyan-500/15"
                >
                    @if ($consultation->engineer->profile_photo)
                        <img
                            src="{{ asset('storage/' . $consultation->engineer->profile_photo) }}"
                            alt="{{ $consultation->engineer->name }}"
                            class="object-cover w-12 h-12 rounded-full ring-2 ring-cyan-500/30"
                        >
                    @else
                        <div
                            class="flex items-center justify-center w-12 h-12 font-black text-white rounded-full bg-gradient-to-br from-cyan-500 to-blue-600"
                        >
                            {{ mb_substr($consultation->engineer->name, 0, 1) }}
                        </div>
                    @endif

                    <div>
                        <p class="font-black text-white">
                            {{ $consultation->engineer->name }}
                        </p>
                        <p class="mt-1 text-xs text-cyan-300">
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
                    dot.classList.toggle('bg-slate-500', !online);
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
                    `ref-message ${mine ? 'mine' : 'theirs'}`;

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
                                    class="object-cover w-8 h-8 border rounded-full border-white/10"
                                >`
                                : `<div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-white/10 ${
                                        mine
                                            ? 'bg-gradient-to-br from-blue-600 to-violet-600'
                                            : 'bg-gradient-to-br from-cyan-600 to-emerald-600'
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
                                class="inline-block mt-3 overflow-hidden border rounded-2xl border-white/10 bg-black/10"
                            >
                                <img
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                    alt="مرفق"
                                    class="object-cover w-auto max-w-[230px] sm:max-w-[300px] max-h-52 rounded-xl"
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
                                class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-white/10 bg-black/15 hover:bg-black/25"
                            >
                                <div class="flex items-center min-w-0 gap-3">
                                    <div
                                        class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-200"
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
                                    class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-white/10"
                                >↓</span>
                            </a>
                        `;
                    }
                }

                wrapper.innerHTML = `
                    ${avatar}

                    <div class="">
                        <div
                            class="ref-bubble ${mine ? 'mine' : 'theirs'}"
                        >
                            <div
                                class="hidden"
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
                                            ? 'text-blue-100/70'
                                            : 'text-slate-500'
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
                                        class="text-left ref-meta"
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
