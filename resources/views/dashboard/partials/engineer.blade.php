<div class="space-y-8" dir="rtl">

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        @php
            $cards = [
                [
                    'title' => 'الاستشارات المسندة',
                    'value' => $statistics['all'] ?? 0,
                    'icon' => '📋',
                    'text' => 'text-white',
                ],
                [
                    'title' => 'قيد التنفيذ',
                    'value' => $statistics['in_progress'] ?? 0,
                    'icon' => '🛠️',
                    'text' => 'text-blue-300',
                ],
                [
                    'title' => 'الاستشارات المكتملة',
                    'value' => $statistics['completed'] ?? 0,
                    'icon' => '✅',
                    'text' => 'text-green-300',
                ],
                [
                    'title' => 'أعمالي المعتمدة',
                    'value' => $statistics['approved_works'] ?? 0,
                    'icon' => '🏗️',
                    'text' => 'text-cyan-300',
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

                        <p class="mt-3 text-4xl font-black {{ $card['text'] }}">
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
            href="{{ route('engineer.consultations') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">📐</div>

            <h3 class="mt-5 text-xl font-black text-white">
                الاستشارات المسندة
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                متابعة الطلبات والتواصل مع العملاء ورفع النتائج النهائية.
            </p>
        </a>

        <a
            href="{{ route('engineer.works.mine') }}"
            class="p-7 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">🏢</div>

            <h3 class="mt-5 text-xl font-black text-white">
                معرض أعمالي
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                إضافة الأعمال الهندسية ومتابعة قبولها من الإدارة.
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
        'title' => 'أحدث الاستشارات المسندة إليّ',
    ])

</div>
