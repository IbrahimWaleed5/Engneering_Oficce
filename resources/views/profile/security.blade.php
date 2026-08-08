<x-app-layout>
    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
    >
        <div class="max-w-5xl px-4 py-10 mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-white/5 border-white/10"
                >
                    <span>⚙️</span>
                    <span class="text-sm font-bold text-slate-300">
                        إعدادات الحساب
                    </span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    الأمان وتسجيل الدخول
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    فعّل التحقق بخطوتين وأضف مفاتيح مرور لحماية حسابك.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            @if (session('security-success'))
                <div
                    class="p-4 mb-6 font-bold text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    {{ session('security-success') }}
                </div>
            @endif

            @if (session('error'))
                <div
                    class="p-4 mb-6 font-bold text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    {{ session('error') }}
                </div>
            @endif

            <section
                class="overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-white/10">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h2 class="text-2xl font-black text-white">
                                🛡️ حماية الحساب
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                تحكم بوسائل التحقق والدخول الآمن إلى حسابك.
                            </p>
                        </div>

                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-black border rounded-full
                            {{ $user->email_two_factor_enabled
                                ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-200'
                                : 'border-amber-500/20 bg-amber-500/10 text-amber-200' }}"
                        >
                            {{ $user->email_two_factor_enabled ? '● مفعّل' : '○ غير مفعّل' }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-6 sm:p-8">

                    {{-- Email 2FA --}}
                    <div
                        class="p-5 border sm:p-6 rounded-3xl border-white/10 bg-slate-900/50"
                    >
                        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-cyan-400/20 bg-cyan-500/10"
                                    >
                                        ✉️
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-white">
                                            التحقق بخطوتين عبر البريد
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Email Two-Factor Authentication
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 text-sm leading-7 text-slate-400">
                                    بعد إدخال كلمة المرور بشكل صحيح، يرسل النظام رمزًا من
                                    6 أرقام إلى بريدك الإلكتروني قبل إكمال تسجيل الدخول.
                                </p>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        6 أرقام
                                    </span>

                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        صالح 10 دقائق
                                    </span>

                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        حتى 5 محاولات
                                    </span>
                                </div>

                                <div class="p-4 mt-5 text-sm border rounded-2xl border-white/10 bg-slate-950/40 text-slate-300">
                                    البريد المستخدم للتحقق:
                                    <strong class="text-white">
                                        {{ $user->email }}
                                    </strong>
                                </div>
                            </div>

                            <div>
                                @if (! $user->email_two_factor_enabled)
                                    @if (session('email_2fa_setup_pending'))
                                        <div
                                            class="p-4 mb-4 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10"
                                        >
                                            تم إرسال رمز التفعيل إلى بريدك.
                                        </div>

                                        <form
                                            method="POST"
                                            action="{{ route('profile.email-2fa.confirm') }}"
                                            class="space-y-4"
                                        >
                                            @csrf

                                            <div>
                                                <label
                                                    for="email_2fa_code"
                                                    class="block mb-2 text-sm font-bold text-slate-200"
                                                >
                                                    رمز التحقق
                                                </label>

                                                <input
                                                    id="email_2fa_code"
                                                    type="text"
                                                    name="code"
                                                    maxlength="6"
                                                    inputmode="numeric"
                                                    autocomplete="one-time-code"
                                                    pattern="[0-9]{6}"
                                                    placeholder="000000"
                                                    required
                                                    autofocus
                                                    class="w-full px-5 py-4 text-xl font-black tracking-[0.35em] text-center text-white border rounded-2xl border-white/10 bg-slate-950/70 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                                                >

                                                @error('code', 'emailTwoFactor')
                                                    <p class="mt-2 text-sm font-bold text-red-300">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <button
                                                type="submit"
                                                class="w-full px-5 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-emerald-500 to-cyan-600 hover:scale-[1.01]"
                                            >
                                                ✅ تأكيد وتفعيل الميزة
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('profile.email-2fa.resend') }}"
                                            class="mt-3"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="w-full px-5 py-3 text-sm font-bold transition border rounded-2xl border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
                                            >
                                                إعادة إرسال الرمز
                                            </button>
                                        </form>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('profile.email-2fa.enable') }}"
                                            class="space-y-4"
                                        >
                                            @csrf

                                            <div>
                                                <label
                                                    for="email_2fa_current_password"
                                                    class="block mb-2 text-sm font-bold text-slate-200"
                                                >
                                                    كلمة المرور الحالية
                                                </label>

                                                <input
                                                    id="email_2fa_current_password"
                                                    type="password"
                                                    name="current_password"
                                                    autocomplete="current-password"
                                                    required
                                                    class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-950/70 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                                                >

                                                @error('current_password', 'emailTwoFactor')
                                                    <p class="mt-2 text-sm font-bold text-red-300">
                                                        {{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <button
                                                type="submit"
                                                class="w-full px-5 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-cyan-500 to-blue-600 hover:scale-[1.01]"
                                            >
                                                تفعيل التحقق بخطوتين
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <div
                                        class="p-4 mb-4 font-bold border text-emerald-200 rounded-2xl border-emerald-500/20 bg-emerald-500/10"
                                    >
                                        ✅ التحقق بخطوتين عبر البريد مفعّل.
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('profile.email-2fa.disable') }}"
                                        class="space-y-4"
                                        onsubmit="return confirm('هل أنت متأكد من تعطيل التحقق بخطوتين؟');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <div>
                                            <label
                                                for="email_2fa_disable_password"
                                                class="block mb-2 text-sm font-bold text-slate-200"
                                            >
                                                كلمة المرور الحالية
                                            </label>

                                            <input
                                                id="email_2fa_disable_password"
                                                type="password"
                                                name="current_password"
                                                autocomplete="current-password"
                                                required
                                                class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-950/70 focus:border-red-400/40 focus:ring-2 focus:ring-red-500/20"
                                            >

                                            @error('current_password', 'emailTwoFactorDisable')
                                                <p class="mt-2 text-sm font-bold text-red-300">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <button
                                            type="submit"
                                            class="w-full px-5 py-4 font-black text-red-200 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                                        >
                                            تعطيل التحقق بخطوتين
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($user->email_two_factor_enabled)
                    {{-- Passkeys --}}
                    <div
                        class="p-5 border sm:p-6 rounded-3xl border-white/10 bg-slate-900/50"
                    >
                        <div class="flex flex-col justify-between gap-5 md:flex-row md:items-center">
                            <div class="max-w-2xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-violet-400/20 bg-violet-500/10"
                                    >
                                        👆
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-white">
                                            مفاتيح المرور Passkeys
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-400">
                                            بصمة الهاتف • Face ID • Windows Hello • PIN
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 text-sm leading-7 text-slate-400">
                                    يتم التحقق من البصمة أو الوجه داخل جهازك؛ الموقع لا يستلم
                                    بيانات البصمة أو صورة الوجه.
                                </p>
                            </div>

                            <button
                                type="button"
                                id="register_passkey_button"
                                class="inline-flex items-center justify-center gap-2 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-violet-600 to-blue-600 hover:scale-[1.02]"
                            >
                                <span>＋</span>
                                <span>إضافة Passkey</span>
                            </button>
                        </div>

                        <div
                            id="passkey_message"
                            class="hidden p-4 mt-5 text-sm font-bold border rounded-2xl"
                        ></div>
                    </div>


                    @endif

                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('register_passkey_button');
            const message = document.getElementById('passkey_message');

            function showMessage(text, success = false) {
                message.className =
                    'p-4 mt-5 text-sm font-bold border rounded-2xl '
                    + (
                        success
                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-200'
                            : 'border-red-500/20 bg-red-500/10 text-red-200'
                    );

                message.textContent = text;
            }

            button?.addEventListener('click', async function () {
                if (! window.PublicKeyCredential) {
                    showMessage(
                        'هذا المتصفح أو الجهاز لا يدعم Passkeys.'
                    );
                    return;
                }

                if (! window.AlwaleedPasskeys?.register) {
                    showMessage(
                        'لم يتم تحميل عميل Passkeys. تأكد من إضافة import الخاص به داخل resources/js/app.js ثم شغّل npm run build.'
                    );
                    return;
                }

                function getAutomaticDeviceName() {
                    const ua = navigator.userAgent || '';
                    const platform =
                        navigator.userAgentData?.platform
                        || navigator.platform
                        || '';

                    if (/Android/i.test(ua)) {
                        const modelMatch = ua.match(
                            /Android[^;]*;\s*([^;)]+?)(?:\s+Build\/[^;)]+)?[;) ]/i
                        );

                        const model = modelMatch?.[1]
                            ?.replace(/\s+Build\/.*$/i, '')
                            ?.trim();

                        if (
                            model
                            && ! /^(wv|Mobile|Android)$/i.test(model)
                        ) {
                            return `Android - ${model}`;
                        }

                        return 'Android';
                    }

                    if (/iPad/i.test(ua)) {
                        return 'iPad';
                    }

                    if (
                        /iPhone|iPod/i.test(ua)
                        || (
                            /Mac/i.test(platform)
                            && navigator.maxTouchPoints > 1
                        )
                    ) {
                        return 'iPhone';
                    }

                    if (
                        /Windows/i.test(platform)
                        || /Windows/i.test(ua)
                    ) {
                        return 'Windows';
                    }

                    if (
                        /Mac/i.test(platform)
                        || /Macintosh/i.test(ua)
                    ) {
                        return 'macOS';
                    }

                    if (
                        /Linux/i.test(platform)
                        || /Linux/i.test(ua)
                    ) {
                        return 'Linux';
                    }

                    return 'جهازي';
                }

                const deviceName =
                    getAutomaticDeviceName();

                button.disabled = true;
                button.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );

                try {
                    await window.AlwaleedPasskeys.register({
                        name: deviceName,
                    });

                    showMessage(
                        '✅ تم تسجيل Passkey بنجاح.',
                        true
                    );
                } catch (error) {
                    console.error(error);

                    showMessage(
                        error?.message
                        || 'تعذر تسجيل Passkey. تأكد من استخدام HTTPS ومن دعم الجهاز.'
                    );
                } finally {
                    button.disabled = false;
                    button.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );
                }
            });
        });
    </script>
</x-app-layout>
