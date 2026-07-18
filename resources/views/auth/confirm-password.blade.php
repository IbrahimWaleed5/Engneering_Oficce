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
                    🛡️
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    تأكيد كلمة المرور
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    هذه منطقة محمية داخل النظام، يرجى إدخال كلمة المرور
                    للمتابعة.
                </p>

            </div>

            @if ($errors->any())

                <div
                    class="p-4 mb-5 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif

            <form
                method="POST"
                action="{{ route('password.confirm') }}"
                class="space-y-5"
            >
                @csrf

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

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    تأكيد
                </button>

            </form>

            <div class="pt-6 mt-6 text-center border-t border-white/10">

                <a
                    href="{{ route('dashboard') }}"
                    class="text-sm text-slate-400 hover:text-slate-200"
                >
                    العودة إلى لوحة التحكم
                </a>

            </div>

        </div>

    </div>

</x-guest-layout>
