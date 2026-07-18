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
                    ✉️
                </div>

                <h1 class="mt-5 text-3xl font-black text-white">
                    تأكيد البريد الإلكتروني
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    شكرًا لتسجيلك. قبل البدء باستخدام النظام، يرجى تأكيد
                    بريدك الإلكتروني عبر الرابط الذي أرسلناه إليك.
                </p>

                <p class="mt-2 text-sm leading-7 text-slate-500">
                    إذا لم يصلك البريد الإلكتروني، يمكنك طلب إرسال رابط جديد.
                </p>

            </div>

            @if (session('status') == 'verification-link-sent')

                <div
                    class="p-4 mb-6 text-sm text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    ✅ تم إرسال رابط تأكيد جديد إلى بريدك الإلكتروني.
                </div>

            @endif

            <div class="space-y-4">

                <form
                    method="POST"
                    action="{{ route('verification.send') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full primary-button"
                    >
                        إعادة إرسال رابط التأكيد
                    </button>

                </form>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full px-4 py-3 font-semibold text-red-300 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                    >
                        تسجيل الخروج
                    </button>

                </form>

            </div>

            <div class="pt-6 mt-6 text-center border-t border-white/10">

                <a
                    href="{{ url('/') }}"
                    class="text-sm text-slate-400 hover:text-slate-200"
                >
                    العودة إلى الصفحة الرئيسية
                </a>

            </div>

        </div>

    </div>

</x-guest-layout>
