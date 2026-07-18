<x-app-layout>

    <div
        class="relative py-12"
        dir="rtl"
    >

        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            <x-page-header
                title="طلب استشارة جديدة"
                description="أدخل تفاصيل مشروعك وارفع الملفات اللازمة، ثم أكمل عملية الدفع"
                icon="📝"
            />

            <x-alerts />

            <div class="grid gap-8 lg:grid-cols-[1fr_340px]">

                {{-- النموذج --}}

                <section
                    class="p-6 glass-panel rounded-[2rem] fade-up md:p-8"
                >

                    <form
                        method="POST"
                        action="{{ route('consultations.store') }}"
                        enctype="multipart/form-data"
                        x-data="{
                            fileName: '',
                            selectedType: '',
                            selectedPrice: ''
                        }"
                    >

                        @csrf

                        {{-- المهندس المختار --}}

                        @if (isset($engineer) && $engineer)

                            <div
                                class="flex items-center justify-between gap-4 p-5 border mb-7 rounded-2xl border-green-400/20 bg-green-500/10"
                            >

                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex items-center justify-center flex-none text-xl font-black border rounded-full w-14 h-14 border-green-300/20 bg-gradient-to-br from-green-500 to-cyan-500"
                                    >
                                        {{ mb_substr($engineer->name, 0, 1) }}
                                    </div>

                                    <div>

                                        <p class="text-xs font-bold text-green-200">
                                            المهندس المختار
                                        </p>

                                        <h2 class="mt-1 text-lg font-black text-white">
                                            {{ $engineer->name }}
                                        </h2>

                                        <p class="mt-1 text-sm text-green-100/70">
                                            سيتم إرسال الطلب لهذا المهندس بعد تأكيد الدفع
                                        </p>

                                    </div>

                                </div>

                                <span
                                    class="text-green-200 status-badge bg-green-400/10"
                                >
                                    تم الاختيار ✓
                                </span>

                            </div>

                            <input
                                type="hidden"
                                name="engineer_id"
                                value="{{ $engineer->id }}"
                            >

                        @else

                            <input
                                type="hidden"
                                name="engineer_id"
                                value=""
                            >

                        @endif

                        <div class="space-y-6">

                            {{-- نوع الاستشارة --}}

                            <div>

                                <label
                                    for="consultation_type_id"
                                    class="block mb-2 text-sm font-bold text-slate-200"
                                >
                                    نوع الاستشارة
                                    <span class="text-red-400">*</span>
                                </label>

                                <select
                                    id="consultation_type_id"
                                    name="consultation_type_id"
                                    required
                                    x-model="selectedType"
                                    @change="
                                        selectedPrice =
                                            $event.target.options[
                                                $event.target.selectedIndex
                                            ].dataset.price || ''
                                    "
                                    class="form-control"
                                >

                                    <option value="">
                                        اختر نوع الاستشارة
                                    </option>

                                    @foreach ($types as $type)

                                        <option
                                            value="{{ $type->id }}"
                                            data-price="{{ $type->price }}"
                                            @selected(
                                                old('consultation_type_id') == $type->id
                                            )
                                        >
                                            {{ $type->name }}
                                            —
                                            {{ number_format($type->price, 2) }}
                                            شيكل
                                        </option>

                                    @endforeach

                                </select>

                                @error('consultation_type_id')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- السعر الظاهر --}}

                            <div
                                x-cloak
                                x-show="selectedPrice"
                                x-transition
                                class="flex items-center justify-between p-4 border rounded-2xl border-cyan-400/20 bg-cyan-500/10"
                            >

                                <span class="text-sm font-bold text-cyan-100">
                                    السعر المحدد
                                </span>

                                <span class="text-xl font-black text-cyan-300">
                                    <span x-text="selectedPrice"></span>
                                    شيكل
                                </span>

                            </div>

                            {{-- العنوان --}}

                            <div>

                                <label
                                    for="title"
                                    class="block mb-2 text-sm font-bold text-slate-200"
                                >
                                    عنوان المشروع
                                    <span class="text-red-400">*</span>
                                </label>

                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    maxlength="255"
                                    required
                                    placeholder="مثال: تصميم منزل سكني من طابقين"
                                    class="form-control"
                                >

                                @error('title')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- الوصف --}}

                            <div
                                x-data="{
                                    count: {{ mb_strlen(old('description', '')) }}
                                }"
                            >

                                <div class="flex items-center justify-between mb-2">

                                    <label
                                        for="description"
                                        class="text-sm font-bold text-slate-200"
                                    >
                                        وصف المشروع
                                        <span class="text-red-400">*</span>
                                    </label>

                                    <span class="text-xs text-slate-500">
                                        <span x-text="count"></span>
                                        حرف
                                    </span>

                                </div>

                                <textarea
                                    id="description"
                                    name="description"
                                    rows="7"
                                    required
                                    @input="count = $event.target.value.length"
                                    placeholder="اشرح تفاصيل المشروع، المساحة، المتطلبات، والملاحظات المهمة..."
                                    class="resize-none form-control"
                                >{{ old('description') }}</textarea>

                                @error('description')
                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- رفع الملف --}}

                            <div>

                                <label class="block mb-2 text-sm font-bold text-slate-200">
                                    ملف المشروع
                                </label>

                                <label
                                    class="relative flex flex-col items-center justify-center p-8 text-center transition border-2 border-dashed cursor-pointer rounded-2xl border-white/10 bg-white/[0.03] hover:border-cyan-400/40 hover:bg-cyan-500/5"
                                >

                                    <input
                                        type="file"
                                        name="customer_file"
                                        accept=".pdf,.jpg,.jpeg,.png,.dwg"
                                        class="hidden"
                                        @change="
                                            fileName =
                                                $event.target.files[0]
                                                    ? $event.target.files[0].name
                                                    : ''
                                        "
                                    >

                                    <div class="text-4xl">
                                        📎
                                    </div>

                                    <p class="mt-4 font-bold text-white">
                                        اضغط لاختيار ملف
                                    </p>

                                    <p class="mt-2 text-sm text-slate-400">
                                        PDF، JPG، PNG أو DWG حتى 10MB
                                    </p>

                                    <div
                                        x-cloak
                                        x-show="fileName"
                                        x-transition
                                        class="px-4 py-2 mt-4 text-sm font-bold text-cyan-200 rounded-xl bg-cyan-500/10"
                                    >
                                        الملف المختار:
                                        <span x-text="fileName"></span>
                                    </div>

                                </label>

                                @error('customer_file')
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
                                حفظ والمتابعة للدفع
                                <span>←</span>
                            </button>

                            @if (auth()->user()->role === 'customer')

                                <a
                                    href="{{ route('consultations.mine') }}"
                                    class="secondary-button"
                                >
                                    إلغاء
                                </a>

                            @else

                                <a
                                    href="{{ route('dashboard') }}"
                                    class="secondary-button"
                                >
                                    إلغاء
                                </a>

                            @endif

                        </div>

                    </form>

                </section>

                {{-- المعلومات الجانبية --}}

                <aside class="space-y-5">

                    <div class="p-6 glass-card rounded-[2rem] fade-up delay-100">

                        <div
                            class="flex items-center justify-center w-12 h-12 text-2xl border rounded-2xl border-blue-400/20 bg-blue-500/10"
                        >
                            💡
                        </div>

                        <h3 class="mt-5 text-lg font-black text-white">
                            قبل إرسال الطلب
                        </h3>

                        <ul class="mt-5 space-y-4 text-sm leading-7 text-slate-400">

                            <li class="flex gap-3">
                                <span class="text-cyan-300">✓</span>
                                اكتب وصفًا واضحًا ومفصلًا للمشروع.
                            </li>

                            <li class="flex gap-3">
                                <span class="text-cyan-300">✓</span>
                                أرفق المخططات أو الصور المتوفرة.
                            </li>

                            <li class="flex gap-3">
                                <span class="text-cyan-300">✓</span>
                                سيتم حفظ الطلب أولًا ثم تحويلك للدفع.
                            </li>

                            <li class="flex gap-3">
                                <span class="text-cyan-300">✓</span>
                                لا يبدأ تنفيذ الاستشارة إلا بعد تأكيد الدفع.
                            </li>

                        </ul>

                    </div>

                    <div class="p-6 glass-card rounded-[2rem] fade-up delay-200">

                        <p class="text-sm font-bold text-slate-400">
                            مراحل الطلب
                        </p>

                        <div class="mt-5 space-y-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center flex-none text-sm font-black rounded-full w-9 h-9 bg-cyan-500 text-slate-950"
                                >
                                    1
                                </div>

                                <div>

                                    <p class="font-bold text-white">
                                        إضافة التفاصيل
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        أنت هنا الآن
                                    </p>

                                </div>

                            </div>

                            <div class="w-px h-5 mr-4 bg-white/10"></div>

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center flex-none text-sm font-black rounded-full w-9 h-9 bg-white/5 text-slate-400"
                                >
                                    2
                                </div>

                                <p class="font-bold text-slate-400">
                                    رفع إيصال الدفع
                                </p>

                            </div>

                            <div class="w-px h-5 mr-4 bg-white/10"></div>

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center flex-none text-sm font-black rounded-full w-9 h-9 bg-white/5 text-slate-400"
                                >
                                    3
                                </div>

                                <p class="font-bold text-slate-400">
                                    مراجعة المدير
                                </p>

                            </div>

                            <div class="w-px h-5 mr-4 bg-white/10"></div>

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center flex-none text-sm font-black rounded-full w-9 h-9 bg-white/5 text-slate-400"
                                >
                                    4
                                </div>

                                <p class="font-bold text-slate-400">
                                    بدء الاستشارة
                                </p>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>

        </div>

    </div>

</x-app-layout>
