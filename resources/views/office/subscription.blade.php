<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $officeStatus = match ($office->status) {
                    'active' => [
                        'label' => 'مكتب فعال',
                        'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                    ],

                    'suspended' => [
                        'label' => 'مكتب موقوف عن العمل',
                        'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                    ],

                    'closed' => [
                        'label' => 'مكتب مغلق',
                        'class' => 'text-slate-200 border-slate-500/20 bg-slate-500/10',
                    ],

                    default => [
                        'label' => 'قيد المراجعة',
                        'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                    ],
                };

                $subscriptionStatus = match ($office->subscription_status) {
                    'active' => [
                        'label' => 'الاشتراك فعال',
                        'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                    ],

                    'expired' => [
                        'label' => 'الاشتراك منتهي',
                        'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                    ],

                    'cancelled' => [
                        'label' => 'الاشتراك ملغي',
                        'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                    ],

                    default => [
                        'label' => 'بانتظار اعتماد الاشتراك',
                        'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                    ],
                };

                $underReview = $subscriptions
                    ->firstWhere('status', 'under_review');
            @endphp

            <div class="flex flex-col gap-5 mb-8 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">
                        إدارة اشتراك المكتب
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        {{ $office->name }}
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        ارفع إيصال دفع الاشتراك الشهري ليقوم مدير النظام بمراجعته واعتماده.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $officeStatus['class'] }}">
                        {{ $officeStatus['label'] }}
                    </span>

                    <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $subscriptionStatus['class'] }}">
                        {{ $subscriptionStatus['label'] }}
                    </span>
                </div>
            </div>

            @if ($office->status === 'suspended')
                <div class="p-6 mb-8 border rounded-3xl border-red-500/30 bg-red-500/10">
                    <h2 class="text-xl font-black text-red-200">
                        مكتب موقوف عن العمل
                    </h2>

                    <p class="mt-2 leading-8 text-red-100">
                        يمكنك الاطلاع على بيانات الاشتراك، لكن المكتب لا يستقبل استشارات أو طلبات انضمام جديدة حتى يقوم مدير النظام بإعادة تفعيله.
                    </p>

                    @if ($office->suspension_reason)
                        <div class="p-4 mt-4 border rounded-2xl border-red-500/20 bg-red-950/20">
                            <p class="text-sm font-black text-red-200">
                                سبب الإيقاف
                            </p>

                            <p class="mt-2 leading-7 text-red-100">
                                {{ $office->suspension_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70 sm:p-8">
                        <h2 class="text-xl font-black text-white">
                            رفع إيصال الاشتراك
                        </h2>

                        <p class="mt-2 leading-7 text-slate-400">
                            قيمة الاشتراك الشهري:
                            <span class="font-black text-white">
                                {{ number_format(
                                    (float) $office->monthly_subscription_amount,
                                    2
                                ) }}
                                {{ $office->subscription_currency }}
                            </span>
                        </p>

                        @if ($underReview)
                            <div class="p-5 mt-6 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                <p class="font-black text-yellow-200">
                                    يوجد إيصال قيد المراجعة
                                </p>

                                <p class="mt-2 leading-7 text-yellow-100">
                                    تم رفع الإيصال بتاريخ
                                    {{ $underReview->paid_at?->format('Y-m-d H:i') ?? $underReview->created_at?->format('Y-m-d H:i') }}.
                                    انتظر قرار مدير النظام قبل رفع إيصال جديد.
                                </p>
                            </div>
                        @elseif (
                            $office->subscription_status === 'active'
                            && $office->subscription_ends_at?->isFuture()
                        )
                            <div class="p-5 mt-6 border rounded-2xl border-green-500/20 bg-green-500/10">
                                <p class="font-black text-green-200">
                                    اشتراك المكتب فعال
                                </p>

                                <p class="mt-2 leading-7 text-green-100">
                                    الاشتراك فعال حتى
                                    {{ $office->subscription_ends_at->format('Y-m-d') }}.
                                </p>
                            </div>
                        @elseif (
                            ! in_array(
                                $office->status,
                                ['closed', 'rejected'],
                                true
                            )
                        )
                            <form
                                method="POST"
                                action="{{ route('office.subscription.store') }}"
                                enctype="multipart/form-data"
                                class="space-y-6 mt-7"
                            >
                                @csrf

                                <div>
                                    <label class="block mb-2 font-bold text-white">
                                        طريقة الدفع
                                    </label>

                                    <select
                                        name="payment_method"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >
                                        <option value="">
                                            اختر طريقة الدفع
                                        </option>

                                        <option
                                            value="bank_transfer"
                                            @selected(old('payment_method') === 'bank_transfer')
                                        >
                                            تحويل بنكي
                                        </option>

                                        <option
                                            value="wallet"
                                            @selected(old('payment_method') === 'wallet')
                                        >
                                            محفظة إلكترونية
                                        </option>

                                        <option
                                            value="cash"
                                            @selected(old('payment_method') === 'cash')
                                        >
                                            دفع نقدي
                                        </option>

                                        <option
                                            value="other"
                                            @selected(old('payment_method') === 'other')
                                        >
                                            طريقة أخرى
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 font-bold text-white">
                                        رقم أو مرجع عملية الدفع
                                    </label>

                                    <input
                                        type="text"
                                        name="payment_reference"
                                        value="{{ old('payment_reference') }}"
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        placeholder="مثال: رقم الحوالة أو العملية"
                                    >
                                </div>

                                <div>
                                    <label class="block mb-2 font-bold text-white">
                                        إيصال الدفع
                                    </label>

                                    <input
                                        type="file"
                                        name="receipt"
                                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >

                                    <p class="mt-2 text-xs leading-6 text-slate-400">
                                        PDF أو صورة، وبحد أقصى 10 ميجابايت.
                                    </p>
                                </div>

                                <div>
                                    <label class="block mb-2 font-bold text-white">
                                        ملاحظات
                                    </label>

                                    <textarea
                                        name="notes"
                                        rows="4"
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        placeholder="أي معلومات إضافية عن عملية الدفع"
                                    >{{ old('notes') }}</textarea>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500 sm:w-auto"
                                >
                                    رفع الإيصال وإرساله للمراجعة
                                </button>
                            </form>
                        @else
                            <div class="p-5 mt-6 border rounded-2xl border-red-500/20 bg-red-500/10">
                                <p class="font-black text-red-200">
                                    لا يمكن رفع اشتراك
                                </p>

                                <p class="mt-2 leading-7 text-red-100">
                                    لا يمكن دفع اشتراك لمكتب مغلق أو مرفوض.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            ملخص الاشتراك
                        </h2>

                        <div class="mt-6 space-y-4">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    قيمة الاشتراك
                                </p>

                                <p class="mt-2 text-xl font-black text-white">
                                    {{ number_format(
                                        (float) $office->monthly_subscription_amount,
                                        2
                                    ) }}
                                    {{ $office->subscription_currency }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    بداية الاشتراك
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $office->subscription_starts_at?->format('Y-m-d') ?? 'لم يبدأ بعد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    نهاية الاشتراك
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $office->subscription_ends_at?->format('Y-m-d') ?? 'غير محددة' }}
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('office.dashboard') }}"
                            class="inline-flex items-center justify-center w-full px-5 py-3 mt-6 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            العودة إلى لوحة المكتب
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden border rounded-3xl border-white/10 bg-slate-900/70">
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-xl font-black text-white">
                        سجل اشتراكات المكتب
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-sm">
                        <thead class="text-slate-300 bg-white/5">
                            <tr>
                                <th class="p-4 text-right">القيمة</th>
                                <th class="p-4 text-right">طريقة الدفع</th>
                                <th class="p-4 text-right">الحالة</th>
                                <th class="p-4 text-right">تاريخ الدفع</th>
                                <th class="p-4 text-right">بداية الاشتراك</th>
                                <th class="p-4 text-right">نهاية الاشتراك</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/5">
                            @forelse ($subscriptions as $subscription)
                                @php
                                    $subscriptionRowStatus = match ($subscription->status) {
                                        'active' => [
                                            'label' => 'فعال',
                                            'class' => 'text-green-200 bg-green-500/10 border-green-500/20',
                                        ],

                                        'under_review' => [
                                            'label' => 'قيد المراجعة',
                                            'class' => 'text-yellow-200 bg-yellow-500/10 border-yellow-500/20',
                                        ],

                                        'expired' => [
                                            'label' => 'منتهي',
                                            'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                        ],

                                        'rejected' => [
                                            'label' => 'مرفوض',
                                            'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                        ],

                                        'cancelled' => [
                                            'label' => 'ملغي',
                                            'class' => 'text-slate-300 bg-white/5 border-white/10',
                                        ],

                                        default => [
                                            'label' => 'بانتظار الدفع',
                                            'class' => 'text-slate-300 bg-white/5 border-white/10',
                                        ],
                                    };
                                @endphp

                                <tr>
                                    <td class="p-4 font-black text-white">
                                        {{ number_format(
                                            (float) $subscription->amount,
                                            2
                                        ) }}
                                        {{ $subscription->currency }}
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->payment_method ?: 'غير محددة' }}
                                    </td>

                                    <td class="p-4">
                                        <span class="inline-flex px-3 py-1 text-xs font-black border rounded-full {{ $subscriptionRowStatus['class'] }}">
                                            {{ $subscriptionRowStatus['label'] }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->paid_at?->format('Y-m-d H:i') ?? '—' }}
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->starts_at?->format('Y-m-d') ?? '—' }}
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->ends_at?->format('Y-m-d') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="6"
                                        class="p-12 text-center text-slate-400"
                                    >
                                        لا توجد اشتراكات مسجلة لهذا المكتب.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($subscriptions->hasPages())
                <div class="mt-8">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
