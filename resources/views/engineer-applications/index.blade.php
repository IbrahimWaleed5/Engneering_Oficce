<x-app-layout>

    <div
        class="py-10"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- رأس الصفحة --}}
            <div
                class="flex flex-col gap-5 mb-8 border shadow-xl p-7 sm:flex-row sm:items-center sm:justify-between rounded-3xl bg-slate-900/90 border-slate-800"
            >

                <div>

                    <p class="mb-2 text-sm font-bold text-orange-300">
                        الإدارة
                    </p>

                    <h1 class="text-3xl font-black text-white">
                        طلبات توظيف المهندسين
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        مراجعة بيانات المتقدمين والشهادات وإيصالات الدفع،
                        ثم قبول أو رفض الطلب.
                    </p>

                </div>

                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 font-bold transition border rounded-2xl border-slate-700 text-slate-300 hover:bg-slate-800"
                >
                    <span>→</span>
                    لوحة التحكم
                </a>

            </div>

            {{-- رسالة النجاح --}}
            @if (session('success'))

                <div
                    class="p-4 mb-6 text-green-200 border rounded-2xl border-green-700/50 bg-green-900/30"
                >
                    {{ session('success') }}
                </div>

            @endif

            {{-- رسالة الخطأ --}}
            @if (session('error'))

                <div
                    class="p-4 mb-6 text-red-200 border rounded-2xl border-red-700/50 bg-red-900/30"
                >
                    {{ session('error') }}
                </div>

            @endif

            {{-- أخطاء النماذج --}}
            @if ($errors->any())

                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-700/50 bg-red-900/30"
                >

                    <ul class="space-y-2">

                        @foreach ($errors->all() as $error)

                            <li>
                                • {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            {{-- ملخص --}}
            <div class="grid gap-5 mb-8 sm:grid-cols-2">

                <div
                    class="p-6 border shadow rounded-3xl bg-slate-900/90 border-slate-800"
                >

                    <p class="text-sm text-slate-400">
                        إجمالي الطلبات
                    </p>

                    <p class="mt-3 text-3xl font-black text-white">
                        {{ $applications->total() }}
                    </p>

                </div>

                <div
                    class="p-6 border shadow rounded-3xl bg-slate-900/90 border-slate-800"
                >

                    <p class="text-sm text-slate-400">
                        الطلبات المعروضة في الصفحة
                    </p>

                    <p class="mt-3 text-3xl font-black text-cyan-300">
                        {{ $applications->count() }}
                    </p>

                </div>

            </div>

            {{-- الطلبات --}}
            <div class="space-y-6">

                @forelse ($applications as $application)

                    @php
                        $statusClass = match (
                            $application->status
                        ) {
                            'approved' =>
                                'bg-green-500/10 text-green-200 border-green-500/20',

                            'rejected' =>
                                'bg-red-500/10 text-red-200 border-red-500/20',

                            default =>
                                'bg-yellow-500/10 text-yellow-200 border-yellow-500/20',
                        };

                        $statusText = match (
                            $application->status
                        ) {
                            'approved' => 'مقبول',
                            'rejected' => 'مرفوض',
                            default => 'قيد المراجعة',
                        };

                        $paymentClass = match (
                            $application->payment_status
                        ) {
                            'paid' =>
                                'bg-green-500/10 text-green-200',

                            'rejected' =>
                                'bg-red-500/10 text-red-200',

                            default =>
                                'bg-yellow-500/10 text-yellow-200',
                        };

                        $paymentText = match (
                            $application->payment_status
                        ) {
                            'paid' => 'تم تأكيد الدفع',
                            'rejected' => 'الدفع مرفوض',
                            default => 'الدفع قيد الفحص',
                        };
                    @endphp

                    <article
                        class="overflow-hidden border shadow-xl rounded-3xl bg-slate-900/90 border-slate-800"
                    >

                        {{-- عنوان الطلب --}}
                        <div
                            class="flex flex-col gap-4 p-6 border-b sm:flex-row sm:items-center sm:justify-between border-slate-800"
                        >

                            <div class="flex items-center gap-4">

                                <div
                                    class="flex items-center justify-center flex-none text-xl font-black text-white rounded-full w-14 h-14 bg-gradient-to-br from-purple-600 to-blue-600"
                                >
                                    {{ mb_substr(
                                        $application->user?->name ?? 'م',
                                        0,
                                        1
                                    ) }}
                                </div>

                                <div>

                                    <h2 class="text-xl font-black text-white">
                                        {{ $application->user?->name ?? 'مستخدم غير معروف' }}
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-400">
                                        طلب رقم:
                                        #{{ $application->id }}
                                    </p>

                                </div>

                            </div>

                            <div
                                class="inline-flex items-center self-start px-4 py-2 text-sm font-bold border rounded-full {{ $statusClass }}"
                            >
                                {{ $statusText }}
                            </div>

                        </div>

                        <div class="grid gap-8 p-6 lg:grid-cols-3">

                            {{-- بيانات المتقدم --}}
                            <div>

                                <h3 class="mb-5 font-black text-white">
                                    بيانات المتقدم
                                </h3>

                                <div class="space-y-4 text-sm">

                                    <div
                                        class="p-4 rounded-2xl bg-slate-950/70"
                                    >

                                        <p class="text-xs text-slate-500">
                                            الاسم
                                        </p>

                                        <p class="mt-2 font-bold text-white">
                                            {{ $application->user?->name ?? 'غير معروف' }}
                                        </p>

                                    </div>

                                    <div
                                        class="p-4 rounded-2xl bg-slate-950/70"
                                    >

                                        <p class="text-xs text-slate-500">
                                            البريد الإلكتروني
                                        </p>

                                        <p
                                            class="mt-2 font-bold text-white break-all"
                                        >
                                            {{ $application->user?->email ?? 'غير متوفر' }}
                                        </p>

                                    </div>

                                    <div
                                        class="p-4 rounded-2xl bg-slate-950/70"
                                    >

                                        <p class="text-xs text-slate-500">
                                            رقم الهاتف
                                        </p>

                                        <p class="mt-2 font-bold text-white">
                                            {{ $application->user?->phone ?? 'غير متوفر' }}
                                        </p>

                                    </div>

                                    <div
                                        class="p-4 rounded-2xl bg-slate-950/70"
                                    >

                                        <p class="text-xs text-slate-500">
                                            التخصص
                                        </p>

                                        <p class="mt-2 font-bold text-cyan-300">
                                            {{ $application->specialty?->name ?? 'غير محدد' }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            {{-- الدفع والملفات --}}
                            <div>

                                <h3 class="mb-5 font-black text-white">
                                    الدفع والمستندات
                                </h3>

                                <div
                                    class="p-4 mb-5 rounded-2xl bg-slate-950/70"
                                >

                                    <div
                                        class="flex items-center justify-between gap-3"
                                    >

                                        <div>

                                            <p class="text-xs text-slate-500">
                                                قيمة الطلب
                                            </p>

                                            <p
                                                class="mt-2 text-2xl font-black text-green-300"
                                            >
                                                {{ number_format(
                                                    $application->amount,
                                                    2
                                                ) }}
                                                ₪
                                            </p>

                                        </div>

                                        <span
                                            class="px-3 py-2 text-xs font-bold rounded-xl {{ $paymentClass }}"
                                        >
                                            {{ $paymentText }}
                                        </span>

                                    </div>

                                </div>

                                <div class="grid gap-3">

                                    <a
                                        href="{{ asset(
                                            'storage/' .
                                            $application->certificate_file
                                        ) }}"
                                        target="_blank"
                                        class="flex items-center justify-between gap-3 p-4 font-bold text-white transition border rounded-2xl border-slate-700 bg-slate-950/70 hover:border-purple-500"
                                    >
                                        <span>
                                            🎓 الشهادة الهندسية
                                        </span>

                                        <span class="text-purple-300">
                                            عرض
                                        </span>
                                    </a>

                                    @if ($application->cv_file)

                                        <a
                                            href="{{ asset(
                                                'storage/' .
                                                $application->cv_file
                                            ) }}"
                                            target="_blank"
                                            class="flex items-center justify-between gap-3 p-4 font-bold text-white transition border rounded-2xl border-slate-700 bg-slate-950/70 hover:border-cyan-500"
                                        >
                                            <span>
                                                📄 السيرة الذاتية
                                            </span>

                                            <span class="text-cyan-300">
                                                عرض
                                            </span>
                                        </a>

                                    @else

                                        <div
                                            class="p-4 text-sm border rounded-2xl border-slate-800 bg-slate-950/50 text-slate-500"
                                        >
                                            لم يتم رفع سيرة ذاتية.
                                        </div>

                                    @endif

                                    <a
                                        href="{{ asset(
                                            'storage/' .
                                            $application->payment_receipt
                                        ) }}"
                                        target="_blank"
                                        class="flex items-center justify-between gap-3 p-4 font-bold text-white transition border rounded-2xl border-slate-700 bg-slate-950/70 hover:border-green-500"
                                    >
                                        <span>
                                            🧾 إيصال الدفع
                                        </span>

                                        <span class="text-green-300">
                                            عرض
                                        </span>
                                    </a>

                                </div>

                                <p class="mt-4 text-xs text-slate-500">
                                    تاريخ تقديم الطلب:
                                    {{ $application->created_at?->format(
                                        'Y-m-d h:i A'
                                    ) }}
                                </p>

                            </div>

                            {{-- الإجراءات --}}
                            <div>

                                <h3 class="mb-5 font-black text-white">
                                    قرار الإدارة
                                </h3>

                                @if ($application->status === 'pending')

                                    {{-- قبول الطلب --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'engineer-applications.approve',
                                            $application
                                        ) }}"
                                        class="p-5 mb-5 border rounded-2xl border-green-500/20 bg-green-500/5"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <label
                                            for="approve-note-{{ $application->id }}"
                                            class="block mb-2 text-sm font-bold text-green-200"
                                        >
                                            ملاحظة الموافقة
                                            <span class="font-normal text-slate-500">
                                                — اختياري
                                            </span>
                                        </label>

                                        <textarea
                                            id="approve-note-{{ $application->id }}"
                                            name="admin_note"
                                            rows="3"
                                            placeholder="اكتب ملاحظة للمهندس..."
                                            class="w-full px-4 py-3 text-sm text-white border outline-none resize-none rounded-2xl border-slate-700 bg-slate-950 focus:border-green-500"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            onclick="
                                                return confirm(
                                                    'هل أنت متأكد من قبول الطلب وتحويل الحساب إلى مهندس؟'
                                                );
                                            "
                                            class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 mt-4 font-black text-white transition bg-green-600 rounded-2xl hover:bg-green-500"
                                        >
                                            <span>✅</span>
                                            تأكيد الدفع وقبول الطلب
                                        </button>

                                    </form>

                                    {{-- رفض الطلب --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'engineer-applications.reject',
                                            $application
                                        ) }}"
                                        class="p-5 border rounded-2xl border-red-500/20 bg-red-500/5"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <label
                                            for="reject-note-{{ $application->id }}"
                                            class="block mb-2 text-sm font-bold text-red-200"
                                        >
                                            سبب الرفض
                                            <span class="text-red-400">*</span>
                                        </label>

                                        <textarea
                                            id="reject-note-{{ $application->id }}"
                                            name="admin_note"
                                            rows="3"
                                            required
                                            placeholder="اكتب سبب رفض الطلب..."
                                            class="w-full px-4 py-3 text-sm text-white border outline-none resize-none rounded-2xl border-slate-700 bg-slate-950 focus:border-red-500"
                                        ></textarea>

                                        <button
                                            type="submit"
                                            onclick="
                                                return confirm(
                                                    'هل أنت متأكد من رفض الطلب؟'
                                                );
                                            "
                                            class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 mt-4 font-black text-white transition bg-red-600 rounded-2xl hover:bg-red-500"
                                        >
                                            <span>❌</span>
                                            رفض الطلب
                                        </button>

                                    </form>

                                @else

                                    <div
                                        class="p-5 border rounded-2xl border-slate-700 bg-slate-950/70"
                                    >

                                        <p class="font-black text-white">
                                            تمت معالجة هذا الطلب
                                        </p>

                                        <p class="mt-2 text-sm text-slate-400">
                                            الحالة الحالية:
                                            <span class="font-bold">
                                                {{ $statusText }}
                                            </span>
                                        </p>

                                        @if ($application->admin_note)

                                            <div
                                                class="p-4 mt-4 text-sm leading-7 rounded-xl bg-slate-900 text-slate-300"
                                            >
                                                <p
                                                    class="mb-1 text-xs font-bold text-slate-500"
                                                >
                                                    ملاحظة الإدارة
                                                </p>

                                                {{ $application->admin_note }}
                                            </div>

                                        @endif

                                    </div>

                                @endif

                            </div>

                        </div>

                    </article>

                @empty

                    <div
                        class="p-12 text-center border shadow-xl rounded-3xl bg-slate-900/90 border-slate-800"
                    >

                        <div class="mb-5 text-6xl">
                            📭
                        </div>

                        <h2 class="text-2xl font-black text-white">
                            لا توجد طلبات توظيف
                        </h2>

                        <p class="mt-3 text-slate-400">
                            ستظهر طلبات الانضمام كمهندس هنا بعد تقديمها.
                        </p>

                    </div>

                @endforelse

            </div>

            {{-- التنقل بين الصفحات --}}
            @if ($applications->hasPages())

                <div
                    class="p-5 mt-8 border rounded-3xl bg-slate-900/90 border-slate-800"
                >
                    {{ $applications->links() }}
                </div>

            @endif

        </div>

    </div>

</x-app-layout>
