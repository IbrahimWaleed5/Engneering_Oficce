<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

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

            <div class="mb-8">
                <p class="text-sm font-bold text-cyan-300">
                    إدارة المكاتب
                </p>

                <h1 class="mt-2 text-3xl font-black text-white">
                    طلبات انضمام المكاتب الهندسية
                </h1>

                <p class="mt-3 leading-7 text-slate-400">
                    راجع طلبات المكاتب الجديدة، ثم وافق عليها أو ارفضها.
                </p>
            </div>

            <div class="grid gap-4 mb-8 sm:grid-cols-3">
                <div class="p-5 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                    <p class="text-sm text-yellow-100">
                        قيد المراجعة
                    </p>

                    <p class="mt-2 text-3xl font-black text-yellow-300">
                        {{ $statistics['pending'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-green-500/20 bg-green-500/10">
                    <p class="text-sm text-green-100">
                        الطلبات المقبولة
                    </p>

                    <p class="mt-2 text-3xl font-black text-green-300">
                        {{ $statistics['approved'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <p class="text-sm text-red-100">
                        الطلبات المرفوضة
                    </p>

                    <p class="mt-2 text-3xl font-black text-red-300">
                        {{ $statistics['rejected'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="overflow-hidden border rounded-3xl border-white/10 bg-slate-900/70">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[950px] text-sm">
                        <thead class="text-slate-300 bg-white/5">
                            <tr>
                                <th class="p-4 text-right">
                                    رقم الطلب
                                </th>

                                <th class="p-4 text-right">
                                    اسم المكتب
                                </th>

                                <th class="p-4 text-right">
                                    صاحب الطلب
                                </th>

                                <th class="p-4 text-right">
                                    المدينة
                                </th>

                                <th class="p-4 text-right">
                                    الحالة
                                </th>

                                <th class="p-4 text-right">
                                    تاريخ الطلب
                                </th>

                                <th class="p-4 text-right">
                                    الإجراء
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/5">
                            @forelse ($applications as $application)
                                @php
                                    $statusData = match ($application->status) {
                                        'approved' => [
                                            'label' => 'مقبول',
                                            'class' => 'text-green-200 bg-green-500/10 border-green-500/20',
                                        ],

                                        'rejected' => [
                                            'label' => 'مرفوض',
                                            'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                        ],

                                        'cancelled' => [
                                            'label' => 'ملغي',
                                            'class' => 'text-slate-300 bg-white/5 border-white/10',
                                        ],

                                        default => [
                                            'label' => 'قيد المراجعة',
                                            'class' => 'text-yellow-200 bg-yellow-500/10 border-yellow-500/20',
                                        ],
                                    };
                                @endphp

                                <tr class="transition hover:bg-white/[0.03]">
                                    <td class="p-4 font-bold text-slate-300">
                                        #{{ $application->id }}
                                    </td>

                                    <td class="p-4">
                                        <div class="font-black text-white">
                                            {{ $application->office_name }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            {{ $application->email }}
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <div class="font-bold text-white">
                                            {{ $application->applicant?->name ?? 'غير معروف' }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            {{ $application->applicant?->email }}
                                        </div>
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $application->city ?: 'غير محددة' }}
                                    </td>

                                    <td class="p-4">
                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-black border rounded-full {{ $statusData['class'] }}"
                                        >
                                            {{ $statusData['label'] }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $application->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="p-4">
                                        <a
                                            href="{{ route(
                                                'admin.office-applications.show',
                                                $application
                                            ) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 font-bold text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                                        >
                                            عرض الطلب
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="p-12 text-center text-slate-400"
                                    >
                                        لا توجد طلبات مكاتب هندسية حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($applications->hasPages())
                <div class="mt-8">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
