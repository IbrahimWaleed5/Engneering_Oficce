<div class="space-y-8" dir="rtl">

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        @php
            $cards = [
                [
                    'title' => 'جميع الاستشارات',
                    'value' => $statistics['consultations'] ?? 0,
                    'icon' => '📋',
                ],
                [
                    'title' => 'بانتظار التعيين',
                    'value' => $statistics['pending_consultations'] ?? 0,
                    'icon' => '⏳',
                ],
                [
                    'title' => 'دفعات للمراجعة',
                    'value' => $statistics['pending_payments'] ?? 0,
                    'icon' => '💳',
                ],
                [
                    'title' => 'المهندسون النشطون',
                    'value' => $statistics['engineers'] ?? 0,
                    'icon' => '👷',
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
                        class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-purple-500/10"
                    >
                        {{ $card['icon'] }}
                    </div>

                </div>

            </div>
        @endforeach

    </div>

    <div class="grid gap-6 md:grid-cols-3">

        <a
            href="{{ route('consultations.index') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">📄</div>

            <h3 class="mt-5 text-xl font-black text-white">
                إدارة الاستشارات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                متابعة الطلبات وتحديث حالاتها وتعيين المهندس.
            </p>
        </a>

        <a
            href="{{ route('payments.index') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">💳</div>

            <h3 class="mt-5 text-xl font-black text-white">
                مراجعة الدفعات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                مراجعة إيصالات العملاء ومتابعة حالة الدفع.
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
                متابعة آخر أحداث النظام والطلبات الجديدة.
            </p>
        </a>

    </div>

    @include('dashboard.partials.consultations-table', [
        'consultations' => $latestConsultations,
        'title' => 'أحدث الاستشارات',
    ])

</div>
