<x-app-layout>
    @php
        $statusData = match ($application->status) {
            'approved' => [
                'label' => 'تم قبول الطلب',
                'class' => 'text-[#4ade80] border-green-500/30 bg-green-500/10',
                'dot' => '#22c55e',
            ],
            'rejected' => [
                'label' => 'تم رفض الطلب',
                'class' => 'text-[#f87171] border-red-500/30 bg-red-500/10',
                'dot' => '#ef4444',
            ],
            'cancelled' => [
                'label' => 'تم إلغاء الطلب',
                'class' => 'text-slate-300 border-white/10 bg-white/5',
                'dot' => '#94a3b8',
            ],
            default => [
                'label' => 'قيد المراجعة',
                'class' => 'text-[#eab308] border-yellow-500/30 bg-yellow-500/10',
                'dot' => '#eab308',
            ],
        };
    @endphp

    <style>
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800 {
            display: none !important;
        }

        .membership-review-page {
            min-height: 100vh;
            color: #e2e8f0;
            background:
                radial-gradient(circle at 85% 8%, rgba(56, 189, 248, .08), transparent 24rem),
                linear-gradient(135deg, #05070a 0%, #0b0f19 100%);
            font-family: "Tajawal", "Almarai", system-ui, sans-serif;
        }

        .membership-review-glass {
            background: rgba(20, 26, 41, .60);
            border: 1px solid rgba(255, 255, 255, .05);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .30);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .membership-review-input {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: .5rem;
            background: rgba(11, 15, 25, .80);
            color: #fff;
            padding: .75rem 1rem;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .membership-review-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, .20);
            outline: none;
        }

        .membership-review-data {
            display: flex;
            min-height: 5.25rem;
            flex-direction: column;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .05);
            border-radius: .75rem;
            background: rgba(11, 15, 25, .50);
            padding: 1rem;
        }

        .membership-review-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid #2a364e;
            border-radius: .75rem;
            background: rgba(20, 26, 41, .80);
            padding: 1rem;
            transition: background .2s ease, border-color .2s ease, transform .2s ease;
        }

        .membership-review-file:hover {
            transform: translateY(-1px);
            border-color: rgba(56, 189, 248, .45);
            background: #1c2539;
        }

        .membership-review-icon {
            display: inline-flex;
            width: 2.5rem;
            height: 2.5rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            background: #0b0f19;
            color: #38bdf8;
        }
    </style>

    <div class="membership-review-page p-4 md:p-8" dir="rtl">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 lg:flex-row">
            {{-- الإجراءات الجانبية --}}
            <aside class="order-2 flex w-full flex-col gap-6 lg:order-1 lg:w-1/3 xl:w-1/4">
                @if ($application->status === 'pending')
                    <section class="membership-review-glass rounded-2xl p-6">
                        <h2 class="mb-4 flex items-center gap-2 border-b border-[#2a364e] pb-3 text-xl font-bold text-white">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16.5 8.5"/>
                            </svg>
                            قبول الطلب
                        </h2>

                        <form
                            method="POST"
                            action="{{ route('office-membership-applications.review', $application) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="decision" value="approve">

                            <div class="mb-4">
                                <label for="position" class="mb-2 block text-sm font-medium text-gray-400">
                                    المسمى الوظيفي داخل المكتب
                                </label>

                                <input
                                    id="position"
                                    type="text"
                                    name="position"
                                    value="{{ old('position', $application->requested_position) }}"
                                    required
                                    maxlength="150"
                                    placeholder="مثال: مهندس معماري"
                                    class="membership-review-input"
                                >

                                @error('position')
                                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                onclick="return confirm('هل تريد قبول المهندس وإضافته إلى المكتب؟')"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#22c55e] px-4 py-3 font-bold text-white transition hover:bg-green-600"
                            >
                                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="9" cy="8" r="3"/>
                                    <path d="M2 20a7 7 0 0 1 14 0M19 8v6M16 11h6"/>
                                </svg>
                                قبول وإضافة المهندس
                            </button>
                        </form>
                    </section>

                    <section class="membership-review-glass rounded-2xl border-t-4 border-t-[#ef4444] p-6">
                        <h2 class="mb-4 flex items-center gap-2 border-b border-[#2a364e] pb-3 text-xl font-bold text-white">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 8 8 8M16 8l-8 8"/>
                            </svg>
                            رفض الطلب
                        </h2>

                        <form
                            method="POST"
                            action="{{ route('office-membership-applications.review', $application) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="decision" value="reject">

                            <div class="mb-4">
                                <label for="rejection_reason" class="mb-2 block text-sm font-medium text-gray-400">
                                    سبب الرفض
                                </label>

                                <textarea
                                    id="rejection_reason"
                                    name="rejection_reason"
                                    rows="4"
                                    required
                                    maxlength="3000"
                                    placeholder="اكتب سبب رفض الطلب هنا..."
                                    class="membership-review-input resize-y placeholder:text-gray-600"
                                >{{ old('rejection_reason') }}</textarea>

                                @error('rejection_reason')
                                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                onclick="return confirm('هل تريد رفض طلب المهندس؟')"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#ef4444] px-4 py-3 font-bold text-white transition hover:bg-red-600"
                            >
                                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="m8 8 8 8M16 8l-8 8"/>
                                </svg>
                                رفض الطلب
                            </button>
                        </form>
                    </section>
                @else
                    <section class="membership-review-glass rounded-2xl p-6">
                        <h2 class="mb-4 border-b border-[#2a364e] pb-3 text-xl font-bold text-white">
                            معلومات المراجعة
                        </h2>

                        <div class="space-y-4">
                            <div class="membership-review-data">
                                <span class="text-xs text-gray-500">تمت المراجعة بواسطة</span>
                                <span class="mt-2 font-bold text-white">
                                    {{ $application->reviewer?->name ?? 'غير معروف' }}
                                </span>
                            </div>

                            <div class="membership-review-data">
                                <span class="text-xs text-gray-500">تاريخ المراجعة</span>
                                <span class="mt-2 font-bold text-white">
                                    {{ $application->reviewed_at?->format('Y-m-d H:i') ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                    </section>
                @endif

                <a
                    href="{{ route('office-membership-applications.index') }}"
                    class="membership-review-glass flex items-center justify-center gap-2 rounded-xl p-4 text-center font-medium text-gray-300 transition hover:bg-[#1c2539]"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    العودة إلى جميع الطلبات
                </a>
            </aside>

            {{-- المحتوى الرئيسي --}}
            <main class="order-1 flex w-full flex-col gap-6 lg:order-2 lg:w-2/3 xl:w-3/4">
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

                @if ($errors->any())
                    <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-red-100">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <header class="mb-4 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <span class="mb-1 block text-sm font-semibold uppercase tracking-wider text-[#38bdf8]">
                            مراجعة طلب انضمام
                        </span>

                        <h1 class="mb-2 text-3xl font-extrabold text-white md:text-4xl">
                            {{ $application->engineer?->name ?? 'مهندس غير موجود' }}
                        </h1>

                        <p class="flex flex-wrap items-center gap-2 text-sm text-gray-400 md:text-base">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2a364e" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 21h16M6 21V9l6-5 6 5v12M9 21v-6h6v6"/>
                            </svg>
                            طلب انضمام إلى
                            <span class="font-semibold text-gray-200">
                                {{ $application->office?->name ?? 'غير معروف' }}
                            </span>
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-bold shadow-[0_0_10px_rgba(234,179,8,.12)] {{ $statusData['class'] }}">
                        <span class="h-2 w-2 rounded-full" style="background: {{ $statusData['dot'] }}"></span>
                        {{ $statusData['label'] }}
                    </span>
                </header>

                <section class="membership-review-glass rounded-2xl p-6 md:p-8">
                    <h2 class="mb-6 flex items-center gap-3 border-b border-[#2a364e] pb-3 text-2xl font-bold text-white">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <circle cx="9" cy="10" r="2"/>
                            <path d="M5.5 17a3.5 3.5 0 0 1 7 0M14 8h4M14 12h4"/>
                        </svg>
                        بيانات المهندس
                    </h2>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="membership-review-data">
                            <span class="mb-1 flex items-center gap-1 text-xs text-gray-500">
                                البريد الإلكتروني
                            </span>
                            <span class="truncate text-lg font-medium text-white" dir="ltr">
                                {{ $application->engineer?->email ?? 'غير متوفر' }}
                            </span>
                        </div>

                        <div class="membership-review-data">
                            <span class="mb-1 text-xs text-gray-500">الاسم</span>
                            <span class="text-lg font-medium text-white">
                                {{ $application->engineer?->name ?? 'غير متوفر' }}
                            </span>
                        </div>

                        <div class="membership-review-data">
                            <span class="mb-1 text-xs text-gray-500">التخصص</span>
                            <span class="text-lg font-medium text-white">
                                {{ $application->specialty?->name ?? 'غير محدد' }}
                            </span>
                        </div>

                        <div class="membership-review-data">
                            <span class="mb-1 text-xs text-gray-500">رقم الهاتف</span>
                            <span class="text-lg font-medium text-white" dir="ltr">
                                {{ $application->engineer?->phone ?: 'غير متوفر' }}
                            </span>
                        </div>

                        <div class="membership-review-data">
                            <span class="mb-1 text-xs text-gray-500">سنوات الخبرة</span>
                            <span class="text-lg font-medium text-white">
                                {{ $application->years_of_experience !== null
                                    ? $application->years_of_experience . ' سنة'
                                    : 'غير محددة' }}
                            </span>
                        </div>

                        <div class="membership-review-data">
                            <span class="mb-1 text-xs text-gray-500">المسمى المطلوب</span>
                            <span class="text-lg font-medium text-white">
                                {{ $application->requested_position ?: 'غير محدد' }}
                            </span>
                        </div>

                        <div class="membership-review-data md:col-span-2">
                            <span class="mb-2 text-xs text-gray-500">رسالة المهندس</span>
                            <p class="min-h-[60px] rounded-lg bg-[#141a29]/50 p-3 text-base leading-8 text-gray-300">
                                {{ $application->message ?: 'لم يكتب المهندس رسالة.' }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="membership-review-glass rounded-2xl p-6 md:p-8">
                    <h2 class="mb-6 flex items-center gap-3 border-b border-[#2a364e] pb-3 text-2xl font-bold text-white">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 7h7l2 2h9v11H3z"/>
                            <path d="M3 7V4h7l2 3"/>
                        </svg>
                        ملفات الطلب
                    </h2>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a
                            href="{{ route('office-membership-applications.file', [
                                'officeMembershipApplication' => $application,
                                'type' => 'cv',
                            ]) }}"
                            class="membership-review-file group"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="membership-review-icon transition group-hover:scale-110">
                                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 2h8l4 4v16H6z"/>
                                        <path d="M14 2v5h5M9 13h6M9 17h6M9 9h2"/>
                                    </svg>
                                </span>
                                <span class="truncate font-medium text-white">السيرة الذاتية CV</span>
                            </div>

                            <span class="flex shrink-0 items-center gap-1 text-sm font-semibold text-[#38bdf8] transition group-hover:text-white">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                عرض الملف
                            </span>
                        </a>

                        <a
                            href="{{ route('office-membership-applications.file', [
                                'officeMembershipApplication' => $application,
                                'type' => 'certificate',
                            ]) }}"
                            class="membership-review-file group"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="membership-review-icon transition group-hover:scale-110">
                                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="9" r="6"/>
                                        <path d="m8.5 14.5-1 7L12 19l4.5 2.5-1-7"/>
                                        <path d="m9.5 9 1.5 1.5L14.5 7"/>
                                    </svg>
                                </span>
                                <span class="truncate font-medium text-white">الشهادة الجامعية</span>
                            </div>

                            <span class="flex shrink-0 items-center gap-1 text-sm font-semibold text-[#38bdf8] transition group-hover:text-white">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                عرض الملف
                            </span>
                        </a>
                    </div>
                </section>

                @if ($application->status === 'rejected')
                    <section class="membership-review-glass rounded-2xl border border-red-500/20 p-6">
                        <h2 class="text-xl font-bold text-red-200">سبب رفض الطلب</h2>
                        <p class="mt-3 leading-8 text-red-100">
                            {{ $application->rejection_reason ?: 'لم يتم تسجيل سبب.' }}
                        </p>
                    </section>
                @endif

                @if ($application->status === 'approved')
                    <section class="membership-review-glass rounded-2xl border border-green-500/20 p-6">
                        <h2 class="text-xl font-bold text-green-200">تم قبول المهندس</h2>
                        <p class="mt-3 leading-8 text-green-100">
                            تمت إضافة المهندس إلى أعضاء المكتب بنجاح.
                        </p>
                    </section>
                @endif
            </main>
        </div>
    </div>
</x-app-layout>
