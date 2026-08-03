<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

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

            <div class="mb-8">
                <p class="text-sm font-bold text-cyan-300">
                    إدارة المكاتب
                </p>

                <h1 class="mt-2 text-3xl font-black text-white">
                    اشتراكات المكاتب الهندسية
                </h1>

                <p class="mt-3 leading-7 text-slate-400">
                    مراجعة إيصالات اشتراك المكاتب واعتماد الاشتراك الشهري أو رفضه.
                </p>
            </div>

            <div class="grid gap-4 mb-8 sm:grid-cols-3">
                <div class="p-5 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                    <p class="text-sm text-yellow-100">
                        قيد المراجعة
                    </p>

                    <p class="mt-2 text-3xl font-black text-yellow-300">
                        {{ $statistics['under_review'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-green-500/20 bg-green-500/10">
                    <p class="text-sm text-green-100">
                        اشتراكات فعالة
                    </p>

                    <p class="mt-2 text-3xl font-black text-green-300">
                        {{ $statistics['active'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <p class="text-sm text-red-100">
                        اشتراكات مرفوضة
                    </p>

                    <p class="mt-2 text-3xl font-black text-red-300">
                        {{ $statistics['rejected'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="overflow-hidden border rounded-3xl border-white/10 bg-slate-900/70">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1200px] text-sm">
                        <thead class="text-slate-300 bg-white/5">
                            <tr>
                                <th class="p-4 text-right">
                                    المكتب
                                </th>

                                <th class="p-4 text-right">
                                    مالك المكتب
                                </th>

                                <th class="p-4 text-right">
                                    القيمة
                                </th>

                                <th class="p-4 text-right">
                                    طريقة الدفع
                                </th>

                                <th class="p-4 text-right">
                                    مرجع الدفع
                                </th>

                                <th class="p-4 text-right">
                                    الحالة
                                </th>

                                <th class="p-4 text-right">
                                    تاريخ الدفع
                                </th>

                                <th class="p-4 text-right">
                                    الإجراء
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/5">
                            @forelse ($subscriptions as $subscription)
                                @php
                                    $statusData = match ($subscription->status) {
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
                                            'class' => 'text-slate-300 bg-white/5 border-white/10',
                                        ],

                                        'rejected' => [
                                            'label' => 'مرفوض',
                                            'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                        ],

                                        'cancelled' => [
                                            'label' => 'ملغي',
                                            'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                                        ],

                                        default => [
                                            'label' => 'بانتظار الدفع',
                                            'class' => 'text-slate-300 bg-white/5 border-white/10',
                                        ],
                                    };
                                @endphp

                                <tr class="align-top transition hover:bg-white/[0.03]">
                                    <td class="p-4">
                                        <div class="font-black text-white">
                                            {{ $subscription->office?->name ?? 'مكتب غير موجود' }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            حالة المكتب:
                                            {{ match ($subscription->office?->status) {
                                                'active' => 'فعال',
                                                'suspended' => 'موقوف عن العمل',
                                                'closed' => 'مغلق',
                                                'rejected' => 'مرفوض',
                                                default => 'قيد المراجعة',
                                            } }}
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        <div class="font-bold text-white">
                                            {{ $subscription->office?->owner?->name ?? 'غير معروف' }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            {{ $subscription->office?->owner?->email }}
                                        </div>
                                    </td>

                                    <td class="p-4 font-black text-white">
                                        {{ number_format(
                                            (float) $subscription->amount,
                                            2
                                        ) }}

                                        {{ $subscription->currency }}
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ match ($subscription->payment_method) {
                                            'bank_transfer' => 'تحويل بنكي',
                                            'wallet' => 'محفظة إلكترونية',
                                            'cash' => 'دفع نقدي',
                                            'other' => 'طريقة أخرى',
                                            default => $subscription->payment_method ?: 'غير محددة',
                                        } }}
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->payment_reference ?: '—' }}
                                    </td>

                                    <td class="p-4">
                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-black border rounded-full {{ $statusData['class'] }}"
                                        >
                                            {{ $statusData['label'] }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-slate-300">
                                        {{ $subscription->paid_at?->format('Y-m-d H:i') ?? '—' }}
                                    </td>

                                    <td class="p-4">
                                        <div class="min-w-[300px] space-y-4">
                                            @if ($subscription->receipt_path)
                                                <a
                                                    href="{{ route(
                                                        'office-subscriptions.receipt',
                                                        $subscription
                                                    ) }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center justify-center w-full px-4 py-2 font-bold text-white transition border rounded-xl border-cyan-500/20 bg-cyan-500/10 hover:bg-cyan-500/20"
                                                >
                                                    عرض الإيصال
                                                </a>
                                            @else
                                                <span class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-bold border text-slate-400 rounded-xl border-white/10 bg-white/5">
                                                    لا يوجد إيصال
                                                </span>
                                            @endif

                                        @if ($subscription->status === 'under_review')
                                            <div class="min-w-[300px] space-y-4">
                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.office-subscriptions.review',
                                                        $subscription
                                                    ) }}"
                                                    class="p-4 border rounded-2xl border-green-500/20 bg-green-500/5"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <input
                                                        type="hidden"
                                                        name="decision"
                                                        value="approve"
                                                    >

                                                    <label class="block mb-2 text-xs font-bold text-slate-300">
                                                        ملاحظات الاعتماد
                                                    </label>

                                                    <textarea
                                                        name="notes"
                                                        rows="2"
                                                        class="w-full px-3 py-2 text-sm text-white border rounded-xl border-white/10 bg-slate-800"
                                                        placeholder="ملاحظات اختيارية"
                                                    ></textarea>

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('هل أنت متأكد من اعتماد اشتراك هذا المكتب لمدة شهر؟')"
                                                        class="w-full px-4 py-2 mt-3 font-black text-white transition bg-green-600 rounded-xl hover:bg-green-500"
                                                    >
                                                        اعتماد الاشتراك
                                                    </button>
                                                </form>

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.office-subscriptions.review',
                                                        $subscription
                                                    ) }}"
                                                    class="p-4 border rounded-2xl border-red-500/20 bg-red-500/5"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <input
                                                        type="hidden"
                                                        name="decision"
                                                        value="reject"
                                                    >

                                                    <label class="block mb-2 text-xs font-bold text-red-200">
                                                        سبب الرفض
                                                    </label>

                                                    <textarea
                                                        name="rejection_reason"
                                                        rows="3"
                                                        required
                                                        class="w-full px-3 py-2 text-sm text-white border rounded-xl border-white/10 bg-slate-800"
                                                        placeholder="اكتب سبب رفض الإيصال"
                                                    ></textarea>

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('هل أنت متأكد من رفض إيصال الاشتراك؟')"
                                                        class="w-full px-4 py-2 mt-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                                                    >
                                                        رفض الإيصال
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="text-xs leading-6 text-slate-400">
                                                @if ($subscription->approved_at)
                                                    تمت المراجعة:
                                                    {{ $subscription->approved_at->format('Y-m-d H:i') }}
                                                @else
                                                    لا يوجد إجراء متاح.
                                                @endif

                                                @if ($subscription->approver)
                                                    <div class="mt-1">
                                                        بواسطة:
                                                        {{ $subscription->approver->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        </div>
                                    </td>
                                </tr>

                                @if (
                                    $subscription->status === 'rejected'
                                    && $subscription->rejection_reason
                                )
                                    <tr>
                                        <td
                                            colspan="8"
                                            class="p-4 border-b border-red-500/10 bg-red-500/5"
                                        >
                                            <span class="font-black text-red-200">
                                                سبب الرفض:
                                            </span>

                                            <span class="text-red-100">
                                                {{ $subscription->rejection_reason }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td
                                        colspan="8"
                                        class="p-12 text-center text-slate-400"
                                    >
                                        لا توجد اشتراكات مكاتب مسجلة حتى الآن.
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
