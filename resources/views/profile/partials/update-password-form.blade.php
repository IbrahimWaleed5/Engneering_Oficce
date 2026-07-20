<section
    x-data="{
        showCurrent: false,
        showNew: false,
        showConfirm: false
    }"
>

    <form
        method="POST"
        action="{{ route('password.update') }}"
        class="space-y-5"
    >
        @csrf
        @method('PUT')

        <div>
            <label
                for="update_password_current_password"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                كلمة المرور الحالية
            </label>

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    🔒
                </span>

                <input
                    id="update_password_current_password"
                    name="current_password"
                    :type="showCurrent ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="أدخل كلمة المرور الحالية"
                    class="pl-20 pr-12 form-control"
                >

                <button
                    type="button"
                    @click="showCurrent = !showCurrent"
                    class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300"
                >
                    <span x-show="!showCurrent">إظهار</span>
                    <span x-cloak x-show="showCurrent">إخفاء</span>
                </button>
            </div>

            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-sm text-red-300">
                    {{ $errors->updatePassword->first('current_password') }}
                </p>
            @endif
        </div>

        <div>
            <label
                for="update_password_password"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                كلمة المرور الجديدة
            </label>

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    🔐
                </span>

                <input
                    id="update_password_password"
                    name="password"
                    :type="showNew ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="أدخل كلمة المرور الجديدة"
                    class="pl-20 pr-12 form-control"
                >

                <button
                    type="button"
                    @click="showNew = !showNew"
                    class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300"
                >
                    <span x-show="!showNew">إظهار</span>
                    <span x-cloak x-show="showNew">إخفاء</span>
                </button>
            </div>

            @if ($errors->updatePassword->has('password'))
                <p class="mt-2 text-sm text-red-300">
                    {{ $errors->updatePassword->first('password') }}
                </p>
            @endif
        </div>

        <div>
            <label
                for="update_password_password_confirmation"
                class="block mb-2 text-sm font-bold text-slate-200"
            >
                تأكيد كلمة المرور
            </label>

            <div class="relative">
                <span
                    class="absolute -translate-y-1/2 pointer-events-none right-4 top-1/2"
                >
                    🛡️
                </span>

                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="أعد كتابة كلمة المرور"
                    class="pl-20 pr-12 form-control"
                >

                <button
                    type="button"
                    @click="showConfirm = !showConfirm"
                    class="absolute text-sm font-bold -translate-y-1/2 left-4 top-1/2 text-cyan-300"
                >
                    <span x-show="!showConfirm">إظهار</span>
                    <span x-cloak x-show="showConfirm">إخفاء</span>
                </button>
            </div>

            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-sm text-red-300">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </p>
            @endif
        </div>

        <div
            class="p-4 text-sm leading-7 border rounded-2xl border-cyan-500/20 bg-cyan-500/5 text-slate-400"
        >
            استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز.
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <button
                type="submit"
                class="primary-button"
            >
                حفظ كلمة المرور
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-bold text-green-300"
                >
                    تم تحديث كلمة المرور
                </p>
            @endif
        </div>
        @if (auth()->user()->role === 'engineer')

    <div
        class="p-5 mt-6 border rounded-2xl border-cyan-500/20 bg-cyan-500/5"
    >

        <p class="text-sm text-slate-400">
            التخصص الهندسي
        </p>

        <p class="mt-2 text-lg font-black text-cyan-300">

            {{
                auth()->user()
                    ->employeeProfile
                    ?->specialty
                    ?->name
                ?? 'لم يتم اختيار التخصص'
            }}

        </p>

        <a
            href="{{ route('engineer.specialty.edit') }}"
            class="inline-block mt-4 text-sm font-bold text-blue-300 hover:text-blue-200"
        >
            تعديل التخصص
        </a>

    </div>

@endif
    </form>

</section>
