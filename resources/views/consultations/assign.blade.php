<x-app-layout>

    <div
        class="relative py-12"
        dir="rtl"
    >

        <div
            class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8"
        >

            <x-page-header
                title="تعيين مهندس وموعد التسليم"
                description="اختر المهندس وحدد تاريخ بدء العمل وموعد التسليم المتوقع"
                icon="👷"
            />

            <x-alerts />

            <div
                class="grid gap-8 lg:grid-cols-[1fr_300px]"
            >

                <section
                    class="p-6 glass-panel rounded-[2rem] md:p-8"
                >

                    <form
                        method="POST"
                        action="{{ route(
                            'consultations.assign',
                            $consultation
                        ) }}"
                        x-data="{
                            selectedEngineer: '{{ old(
                                'engineer_id',
                                $consultation->engineer_id
                            ) }}'
                        }"
                    >

                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="in_progress"
                        >

                        {{-- اختيار المهندس --}}
                        <div>

                            <h2
                                class="mb-4 text-lg font-black text-white"
                            >
                                اختر المهندس
                            </h2>

                            <div class="space-y-4">

                                @forelse ($engineers as $engineer)

                                    <label
                                        :class="
                                            selectedEngineer == '{{ $engineer->id }}'
                                                ? 'border-cyan-400 bg-cyan-500/10 ring-2 ring-cyan-400/10'
                                                : 'border-white/10 bg-white/[0.03] hover:border-white/20'
                                        "
                                        class="flex items-center justify-between gap-5 p-5 transition border cursor-pointer rounded-2xl"
                                    >

                                        <input
                                            type="radio"
                                            name="engineer_id"
                                            value="{{ $engineer->id }}"
                                            x-model="selectedEngineer"
                                            class="hidden"
                                            required
                                        >

                                        <div
                                            class="flex items-center gap-4"
                                        >

                                            @if ($engineer->profile_photo)

                                                <img
                                                    src="{{ asset(
                                                        'storage/' .
                                                        $engineer
                                                            ->profile_photo
                                                    ) }}"
                                                    alt="{{ $engineer->name }}"
                                                    class="flex-none object-cover border rounded-full w-14 h-14 border-cyan-400/20"
                                                >

                                            @else

                                                <div
                                                    class="flex items-center justify-center flex-none text-xl font-black text-white border rounded-full w-14 h-14 border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500"
                                                >
                                                    {{ mb_substr(
                                                        $engineer->name,
                                                        0,
                                                        1
                                                    ) }}
                                                </div>

                                            @endif

                                            <div>

                                                <h3
                                                    class="font-black text-white"
                                                >
                                                    {{ $engineer->name }}
                                                </h3>

                                                <p
                                                    class="mt-1 text-sm text-slate-400"
                                                >
                                                    {{ $engineer->email }}
                                                </p>

                                                @if (
                                                    $engineer
                                                        ->employeeProfile
                                                        ?->specialty
                                                )

                                                    <p
                                                        class="mt-1 text-xs text-cyan-300"
                                                    >
                                                        {{ $engineer
                                                            ->employeeProfile
                                                            ->specialty
                                                            ->name }}
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                        <div
                                            x-show="selectedEngineer == '{{ $engineer->id }}'"
                                            class="flex items-center justify-center w-8 h-8 text-sm font-black rounded-full bg-cyan-500 text-slate-950"
                                        >
                                            ✓
                                        </div>

                                    </label>

                                @empty

                                    <div
                                        class="p-10 text-center rounded-2xl bg-white/5"
                                    >

                                        <div class="text-5xl">
                                            👷
                                        </div>

                                        <p
                                            class="mt-4 text-slate-400"
                                        >
                                            لا يوجد مهندسون متاحون حاليًا.
                                        </p>

                                    </div>

                                @endforelse

                            </div>

                            @error('engineer_id')

                                <p
                                    class="mt-4 text-sm text-red-300"
                                >
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        {{-- مواعيد التنفيذ --}}
                        <div
                            class="pt-8 mt-8 border-t border-white/10"
                        >

                            <h2
                                class="text-lg font-black text-white"
                            >
                                مواعيد تنفيذ الاستشارة
                            </h2>

                            <p
                                class="mt-2 text-sm text-slate-400"
                            >
                                حدد تاريخ بدء العمل وموعد التسليم المتوقع.
                            </p>

                            <div
                                class="grid gap-5 mt-6 sm:grid-cols-2"
                            >

                                <div>

                                    <label
                                        for="started_at"
                                        class="block mb-2 text-sm font-bold text-slate-300"
                                    >
                                        تاريخ بدء العمل
                                    </label>

                                    <input
                                        id="started_at"
                                        type="datetime-local"
                                        name="started_at"
                                        value="{{ old(
                                            'started_at',
                                            $consultation->started_at
                                                ?->format(
                                                    'Y-m-d\TH:i'
                                                )
                                            ?? now()->format(
                                                'Y-m-d\TH:i'
                                            )
                                        ) }}"
                                        min="{{ now()->format(
                                            'Y-m-d\TH:i'
                                        ) }}"
                                        class="w-full px-4 py-3 text-white border rounded-2xl border-white/10 bg-slate-950/70 focus:border-cyan-400 focus:ring-cyan-400"
                                        required
                                    >

                                    @error('started_at')

                                        <p
                                            class="mt-2 text-sm text-red-300"
                                        >
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                                <div>

                                    <label
                                        for="expected_delivery_at"
                                        class="block mb-2 text-sm font-bold text-slate-300"
                                    >
                                        موعد التسليم المتوقع
                                    </label>

                                    <input
                                        id="expected_delivery_at"
                                        type="datetime-local"
                                        name="expected_delivery_at"
                                        value="{{ old(
                                            'expected_delivery_at',
                                            $consultation
                                                ->expected_delivery_at
                                                ?->format(
                                                    'Y-m-d\TH:i'
                                                )
                                        ) }}"
                                        min="{{ now()->format(
                                            'Y-m-d\TH:i'
                                        ) }}"
                                        class="w-full px-4 py-3 text-white border rounded-2xl border-white/10 bg-slate-950/70 focus:border-cyan-400 focus:ring-cyan-400"
                                        required
                                    >

                                    @error('expected_delivery_at')

                                        <p
                                            class="mt-2 text-sm text-red-300"
                                        >
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                            </div>

                            <div
                                class="p-4 mt-5 text-sm text-yellow-100 border rounded-2xl border-yellow-500/20 bg-yellow-500/10"
                            >
                                يجب أن يكون موعد التسليم بعد
                                تاريخ بدء العمل.
                            </div>

                        </div>

                        @error('status')

                            <p
                                class="mt-4 text-sm text-red-300"
                            >
                                {{ $message }}
                            </p>

                        @enderror

                        @if ($engineers->count() > 0)

                            <div
                                class="flex flex-col gap-3 mt-8 border-t pt-7 sm:flex-row border-white/10"
                            >

                                <button
                                    type="submit"
                                    class="flex-1 primary-button"
                                >
                                    حفظ المهندس وموعد التسليم
                                </button>

                                <a
                                    href="{{ route(
                                        'consultations.index'
                                    ) }}"
                                    class="secondary-button"
                                >
                                    إلغاء
                                </a>

                            </div>

                        @endif

                    </form>

                </section>

                <aside class="space-y-5">

                    <div
                        class="p-6 glass-card rounded-[2rem]"
                    >

                        <p
                            class="text-sm font-bold text-slate-400"
                        >
                            الاستشارة
                        </p>

                        <h2
                            class="mt-4 text-xl font-black text-white"
                        >
                            {{ $consultation->title }}
                        </h2>

                        <div class="mt-5 space-y-3 text-sm">

                            <div
                                class="flex justify-between gap-3"
                            >

                                <span class="text-slate-500">
                                    العميل
                                </span>

                                <span class="font-bold">
                                    {{ $consultation
                                        ->customer
                                        ?->name }}
                                </span>

                            </div>

                            <div
                                class="flex justify-between gap-3"
                            >

                                <span class="text-slate-500">
                                    النوع
                                </span>

                                <span class="font-bold">
                                    {{ $consultation
                                        ->consultationType
                                        ?->name }}
                                </span>

                            </div>

                            <div
                                class="flex justify-between gap-3"
                            >

                                <span class="text-slate-500">
                                    السعر
                                </span>

                                <span
                                    class="font-black text-cyan-300"
                                >
                                    {{ number_format(
                                        $consultation
                                            ->final_price,
                                        2
                                    ) }}
                                    ₪
                                </span>

                            </div>

                            <div
                                class="flex justify-between gap-3"
                            >

                                <span class="text-slate-500">
                                    حالة الدفع
                                </span>

                                <span
                                    class="font-bold text-green-300"
                                >
                                    {{ $consultation
                                        ->payment_status
                                        === 'paid'
                                        ? 'تم الدفع'
                                        : $consultation
                                            ->payment_status }}
                                </span>

                            </div>

                        </div>

                    </div>

                    <div
                        class="p-5 border rounded-2xl border-blue-400/10 bg-blue-500/10"
                    >

                        <p
                            class="text-sm leading-7 text-blue-100"
                        >
                            عند تعيين المهندس سيتم إرسال إشعار
                            له، وسيظهر موعد التسليم للمهندس
                            والعميل.
                        </p>

                    </div>

                </aside>

            </div>

        </div>

    </div>

</x-app-layout>
