<x-guest-layout>

    <div
        class="relative w-full max-w-md px-4 py-10 mx-auto"
        dir="rtl"
        x-data="{ showPassword: false }"
    >

        <div class="p-8 glass-panel-strong rounded-[2rem]">

            <div class="mb-8 text-center">

                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto text-2xl shadow-lg rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-500/20"
                >
                    🏢
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    تسجيل الدخول
                </h1>

                <p class="mt-2 text-sm leading-7 text-slate-400">
                    ادخل إلى حسابك لمتابعة الاستشارات والمشاريع الهندسية
                </p>

            </div>

            <x-auth-session-status
                class="p-4 mb-5 text-sm text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                :status="session('status')"
            />

            @if ($errors->any())

                <div
                    class="p-4 mb-5 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <p class="mb-2 font-bold">
                        تعذر تسجيل الدخول
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
                action="{{ route('login') }}"
                class="space-y-5"
            >
                @csrf

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
                            autofocus
                            autocomplete="username"
                            placeholder="example@email.com"
                            class="pr-12 form-control"
                            dir="ltr"
                        >

                    </div>

                    @error('email')
                        <p class="mt-2 text-sm text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

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
                            autocomplete="current-password"
                            placeholder="أدخل كلمة المرور"
                            class="pr-12 form-control pl-14"
                            dir="ltr"
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

                <div class="flex flex-wrap items-center justify-between gap-3">

                    <label
                        for="remember_me"
                        class="inline-flex items-center gap-2 cursor-pointer"
                    >
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-blue-600 rounded border-slate-600 bg-slate-900 focus:ring-blue-500 focus:ring-offset-slate-950"
                        >

                        <span class="text-sm text-slate-400">
                            تذكرني
                        </span>
                    </label>

                    @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm font-bold transition text-cyan-300 hover:text-cyan-200"
                        >
                            نسيت كلمة المرور؟
                        </a>

                    @endif

                </div>

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    تسجيل الدخول
                </button>

            </form>

            @if (Route::has('register'))

                <div class="pt-6 mt-6 text-center border-t border-white/10">

                    <p class="text-sm text-slate-400">
                        ليس لديك حساب؟
                        <a
                            href="{{ route('register') }}"
                            class="font-bold text-cyan-300 hover:text-cyan-200"
                        >
                            إنشاء حساب جديد
                        </a>
                    </p>

                </div>

            @endif

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
