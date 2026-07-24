<x-app-layout>
    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
        x-data="{
            showCurrent: false,
            showPassword: false,
            showPasswordConfirmation: false
        }"
    >
        <div class="max-w-6xl px-4 py-8 mx-auto sm:px-6 lg:px-8">

            {{-- العنوان --}}
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-white/5 border-white/10 backdrop-blur-xl">
                    <span class="text-cyan-300">⚙️</span>
                    <span class="text-sm font-semibold text-slate-300">إدارة الملف الشخصي</span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    إعدادات الحساب
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400 sm:text-base">
                    يمكنك من هنا تعديل بياناتك الشخصية وكلمة المرور والتخصص والنبذة، مع تصميم أوضح وأسهل للاستخدام.
                </p>
            </div>

            {{-- رسائل النجاح / الخطأ --}}
            @if (session('status'))
                <div class="p-4 mb-6 text-green-200 border bg-green-500/10 border-green-400/20 rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-5 mb-6 border bg-red-500/10 border-red-400/20 rounded-2xl">
                    <p class="mb-3 font-bold text-red-200">يوجد أخطاء يجب تعديلها:</p>
                    <ul class="space-y-1 text-sm list-disc list-inside text-red-200/90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- شريط التبويبات --}}
            <div class="sticky z-20 mb-8 top-4">
                <div class="p-3 border shadow-2xl rounded-3xl bg-slate-950/80 border-white/10 backdrop-blur-2xl">
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">

                        <a
                            href="#section-profile"
                            onclick="event.preventDefault(); document.getElementById('section-profile').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                            class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold text-white transition-all duration-200 border border-cyan-400/20 rounded-2xl bg-gradient-to-l from-cyan-500/20 to-blue-600/20 hover:-translate-y-0.5 hover:border-cyan-300/40 hover:bg-cyan-500/20"
                        >
                            <span>👤</span>
                            <span>البيانات الشخصية</span>
                        </a>

                        <a
                            href="#section-password"
                            onclick="event.preventDefault(); document.getElementById('section-password').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                            class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all duration-200 border rounded-2xl border-white/10 bg-white/[0.03] text-slate-300 hover:-translate-y-0.5 hover:bg-white/[0.07] hover:text-white"
                        >
                            <span>🔐</span>
                            <span>كلمة المرور</span>
                        </a>

                        <a
                            href="#section-specialty"
                            onclick="event.preventDefault(); document.getElementById('section-specialty').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                            class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all duration-200 border rounded-2xl border-white/10 bg-white/[0.03] text-slate-300 hover:-translate-y-0.5 hover:bg-white/[0.07] hover:text-white"
                        >
                            <span>🧾</span>
                            <span>التخصص والنبذة</span>
                        </a>

                        <a
                            href="#section-delete"
                            onclick="event.preventDefault(); document.getElementById('section-delete').scrollIntoView({ behavior: 'smooth', block: 'start' });"
                            class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-bold transition-all duration-200 border rounded-2xl border-red-400/20 bg-red-500/5 text-red-200 hover:-translate-y-0.5 hover:bg-red-500/10 hover:text-white"
                        >
                            <span>⚠️</span>
                            <span>حذف الحساب</span>
                        </a>

                    </div>
                </div>
            </div>

            <div class="space-y-8">

                {{-- البيانات الشخصية --}}
                <section
                    id="section-profile"
                    class="scroll-mt-32 overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
                >
                    <div class="p-6 border-b sm:p-8 border-white/10">
                        <h2 class="text-2xl font-black text-white">البيانات الشخصية</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            قم بتعديل اسمك وبريدك ورقم هاتفك والصورة الشخصية.
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
                        <div class="p-5 border rounded-3xl border-white/10 bg-white/[0.02]">
                            <label class="block mb-4 text-lg font-bold text-white">
                                الصورة الشخصية
                            </label>

                            <div class="flex flex-col gap-5 md:flex-row md:items-center">
                                <div class="flex items-center justify-center overflow-hidden border w-28 h-28 rounded-3xl border-cyan-400/20 bg-slate-900/70">
                                    @if (auth()->user()->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                            alt="الصورة الشخصية"
                                            class="object-cover w-full h-full"
                                        >
                                    @else
                                        <img
                                            src="{{ asset('images/Mainlogo.png') }}"
                                            alt="الصورة الشخصية"
                                            class="object-contain w-full h-full p-3"
                                        >
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <label class="inline-flex items-center gap-2 px-5 py-3 mb-3 text-sm font-bold text-white transition-all bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02] cursor-pointer">
                                        <span>📁</span>
                                        <span>اختيار ملف</span>
                                        <input
                                            type="file"
                                            name="profile_photo"
                                            class="hidden"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                    </label>

                                    <p class="text-sm leading-7 text-slate-400">
                                        اترك الحقل فارغًا إذا لا ترغب بتغيير الصورة.
                                        الصيغ المسموحة: JPG, JPEG, PNG, WEBP — الحد الأقصى 2MB.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-200">
                                    الاسم الكامل
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', auth()->user()->name) }}"
                                    class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="أدخل الاسم الكامل"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-200">
                                    البريد الإلكتروني
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="أدخل البريد الإلكتروني"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-200">
                                رقم الهاتف
                            </label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone', auth()->user()->phone) }}"
                                class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                placeholder="أدخل رقم الهاتف"
                            >
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-4 font-bold text-white transition-all shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02] hover:shadow-cyan-500/20"
                            >
                                <span>💾</span>
                                <span>حفظ التعديلات</span>
                            </button>
                        </div>
                    </form>
                </section>

                {{-- كلمة المرور --}}
                <section
                    id="section-password"
                    class="scroll-mt-32 overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
                >
                    <div class="p-6 border-b sm:p-8 border-white/10">
                        <h2 class="text-2xl font-black text-white">كلمة المرور</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('password.update') }}"
                        class="p-6 space-y-6 sm:p-8"
                    >
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-200">
                                كلمة المرور الحالية
                            </label>
                            <div class="relative">
                                <input
                                    :type="showCurrent ? 'text' : 'password'"
                                    name="current_password"
                                    class="w-full px-5 py-4 pl-24 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="أدخل كلمة المرور الحالية"
                                >
                                <button
                                    type="button"
                                    @click="showCurrent = !showCurrent"
                                    class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300 hover:text-white"
                                >
                                    <span x-text="showCurrent ? 'إخفاء' : 'إظهار'"></span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-200">
                                كلمة المرور الجديدة
                            </label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    class="w-full px-5 py-4 pl-24 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="أدخل كلمة المرور الجديدة"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300 hover:text-white"
                                >
                                    <span x-text="showPassword ? 'إخفاء' : 'إظهار'"></span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-200">
                                تأكيد كلمة المرور
                            </label>
                            <div class="relative">
                                <input
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    name="password_confirmation"
                                    class="w-full px-5 py-4 pl-24 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="أعد كتابة كلمة المرور"
                                >
                                <button
                                    type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300 hover:text-white"
                                >
                                    <span x-text="showPasswordConfirmation ? 'إخفاء' : 'إظهار'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="p-4 border rounded-2xl border-cyan-400/15 bg-cyan-500/5">
                            <p class="text-sm leading-7 text-slate-300">
                                يفضّل أن تكون كلمة المرور مكوّنة من 8 أحرف على الأقل، وتحتوي على حرف كبير ورقم ورمز.
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-4 font-bold text-white transition-all shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                            >
                                <span>🔐</span>
                                <span>حفظ كلمة المرور</span>
                            </button>
                        </div>
                    </form>
                </section>

                {{-- التخصص والنبذة --}}
                <section
                    id="section-specialty"
                    class="scroll-mt-32 overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
                >
                    <div class="p-6 border-b sm:p-8 border-white/10">
                        <h2 class="text-2xl font-black text-white">التخصص والنبذة</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            عدّل تخصصك والنبذة التي تظهر في ملفك الشخصي وصفحتك العامة.
                        </p>
                    </div>

                    @if (auth()->user()->role === 'engineer')
                        <form
                            method="POST"
                            action="{{ route('engineer.specialty.update') }}"
                            class="p-6 space-y-6 sm:p-8"
                        >
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-200">
                                    التخصص الهندسي
                                </label>
                                <input
                                    type="text"
                                    name="specialty"
                                    value="{{ old('specialty', auth()->user()->employeeProfile?->specialty?->name) }}"
                                    class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="مثال: الهندسة الكهربائية"
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-200">
                                    النبذة الشخصية
                                </label>
                                <textarea
                                    name="bio"
                                    rows="5"
                                    class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400/40"
                                    placeholder="اكتب نبذة قصيرة عن خبراتك وتخصصك..."
                                >{{ old('bio', auth()->user()->employeeProfile?->bio) }}</textarea>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-4 font-bold text-white transition-all shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                                >
                                    <span>🧾</span>
                                    <span>حفظ التخصص والنبذة</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-6 sm:p-8">
                            <div class="p-5 border rounded-2xl border-white/10 bg-white/[0.03]">
                                <p class="font-bold text-white">هذا القسم مخصص للمهندسين.</p>
                                <p class="mt-2 text-sm leading-7 text-slate-400">
                                    إذا أردت جعله متاحًا لباقي الأدوار، أخبرني وأعدله لك.
                                </p>
                            </div>
                        </div>
                    @endif
                </section>

                {{-- حذف الحساب --}}
                <section
                    id="section-delete"
                    class="scroll-mt-32 overflow-hidden border shadow-2xl rounded-[2rem] border-red-400/10 bg-slate-950/40 backdrop-blur-xl"
                >
                    <div class="p-6 border-b sm:p-8 border-red-400/10">
                        <h2 class="text-2xl font-black text-white">حذف الحساب</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            عند حذف الحساب سيتم حذف البيانات بشكل نهائي، ولن تتمكن من استعادتها لاحقًا.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('profile.destroy') }}"
                        class="p-6 space-y-5 sm:p-8"
                        onsubmit="return confirm('هل أنت متأكد من حذف الحساب نهائيًا؟');"
                    >
                        @csrf
                        @method('DELETE')

                        <div class="p-5 border rounded-3xl border-red-400/20 bg-red-500/10">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center flex-none text-2xl w-14 h-14 rounded-2xl bg-red-500/15">
                                    ⚠️
                                </div>

                                <div>
                                    <p class="text-lg font-black text-red-200">
                                        تنبيه مهم
                                    </p>
                                    <p class="mt-2 text-sm leading-7 text-red-100/80">
                                        هذا الإجراء غير قابل للتراجع، وسيتم حذف حسابك نهائيًا مع بياناته المرتبطة.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-200">
                                أدخل كلمة المرور للتأكيد
                            </label>
                            <input
                                type="password"
                                name="password"
                                class="w-full px-5 py-4 text-white transition border rounded-2xl border-white/10 bg-slate-900/60 placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:border-red-400/40"
                                placeholder="أدخل كلمة المرور الحالية"
                                required
                            >
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-6 py-4 font-bold text-white transition-all shadow-xl bg-gradient-to-l from-red-500 to-rose-600 rounded-2xl hover:scale-[1.02]"
                            >
                                <span>🗑️</span>
                                <span>حذف الحساب نهائيًا</span>
                            </button>
                        </div>
                    </form>
                </section>

            </div>
        </div>
    </div>
</x-app-layout>
