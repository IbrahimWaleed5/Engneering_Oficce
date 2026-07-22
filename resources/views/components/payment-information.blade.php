@php
    $bank = config('payment.bank', []);
    $wallet = config('payment.wallet', []);
    $instructions = config('payment.instructions', []);

    $bankInformation = [
        [
            'key' => 'bank_name',
            'label' => 'اسم البنك',
            'value' => $bank['bank_name'] ?? '',
        ],
        [
            'key' => 'bank_account_name',
            'label' => 'اسم صاحب الحساب',
            'value' => $bank['account_name'] ?? '',
        ],
        [
            'key' => 'bank_account_number',
            'label' => 'رقم الحساب',
            'value' => $bank['account_number'] ?? '',
            'direction' => 'ltr',
        ],
        [
            'key' => 'bank_iban',
            'label' => 'رقم الآيبان IBAN',
            'value' => $bank['iban'] ?? '',
            'direction' => 'ltr',
        ],
    ];

    $walletInformation = [
        [
            'key' => 'wallet_name',
            'label' => 'اسم المحفظة',
            'value' => $wallet['wallet_name'] ?? '',
        ],
        [
            'key' => 'wallet_account_name',
            'label' => 'اسم صاحب المحفظة',
            'value' => $wallet['account_name'] ?? '',
        ],
        [
            'key' => 'wallet_phone',
            'label' => 'رقم STC Pay',
            'value' => $wallet['phone'] ?? '',
            'direction' => 'ltr',
        ],
    ];
@endphp

<div
    class="space-y-6"
    dir="rtl"
    x-data="{
        copied: null,

        async copyValue(value, key) {
            try {
                if (
                    navigator.clipboard
                    && window.isSecureContext
                ) {
                    await navigator.clipboard.writeText(
                        value
                    );
                } else {
                    const textarea =
                        document.createElement(
                            'textarea'
                        );

                    textarea.value = value;

                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';

                    document.body.appendChild(
                        textarea
                    );

                    textarea.focus();
                    textarea.select();

                    document.execCommand('copy');

                    textarea.remove();
                }

                this.copied = key;

                setTimeout(() => {
                    this.copied = null;
                }, 1800);

            } catch (error) {
                console.error(
                    'تعذر نسخ معلومات الدفع',
                    error
                );
            }
        }
    }"
>

    {{-- الحساب البنكي --}}
    <section
        class="p-6 border glass-panel rounded-[2rem] border-white/10"
    >

        <div class="flex items-center gap-4">

            <div
                class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-blue-500/10"
            >
                🏦
            </div>

            <div>

                <h2
                    class="text-xl font-black text-white"
                >
                    {{ $bank['title']
                        ?? 'التحويل البنكي' }}
                </h2>

                <p
                    class="mt-1 text-sm text-slate-400"
                >
                    حوّل المبلغ إلى الحساب البنكي
                    التالي
                </p>

            </div>

        </div>

        <div class="grid gap-4 mt-6">

            @foreach ($bankInformation as $item)

                <div
                    class="flex flex-col gap-4 p-4 border sm:flex-row sm:items-center sm:justify-between rounded-2xl border-white/10 bg-white/[0.04]"
                >

                    <div class="min-w-0">

                        <p
                            class="text-xs text-slate-500"
                        >
                            {{ $item['label'] }}
                        </p>

                        <p
                            class="mt-1 font-mono font-black text-white break-all"
                            dir="{{ $item['direction']
                                ?? 'rtl' }}"
                        >
                            {{ $item['value'] }}
                        </p>

                    </div>

                    <button
                        type="button"
                        @click='copyValue(
                            @js($item["value"]),
                            @js($item["key"])
                        )'
                        class="inline-flex items-center justify-center flex-none gap-2 px-4 py-2 text-sm font-bold text-blue-200 transition border rounded-xl border-blue-500/20 bg-blue-500/10 hover:bg-blue-500/20"
                    >

                        <span
                            x-show="copied !== @js($item['key'])"
                        >
                            📋 نسخ
                        </span>

                        <span
                            x-show="copied === @js($item['key'])"
                            x-cloak
                        >
                            ✓ تم النسخ
                        </span>

                    </button>

                </div>

            @endforeach

        </div>

    </section>

    {{-- المحفظة الإلكترونية --}}
    <section
        class="p-6 border glass-panel rounded-[2rem] border-white/10"
    >

        <div class="flex items-center gap-4">

            <div
                class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-violet-500/10"
            >
                📱
            </div>

            <div>

                <h2
                    class="text-xl font-black text-white"
                >
                    {{ $wallet['title']
                        ?? 'المحفظة الإلكترونية' }}
                </h2>

                <p
                    class="mt-1 text-sm text-slate-400"
                >
                    يمكنك الدفع من خلال STC Pay
                </p>

            </div>

        </div>

        <div class="grid gap-4 mt-6">

            @foreach ($walletInformation as $item)

                <div
                    class="flex flex-col gap-4 p-4 border sm:flex-row sm:items-center sm:justify-between rounded-2xl border-white/10 bg-white/[0.04]"
                >

                    <div class="min-w-0">

                        <p
                            class="text-xs text-slate-500"
                        >
                            {{ $item['label'] }}
                        </p>

                        <p
                            class="mt-1 font-mono font-black text-white break-all"
                            dir="{{ $item['direction']
                                ?? 'rtl' }}"
                        >
                            {{ $item['value'] }}
                        </p>

                    </div>

                    <button
                        type="button"
                        @click='copyValue(
                            @js($item["value"]),
                            @js($item["key"])
                        )'
                        class="inline-flex items-center justify-center flex-none gap-2 px-4 py-2 text-sm font-bold transition border text-violet-200 rounded-xl border-violet-500/20 bg-violet-500/10 hover:bg-violet-500/20"
                    >

                        <span
                            x-show="copied !== @js($item['key'])"
                        >
                            📋 نسخ
                        </span>

                        <span
                            x-show="copied === @js($item['key'])"
                            x-cloak
                        >
                            ✓ تم النسخ
                        </span>

                    </button>

                </div>

            @endforeach

        </div>

    </section>

    {{-- تعليمات الدفع --}}
    @if (count($instructions) > 0)

        <section
            class="p-5 border rounded-2xl border-yellow-500/20 bg-yellow-500/10"
        >

            <h3
                class="font-black text-yellow-200"
            >
                ⚠️ تعليمات مهمة
            </h3>

            <ul
                class="mt-3 space-y-2 text-sm leading-7 list-disc list-inside text-yellow-100/80"
            >

                @foreach ($instructions as $instruction)

                    <li>
                        {{ $instruction }}
                    </li>

                @endforeach

            </ul>

        </section>

    @endif

</div>
