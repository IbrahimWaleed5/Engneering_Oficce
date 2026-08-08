<x-app-layout>

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css"
>

<style>
    #profile_crop_modal .cropper-view-box,
    #profile_crop_modal .cropper-face {
        border-radius: 50%;
    }

    #profile_crop_modal .cropper-view-box {
        outline: 2px solid rgba(34, 211, 238, 0.9);
    }

    #profile_crop_modal .cropper-line,
    #profile_crop_modal .cropper-point {
        background-color: #22d3ee;
    }

    #profile_crop_modal .cropper-modal {
        background-color: #020617;
        opacity: 0.75;
    }
</style>

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
                    البيانات الشخصية
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    عدّل الاسم والبريد الإلكتروني ورقم الهاتف والصورة الشخصية.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            @if (session('status') === 'profile-updated')
                <div
                    class="p-4 mb-6 font-bold text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    تم حفظ البيانات الشخصية بنجاح.
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <ul class="space-y-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section
                class="overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-white/10">
                    <h2 class="text-2xl font-black text-white">
                        معلومات الحساب
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        تأكد من أن بيانات الاتصال الخاصة بك صحيحة.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    enctype="multipart/form-data"
                    class="p-6 space-y-6 sm:p-8"
                >
                    @csrf
                    @method('PATCH')

                    {{-- الصورة الشخصية --}}
<div class="space-y-4">

    <label class="block text-sm font-black text-white">
        الصورة الشخصية
    </label>

    <div
        class="flex flex-col items-center gap-5 p-6 border sm:flex-row rounded-3xl border-white/10 bg-slate-950/50"
    >
        <div class="relative flex-none">

            <img
                id="profile_photo_preview"
                src="{{ auth()->user()->profile_photo
                    ? asset('storage/' . auth()->user()->profile_photo)
                    : asset('images/default-avatar.png') }}"
                alt="الصورة الشخصية"
                class="object-cover w-32 h-32 border-4 rounded-full shadow-xl border-cyan-400/20 bg-slate-800"
            >

            <div
                id="profile_photo_icon"
                class="absolute inset-0 items-center justify-center hidden rounded-full bg-slate-900"
            >
                👤
            </div>

        </div>

        <div class="flex-1 text-center sm:text-right">

            <h3 class="font-black text-white">
                اختر صورة مناسبة لحسابك
            </h3>

            <p
                id="profile_photo_name"
                class="mt-2 text-sm leading-7 text-slate-400"
            >
                يمكنك تحريك الصورة وتكبيرها واختيار الجزء الذي تريد ظهوره.
            </p>

            <label
                for="profile_photo"
                id="choose_profile_photo"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 mt-4 text-sm font-black transition border cursor-pointer rounded-2xl border-cyan-400/20 bg-cyan-500/10 text-cyan-200 hover:bg-cyan-500/20"
            >
                <svg
                    class="w-5 h-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 20h9" />
                    <path
                        d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"
                    />
                </svg>

                تعديل الصورة
            </label>

            <input
                id="profile_photo"
                name="profile_photo"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="hidden"
            >

        </div>
    </div>

    @error('profile_photo')
        <p class="text-sm font-bold text-red-300">
            {{ $message }}
        </p>
    @enderror

