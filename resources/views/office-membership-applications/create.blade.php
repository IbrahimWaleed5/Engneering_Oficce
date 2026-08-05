<x-app-layout>
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

        /* تثبيت أحجام SVG ومنع الحجم الافتراضي الضخم للمتصفح */
        svg[aria-hidden="true"] {
            display: inline-block;
            max-width: 100%;
            vertical-align: middle;
        }

    </style>

    <div class="join-office-page" dir="rtl">
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
                            <svg class="text-[#3b82f6] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                            الملفات المطلوبة
                        </h2>

                        <ul class="space-y-2 text-sm text-[#cbd5e1]">
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6] shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M9 13h6M9 17h6M9 9h2"/></svg>
                                السيرة الذاتية CV.
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6] shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10 9-5 9 5-9 5-9-5Z"/><path d="M7 12v5c3 2 7 2 10 0v-5M21 10v6"/></svg>
                                الشهادة الجامعية أو المهنية.
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="text-lg text-[#3b82f6] shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V4h8v3M3 12h18M10 12v2h4v-2"/></svg>
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

                                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#cbd5e1] shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="requested_position" class="mb-2 block text-sm font-semibold text-[#f8fafc]">
                                    المسمى الوظيفي المطلوب
                                </label>

                                <div class="relative">
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-lg text-[#cbd5e1] shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3"/><path d="M6 21a6 6 0 0 1 12 0M5 3h14v14H5z"/></svg>

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
                                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-lg text-[#cbd5e1] shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>

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
                                    <svg class="text-lg shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M5 20h14a3 3 0 0 0 .8-5.9A6 6 0 0 0 8.3 8.4 4 4 0 0 0 5 16"/></svg>
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
                                    <svg class="text-lg shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M5 20h14a3 3 0 0 0 .8-5.9A6 6 0 0 0 8.3 8.4 4 4 0 0 0 5 16"/></svg>
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
