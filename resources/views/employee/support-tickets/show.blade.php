<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    {{ $ticket->ticket_number }}
                </h1>

                <p class="mt-1 text-sm text-slate-400">
                    {{ $ticket->subject }}
                </p>
            </div>

            <a
                href="{{ route('employee.support-tickets.index') }}"
                class="px-4 py-2 border rounded-xl border-white/10 text-slate-300"
            >
                العودة للتذاكر
            </a>
        </div>
    </x-slot>

    <div class="grid gap-6 px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8 lg:grid-cols-3">
        <aside class="p-5 border bg-slate-900/70 border-white/10 rounded-2xl">
            <h2 class="mb-5 text-lg font-bold text-white">
                بيانات التذكرة
            </h2>

            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-slate-500">العميل</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->user?->name }}
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">البريد</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->user?->email }}
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">الهاتف</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->user?->phone ?? 'غير متوفر' }}
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">الحالة</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->status }}
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">الأولوية</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->priority }}
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">الموظف</p>
                    <p class="mt-1 text-white">
                        {{ $ticket->assignedEmployee?->name ?? 'لم يتم التعيين' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @if(!$ticket->assigned_employee_id)
                    <form
                        method="POST"
                        action="{{ route('employee.support-tickets.claim', $ticket) }}"
                    >
                        @csrf

                        <button
                            class="w-full px-4 py-3 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-500"
                        >
                            استلام التذكرة
                        </button>
                    </form>
                @endif

                @if(!in_array($ticket->status, ['resolved', 'closed'], true))
                    <form
                        method="POST"
                        action="{{ route('employee.support-tickets.resolve', $ticket) }}"
                    >
                        @csrf

                        <button
                            class="w-full px-4 py-3 font-bold rounded-xl bg-emerald-500/15 text-emerald-300"
                        >
                            تحديد كمحلولة
                        </button>
                    </form>
                @endif

                @if($ticket->status !== 'closed')
                    <form
                        method="POST"
                        action="{{ route('employee.support-tickets.close', $ticket) }}"
                    >
                        @csrf

                        <button
                            class="w-full px-4 py-3 font-bold text-red-300 rounded-xl bg-red-500/15"
                        >
                            إغلاق التذكرة
                        </button>
                    </form>
                @endif
            </div>
        </aside>

        <section class="flex flex-col overflow-hidden border bg-slate-900/70 border-white/10 rounded-2xl lg:col-span-2">
            <div
                id="ticketMessages"
                class="flex-1 p-5 space-y-4 overflow-y-auto"
                style="min-height: 500px; max-height: 620px;"
            >
                @foreach($ticket->messages as $message)
                    @continue(
                        $message->is_internal &&
                        !in_array(auth()->user()->role, ['employee', 'admin'], true)
                    )

                    <div class="flex {{ $message->sender_type === 'customer' ? 'justify-start' : 'justify-end' }}">
                        <div
                            class="max-w-[85%] rounded-2xl px-4 py-3
                            {{
                                $message->is_internal
                                    ? 'border border-yellow-500/30 bg-yellow-500/10 text-yellow-200'
                                    : (
                                        $message->sender_type === 'customer'
                                            ? 'bg-blue-600 text-white'
                                            : (
                                                $message->sender_type === 'system'
                                                    ? 'border border-white/10 bg-white/5 text-slate-300'
                                                    : 'bg-emerald-500/15 text-emerald-100'
                                            )
                                    )
                            }}"
                        >
                            <div class="mb-1 text-xs opacity-70">
                                @if($message->is_internal)
                                    ملاحظة داخلية
                                @elseif($message->sender_type === 'bot')
                                    البوت
                                @elseif($message->sender_type === 'customer')
                                    {{ $ticket->user?->name }}
                                @elseif($message->sender_type === 'system')
                                    النظام
                                @else
                                    {{ $message->sender?->name ?? 'موظف الدعم' }}
                                @endif
                            </div>

                            <p class="leading-7 whitespace-pre-wrap">
                                {{ $message->message }}
                            </p>

                            <div class="mt-2 text-xs opacity-60">
                                {{ $message->created_at->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(!in_array($ticket->status, ['resolved', 'closed'], true))
                <form
                    method="POST"
                    action="{{ route('employee.support-tickets.reply', $ticket) }}"
                    class="p-5 border-t border-white/10"
                >
                    @csrf

                    <textarea
                        name="message"
                        rows="4"
                        required
                        maxlength="5000"
                        placeholder="اكتب ردك للعميل..."
                        class="w-full text-white border rounded-xl border-white/10 bg-slate-950/70"
                    >{{ old('message') }}</textarea>

                    @error('message')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <label class="flex items-center gap-2 mt-3 text-sm text-slate-300">
                        <input
                            type="checkbox"
                            name="is_internal"
                            value="1"
                            class="rounded border-white/20 bg-slate-950"
                        >

                        ملاحظة داخلية لا تظهر للعميل
                    </label>

                    <button
                        class="px-6 py-3 mt-4 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-500"
                    >
                        إرسال الرد
                    </button>
                </form>
            @else
                <div class="p-5 text-center border-t border-white/10 text-slate-400">
                    هذه التذكرة مغلقة ولا يمكن إضافة رد جديد.
                </div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const messages = document.getElementById('ticketMessages');

                if (messages) {
                    messages.scrollTop = messages.scrollHeight;
                }
            });
        </script>
    @endpush
</x-app-layout>
