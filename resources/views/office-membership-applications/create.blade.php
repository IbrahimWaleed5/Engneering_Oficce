<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

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

            <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70 sm:p-8">
                <div class="pb-6 border-b border-white/10">
                    <p class="text-sm font-bold text-cyan-300">
                        طلب انضمام إلى مكتب هندسي
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        {{ $office->name }}
                    </h1>

                    <p class="mt-3 leading-8 text-slate-400">
                        أرسل بياناتك المهنية إلى مدير المكتب.
                        لا يوجد دفع أو رفع إيصال من المهندس.
                    </p>
                </div>

                <div class="p-5 mt-6 border rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    <p class="font-black text-cyan-200">
                        الملفات المطلوبة
                    </p>

                    <ul class="mt-3 space-y-2 text-sm leading-7 text-cyan-100">
                        <li>• السيرة الذاتية CV.</li>
                        <li>• الشهادة الجامعية أو المهنية.</li>
                        <li>• تحديد التخصص الهندسي.</li>
                    </ul>
                </div>

                <form
                    method="POST"
                    action="{{ route(
                        'office-membership-applications.store',
                        $office
                    ) }}"
                    enctype="multipart/form-data"
                    class="mt-8 space-y-6"
                >
                    @csrf

                    <div>
                        <label
                            for="specialty_id"
                            class="block mb-2 font-bold text-white"
                        >
                            التخصص الهندسي
                        </label>

                        <select
                            id="specialty_id"
                            name="specialty_id"
                            required
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >
                            <option value="">
                                اختر التخصص
                            </option>

                            @foreach ($specialties as $specialty)
                                <option
                                    value="{{ $specialty->id }}"
                                    @selected(
                                        (string) old('specialty_id')
                                        === (string) $specialty->id
                                    )
                                >
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="requested_position"
                                class="block mb-2 font-bold text-white"
                            >
                                المسمى الوظيفي المطلوب
                            </label>

                            <input
                                id="requested_position"
                                type="text"
                                name="requested_position"
                                value="{{ old('requested_position') }}"
                                maxlength="150"
                                placeholder="مثال: مهندس معماري"
                                class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                            >
                        </div>

                        <div>
                            <label
                                for="years_of_experience"
                                class="block mb-2 font-bold text-white"
                            >
                                سنوات الخبرة
                            </label>

                            <input
                                id="years_of_experience"
                                type="number"
                                name="years_of_experience"
                                value="{{ old('years_of_experience') }}"
                                min="0"
                                max="60"
                                placeholder="0"
                                class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="cv"
                            class="block mb-2 font-bold text-white"
                        >
                            السيرة الذاتية CV
                        </label>

                        <input
                            id="cv"
                            type="file"
                            name="cv"
                            accept=".pdf,.doc,.docx"
                            required
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >

                        <p class="mt-2 text-xs leading-6 text-slate-400">
                            PDF أو Word، بحد أقصى 10 ميجابايت.
                        </p>
                    </div>

                    <div>
                        <label
                            for="certificate"
                            class="block mb-2 font-bold text-white"
                        >
                            الشهادة الجامعية أو المهنية
                        </label>

                        <input
                            id="certificate"
                            type="file"
                            name="certificate"
                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                            required
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >

                        <p class="mt-2 text-xs leading-6 text-slate-400">
                            PDF أو صورة، بحد أقصى 10 ميجابايت.
                        </p>
                    </div>

                    <div>
                        <label
                            for="message"
                            class="block mb-2 font-bold text-white"
                        >
                            رسالة إلى مدير المكتب
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            maxlength="3000"
                            placeholder="اكتب نبذة مختصرة عن خبرتك وسبب رغبتك في الانضمام..."
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >{{ old('message') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                        >
                            إرسال طلب الانضمام
                        </button>

                        <a
                            href="{{ route(
                                'engineering-offices.show',
                                $office
                            ) }}"
                            class="inline-flex items-center justify-center px-6 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            العودة إلى المكتب
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
