<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">
            تقييم الاستشارة
        </h2>
    </x-slot>

    <div class="py-10" dir="rtl">
        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-300 border rounded-xl border-red-500/30 bg-red-500/10">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden border shadow-xl rounded-2xl border-slate-700 bg-slate-900">

                <div class="p-6 border-b border-slate-700 bg-slate-800/70">
                    <p class="text-sm text-slate-400">
                        رقم الاستشارة
                    </p>

                    <h3 class="mt-1 text-lg font-bold text-white">
                        {{ $consultation->consultation_number }}
                    </h3>

                    <p class="mt-2 text-slate-300">
                        {{ $consultation->title }}
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('reviews.store', $consultation) }}"
                    class="p-6 space-y-6"
                >
                    @csrf

                    <div>
                        <label class="block mb-3 font-bold text-slate-200">
                            تقييمك للاستشارة
                        </label>

                        <div class="flex flex-row-reverse justify-end gap-2">
                            @for ($i = 5; $i >= 1; $i--)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="rating"
                                        value="{{ $i }}"
                                        class="sr-only peer"
                                        {{ old('rating') == $i ? 'checked' : '' }}
                                    >

                                    <span class="text-4xl transition text-slate-600 hover:text-yellow-400 peer-checked:text-yellow-400">
                                        ★
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label
                            for="comment"
                            class="block mb-2 font-bold text-slate-200"
                        >
                            رأيك وملاحظاتك
                        </label>

                        <textarea
                            id="comment"
                            name="comment"
                            rows="6"
                            maxlength="2000"
                            required
                            placeholder="اكتب رأيك في جودة الخدمة والتعامل والنتيجة النهائية..."
                            class="w-full text-white rounded-xl border-slate-600 bg-slate-800 placeholder-slate-500 focus:border-blue-500 focus:ring-blue-500"
                        >{{ old('comment') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('consultations.my') }}"
                            class="px-5 py-3 transition border rounded-xl border-slate-600 text-slate-300 hover:bg-slate-800"
                        >
                            رجوع
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-3 font-bold text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                        >
                            إرسال التقييم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
