<x-guest-layout>

    <div
        class="relative w-full max-w-2xl px-4 py-10 mx-auto"
        dir="rtl"
        x-data="{
            showPassword: false,
            showPasswordConfirmation: false
        }"
    >

        <div class="p-8 glass-panel-strong rounded-[2rem]">

            <div class="mb-8 text-center">

                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto text-2xl shadow-lg rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-500/20"
                >
                    👤
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    إنشاء حساب جديد
                </h1>

                <p class="mt-2 text-sm leading-7 text-slate-400">
                    أنشئ حسابك وابدأ بطلب ومتابعة الاستشارات الهندسية
                </p>

            </div>

            @if ($errors->any())

                <div
                    class="p-4 mb-6 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <p class="mb-2 font-bold">
                        تعذر إنشاء الحساب
                    </p>

                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @endif

            <form
                method="POST"
                action="{{ route('register') }}"
                enctype="multipart/form-data"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="profile_photo"
                        class="block mb-2 text-sm font-bold text-slate-200"
                    >
                        الصورة الشخصية
                    </label>

                    <div class="flex flex-col items-center gap-4 p-5 border rounded-2xl border-slate-700 bg-slate-900/60 sm:flex-row">

                        <div class="shrink-0">

                            <template x-if="photoPreview">
                                <img
                                    :src="photoPreview"
                                    alt="معاينة الصورة الشخصية"
                                    class="object-cover w-24 h-24 border-2 rounded-full border-cyan-500/40"
                                >
                            </template>

                            <template x-if="!photoPreview">
                                <div class="flex items-center justify-center w-24 h-24 text-3xl border-2 rounded-full border-slate-700 bg-slate-800">
                                    👤
                                </div>
                            </template>

                        </div>

                        <div class="w-full">

                            <input
                                id="profile_photo"
                                type="file"
                                name="profile_photo"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                required
                                class="block w-full text-sm text-slate-300 file:ml-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-bold file:text-white hover:file:bg-blue-700"
                                @change="
                                    const file = $event.target.files[0];

                                    if (file) {
                                        const reader = new FileReader();

                                        reader.onload = event => {
                                            photoPreview = event.target.result;
                                        };

                                        reader.readAsDataURL(file);
                                    } else {
                                        photoPreview = null;
                                    }
                                "
                            >

                            <p class="mt-2 text-xs leading-6 text-slate-500">
                                الصيغ المسموحة: JPG، JPEG، PNG، WEBP — الحد الأقصى 2MB.
                            </p>

                            @error('profile_photo')
                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>

                <div class="grid gap-5 md:grid-cols-2">

                    <div>

                        <label
                            for="name"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            الاسم الكامل
                        </label>

                        <div class="relative">

                            <span
                                class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                            >
                                👤
                            </span>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="أدخل الاسم الكامل"
                                class="pr-12 form-control"
                            >

                        </div>

                        @error('name')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label
                            for="phone"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            رقم الهاتف
                        </label>

                        <div class="relative">

                            <span
                                class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                            >
                                📱
                            </span>

                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                                autocomplete="tel"
                                placeholder="مثال: 0590000000"
                                class="pr-12 form-control"
                            >

                        </div>

                        @error('phone')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

                <div>

                    <label
                        for="email"
                        class="block mb-2 text-sm font-bold text-slate-200"
                    >
                        البريد الإلكتروني
                    </label>

                    <div class="relative">

                        <span
                            class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                        >
                            ✉️
                        </span>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="example@email.com"
                            class="pr-12 form-control"
                        >

                    </div>

                    @error('email')
                        <p class="mt-2 text-sm text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div class="grid gap-5 md:grid-cols-2">

                    <div>

                        <label
                            for="password"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            كلمة المرور
                        </label>

                        <div class="relative">

                            <span
                                class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                            >
                                🔒
                            </span>

                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="أدخل كلمة المرور"
                                class="pr-12 form-control pl-14"
                            >

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300 hover:text-cyan-200"
                            >
                                <span x-show="!showPassword">
                                    إظهار
                                </span>

                                <span
                                    x-cloak
                                    x-show="showPassword"
                                >
                                    إخفاء
                                </span>
                            </button>

                        </div>

                        @error('password')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label
                            for="password_confirmation"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            تأكيد كلمة المرور
                        </label>

                        <div class="relative">

                            <span
                                class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                            >
                                🔐
                            </span>

                            <input
                                id="password_confirmation"
                                :type="showPasswordConfirmation ? 'text' : 'password'"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="أعد كتابة كلمة المرور"
                                class="pr-12 form-control pl-14"
                            >

                            <button
                                type="button"
                                @click="
                                    showPasswordConfirmation =
                                        !showPasswordConfirmation
                                "
                                class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300 hover:text-cyan-200"
                            >
                                <span x-show="!showPasswordConfirmation">
                                    إظهار
                                </span>

                                <span
                                    x-cloak
                                    x-show="showPasswordConfirmation"
                                >
                                    إخفاء
                                </span>
                            </button>

                        </div>

                    </div>

                </div>

                <div
                    class="p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-400"
                >
                    عند إنشاء الحساب سيتم تسجيلك كعميل تلقائيًا، ويمكن للإدارة
                    لاحقًا تعديل نوع الحساب عند الحاجة.
                </div>

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    إنشاء الحساب
                </button>

            </form>

            <div class="pt-6 mt-6 text-center border-t border-white/10">

                <p class="text-sm text-slate-400">
                    لديك حساب بالفعل؟

                    <a
                        href="{{ route('login') }}"
                        class="font-bold text-cyan-300 hover:text-cyan-200"
                    >
                        تسجيل الدخول
                    </a>
                </p>

            </div>

            <div class="mt-5 text-center">

                <a
                    href="{{ url('/') }}"
                    class="text-sm transition text-slate-500 hover:text-slate-300"
                >
                    العودة إلى الصفحة الرئيسية
                </a>

            </div>

        </div>

    </div>

</x-guest-layout>
