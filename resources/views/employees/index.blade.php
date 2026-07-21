<x-app-layout>

    <x-slot name="header">

        <div
            class="flex flex-wrap items-center justify-between gap-4"
            dir="rtl"
        >

            <div>

                <h2 class="text-2xl font-black text-white">
                    إدارة الموظفين
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    إدارة الموظفين والمهندسين والمشرفين
                </p>

            </div>

            <a
                href="{{ route('employees.create') }}"
                class="primary-button"
            >
                إضافة موظف
            </a>

        </div>

    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- الإحصائيات --}}

            <div class="grid gap-4 mb-8 md:grid-cols-3">

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        جميع الموظفين
                    </p>

                    <p class="mt-3 text-3xl font-black text-white">
                        {{ $employees->count() }}
                    </p>

                </div>

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        المهندسون
                    </p>

                    <p class="mt-3 text-3xl font-black text-cyan-300">
                        {{ $employees->where('role', 'engineer')->count() }}
                    </p>

                </div>

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        المدراء
                    </p>

                    <p class="mt-3 text-3xl font-black text-purple-300">
                        {{ $employees->where('role', 'admin')->count() }}
                    </p>

                </div>

            </div>

            {{-- الجدول --}}

            <section
                class="overflow-hidden glass-panel-strong rounded-[2rem]"
            >

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-white/5 text-slate-400">

                            <tr>

                                <th class="px-6 py-4 text-right">
                                    المستخدم
                                </th>

                                <th class="px-6 py-4 text-right">
                                    البريد الإلكتروني
                                </th>

                                <th class="px-6 py-4 text-right">
                                    الصلاحية
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            @forelse ($employees as $employee)

                                <tr
                                    class="transition hover:bg-white/5"
                                >

                                    <td class="px-6 py-5">

                                        <div
                                            class="flex items-center gap-3"
                                        >

                                            <div
                                                class="flex items-center justify-center w-12 h-12 font-black text-white rounded-full bg-gradient-to-br from-blue-600 to-cyan-500"
                                            >
                                                {{ mb_substr($employee->name, 0, 1) }}
                                            </div>

                                            <div>

                                                <p
                                                    class="font-bold text-white"
                                                >
                                                    {{ $employee->name }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-6 py-5 text-slate-300">

                                        {{ $employee->email }}

                                    </td>

                                    <td class="px-6 py-5">

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

                                            ];

                                            $role = $roles[$employee->role] ?? [

                                                'label' => $employee->role,
                                                'class' => 'bg-slate-500/10 text-slate-300',

                                            ];

                                        @endphp

                                        <span
                                            class="inline-flex px-3 py-2 text-xs font-bold rounded-full {{ $role['class'] }}"
                                        >
                                            {{ $role['label'] }}
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="3"
                                        class="px-6 py-16 text-center text-slate-400"
                                    >

                                        لا يوجد موظفون حتى الآن.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </div>

</x-app-layout>
