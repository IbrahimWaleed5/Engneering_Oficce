<x-app-layout>

    <div
        class="py-10"
        dir="rtl"
    >

        <div class="max-w-2xl px-4 mx-auto sm:px-6 lg:px-8">

            <div
                class="p-6 border shadow-xl sm:p-8 rounded-3xl bg-slate-900 border-slate-800"
            >

                <div class="mb-8 text-center">

                    <div class="mb-4 text-5xl">
                        ⭐
                    </div>

                    <h1 class="text-3xl font-black text-white">
                        تقييم المهندس
                    </h1>

                    <p class="mt-3 text-slate-400">
                        قيّم تجربتك مع المهندس
                        {{ $consultation->engineer?->name }}
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        الاستشارة:
                        {{ $consultation->consultation_number }}
                    </p>

                </div>

                @if ($errors->any())

                    <div
                        class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/30 bg-red-500/10"
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

                <form
                    method="POST"
                    action="{{ route(
                        'engineer-reviews.store',
                        $consultation
                    ) }}"
                >

                    @csrf

                    <div class="mb-8">

                        <p
                            class="mb-4 text-lg font-bold text-center text-white"
                        >
                            اختر التقييم
                        </p>

                        <div
                            class="flex flex-row-reverse justify-center gap-2 rating-stars"
                        >

                            @for ($star = 5; $star >= 1; $star--)

                                <input
                                    id="rating-{{ $star }}"
                                    name="rating"
                                    type="radio"
                                    value="{{ $star }}"
                                    class="hidden peer"
                                    @checked(
                                        (int) old('rating') === $star
                                    )
                                >

                                <label
                                    for="rating-{{ $star }}"
                                    title="{{ $star }} نجوم"
                                    class="text-5xl transition cursor-pointer text-slate-600 hover:text-yellow-400 peer-checked:text-yellow-400"
                                >
                                    ★
                                </label>

                            @endfor

                        </div>

                        @error('rating')

                            <p class="mt-3 text-sm text-center text-red-400">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div class="mb-6">

                        <label
                            for="comment"
                            class="block mb-2 font-bold text-slate-300"
                        >
                            تعليقك

                            <span class="font-normal text-slate-500">
                                — اختياري
                            </span>
                        </label>

                        <textarea
                            id="comment"
                            name="comment"
                            rows="6"
                            maxlength="2000"
                            placeholder="اكتب رأيك في تعامل المهندس وجودة العمل..."
                            class="w-full px-4 py-3 text-white border outline-none resize-none rounded-2xl border-slate-700 bg-slate-950 focus:border-yellow-500"
                        >{{ old('comment') }}</textarea>

                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center flex-1 gap-2 px-6 py-3 font-black text-white transition bg-yellow-600 rounded-2xl hover:bg-yellow-500"
                        >
                            ⭐ إرسال التقييم
                        </button>

                        <a
                            href="{{ route('consultations.mine') }}"
                            class="inline-flex items-center justify-center px-6 py-3 font-bold transition border rounded-2xl border-slate-700 text-slate-300 hover:bg-slate-800"
                        >
                            إلغاء
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
