<x-app-layout>
    <style>
        .office-consultations-page {
            min-height: 100vh;
            background: #0b1326;
            color: #e3e4e7;
            font-family: "Hanken Grotesk", "Almarai", system-ui, sans-serif;
        }

        .office-consultations-glass {
            background: rgba(28, 36, 56, .72);
            border: 1px solid rgba(255, 255, 255, .05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .office-consultations-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .office-consultations-banner::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(
                circle,
                rgba(96, 165, 250, .30) 0%,
                rgba(255, 255, 255, 0) 70%
            );
        }

        .office-consultations-card {
            transition:
                border-color .25s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        .office-consultations-card:hover {
            transform: translateY(-2px);
            border-color: rgba(59, 130, 246, .45);
            box-shadow: 0 16px 40px rgba(0, 0, 0, .20);
        }

        .office-consultations-input {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: .75rem;
            background: #1c2438;
            color: #fff;
            padding: .75rem 1rem;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .office-consultations-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
            outline: none;
        }
    </style>

    <div class="py-10 office-consultations-page" dir="rtl">
        <div class="mx-auto max-w-[1400px] space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Hero --}}
            <section class="office-consultations-banner flex flex-col items-start justify-between gap-6 rounded-[20px] p-8 shadow-2xl md:flex-row md:items-center md:p-10">
                <div class="relative z-10 space-y-3">
                    <p class="text-sm font-medium tracking-wide text-blue-200">
                        إدارة أعمال المكتب
                    </p>

                    <h1 class="text-3xl font-bold text-white md:text-4xl">
                        استشارات {{ $office->name }}
                    </h1>

                    <p class="max-w-3xl text-sm leading-relaxed text-blue-100/90 md:text-base">
                        عرض الاستشارات المحولة إلى المكتب وتعيين مهندس فعال من فريق المكتب لكل استشارة لضمان جودة وسرعة الإنجاز.
                    </p>
                </div>

                <div class="relative z-10 flex flex-wrap gap-3">
                    <a
                        href="{{ route('office.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-6 py-2.5 font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
                    >
                        لوحة المكتب
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </a>

                    <a
                        href="{{ route('office.members.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-6 py-2.5 font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
                    >
                        أعضاء المكتب
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="9" cy="8" r="3"/>
                            <circle cx="17" cy="9" r="2.5"/>
                            <path d="M2 20a7 7 0 0 1 14 0M14 16a6 6 0 0 1 8 4"/>
                        </svg>
                    </a>
                </div>
            </section>

            {{-- Statistics --}}
            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <article class="flex items-center justify-between p-6 transition office-consultations-glass group rounded-2xl hover:border-blue-500/50">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-[#c3c6ca]">جميع الاستشارات</p>
                        <p class="text-3xl font-bold text-white transition group-hover:text-blue-400">
                            {{ $statistics['all'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#272f43] text-blue-500">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                            <path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>
                        </svg>
                    </div>
                </article>

                <article class="flex items-center justify-between p-6 transition office-consultations-glass group rounded-2xl hover:border-yellow-500/50">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-[#c3c6ca]">قيد الانتظار</p>
                        <p class="text-3xl font-bold text-white transition group-hover:text-yellow-400">
                            {{ $statistics['pending'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#272f43] text-yellow-500">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                    </div>
                </article>

                <article class="flex items-center justify-between p-6 transition office-consultations-glass group rounded-2xl hover:border-blue-400/50">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-[#c3c6ca]">قيد التنفيذ</p>
                        <p class="text-3xl font-bold text-white transition group-hover:text-blue-400">
                            {{ $statistics['in_progress'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#272f43] text-blue-400">
                        <svg class="animate-spin" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                            <path d="M21 3v6h-6"/>
                        </svg>
                    </div>
                </article>

                <article class="flex items-center justify-between p-6 transition office-consultations-glass group rounded-2xl hover:border-emerald-500/50">
                    <div class="space-y-1">
                        <p class="text-sm font-medium text-[#c3c6ca]">مكتملة</p>
                        <p class="text-3xl font-bold text-white transition group-hover:text-emerald-400">
                            {{ $statistics['completed'] ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#272f43] text-emerald-500">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m4 12 4 4 6-8"/>
                            <path d="m13 12 3 3 5-7"/>
                        </svg>
                    </div>
                </article>
            </section>

            @php
                $officeEngineers = $office
                    ->members()
                    ->where('office_role', 'engineer')
                    ->where('status', 'active')
                    ->with('user:id,name,email')
                    ->get();
            @endphp

            <section class="space-y-5">
                @forelse ($consultations as $consultation)
                    @php
                        $statusData = match ($consultation->status) {
                            'in_progress' => [
                                'label' => 'قيد التنفيذ',
                                'class' => 'text-blue-200 border-blue-500/20 bg-blue-500/10',
                            ],
                            'completed' => [
                                'label' => 'مكتملة',
                                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                            ],
                            'cancelled' => [
                                'label' => 'ملغاة',
                                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                            ],
                            default => [
                                'label' => 'قيد الانتظار',
                                'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                            ],
                        };
                    @endphp

                    <article
                        id="consultation-{{ $consultation->id }}"
                        class="office-consultations-glass office-consultations-card rounded-[20px] p-6"
                    >
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-black text-white">
                                        {{ $consultation->title }}
                                    </h2>

                                    <span class="rounded-full border px-3 py-1 text-xs font-black {{ $statusData['class'] }}">
                                        {{ $statusData['label'] }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-[#c3c6ca]">
                                    رقم الاستشارة:
                                    <span class="font-bold text-white">
                                        {{ $consultation->number }}
                                    </span>
                                </p>
                            </div>

                            <div class="text-sm text-[#c3c6ca]">
                                تاريخ الإنشاء:
                                <span class="font-bold text-white">
                                    {{ $consultation->created_at?->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-4 mt-6 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-xl border border-white/5 bg-[#272f43]/60 p-4">
                                <p class="text-xs text-[#c3c6ca]">العميل</p>
                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->customer?->name ?? 'غير معروف' }}
                                </p>
                                <p class="mt-1 text-xs text-[#8c9096]">
                                    {{ $consultation->customer?->email }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-white/5 bg-[#272f43]/60 p-4">
                                <p class="text-xs text-[#c3c6ca]">نوع الاستشارة</p>
                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-white/5 bg-[#272f43]/60 p-4">
                                <p class="text-xs text-[#c3c6ca]">السعر النهائي</p>
                                <p class="mt-2 font-black text-white">
                                    {{ number_format((float) $consultation->final_price, 2) }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-white/5 bg-[#272f43]/60 p-4">
                                <p class="text-xs text-[#c3c6ca]">المهندس الحالي</p>
                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->engineer?->name ?? 'لم يتم التعيين' }}
                                </p>
                            </div>
                        </div>

                        @if ($consultation->description)
                            <div class="mt-4 rounded-xl border border-white/5 bg-[#272f43]/40 p-4">
                                <p class="text-xs text-[#c3c6ca]">وصف الاستشارة</p>
                                <p class="mt-2 leading-8 text-white">
                                    {{ $consultation->description }}
                                </p>
                            </div>
                        @endif

                        @if (
                            $consultation->status !== 'completed'
                            && $consultation->status !== 'cancelled'
                        )
                            <form
                                method="POST"
                                action="{{ route('office.consultations.assign-engineer', $consultation) }}"
                                class="p-5 mt-6 border rounded-2xl border-blue-500/20 bg-blue-500/5"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                                    <div>
                                        <label
                                            for="engineer_id_{{ $consultation->id }}"
                                            class="block mb-2 font-bold text-white"
                                        >
                                            تعيين مهندس من المكتب
                                        </label>

                                        <select
                                            id="engineer_id_{{ $consultation->id }}"
                                            name="engineer_id"
                                            required
                                            class="office-consultations-input"
                                        >
                                            <option value="">اختر المهندس</option>

                                            @foreach ($officeEngineers as $member)
                                                <option
                                                    value="{{ $member->user_id }}"
                                                    @selected(
                                                        (string) old(
                                                            'engineer_id',
                                                            $consultation->engineer_id
                                                        ) === (string) $member->user_id
                                                    )
                                                >
                                                    {{ $member->user?->name ?? 'مهندس غير موجود' }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ($officeEngineers->isEmpty())
                                            <p class="mt-2 text-sm text-yellow-200">
                                                لا يوجد مهندسون فعالون في المكتب حاليًا.
                                            </p>
                                        @endif
                                    </div>

                                    <button
                                        type="submit"
                                        @disabled($officeEngineers->isEmpty())
                                        class="px-6 py-3 font-black text-white transition bg-blue-600 rounded-xl hover:bg-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        حفظ التعيين
                                    </button>
                                </div>
                            </form>
                        @endif

                        <div class="flex flex-wrap gap-3 mt-6">
                            <a
                                href="{{ route('office.consultations.index') }}#consultation-{{ $consultation->id }}"
                                class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                            >
                                الاستشارة الحالية
                            </a>

                            @if ($consultation->engineer)
                                <span class="inline-flex items-center px-4 py-3 text-sm text-green-200 border rounded-xl border-green-500/20 bg-green-500/10">
                                    مسندة إلى:
                                    <span class="mr-1 font-black">
                                        {{ $consultation->engineer->name }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </article>
                @empty
                    <section class="office-consultations-glass flex min-h-[400px] flex-col items-center justify-center rounded-[24px] border-2 border-dashed border-[#31394d]/60 p-16 text-center">
                        <div class="mb-6 flex h-24 w-24 items-center justify-center text-[#31394d]">
                            <svg width="74" height="74" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 7h7l2 2h9v10H3z"/>
                                <circle cx="15.5" cy="15.5" r="3.5"/>
                                <path d="m18 18 3 3"/>
                            </svg>
                        </div>

                        <h2 class="mb-2 text-xl font-bold text-white">
                            لا توجد استشارات محولة
                        </h2>

                        <p class="max-w-md text-[#c3c6ca]">
                            لم يقم مدير النظام بتحويل أي استشارة هندسية إلى مكتبكم حتى الآن. ستظهر جميع الطلبات الجديدة هنا فور تحويلها.
                        </p>

                        <a
                            href="{{ route('office.consultations.index') }}"
                            class="mt-8 inline-flex items-center gap-2 rounded-xl border border-[#31394d] bg-[#1c2438] px-5 py-2.5 text-sm font-medium transition hover:bg-[#272f43]"
                        >
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 11a8 8 0 1 0-2.3 5.7L20 14"/>
                                <path d="M20 6v5h-5"/>
                            </svg>
                            تحديث البيانات
                        </a>
                    </section>
                @endforelse
            </section>

            @if ($consultations->hasPages())
                <div>
                    {{ $consultations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
