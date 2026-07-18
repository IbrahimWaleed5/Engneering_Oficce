<x-guest-layout>

    <div
        class="relative w-full max-w-md px-4 py-10 mx-auto"
        dir="rtl"
    >

        <div class="p-8 glass-panel-strong rounded-[2rem]">

            <div class="mb-8 text-center">

                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto text-2xl shadow-lg rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-500/20"
                >
                    🔑
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    نسيت كلمة المرور؟
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    أدخل بريدك الإلكتروني وسنرسل لك رابطًا لإنشاء كلمة مرور جديدة.
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
                        تعذر إرسال رابط الاستعادة
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
                action="{{ route('password.email') }}"
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
                            autocomplete="email"
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

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    إرسال رابط استعادة كلمة المرور
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
