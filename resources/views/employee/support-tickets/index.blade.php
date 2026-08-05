<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    تذاكر الدعم الفني
                </h1>

                <p class="mt-1 text-sm text-slate-400">
                    متابعة التذاكر المحولة من البوت والرد على العملاء
                </p>
            </div>
        </div>
    </x-slot>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="p-4 mb-5 border rounded-xl border-emerald-500/30 bg-emerald-500/10 text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-5 text-red-300 border rounded-xl border-red-500/30 bg-red-500/10">
                {{ session('error') }}
            </div>
        @endif

        <form
            method="GET"
            class="grid gap-4 p-5 mb-6 border bg-slate-900/70 border-white/10 rounded-2xl md:grid-cols-4"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="رقم التذكرة أو اسم العميل"
                class="w-full text-white border rounded-xl border-white/10 bg-slate-950/70"
            >

            <select
                name="status"
                class="w-full text-white border rounded-xl border-white/10 bg-slate-950/70"
            >
                <option value="">كل الحالات</option>
                <option value="open" @selected(request('status') === 'open')>
                    مفتوحة
                </option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>
                    قيد المعالجة
                </option>
                <option value="waiting_customer" @selected(request('status') === 'waiting_customer')>
                    بانتظار العميل
                </option>
                <option value="resolved" @selected(request('status') === 'resolved')>
                    تم الحل
                </option>
                <option value="closed" @selected(request('status') === 'closed')>
                    مغلقة
                </option>
            </select>

            <select
                name="priority"
                class="w-full text-white border rounded-xl border-white/10 bg-slate-950/70"
            >
                <option value="">كل الأولويات</option>
                <option value="urgent" @selected(request('priority') === 'urgent')>
                    عاجلة
                </option>
                <option value="high" @selected(request('priority') === 'high')>
                    مرتفعة
                </option>
                <option value="medium" @selected(request('priority') === 'medium')>
                    متوسطة
                </option>
                <option value="low" @selected(request('priority') === 'low')>
                    منخفضة
                </option>
            </select>

            <div class="flex gap-2">
                <button
                    class="flex-1 px-4 py-2 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-500"
                >
                    بحث
                </button>

                <a
                    href="{{ route('employee.support-tickets.index') }}"
                    class="px-4 py-2 border rounded-xl border-white/10 text-slate-300"
                >
                    مسح
                </a>
            </div>
        </form>

        <div class="overflow-hidden border bg-slate-900/70 border-white/10 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-slate-300 bg-white/5">
                        <tr>
                            <th class="px-4 py-4 text-right">التذكرة</th>
                            <th class="px-4 py-4 text-right">العميل</th>
                            <th class="px-4 py-4 text-right">الموضوع</th>
                            <th class="px-4 py-4 text-right">الأولوية</th>
                            <th class="px-4 py-4 text-right">الحالة</th>
                            <th class="px-4 py-4 text-right">الموظف</th>
                            <th class="px-4 py-4 text-right">آخر تحديث</th>
                            <th class="px-4 py-4"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/5">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-white/[0.03]">
                                <td class="px-4 py-4 font-bold text-blue-300">
                                    {{ $ticket->ticket_number }}
                                </td>

                                <td class="px-4 py-4 text-white">
                                    {{ $ticket->user?->name ?? 'غير معروف' }}
                                </td>

                                <td class="px-4 py-4 text-slate-300">
                                    {{ $ticket->subject }}
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                        $priorityClasses = [
                                            'urgent' => 'bg-red-500/15 text-red-300',
                                            'high' => 'bg-orange-500/15 text-orange-300',
                                            'medium' => 'bg-yellow-500/15 text-yellow-300',
                                            'low' => 'bg-slate-500/15 text-slate-300',
                                        ];

                                        $priorityLabels = [
                                            'urgent' => 'عاجلة',
                                            'high' => 'مرتفعة',
                                            'medium' => 'متوسطة',
                                            'low' => 'منخفضة',
                                        ];
                                    @endphp

                                    <span class="px-3 py-1 rounded-full {{ $priorityClasses[$ticket->priority] ?? '' }}">
                                        {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-slate-300">
                                    {{ $ticket->status }}
                                </td>

                                <td class="px-4 py-4 text-slate-300">
                                    {{ $ticket->assignedEmployee?->name ?? 'بانتظار موظف' }}
                                </td>

                                <td class="px-4 py-4 text-slate-400">
                                    {{ $ticket->last_message_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}
                                </td>

                                <td class="px-4 py-4">
                                    <a
                                        href="{{ route('employee.support-tickets.show', $ticket) }}"
                                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-500"
                                    >
                                        فتح
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="8"
                                    class="px-4 py-12 text-center text-slate-400"
                                >
                                    لا توجد تذاكر دعم.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="p-4 border-t border-white/10">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
