<x-app-layout>

    <x-slot name="header">

        <div
            class="flex flex-wrap items-center justify-between gap-4"
            dir="rtl"
        >

            <div>

                <h2 class="text-2xl font-black text-white">
                    إدارة الدفعات
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    مراجعة إيصالات الدفع وقبول الدفعات أو رفضها
                </p>

            </div>

            <a
                href="{{ route('dashboard') }}"
                class="px-5 py-3 text-sm font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800 hover:text-white"
            >
                العودة إلى لوحة التحكم
            </a>

        </div>

    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-[1600px] sm:px-6 lg:px-8">

            {{-- رسائل النجاح --}}

            @if (session('success'))

                <div
                    class="flex items-start gap-3 p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >

                    <div
                        class="flex items-center justify-center flex-none w-10 h-10 text-lg rounded-xl bg-green-500/15"
                    >
                        ✓
                    </div>

                    <div>

                        <p class="font-bold">
                            تمت العملية بنجاح
                        </p>

                        <p class="mt-1 text-sm text-green-200/80">
                            {{ session('success') }}
                        </p>

                    </div>

                </div>

            @endif

            {{-- رسائل الخطأ --}}

            @if (session('error'))

                <div
                    class="flex items-start gap-3 p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >

                    <div
                        class="flex items-center justify-center flex-none w-10 h-10 text-lg rounded-xl bg-red-500/15"
                    >
                        !
                    </div>

                    <div>

                        <p class="font-bold">
                            حدث خطأ
                        </p>

                        <p class="mt-1 text-sm text-red-200/80">
                            {{ session('error') }}
                        </p>

                    </div>

                </div>

            @endif

            @if ($errors->any())

                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >

                    <p class="mb-3 font-bold">
                        يرجى تصحيح الأخطاء التالية:
                    </p>

                    <ul class="space-y-2 text-sm list-disc list-inside">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            {{-- الإحصائيات --}}

            @php
                $allPayments = $payments->count();

                $pendingPayments = $payments
                    ->where('status', 'pending')
                    ->count();

                $completedPayments = $payments
                    ->where('status', 'completed')
                    ->count();

                $rejectedPayments = $payments
                    ->where('status', 'rejected')
                    ->count();

                $completedAmount = $payments
                    ->where('status', 'completed')
                    ->sum('amount');
            @endphp

            <div class="grid gap-4 mb-8 sm:grid-cols-2 xl:grid-cols-5">

                <div class="p-5 glass-panel-strong rounded-[2rem]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                جميع الدفعات
                            </p>

                            <p class="mt-3 text-3xl font-black text-white">
                                {{ $allPayments }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-blue-400/20 bg-blue-500/10"
                        >
                            💳
                        </div>

                    </div>

                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                قيد المراجعة
                            </p>

                            <p class="mt-3 text-3xl font-black text-yellow-300">
                                {{ $pendingPayments }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-yellow-400/20 bg-yellow-500/10"
                        >
                            ⏳
                        </div>

                    </div>

                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                الدفعات المقبولة
                            </p>

                            <p class="mt-3 text-3xl font-black text-green-300">
                                {{ $completedPayments }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-green-400/20 bg-green-500/10"
                        >
                            ✓
                        </div>

                    </div>

                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                الدفعات المرفوضة
                            </p>

                            <p class="mt-3 text-3xl font-black text-red-300">
                                {{ $rejectedPayments }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-red-400/20 bg-red-500/10"
                        >
                            ✕
                        </div>

                    </div>

                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                إجمالي المقبول
                            </p>

                            <p class="mt-3 text-2xl font-black text-cyan-300">
                                {{ number_format($completedAmount, 2) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                شيكل
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-cyan-400/20 bg-cyan-500/10"
                        >
                            ₪
                        </div>

                    </div>

                </div>

            </div>

            {{-- جدول الدفعات --}}

            <section
                class="overflow-hidden glass-panel-strong rounded-[2rem]"
            >

                <div
                    class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-white/10"
                >

                    <div>

                        <h3 class="text-xl font-black text-white">
                            سجل الدفعات
                        </h3>

                        <p class="mt-1 text-sm text-slate-400">
                            جميع طلبات الدفع المرسلة من العملاء
                        </p>

                    </div>

                    <div
                        class="px-4 py-2 text-sm font-bold border rounded-xl border-cyan-400/20 bg-cyan-500/10 text-cyan-300"
                    >
                        {{ $allPayments }} دفعة
                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1450px] text-sm">

                        <thead
                            class="text-xs border-b bg-white/5 text-slate-400 border-white/10"
                        >

                            <tr>

                                <th class="px-5 py-4 text-right">
                                    رقم الاستشارة
                                </th>

                                <th class="px-5 py-4 text-right">
                                    العميل
                                </th>

                                <th class="px-5 py-4 text-right">
                                    المبلغ
                                </th>

                                <th class="px-5 py-4 text-right">
                                    طريقة الدفع
                                </th>

                                <th class="px-5 py-4 text-right">
                                    رقم العملية
                                </th>

                                <th class="px-5 py-4 text-right">
                                    الإيصال
                                </th>

                                <th class="px-5 py-4 text-right">
                                    الحالة
                                </th>

                                <th class="px-5 py-4 text-right">
                                    تاريخ الإرسال
                                </th>

                                <th class="px-5 py-4 text-right">
                                    الإجراءات
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            @forelse ($payments as $payment)

                                <tr class="transition hover:bg-white/[0.03]">

                                    {{-- رقم الاستشارة --}}

                                    <td class="px-5 py-5">

                                        <div
                                            class="inline-flex px-3 py-2 font-bold text-blue-300 border rounded-xl border-blue-400/20 bg-blue-500/10"
                                        >
                                            {{ $payment->consultation?->consultation_number ?? '—' }}
                                        </div>

                                    </td>

                                    {{-- العميل --}}

                                    <td class="px-5 py-5">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="flex items-center justify-center flex-none w-10 h-10 font-black text-white rounded-full bg-gradient-to-br from-blue-600 to-cyan-500"
                                            >
                                                {{ mb_substr(
                                                    $payment->customer?->name ?? 'ع',
                                                    0,
                                                    1
                                                ) }}
                                            </div>

                                            <div>

                                                <p class="font-bold text-white">
                                                    {{ $payment->customer?->name ?? 'عميل غير معروف' }}
                                                </p>

                                                @if ($payment->customer?->email)

                                                    <p class="mt-1 text-xs text-slate-500">
                                                        {{ $payment->customer->email }}
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                    {{-- المبلغ --}}

                                    <td class="px-5 py-5">

                                        <p class="text-lg font-black text-cyan-300">
                                            {{ number_format($payment->amount, 2) }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            شيكل
                                        </p>

                                    </td>

                                    {{-- طريقة الدفع --}}

                                    <td class="px-5 py-5">

                                        @php
                                            $paymentMethod = match (
                                                $payment->payment_method
                                            ) {
                                                'cash' => [
                                                    'label' => 'نقدي',
                                                    'icon' => '💵',
                                                ],

                                                'card' => [
                                                    'label' => 'بطاقة',
                                                    'icon' => '💳',
                                                ],

                                                'bank' => [
                                                    'label' => 'تحويل بنكي',
                                                    'icon' => '🏦',
                                                ],

                                                'wallet' => [
                                                    'label' => 'محفظة إلكترونية',
                                                    'icon' => '📱',
                                                ],

                                                default => [
                                                    'label' => $payment->payment_method,
                                                    'icon' => '💰',
                                                ],
                                            };
                                        @endphp

                                        <div
                                            class="inline-flex items-center gap-2 px-3 py-2 border rounded-xl border-white/10 bg-white/5 text-slate-300"
                                        >
                                            <span>
                                                {{ $paymentMethod['icon'] }}
                                            </span>

                                            <span>
                                                {{ $paymentMethod['label'] }}
                                            </span>
                                        </div>

                                    </td>

                                    {{-- رقم العملية --}}

                                    <td class="px-5 py-5">

                                        <span
                                            class="font-mono text-sm text-slate-300"
                                        >
                                            {{ $payment->transaction_reference ?: '—' }}
                                        </span>

                                    </td>

                                    {{-- الإيصال --}}

                                    <td class="px-5 py-5">

                                        @if ($payment->receipt_image)

                                            @php
                                                $receiptUrl = asset(
                                                    'storage/' . $payment->receipt_image
                                                );

                                                $isPdf = str_ends_with(
                                                    strtolower(
                                                        $payment->receipt_image
                                                    ),
                                                    '.pdf'
                                                );
                                            @endphp

                                            @if ($isPdf)

                                                <a
                                                    href="{{ $receiptUrl }}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-2 px-4 py-3 font-bold text-red-300 transition border rounded-xl border-red-400/20 bg-red-500/10 hover:bg-red-500/20"
                                                >
                                                    <span>📄</span>
                                                    عرض PDF
                                                </a>

                                            @else

                                                <a
                                                    href="{{ $receiptUrl }}"
                                                    target="_blank"
                                                    class="relative block w-20 h-20 overflow-hidden transition border rounded-2xl group border-white/10 hover:border-cyan-400/50"
                                                    title="اضغط لعرض الإيصال"
                                                >

                                                    <img
                                                        src="{{ $receiptUrl }}"
                                                        alt="إيصال الدفع"
                                                        class="object-cover w-full h-full transition duration-300 group-hover:scale-110"
                                                    >

                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center transition opacity-0 bg-slate-950/70 group-hover:opacity-100"
                                                    >
                                                        <span class="text-xl">
                                                            👁
                                                        </span>
                                                    </div>

                                                </a>

                                            @endif

                                        @else

                                            <span
                                                class="inline-flex px-3 py-2 text-xs border rounded-xl border-slate-600/30 bg-slate-800/50 text-slate-500"
                                            >
                                                لا يوجد إيصال
                                            </span>

                                        @endif

                                    </td>

                                    {{-- الحالة --}}

                                    <td class="px-5 py-5">

                                        @if ($payment->status === 'pending')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-yellow-300 border rounded-full border-yellow-400/20 bg-yellow-500/10"
                                            >
                                                <span
                                                    class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"
                                                ></span>

                                                قيد المراجعة
                                            </span>

                                        @elseif ($payment->status === 'completed')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-green-300 border rounded-full border-green-400/20 bg-green-500/10"
                                            >
                                                <span>
                                                    ✓
                                                </span>

                                                مقبولة
                                            </span>

                                        @elseif ($payment->status === 'rejected')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-red-300 border rounded-full border-red-400/20 bg-red-500/10"
                                            >
                                                <span>
                                                    ✕
                                                </span>

                                                مرفوضة
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex px-3 py-2 text-xs font-bold rounded-full bg-slate-500/10 text-slate-300"
                                            >
                                                {{ $payment->status }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- تاريخ الإرسال --}}

                                    <td class="px-5 py-5">

                                        <p class="font-bold text-slate-300">
                                            {{ $payment->created_at?->format('Y-m-d') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $payment->created_at?->format('H:i') }}
                                        </p>

                                    </td>

                                    {{-- الإجراءات --}}

                                    <td class="px-5 py-5">

                                        @if ($payment->status === 'pending')

                                            <div class="min-w-[260px] space-y-3">

                                                <form
                                                    action="{{ route(
                                                        'payments.confirm',
                                                        $payment
                                                    ) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من قبول الدفعة؟')"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="flex items-center justify-center w-full gap-2 px-4 py-3 text-sm font-bold text-green-200 transition border rounded-xl border-green-400/20 bg-green-500/10 hover:bg-green-500/20 hover:border-green-400/40"
                                                    >
                                                        <span>✓</span>
                                                        قبول الدفعة
                                                    </button>

                                                </form>

                                                <form
                                                    action="{{ route(
                                                        'payments.reject',
                                                        $payment
                                                    ) }}"
                                                    method="POST"
                                                    class="space-y-2"
                                                    onsubmit="return confirm('هل أنت متأكد من رفض الدفعة؟')"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <textarea
                                                        name="rejection_reason"
                                                        rows="2"
                                                        placeholder="اكتب سبب رفض الإيصال..."
                                                        required
                                                        class="w-full px-3 py-3 text-sm text-white transition border resize-none rounded-xl border-white/10 bg-slate-950/50 placeholder:text-slate-600 focus:border-red-400/50 focus:ring-2 focus:ring-red-500/10"
                                                    >{{ old('rejection_reason') }}</textarea>

                                                    <button
                                                        type="submit"
                                                        class="flex items-center justify-center w-full gap-2 px-4 py-3 text-sm font-bold text-red-200 transition border rounded-xl border-red-400/20 bg-red-500/10 hover:bg-red-500/20 hover:border-red-400/40"
                                                    >
                                                        <span>✕</span>
                                                        رفض الدفعة
                                                    </button>

                                                </form>

                                            </div>

                                        @elseif ($payment->status === 'completed')

                                            <div
                                                class="min-w-[210px] p-4 text-green-200 border rounded-2xl border-green-400/20 bg-green-500/10"
                                            >

                                                <p class="font-bold">
                                                    ✓ تم قبول الدفعة
                                                </p>

                                                @if ($payment->paid_at)

                                                    <p class="mt-2 text-xs text-green-200/70">
                                                        {{ $payment->paid_at->format('Y-m-d H:i') }}
                                                    </p>

                                                @endif

                                                @if ($payment->invoice)

                                                    <div class="grid gap-2 mt-4">

                                                        <a
                                                            href="{{ route(
                                                                'invoices.show',
                                                                $payment->invoice
                                                            ) }}"
                                                            target="_blank"
                                                            class="flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-blue-200 transition border rounded-xl border-blue-400/20 bg-blue-500/10 hover:bg-blue-500/20"
                                                        >
                                                            👁️ عرض الفاتورة
                                                        </a>

                                                        <a
                                                            href="{{ route(
                                                                'invoices.download',
                                                                $payment->invoice
                                                            ) }}"
                                                            class="flex items-center justify-center gap-2 px-3 py-2 text-xs font-bold text-green-200 transition border rounded-xl border-green-400/20 bg-green-500/10 hover:bg-green-500/20"
                                                        >
                                                            📄 تحميل PDF
                                                        </a>

                                                    </div>

                                                @endif

                                            </div>

                                        @elseif ($payment->status === 'rejected')

                                            <div
                                                class="min-w-[230px] p-4 text-red-200 border rounded-2xl border-red-400/20 bg-red-500/10"
                                            >

                                                <p class="font-bold">
                                                    سبب الرفض
                                                </p>

                                                <p class="mt-2 text-sm leading-6 text-red-200/80">
                                                    {{ $payment->rejection_reason
                                                        ?: 'لم يتم تحديد السبب' }}
                                                </p>

                                            </div>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="px-6 py-20 text-center"
                                    >

                                        <div
                                            class="flex items-center justify-center w-16 h-16 mx-auto text-3xl border rounded-2xl border-white/10 bg-white/5"
                                        >
                                            💳
                                        </div>

                                        <h3 class="mt-5 text-lg font-black text-white">
                                            لا توجد دفعات
                                        </h3>

                                        <p class="mt-2 text-sm text-slate-400">
                                            لم يتم إرسال أي طلبات دفع حتى الآن.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if (
                    method_exists($payments, 'hasPages')
                    && $payments->hasPages()
                )

                    <div class="p-6 border-t border-white/10">
                        {{ $payments->links() }}
                    </div>

                @endif

            </section>

        </div>

    </div>

</x-app-layout>