</div>

                    <div class="grid gap-6 md:grid-cols-2">

                        <div>
                            <label
                                for="name"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                الاسم الكامل
                            </label>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            @error('name')
                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                البريد الإلكتروني
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            @error('email')
                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div>
                        <label
                            for="phone"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            رقم الهاتف
                        </label>

                        <div class="premium-phone-field">
                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                dir="ltr"
                                autocomplete="tel"
                                inputmode="tel"
                                class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            <input
                                id="country_code"
                                type="hidden"
                                name="country_code"
                                value="{{ old('country_code', $user->country_code ?? 'PS') }}"
                            >

                            <input
                                id="dial_code"
                                type="hidden"
                                name="dial_code"
                                value="{{ old('dial_code', $user->dial_code ?? '+970') }}"
                            >
                        </div>

                        @error('phone')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-7 py-4 font-bold text-white transition shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                        >
                            <span>💾</span>
                            <span>حفظ التعديلات</span>
                        </button>
                    </div>

                </form>

            </section>

            {{-- ============================================================
                الأمان وتسجيل الدخول
            ============================================================ --}}
            <section
                id="security"
                class="mt-8 overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-white/10">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h2 class="text-2xl font-black text-white">
                                🔐 الأمان وتسجيل الدخول
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                عزّز حماية حسابك باستخدام التحقق بخطوتين ومفاتيح المرور.
                            </p>
                        </div>

                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-black border rounded-full
                            {{ $user->email_two_factor_enabled
                                ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-200'
                                : 'border-amber-500/20 bg-amber-500/10 text-amber-200' }}"
                        >
                            <span>
                                {{ $user->email_two_factor_enabled ? '●' : '○' }}
                            </span>

                            <span>
                                التحقق بالبريد:
                                {{ $user->email_two_factor_enabled ? 'مفعّل' : 'غير مفعّل' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6 sm:p-8">

                    {{-- رسائل إعدادات الأمان --}}
                    @if (session('security-success'))
                        <div
                            class="p-4 font-bold text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                        >
                            {{ session('security-success') }}
                        </div>
                    @endif

                    @if (session('email_2fa_setup_pending'))
                        <div
                            class="p-4 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10"
                        >
                            تم إرسال رمز من 6 أرقام إلى
                            <strong>{{ $user->email }}</strong>.
                            أدخل الرمز أدناه لإكمال التفعيل.
                        </div>
                    @endif

                    {{-- Email 2FA --}}
                    <div
                        class="p-5 border sm:p-6 rounded-3xl border-white/10 bg-slate-900/50"
                    >
                        <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">
                            <div class="max-w-2xl">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 text-xl border rounded-2xl border-cyan-400/20 bg-cyan-500/10"
                                    >
                                        ✉️
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-black text-white">
                                            التحقق بخطوتين عبر البريد الإلكتروني
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Email Two-Factor Authentication
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 text-sm leading-7 text-slate-400">
                                    عند تسجيل الدخول بكلمة المرور، سنرسل رمز تحقق من
                                    6 أرقام إلى بريدك الإلكتروني قبل السماح بالدخول للحساب.
                                </p>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        رمز من 6 أرقام
                                    </span>

                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        صلاحية 10 دقائق
                                    </span>

                                    <span class="px-3 py-1 text-xs font-bold border rounded-full border-white/10 bg-white/5 text-slate-300">
                                        حماية إضافية للحساب
                                    </span>
                                </div>
                            </div>

                            <div class="w-full lg:max-w-sm">
                                @if (! $user->email_two_factor_enabled)
                                    @if (session('email_2fa_setup_pending'))
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
                                        ✅ التحقق بخطوتين عبر البريد مفعّل على حسابك.
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
                                                كلمة المرور الحالية للتأكيد
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
                                    يمكنك استخدام حماية جهازك لتسجيل الدخول.
                                    بصمتك أو صورة وجهك لا يتم إرسالها أو تخزينها في الموقع.
                                </p>
                            </div>

                            <div class="flex-none">
                                <button
                                    type="button"
                                    id="register_passkey_button"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-violet-600 to-blue-600 hover:scale-[1.02]"
                                >
                                    <span>＋</span>
                                    <span>إضافة Passkey</span>
                                </button>
                            </div>
                        </div>

                        <div
                            id="passkey_message"
                            class="hidden p-4 mt-5 text-sm font-bold border rounded-2xl"
                        ></div>
                    </div>

                </div>

        </div>
    </div>
{{-- نافذة التحكم وقص الصورة --}}
<div
    id="profile_crop_modal"
    class="fixed inset-0 z-[999999] hidden overflow-y-auto bg-slate-950/90 p-4 backdrop-blur-sm"
>
    <div
        class="flex items-center justify-center min-h-full py-6"
    >
        <div
            class="w-full max-w-3xl overflow-hidden border shadow-2xl rounded-3xl border-white/10 bg-slate-900"
            dir="rtl"
        >
            {{-- رأس النافذة --}}
            <div
                class="flex items-center justify-between gap-4 px-6 py-5 border-b border-white/10"
            >
                <div>
                    <h2 class="text-xl font-black text-white">
                        تعديل الصورة الشخصية
                    </h2>

                    <p class="mt-1 text-sm text-slate-400">
                        حرّك الصورة واختر الجزء الذي تريد ظهوره.
                    </p>
                </div>

                <button
                    type="button"
                    data-cancel-profile-crop
                    class="flex items-center justify-center flex-none w-10 h-10 transition rounded-xl bg-white/5 text-slate-400 hover:bg-red-500/10 hover:text-red-300"
                    aria-label="إغلاق"
                >
                    ✕
                </button>
            </div>

            {{-- مساحة الصورة --}}
            <div class="p-4 sm:p-6">

                <div
                    class="flex items-center justify-center w-full overflow-hidden rounded-2xl bg-slate-950"
                    style="height: min(55vh, 520px);"
                >
                    <img
                        id="profile_crop_image"
                        src=""
                        alt="قص الصورة"
                        class="block max-w-full"
                    >
                </div>

                {{-- أدوات التحكم --}}
                <div
                    class="grid grid-cols-5 gap-2 mt-5"
                    dir="ltr"
                >
                    <button
                        id="crop_zoom_in"
                        type="button"
                        class="px-3 py-3 font-bold transition border rounded-xl border-white/10 bg-white/5 text-slate-200 hover:bg-white/10"
                        title="تكبير"
                    >
                        ＋
                    </button>

                    <button
                        id="crop_zoom_out"
                        type="button"
                        class="px-3 py-3 font-bold transition border rounded-xl border-white/10 bg-white/5 text-slate-200 hover:bg-white/10"
                        title="تصغير"
                    >
                        −
                    </button>

                    <button
                        id="crop_rotate_left"
                        type="button"
                        class="px-3 py-3 font-bold transition border rounded-xl border-white/10 bg-white/5 text-slate-200 hover:bg-white/10"
                        title="تدوير لليسار"
                    >
                        ↶
                    </button>

                    <button
                        id="crop_rotate_right"
                        type="button"
                        class="px-3 py-3 font-bold transition border rounded-xl border-white/10 bg-white/5 text-slate-200 hover:bg-white/10"
                        title="تدوير لليمين"
                    >
                        ↷
                    </button>

                    <button
                        id="crop_reset"
                        type="button"
                        class="px-3 py-3 text-sm font-bold transition border rounded-xl border-white/10 bg-white/5 text-slate-200 hover:bg-white/10"
                    >
                        إعادة
                    </button>
                </div>
            </div>

            {{-- الأزرار --}}
            <div
                class="flex flex-col-reverse gap-3 px-6 py-5 border-t sm:flex-row border-white/10"
            >
                <button
                    type="button"
                    data-cancel-profile-crop
                    class="flex-1 px-6 py-4 font-bold transition border rounded-2xl border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
                >
                    إلغاء
                </button>

                <button
                    id="save_profile_crop"
                    type="button"
                    class="flex-1 px-6 py-4 font-black text-white transition shadow-xl rounded-2xl bg-gradient-to-l from-blue-600 to-cyan-500 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    حفظ الصورة
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile_photo');
    const preview = document.getElementById('profile_photo_preview');
    const modal = document.getElementById('profile_crop_modal');
    const cropImage = document.getElementById('profile_crop_image');
    const saveButton = document.getElementById('save_profile_crop');
    const cancelButtons = document.querySelectorAll('[data-cancel-profile-crop]');
    const zoomInButton = document.getElementById('crop_zoom_in');
    const zoomOutButton = document.getElementById('crop_zoom_out');
    const rotateLeftButton = document.getElementById('crop_rotate_left');
    const rotateRightButton = document.getElementById('crop_rotate_right');
    const resetButton = document.getElementById('crop_reset');
    const photoName = document.getElementById('profile_photo_name');

    if (!fileInput || !preview || !modal || !cropImage || !saveButton) {
        return;
    }

    let cropper = null;
    let sourceUrl = null;
    let previewUrl = null;

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function destroyCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        if (sourceUrl) {
            URL.revokeObjectURL(sourceUrl);
            sourceUrl = null;
        }

        cropImage.removeAttribute('src');
    }

    function cancelCropping() {
        fileInput.value = '';
        destroyCropper();
        closeModal();
    }

    fileInput.addEventListener('change', function () {
        const file = fileInput.files && fileInput.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('اختر صورة بصيغة JPG أو PNG أو WEBP.');
            fileInput.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('حجم الصورة يجب ألا يتجاوز 5MB.');
            fileInput.value = '';
            return;
        }

        if (typeof Cropper === 'undefined') {
            alert('تعذر تحميل أداة تعديل الصورة. حدّث الصفحة وحاول مرة أخرى.');
            fileInput.value = '';
            return;
        }

        destroyCropper();
        sourceUrl = URL.createObjectURL(file);
        cropImage.src = sourceUrl;
        openModal();

        cropImage.onload = function () {
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                background: false,
                movable: true,
                rotatable: true,
                scalable: true,
                zoomable: true,
                zoomOnTouch: true,
                zoomOnWheel: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                checkOrientation: true
            });
        };
    });

    zoomInButton?.addEventListener('click', function () {
        cropper?.zoom(0.1);
    });

    zoomOutButton?.addEventListener('click', function () {
        cropper?.zoom(-0.1);
    });

    rotateLeftButton?.addEventListener('click', function () {
        cropper?.rotate(-90);
    });

    rotateRightButton?.addEventListener('click', function () {
        cropper?.rotate(90);
    });

    resetButton?.addEventListener('click', function () {
        cropper?.reset();
    });

    cancelButtons.forEach(function (button) {
        button.addEventListener('click', cancelCropping);
    });

    saveButton.addEventListener('click', function () {
        if (!cropper) {
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = 'جاري تجهيز الصورة...';

        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 800,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            saveButton.disabled = false;
            saveButton.textContent = 'حفظ الصورة';
            alert('تعذر قص الصورة.');
            return;
        }

        canvas.toBlob(function (blob) {
            if (!blob) {
                saveButton.disabled = false;
                saveButton.textContent = 'حفظ الصورة';
                alert('تعذر تجهيز الصورة.');
                return;
            }

            const croppedFile = new File(
                [blob],
                'profile-' + Date.now() + '.jpg',
                {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                }
            );

            const transfer = new DataTransfer();
            transfer.items.add(croppedFile);
            fileInput.files = transfer.files;

            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            previewUrl = URL.createObjectURL(blob);
            preview.src = previewUrl;

            if (photoName) {
                photoName.textContent = 'تم تجهيز الصورة. اضغط حفظ التعديلات.';
            }

            destroyCropper();
            closeModal();

            saveButton.disabled = false;
            saveButton.textContent = 'حفظ الصورة';
        }, 'image/jpeg', 0.9);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            cancelCropping();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cancelCropping();
        }
    });
});
</script>


