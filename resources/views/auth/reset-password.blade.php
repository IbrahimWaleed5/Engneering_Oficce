<x-guest-layout>

    <div
        class="relative w-full max-w-md px-4 py-10 mx-auto"
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
                    🔐
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    تعيين كلمة مرور جديدة
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    أدخل بريدك الإلكتروني ثم اختر كلمة مرور جديدة لحسابك.
                </p>

            </div>

            @if ($errors->any())

                <div
                    class="p-4 mb-5 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
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

            @endif

            <form
                method="POST"
                action="{{ route('password.store') }}"
                class="space-y-5"
            >
                @csrf

                <input
                    type="hidden"
                    name="token"
                    value="{{ request()->route('token') }}"
                >

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
                            value="{{ old('email', request('email')) }}"
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
                        كلمة المرور الجديدة
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
                            placeholder="أدخل كلمة المرور الجديدة"
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

                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div
                    class="p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-400"
                >
                    استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز، ولا تقل
                    عن 8 خانات.
                </div>

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    حفظ كلمة المرور الجديدة
                </button>

            </form>

            <div class="pt-6 mt-6 text-center border-t border-white/10">

                <a
                    href="{{ route('login') }}"
                    class="text-sm font-bold transition text-cyan-300 hover:text-cyan-200"
                >
                    العودة إلى تسجيل الدخول
                </a>

            </div>

        </div>

    </div>

</x-guest-layout>
