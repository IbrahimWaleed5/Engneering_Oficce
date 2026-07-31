<x-app-layout>

    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
        x-data="{ photoPreview: null }"
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

            <button
                id="choose_profile_photo"
                type="button"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 mt-4 text-sm font-black transition border rounded-2xl border-cyan-400/20 bg-cyan-500/10 text-cyan-200 hover:bg-cyan-500/20"
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
            </button>

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
</x-app-layout>