<script>
(function () {
    function removeStandaloneMyWorksButton() {
        document
            .querySelectorAll('a, button, [role="button"]')
            .forEach(function (element) {
                const label = element.textContent
                    .replace(/\s+/g, ' ')
                    .trim();

                const insideMainNavigation =
                    element.closest('#main-navigation');

                if (
                    label === 'أعمالي'
                    && ! insideMainNavigation
                ) {
                    element.remove();
                }
            });
    }

    removeStandaloneMyWorksButton();

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            removeStandaloneMyWorksButton
        );
    }

    window.addEventListener(
        'load',
        removeStandaloneMyWorksButton
    );

    const observer = new MutationObserver(
        removeStandaloneMyWorksButton
    );

    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });

    window.setTimeout(function () {
        removeStandaloneMyWorksButton();
        observer.disconnect();
    }, 5000);
})();
</script>


<script type="module">
    import { Passkeys } from '@laravel/passkeys';

    const button = document.getElementById('register_passkey_button');
    const message = document.getElementById('passkey_message');

    function showPasskeyMessage(text, success = false) {
        if (!message) {
            return;
        }

        message.classList.remove(
            'hidden',
            'border-red-500/20',
            'bg-red-500/10',
            'text-red-200',
            'border-emerald-500/20',
            'bg-emerald-500/10',
            'text-emerald-200'
        );

        message.classList.add(
            success ? 'border-emerald-500/20' : 'border-red-500/20',
            success ? 'bg-emerald-500/10' : 'bg-red-500/10',
            success ? 'text-emerald-200' : 'text-red-200'
        );

        message.textContent = text;
    }

    button?.addEventListener('click', async function () {
        if (!window.PublicKeyCredential) {
            showPasskeyMessage(
                'هذا المتصفح أو الجهاز لا يدعم Passkeys.'
            );
            return;
        }

        const defaultName =
            navigator.userAgentData?.platform
            || navigator.platform
            || 'جهازي';

        const name = window.prompt(
            'اكتب اسمًا لهذا الجهاز أو مفتاح المرور:',
            defaultName
        );

        if (!name) {
            return;
        }

        button.disabled = true;
        button.classList.add('opacity-60', 'cursor-not-allowed');

        try {
            await Passkeys.register({
                name: name.trim(),
            });

            showPasskeyMessage(
                '✅ تم تسجيل مفتاح المرور بنجاح. يمكنك استخدام بصمة الجهاز أو Face ID أو Windows Hello عند تسجيل الدخول.',
                true
            );
        } catch (error) {
            console.error(error);

            showPasskeyMessage(
                error?.message
                    || 'تعذر تسجيل Passkey. تأكد من استخدام HTTPS ومن دعم الجهاز للميزة.'
            );
        } finally {
            button.disabled = false;
            button.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    });
</script>

</x-app-layout>
