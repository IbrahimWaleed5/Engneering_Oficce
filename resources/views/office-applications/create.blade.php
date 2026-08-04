<x-app-layout>
    @php
        $currentUser = auth()->user();
    @endphp

    <style>
        [x-cloak]{display:none!important}
        .office-register-page{
            min-height:100vh;
            overflow-x:hidden;
            color:#dae2fd;
            background:
                radial-gradient(circle at 12% 12%,rgba(37,99,235,.20),transparent 32%),
                radial-gradient(circle at 88% 10%,rgba(131,67,244,.15),transparent 30%),
                #0b1326;
            font-family:'Be Vietnam Pro','Almarai',system-ui,sans-serif;
        }
        .office-register-glass{
            background:rgba(23,31,51,.56);
            border:1px solid rgba(180,197,255,.10);
            box-shadow:0 24px 70px rgba(0,0,0,.28);
            backdrop-filter:blur(16px);
            -webkit-backdrop-filter:blur(16px);
        }
        .office-register-input{
            width:100%;
            border:1px solid rgba(67,70,85,.72);
            border-radius:.85rem;
            background:rgba(6,14,32,.68);
            padding:.9rem 1rem;
            color:#fff;
            outline:none;
            transition:.25s ease;
        }
        .office-register-input:focus{
            border-color:#b4c5ff;
            box-shadow:0 0 0 3px rgba(180,197,255,.10),0 0 18px rgba(37,99,235,.15);
        }
        .office-register-upload{
            border:2px dashed rgba(67,70,85,.9);
            background:rgba(34,42,61,.22);
            transition:.25s ease;
        }
        .office-register-upload:hover{
            border-color:rgba(180,197,255,.55);
            background:rgba(180,197,255,.05);
        }
        .office-register-neon{
            box-shadow:0 0 22px rgba(37,99,235,.35);
        }
        @media(max-width:640px){
            .office-register-page{padding-left:.25rem;padding-right:.25rem}
            .office-register-card{padding:1rem!important;border-radius:1rem!important}
            .office-register-upload{padding:1.25rem!important}
            .office-register-title{font-size:1.75rem!important;line-height:2.25rem!important}
        }
    </style>

    <div
        class="px-4 py-10 office-register-page sm:px-6"
        dir="rtl"
        x-data="{
            commercialName: '',
            licenseName: '',
            receiptName: '',
            commercialDrag: false,
            licenseDrag: false,
            receiptDrag: false,
            submitting: false,
            copyText(value) {
                navigator.clipboard?.writeText(value);
            },
            putDroppedFile(input, event, targetName) {
                if (!event.dataTransfer.files.length) return;
                const transfer = new DataTransfer();
                transfer.items.add(event.dataTransfer.files[0]);
                input.files = transfer.files;
                this[targetName] = event.dataTransfer.files[0].name;
            }
        }"
    >
        <div class="max-w-5xl mx-auto">
            <header class="mb-8 text-center">
                <span class="text-xs font-black uppercase tracking-[.25em] text-[#b4c5ff]">
                    خدمة تسجيل المكاتب الهندسية
                </span>

                <h1 class="mt-3 text-4xl font-black text-white office-register-title">
                    طلب تسجيل مكتب هندسي جديد
                </h1>

                <p class="mx-auto mt-4 max-w-3xl leading-8 text-[#c3c6d7]">
                    أدخل بيانات المكتب وارفع المستندات المطلوبة وإيصال الاشتراك الشهري.
                    ستراجع الإدارة الطلب والإيصال قبل تفعيل المكتب.
                </p>
            </header>

            @if (session('success'))
                <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 mb-6 border rounded-2xl border-cyan-500/20 bg-cyan-500/10 text-cyan-100">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-5 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <h2 class="mb-3 font-black">يرجى تصحيح الأخطاء التالية:</h2>
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="relative p-6 overflow-hidden office-register-card office-register-glass rounded-3xl sm:p-8">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-l from-transparent via-[#b4c5ff] to-transparent opacity-60"></div>

                <form
                    method="POST"
                    action="{{ route('office-applications.store') }}"
                    enctype="multipart/form-data"
                    class="space-y-9"
                    @submit="submitting = true"
                >
                    @csrf

                    <input type="hidden" name="payment_method" value="bank_transfer">

                    <section>
                        <div class="flex items-center gap-3 pb-4 mb-6 border-b border-white/10">
                            <span class="flex items-center justify-center text-2xl h-11 w-11 rounded-xl bg-blue-500/10">🏢</span>
                            <div>
                                <h2 class="text-2xl font-black text-[#b4c5ff]">بيانات المكتب</h2>
                                <p class="mt-1 text-sm text-[#8d90a0]">البيانات الرسمية التي ستظهر للإدارة.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="office_name" class="block mb-2 text-sm font-black text-white">
                                    اسم المكتب <span class="text-red-400">*</span>
                                </label>
                                <input id="office_name" name="office_name" type="text"
                                       value="{{ old('office_name') }}" required
                                       placeholder="أدخل اسم المكتب الهندسي"
                                       class="office-register-input">
                                @error('office_name')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="block mb-2 text-sm font-black text-white">
                                    البريد الإلكتروني للمكتب <span class="text-red-400">*</span>
                                </label>
                                <input id="email" name="email" type="email"
                                       value="{{ old('email', $currentUser->email) }}" required
                                       dir="ltr"
                                       class="text-left office-register-input">
                                @error('email')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="phone" class="block mb-2 text-sm font-black text-white">
                                    رقم الهاتف <span class="text-red-400">*</span>
                                </label>
                                <input id="phone" name="phone" type="text"
                                       value="{{ old('phone', $currentUser->phone) }}" required
                                       dir="ltr"
                                       class="text-left office-register-input">
                                @error('phone')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="commercial_registration" class="block mb-2 text-sm font-black text-white">
                                    رقم السجل التجاري <span class="text-red-400">*</span>
                                </label>
                                <input id="commercial_registration" name="commercial_registration" type="text"
                                       value="{{ old('commercial_registration') }}" required
                                       class="office-register-input">
                                @error('commercial_registration')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="license_number" class="block mb-2 text-sm font-black text-white">
                                    رقم الترخيص <span class="text-red-400">*</span>
                                </label>
                                <input id="license_number" name="license_number" type="text"
                                       value="{{ old('license_number') }}" required
                                       class="office-register-input">
                                @error('license_number')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="country" class="block mb-2 text-sm font-black text-white">
                                    الدولة <span class="text-red-400">*</span>
                                </label>
                                <input id="country" name="country" type="text"
                                       value="{{ old('country', 'السعودية') }}" required
                                       class="office-register-input">
                                @error('country')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="city" class="block mb-2 text-sm font-black text-white">
                                    المدينة <span class="text-red-400">*</span>
                                </label>
                                <input id="city" name="city" type="text"
                                       value="{{ old('city') }}" required
                                       class="office-register-input">
                                @error('city')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="address" class="block mb-2 text-sm font-black text-white">
                                    عنوان المكتب <span class="text-red-400">*</span>
                                </label>
                                <textarea id="address" name="address" rows="3" required
                                          class="office-register-input">{{ old('address') }}</textarea>
                                @error('address')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center gap-3 pb-4 mb-6 border-b border-white/10">
                            <span class="flex items-center justify-center text-2xl h-11 w-11 rounded-xl bg-violet-500/10">📎</span>
                            <div>
                                <h2 class="text-2xl font-black text-[#b4c5ff]">المرفقات والمستندات</h2>
                                <p class="mt-1 text-sm text-[#8d90a0]">PDF أو صورة، بحد أقصى 10 ميجابايت لكل ملف.</p>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-black text-white">
                                    ملف السجل التجاري <span class="text-red-400">*</span>
                                </label>
                                <label
                                    for="commercial_registration_file"
                                    class="flex flex-col items-center justify-center p-8 text-center cursor-pointer office-register-upload rounded-2xl"
                                    :class="commercialDrag ? 'border-[#b4c5ff] bg-[#b4c5ff]/5' : ''"
                                    @dragover.prevent="commercialDrag=true"
                                    @dragleave.prevent="commercialDrag=false"
                                    @drop.prevent="commercialDrag=false; putDroppedFile($refs.commercialInput, $event, 'commercialName')"
                                >
                                    <span class="text-4xl">📄</span>
                                    <span class="mt-3 font-black text-white">اضغط أو اسحب ملف السجل</span>
                                    <span x-cloak x-show="commercialName" x-text="commercialName"
                                          class="px-3 py-2 mt-3 text-sm font-bold rounded-lg bg-blue-500/10 text-cyan-300"></span>
                                </label>
                                <input x-ref="commercialInput" id="commercial_registration_file"
                                       name="commercial_registration_file" type="file" required
                                       accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden"
                                       @change="commercialName=$event.target.files[0]?.name || ''">
                                @error('commercial_registration_file')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-black text-white">
                                    ملف ترخيص المكتب <span class="text-red-400">*</span>
                                </label>
                                <label
                                    for="license_document"
                                    class="flex flex-col items-center justify-center p-8 text-center cursor-pointer office-register-upload rounded-2xl"
                                    :class="licenseDrag ? 'border-[#b4c5ff] bg-[#b4c5ff]/5' : ''"
                                    @dragover.prevent="licenseDrag=true"
                                    @dragleave.prevent="licenseDrag=false"
                                    @drop.prevent="licenseDrag=false; putDroppedFile($refs.licenseInput, $event, 'licenseName')"
                                >
                                    <span class="text-4xl">✅</span>
                                    <span class="mt-3 font-black text-white">اضغط أو اسحب ملف الترخيص</span>
                                    <span x-cloak x-show="licenseName" x-text="licenseName"
                                          class="px-3 py-2 mt-3 text-sm font-bold rounded-lg bg-blue-500/10 text-cyan-300"></span>
                                </label>
                                <input x-ref="licenseInput" id="license_document"
                                       name="license_document" type="file" required
                                       accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden"
                                       @change="licenseName=$event.target.files[0]?.name || ''">
                                @error('license_document')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center gap-3 pb-4 mb-6 border-b border-white/10">
                            <span class="flex items-center justify-center text-2xl h-11 w-11 rounded-xl bg-emerald-500/10">💳</span>
                            <div>
                                <h2 class="text-2xl font-black text-[#b4c5ff]">الاشتراك والدفع</h2>
                                <p class="mt-1 text-sm text-[#8d90a0]">اشتراك شهري ثابت للمكتب الهندسي.</p>
                            </div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <aside class="office-register-neon rounded-2xl border border-[#b4c5ff]/15 bg-[#222a3d]/50 p-6 text-center">
                                <span class="text-xs font-black uppercase tracking-[.2em] text-[#ffb1c7]">
                                    الباقة الاحترافية
                                </span>

                                <div class="flex items-baseline justify-center gap-2 mt-4">
                                    <span class="text-5xl font-black text-white">$300</span>
                                    <span class="font-bold text-[#c3c6d7]">/ شهريًا</span>
                                </div>

                                <ul class="mt-6 space-y-3 text-right text-sm text-[#c3c6d7]">
                                    <li>✓ إدارة أعضاء المكتب والمهندسين</li>
                                    <li>✓ استقبال وتوزيع الاستشارات</li>
                                    <li>✓ إدارة الملف والاشتراكات</li>
                                </ul>
                            </aside>

                            <div class="space-y-5 lg:col-span-2">
                                <div class="rounded-2xl border border-white/10 bg-[#060e20]/45 p-5">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="text-2xl">🏦</span>
                                        <h3 class="text-xl font-black text-white">بيانات الدفع المتاحة</h3>
                                    </div>

                                    <x-payment-information />
                                </div>

                                <div>
                                    <label for="payment_reference" class="block mb-2 text-sm font-black text-white">
                                        رقم مرجع التحويل
                                        <span class="font-normal text-[#8d90a0]">— اختياري</span>
                                    </label>
                                    <input id="payment_reference" name="payment_reference" type="text"
                                           value="{{ old('payment_reference') }}"
                                           placeholder="رقم العملية أو مرجع التحويل"
                                           class="office-register-input">
                                    @error('payment_reference')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-black text-white">
                                        إيصال دفع الاشتراك <span class="text-red-400">*</span>
                                    </label>

                                    <label
                                        for="payment_receipt"
                                        class="flex flex-col items-center justify-center p-10 text-center cursor-pointer office-register-upload rounded-2xl"
                                        :class="receiptDrag ? 'border-[#b4c5ff] bg-[#b4c5ff]/5' : ''"
                                        @dragover.prevent="receiptDrag=true"
                                        @dragleave.prevent="receiptDrag=false"
                                        @drop.prevent="receiptDrag=false; putDroppedFile($refs.receiptInput, $event, 'receiptName')"
                                    >
                                        <span class="text-5xl">⬆️</span>
                                        <span class="mt-3 text-lg font-black text-white">اضغط أو اسحب إيصال الدفع</span>
                                        <span class="mt-2 text-sm text-[#8d90a0]">JPG أو PNG أو WEBP أو PDF — حتى 10MB</span>

                                        <span x-cloak x-show="receiptName" x-text="receiptName"
                                              class="mt-4 rounded-lg bg-[#b4c5ff]/10 px-4 py-2 text-sm font-bold text-[#b4c5ff]"></span>
                                    </label>

                                    <input x-ref="receiptInput" id="payment_receipt"
                                           name="payment_receipt" type="file" required
                                           accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden"
                                           @change="receiptName=$event.target.files[0]?.name || ''">

                                    @error('payment_receipt')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                                </div>

                                <div class="p-5 border rounded-2xl border-red-400/20 bg-red-950/20">
                                    <h4 class="font-black text-red-300">ملاحظات مهمة</h4>
                                    <ul class="mt-3 list-inside list-disc space-y-2 text-sm text-[#c3c6d7]">
                                        <li>اكتب اسم مقدم الطلب أو اسم المكتب في وصف التحويل.</li>
                                        <li>يجب أن يكون الإيصال واضحًا وكاملًا.</li>
                                        <li>لن يتفعّل المكتب قبل مراجعة الطلب والإيصال.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <label for="notes" class="block mb-2 text-sm font-black text-white">
                            نبذة عن المكتب أو ملاحظات إضافية
                        </label>
                        <textarea id="notes" name="notes" rows="4"
                                  placeholder="اكتب نبذة مختصرة عن المكتب..."
                                  class="office-register-input">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                    </section>

                    <div class="flex flex-col gap-4 pt-6 border-t border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <label class="flex items-start gap-3 text-sm text-[#c3c6d7]">
                            <input name="terms" value="1" type="checkbox" required
                                   class="mt-1 rounded border-[#434655] bg-[#131b2e] text-blue-600 focus:ring-blue-500">
                            <span>
                                أوافق على الشروط والأحكام وسياسة الخصوصية، وأؤكد صحة البيانات والمستندات المرفوعة.
                            </span>
                        </label>

                        <button
                            type="submit"
                            :disabled="submitting"
                            class="office-register-neon flex min-w-56 items-center justify-center gap-3 rounded-xl bg-gradient-to-l from-[#2563eb] to-[#8343f4] px-8 py-4 font-black text-white transition hover:scale-[1.02] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-show="!submitting">إرسال طلب المكتب</span>
                            <span x-cloak x-show="submitting">جارٍ إرسال الطلب...</span>
                            <span x-show="!submitting">➤</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-7">
                <a href="{{ route('dashboard') }}" class="font-bold text-[#8d90a0] transition hover:text-[#b4c5ff]">
                    العودة إلى لوحة التحكم
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
