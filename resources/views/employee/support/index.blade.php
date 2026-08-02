<x-app-layout>
    <div class="min-h-screen py-10 bg-slate-950" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-5 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-5 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col justify-between gap-4 mb-8 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-3xl font-black text-white">
                        تذاكر الدعم الفني
                    </h1>

                    <p class="mt-2 text-slate-400">
                        جميع التذاكر المسندة إليك من المدير
                    </p>
                </div>

                <a
                    href="{{ route('dashboard') }}"
                    class="px-6 py-3 font-bold text-center text-white border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                >
                    العودة إلى لوحة التحكم
                </a>
            </div>

            <div class="grid gap-5">

                @forelse ($tickets as $ticket)

                    <div class="p-5 border rounded-2xl border-white/10 bg-slate-900">

                        <div class="flex flex-col justify-between gap-5 sm:flex-row">

                            <div>
                                <div class="flex flex-wrap items-center gap-3">

                                    <span class="px-3 py-1 text-sm font-bold text-blue-300 rounded-lg bg-blue-500/10">
                                        {{ $ticket->ticket_number }}
                                    </span>

                                    @if ($ticket->status === 'open')
                                        <span class="px-3 py-1 text-xs font-bold text-blue-300 rounded-full bg-blue-500/10">
                                            مفتوحة
                                        </span>
                                    @elseif ($ticket->status === 'in_progress')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-500/10 text-amber-300">
                                            قيد المعالجة
                                        </span>
                                    @elseif ($ticket->status === 'resolved')
                                        <span class="px-3 py-1 text-xs font-bold text-green-300 rounded-full bg-green-500/10">
                                            محلولة
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-500/10 text-slate-300">
                                            مغلقة
                                        </span>
                                    @endif

                                    @if ($ticket->priority === 'urgent')
                                        <span class="px-3 py-1 text-xs font-bold text-red-300 rounded-full bg-red-500/10">
                                            عاجلة
                                        </span>
                                    @elseif ($ticket->priority === 'high')
                                        <span class="px-3 py-1 text-xs font-bold text-orange-300 rounded-full bg-orange-500/10">
                                            مرتفعة
                                        </span>
                                    @elseif ($ticket->priority === 'medium')
                                        <span class="px-3 py-1 text-xs font-bold text-yellow-300 rounded-full bg-yellow-500/10">
                                            متوسطة
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-500/10 text-slate-300">
                                            منخفضة
                                        </span>
                                    @endif

                                </div>

                                <h2 class="mt-4 text-xl font-bold text-white">
                                    {{ $ticket->subject }}
                                </h2>

                                <p class="mt-2 text-sm text-slate-400">
                                    صاحب التذكرة:
                                    <span class="font-bold text-white">
                                        {{ $ticket->user?->name ?? 'مستخدم غير معروف' }}
                                    </span>
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    البريد:
                                    {{ $ticket->user?->email ?? '—' }}
                                </p>

                                <p class="mt-3 text-xs text-slate-500">
                                    آخر تحديث:
                                    {{ optional($ticket->last_message_at)->diffForHumans() ?? $ticket->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex items-center">
                                <a
                                    href="{{ route('support.show', $ticket) }}"
                                    class="px-6 py-3 font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                                >
                                    فتح التذكرة
                                </a>
                            </div>

                        </div>

                    </div>

                @empty

                    <div class="p-12 text-center border rounded-2xl border-white/10 bg-slate-900">

                        <div class="mb-4 text-5xl">
                            🎧
                        </div>

                        <h3 class="text-xl font-bold text-white">
                            لا توجد تذاكر مسندة إليك
                        </h3>

                        <p class="mt-2 text-slate-400">
                            ستظهر هنا التذاكر التي يعيّنها المدير لك
                        </p>

                    </div>

                @endforelse

            </div>

            @if ($tickets->hasPages())
                <div class="mt-8">
                    {{ $tickets->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
