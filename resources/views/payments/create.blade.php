<x-app-layout>

    <div
        class="relative py-12"
        dir="rtl"
    >

        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            <x-page-header
                title="إكمال الدفع"
                description="ارفع إيصال الدفع ليتم فحصه وتفعيل طلب الاستشارة"
                icon="💳"
            />

            <x-alerts />

            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">

                {{-- معلومات الدفع --}}
                <div class="lg:col-span-2">
                    <x-payment-information />
                </div>

                {{-- نموذج الدفع --}}

                <section
                    class="p-6 glass-panel rounded-[2rem] fade-up md:p-8"
                >

                    <form
                        method="POST"
                        action="{{ route('payments.store', $consultation) }}"
                        enctype="multipart/form-data"
                        x-data="{
                            paymentMethod: '{{ old('payment_method', '') }}',
                            receiptName: '',
                            receiptPreview: ''
                        }"
                    >

                        @csrf

                        <div class="space-y-6">

                            {{-- طريقة الدفع --}}

                            <div>

                                <label class="block mb-3 text-sm font-bold text-slate-200">
                                    طريقة الدفع
                                    <span class="text-red-400">*</span>
                                </label>

                                <div class="grid gap-3 sm:grid-cols-2">

                                    @php
                                        $methods = [
                                            [
                                                'value' => 'cash',
                                                'icon' => '💵',
                                                'title' => 'نقدًا',
                                                'description' => 'دفع نقدي للمكتب',
                                            ],
                                            [
                                                'value' => 'card',
                                                'icon' => '💳',
                                                'title' => 'بطاقة',
                                                'description' => 'دفع بواسطة البطاقة',
                                            ],
                                            [
                                                'value' => 'bank',
                                                'icon' => '🏦',
                                                'title' => 'تحويل بنكي',
                                                'description' => 'تحويل إلى حساب المكتب',
                                            ],
                                            [
                                                'value' => 'wallet',
                                                'icon' => '📱',
                                                'title' => 'محفظة إلكترونية',
                                                'description' => 'تحويل عبر المحفظة',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($methods as $method)

                                        <label
                                            :class="paymentMethod === '{{ $method['value'] }}'
                                                ? 'border-cyan-400 bg-cyan-500/10 ring-2 ring-cyan-400/10'
                                                : 'border-white/10 bg-white/[0.03] hover:border-white/20'"
                                            class="flex gap-4 p-4 transition border cursor-pointer rounded-2xl"
                                        >

                                            <input
                                                type="radio"
                                                name="payment_method"
                                                value="{{ $method['value'] }}"
                                                x-model="paymentMethod"
                                                class="hidden"
                                                required
                                            >

                                            <div
                                                class="flex items-center justify-center flex-none w-12 h-12 text-2xl rounded-xl bg-white/5"
                                            >
                                                {{ $method['icon'] }}
                                            </div>

                                            <div>

                                                <p class="font-black text-white">
                                                    {{ $method['title'] }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-400">
                                                    {{ $method['description'] }}
                                                </p>

                                            </div>

                                        </label>

                                    @endforeach

                                </div>

                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- الرقم المرجعي --}}

                            <div
                                x-show="
                                    paymentMethod === 'card'
                                    || paymentMethod === 'bank'
                                    || paymentMethod === 'wallet'
                                "
                                x-transition
                            >

                                <label
                                    for="transaction_reference"
                                    class="block mb-2 text-sm font-bold text-slate-200"
                                >
                                    رقم العملية أو التحويل
                                </label>

                                <input
                                    id="transaction_reference"
                                    type="text"
                                    name="transaction_reference"
                                    value="{{ old('transaction_reference') }}"
                                    placeholder="أدخل رقم العملية إن وجد"
                                    class="form-control"
                                >

                                @error('transaction_reference')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- إيصال الدفع --}}

                            <div>

                                <label class="block mb-3 text-sm font-bold text-slate-200">
                                    إيصال الدفع
                                    <span class="text-red-400">*</span>
                                </label>

                                <label
                                    class="relative flex flex-col items-center justify-center p-8 overflow-hidden text-center transition border-2 border-dashed cursor-pointer rounded-2xl border-white/10 bg-white/[0.03] hover:border-cyan-400/40"
                                >

                                    <input
                                        type="file"
                                        name="receipt_image"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        required
                                        class="hidden"
                                        @change="
                                            const file = $event.target.files[0];

                                            receiptName = file ? file.name : '';

                                            if (
                                                file
                                                && file.type.startsWith('image/')
                                            ) {
                                                receiptPreview =
                                                    URL.createObjectURL(file);
                                            } else {
                                                receiptPreview = '';
                                            }
                                        "
                                    >

                                    <template x-if="!receiptPreview">

                                        <div>

                                            <div class="text-5xl">
                                                🧾
                                            </div>

                                            <p class="mt-4 font-black text-white">
                                                اختر صورة إيصال الدفع
                                            </p>

                                            <p class="mt-2 text-sm text-slate-400">
                                                JPG، PNG، WEBP أو PDF حتى 5MB
                                            </p>

                                        </div>

                                    </template>

                                    <template x-if="receiptPreview">

                                        <img
                                            :src="receiptPreview"
                                            alt="معاينة الإيصال"
                                            class="object-contain max-h-72 rounded-xl"
                                        >

                                    </template>

                                    <div
                                        x-cloak
                                        x-show="receiptName"
                                        class="px-4 py-2 mt-4 text-sm font-bold text-cyan-200 rounded-xl bg-cyan-500/10"
                                    >
                                        <span x-text="receiptName"></span>
                                    </div>

                                </label>

                                @error('receipt_image')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                        <div
                            class="flex flex-col gap-3 mt-8 border-t pt-7 sm:flex-row border-white/10"
                        >

                            <button
                                type="submit"
                                class="flex-1 primary-button"
                            >
                                إرسال إيصال الدفع
                                <span>✓</span>
                            </button>

                            <a
                                href="{{ route('consultations.mine') }}"
                                class="secondary-button"
                            >
                                الدفع لاحقًا
                            </a>

                        </div>

                    </form>

                </section>

                {{-- ملخص الطلب --}}

                <aside class="space-y-5">

                    <div class="p-6 glass-card rounded-[2rem] fade-up delay-100">

                        <p class="text-sm font-bold text-slate-400">
                            ملخص الاستشارة
                        </p>

                        <h2 class="mt-4 text-xl font-black text-white">
                            {{ $consultation->title }}
                        </h2>

                        <div class="mt-6 space-y-4">

                            <div class="flex items-center justify-between gap-4">

                                <span class="text-sm text-slate-400">
                                    رقم الاستشارة
                                </span>

                                <span class="text-sm font-bold text-white">
                                    {{ $consultation->consultation_number }}
                                </span>

                            </div>

                            <div class="h-px bg-white/10"></div>

                            <div class="flex items-center justify-between gap-4">

                                <span class="text-sm text-slate-400">
                                    حالة الطلب
                                </span>

                                <span
                                    class="text-yellow-200 status-badge bg-yellow-500/10"
                                >
                                    بانتظار الدفع
                                </span>

                            </div>

                            <div class="h-px bg-white/10"></div>

                            <div class="flex items-center justify-between gap-4">

                                <span class="text-sm text-slate-400">
                                    المبلغ المطلوب
                                </span>

                                <span class="text-2xl font-black text-cyan-300">
                                    {{ number_format(
                                        $consultation->final_price,
                                        2
                                    ) }}
                                    شيكل
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="p-6 glass-card rounded-[2rem] fade-up delay-200">

                        <div
                            class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-green-500/10"
                        >
                            🔒
                        </div>

                        <h3 class="mt-5 text-lg font-black text-white">
                            مراجعة آمنة
                        </h3>

                        <p class="mt-3 text-sm leading-7 text-slate-400">
                            سيتم إرسال إيصال الدفع إلى المدير للفحص. بعد
                            الموافقة يتم تفعيل الاستشارة وإشعار المهندس
                            المختار.
                        </p>

                    </div>

                    <div class="p-5 border glass-panel rounded-2xl border-yellow-400/10">

                        <div class="flex gap-3">

                            <span class="text-xl">
                                ⚠️
                            </span>

                            <p class="text-sm leading-7 text-yellow-100/80">
                                تأكد من وضوح صورة الإيصال وظهور المبلغ ورقم
                                العملية قبل الإرسال.
                            </p>

                        </div>

                    </div>

                </aside>

            </div>

        </div>

    </div>

</x-app-layout>

