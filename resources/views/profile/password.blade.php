<x-app-layout>

    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
        x-data="{
            showCurrent: false,
            showPassword: false,
            showConfirmation: false
        }"
    >
        <div class="max-w-5xl px-4 py-10 mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-white/5 border-white/10"
                >
                    <span>🔐</span>
                    <span class="text-sm font-bold text-slate-300">
                        حماية الحساب
                    </span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    تغيير كلمة المرور
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    قم بتحديث كلمة المرور للحفاظ على أمان حسابك.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            @if (session('status') === 'password-updated')
                <div
                    class="p-4 mb-6 font-bold text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    تم تحديث كلمة المرور بنجاح.
                </div>
            @endif

            @if ($errors->updatePassword->any())
                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <ul class="space-y-2 list-disc list-inside">
                        @foreach ($errors->updatePassword->all() as $error)
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
                        كلمة المرور
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        استخدم كلمة مرور قوية يصعب تخمينها.
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
                        <label
                            for="current_password"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            كلمة المرور الحالية
                        </label>

                        <div class="relative">
                            <input
                                id="current_password"
                                name="current_password"
                                :type="showCurrent ? 'text' : 'password'"
                                autocomplete="current-password"
                                class="w-full px-5 py-4 pl-24 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            <button
                                type="button"
                                @click="showCurrent = !showCurrent"
                                class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300"
                            >
                                <span x-text="showCurrent ? 'إخفاء' : 'إظهار'"></span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label
                            for="password"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            كلمة المرور الجديدة
                        </label>

                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                class="w-full px-5 py-4 pl-24 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300"
                            >
                                <span x-text="showPassword ? 'إخفاء' : 'إظهار'"></span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label
                            for="password_confirmation"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            تأكيد كلمة المرور
                        </label>

                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="showConfirmation ? 'text' : 'password'"
                                autocomplete="new-password"
                                class="w-full px-5 py-4 pl-24 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            >

                            <button
                                type="button"
                                @click="showConfirmation = !showConfirmation"
                                class="absolute px-4 py-2 text-sm font-bold -translate-y-1/2 left-3 top-1/2 text-cyan-300"
                            >
                                <span x-text="showConfirmation ? 'إخفاء' : 'إظهار'"></span>
                            </button>
                        </div>
                    </div>

                    <div
                        class="p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-300"
                    >
                        يفضل أن تحتوي كلمة المرور على 8 أحرف على الأقل،
                        مع أرقام ورموز وأحرف كبيرة وصغيرة.
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-7 py-4 font-bold text-white transition shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                        >
                            <span>🔐</span>
                            <span>حفظ كلمة المرور</span>
                        </button>
                    </div>

                </form>
            </section>

        </div>
    </div>

</x-app-layout>
