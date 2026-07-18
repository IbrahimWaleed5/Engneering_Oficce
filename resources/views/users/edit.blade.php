<x-app-layout>

    <x-slot name="header">

        <div dir="rtl">

            <h2 class="text-2xl font-black text-white">
                تعديل المستخدم
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                تعديل بيانات وصلاحيات {{ $user->name }}
            </p>

        </div>

    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">

            <section class="p-6 sm:p-8 glass-panel-strong rounded-[2rem]">

                <div class="flex items-center gap-4 mb-8">

                    <div
                        class="flex items-center justify-center w-16 h-16 text-2xl font-black text-white rounded-full bg-cyan-600"
                    >
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-white">
                            {{ $user->name }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-400">
                            {{ $user->email }}
                        </p>
                    </div>

                </div>

                <form
                    method="POST"
                    action="{{ route('users.update', $user) }}"
                    class="space-y-5"
                >
                    @csrf
                    @method('PATCH')

                    <div>

                        <label
                            for="name"
                            class="block mb-2 text-sm font-bold text-slate-200"
                        >
                            الاسم الكامل
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="form-control"
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
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="form-control"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror

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
                            name="phone"
                            type="text"
                            value="{{ old('phone', $user->phone) }}"
                            class="form-control"
                        >

                        @error('phone')
                            <p class="mt-2 text-sm text-red-300">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="grid gap-5 md:grid-cols-2">

                        <div>

                            <label
                                for="role"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                الدور
                            </label>

                            <select
                                id="role"
                                name="role"
                                required
                                class="form-control"
                            >
                                <option
                                    value="admin"
                                    @selected(old('role', $user->role) === 'admin')
                                >
                                    مدير
                                </option>

                                <option
                                    value="engineer"
                                    @selected(old('role', $user->role) === 'engineer')
                                >
                                    مهندس
                                </option>

                                <option
                                    value="employee"
                                    @selected(old('role', $user->role) === 'employee')
                                >
                                    موظف
                                </option>

                                <option
                                    value="customer"
                                    @selected(old('role', $user->role) === 'customer')
                                >
                                    عميل
                                </option>
                            </select>

                            @error('role')
                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <div>

                            <label
                                for="status"
                                class="block mb-2 text-sm font-bold text-slate-200"
                            >
                                حالة الحساب
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="form-control"
                            >
                                <option
                                    value="active"
                                    @selected(old('status', $user->status) === 'active')
                                >
                                    نشط
                                </option>

                                <option
                                    value="inactive"
                                    @selected(old('status', $user->status) === 'inactive')
                                >
                                    غير نشط
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-2 text-sm text-red-300">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    <div class="flex flex-wrap gap-3 pt-4">
@if (
    old('role', $user->role) === 'engineer'
    || old('role', $user->role) === 'employee'
)

    <div class="p-5 border rounded-2xl border-cyan-500/20 bg-cyan-500/5">

        <h3 class="mb-5 text-lg font-black text-white">
            بيانات الوظيفة
        </h3>

        <div class="grid gap-5 md:grid-cols-3">

            <div>
                <label
                    for="job_title"
                    class="block mb-2 text-sm font-bold text-slate-200"
                >
                    المسمى الوظيفي
                </label>

                <input
                    id="job_title"
                    name="job_title"
                    type="text"
                    value="{{ old(
                        'job_title',
                        $user->employeeProfile?->job_title
                    ) }}"
                    class="form-control"
                >
            </div>

            <div>
                <label
                    for="salary"
                    class="block mb-2 text-sm font-bold text-slate-200"
                >
                    الراتب
                </label>

                <input
                    id="salary"
                    name="salary"
                    type="number"
                    min="0"
                    step="0.01"
                    value="{{ old(
                        'salary',
                        $user->employeeProfile?->salary ?? 0
                    ) }}"
                    class="form-control"
                >
            </div>

            <div>
                <label
                    for="hire_date"
                    class="block mb-2 text-sm font-bold text-slate-200"
                >
                    تاريخ التعيين
                </label>

                <input
                    id="hire_date"
                    name="hire_date"
                    type="date"
                    value="{{ old(
                        'hire_date',
                        $user->employeeProfile?->hire_date?->format('Y-m-d')
                    ) }}"
                    class="form-control"
                >
            </div>

        </div>

    </div>

@endif
                        <button
                            type="submit"
                            class="primary-button"
                        >
                            حفظ التعديلات
                        </button>

                        <a
                            href="{{ route('users.index') }}"
                            class="px-5 py-3 font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
                        >
                            إلغاء
                        </a>

                    </div>

                </form>

            </section>

        </div>

    </div>

</x-app-layout>
