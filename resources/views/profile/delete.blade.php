<x-app-layout>

    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(239,68,68,0.10),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
        x-data="{ showPassword: false }"
    >
        <div class="max-w-5xl px-4 py-10 mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-red-500/5 border-red-400/20"
                >
                    <span>⚠️</span>
                    <span class="text-sm font-bold text-red-200">
                        المنطقة الخطرة
                    </span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    حذف الحساب
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    اقرأ التنبيه جيدًا قبل تنفيذ عملية حذف الحساب.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            @if ($errors->userDeletion->any())
                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <ul class="space-y-2 list-disc list-inside">
                        @foreach ($errors->userDeletion->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section
                class="overflow-hidden border shadow-2xl rounded-[2rem] border-red-400/20 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-red-400/10">
                    <h2 class="text-2xl font-black text-white">
                        حذف الحساب نهائيًا
                    </h2>

                    <p class="mt-2 text-sm leading-7 text-slate-400">
                        لا يمكن استعادة الحساب أو بياناته بعد الحذف.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('profile.destroy') }}"
                    class="p-6 space-y-6 sm:p-8"
                    onsubmit="return confirm('هل أنت متأكد من حذف الحساب نهائيًا؟');"
                >
                    @csrf
                    @method('DELETE')

                    <div
                        class="p-6 border rounded-3xl border-red-400/20 bg-red-500/10"
                    >
                        <div class="flex items-start gap-4">

                            <div
                                class="flex items-center justify-center flex-none w-16 h-16 text-3xl rounded-2xl bg-red-500/15"
                            >
                                ⚠️
                            </div>

                            <div>
                                <h3 class="text-xl font-black text-red-200">
                                    تحذير
                                </h3>

                                <p class="mt-2 text-sm leading-8 text-red-100/80">
                                    سيتم حذف حسابك وبياناتك الشخصية بشكل نهائي.
                                    قد يتم أيضًا حذف البيانات المرتبطة بالحساب
                                    بحسب العلاقات الموجودة في قاعدة البيانات.
                                </p>
                            </div>

                        </div>
                    </div>

                    <div>
                        <label
                            for="delete_password"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            أدخل كلمة المرور للتأكيد
                        </label>

                        <div class="relative">
                            <input
                                id="delete_password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                class="w-full px-5 py-4 pl-24 text-white border rounded-2xl border-white/10 bg-slate-900/60 focus:border-red-400/40 focus:ring-2 focus:ring-red-500/20"
                            >

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute px-4 py-2 text-sm font-bold text-red-300 -translate-y-1/2 left-3 top-1/2"
                            >
                                <span x-text="showPassword ? 'إخفاء' : 'إظهار'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-7 py-4 font-bold text-white transition shadow-xl bg-gradient-to-l from-red-500 to-rose-600 rounded-2xl hover:scale-[1.02]"
                        >
                            <span>🗑️</span>
                            <span>حذف الحساب نهائيًا</span>
                        </button>
                    </div>

                </form>
            </section>

        </div>
    </div>

</x-app-layout>
