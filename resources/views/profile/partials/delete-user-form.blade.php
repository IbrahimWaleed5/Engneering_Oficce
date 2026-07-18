<section class="space-y-6">

    <div
        class="p-5 border rounded-2xl border-red-500/20 bg-red-500/5"
    >
        <div class="flex items-start gap-4">
            <div
                class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-red-500/10"
            >
                ⚠️
            </div>

            <div>
                <h2 class="text-xl font-black text-white">
                    حذف الحساب
                </h2>

                <p class="mt-2 text-sm leading-7 text-slate-400">
                    عند حذف الحساب سيتم حذف جميع البيانات المرتبطة به
                    بشكل نهائي، ولن تتمكن من استعادتها لاحقًا.
                </p>
            </div>
        </div>
    </div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-5 py-3 font-bold text-red-300 transition border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
    >
        حذف الحساب نهائيًا
    </button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-8"
            dir="rtl"
            x-data="{ showPassword: false }"
        >
            @csrf
            @method('DELETE')

            <div class="text-center">
                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto text-3xl rounded-full bg-red-500/10"
                >
                    🗑️
                </div>

                <h2 class="mt-5 text-2xl font-black text-white">
                    تأكيد حذف الحساب
                </h2>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    لا يمكن التراجع عن هذه العملية. أدخل كلمة المرور للتأكيد.
                </p>
            </div>

            <div class="mt-8">
                <label
                    for="delete_account_password"
                    class="block mb-2 text-sm font-bold text-slate-200"
                >
                    كلمة المرور
                </label>

                <div class="relative">
                    <span
                        class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                    >
                        🔒
                    </span>

                    <input
                        id="delete_account_password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="pl-20 pr-12 form-control"
                        placeholder="أدخل كلمة المرور"
                    >

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300"
                    >
                        <span x-show="!showPassword">إظهار</span>
                        <span x-cloak x-show="showPassword">إخفاء</span>
                    </button>
                </div>

                @if ($errors->userDeletion->has('password'))
                    <p class="mt-2 text-sm text-red-300">
                        {{ $errors->userDeletion->first('password') }}
                    </p>
                @endif
            </div>

            <div class="flex flex-wrap justify-end gap-3 mt-8">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-5 py-3 font-semibold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
                >
                    إلغاء
                </button>

                <button
                    type="submit"
                    class="px-5 py-3 font-bold text-white transition bg-red-600 rounded-2xl hover:bg-red-700"
                >
                    حذف الحساب
                </button>
            </div>
        </form>
    </x-modal>

</section>
