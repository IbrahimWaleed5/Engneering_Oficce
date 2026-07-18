<div class="space-y-8" dir="rtl">

    <section
        class="relative p-8 overflow-hidden border bg-gradient-to-l from-blue-700/80 to-cyan-600/70 rounded-[2rem] border-cyan-400/20"
    >
        <div class="relative z-10">
            <p class="text-sm text-cyan-100">
                أهلًا بك
            </p>

            <h2 class="mt-2 text-3xl font-black text-white">
                {{ auth()->user()->name }}
            </h2>

            <p class="mt-3 text-cyan-50/80">
                يمكنك إنشاء استشارة ومتابعة الدفع والتنفيذ من مكان واحد.
            </p>

            <a
                href="{{ route('consultations.create') }}"
                class="inline-flex px-6 py-3 mt-6 font-black text-blue-700 transition bg-white rounded-2xl hover:bg-cyan-50"
            >
                طلب استشارة جديدة
            </a>
        </div>
    </section>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        @php
            $cards = [
                [
                    'title' => 'جميع استشاراتي',
                    'value' => $statistics['all'] ?? 0,
                    'icon' => '📋',
                ],
                [
                    'title' => 'بانتظار الدفع',
                    'value' => $statistics['waiting_payment'] ?? 0,
                    'icon' => '💳',
                ],
                [
                    'title' => 'قيد التنفيذ',
                    'value' => $statistics['in_progress'] ?? 0,
                    'icon' => '🛠️',
                ],
                [
                    'title' => 'مكتملة',
                    'value' => $statistics['completed'] ?? 0,
                    'icon' => '✅',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="p-6 glass-panel-strong rounded-[2rem]">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-slate-400">
                            {{ $card['title'] }}
                        </p>

                        <p class="mt-3 text-4xl font-black text-white">
                            {{ $card['value'] }}
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-cyan-500/10"
                    >
                        {{ $card['icon'] }}
                    </div>

                </div>

            </div>
        @endforeach

    </div>

    <div class="grid gap-6 md:grid-cols-3">

        <a
            href="{{ route('consultations.create') }}"
            class="p-7 transition border border-cyan-500/20 bg-cyan-500/10 rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">➕</div>

            <h3 class="mt-5 text-xl font-black text-white">
                استشارة جديدة
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-300">
                اختر النوع وارفع ملفات المشروع وأرسل الطلب.
            </p>
        </a>

        <a
            href="{{ route('consultations.mine') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">📂</div>

            <h3 class="mt-5 text-xl font-black text-white">
                متابعة الاستشارات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                متابعة حالة الدفع والمهندس والتنفيذ والملفات.
            </p>
        </a>

        <a
            href="{{ route('notifications.index') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">🔔</div>

            <h3 class="mt-5 text-xl font-black text-white">
                الإشعارات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                لديك
                <span class="font-black text-cyan-300">
                    {{ $statistics['unread_notifications'] ?? 0 }}
                </span>
                إشعارات غير مقروءة.
            </p>
        </a>

    </div>

    @include('dashboard.partials.consultations-table', [
        'consultations' => $latestConsultations,
        'title' => 'أحدث استشاراتي',
    ])

</div>
