<x-app-layout>
    <div class="min-h-screen px-4 py-8 bg-slate-950 text-slate-100 sm:px-6 lg:px-8" dir="rtl">
        <style>
            .appeals-glass {
                background: rgba(15, 23, 42, .74);
                border: 1px solid rgba(51, 65, 85, .72);
                box-shadow: 0 4px 30px rgba(0, 0, 0, .14);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
            }

            .appeals-scroll::-webkit-scrollbar {
                width: 7px;
                height: 7px;
            }

            .appeals-scroll::-webkit-scrollbar-track {
                background: #020617;
            }

            .appeals-scroll::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 999px;
            }
        </style>

        <div class="mx-auto max-w-[1500px]">
            <div class="flex flex-col gap-4 mb-8 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-black text-white md:text-4xl">
                        طلبات الطعن على تعليق الحسابات
                    </h1>

                    <p class="mt-3 text-sm leading-7 text-slate-400">
                        مراجعة رسائل المستخدمين واتخاذ القرار المناسب بشأن إعادة التفعيل أو تثبيت التعليق.
                    </p>
                </div>

                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-black transition border rounded-xl border-slate-700 bg-slate-900 text-slate-200 hover:border-blue-400 hover:text-white"
                >
                    العودة إلى لوحة التحكم
                </a>
            </div>

            @if (session('success'))
                <div class="p-4 mb-6 border rounded-xl border-emerald-500/25 bg-emerald-500/10 text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/25 bg-red-500/10">
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-5">
                <div class="p-6 text-center appeals-glass rounded-2xl">
                    <div class="text-4xl font-black text-white">{{ number_format($statistics['all']) }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-400">جميع الطعون</div>
                </div>

                <div class="p-6 text-center appeals-glass rounded-2xl">
                    <div class="text-4xl font-black text-amber-300">{{ number_format($statistics['pending']) }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-400">بانتظار المراجعة</div>
                </div>

                <div class="p-6 text-center appeals-glass rounded-2xl">
                    <div class="text-4xl font-black text-blue-300">{{ number_format($statistics['under_review']) }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-400">تحت المراجعة</div>
                </div>

                <div class="p-6 text-center appeals-glass rounded-2xl">
                    <div class="text-4xl font-black text-emerald-300">{{ number_format($statistics['approved']) }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-400">مقبولة</div>
                </div>

                <div class="p-6 text-center appeals-glass rounded-2xl">
                    <div class="text-4xl font-black text-red-300">{{ number_format($statistics['rejected']) }}</div>
                    <div class="mt-2 text-sm font-bold text-slate-400">مرفوضة</div>
                </div>
            </section>

            <section class="p-6 mb-8 appeals-glass rounded-2xl">
                <form method="GET" action="{{ route('admin.moderation-appeals.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <div class="md:col-span-7">
                        <label for="search" class="block mb-2 text-sm font-bold text-slate-300">
                            البحث
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="اسم المستخدم أو البريد أو الهاتف أو نص الطعن..."
                            class="w-full px-4 py-3 text-white border outline-none rounded-xl border-slate-700 bg-slate-900 placeholder:text-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>

                    <div class="md:col-span-3">
                        <label for="status" class="block mb-2 text-sm font-bold text-slate-300">
                            حالة الطعن
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full px-4 py-3 text-white border outline-none rounded-xl border-slate-700 bg-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                            <option value="">الكل</option>
                            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>بانتظار المراجعة</option>
                            <option value="under_review" @selected(($filters['status'] ?? '') === 'under_review')>تحت المراجعة</option>
                            <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>مقبول</option>
                            <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>مرفوض</option>
                            <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>ملغي</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3 md:col-span-2">
                        <button
                            type="submit"
                            class="flex-1 px-4 py-3 text-sm font-black text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                        >
                            تطبيق
                        </button>

                        <a
                            href="{{ route('admin.moderation-appeals.index') }}"
                            class="px-4 py-3 text-sm font-black transition border rounded-xl border-slate-700 bg-slate-900 text-slate-300 hover:bg-slate-800"
                        >
                            مسح
                        </a>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden appeals-glass rounded-2xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800">
                    <div>
                        <h2 class="text-xl font-black text-white">سجل طلبات الطعن</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ number_format($appeals->total()) }} نتيجة</p>
                    </div>
                </div>

                @if ($appeals->isEmpty())
                    <div class="flex min-h-[360px] flex-col items-center justify-center px-6 py-16 text-center">
                        <div class="flex items-center justify-center w-20 h-20 mb-5 text-4xl border rounded-full border-slate-700 bg-slate-900">
                            ⚖️
                        </div>

                        <h3 class="text-xl font-black text-white">لا توجد طلبات طعن</h3>
                        <p class="mt-2 text-sm text-slate-400">لا توجد نتائج مطابقة للفلاتر الحالية.</p>
                    </div>
                @else
                    <div class="overflow-x-auto appeals-scroll">
                        <table class="w-full min-w-[1050px] text-right">
                            <thead class="border-b border-slate-800 bg-slate-900/80">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400">المستخدم</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400">رسالة الطعن</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400">التحذير المرتبط</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400">الحالة</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-400">التاريخ</th>
                                    <th class="px-6 py-4 text-xs font-black text-center text-slate-400">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-800">
                                @foreach ($appeals as $appeal)
                                    @php
                                        $statusLabels = [
                                            'pending' => 'بانتظار المراجعة',
                                            'under_review' => 'تحت المراجعة',
                                            'approved' => 'مقبول',
                                            'rejected' => 'مرفوض',
                                            'cancelled' => 'ملغي',
                                        ];

                                        $statusClasses = [
                                            'pending' => 'border-amber-400/25 bg-amber-400/10 text-amber-300',
                                            'under_review' => 'border-blue-400/25 bg-blue-400/10 text-blue-300',
                                            'approved' => 'border-emerald-400/25 bg-emerald-400/10 text-emerald-300',
                                            'rejected' => 'border-red-400/25 bg-red-400/10 text-red-300',
                                            'cancelled' => 'border-slate-400/25 bg-slate-400/10 text-slate-300',
                                        ];
                                    @endphp

                                    <tr class="transition hover:bg-white/[0.025]">
                                        <td class="px-6 py-4">
                                            @if ($appeal->user)
                                                <div>
                                                    <div class="font-black text-white">{{ $appeal->user->name }}</div>
                                                    <div class="mt-1 text-xs text-slate-400">{{ $appeal->user->email }}</div>
                                                    @if ($appeal->user->phone)
                                                        <div class="mt-1 text-xs text-slate-500">{{ $appeal->user->phone }}</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-500">مستخدم محذوف</span>
                                            @endif
                                        </td>

                                        <td class="max-w-[360px] px-6 py-4">
                                            <p class="text-sm leading-7 text-slate-300">
                                                {{ \Illuminate\Support\Str::limit($appeal->message, 140) }}
                                            </p>

                                            @if ($appeal->attachment_path)
                                                <span class="mt-2 inline-flex rounded-lg border border-blue-400/20 bg-blue-400/10 px-2.5 py-1 text-xs font-black text-blue-300">
                                                    يحتوي على مرفق
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            @if ($appeal->warning)
                                                <div class="font-black text-white">
                                                    تحذير {{ $appeal->warning->warning_number }} / 3
                                                </div>
                                                <div class="mt-1 text-xs text-slate-400">
                                                    {{ $appeal->warning->category ?: 'غير محدد' }}
                                                </div>
                                            @else
                                                <span class="text-sm text-slate-500">غير مرتبط</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-lg border px-3 py-1.5 text-xs font-black {{ $statusClasses[$appeal->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                                {{ $statusLabels[$appeal->status] ?? $appeal->status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-200">{{ $appeal->created_at?->format('Y-m-d') }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $appeal->created_at?->format('H:i') }}</div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap justify-center gap-2">
                                                <a
                                                    href="{{ route('admin.moderation-appeals.show', $appeal) }}"
                                                    class="px-3 py-2 text-xs font-black text-blue-300 transition border rounded-lg border-blue-400/25 bg-blue-400/10 hover:bg-blue-500 hover:text-white"
                                                >
                                                    التفاصيل
                                                </a>

                                                @if ($appeal->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.moderation-appeals.start-review', $appeal) }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="px-3 py-2 text-xs font-black transition border rounded-lg border-amber-400/25 bg-amber-400/10 text-amber-300 hover:bg-amber-500 hover:text-white"
                                                        >
                                                            بدء المراجعة
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($appeals->hasPages())
                        <div class="px-6 py-4 border-t border-slate-800">
                            {{ $appeals->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
