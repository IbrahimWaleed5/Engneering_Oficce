<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 mb-6 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    {{ session('info') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">
                        المكاتب الهندسية
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        طلبات انضمامي إلى المكاتب
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        تابع حالة طلبات الانضمام التي أرسلتها إلى المكاتب الهندسية.
                    </p>
                </div>

                <a
                    href="{{ route('engineering-offices.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                >
                    عرض جميع المكاتب
                </a>
            </div>

            <div class="space-y-5">
                @forelse ($applications as $application)
                    @php
                        $statusData = match ($application->status) {
                            'approved' => [
                                'label' => 'تم قبول الطلب',
                                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                                'icon' => '✅',
                            ],

                            'rejected' => [
                                'label' => 'تم رفض الطلب',
                                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                                'icon' => '⛔',
                            ],

                            'cancelled' => [
                                'label' => 'تم إلغاء الطلب',
                                'class' => 'text-slate-300 border-white/10 bg-white/5',
                                'icon' => '✖',
                            ],

                            default => [
                                'label' => 'قيد المراجعة',
                                'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                                'icon' => '⏳',
                            ],
                        };
                    @endphp

                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 text-2xl border rounded-2xl border-white/10 bg-slate-800">
                                    🏢
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-xl font-black text-white">
                                            {{ $application->office?->name ?? 'مكتب غير موجود' }}
                                        </h2>

                                        <span class="px-3 py-1 text-xs font-black border rounded-full text-cyan-200 border-cyan-500/20 bg-cyan-500/10">
                                            مكتب هندسي
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm text-slate-400">
                                        تاريخ الطلب:
                                        {{ $application->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-black border rounded-full {{ $statusData['class'] }}">
                                <span>
                                    {{ $statusData['icon'] }}
                                </span>

                                {{ $statusData['label'] }}
                            </span>
                        </div>

                        <div class="grid gap-4 mt-6 md:grid-cols-3">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    التخصص
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->specialty?->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    المسمى المطلوب
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->requested_position ?: 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    سنوات الخبرة
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->years_of_experience !== null
                                        ? $application->years_of_experience . ' سنة'
                                        : 'غير محددة' }}
                                </p>
                            </div>
                        </div>

                        @if ($application->message)
                            <div class="p-4 mt-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    رسالتك إلى المكتب
                                </p>

                                <p class="mt-2 leading-8 text-white">
                                    {{ $application->message }}
                                </p>
                            </div>
                        @endif

                        @if (
                            $application->status === 'rejected'
                            && $application->rejection_reason
                        )
                            <div class="p-5 mt-5 border rounded-2xl border-red-500/20 bg-red-500/10">
                                <p class="font-black text-red-200">
                                    سبب الرفض
                                </p>

                                <p class="mt-2 leading-8 text-red-100">
                                    {{ $application->rejection_reason }}
                                </p>
                            </div>
                        @endif

                        @if ($application->status === 'approved')
                            <div class="p-5 mt-5 border rounded-2xl border-green-500/20 bg-green-500/10">
                                <p class="font-black text-green-200">
                                    أصبحت عضوًا في المكتب
                                </p>

                                <p class="mt-2 leading-8 text-green-100">
                                    تم قبول طلبك وإضافتك إلى فريق المكتب الهندسي.
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-3 mt-6">
                            @if ($application->office)
                                <a
                                    href="{{ route(
                                        'engineering-offices.show',
                                        $application->office
                                    ) }}"
                                    class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                                >
                                    عرض المكتب
                                </a>
                            @endif

                            @if ($application->reviewer)
                                <div class="inline-flex items-center px-4 py-3 text-sm border text-slate-300 rounded-xl border-white/10 bg-white/5">
                                    تمت المراجعة بواسطة:
                                    <span class="mr-1 font-bold text-white">
                                        {{ $application->reviewer->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center border rounded-3xl border-white/10 bg-slate-900/70">
                        <div class="text-6xl">
                            📄
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            لا توجد طلبات انضمام
                        </h2>

                        <p class="mt-3 text-slate-400">
                            لم ترسل أي طلب انضمام إلى مكتب هندسي حتى الآن.
                        </p>

                        <a
                            href="{{ route('engineering-offices.index') }}"
                            class="inline-flex items-center justify-center px-6 py-3 mt-6 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                        >
                            استعراض المكاتب
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($applications->hasPages())
                <div class="mt-8">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
