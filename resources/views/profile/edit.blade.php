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

                    {{-- الصورة --}}
                    <div
                        class="p-5 border rounded-3xl border-white/10 bg-white/[0.02]"
                    >
                        <label class="block mb-4 text-lg font-bold text-white">
                            الصورة الشخصية
                        </label>

                        <div class="flex flex-col gap-5 md:flex-row md:items-center">

                            <div
                                class="flex items-center justify-center overflow-hidden border w-28 h-28 rounded-3xl border-cyan-400/20 bg-slate-900/70"
                            >
                                <template x-if="photoPreview">
                                    <img
                                        :src="photoPreview"
                                        alt="معاينة الصورة"
                                        class="object-cover w-full h-full"
                                    >
                                </template>

                                <template x-if="!photoPreview">
                                    <div class="w-full h-full">
                                        @if ($user->profile_photo)
                                            <img
                                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                                alt="{{ $user->name }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            <img
                                                src="{{ asset('images/Mainlogo.png') }}"
                                                alt="{{ $user->name }}"
                                                class="object-contain w-full h-full p-3"
                                            >
                                        @endif
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1">
                                <label
                                    class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold text-white transition cursor-pointer bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                                >
                                    <span>📁</span>
                                    <span>اختيار صورة</span>

                                    <input
                                        type="file"
                                        name="profile_photo"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="hidden"
                                        @change="
                                            const file = $event.target.files[0];

                                            if (file) {
                                                photoPreview =
                                                    URL.createObjectURL(file);
                                            } else {
                                                photoPreview = null;
                                            }
                                        "
                                    >
                                </label>

                                <p class="mt-3 text-sm leading-7 text-slate-400">
                                    JPG أو PNG أو WEBP، وبحجم أقصى 2MB.
                                </p>
                            </div>

                        </div>
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

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                        >

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

</x-app-layout>
