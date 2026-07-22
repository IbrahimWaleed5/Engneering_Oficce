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
                class="relative hidden min-h-[700px] overflow-hidden lg:flex"
            >

                {{-- الخلفية --}}
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
                            خطوة أخيرة
                        </span>

                        <h1
                            class="mt-6 text-4xl font-black leading-tight text-white"
                        >
                            أكد بريدك الإلكتروني

                            <span
                                class="block mt-2 text-transparent bg-gradient-to-l from-cyan-300 to-blue-400 bg-clip-text"
                            >
                                وابدأ استخدام المنصة
                            </span>
                        </h1>

                        <p
                            class="mt-5 text-base leading-8 text-slate-300"
                        >
                            تأكيد البريد الإلكتروني يساعدنا على حماية حسابك
                            وضمان وصول الإشعارات وروابط استعادة كلمة المرور
                            إليك بشكل صحيح.
                        </p>

                    </div>

                    {{-- المميزات --}}
                    <div class="grid grid-cols-3 gap-3">

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                ✉️
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                بريد موثّق
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                🛡️
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                حساب محمي
                            </p>

                        </div>

                        <div
                            class="p-4 text-center border rounded-2xl border-white/10 bg-white/5 backdrop-blur"
                        >

                            <div class="text-xl">
                                🔔
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                إشعارات مضمونة
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            {{-- قسم تأكيد البريد --}}
            <section
                class="flex items-center min-h-[700px] p-6 sm:p-10 lg:p-14 bg-slate-900/95"
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

                    {{-- الأيقونة والعنوان --}}
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
                                    x="3"
                                    y="5"
                                    width="18"
                                    height="14"
                                    rx="2"
                                />

                                <path d="m3 7 9 6 9-6" />

                                <path d="m15.5 16 1.5 1.5 3-3" />
                            </svg>

                        </div>

                        <h1 class="mt-6 text-3xl font-black text-white">
                            تأكيد البريد الإلكتروني
                        </h1>

                        <p class="mt-3 text-sm leading-7 text-slate-400">
                            أرسلنا رابط تأكيد إلى بريدك الإلكتروني. افتح
                            الرسالة واضغط على الرابط لتفعيل حسابك.
                        </p>

                    </div>

                    {{-- البريد الحالي --}}
                    @auth

                        <div
                            class="flex items-center gap-3 p-4 mt-6 border rounded-2xl border-white/10 bg-slate-950/60"
                        >

                            <div
                                class="flex items-center justify-center flex-none w-10 h-10 rounded-xl bg-blue-500/10 text-cyan-300"
                            >
                                ✉️
                            </div>

                            <div class="min-w-0">

                                <p class="text-xs text-slate-500">
                                    تم إرسال الرابط إلى
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-white truncate"
                                    dir="ltr"
                                >
                                    {{ auth()->user()->email }}
                                </p>

                            </div>

                        </div>

                    @endauth

                    {{-- رسالة نجاح إعادة الإرسال --}}
                    @if (
                        session('status')
                        === 'verification-link-sent'
                    )

                        <div
                            class="p-4 mt-6 text-sm border rounded-2xl border-emerald-500/20 bg-emerald-500/10 text-emerald-200"
                        >

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex items-center justify-center flex-none w-10 h-10 font-bold rounded-xl bg-emerald-500/15"
                                >
                                    ✓
                                </div>

                                <div>

                                    <p class="font-bold">
                                        تم إرسال الرابط بنجاح
                                    </p>

                                    <p class="mt-1 leading-6">
                                        تم إرسال رابط تأكيد جديد إلى بريدك
                                        الإلكتروني.
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                    {{-- تعليمات --}}
                    <div
                        class="p-5 mt-6 border rounded-3xl border-white/10 bg-slate-950/50"
                    >

                        <h3 class="font-bold text-white">
                            لم تجد رسالة التأكيد؟
                        </h3>

                        <div class="mt-4 space-y-3 text-sm text-slate-400">

                            <p class="flex items-start gap-3">
                                <span class="text-cyan-300">
                                    01
                                </span>

                                تحقق من مجلد الرسائل غير المرغوب فيها.
                            </p>

                            <p class="flex items-start gap-3">
                                <span class="text-cyan-300">
                                    02
                                </span>

                                تأكد أن البريد المسجل صحيح.
                            </p>

                            <p class="flex items-start gap-3">
                                <span class="text-cyan-300">
                                    03
                                </span>

                                اضغط على زر إعادة الإرسال في الأسفل.
                            </p>

                        </div>

                    </div>

                    <div class="mt-8 space-y-4">

                        {{-- إعادة إرسال الرابط --}}
                        <form
                            method="POST"
                            action="{{ route('verification.send') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex items-center justify-center w-full gap-3 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-blue-600 to-cyan-500 shadow-blue-500/20 hover:-translate-y-0.5 hover:shadow-blue-500/30"
                            >

                                <svg
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M20 11a8.1 8.1 0 1 0 .5 4"
                                    />

                                    <path d="M20 4v7h-7" />
                                </svg>

                                إعادة إرسال رابط التأكيد

                            </button>

                        </form>

                        {{-- تسجيل الخروج --}}
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex items-center justify-center w-full gap-3 px-6 py-4 font-bold text-red-300 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                            >

                                <svg
                                    class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                    />

                                    <path d="M16 17l5-5-5-5" />

                                    <path d="M21 12H9" />
                                </svg>

                                تسجيل الخروج

                            </button>

                        </form>

                    </div>

                    <div
                        class="pt-6 mt-8 text-center border-t border-white/10"
                    >

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
