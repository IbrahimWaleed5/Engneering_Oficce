<x-guest-layout>

    <div
        class="w-full px-4 py-8 sm:px-6"
        dir="rtl"
        x-data="{
            showPassword: false,
            showPasswordConfirmation: false
        }"
    >

        <div
            class="grid w-full max-w-6xl mx-auto overflow-hidden border shadow-2xl lg:grid-cols-2 rounded-[2rem] border-white/10 bg-slate-950/90 shadow-blue-950/50"
        >

            {{-- القسم التعريفي --}}
            <section
                class="relative hidden min-h-[760px] overflow-hidden lg:flex"
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

                {{-- الرسم الهندسي --}}
                <div
                    class="absolute inset-x-0 bottom-0 h-64 opacity-50"
                >

                    <svg
                        viewBox="0 0 800 300"
                        class="w-full h-full"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >

                        <path
                            d="M0 280H800"
                            stroke="#38BDF8"
                            stroke-opacity=".35"
                        />

                        <path
                            d="M50 280V155L150 90V280"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M150 280V115L245 55V280"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M245 280V170L335 115V280"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M335 280V90L445 35V280"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M445 280V145L540 85V280"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        <path
                            d="M540 280V110L660 45V280"
                            stroke="#38BDF8"
                            stroke-width="2"
                        />

                        <path
                            d="M660 280V160L750 105V280"
                            stroke="#3B82F6"
                            stroke-width="2"
                        />

                        @for ($x = 80; $x <= 720; $x += 55)

                            <path
                                d="M{{ $x }} 130V270"
                                stroke="#60A5FA"
                                stroke-opacity=".18"
                            />

                        @endfor

                    </svg>

                </div>

                <div
                    class="relative z-10 flex flex-col justify-between w-full p-10"
                >

                    {{-- شعار المكتب --}}
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
                            استعادة الحساب بأمان
                        </span>

                        <h1
                            class="mt-6 text-4xl font-black leading-tight text-white"
                        >
                            أنشئ كلمة مرور

                            <span
                                class="block mt-2 text-transparent bg-gradient-to-l from-cyan-300 to-blue-400 bg-clip-text"
                            >
                                جديدة وقوية لحسابك
                            </span>
                        </h1>

                        <p
                            class="mt-5 text-base leading-8 text-slate-300"
                        >
                            اختر كلمة مرور يصعب تخمينها، وتجنب استخدام
                            المعلومات الشخصية أو كلمات المرور القديمة.
                        </p>

                    </div>

                    {{-- المميزات --}}
                    <div class="grid grid-cols-3 gap-3">

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                🔐
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                حماية الحساب
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                🛡️
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                رابط آمن
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                ⚡
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                استعادة سريعة
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            {{-- نموذج تعيين كلمة المرور --}}
            <section
                class="flex items-center min-h-[760px] p-6 sm:p-10 lg:p-14 bg-slate-900/95"
            >

                <div class="w-full max-w-md mx-auto">

                    {{-- شعار الموبايل --}}
                    <a
                        href="{{ route('home') }}"
                        class="flex items-center justify-center gap-3 mb-10 lg:hidden"
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
                                <rect
                                    x="5"
                                    y="10"
                                    width="14"
                                    height="10"
                                    rx="2"
                                />

                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />

                                <path d="M12 14v2" />
                            </svg>

                        </div>

                        <h1 class="mt-6 text-3xl font-black text-white">
                            تعيين كلمة مرور جديدة
                        </h1>

                        <p class="mt-3 text-sm leading-7 text-slate-400">
                            أدخل بريدك الإلكتروني واختر كلمة مرور جديدة
                            لحسابك.
                        </p>

                    </div>

                    {{-- رسائل الأخطاء --}}
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
                                        تعذر إعادة تعيين كلمة المرور
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
                        action="{{ route('password.store') }}"
                        class="mt-8 space-y-6"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="token"
                            value="{{ request()->route('token') }}"
                        >

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
                                    value="{{ old(
                                        'email',
                                        request('email')
                                    ) }}"
                                    required
                                    autofocus
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

                        {{-- كلمة المرور --}}
                        <div>

                            <label
                                for="password"
                                class="block mb-3 text-sm font-bold text-slate-200"
                            >
                                كلمة المرور الجديدة
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
                                    placeholder="أدخل كلمة المرور الجديدة"
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

                        {{-- تأكيد كلمة المرور --}}
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

                                        <path d="m10 15 1.5 1.5L15 13" />
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
                                    placeholder="أعد كتابة كلمة المرور"
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

                            @error('password_confirmation')

                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        {{-- تنبيه قوة كلمة المرور --}}
                        <div
                            class="flex items-start gap-3 p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-400"
                        >

                            <div
                                class="flex items-center justify-center flex-none text-lg w-9 h-9 rounded-xl bg-cyan-500/10"
                            >
                                🛡️
                            </div>

                            <div>

                                <p class="font-bold text-slate-300">
                                    استخدم كلمة مرور قوية
                                </p>

                                <p class="mt-1 text-xs leading-6">
                                    يُفضل أن تحتوي على أحرف كبيرة وصغيرة
                                    وأرقام ورموز، وألا تقل عن 8 خانات.
                                </p>

                            </div>

                        </div>

                        {{-- زر الحفظ --}}
                        <button
                            type="submit"
                            class="flex items-center justify-center w-full gap-3 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-blue-600 to-cyan-500 shadow-blue-500/20 hover:-translate-y-0.5 hover:shadow-blue-500/30"
                        >

                            <span>
                                حفظ كلمة المرور الجديدة
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
                        class="pt-6 mt-8 border-t border-white/10"
                    >

                        <a
                            href="{{ route('login') }}"
                            class="flex items-center justify-center w-full gap-2 px-5 py-3 text-sm font-bold transition border rounded-2xl border-white/10 text-slate-300 bg-white/5 hover:bg-white/10 hover:text-white"
                        >
                            <span>
                                ←
                            </span>

                            العودة إلى تسجيل الدخول
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
