<x-app-layout>
    @php
        $currentUser = auth()->user();

        $profilePhoto =
            $currentUser?->profile_photo_path
            ?? $currentUser?->profile_photo
            ?? null;
    @endphp

    @push('styles')
@endpush

    <style>
        .join-office-page {
            --primary: #3b82f6;
            --surface: #0b1326;
            --surface-container: #131b2e;
            --surface-container-high: #1a243a;
            --on-surface: #f8fafc;
            --on-surface-variant: #cbd5e1;
            --outline: #475569;
            --outline-variant: #334155;
            min-height: 100vh;
            background: var(--surface);
            color: var(--on-surface);
            font-family: "Hanken Grotesk", "Almarai", sans-serif;
        }

        .join-office-page .form-control {
            width: 100%;
            border: 1px solid var(--outline);
            border-radius: .5rem;
            background: var(--surface);
            color: var(--on-surface);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .join-office-page .form-control:hover {
            border-color: var(--outline-variant);
        }

        .join-office-page .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 1px var(--primary);
            outline: none;
        }

        .join-office-page .file-shell {
            position: relative;
            display: flex;
            min-height: 3rem;
            align-items: center;
            overflow: hidden;
            border: 1px solid var(--outline);
            border-radius: .5rem;
            background: var(--surface);
            transition: border-color .2s ease;
        }

        .join-office-page .file-shell:hover {
            border-color: var(--outline-variant);
        }
    </style>

    <div class="join-office-page" dir="rtl">
        {{-- Top navigation --}}
        <header class="sticky top-0 z-50 w-full border-b border-[#334155] bg-[#0b1326]/95 backdrop-blur-xl">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-3 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-[#334155] bg-[#1a243a]">
                        <svg class="text-xl text-[#3b82f6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21h16M6 21V9l6-5 6 5v12M9 21v-6h6v6M9 10h.01M12 10h.01M15 10h.01"/></svg>
                    </div>

                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold text-[#f8fafc]">مكتب الوليد الهندسي</h1>
                        <p class="text-xs text-[#cbd5e1]">منصة الاستشارات الهندسية</p>
                    </div>
                </div>

                <nav class="hidden items-center gap-6 md:flex">
                    @if (Route::has('home'))
                        <a href="{{ route('home') }}" class="flex items-center gap-1 font-medium text-[#cbd5e1] transition hover:text-[#3b82f6]">
                            <svg class="text-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 9-8 9 8"/><path d="M5 10v11h14V10M9 21v-6h6v6"/></svg>
                            الصفحة الرئيسية
                        </a>
                    @endif

                    @if (Route::has('dashboard'))
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-1 font-medium text-[#cbd5e1] transition hover:text-[#3b82f6]">
                            <svg class="text-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                            لوحة التحكم
                        </a>
                    @endif

                    @if (Route::has('engineer.works.public'))
                        <a href="{{ route('engineer.works.public') }}" class="flex items-center gap-1 font-medium text-[#cbd5e1] transition hover:text-[#3b82f6]">
                            <svg class="text-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V4H6.5A2.5 2.5 0 0 0 4 6.5v13Z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/></svg>
                            مكتبة المهندسين
                        </a>
                    @endif

                    @if (Route::has('consultations.mine'))
                        <a href="{{ route('consultations.mine') }}" class="flex items-center gap-1 font-medium text-[#cbd5e1] transition hover:text-[#3b82f6]">
                            <svg class="text-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4"/></svg>
                            طلباتي
                        </a>
                    @endif

                    @if (Route::has('engineering-offices.index'))
                        <a href="{{ route('engineering-offices.index') }}" class="flex items-center gap-1 border-b-2 border-[#3b82f6] pb-1 font-bold text-[#3b82f6]">
                            <svg class="text-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h12M5 21V5h8v16M8 8h2M8 12h2M8 16h2M19 8v6M16 11h6"/></svg>
                            المكاتب الهندسية
                        </a>
                    @endif
                </nav>

                <div class="flex items-center gap-3">
                    @if (Route::has('notifications.index'))
                        <a href="{{ route('notifications.index') }}" class="relative rounded-full p-2 text-[#cbd5e1] transition hover:bg-[#131b2e] hover:text-[#3b82f6]">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                        </a>
                    @endif

                    @if (Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg border-l border-[#334155] p-1 pl-2 transition hover:bg-[#131b2e]">
                            <div class="hidden text-left sm:block">
                                <p class="text-sm font-semibold text-[#f8fafc]">{{ $currentUser?->name }}</p>
                                <p class="text-xs text-[#cbd5e1]">مهندس</p>
                            </div>

                            <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-[#475569] bg-[#1a243a]">
                                @if ($profilePhoto)
                                    <img
                                        src="{{ asset('storage/' . $profilePhoto) }}"
                                        alt="{{ $currentUser?->name }}"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <span class="font-black text-[#3b82f6]">
                                        {{ mb_substr($currentUser?->name ?? 'م', 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <main class="flex flex-grow justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="w-full max-w-3xl">

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 p-4 text-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-500/20 bg-red-500/10 p-4 text-red-100">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="rounded-xl border border-[#334155] bg-[#131b2e]/80 p-6 shadow-2xl backdrop-blur-md sm:p-10">
                    <div class="mb-8 text-center sm:text-right">
                        <p class="mb-1 text-sm font-medium text-[#3b82f6]">
                            طلب الانضمام إلى مكتب هندسي
                        </p>

                        <h1 class="mb-2 text-3xl font-bold tracking-tight text-[#f8fafc] sm:text-4xl">
                            {{ $office->name }}
                        </h1>

                        <p class="text-sm text-[#cbd5e1]">
                            أرسل بياناتك المهنية إلى مدير المكتب. لا يوجد دفع أو رفع إيصال من المهندس.
                        </p>
                    </div>

                    <div class="mb-8 h-px w-full bg-[#334155]"></div>

                    <div class="mb-8 rounded-lg border border-[#475569] bg-[#1a243a] p-5">
                        <h2 class="mb-3 flex items-center gap-2 text-base font-semibold text-[#f8fafc]">
                            <svg class="text-[#3b82f6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                            الملفات المطلوبة
                        </h2>

                        <ul class="space-y-2 text-sm text-[#cbd5e1]">
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M9 13h6M9 17h6M9 9h2"/></svg>
                                السيرة الذاتية CV.
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-5 9 5-9 5-9-5Z"/><path d="M7 12v5c3 2 7 2 10 0v-5M21 10v6"/></svg>
                                الشهادة الجامعية أو المهنية.
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V4h8v3M3 12h18M10 12v2h4v-2"/></svg>
                                تحديد التخصص الهندسي.
                            </li>
                        </ul>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('office-membership-applications.store', $office) }}"
                        enctype="multipart/form-data"
                        class="space-y-6"
                    >
                        @csrf

                        <div>
                            <label for="specialty_id" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                التخصص الهندسي
                            </label>

                            <div class="relative">
                                <select
                                    id="specialty_id"
                                    name="specialty_id"
                                    required
                                    class="form-control appearance-none px-4 py-3 text-sm"
                                >
                                    <option value="">اختر التخصص</option>

                                    @foreach ($specialties as $specialty)
                                        <option
                                            value="{{ $specialty->id }}"
                                            @selected((string) old('specialty_id') === (string) $specialty->id)
                                        >
                                            {{ $specialty->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#cbd5e1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="requested_position" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                    المسمى الوظيفي المطلوب
                                </label>

                                <div class="relative">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-lg text-[#cbd5e1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3"/><path d="M6 21a6 6 0 0 1 12 0M5 3h14v14H5z"/></svg>

                                    <input
                                        id="requested_position"
                                        type="text"
                                        name="requested_position"
                                        value="{{ old('requested_position') }}"
                                        maxlength="150"
                                        placeholder="مثال: مهندس معماري"
                                        class="form-control py-3 pl-4 pr-10 text-sm placeholder:text-[#cbd5e1]/50"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="years_of_experience" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                    سنوات الخبرة
                                </label>

                                <div class="relative">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-lg text-[#cbd5e1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>

                                    <input
                                        id="years_of_experience"
                                        type="number"
                                        name="years_of_experience"
                                        value="{{ old('years_of_experience') }}"
                                        min="0"
                                        max="60"
                                        placeholder="0"
                                        class="form-control py-3 pl-4 pr-10 text-sm placeholder:text-[#cbd5e1]/50"
                                    >
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="cv" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                السيرة الذاتية CV
                            </label>

                            <label class="file-shell cursor-pointer">
                                <span class="flex h-12 shrink-0 items-center gap-2 border-l border-[#475569] bg-[#1a243a] px-4 text-sm font-medium">
                                    <svg class="text-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M5 20h14a3 3 0 0 0 .8-5.9A6 6 0 0 0 8.3 8.4 4 4 0 0 0 5 16"/></svg>
                                    اختيار ملف
                                </span>

                                <span id="cv-file-name" class="truncate px-4 text-sm text-[#cbd5e1]">
                                    لم يتم اختيار أي ملف
                                </span>

                                <input
                                    id="cv"
                                    type="file"
                                    name="cv"
                                    accept=".pdf,.doc,.docx"
                                    required
                                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                    onchange="
                                        document.getElementById('cv-file-name').textContent =
                                            this.files[0]
                                                ? this.files[0].name
                                                : 'لم يتم اختيار أي ملف';
                                    "
                                >
                            </label>

                            <p class="mt-1.5 text-xs text-[#cbd5e1]">
                                PDF أو Word، بحد أقصى 10 ميجابايت.
                            </p>
                        </div>

                        <div>
                            <label for="certificate" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                الشهادة الجامعية أو المهنية
                            </label>

                            <label class="file-shell cursor-pointer">
                                <span class="flex h-12 shrink-0 items-center gap-2 border-l border-[#475569] bg-[#1a243a] px-4 text-sm font-medium">
                                    <svg class="text-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M5 20h14a3 3 0 0 0 .8-5.9A6 6 0 0 0 8.3 8.4 4 4 0 0 0 5 16"/></svg>
                                    اختيار ملف
                                </span>

                                <span id="certificate-file-name" class="truncate px-4 text-sm text-[#cbd5e1]">
                                    لم يتم اختيار أي ملف
                                </span>

                                <input
                                    id="certificate"
                                    type="file"
                                    name="certificate"
                                    accept=".pdf,.jpg,.jpeg,.png,.webp"
                                    required
                                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                    onchange="
                                        document.getElementById('certificate-file-name').textContent =
                                            this.files[0]
                                                ? this.files[0].name
                                                : 'لم يتم اختيار أي ملف';
                                    "
                                >
                            </label>

                            <p class="mt-1.5 text-xs text-[#cbd5e1]">
                                PDF أو صورة، بحد أقصى 10 ميجابايت.
                            </p>
                        </div>

                        <div>
                            <label for="message" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                رسالة إلى مدير المكتب
                            </label>

                            <textarea
                                id="message"
                                name="message"
                                rows="4"
                                maxlength="3000"
                                placeholder="اكتب نبذة مختصرة عن خبرتك وسبب رغبتك في الانضمام..."
                                class="form-control resize-y px-4 py-3 text-sm placeholder:text-[#cbd5e1]/50"
                            >{{ old('message') }}</textarea>
                        </div>

                        <div class="mt-6 flex flex-col-reverse items-center justify-end gap-4 border-t border-[#334155] pt-6 sm:flex-row">
                            <a
                                href="{{ route('engineering-offices.show', $office) }}"
                                class="w-full rounded-md border border-[#475569] bg-transparent px-6 py-2.5 text-center text-sm font-semibold text-[#f8fafc] transition hover:bg-[#1a243a] sm:w-auto"
                            >
                                العودة إلى المكتب
                            </a>

                            <button
                                type="submit"
                                class="w-full rounded-md bg-[#3b82f6] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_0_15px_rgba(59,130,246,0.5)] transition hover:bg-blue-600 hover:shadow-[0_0_25px_rgba(59,130,246,0.7)] focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-offset-2 focus:ring-offset-[#0b1326] sm:w-auto"
                            >
                                إرسال طلب الانضمام
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
