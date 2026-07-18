<x-app-layout>

    <x-slot name="header">

        <div
            class="flex flex-wrap items-center justify-between gap-4"
            dir="rtl"
        >
            <div>
                <h2 class="text-2xl font-black text-white">
                    إدارة المستخدمين
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    إدارة العملاء والمهندسين والموظفين والمديرين
                </p>
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="px-5 py-3 text-sm font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
            >
                العودة إلى لوحة التحكم
            </a>
        </div>

    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('delete'))
                <div
                    class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    {{ $errors->first('delete') }}
                </div>
            @endif

            <div class="grid gap-4 mb-8 sm:grid-cols-2 xl:grid-cols-4">

                <div class="p-5 glass-panel-strong rounded-[2rem]">
                    <p class="text-sm text-slate-400">
                        جميع المستخدمين
                    </p>

                    <p class="mt-3 text-3xl font-black text-white">
                        {{ $statistics['all'] }}
                    </p>
                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">
                    <p class="text-sm text-slate-400">
                        المهندسون
                    </p>

                    <p class="mt-3 text-3xl font-black text-blue-300">
                        {{ $statistics['engineers'] }}
                    </p>
                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">
                    <p class="text-sm text-slate-400">
                        العملاء
                    </p>

                    <p class="mt-3 text-3xl font-black text-cyan-300">
                        {{ $statistics['customers'] }}
                    </p>
                </div>

                <div class="p-5 glass-panel-strong rounded-[2rem]">
                    <p class="text-sm text-slate-400">
                        الحسابات غير النشطة
                    </p>

                    <p class="mt-3 text-3xl font-black text-red-300">
                        {{ $statistics['inactive'] }}
                    </p>
                </div>

            </div>

            <section class="p-6 mb-8 glass-panel-strong rounded-[2rem]">

                <form
                    method="GET"
                    action="{{ route('users.index') }}"
                    class="grid gap-4 md:grid-cols-4"
                >

                    <div class="md:col-span-2">

                        <label
                            for="search"
                            class="block mb-2 text-sm font-bold text-slate-300"
                        >
                            البحث
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="الاسم أو البريد أو الهاتف"
                            class="form-control"
                        >

                    </div>

                    <div>

                        <label
                            for="role"
                            class="block mb-2 text-sm font-bold text-slate-300"
                        >
                            الدور
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="form-control"
                        >
                            <option value="">
                                جميع الأدوار
                            </option>

                            <option
                                value="admin"
                                @selected(request('role') === 'admin')
                            >
                                مدير
                            </option>

                            <option
                                value="engineer"
                                @selected(request('role') === 'engineer')
                            >
                                مهندس
                            </option>

                            <option
                                value="employee"
                                @selected(request('role') === 'employee')
                            >
                                موظف
                            </option>

                            <option
                                value="customer"
                                @selected(request('role') === 'customer')
                            >
                                عميل
                            </option>
                        </select>

                    </div>

                    <div>

                        <label
                            for="status"
                            class="block mb-2 text-sm font-bold text-slate-300"
                        >
                            الحالة
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-control"
                        >
                            <option value="">
                                جميع الحالات
                            </option>

                            <option
                                value="active"
                                @selected(request('status') === 'active')
                            >
                                نشط
                            </option>

                            <option
                                value="inactive"
                                @selected(request('status') === 'inactive')
                            >
                                غير نشط
                            </option>
                        </select>

                    </div>

                    <div class="flex flex-wrap gap-3 md:col-span-4">

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            تطبيق البحث
                        </button>

                        <a
                            href="{{ route('users.index') }}"
                            class="px-5 py-3 font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
                        >
                            مسح الفلاتر
                        </a>

                    </div>

                </form>

            </section>

            <section class="overflow-hidden glass-panel-strong rounded-[2rem]">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-white/5 text-slate-400">

                            <tr>
                                <th class="px-6 py-4 text-right">
                                    المستخدم
                                </th>

                                <th class="px-6 py-4 text-right">
                                    الهاتف
                                </th>

                                <th class="px-6 py-4 text-right">
                                    الدور
                                </th>

                                <th class="px-6 py-4 text-right">
                                    الحالة
                                </th>

                                <th class="px-6 py-4 text-right">
                                    تاريخ التسجيل
                                </th>

                                <th class="px-6 py-4 text-right">
                                    الإجراءات
                                </th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            @forelse ($users as $user)

                                <tr class="transition hover:bg-white/5">

                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="flex items-center justify-center font-black text-white rounded-full w-11 h-11 bg-cyan-600"
                                            >
                                                {{ mb_substr($user->name, 0, 1) }}
                                            </div>

                                            <div>
                                                <p class="font-bold text-white">
                                                    {{ $user->name }}

                                                    @if ($user->id === auth()->id())
                                                        <span class="text-xs text-cyan-300">
                                                            (أنت)
                                                        </span>
                                                    @endif
                                                </p>

                                                <p class="mt-1 text-xs text-slate-400">
                                                    {{ $user->email }}
                                                </p>
                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-6 py-4 text-slate-300">
                                        {{ $user->phone ?: '—' }}
                                    </td>

                                    <td class="px-6 py-4">

                                        @php
                                            $roles = [
                                                'admin' => [
                                                    'label' => 'مدير',
                                                    'class' => 'bg-purple-500/10 text-purple-300',
                                                ],

                                                'engineer' => [
                                                    'label' => 'مهندس',
                                                    'class' => 'bg-blue-500/10 text-blue-300',
                                                ],

                                                'employee' => [
                                                    'label' => 'موظف',
                                                    'class' => 'bg-yellow-500/10 text-yellow-300',
                                                ],

                                                'customer' => [
                                                    'label' => 'عميل',
                                                    'class' => 'bg-cyan-500/10 text-cyan-300',
                                                ],
                                            ];

                                            $role = $roles[$user->role] ?? [
                                                'label' => $user->role,
                                                'class' => 'bg-slate-500/10 text-slate-300',
                                            ];
                                        @endphp

                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $role['class'] }}"
                                        >
                                            {{ $role['label'] }}
                                        </span>

                                    </td>

                                    <td class="px-6 py-4">

                                        @if ($user->status === 'active')
                                            <span
                                                class="inline-flex px-3 py-1 text-xs font-bold text-green-300 rounded-full bg-green-500/10"
                                            >
                                                نشط
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-3 py-1 text-xs font-bold text-red-300 rounded-full bg-red-500/10"
                                            >
                                                غير نشط
                                            </span>
                                        @endif

                                    </td>

                                    <td class="px-6 py-4 text-slate-400">
                                        {{ $user->created_at?->format('Y-m-d') }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <div class="flex flex-wrap gap-2">

                                            <a
                                                href="{{ route('users.edit', $user) }}"
                                                class="px-4 py-2 text-xs font-bold text-blue-300 transition rounded-xl bg-blue-500/10 hover:bg-blue-500/20"
                                            >
                                                تعديل
                                            </a>

                                            @if ($user->id !== auth()->id())

                                                <form
                                                    method="POST"
                                                    action="{{ route('users.destroy', $user) }}"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2 text-xs font-bold text-red-300 transition rounded-xl bg-red-500/10 hover:bg-red-500/20"
                                                    >
                                                        حذف
                                                    </button>
                                                </form>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="6"
                                        class="px-6 text-center py-14 text-slate-400"
                                    >
                                        لا يوجد مستخدمون مطابقون للبحث.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if ($users->hasPages())
                    <div class="p-6 border-t border-white/10">
                        {{ $users->links() }}
                    </div>
                @endif

            </section>

        </div>

    </div>
    <div class="flex flex-wrap gap-3">

    <a
        href="{{ route('users.create') }}"
        class="primary-button"
    >
        إضافة مستخدم جديد
    </a>

    <a
        href="{{ route('dashboard') }}"
        class="px-5 py-3 text-sm font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
    >
        العودة إلى لوحة التحكم
    </a>

</div>

</x-app-layout>
