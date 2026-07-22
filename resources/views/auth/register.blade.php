<x-guest-layout>

    <div
        class="w-full px-4 py-8 sm:px-6"
        dir="rtl"
        x-data="{
            showPassword: false,
            showPasswordConfirmation: false,
            photoPreview: null,

            previewPhoto(event) {
                const file = event.target.files[0];

                if (!file) {
                    this.photoPreview = null;
                    return;
                }

                const reader = new FileReader();

                reader.onload = (event) => {
                    this.photoPreview = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        }"
    >

        <div
            class="grid w-full max-w-7xl mx-auto overflow-hidden border shadow-2xl lg:grid-cols-[0.9fr_1.1fr] rounded-[2rem] border-white/10 bg-slate-950/90 shadow-blue-950/50"
        >

            {{-- القسم التعريفي --}}
            <section
                class="relative hidden min-h-[850px] overflow-hidden lg:flex"
            >

                <div
                    class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900"
                ></div>

                {{-- الشبكة الهندسية --}}
                <div
                    class="absolute inset-0 opacity-30"
                    style="
                        background-image:
                            linear-gradient(
                                rgba(59, 130, 246, .15) 1px,
                                transparent 1px
                            ),
                            linear-gradient(
                                90deg,
                                rgba(59, 130, 246, .15) 1px,
                                transparent 1px
                            );

                        background-size: 45px 45px;
                    "
                ></div>

                {{-- الإضاءات --}}
                <div
                    class="absolute w-64 h-64 rounded-full -top-20 -right-20 bg-blue-500/20 blur-3xl"
                ></div>

                <div
                    class="absolute rounded-full -bottom-24 -left-20 w-80 h-80 bg-cyan-500/20 blur-3xl"
                ></div>

                {{-- رسم هندسي --}}
                <div
                    class="absolute inset-x-0 bottom-0 opacity-50 h-72"
                >

                    <svg
                        viewBox="0 0 800 320"
                        class="w-full h-full"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >

                        <path
                            d="M0 300H800"
                            stroke="#38BDF8"
                            stroke-opacity=".35"
                        />

                        <path
                            d="M35 300V175L135 105V300"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M135 300V125L235 60V300"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M235 300V185L325 125V300"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M325 300V100L440 40V300"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M440 300V160L535 95V300"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M535 300V120L655 55V300"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M655 300V175L760 110V300"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        @for ($x = 75; $x <= 725; $x += 55)

                            <path
                                d="M{{ $x }} 145V290"
                                stroke="#60A5FA"
                                stroke-opacity=".18"
                            />

                        @endfor

                    </svg>

                </div>

                <div
                    class="relative z-10 flex flex-col justify-between w-full p-10 xl:p-12"
                >

                    {{-- الشعار --}}
                    <div>

                        <a
                            href="{{ route('home') }}"
                            class="inline-flex items-center gap-4"
                        >

                            <div
                                class="flex items-center justify-center border shadow-xl w-14 h-14 rounded-2xl border-cyan-400/20 bg-blue-500/15 shadow-blue-500/10"
                            >

                                <svg
                                    class="w-8 h-8 text-cyan-300"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path d="M4 21V9l8-6 8 6v12" />
                                    <path d="M8 21v-8h8v8" />
                                    <path d="M12 3v6" />
                                </svg>

                            </div>

                            <div>

                                <h2 class="text-xl font-black text-white">
                                    مكتب الوليد الهندسي
                                </h2>

                                <p class="mt-1 text-sm text-cyan-200/70">
                                    حلول هندسية متكاملة
                                </p>

                            </div>

                        </a>

                    </div>

                    {{-- النص --}}
                    <div class="max-w-md">

                        <span
                            class="inline-flex px-4 py-2 text-xs font-bold border rounded-full border-cyan-400/20 bg-cyan-400/10 text-cyan-200"
                        >
                            انضم إلى منصتنا
                        </span>

                        <h1
                            class="mt-6 text-4xl font-black leading-tight text-white"
                        >
                            أنشئ حسابك

                            <span
                                class="block mt-2 text-transparent bg-gradient-to-l from-cyan-300 to-blue-400 bg-clip-text"
                            >
                                وابدأ مشروعك الهندسي
                            </span>
                        </h1>

                        <p
                            class="mt-5 text-base leading-8 text-slate-300"
                        >
                            أنشئ حسابًا جديدًا لتقديم طلبات الاستشارة،
                            اختيار المهندس المناسب، متابعة الدفع والتواصل
                            واستلام الملفات النهائية.
                        </p>

                    </div>

                    {{-- المميزات --}}
                    <div class="grid grid-cols-3 gap-3">

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                👷
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                مهندسون متخصصون
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                💬
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                تواصل مباشر
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                🔒
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                حساب آمن
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            {{-- نموذج إنشاء الحساب --}}
            <section
                class="flex items-center min-h-[850px] p-6 sm:p-10 lg:p-12 xl:p-14 bg-slate-900/95"
            >

                <div class="w-full max-w-2xl mx-auto">

                    {{-- شعار الموبايل --}}
                    <a
                        href="{{ route('home') }}"
                        class="flex items-center justify-center gap-3 mb-9 lg:hidden"
                    >

                        <div
                            class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500"
                        >

                            <svg
                                class="text-white w-7 h-7"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 21V9l8-6 8 6v12" />
                                <path d="M8 21v-8h8v8" />
                            </svg>

                        </div>

                        <div>

                            <p class="font-black text-white">
                                مكتب الوليد الهندسي
                            </p>

                            <p class="text-xs text-slate-400">
                                حلول هندسية متكاملة
                            </p>

                        </div>

                    </a>

                    {{-- عنوان الصفحة --}}
                    <div>

                        <div
                            class="flex items-center justify-center w-16 h-16 border shadow-lg rounded-2xl border-blue-400/20 bg-blue-500/10 shadow-blue-500/10"
                        >

                            <svg
                                class="w-8 h-8 text-cyan-300"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                />

                                <circle cx="9" cy="7" r="4" />

                                <path d="M19 8v6" />
                                <path d="M22 11h-6" />
                            </svg>

                        </div>

                        <h1 class="mt-6 text-3xl font-black text-white">
                            إنشاء حساب جديد
                        </h1>

                        <p class="mt-3 text-sm leading-7 text-slate-400">
                            أدخل بياناتك لإنشاء حساب عميل جديد في المنصة.
                        </p>

                    </div>

                    {{-- الأخطاء --}}
                    @if ($errors->any())

                        <div
                            class="p-4 mt-6 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                        >

                            <div class="flex items-start gap-3">

                                <span class="text-lg">
                                    ⚠️
                                </span>

                                <div>

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

                            </div>

                        </div>

                    @endif

                    <form
                        method="POST"
                        action="{{ route('register') }}"
                        enctype="multipart/form-data"
                        class="mt-8 space-y-6"
                    >
                        @csrf

                        {{-- الصورة الشخصية --}}
                        <div>

                            <label
                                for="profile_photo"
                                class="block mb-3 text-sm font-bold text-slate-200"
                            >
                                الصورة الشخصية
                            </label>

                            <label
                                for="profile_photo"
                                class="flex flex-col items-center gap-5 p-5 transition border border-dashed cursor-pointer rounded-3xl border-white/15 bg-slate-950/50 hover:border-cyan-500/50 hover:bg-slate-950/80 sm:flex-row"
                            >

                                <div class="relative flex-none">

                                    <template x-if="photoPreview">

                                        <img
                                            :src="photoPreview"
                                            alt="معاينة الصورة الشخصية"
                                            class="object-cover w-24 h-24 border-2 rounded-full shadow-lg border-cyan-500/50 shadow-cyan-500/10"
                                        >

                                    </template>

                                    <template x-if="!photoPreview">

                                        <div
                                            class="flex items-center justify-center w-24 h-24 border-2 rounded-full border-white/10 bg-slate-800"
                                        >

                                            <svg
                                                class="w-10 h-10 text-slate-500"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                            >
                                                <path
                                                    d="M20 21a8 8 0 0 0-16 0"
                                                />

                                                <circle
                                                    cx="12"
                                                    cy="7"
                                                    r="4"
                                                />
                                            </svg>

                                        </div>

                                    </template>

                                    <div
                                        class="absolute bottom-0 left-0 flex items-center justify-center w-8 h-8 text-sm text-white bg-blue-600 border-2 rounded-full border-slate-900"
                                    >
                                        📷
                                    </div>

                                </div>

                                <div class="flex-1 text-center sm:text-right">

                                    <p class="font-bold text-white">
                                        اختر صورتك الشخصية
                                    </p>

                                    <p
                                        class="mt-2 text-xs leading-6 text-slate-500"
                                    >
                                        JPG أو JPEG أو PNG أو WEBP، والحد
                                        الأقصى لحجم الصورة 2MB.
                                    </p>

                                    <span
                                        class="inline-flex px-4 py-2 mt-3 text-xs font-bold text-blue-200 rounded-xl bg-blue-500/10"
                                    >
                                        اختيار صورة
                                    </span>

                                </div>

                            </label>

                            <input
                                id="profile_photo"
                                type="file"
                                name="profile_photo"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                required
                                class="hidden"
                                @change="previewPhoto($event)"
                            >

                            @error('profile_photo')

                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        {{-- الاسم والهاتف --}}
                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>

                                <label
                                    for="name"
                                    class="block mb-3 text-sm font-bold text-slate-200"
                                >
                                    الاسم الكامل
                                </label>

                                <div class="relative">

                                    <span
                                        class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500"
                                    >

                                        <svg
                                            class="w-5 h-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <circle
                                                cx="12"
                                                cy="7"
                                                r="4"
                                            />

                                            <path
                                                d="M4 21a8 8 0 0 1 16 0"
                                            />
                                        </svg>

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
                                        class="w-full py-4 pl-4 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
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
                                    class="block mb-3 text-sm font-bold text-slate-200"
                                >
                                    رقم الهاتف
                                </label>

                                <div class="relative">

                                    <span
                                        class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500"
                                    >

                                        <svg
                                            class="w-5 h-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <rect
                                                x="6"
                                                y="2"
                                                width="12"
                                                height="20"
                                                rx="2"
                                            />

                                            <path d="M10 18h4" />
                                        </svg>

                                    </span>

                                    <input
                                        id="phone"
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        required
                                        autocomplete="tel"
                                        placeholder="0590000000"
                                        class="w-full py-4 pl-4 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        dir="ltr"
                                    >

                                </div>

                                @error('phone')

                                    <p class="mt-2 text-sm text-red-300">
                                        {{ $message }}
                                    </p>

                                @enderror

                            </div>

                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div>

                            <label
                                for="email"
                                class="block mb-3 text-sm font-bold text-slate-200"
                            >
                                البريد الإلكتروني
                            </label>

                            <div class="relative">

                                <span
                                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500"
                                >

                                    <svg
                                        class="w-5 h-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <rect
                                            x="3"
                                            y="5"
                                            width="18"
                                            height="14"
                                            rx="2"
                                        />

                                        <path d="m3 7 9 6 9-6" />
                                    </svg>

                                </span>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="username"
                                    placeholder="example@email.com"
                                    class="w-full py-4 pl-4 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                    dir="ltr"
                                >

                            </div>

                            @error('email')

                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        {{-- كلمات المرور --}}
                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>

                                <label
                                    for="password"
                                    class="block mb-3 text-sm font-bold text-slate-200"
                                >
                                    كلمة المرور
                                </label>

                                <div class="relative">

                                    <span
                                        class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500"
                                    >

                                        <svg
                                            class="w-5 h-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <rect
                                                x="5"
                                                y="10"
                                                width="14"
                                                height="10"
                                                rx="2"
                                            />

                                            <path
                                                d="M8 10V7a4 4 0 0 1 8 0v3"
                                            />
                                        </svg>

                                    </span>

                                    <input
                                        id="password"
                                        :type="showPassword
                                            ? 'text'
                                            : 'password'"
                                        name="password"
                                        required
                                        autocomplete="new-password"
                                        placeholder="كلمة المرور"
                                        class="w-full py-4 pl-20 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        dir="ltr"
                                    >

                                    <button
                                        type="button"
                                        @click="
                                            showPassword =
                                                !showPassword
                                        "
                                        class="absolute text-xs font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300 hover:text-cyan-200"
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
                                    class="block mb-3 text-sm font-bold text-slate-200"
                                >
                                    تأكيد كلمة المرور
                                </label>

                                <div class="relative">

                                    <span
                                        class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2 text-slate-500"
                                    >

                                        <svg
                                            class="w-5 h-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        >
                                            <rect
                                                x="5"
                                                y="10"
                                                width="14"
                                                height="10"
                                                rx="2"
                                            />

                                            <path
                                                d="M8 10V7a4 4 0 0 1 8 0v3"
                                            />
                                        </svg>

                                    </span>

                                    <input
                                        id="password_confirmation"
                                        :type="
                                            showPasswordConfirmation
                                                ? 'text'
                                                : 'password'
                                        "
                                        name="password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        placeholder="تأكيد كلمة المرور"
                                        class="w-full py-4 pl-20 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                        dir="ltr"
                                    >

                                    <button
                                        type="button"
                                        @click="
                                            showPasswordConfirmation =
                                                !showPasswordConfirmation
                                        "
                                        class="absolute text-xs font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300 hover:text-cyan-200"
                                    >

                                        <span
                                            x-show="
                                                !showPasswordConfirmation
                                            "
                                        >
                                            إظهار
                                        </span>

                                        <span
                                            x-cloak
                                            x-show="
                                                showPasswordConfirmation
                                            "
                                        >
                                            إخفاء
                                        </span>

                                    </button>

                                </div>

                            </div>

                        </div>

                        <div
                            class="flex items-start gap-3 p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-400"
                        >

                            <span class="flex-none text-lg">
                                ℹ️
                            </span>

                            <p>
                                عند إنشاء الحساب سيتم تسجيلك كعميل تلقائيًا،
                                ويمكن للإدارة لاحقًا تعديل نوع الحساب.
                            </p>

                        </div>

                        <button
                            type="submit"
                            class="flex items-center justify-center w-full gap-3 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-blue-600 to-cyan-500 shadow-blue-500/20 hover:-translate-y-0.5 hover:shadow-blue-500/30"
                        >

                            <span>
                                إنشاء الحساب
                            </span>

                            <svg
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>

                        </button>

                    </form>

                    <div
                        class="pt-6 mt-8 text-center border-t border-white/10"
                    >

                        <p class="text-sm text-slate-400">
                            لديك حساب بالفعل؟
                        </p>

                        <a
                            href="{{ route('login') }}"
                            class="flex items-center justify-center w-full gap-2 px-5 py-3 mt-4 text-sm font-bold transition border rounded-2xl border-white/10 text-slate-200 bg-white/5 hover:bg-white/10 hover:text-white"
                        >
                            تسجيل الدخول
                        </a>

                    </div>

                    <div class="mt-5 text-center">

                        <a
                            href="{{ route('home') }}"
                            class="text-sm transition text-slate-500 hover:text-cyan-300"
                        >
                            العودة إلى الصفحة الرئيسية
                        </a>

                    </div>

                </div>

            </section>

        </div>

    </div>

</x-guest-layout>
