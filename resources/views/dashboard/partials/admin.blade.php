<div class="space-y-8" dir="rtl">

    {{-- بطاقات الإحصائيات --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <div class="p-6 glass-panel-strong rounded-[2rem]">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400">
                        جميع المستخدمين
                    </p>

                    <p class="mt-3 text-4xl font-black text-white">
                        {{ $statistics['users'] ?? 0 }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-blue-500/10"
                >
                    👥
                </div>

            </div>
        </div>

        <div class="p-6 glass-panel-strong rounded-[2rem]">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400">
                        جميع الاستشارات
                    </p>

                    <p class="mt-3 text-4xl font-black text-white">
                        {{ $statistics['consultations'] ?? 0 }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-cyan-500/10"
                >
                    📋
                </div>

            </div>
        </div>

        <div class="p-6 glass-panel-strong rounded-[2rem]">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400">
                        دفعات بانتظار المراجعة
                    </p>

                    <p class="mt-3 text-4xl font-black text-yellow-300">
                        {{ $statistics['pending_payments'] ?? 0 }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-yellow-500/10"
                >
                    💳
                </div>

            </div>
        </div>

        <div class="p-6 glass-panel-strong rounded-[2rem]">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-400">
                        إجمالي الإيرادات
                    </p>

                    <p class="mt-3 text-3xl font-black text-green-300">
                        {{ number_format($statistics['total_revenue'] ?? 0, 2) }}

                        <span class="text-base">
                            شيكل
                        </span>
                    </p>
                </div>

                <div
                    class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-green-500/10"
                >
                    💰
                </div>

            </div>
        </div>

    </div>

    {{-- روابط الإدارة السريعة --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <a
            href="{{ route('consultations.index') }}"
            class="p-6 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">
                📄
            </div>

            <h3 class="mt-4 text-lg font-black text-white">
                إدارة الاستشارات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                مراجعة جميع الاستشارات وتعيين المهندسين وتغيير الحالة.
            </p>
        </a>

        <a
            href="{{ route('payments.index') }}"
            class="p-6 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">
                💳
            </div>

            <h3 class="mt-4 text-lg font-black text-white">
                مراجعة الدفعات
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                فحص إيصالات الدفع وقبول أو رفض طلبات الدفع.
            </p>
        </a>

        <a
            href="{{ route('users.index') }}"
            class="p-6 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">
                👥
            </div>

            <h3 class="mt-4 text-lg font-black text-white">
                إدارة المستخدمين
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                إدارة العملاء والمهندسين والموظفين وحالات الحسابات.
            </p>
        </a>

        <a
            href="{{ route('admin.engineer-works.index') }}"
            class="p-6 transition glass-panel-strong rounded-[2rem] hover:-translate-y-1"
        >
            <div class="text-4xl">
                🏗️
            </div>

            <h3 class="mt-4 text-lg font-black text-white">
                أعمال المهندسين
            </h3>

            <p class="mt-2 text-sm leading-7 text-slate-400">
                مراجعة مشاريع المهندسين وقبولها أو رفضها.
            </p>
        </a>

    </div>

    {{-- توزيع الاستشارات والمستخدمين --}}
    <div class="grid gap-6 lg:grid-cols-3">

        <section
            class="p-6 lg:col-span-2 glass-panel-strong rounded-[2rem]"
        >

            <div class="flex items-center justify-between mb-6">

                <div>
                    <h3 class="text-xl font-black text-white">
                        حالة الاستشارات
                    </h3>

                    <p class="mt-1 text-sm text-slate-400">
                        توزيع الاستشارات حسب الحالة الحالية
                    </p>
                </div>

                <span class="text-3xl">
                    📊
                </span>

            </div>

            @php
                $statusItems = [
                    [
                        'label' => 'بانتظار الدفع',
                        'value' => $consultationsByStatus['waiting_payment'] ?? 0,
                        'bar' => 'bg-orange-500',
                    ],
                    [
                        'label' => 'قيد الانتظار',
                        'value' => $consultationsByStatus['pending'] ?? 0,
                        'bar' => 'bg-yellow-500',
                    ],
                    [
                        'label' => 'قيد التنفيذ',
                        'value' => $consultationsByStatus['in_progress'] ?? 0,
                        'bar' => 'bg-blue-500',
                    ],
                    [
                        'label' => 'مكتملة',
                        'value' => $consultationsByStatus['completed'] ?? 0,
                        'bar' => 'bg-green-500',
                    ],
                    [
                        'label' => 'ملغاة',
                        'value' => $consultationsByStatus['cancelled'] ?? 0,
                        'bar' => 'bg-red-500',
                    ],
                ];

                $total = collect($statusItems)->sum('value');
                $safeTotal = max($total, 1);
            @endphp

            <div class="space-y-5">

                @foreach ($statusItems as $item)

                    @php
                        $percentage = round(
                            ($item['value'] / $safeTotal) * 100
                        );
                    @endphp

                    <div>

                        <div
                            class="flex items-center justify-between mb-2 text-sm"
                        >
                            <span class="font-bold text-slate-300">
                                {{ $item['label'] }}
                            </span>

                            <span class="text-slate-400">
                                {{ $item['value'] }}
                                ({{ $percentage }}%)
                            </span>
                        </div>

                        <div
                            class="w-full h-3 overflow-hidden rounded-full bg-slate-800"
                        >
                            <div
                                class="h-full rounded-full {{ $item['bar'] }}"
                                style="width: {{ $percentage }}%"
                            ></div>
                        </div>

                    </div>

                @endforeach

            </div>

        </section>

        <section class="p-6 glass-panel-strong rounded-[2rem]">

            <h3 class="text-xl font-black text-white">
                المستخدمون
            </h3>

            <p class="mt-1 text-sm text-slate-400">
                توزيع الحسابات حسب الدور
            </p>

            <div class="mt-6 space-y-4">

                <div
                    class="flex items-center justify-between p-4 rounded-2xl bg-white/5"
                >
                    <span class="text-slate-300">
                        العملاء
                    </span>

                    <span class="text-lg font-black text-cyan-300">
                        {{ $statistics['customers'] ?? 0 }}
                    </span>
                </div>

                <div
                    class="flex items-center justify-between p-4 rounded-2xl bg-white/5"
                >
                    <span class="text-slate-300">
                        المهندسون
                    </span>

                    <span class="text-lg font-black text-blue-300">
                        {{ $statistics['engineers'] ?? 0 }}
                    </span>
                </div>

                <div
                    class="flex items-center justify-between p-4 rounded-2xl bg-white/5"
                >
                    <span class="text-slate-300">
                        الموظفون
                    </span>

                    <span class="text-lg font-black text-purple-300">
                        {{ $statistics['employees'] ?? 0 }}
                    </span>
                </div>

            </div>

            <a
                href="{{ route('users.index') }}"
                class="block w-full px-5 py-3 mt-6 font-bold text-center text-white transition rounded-2xl bg-cyan-600 hover:bg-cyan-500"
            >
                فتح إدارة المستخدمين
            </a>

        </section>

    </div>

    {{-- أحدث الاستشارات --}}
    <section class="overflow-hidden glass-panel-strong rounded-[2rem]">

        <div
            class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-white/10"
        >
            <div>
                <h3 class="text-xl font-black text-white">
                    أحدث الاستشارات
                </h3>

                <p class="mt-1 text-sm text-slate-400">
                    آخر الاستشارات المضافة إلى النظام
                </p>
            </div>

            <a
                href="{{ route('consultations.index') }}"
                class="text-sm font-bold text-cyan-300 hover:text-cyan-200"
            >
                عرض جميع الاستشارات
            </a>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-white/5 text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-right">
                            رقم الاستشارة
                        </th>

                        <th class="px-6 py-4 text-right">
                            العميل
                        </th>

                        <th class="px-6 py-4 text-right">
                            النوع
                        </th>

                        <th class="px-6 py-4 text-right">
                            المهندس
                        </th>

                        <th class="px-6 py-4 text-right">
                            الحالة
                        </th>

                        <th class="px-6 py-4 text-right">
                            التاريخ
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">

                    @forelse ($latestConsultations ?? [] as $consultation)

                        <tr class="transition hover:bg-white/5">

                            <td class="px-6 py-4 font-bold text-white">
                                {{ $consultation->consultation_number }}
                            </td>

                            <td class="px-6 py-4 text-slate-300">
                                {{ $consultation->customer?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-slate-300">
                                {{ $consultation->consultationType?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-slate-300">
                                {{ $consultation->engineer?->name ?? 'غير معين' }}
                            </td>

                            <td class="px-6 py-4">

                                @include(
                                    'dashboard.partials.status-badge',
                                    ['status' => $consultation->status]
                                )

                            </td>

                            <td class="px-6 py-4 text-slate-400">
                                {{ $consultation->created_at?->format('Y-m-d') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                لا توجد استشارات مسجلة حتى الآن.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>
