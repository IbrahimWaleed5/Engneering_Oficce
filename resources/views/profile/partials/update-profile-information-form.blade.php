<section
    x-data="{
        photoPreview: null
    }"
>

    <form
        id="send-verification"
        method="POST"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form
        method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="space-y-5"
    >
        @csrf
        @method('PATCH')

        <div>

            <label
                for="profile_photo"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                الصورة الشخصية
            </label>

            <div
                class="flex flex-col items-center gap-5 p-5 border rounded-2xl border-slate-700 bg-slate-900/50 sm:flex-row"
            >

                <div class="shrink-0">

                    <template x-if="photoPreview">

                        <img
                            :src="photoPreview"
                            alt="معاينة الصورة الشخصية"
                            class="object-cover w-24 h-24 border-4 rounded-full border-cyan-500/30"
                        >

                    </template>

                    <template x-if="!photoPreview">

                        <div>

                            @if($user->profile_photo)

                                <img
                                    src="{{ asset('storage/' . $user->profile_photo) }}"
                                    alt="{{ $user->name }}"
                                    class="object-cover w-24 h-24 border-4 rounded-full border-cyan-500/30"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-24 h-24 text-3xl font-black text-white border-4 rounded-full border-slate-700 bg-gradient-to-br from-blue-600 to-cyan-500"
                                >
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>

                            @endif

                        </div>

                    </template>

                </div>

                <div class="w-full">

                    <input
                        id="profile_photo"
                        name="profile_photo"
                        type="file"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full text-sm text-slate-300 file:ml-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-bold file:text-white hover:file:bg-blue-700"
                        @change="
                            const file = $event.target.files[0];

                            if (file) {
                                const reader = new FileReader();

                                reader.onload = event => {
                                    photoPreview = event.target.result;
                                };

                                reader.readAsDataURL(file);
                            } else {
                                photoPreview = null;
                            }
                        "
                    >

                    <p class="mt-2 text-xs leading-6 text-slate-500">
                        اترك الحقل فارغًا إذا لم ترغب بتغيير الصورة.
                        الصيغ المسموحة: JPG، JPEG، PNG، WEBP — الحد الأقصى 2MB.
                    </p>

                    @error('profile_photo')
                        <p class="mt-2 text-sm text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

        <div>
            <label
                for="name"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                الاسم الكامل
            </label>

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    👤
                </span>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="أدخل الاسم الكامل"
                    class="pr-12 form-control"
                >
            </div>

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

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    ✉️
                </span>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                    placeholder="example@email.com"
                    class="pr-12 form-control"
                >
            </div>

            @error('email')
                <p class="mt-2 text-sm text-red-300">
                    {{ $message }}
                </p>
            @enderror

            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                && ! $user->hasVerifiedEmail()
            )
                <div
                    class="p-4 mt-4 text-sm border rounded-2xl border-yellow-500/20 bg-yellow-500/10"
                >
                    <p class="text-yellow-100">
                        بريدك الإلكتروني غير مؤكد حتى الآن.
                    </p>

                    <button
                        type="submit"
                        form="send-verification"
                        class="mt-2 font-bold text-yellow-300 hover:text-yellow-200"
                    >
                        إعادة إرسال رابط التأكيد
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div
                        class="p-4 mt-3 text-sm text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                    >
                        تم إرسال رابط تأكيد جديد.
                    </div>
                @endif
            @endif
        </div>

        <div>
            <label
                for="phone"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                رقم الهاتف
            </label>

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    📱
                </span>

                <input
                    id="phone"
                    name="phone"
                    type="text"
                    value="{{ old('phone', $user->phone) }}"
                    autocomplete="tel"
                    placeholder="0590000000"
                    class="pr-12 form-control"
                >
            </div>

            @error('phone')
                <p class="mt-2 text-sm text-red-300">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-2">
            <button
                type="submit"
                class="primary-button"
            >
                حفظ التعديلات
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-bold text-green-300"
                >
                    تم الحفظ بنجاح
                </p>
            @endif
        </div>
    </form>

</section>
