<x-app-layout>
    <div class="min-h-screen py-8 bg-slate-950" dir="rtl">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-5 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            <div class="border shadow-2xl rounded-3xl border-white/10 bg-slate-900">
                <header class="p-6 border-b border-white/10">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-bold text-blue-300">
                                {{ $supportTicket->ticket_number }}
                            </p>

                            <h1 class="mt-2 text-2xl font-black text-white">
                                {{ $supportTicket->subject }}
                            </h1>

                            <p class="mt-2 text-sm text-slate-400">
                                العميل: {{ $supportTicket->user->name }}
                                · موظف الدعم:
                                {{ $supportTicket->assignedEmployee?->name ?? 'غير معيّن' }}
                            </p>
                        </div>

                        @if (
                            auth()->user()->role === 'admin'
                            || auth()->id() === $supportTicket->assigned_employee_id
                        )
                            <form
                                method="POST"
                                action="{{ route('support.status.update', $supportTicket) }}"
                                class="flex gap-2"
                            >
                                @csrf
                                @method('PATCH')

                                <select
                                    name="status"
                                    class="px-4 py-2 text-white border rounded-xl border-slate-700 bg-slate-950"
                                >
                                    @foreach ([
                                        'open' => 'مفتوحة',
                                        'in_progress' => 'قيد المعالجة',
                                        'resolved' => 'محلولة',
                                        'closed' => 'مغلقة',
                                    ] as $value => $label)
                                        <option
                                            value="{{ $value }}"
                                            @selected($supportTicket->status === $value)
                                        >
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="px-4 py-2 font-bold text-white bg-blue-600 rounded-xl">
                                    تحديث
                                </button>
                            </form>
                        @endif
                    </div>
                </header>

                <div class="max-h-[580px] space-y-4 overflow-y-auto p-6">
                    @foreach ($supportTicket->messages as $message)
                        @php
                            $isMine = $message->sender_id === auth()->id();
                        @endphp

                        <div class="flex {{ $isMine ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-[85%] rounded-2xl p-4 {{
                                $isMine
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-slate-800 text-slate-100'
                            }}">
                                <p class="mb-2 text-xs font-bold opacity-80">
                                    {{ $message->sender->name }}
                                </p>

                                @if ($message->message)
                                    <p class="leading-7 whitespace-pre-wrap">
                                        {{ $message->message }}
                                    </p>
                                @endif

                                @if ($message->hasAttachment())
                                    <a
                                        href="{{ route('support.messages.attachment', $message) }}"
                                        class="block px-4 py-3 mt-3 font-bold underline rounded-xl bg-black/20"
                                    >
                                        {{ $message->attachment_name ?? 'تحميل المرفق' }}
                                    </a>
                                @endif

                                <p class="mt-3 text-[11px] opacity-60">
                                    {{ $message->created_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($supportTicket->status !== 'closed')
                    <form
                        method="POST"
                        action="{{ route('support.messages.store', $supportTicket) }}"
                        enctype="multipart/form-data"
                        class="p-5 border-t border-white/10"
                    >
                        @csrf

                        <textarea
                            name="message"
                            rows="3"
                            placeholder="اكتب رسالتك..."
                            class="w-full px-4 py-3 text-white border resize-none rounded-xl border-slate-700 bg-slate-950"
                        ></textarea>

                        <div class="flex flex-col gap-3 mt-3 sm:flex-row">
                            <input
                                name="attachment"
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                                class="flex-1 px-4 py-3 border rounded-xl border-slate-700 bg-slate-950 text-slate-300"
                            >

                            <button
                                type="submit"
                                class="px-8 py-3 font-black text-white bg-blue-600 rounded-xl"
                            >
                                إرسال
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-5 text-center border-t border-white/10 text-slate-400">
                        هذه التذكرة مغلقة.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
