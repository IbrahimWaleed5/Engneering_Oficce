<x-app-layout>

    <x-slot name="header">

        <div dir="rtl">

            <h2 class="text-2xl font-black text-white">
                الملف الشخصي
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                إدارة بيانات الحساب والصورة الشخصية وكلمة المرور وإعدادات الأمان
            </p>

        </div>

    </x-slot>

    <div
        class="py-10"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="grid gap-6 lg:grid-cols-3">

                <aside class="lg:col-span-1">

                    <div class="sticky p-6 glass-panel-strong rounded-[2rem] top-6">

                        <div class="text-center">

                            @if(auth()->user()->profile_photo)

                                <img
                                    src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="object-cover w-24 h-24 mx-auto border-4 rounded-full shadow-lg border-cyan-500/30 shadow-blue-500/20"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-24 h-24 mx-auto text-4xl font-black text-white rounded-full shadow-lg bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-500/20"
                                >
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>

                            @endif

                            <h3 class="mt-5 text-xl font-black text-white">
                                {{ auth()->user()->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-400">
                                {{ auth()->user()->email }}
                            </p>

                            @if (auth()->user()->phone)
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ auth()->user()->phone }}
                                </p>
                            @endif

                            <div class="mt-4">

                                <span
                                    class="inline-flex items-center px-4 py-2 text-sm font-bold border rounded-full bg-blue-500/10 text-cyan-300 border-blue-500/20"
                                >
                                    @switch(auth()->user()->role)

                                        @case('admin')
                                            مدير النظام
                                            @break

                                        @case('engineer')
                                            مهندس
                                            @break

                                        @case('employee')
                                            موظف
                                            @break

                                        @case('customer')
                                            عميل
                                            @break

                                        @default
                                            مستخدم

                                    @endswitch
                                </span>

                            </div>

                        </div>

                        <div class="pt-6 mt-6 space-y-3 border-t border-white/10">

                            <div
                                class="flex items-center justify-between p-3 rounded-2xl bg-white/5"
                            >
                                <span class="text-sm text-slate-400">
                                    حالة الحساب
                                </span>

                                <span
                                    class="text-sm font-bold
                                    {{ auth()->user()->status === 'active'
                                        ? 'text-green-300'
                                        : 'text-red-300' }}"
                                >
                                    {{ auth()->user()->status === 'active'
                                        ? 'نشط'
                                        : 'غير نشط' }}
                                </span>
                            </div>

                            <div
                                class="flex items-center justify-between p-3 rounded-2xl bg-white/5"
                            >
                                <span class="text-sm text-slate-400">
                                    تاريخ التسجيل
                                </span>

                                <span class="text-sm text-slate-300">
                                    {{ auth()->user()->created_at?->format('Y-m-d') }}
                                </span>
                            </div>

                        </div>

                    </div>

                </aside>

                <main class="space-y-6 lg:col-span-2">

                    @if (session('status') === 'profile-updated')

                        <div
                            class="p-4 text-sm text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                        >
                            ✅ تم تحديث بيانات الحساب بنجاح.
                        </div>

                    @endif

                    @if (session('status') === 'password-updated')

                        <div
                            class="p-4 text-sm text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                        >
                            ✅ تم تحديث كلمة المرور بنجاح.
                        </div>

                    @endif

                    <section class="p-6 sm:p-8 glass-panel-strong rounded-[2rem]">

                        <div class="mb-6">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-blue-500/10"
                                >
                                    👤
                                </div>

                                <div>

                                    <h3 class="text-xl font-black text-white">
                                        البيانات الشخصية
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        تعديل الصورة الشخصية والاسم والبريد الإلكتروني ورقم الهاتف
                                    </p>

                                </div>

                            </div>

                        </div>

                        @include('profile.partials.update-profile-information-form')

                    </section>

                    <section class="p-6 sm:p-8 glass-panel-strong rounded-[2rem]">

                        <div class="mb-6">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-cyan-500/10"
                                >
                                    🔐
                                </div>

                                <div>

                                    <h3 class="text-xl font-black text-white">
                                        تغيير كلمة المرور
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        استخدم كلمة مرور قوية للحفاظ على أمان الحساب
                                    </p>

                                </div>

                            </div>

                        </div>

                        @include('profile.partials.update-password-form')

                    </section>

                    <section
                        class="p-6 sm:p-8 border glass-panel-strong rounded-[2rem] border-red-500/20"
                    >

                        <div class="mb-6">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-red-500/10"
                                >
                                    ⚠️
                                </div>

                                <div>

                                    <h3 class="text-xl font-black text-white">
                                        حذف الحساب
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        حذف الحساب نهائي ولا يمكن التراجع عنه
                                    </p>

                                </div>

                            </div>

                        </div>

                        @include('profile.partials.delete-user-form')

                    </section>

                </main>

            </div>

        </div>

    </div>

</x-app-layout>
