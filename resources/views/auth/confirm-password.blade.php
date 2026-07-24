<x-guest-layout>

    <div
        class="w-full px-4 py-8 sm:px-6"
        dir="rtl"
    >

        <div
            class="grid w-full max-w-6xl mx-auto overflow-hidden border shadow-2xl lg:grid-cols-2 rounded-[2rem] border-white/10 bg-slate-950/90 shadow-blue-950/50"
        >

            {{-- القسم التعريفي --}}
            <section
                class="relative hidden min-h-[680px] overflow-hidden lg:flex"
            >

                {{-- الخلفية --}}
                <div
                    class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900"
                ></div>

                <div
                    class="absolute inset-0 opacity-30"
                    style="
                        background-image:
                            linear-gradient(rgba(59,130,246,.15) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(59,130,246,.15) 1px, transparent 1px);
                        background-size: 45px 45px;
                    "
                ></div>

                {{-- إضاءات --}}
                <div
                    class="absolute w-64 h-64 rounded-full -top-20 -right-20 bg-blue-500/20 blur-3xl"
                ></div>

                <div
                    class="absolute rounded-full -bottom-24 -left-20 w-80 h-80 bg-cyan-500/20 blur-3xl"
                ></div>

                {{-- رسمة مبانٍ هندسية --}}
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

                {{-- محتوى القسم --}}
                <div
                    class="relative z-10 flex flex-col justify-between w-full p-10"
                >

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
                                    <path
                                        d="M4 21V9l8-6 8 6v12"
                                    />

                                    <path
                                        d="M8 21v-8h8v8"
                                    />

                                    <path
                                        d="M12 3v6"
                                    />
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

                    <div class="max-w-md">

                        <span
                            class="inline-flex px-4 py-2 text-xs font-bold border rounded-full border-cyan-400/20 bg-cyan-400/10 text-cyan-200"
                        >
                            حماية وأمان
                        </span>

                        <h1
                            class="mt-6 text-4xl font-black leading-tight text-white"
                        >
                            بياناتك محفوظة
                            <span
                                class="block mt-2 text-transparent bg-gradient-to-l from-cyan-300 to-blue-400 bg-clip-text"
                            >
                                بأعلى معايير الأمان
                            </span>
                        </h1>

                        <p
                            class="mt-5 text-base leading-8 text-slate-300"
                        >
                            نطلب تأكيد كلمة المرور لحماية حسابك قبل الوصول
                            إلى هذه المنطقة الحساسة داخل النظام.
                        </p>

                    </div>

                    <div class="grid grid-cols-3 gap-3">

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >
                            <div class="text-xl">
                                🔐
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                تسجيل آمن
                            </p>
                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >
                            <div class="text-xl">
                                🛡️
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                حماية البيانات
                            </p>
                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >
                            <div class="text-xl">
                                ⚡
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                وصول سريع
                            </p>
                        </div>

                    </div>

                </div>

            </section>

            {{-- نموذج تأكيد كلمة المرور --}}
            <section
                class="flex items-center min-h-[680px] p-6 sm:p-10 lg:p-14 bg-slate-900/95"
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

                                <path
                                    d="M8 10V7a4 4 0 0 1 8 0v3"
                                />
                            </svg>
                        </div>

                        <h1 class="mt-6 text-3xl font-black text-white">
                            تأكيد كلمة المرور
                        </h1>

                        <p class="mt-3 text-sm leading-7 text-slate-400">
                            هذه منطقة محمية. أدخل كلمة المرور الحالية للمتابعة.
                        </p>

                    </div>

                    @if ($errors->any())

                        <div
                            class="p-4 mt-6 text-sm text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                        >

                            <div class="flex items-start gap-3">

                                <span class="text-lg">
                                    ⚠️
                                </span>

                                <ul class="space-y-1">

                                    @foreach ($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    @endif

                    <form
                        method="POST"
                        action="{{ route('password.confirm') }}"
                        class="mt-8 space-y-6"
                    >
                        @csrf

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
                                    type="password"
                                    name="password"
                                    required
                                    autofocus
                                    autocomplete="current-password"
                                    placeholder="أدخل كلمة المرور"
                                    class="w-full py-4 pl-20 pr-12 transition border outline-none rounded-2xl border-white/10 bg-slate-950/70 text-slate-100 placeholder:text-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                                >

                                <button
                                    type="button"
                                    id="toggle_confirm_password"
                                    data-password-toggle="password"
                                    aria-controls="password"
                                    aria-pressed="false"
                                    class="absolute text-xs font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300 hover:text-cyan-200"
                                >
                                    إظهار
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
                            class="flex items-center justify-center w-full gap-3 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-blue-600 to-cyan-500 shadow-blue-500/20 hover:-translate-y-0.5 hover:shadow-blue-500/30"
                        >
                            <span>
                                تأكيد والمتابعة
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

                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-cyan-300"
                        >
                            <span>
                                ←
                            </span>

                            العودة إلى لوحة التحكم
                        </a>

                    </div>

                </div>

            </section>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.querySelector(
                '[data-password-toggle="password"]'
            );
            const passwordInput = document.getElementById('password');

            if (!toggleButton || !passwordInput) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';
                toggleButton.textContent = isHidden ? 'إخفاء' : 'إظهار';
                toggleButton.setAttribute(
                    'aria-pressed',
                    isHidden ? 'true' : 'false'
                );
            });
        });
    </script>

</x-guest-layout>
