<x-app-layout>
    @php
        $user = $appeal->user;
        $warning = $appeal->warning;
        $moderation = $warning?->moderation;

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

    <div class="min-h-screen px-4 py-8 bg-slate-950 text-slate-100 sm:px-6 lg:px-8" dir="rtl">
        <style>
            .appeal-detail-glass {
                background: rgba(15, 23, 42, .74);
                border: 1px solid rgba(51, 65, 85, .72);
                box-shadow: 0 4px 30px rgba(0, 0, 0, .14);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
            }
        </style>

        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col gap-4 mb-8 md:flex-row md:items-end md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <h1 class="text-3xl font-black text-white md:text-4xl">
                            تفاصيل طلب الطعن
                        </h1>

                        <span class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-black text-blue-300">
                            #APL-{{ str_pad((string) $appeal->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <p class="text-sm leading-7 text-slate-400">
                        مراجعة رسالة المستخدم والمرفقات والقرار المرتبط بتعليق الحساب.
                    </p>
                </div>

                <a
                    href="{{ route('admin.moderation-appeals.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 text-sm font-black transition border rounded-xl border-slate-700 bg-slate-900 text-slate-200 hover:border-blue-400 hover:text-white"
                >
                    العودة إلى قائمة الطعون
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

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <aside class="space-y-6 lg:col-span-4">
                    <section class="p-6 appeal-detail-glass rounded-2xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-800">
                            <h2 class="text-xl font-black text-white">بيانات المستخدم</h2>
                        </div>

                        @if ($user)
                            <div class="space-y-4">
                                <div>
                                    <span class="block text-xs font-bold text-slate-500">الاسم</span>
                                    <strong class="block mt-1 text-white">{{ $user->name }}</strong>
                                </div>

                                <div>
                                    <span class="block text-xs font-bold text-slate-500">البريد الإلكتروني</span>
                                    <span class="block mt-1 text-sm break-all text-slate-300">{{ $user->email }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-bold text-slate-500">الهاتف</span>
                                    <span class="block mt-1 text-sm text-slate-300">{{ $user->phone ?: 'غير متوفر' }}</span>
                                </div>

                                <div class="flex items-center justify-between p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="text-sm font-bold text-slate-300">حالة الحساب</span>
                                    <span class="rounded-lg border border-red-400/20 bg-red-400/10 px-3 py-1.5 text-xs font-black text-red-300">
                                        {{ $user->status }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="text-sm font-bold text-slate-300">عدد التحذيرات</span>
                                    <span class="font-black text-amber-300">{{ $user->warnings_count ?? 0 }} / 3</span>
                                </div>
                            </div>
                        @else
                            <div class="py-10 text-center text-slate-500">المستخدم غير موجود.</div>
                        @endif
                    </section>

                    <section class="p-6 appeal-detail-glass rounded-2xl">
                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-800">
                            <h2 class="text-xl font-black text-white">بيانات الطعن</h2>

                            <span class="rounded-lg border px-3 py-1.5 text-xs font-black {{ $statusClasses[$appeal->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                {{ $statusLabels[$appeal->status] ?? $appeal->status }}
                            </span>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between gap-4 pb-3 border-b border-slate-800">
                                <span class="font-bold text-slate-400">تاريخ الإرسال</span>
                                <span class="text-slate-200">{{ $appeal->created_at?->format('Y-m-d H:i') }}</span>
                            </div>

                            <div class="flex justify-between gap-4 pb-3 border-b border-slate-800">
                                <span class="font-bold text-slate-400">بدأت المراجعة</span>
                                <span class="text-slate-200">{{ $appeal->reviewed_at?->format('Y-m-d H:i') ?? 'لم تبدأ' }}</span>
                            </div>

                            <div class="flex justify-between gap-4">
                                <span class="font-bold text-slate-400">راجع الطلب</span>
                                <span class="text-slate-200">{{ $appeal->reviewer?->name ?? 'لم يحدد' }}</span>
                            </div>
                        </div>
                    </section>

                    @if ($warning)
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="pb-4 mb-5 border-b border-slate-800">
                                <h2 class="text-xl font-black text-white">التحذير المرتبط</h2>
                            </div>

                            <div class="space-y-4 text-sm">
                                <div>
                                    <span class="block text-xs font-bold text-slate-500">رقم التحذير</span>
                                    <strong class="block mt-1 text-blue-300">{{ $warning->warning_number }} / 3</strong>
                                </div>

                                <div>
                                    <span class="block text-xs font-bold text-slate-500">التصنيف</span>
                                    <span class="block mt-1 text-slate-200">{{ $warning->category ?: 'غير محدد' }}</span>
                                </div>

                                <div>
                                    <span class="block text-xs font-bold text-slate-500">السبب</span>
                                    <p class="p-4 mt-2 leading-7 border rounded-xl border-slate-800 bg-slate-900 text-slate-300">
                                        {{ $warning->reason }}
                                    </p>
                                </div>

                                <a
                                    href="{{ route('admin.moderation.show', $warning) }}"
                                    class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-black text-blue-300 transition border rounded-xl border-blue-400/25 bg-blue-400/10 hover:bg-blue-500 hover:text-white"
                                >
                                    فتح تفاصيل التحذير
                                </a>
                            </div>
                        </section>
                    @endif
                </aside>

                <main class="space-y-6 lg:col-span-8">
                    <section class="p-6 appeal-detail-glass rounded-2xl">
                        <div class="pb-4 mb-5 border-b border-slate-800">
                            <h2 class="text-2xl font-black text-white">رسالة المستخدم</h2>
                        </div>

                        <div class="p-5 border rounded-xl border-slate-800 bg-slate-900">
                            <p class="text-sm leading-8 whitespace-pre-line text-slate-300">
                                {{ $appeal->message }}
                            </p>
                        </div>
                    </section>

                    @if ($appeal->attachment_path)
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="pb-4 mb-5 border-b border-slate-800">
                                <h2 class="text-xl font-black text-white">المرفق الداعم</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="block text-xs font-bold text-slate-500">اسم الملف</span>
                                    <span class="block mt-2 text-sm break-all text-slate-200">
                                        {{ $appeal->attachment_original_name ?: basename($appeal->attachment_path) }}
                                    </span>
                                </div>

                                <div class="p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="block text-xs font-bold text-slate-500">نوع الملف</span>
                                    <span class="block mt-2 text-sm text-slate-200">
                                        {{ $appeal->attachment_mime_type ?: 'غير محدد' }}
                                    </span>
                                </div>
                            </div>

                            @if ($appeal->attachment_mime_type && str_starts_with($appeal->attachment_mime_type, 'image/'))
                                <div class="flex justify-center p-4 mt-5 border rounded-xl border-slate-800 bg-slate-900">
                                    <img
                                        src="{{ asset('storage/' . $appeal->attachment_path) }}"
                                        alt="مرفق الطعن"
                                        class="max-h-[520px] max-w-full rounded-xl object-contain"
                                    >
                                </div>
                            @else
                                <a
                                    href="{{ asset('storage/' . $appeal->attachment_path) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center justify-center px-5 py-3 mt-5 text-sm font-black text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                                >
                                    فتح المرفق
                                </a>
                            @endif
                        </section>
                    @endif

                    @if ($moderation)
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="pb-4 mb-5 border-b border-slate-800">
                                <h2 class="text-xl font-black text-white">ملخص نتيجة الفحص</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="block text-xs font-bold text-slate-500">قرار النظام</span>
                                    <strong class="block mt-2 text-red-300">{{ $moderation->decision ?: 'غير محدد' }}</strong>
                                </div>

                                <div class="p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="block text-xs font-bold text-slate-500">مستوى الخطر</span>
                                    <strong class="block mt-2 text-amber-300">{{ $moderation->risk_level ?: 'غير محدد' }}</strong>
                                </div>

                                <div class="p-4 border rounded-xl border-slate-800 bg-slate-900">
                                    <span class="block text-xs font-bold text-slate-500">المزود</span>
                                    <strong class="block mt-2 text-blue-300">{{ $moderation->provider ?: 'غير محدد' }}</strong>
                                </div>
                            </div>
                        </section>
                    @endif

                    @if ($appeal->status === 'pending')
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="pb-4 mb-5 border-b border-slate-800">
                                <h2 class="text-xl font-black text-white">بدء مراجعة الطلب</h2>
                            </div>

                            <form method="POST" action="{{ route('admin.moderation-appeals.start-review', $appeal) }}">
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="w-full px-6 py-4 text-lg font-black transition rounded-xl bg-amber-500 text-slate-950 hover:bg-amber-400"
                                >
                                    تحويل الطعن إلى حالة تحت المراجعة
                                </button>
                            </form>
                        </section>
                    @endif

                    @if (in_array($appeal->status, ['pending', 'under_review'], true))
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="pb-4 mb-5 border-b border-slate-800">
                                <h2 class="text-2xl font-black text-white">قرار الإدارة</h2>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation-appeals.approve', $appeal) }}"
                                    class="p-5 border rounded-2xl border-emerald-400/25 bg-emerald-400/5"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="text-xl font-black text-emerald-300">قبول الطعن</h3>

                                    <p class="mt-2 text-sm leading-7 text-slate-400">
                                        سيُلغى التحذير المرتبط ويُعاد تفعيل حساب المستخدم.
                                    </p>

                                    <textarea
                                        name="admin_response"
                                        rows="5"
                                        minlength="10"
                                        maxlength="3000"
                                        required
                                        placeholder="اكتب رد الإدارة وسبب قبول الطعن..."
                                        class="w-full p-4 mt-5 text-sm text-white border outline-none rounded-xl border-slate-700 bg-slate-950 placeholder:text-slate-600 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400"
                                    >{{ old('admin_response') }}</textarea>

                                    <button
                                        type="submit"
                                        class="w-full px-5 py-3 mt-4 text-sm font-black text-white transition rounded-xl bg-emerald-500 hover:bg-emerald-400"
                                        onclick="return confirm('هل تريد قبول الطعن وإعادة تفعيل الحساب؟');"
                                    >
                                        قبول الطعن وإعادة التفعيل
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation-appeals.reject', $appeal) }}"
                                    class="p-5 border rounded-2xl border-red-400/25 bg-red-400/5"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="text-xl font-black text-red-300">رفض الطعن</h3>

                                    <p class="mt-2 text-sm leading-7 text-slate-400">
                                        سيبقى الحساب معلقًا ويُرسل الرد إلى المستخدم.
                                    </p>

                                    <textarea
                                        name="admin_response"
                                        rows="5"
                                        minlength="10"
                                        maxlength="3000"
                                        required
                                        placeholder="اكتب سبب رفض الطعن..."
                                        class="w-full p-4 mt-5 text-sm text-white border outline-none rounded-xl border-red-400/30 bg-slate-950 placeholder:text-slate-600 focus:border-red-400 focus:ring-1 focus:ring-red-400"
                                    >{{ old('admin_response') }}</textarea>

                                    <button
                                        type="submit"
                                        class="w-full px-5 py-3 mt-4 text-sm font-black text-white transition bg-red-500 rounded-xl hover:bg-red-400"
                                        onclick="return confirm('هل تريد رفض الطعن وتثبيت تعليق الحساب؟');"
                                    >
                                        رفض الطعن وتثبيت التعليق
                                    </button>
                                </form>
                            </div>
                        </section>
                    @else
                        <section class="p-6 appeal-detail-glass rounded-2xl">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-black text-white">قرار الإدارة النهائي</h2>

                                <span class="rounded-lg border px-3 py-1.5 text-xs font-black {{ $statusClasses[$appeal->status] ?? 'border-slate-400/20 bg-slate-400/10 text-slate-300' }}">
                                    {{ $statusLabels[$appeal->status] ?? $appeal->status }}
                                </span>
                            </div>

                            <div class="p-5 border rounded-xl border-slate-800 bg-slate-900">
                                <p class="text-sm leading-8 whitespace-pre-line text-slate-300">
                                    {{ $appeal->admin_response ?: 'لا يوجد رد مسجل.' }}
                                </p>
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
