<x-app-layout>
    <div class="min-h-screen py-10 bg-slate-950" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 mb-8 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-black text-white">إدارة الدعم الفني</h1>
                    <p class="mt-2 text-slate-400">عرض جميع تذاكر الدعم الفني.</p>
                </div>

                <a
                    href="{{ route('admin.support.settings') }}"
                    class="px-6 py-3 font-bold text-center text-white bg-blue-600 rounded-xl"
                >
                    إعداد موظف الدعم
                </a>
            </div>

            <form method="GET" class="flex gap-3 mb-6">
                <select
                    name="status"
                    class="px-4 py-3 text-white border rounded-xl border-slate-700 bg-slate-900"
                >
                    <option value="">كل الحالات</option>
                    @foreach ([
                        'open' => 'مفتوحة',
                        'in_progress' => 'قيد المعالجة',
                        'resolved' => 'محلولة',
                        'closed' => 'مغلقة',
                    ] as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(request('status') === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button class="px-5 py-3 font-bold text-white rounded-xl bg-slate-800">
                    تطبيق
                </button>
            </form>

            <div class="overflow-hidden border rounded-2xl border-white/10 bg-slate-900">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[950px]">
                        <thead class="bg-white/5 text-slate-300">
                            <tr>
                                <th class="px-5 py-4 text-right">الرقم</th>
                                <th class="px-5 py-4 text-right">الموضوع</th>
                                <th class="px-5 py-4 text-right">المستخدم</th>
                                <th class="px-5 py-4 text-right">موظف الدعم</th>
                                <th class="px-5 py-4 text-right">الأولوية</th>
                                <th class="px-5 py-4 text-right">الحالة</th>
                                <th class="px-5 py-4 text-right">الإجراء</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-5 py-4 text-blue-300">{{ $ticket->ticket_number }}</td>
                                    <td class="px-5 py-4 text-white">{{ $ticket->subject }}</td>
                                    <td class="px-5 py-4 text-slate-300">{{ $ticket->user->name }}</td>
                                    <td class="px-5 py-4 text-slate-300">{{ $ticket->assignedEmployee?->name ?? 'غير معيّن' }}</td>
                                    <td class="px-5 py-4 text-slate-300">{{ $ticket->priority }}</td>
                                    <td class="px-5 py-4 text-slate-300">{{ $ticket->status }}</td>
                                    <td class="px-5 py-4">
                                        <a
                                            href="{{ route('support.show', $ticket) }}"
                                            class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg"
                                        >
                                            فتح
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 text-center py-14 text-slate-400">
                                        لا توجد تذاكر.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($tickets->hasPages())
                <div class="mt-8">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
