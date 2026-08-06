<x-app-layout>
    <div
        class="min-h-screen px-4 py-8 bg-slate-950 text-slate-100 sm:px-6 lg:px-8"
        dir="rtl"
    >
        <style>
            .appeal-glass-card {
                background: rgba(15, 23, 42, 0.72);
                border: 1px solid rgba(51, 65, 85, 0.72);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.16);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }

            .appeal-alert {
                background: linear-gradient(
                    135deg,
                    rgba(127, 29, 29, 0.34) 0%,
                    rgba(69, 10, 10, 0.76) 100%
                );
                border: 1px solid rgba(248, 113, 113, 0.22);
                box-shadow: inset 0 0 40px rgba(69, 10, 10, 0.36);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }

            [x-cloak] {
                display: none !important;
            }
        </style>

        <main class="relative w-full max-w-6xl mx-auto">
            {{-- رسالة تعليق الحساب --}}
            <section class="flex flex-col items-center gap-6 mb-6 appeal-alert rounded-2xl p-7 md:flex-row md:p-10">
                <div class="flex h-16 w-16 flex-none items-center justify-center rounded-full border border-red-300/25 bg-red-300/10 shadow-[0_0_20px_rgba(248,113,113,.16)]">
                    <svg class="w-8 h-8 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7L2.6 17a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 3.7a2 2 0 00-3.4 0z"/>
                    </svg>
                </div>

                <div class="flex-1 text-center md:text-right">
                    <h1 class="mb-2 text-3xl font-black text-red-100 md:text-4xl">
                        تم تعليق حسابك
                    </h1>

                    <p class="text-base leading-8 text-red-200/80 md:text-lg">
                        @if ($pendingAppeal)
                            لقد قمت بتقديم طعن، يرجى انتظار مراجعة الإدارة.
                        @else
                            تم تعليق حسابك بسبب مخالفة شروط وسياسة المحتوى. يمكنك تقديم طعن عندما تعتقد أن القرار صدر بالخطأ.
                        @endif
                    </p>
                </div>
            </section>

            @if (session('success'))
                <div class="p-4 mb-6 border rounded-xl border-emerald-500/25 bg-emerald-500/10 text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                {{-- بيانات الحساب وآخر مخالفة --}}
                <aside class="space-y-6 md:col-span-4">
                    <section class="p-6 appeal-glass-card rounded-xl">
                        <h2 class="flex items-center gap-2 pb-4 mb-5 text-xl font-black text-white border-b border-slate-700">
                            <svg class="w-6 h-6 text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 00-16 0m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg>
                            بيانات الحساب
                        </h2>

                        <div class="space-y-5">
                            <div>
                                <span class="block mb-1 text-xs font-bold tracking-wider uppercase text-slate-500">
                                    الاسم
                                </span>
                                <span class="font-bold text-slate-100">
                                    {{ $user->name }}
                                </span>
                            </div>

                            <div>
                                <span class="block mb-1 text-xs font-bold tracking-wider uppercase text-slate-500">
                                    البريد الإلكتروني
                                </span>
                                <span class="block text-sm break-all text-slate-300">
                                    {{ $user->email }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 border rounded-lg border-slate-700 bg-slate-900/80">
                                <span class="text-xs font-bold uppercase text-slate-400">
                                    عدد التحذيرات
                                </span>
                                <span class="px-2 py-1 text-sm font-black text-red-300 rounded bg-red-400/10">
                                    {{ $user->warnings_count ?? 0 }} / 3
                                </span>
                            </div>
                        </div>
                    </section>

                    @if ($latestWarning)
                        <section class="p-6 border-r-4 appeal-glass-card rounded-xl border-r-red-400">
                            <h2 class="flex items-center gap-2 pb-4 mb-5 text-xl font-black text-white border-b border-slate-700">
                                <svg class="w-6 h-6 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20h16M7 17l10-10M14 4l3 3M4 13l7 7"/>
                                </svg>
                                تفاصيل آخر مخالفة
                            </h2>

                            <div class="space-y-5">
                                <div>
                                    <span class="block mb-1 text-xs font-bold tracking-wider uppercase text-slate-500">
                                        التصنيف
                                    </span>
                                    <span class="font-bold text-slate-100">
                                        {{ $latestWarning->category ?: 'غير محدد' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block mb-1 text-xs font-bold tracking-wider uppercase text-slate-500">
                                        التاريخ
                                    </span>
                                    <span class="text-sm text-blue-300" dir="ltr">
                                        {{ $latestWarning->created_at?->format('Y-m-d H:i') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block mb-2 text-xs font-bold tracking-wider uppercase text-slate-500">
                                        السبب
                                    </span>
                                    <p class="p-3 text-sm leading-7 border rounded-lg border-slate-700 bg-slate-900/80 text-slate-300">
                                        {{ $latestWarning->reason }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    @endif
                </aside>

                {{-- مركز الطعون --}}
                <section class="md:col-span-8">
                    <div class="appeal-glass-card flex h-full min-h-[580px] flex-col rounded-xl p-6 md:p-10">
                        <h2 class="flex items-center gap-3 text-3xl font-black text-blue-300 mb-7">
                            <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v18M5 7h14M7 7l-4 7h8L7 7zm10 0l-4 7h8l-4-7zM8 21h8"/>
                            </svg>
                            مركز الطعون
                        </h2>

                        @if ($pendingAppeal)
                            <div class="flex flex-col items-center justify-center flex-1 p-8 text-center border-2 border-dashed rounded-xl border-slate-700 bg-slate-900/50">
                                <svg class="w-16 h-16 mb-5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 7v5l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>

                                <h3 class="mb-3 text-2xl font-black text-white">
                                    الطعن قيد المراجعة
                                </h3>

                                <p class="max-w-xl text-sm leading-8 text-slate-400">
                                    تم استلام طلب الطعن الخاص بك وهو الآن
                                    {{ $pendingAppeal->status === 'under_review' ? 'تحت مراجعة الإدارة' : 'بانتظار بدء المراجعة' }}.
                                </p>

                                <div class="w-full max-w-xl p-5 mt-6 text-right border rounded-xl border-slate-700 bg-slate-950/70">
                                    <span class="block mb-2 text-xs font-bold text-slate-500">
                                        رسالتك
                                    </span>
                                    <p class="text-sm leading-8 whitespace-pre-line text-slate-300">
                                        {{ $pendingAppeal->message }}
                                    </p>
                                </div>

                                <p class="mt-4 text-xs text-slate-500">
                                    تاريخ الإرسال:
                                    {{ $pendingAppeal->created_at?->format('Y-m-d H:i') }}
                                </p>

                                @if ($pendingAppeal->status === 'pending')
                                    <form
                                        method="POST"
                                        action="{{ route('moderation.appeal.cancel', $pendingAppeal) }}"
                                        class="mt-6"
                                        onsubmit="return confirm('هل تريد إلغاء طلب الطعن؟');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="px-5 py-3 text-sm font-black text-red-300 transition border rounded-lg border-red-400/30 hover:bg-red-500 hover:text-white"
                                        >
                                            إلغاء الطعن
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            @if (
                                $latestAppeal
                                && in_array($latestAppeal->status, ['approved', 'rejected'], true)
                            )
                                <div
                                    class="mb-6 rounded-xl border p-5
                                    {{ $latestAppeal->status === 'approved'
                                        ? 'border-emerald-500/25 bg-emerald-500/10'
                                        : 'border-red-500/25 bg-red-500/10' }}"
                                >
                                    <h3 class="text-xl font-black {{ $latestAppeal->status === 'approved' ? 'text-emerald-200' : 'text-red-200' }}">
                                        {{ $latestAppeal->status === 'approved' ? 'تم قبول الطعن السابق' : 'تم رفض الطعن السابق' }}
                                    </h3>

                                    @if ($latestAppeal->admin_response)
                                        <p class="mt-3 text-sm leading-8 whitespace-pre-line text-slate-300">
                                            {{ $latestAppeal->admin_response }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('moderation.appeal.store') }}"
                                enctype="multipart/form-data"
                                class="flex flex-col flex-1"
                            >
                                @csrf

                                <div class="flex-1 space-y-7">
                                    <div>
                                        <label for="message" class="block mb-3 text-sm font-black text-slate-200">
                                            سبب الطعن <span class="text-red-300">*</span>
                                        </label>

                                        <textarea
                                            id="message"
                                            name="message"
                                            rows="7"
                                            minlength="20"
                                            maxlength="5000"
                                            required
                                            class="w-full p-4 text-sm leading-7 text-white transition border outline-none resize-none rounded-xl border-slate-700 bg-slate-950 placeholder:text-slate-600 focus:border-blue-400 focus:ring-1 focus:ring-blue-400"
                                            placeholder="اشرح بالتفصيل لماذا تعتقد أن تعليق الحساب كان بالخطأ..."
                                        >{{ old('message') }}</textarea>

                                        <p class="mt-2 text-xs text-slate-500">
                                            من 20 إلى 5000 حرف.
                                        </p>
                                    </div>

                                    <div x-data="{ fileName: '' }">
                                        <label class="block mb-3 text-sm font-black text-slate-200">
                                            المرفقات الداعمة (اختياري)
                                        </label>

                                        <label
                                            for="attachment"
                                            class="block text-center transition border-2 border-dashed cursor-pointer group rounded-xl border-slate-700 p-7 hover:border-blue-400 hover:bg-slate-900/80"
                                        >
                                            <input
                                                type="file"
                                                id="attachment"
                                                name="attachment"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                class="hidden"
                                                @change="fileName = $event.target.files[0]?.name || ''"
                                            >

                                            <svg class="w-10 h-10 mx-auto mb-3 transition text-slate-500 group-hover:text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14"/>
                                            </svg>

                                            <p class="text-sm text-slate-400">
                                                انقر لاختيار ملف داعم
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                JPG, PNG, PDF — الحد الأقصى 5 ميجابايت
                                            </p>

                                            <p
                                                x-show="fileName"
                                                x-cloak
                                                class="mt-3 text-sm font-bold text-blue-300"
                                                x-text="fileName"
                                            ></p>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-3 pt-6 mt-8 border-t border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                    </form>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-8 py-3 text-lg font-black transition bg-blue-300 rounded-lg text-blue-950 hover:bg-blue-200"
                                    >
                                        إرسال الطعن
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </section>
            </div>

            <div class="mt-8 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="text-sm font-bold transition text-slate-400 hover:text-white"
                    >
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>
