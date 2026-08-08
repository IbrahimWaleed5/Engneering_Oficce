<x-app-layout>
    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
    >
        <div class="max-w-5xl px-4 py-10 mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-white/5 border-white/10"
                >
                    <span>⚙️</span>
                    <span class="text-sm font-bold text-slate-300">
                        إعدادات الحساب
                    </span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    حالة الحساب
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    راقب حالة حسابك والتحذيرات والمخالفات المرتبطة به.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            <section
                class="overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-white/10">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-2xl font-black text-white">
                                    📊 حالة الحساب
                                </h2>

                                <span
                                    class="inline-flex items-center px-3 py-1 text-sm font-black border rounded-full {{ $accountStatusClasses }}"
                                >
                                    {{ $accountStatusLabel }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                يتم تعليق الحساب تلقائيًا عند الوصول إلى
                                {{ $accountWarningLimit }}
                                مخالفات مؤكدة.
                            </p>
                        </div>

                        @if (
                            in_array(
                                $user->status,
                                [
                                    'suspended',
                                    'suspended_pending_review',
                                ],
                                true
                            )
                            && Route::has('moderation.appeal.create')
                        )
                            <a
                                href="{{ route('moderation.appeal.create') }}"
                                class="inline-flex items-center justify-center px-5 py-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                            >
                                تقديم اعتراض
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <div
                            class="p-5 border rounded-2xl border-white/10 bg-slate-900/50"
                        >
                            <p class="text-sm font-bold text-slate-400">
                                التحذيرات المسجلة
                            </p>

                            <p class="mt-3 text-3xl font-black text-white">
                                {{ $accountTotalWarnings }}
                            </p>
                        </div>

                        <div
                            class="p-5 border rounded-2xl border-white/10 bg-slate-900/50"
                        >
                            <p class="text-sm font-bold text-slate-400">
                                المخالفات المؤكدة
                            </p>

                            <p class="mt-3 text-3xl font-black text-white">
                                {{ $accountWarningsCount }}
                                <span class="text-sm font-bold text-slate-500">
                                    / {{ $accountWarningLimit }}
                                </span>
                            </p>
                        </div>

                        <div
                            class="p-5 border rounded-2xl border-white/10 bg-slate-900/50"
                        >
                            <p class="text-sm font-bold text-slate-400">
                                المحتوى المرفوض
                            </p>

                            <p class="mt-3 text-3xl font-black text-white">
                                {{ $accountRejectedViolations }}
                            </p>
                        </div>

                        <div
                            class="p-5 border rounded-2xl
                                {{ $accountWarningsRemaining > 1
                                    ? 'border-emerald-500/30 bg-emerald-500/10'
                                    : ($accountWarningsRemaining === 1
                                        ? 'border-amber-500/30 bg-amber-500/10'
                                        : 'border-red-500/30 bg-red-500/10') }}"
                        >
                            <p class="text-sm font-bold text-slate-300">
                                المتبقي حتى الحظر
                            </p>

                            <p
                                class="mt-3 text-3xl font-black
                                    {{ $accountWarningsRemaining > 1
                                        ? 'text-emerald-200'
                                        : ($accountWarningsRemaining === 1
                                            ? 'text-amber-200'
                                            : 'text-red-200') }}"
                            >
                                @if ($accountWarningsRemaining === 0)
                                    تم بلوغ الحد
                                @elseif ($accountWarningsRemaining === 1)
                                    مخالفة واحدة
                                @elseif ($accountWarningsRemaining === 2)
                                    مخالفتان
                                @else
                                    {{ $accountWarningsRemaining }} مخالفات
                                @endif
                            </p>
                        </div>
                    </div>

                    <div
                        class="p-5 mt-6 border rounded-2xl border-white/10 bg-slate-900/50"
                    >
                        <div
                            class="flex items-center justify-between mb-3 text-sm font-bold text-slate-400"
                        >
                            <span>مستوى المخالفات</span>

                            <span>
                                {{ min($accountWarningsCount, $accountWarningLimit) }}
                                / {{ $accountWarningLimit }}
                            </span>
                        </div>

                        <div
                            class="h-3 overflow-hidden rounded-full bg-slate-800"
                        >
                            <div
                                class="h-full rounded-full transition-all
                                    {{ $accountWarningsCount >= 3
                                        ? 'bg-red-500'
                                        : ($accountWarningsCount === 2
                                            ? 'bg-amber-500'
                                            : 'bg-emerald-500') }}"
                                style="width: {{ min(
                                    100,
                                    ($accountWarningsCount / $accountWarningLimit) * 100
                                ) }}%"
                            ></div>
                        </div>

                        <p class="mt-4 text-sm leading-7 text-slate-400">
                            المخالفات المؤكدة هي التي تدخل في احتساب حد تعليق الحساب.
                            أما التحذيرات المسجلة والمحتوى المرفوض فتبقى ظاهرة هنا كسجل لحالة الحساب.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
