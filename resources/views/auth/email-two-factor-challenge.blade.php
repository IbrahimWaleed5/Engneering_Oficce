<x-guest-layout>
    <div dir="rtl" class="max-w-md mx-auto">
        <h1 class="mb-3 text-2xl font-black text-white">
            التحقق بخطوتين
        </h1>

        <p class="mb-6 text-sm leading-7 text-slate-400">
            أرسلنا رمزًا من 6 أرقام إلى بريدك الإلكتروني.
        </p>

        @if (session('success'))
            <div class="p-3 mb-4 text-green-200 border border-green-700 rounded-xl bg-green-900/30">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('email-2fa.verify') }}">
            @csrf

            <input
                type="text"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                pattern="[0-9]{6}"
                required
                autofocus
                class="w-full text-center tracking-[.5em] rounded-xl"
            >

            @error('code')
                <p class="mt-2 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror

            <button
                type="submit"
                class="w-full px-5 py-3 mt-5 font-black text-white bg-blue-600 rounded-xl"
            >
                تأكيد الدخول
            </button>
        </form>

        <form method="POST" action="{{ route('email-2fa.resend') }}" class="mt-3">
            @csrf

            <button
                type="submit"
                class="w-full px-5 py-3 font-bold border rounded-xl border-slate-700 text-slate-300"
            >
                إعادة إرسال الرمز
            </button>
        </form>
    </div>
</x-guest-layout>
