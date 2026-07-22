<x-app-layout>

    @php
        $isRenewal = $isRenewal ?? false;
        $lastApproved = $lastApproved ?? null;

        $savedSpecialtyId =
            auth()->user()->employeeProfile?->specialty_id
            ?? $lastApproved?->specialty_id;
    @endphp

    <div
        class="py-10"
        dir="rtl"
    >

        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- العنوان --}}
            <div
                class="relative p-8 mb-8 overflow-hidden border shadow-xl rounded-3xl bg-gradient-to-l from-purple-700 via-blue-700 to-cyan-600 border-purple-500/30"
            >

                <div
                    class="absolute w-56 h-56 rounded-full -top-24 -left-16 bg-white/10"
                ></div>

                <div class="relative">

                    <div
                        class="flex items-center justify-center mb-5 text-3xl w-14 h-14 rounded-2xl bg-white/10"
                    >
                        {{ $isRenewal ? '💳' : '👷' }}
                    </div>

                    <h1 class="text-3xl font-black text-white">

                        {{ $isRenewal
                            ? 'تجديد اشتراك المهندس'
                            : 'طلب الانضمام كمهندس' }}

                    </h1>

                    <p class="max-w-3xl mt-4 leading-8 text-purple-100">

                        @if ($isRenewal)

                            ارفع إيصال الدفع الجديد، وبعد موافقة الإدارة
                            يتم تفعيل حسابك مع الاحتفاظ بتخصصك وشهادتك
                            وسيرتك الذاتية وأعمالك السابقة.

                        @else

                            أدخل بيانات تخصصك وارفع الشهادة والسيرة الذاتية
                            وإيصال الدفع. ستقوم الإدارة بمراجعة الطلب
                            وتحديد مدة تفعيل حساب المهندس.

                        @endif

                    </p>

                </div>

            </div>

            {{-- الرجوع --}}
            <div class="mb-6">

                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 text-sm font-bold transition text-slate-400 hover:text-white"
                >
                    <span>→</span>
                    العودة إلى لوحة التحكم
                </a>

            </div>

            {{-- رسائل النجاح --}}
            @if (session('success'))

                <div
                    class="p-4 mb-6 text-green-200 border rounded-2xl border-green-700/50 bg-green-900/30"
                >
                    {{ session('success') }}
                </div>

            @endif

            {{-- رسائل الخطأ --}}
            @if (session('error'))

                <div
                    class="p-4 mb-6 text-red-200 border rounded-2xl border-red-700/50 bg-red-900/30"
                >
                    {{ session('error') }}
                </div>

            @endif

            {{-- أخطاء التحقق --}}
            @if ($errors->any())

                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-700/50 bg-red-900/30"
                >

                    <h2 class="mb-3 font-black">
                        يرجى تصحيح الأخطاء التالية:
                    </h2>

                    <ul class="space-y-2 text-sm">

                        @foreach ($errors->all() as $error)

                            <li>
                                • {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="grid gap-8 lg:grid-cols-[1fr_320px]">

                {{-- نموذج الطلب --}}
                <form
                    method="POST"
                    action="{{ route('engineer-applications.store') }}"
                    enctype="multipart/form-data"
                    class="p-6 border shadow-xl sm:p-8 rounded-3xl bg-slate-900/90 border-slate-800"
                >

                    @csrf

                    @if ($isRenewal)

                        <div
                            class="p-5 border mb-7 rounded-2xl border-cyan-500/20 bg-cyan-500/5"
                        >

                            <h3 class="font-black text-cyan-200">
                                طلب تجديد اشتراك
                            </h3>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                بيانات المهندس السابقة محفوظة ولن يتم حذف
                                التخصص أو الشهادة أو السيرة الذاتية أو الأعمال.
                                المطلوب رفع إيصال الدفع الجديد فقط.
                            </p>

                        </div>

                        <input
                            type="hidden"
                            name="specialty_id"
                            value="{{ old(
                                'specialty_id',
                                $savedSpecialtyId
                            ) }}"
                        >

                    @endif

                    <div class="mb-8">

                        <h2 class="text-2xl font-black text-white">

                            {{ $isRenewal
                                ? 'بيانات تجديد الاشتراك'
                                : 'بيانات طلب الانضمام' }}

                        </h2>

                        <p class="mt-2 text-sm leading-7 text-slate-400">

                            {{ $isRenewal
                                ? 'ارفع إيصال دفع واضحًا حتى تتمكن الإدارة من تأكيد التجديد.'
                                : 'تأكد من رفع ملفات واضحة وصحيحة حتى تتمكن الإدارة من مراجعة طلبك.' }}

                        </p>

                    </div>

                    <div class="space-y-6">

                        @if (! $isRenewal)

                            {{-- التخصص --}}
                            <div>

                                <label
                                    for="specialty_id"
                                    class="block mb-2 text-sm font-bold text-slate-300"
                                >
                                    التخصص الهندسي
                                    <span class="text-red-400">*</span>
                                </label>

                                <select
                                    id="specialty_id"
                                    name="specialty_id"
                                    required
                                    class="w-full px-4 py-3 text-white border outline-none rounded-2xl border-slate-700 bg-slate-950 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20"
                                >

                                    <option value="">
                                        اختر التخصص الهندسي
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

                                @error('specialty_id')

                                    <p class="mt-2 text-sm text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- الشهادة --}}
                            <div>

                                <label
                                    for="certificate_file"
                                    class="block mb-2 text-sm font-bold text-slate-300"
                                >
                                    الشهادة الهندسية
                                    <span class="text-red-400">*</span>
                                </label>

                                <label
                                    for="certificate_file"
                                    class="flex flex-col items-center justify-center p-6 text-center transition border border-dashed cursor-pointer rounded-2xl border-slate-700 bg-slate-950/70 hover:border-purple-500 hover:bg-purple-500/5"
                                >

                                    <span class="mb-3 text-4xl">
                                        🎓
                                    </span>

                                    <span class="font-bold text-white">
                                        اضغط لرفع الشهادة
                                    </span>

                                    <span class="mt-2 text-xs text-slate-500">
                                        PDF أو JPG أو JPEG أو PNG
                                    </span>

                                    <span class="mt-1 text-xs text-slate-500">
                                        الحد الأقصى 10 ميجابايت
                                    </span>

                                </label>

                                <input
                                    id="certificate_file"
                                    name="certificate_file"
                                    type="file"
                                    required
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="hidden"
                                    onchange="
                                        document.getElementById(
                                            'certificate-name'
                                        ).textContent =
                                            this.files[0]
                                                ? this.files[0].name
                                                : 'لم يتم اختيار ملف';
                                    "
                                >

                                <p
                                    id="certificate-name"
                                    class="mt-3 text-sm text-cyan-300"
                                >
                                    لم يتم اختيار ملف
                                </p>

                                @error('certificate_file')

                                    <p class="mt-2 text-sm text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                            {{-- السيرة الذاتية --}}
                            <div>

                                <label
                                    for="cv_file"
                                    class="block mb-2 text-sm font-bold text-slate-300"
                                >
                                    السيرة الذاتية

                                    <span class="font-normal text-slate-500">
                                        — اختياري
                                    </span>

                                </label>

                                <label
                                    for="cv_file"
                                    class="flex flex-col items-center justify-center p-6 text-center transition border border-dashed cursor-pointer rounded-2xl border-slate-700 bg-slate-950/70 hover:border-cyan-500 hover:bg-cyan-500/5"
                                >

                                    <span class="mb-3 text-4xl">
                                        📄
                                    </span>

                                    <span class="font-bold text-white">
                                        اضغط لرفع السيرة الذاتية
                                    </span>

                                    <span class="mt-2 text-xs text-slate-500">
                                        PDF أو DOC أو DOCX
                                    </span>

                                    <span class="mt-1 text-xs text-slate-500">
                                        الحد الأقصى 10 ميجابايت
                                    </span>

                                </label>

                                <input
                                    id="cv_file"
                                    name="cv_file"
                                    type="file"
                                    accept=".pdf,.doc,.docx"
                                    class="hidden"
                                    onchange="
                                        document.getElementById(
                                            'cv-name'
                                        ).textContent =
                                            this.files[0]
                                                ? this.files[0].name
                                                : 'لم يتم اختيار ملف';
                                    "
                                >

                                <p
                                    id="cv-name"
                                    class="mt-3 text-sm text-cyan-300"
                                >
                                    لم يتم اختيار ملف
                                </p>

                                @error('cv_file')

                                    <p class="mt-2 text-sm text-red-400">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        @endif

                        {{-- معلومات الدفع --}}
                        <div class="space-y-4">

                            <div>

                                <h3 class="text-xl font-black text-white">
                                    معلومات الدفع
                                </h3>

                                <p class="mt-2 text-sm leading-7 text-slate-400">
                                    انسخ بيانات الحساب، أتم التحويل، ثم ارفع إيصال الدفع من الأسفل.
                                </p>

                            </div>

                            <x-payment-information />

                        </div>

                        {{-- إيصال الدفع --}}
                        <div>

                            <label
                                for="payment_receipt"
                                class="block mb-2 text-sm font-bold text-slate-300"
                            >

                                {{ $isRenewal
                                    ? 'إيصال دفع تجديد الاشتراك'
                                    : 'إيصال دفع رسوم الطلب' }}

                                <span class="text-red-400">*</span>

                            </label>

                            <label
                                for="payment_receipt"
                                class="flex flex-col items-center justify-center p-6 text-center transition border border-dashed cursor-pointer rounded-2xl border-slate-700 bg-slate-950/70 hover:border-green-500 hover:bg-green-500/5"
                            >

                                <span class="mb-3 text-4xl">
                                    🧾
                                </span>

                                <span class="font-bold text-white">
                                    اضغط لرفع إيصال الدفع
                                </span>

                                <span class="mt-2 text-xs text-slate-500">
                                    JPG أو JPEG أو PNG أو PDF
                                </span>

                                <span class="mt-1 text-xs text-slate-500">
                                    الحد الأقصى 10 ميجابايت
                                </span>

                            </label>

                            <input
                                id="payment_receipt"
                                name="payment_receipt"
                                type="file"
                                required
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="hidden"
                                onchange="
                                    document.getElementById(
                                        'receipt-name'
                                    ).textContent =
                                        this.files[0]
                                            ? this.files[0].name
                                            : 'لم يتم اختيار ملف';
                                "
                            >

                            <p
                                id="receipt-name"
                                class="mt-3 text-sm text-green-300"
                            >
                                لم يتم اختيار ملف
                            </p>

                            @error('payment_receipt')

                                <p class="mt-2 text-sm text-red-400">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>

                    <div
                        class="flex flex-col gap-3 pt-6 mt-8 border-t sm:flex-row border-slate-800"
                    >

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center flex-1 gap-2 px-6 py-3 font-black text-white transition rounded-2xl bg-gradient-to-l from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500"
                        >
                            <span>📨</span>

                            {{ $isRenewal
                                ? 'إرسال دفعة التجديد'
                                : 'إرسال طلب الانضمام' }}

                        </button>

                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-3 font-bold transition border rounded-2xl border-slate-700 text-slate-300 hover:bg-slate-800"
                        >
                            إلغاء
                        </a>

                    </div>

                </form>

                {{-- معلومات الطلب --}}
                <aside class="space-y-5">

                    <div
                        class="p-6 border shadow-xl rounded-3xl bg-slate-900/90 border-slate-800"
                    >

                        <h2 class="text-lg font-black text-white">

                            {{ $isRenewal
                                ? 'رسوم تجديد الاشتراك'
                                : 'رسوم تقديم الطلب' }}

                        </h2>

                        <p class="mt-4 text-4xl font-black text-green-300">
                            150

                            <span class="text-lg">
                                ريال سعودي
                            </span>

                        </p>

                        <p class="mt-3 text-sm leading-7 text-slate-400">

                            @if ($isRenewal)

                                تقوم الإدارة بفحص إيصال الدفع ثم تحدد عدد
                                أيام التفعيل الجديدة.

                            @else

                                يتم رفع إيصال الدفع مع الطلب، وتقوم الإدارة
                                بمراجعته وتحديد عدد أيام تفعيل حساب المهندس.

                            @endif

                        </p>

                    </div>

                    <div
                        class="p-6 border shadow-xl rounded-3xl bg-slate-900/90 border-slate-800"
                    >

                        <h2 class="mb-5 text-lg font-black text-white">
                            مراحل الطلب
                        </h2>

                        <div class="space-y-5">

                            <div class="flex gap-3">

                                <div
                                    class="flex items-center justify-center flex-none font-black text-blue-200 rounded-full w-9 h-9 bg-blue-500/20"
                                >
                                    1
                                </div>

                                <div>

                                    <p class="font-bold text-white">

                                        {{ $isRenewal
                                            ? 'رفع إيصال التجديد'
                                            : 'إرسال البيانات' }}

                                    </p>

                                    <p class="mt-1 text-xs leading-6 text-slate-500">

                                        {{ $isRenewal
                                            ? 'رفع إيصال الدفع الجديد.'
                                            : 'اختيار التخصص ورفع المستندات.' }}

                                    </p>

                                </div>

                            </div>

                            <div class="flex gap-3">

                                <div
                                    class="flex items-center justify-center flex-none font-black text-yellow-200 rounded-full w-9 h-9 bg-yellow-500/20"
                                >
                                    2
                                </div>

                                <div>

                                    <p class="font-bold text-white">
                                        مراجعة الإدارة
                                    </p>

                                    <p class="mt-1 text-xs leading-6 text-slate-500">
                                        فحص الطلب وإيصال الدفع وتحديد مدة التفعيل.
                                    </p>

                                </div>

                            </div>

                            <div class="flex gap-3">

                                <div
                                    class="flex items-center justify-center flex-none font-black text-green-200 rounded-full w-9 h-9 bg-green-500/20"
                                >
                                    3
                                </div>

                                <div>

                                    <p class="font-bold text-white">
                                        تفعيل حساب المهندس
                                    </p>

                                    <p class="mt-1 text-xs leading-6 text-slate-500">

                                        {{ $isRenewal
                                            ? 'تعود جميع صلاحيات المهندس وبياناته السابقة.'
                                            : 'يتحول الحساب إلى مهندس للمدة المحددة.' }}

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </aside>

            </div>

        </div>

    </div>

</x-app-layout>
