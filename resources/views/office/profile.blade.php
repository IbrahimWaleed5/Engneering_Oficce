<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <p class="mb-3 font-black">
                        توجد أخطاء في البيانات:
                    </p>

                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 mb-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-[#b4c5ff]">
                        إعدادات المكتب
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-[#dae2fd]">
                        الملف الشخصي للمكتب
                    </h1>

                    <p class="mt-3 leading-7 text-[#c3c6d7]">
                        حدّث بيانات المكتب والشعار والغلاف والمعلومات التي
                        تظهر للمهندسين في صفحة المكتب العامة.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if (Route::has('engineering-offices.show'))
                        <a
                            href="{{ route('engineering-offices.show', $office) }}"
                            class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-[#434655]/30 bg-[#222a3d] hover:bg-[#31394d]"
                        >
                            عرض صفحة المكتب
                        </a>
                    @endif

                    <a
                        href="{{ route('office.dashboard') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-[#434655]/30 bg-[#222a3d] hover:bg-[#31394d]"
                    >
                        لوحة المكتب
                    </a>
                </div>
            </div>

            @if ($office->status === 'suspended')
                <div class="p-6 mb-8 border rounded-3xl border-red-500/20 bg-red-500/10">
                    <h2 class="text-xl font-black text-red-200">
                        المكتب موقوف عن العمل
                    </h2>

                    <p class="mt-3 leading-8 text-red-100">
                        تستطيع تعديل بيانات المكتب، لكن المكتب لا يستقبل
                        طلبات انضمام أو استشارات جديدة أثناء الإيقاف.
                    </p>

                    @if ($office->suspension_reason)
                        <div class="p-4 mt-4 border rounded-2xl border-red-500/20 bg-red-950/20">
                            <p class="text-sm font-black text-red-200">
                                سبب الإيقاف
                            </p>

                            <p class="mt-2 leading-7 text-red-100">
                                {{ $office->suspension_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('office.profile.update') }}"
                enctype="multipart/form-data"
                class="space-y-6"
            >
                @csrf
                @method('PATCH')

                <section class="overflow-hidden border rounded-3xl border-[#434655]/20 bg-[#131b2e]/80">
                    <div class="p-6 border-b border-[#434655]/20 sm:p-8">
                        <h2 class="text-2xl font-black text-[#dae2fd]">
                            الصور التعريفية
                        </h2>

                        <p class="mt-2 text-sm leading-7 text-[#c3c6d7]">
                            الشعار يظهر بجانب اسم المكتب، والغلاف يظهر أعلى
                            الصفحة الشخصية للمكتب.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2">
                        <div>
                            <label class="block mb-3 font-bold text-[#dae2fd]">
                                شعار المكتب
                            </label>

                            <div class="flex items-center gap-5">
                                <div class="flex items-center justify-center flex-shrink-0 w-24 h-24 overflow-hidden text-3xl border rounded-2xl border-[#434655]/30 bg-[#0b1326]">
                                    @if ($office->logo_path)
                                        <img
                                            src="{{ asset('storage/' . $office->logo_path) }}"
                                            alt="{{ $office->name }}"
                                            class="object-cover w-full h-full"
                                        >
                                    @else
                                        🏢
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <input
                                        type="file"
                                        name="logo"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-sm text-white file:ml-4 file:rounded-lg file:border-0 file:bg-[#2563eb] file:px-4 file:py-2 file:font-bold file:text-white"
                                    >

                                    <p class="mt-2 text-xs leading-6 text-[#8d90a0]">
                                        JPG أو PNG أو WEBP، بحد أقصى 4 ميجابايت.
                                    </p>

                                    @if ($office->logo_path)
                                        <label class="flex items-center gap-2 mt-3 text-sm text-red-200">
                                            <input
                                                type="checkbox"
                                                name="remove_logo"
                                                value="1"
                                                class="rounded border-[#434655] bg-[#0b1326] text-red-500 focus:ring-red-500"
                                            >

                                            حذف الشعار الحالي
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-3 font-bold text-[#dae2fd]">
                                صورة الغلاف
                            </label>

                            <div class="overflow-hidden border rounded-2xl border-[#434655]/30 bg-[#0b1326]">
                                <div class="flex items-center justify-center h-32 text-4xl">
                                    @if ($office->cover_path)
                                        <img
                                            src="{{ asset('storage/' . $office->cover_path) }}"
                                            alt="{{ $office->name }}"
                                            class="object-cover w-full h-full"
                                        >
                                    @else
                                        🏙️
                                    @endif
                                </div>
                            </div>

                            <input
                                type="file"
                                name="cover"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="w-full mt-4 rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-sm text-white file:ml-4 file:rounded-lg file:border-0 file:bg-[#2563eb] file:px-4 file:py-2 file:font-bold file:text-white"
                            >

                            <p class="mt-2 text-xs leading-6 text-[#8d90a0]">
                                يفضّل استخدام صورة أفقية، بحد أقصى 6 ميجابايت.
                            </p>

                            @if ($office->cover_path)
                                <label class="flex items-center gap-2 mt-3 text-sm text-red-200">
                                    <input
                                        type="checkbox"
                                        name="remove_cover"
                                        value="1"
                                        class="rounded border-[#434655] bg-[#0b1326] text-red-500 focus:ring-red-500"
                                    >

                                    حذف صورة الغلاف الحالية
                                </label>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="border rounded-3xl border-[#434655]/20 bg-[#131b2e]/80">
                    <div class="p-6 border-b border-[#434655]/20 sm:p-8">
                        <h2 class="text-2xl font-black text-[#dae2fd]">
                            البيانات الأساسية
                        </h2>
                    </div>

                    <div class="grid gap-5 p-6 sm:p-8 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label
                                for="name"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                اسم المكتب
                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $office->name) }}"
                                required
                                maxlength="200"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                البريد الإلكتروني
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $office->email) }}"
                                required
                                maxlength="255"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label
                                for="phone"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                رقم الهاتف
                            </label>

                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ old('phone', $office->phone) }}"
                                maxlength="30"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label
                                for="commercial_registration"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                رقم السجل التجاري
                            </label>

                            <input
                                id="commercial_registration"
                                type="text"
                                name="commercial_registration"
                                value="{{ old(
                                    'commercial_registration',
                                    $office->commercial_registration
                                ) }}"
                                maxlength="100"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label
                                for="license_number"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                رقم الترخيص
                            </label>

                            <input
                                id="license_number"
                                type="text"
                                name="license_number"
                                value="{{ old('license_number', $office->license_number) }}"
                                maxlength="100"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>
                    </div>
                </section>

                <section class="border rounded-3xl border-[#434655]/20 bg-[#131b2e]/80">
                    <div class="p-6 border-b border-[#434655]/20 sm:p-8">
                        <h2 class="text-2xl font-black text-[#dae2fd]">
                            الموقع والعنوان
                        </h2>
                    </div>

                    <div class="grid gap-5 p-6 sm:p-8 md:grid-cols-2">
                        <div>
                            <label
                                for="country"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                الدولة
                            </label>

                            <input
                                id="country"
                                type="text"
                                name="country"
                                value="{{ old('country', $office->country) }}"
                                maxlength="100"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div>
                            <label
                                for="city"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                المدينة
                            </label>

                            <input
                                id="city"
                                type="text"
                                name="city"
                                value="{{ old('city', $office->city) }}"
                                maxlength="100"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label
                                for="address"
                                class="block mb-2 font-bold text-[#dae2fd]"
                            >
                                العنوان التفصيلي
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="4"
                                maxlength="1000"
                                class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 text-white focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                            >{{ old('address', $office->address) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="border rounded-3xl border-[#434655]/20 bg-[#131b2e]/80">
                    <div class="p-6 border-b border-[#434655]/20 sm:p-8">
                        <h2 class="text-2xl font-black text-[#dae2fd]">
                            نبذة عن المكتب
                        </h2>

                        <p class="mt-2 text-sm leading-7 text-[#c3c6d7]">
                            اكتب تعريفًا واضحًا عن خدمات المكتب وخبراته
                            وتخصصاته الهندسية.
                        </p>
                    </div>

                    <div class="p-6 sm:p-8">
                        <textarea
                            id="description"
                            name="description"
                            rows="9"
                            maxlength="5000"
                            placeholder="اكتب نبذة تعريفية عن المكتب..."
                            class="w-full rounded-xl border border-[#434655]/30 bg-[#0b1326] px-4 py-3 leading-8 text-white placeholder:text-[#8d90a0] focus:border-[#b4c5ff] focus:ring-1 focus:ring-[#b4c5ff]"
                        >{{ old('description', $office->description) }}</textarea>
                    </div>
                </section>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-7 py-3 font-black text-white transition rounded-xl bg-[#2563eb] shadow-lg shadow-blue-500/20 hover:brightness-110"
                    >
                        حفظ بيانات المكتب
                    </button>

                    <a
                        href="{{ route('office.dashboard') }}"
                        class="inline-flex items-center justify-center px-7 py-3 font-bold text-[#dae2fd] transition border rounded-xl border-[#434655]/30 bg-[#222a3d] hover:bg-[#31394d]"
                    >
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
