<x-app-layout>

    <div class="relative py-12" dir="rtl">

        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

            <div class="p-6 border shadow-2xl bg-slate-900/90 rounded-[2rem] border-white/10 md:p-8">

                <div class="mb-8 text-center">

                    <div class="flex items-center justify-center w-16 h-16 mx-auto text-3xl border rounded-2xl border-cyan-400/20 bg-cyan-500/10">
                        🏗️
                    </div>

                    <h2 class="mt-5 text-3xl font-black text-white">
                        إضافة عمل أو إنجاز
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        أضف تفاصيل عملك الهندسي وصوره ليتم عرضه بعد المراجعة
                    </p>

                </div>

                @if ($errors->any())

                    <div class="p-5 mb-6 border rounded-2xl border-red-400/20 bg-red-500/10">

                        <p class="mb-3 font-bold text-red-300">
                            يرجى تصحيح الأخطاء التالية:
                        </p>

                        <div class="space-y-2 text-sm text-red-200">

                            @foreach ($errors->all() as $error)
                                <p>• {{ $error }}</p>
                            @endforeach

                        </div>

                    </div>

                @endif

                <form
                    method="POST"
                    action="{{ route('engineer.works.store') }}"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >

                    @csrf

                    <div>

                        <label
                            for="title"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            عنوان العمل
                            <span class="text-red-400">*</span>
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="مثال: تصميم فيلا سكنية حديثة"
                            class="w-full px-4 py-3 text-white transition border outline-none rounded-2xl bg-slate-950/60 border-white/10 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                        >

                    </div>

                    <div>

                        <label
                            for="project_type"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            نوع المشروع
                        </label>

                        <input
                            id="project_type"
                            type="text"
                            name="project_type"
                            value="{{ old('project_type') }}"
                            placeholder="تصميم معماري، إنشائي، كهربائي..."
                            class="w-full px-4 py-3 text-white transition border outline-none rounded-2xl bg-slate-950/60 border-white/10 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                        >

                    </div>

                    <div class="grid gap-6 md:grid-cols-2">

                        <div>

                            <label
                                for="location"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                الموقع
                            </label>

                            <input
                                id="location"
                                type="text"
                                name="location"
                                value="{{ old('location') }}"
                                placeholder="مثال: الرياض"
                                class="w-full px-4 py-3 text-white transition border outline-none rounded-2xl bg-slate-950/60 border-white/10 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                            >

                        </div>

                        <div>

                            <label
                                for="completion_year"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                سنة الإنجاز
                            </label>

                            <input
                                id="completion_year"
                                type="number"
                                name="completion_year"
                                value="{{ old('completion_year') }}"
                                min="1900"
                                max="{{ date('Y') }}"
                                placeholder="{{ date('Y') }}"
                                class="w-full px-4 py-3 text-white transition border outline-none rounded-2xl bg-slate-950/60 border-white/10 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                            >

                        </div>

                    </div>

                    <div>

                        <label
                            for="description"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            وصف العمل
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            placeholder="اكتب وصفًا مختصرًا للعمل، الفكرة، المساحة، والتفاصيل المهمة..."
                            class="w-full px-4 py-3 text-white transition border outline-none resize-none rounded-2xl bg-slate-950/60 border-white/10 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                        >{{ old('description') }}</textarea>

                    </div>

                    <div>

                        <label class="block mb-2 text-sm font-bold text-slate-200">
                            صور العمل
                            <span class="text-red-400">*</span>
                        </label>

                        <label
                            class="relative flex flex-col items-center justify-center p-8 text-center transition border-2 border-dashed cursor-pointer rounded-2xl border-white/10 bg-white/[0.03] hover:border-cyan-400/40 hover:bg-cyan-500/5"
                        >

                            <input
                                type="file"
                                name="images[]"
                                accept=".jpg,.jpeg,.png,.webp"
                                multiple
                                required
                                class="hidden"
                            >

                            <div class="text-4xl">
                                🖼️
                            </div>

                            <p class="mt-4 font-bold text-white">
                                اضغط لاختيار صور العمل
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                JPG، JPEG، PNG أو WEBP
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                يمكنك اختيار من صورة واحدة حتى 10 صور
                            </p>

                        </label>

                    </div>

                    <div class="flex flex-col gap-3 pt-6 border-t sm:flex-row border-white/10">

                        <button
                            type="submit"
                            class="flex items-center justify-center flex-1 gap-2 px-6 py-3 font-black text-white transition rounded-2xl bg-gradient-to-l from-cyan-500 to-blue-600 hover:scale-[1.01] hover:shadow-lg hover:shadow-cyan-500/20"
                        >
                            إرسال العمل للمراجعة
                            <span>←</span>
                        </button>

                        <a
                            href="{{ url()->previous() }}"
                            class="flex items-center justify-center px-6 py-3 font-bold text-white transition border rounded-2xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            إلغاء
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
