<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">
            الإشعارات
        </h2>
    </x-slot>

    <div class="py-10" dir="rtl">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            <div class="p-6 border shadow-xl rounded-2xl border-slate-700 bg-slate-900/80">

                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-white">
                            الإشعارات
                        </h1>

                        <p class="mt-2 text-slate-400">
                            تابع آخر تحديثات الاستشارات والدفعات والملفات
                        </p>
                    </div>

                    @if(auth()->user()->unreadNotifications()->count() > 0)
                        <form
                            method="POST"
                            action="{{ route('notifications.read-all') }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-bold text-white transition bg-blue-600 rounded-xl hover:bg-blue-700"
                            >
                                تعليم الكل كمقروء
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid gap-4 mb-6 sm:grid-cols-3">

                    <div class="p-4 border rounded-xl border-slate-700 bg-slate-800">
                        <p class="text-sm text-slate-400">
                            جميع الإشعارات
                        </p>

                        <p class="mt-2 text-2xl font-bold text-white">
                            {{ $notifications->count() }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-xl border-amber-500/30 bg-amber-500/10">
                        <p class="text-sm text-amber-300">
                            غير مقروءة
                        </p>

                        <p class="mt-2 text-2xl font-bold text-white">
                            {{ auth()->user()->unreadNotifications()->count() }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-xl border-emerald-500/30 bg-emerald-500/10">
                        <p class="text-sm text-emerald-300">
                            مقروءة
                        </p>

                        <p class="mt-2 text-2xl font-bold text-white">
                            {{
                                $notifications->count()
                                - auth()->user()->unreadNotifications()->count()
                            }}
                        </p>
                    </div>

                </div>

                <div class="space-y-4">

                    @forelse($notifications as $notification)

                        <div
                            class="rounded-xl border p-5 transition
                            {{
                                is_null($notification->read_at)
                                    ? 'border-blue-500/40 bg-blue-500/10'
                                    : 'border-slate-700 bg-slate-800/70'
                            }}"
                        >
                            <div class="flex flex-col justify-between gap-4 sm:flex-row">

                                <div class="flex-1">

                                    <div class="flex items-center gap-3">

                                        <span class="text-2xl">
                                            {{
                                                is_null($notification->read_at)
                                                    ? '🔔'
                                                    : '📩'
                                            }}
                                        </span>

                                        <h3 class="text-lg font-bold text-white">
                                            {{
                                                $notification->data['title']
                                                ?? 'إشعار جديد'
                                            }}
                                        </h3>

                                        @if(is_null($notification->read_at))
                                            <span class="px-3 py-1 text-xs font-bold text-white bg-blue-600 rounded-full">
                                                جديد
                                            </span>
                                        @endif

                                    </div>

                                    <p class="mt-3 text-slate-300">
                                        {{
                                            $notification->data['message']
                                            ?? 'لديك إشعار جديد'
                                        }}
                                    </p>

                                    <p class="mt-3 text-sm text-slate-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>

                                </div>

                                <div class="flex items-center gap-2">

                                    @if(!empty($notification->data['url']))

                                        <form
                                            method="POST"
                                            action="{{ route('notifications.read', $notification->id) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                            >
                                                عرض
                                            </button>
                                        </form>

                                    @elseif(is_null($notification->read_at))

                                        <form
                                            method="POST"
                                            action="{{ route('notifications.read', $notification->id) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="px-4 py-2 text-sm text-white rounded-lg bg-slate-700 hover:bg-slate-600"
                                            >
                                                تعليم كمقروء
                                            </button>
                                        </form>

                                    @endif

                                    <form
                                        method="POST"
                                        action="{{ route('notifications.destroy', $notification->id) }}"
                                        onsubmit="return confirm('هل تريد حذف هذا الإشعار؟')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700"
                                        >
                                            حذف
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>

                    @empty

                        <div class="p-10 text-center border rounded-xl border-slate-700 bg-slate-800">

                            <div class="mb-4 text-5xl">
                                🔕
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                لا توجد إشعارات
                            </h3>

                            <p class="mt-2 text-slate-400">
                                ستظهر الإشعارات الجديدة هنا
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>
        </div>
    </div>

</x-app-layout>
