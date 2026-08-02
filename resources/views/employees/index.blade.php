<x-app-layout>
    @php
        $currentUser = auth()->user();

        $totalEmployees = $employees->count();
        $engineersCount = $employees->where('role', 'engineer')->count();
        $adminsCount = $employees->where('role', 'admin')->count();

        $roleLabels = [
            'admin' => 'مدير',
            'engineer' => 'مهندس',
            'employee' => 'موظف',
        ];
    @endphp

    <style>
        .employees-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family: 'Noto Sans Arabic', 'Almarai', sans-serif;
        }

        .employees-glass {
            background: rgba(23, 31, 51, .6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
            transition: all .3s ease-in-out;
        }

        .employees-glass:hover {
            background: rgba(34, 42, 61, .8);
            border-color: rgba(180, 197, 255, .2);
            box-shadow:
                0 8px 32px rgba(0, 0, 0, .4),
                0 0 15px rgba(37, 99, 235, .1);
        }

        .employees-sidebar-active {
            color: #b4c5ff;
            background: rgba(37, 99, 235, .1);
            border-right: 4px solid #2563eb;
        }

        .employees-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .employees-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .employees-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }

        .employee-row {
            transition: background-color .2s ease;
        }

        @media (max-width: 1023px) {
            .employees-sidebar {
                display: none !important;
            }

            .employees-main {
                margin-right: 0 !important;
            }

            .employees-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div class="employees-page" dir="rtl">
        {{-- القائمة الجانبية --}}
        <aside class="employees-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-[#434655] bg-[#171f33] shadow-xl">
            <div class="flex flex-col h-full px-6 py-6">
                <div class="mb-10">
                    <h1 class="text-2xl font-bold text-[#b4c5ff]">
                        CreativeHome
                    </h1>

                    <p class="text-xs text-[#c3c6d7]">
                        لوحة تحكم الإدارة
                    </p>
                </div>

                <nav class="flex-1 space-y-2">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>

                        <span>لوحة التحكم</span>
                    </a>

                    <a
                        href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <rect x="5" y="3" width="14" height="18" rx="2"/>
                            <path d="M8 8h8M8 12h8M8 16h5"/>
                        </svg>

                        <span>المشاريع</span>
                    </a>

                    <a
                        href="{{ Route::has('employees.index') ? route('employees.index') : request()->url() }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg employees-sidebar-active"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="9" cy="8" r="3"/>
                            <circle cx="17" cy="9" r="2.5"/>
                            <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                        </svg>

                        <span class="font-bold">فرق الهندسة</span>
                    </a>

                    <a
                        href="{{ Route::has('engineer.works.public') ? route('engineer.works.public') : route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                        </svg>

                        <span>مستودع الأعمال</span>
                    </a>

                    <a
                        href="{{ Route::has('conversations.index') ? route('conversations.index') : route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>

                        <span>المحادثات</span>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                        </svg>

                        <span>الإعدادات</span>
                    </a>
                </nav>

                <div class="pt-6 mt-auto border-t border-[#434655]">
                    <a
                        href="{{ route('employees.create') }}"
                        class="flex items-center justify-center w-full gap-2 px-4 py-3 font-bold text-white transition rounded-xl bg-[#2563eb] hover:scale-[1.02] active:scale-95"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>

                        إضافة موظف
                    </a>
                </div>
            </div>
        </aside>

        <main class="employees-main relative min-h-screen overflow-hidden bg-[#0b1326] lg:mr-64">
            <div class="absolute top-[-10%] left-[-10%] h-[40%] w-[40%] rounded-full bg-[#b4c5ff]/10 blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-[-5%] right-[5%] h-[30%] w-[30%] rounded-full bg-[#d2bbff]/5 blur-[100px] pointer-events-none"></div>

            {{-- الشريط العلوي --}}
            <header class="employees-topbar fixed top-0 left-0 right-64 z-40 flex h-16 items-center justify-between border-b border-[#434655] bg-[#0b1326]/80 px-4 backdrop-blur-md">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <svg class="absolute w-5 h-5 -translate-y-1/2 right-3 top-1/2 text-[#8d90a0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>

                        <input
                            id="employeesSearch"
                            type="search"
                            placeholder="البحث في الموظفين..."
                            class="w-64 rounded-full border-0 bg-[#2d3449]/50 py-2 pr-10 pl-4 text-sm text-white placeholder:text-[#8d90a0] focus:ring-2 focus:ring-[#b4c5ff]"
                        >
                    </div>

                    <nav class="hidden h-16 gap-6 md:flex">
                        <a href="#employeesContent" class="flex items-center h-full border-b-2 border-[#b4c5ff] font-bold text-[#b4c5ff]">
                            نظرة عامة
                        </a>

                        <button
                            type="button"
                            data-scroll-target="summaryCards"
                            class="flex items-center h-full text-[#c3c6d7] transition hover:text-[#b4c5ff]"
                        >
                            التحليلات
                        </button>

                        <button
                            type="button"
                            data-scroll-target="employeesTable"
                            class="flex items-center h-full text-[#c3c6d7] transition hover:text-[#b4c5ff]"
                        >
                            التقارير
                        </button>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <a
                        href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                        class="flex items-center justify-center w-10 h-10 rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]"
                        title="الإشعارات"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>
                        </svg>
                    </a>

                    <div class="h-8 w-px bg-[#434655]"></div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group">
                        <div class="ml-2 text-left">
                            <p class="text-xs font-bold text-[#dae2fd]">
                                {{ $currentUser->name }}
                            </p>

                            <p class="text-[10px] text-[#c3c6d7]">
                                مدير النظام
                            </p>
                        </div>

                        <div class="w-10 h-10 overflow-hidden border-2 rounded-full border-[#b4c5ff]/20 group-hover:border-[#b4c5ff]">
                            @if ($currentUser->profile_photo)
                                <img
                                    src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                    alt="{{ $currentUser->name }}"
                                    class="object-cover w-full h-full"
                                >
                            @else
                                <div class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-purple-600">
                                    {{ mb_substr($currentUser->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
            </header>

            <div id="employeesContent" class="relative z-10 px-4 pt-24 pb-10 mx-auto space-y-8 max-w-7xl">
                @if (session('success'))
                    <div class="p-4 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- العنوان --}}
                <section class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <h2 class="text-3xl font-black text-[#dae2fd]">
                            إدارة الموظفين
                        </h2>

                        <p class="mt-1 text-[#c3c6d7]">
                            CreativeHome للهندسة • {{ now()->year }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            id="toggleEmployeeFilter"
                            class="flex items-center gap-2 rounded-xl border border-[#434655] bg-[#222a3d] px-6 py-2 text-[#c3c6d7] transition hover:bg-[#2d3449]"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/>
                            </svg>

                            تصفية
                        </button>

                        <a
                            href="{{ route('employees.create') }}"
                            class="flex items-center gap-2 rounded-xl bg-[#2563eb] px-6 py-2 font-bold text-white shadow-lg shadow-blue-500/20 transition hover:opacity-90"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="9" cy="8" r="3"/>
                                <path d="M3 20a6 6 0 0 1 12 0M18 8v6M15 11h6"/>
                            </svg>

                            إضافة موظف
                        </a>
                    </div>
                </section>

                {{-- شريط التصفية --}}
                <section id="employeeFilterPanel" class="hidden p-4 employees-glass rounded-2xl">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="employeeRoleFilter" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                الصلاحية
                            </label>

                            <select
                                id="employeeRoleFilter"
                                class="w-full rounded-xl border border-[#434655] bg-[#131b2e] px-4 py-3 text-white focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">جميع الصلاحيات</option>
                                <option value="engineer">مهندس</option>
                                <option value="employee">موظف</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>

                        <div>
                            <label for="employeeStatusFilter" class="block mb-2 text-sm font-bold text-[#c3c6d7]">
                                الحالة
                            </label>

                            <select
                                id="employeeStatusFilter"
                                class="w-full rounded-xl border border-[#434655] bg-[#131b2e] px-4 py-3 text-white focus:ring-1 focus:ring-[#b4c5ff]"
                            >
                                <option value="all">جميع الحالات</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                type="button"
                                id="resetEmployeeFilters"
                                class="w-full rounded-xl bg-[#2d3449] px-4 py-3 font-bold text-[#dae2fd] transition hover:bg-[#31394d]"
                            >
                                مسح الفلاتر
                            </button>
                        </div>
                    </div>
                </section>

                {{-- البطاقات --}}
                <section id="summaryCards" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <article class="employees-glass relative flex items-center justify-between overflow-hidden rounded-[20px] p-5">
                        <div class="absolute w-24 h-24 rounded-full -bottom-6 -left-6 bg-[#b4c5ff]/5 blur-2xl"></div>

                        <div>
                            <p class="mb-2 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                إجمالي الموظفين
                            </p>

                            <h3 class="text-[48px] font-bold text-[#b4c5ff]">
                                {{ $totalEmployees }}
                            </h3>

                            <span class="mt-2 flex items-center gap-1 text-[11px] text-[#b4c5ff]">
                                فريق المكتب
                            </span>
                        </div>

                        <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-[#2563eb]/20 text-[#b4c5ff]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="9" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                            </svg>
                        </div>
                    </article>

                    <article class="employees-glass relative flex items-center justify-between overflow-hidden rounded-[20px] p-5">
                        <div class="absolute w-24 h-24 rounded-full -bottom-6 -left-6 bg-[#d2bbff]/5 blur-2xl"></div>

                        <div>
                            <p class="mb-2 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                المهندسون
                            </p>

                            <h3 class="text-[48px] font-bold text-[#d2bbff]">
                                {{ $engineersCount }}
                            </h3>

                            <span class="mt-2 flex items-center gap-1 text-[11px] text-[#d2bbff]">
                                معتمدون
                            </span>
                        </div>

                        <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-[#8343f4]/20 text-[#d2bbff]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="7" r="3"/>
                                <path d="M5 21a7 7 0 0 1 14 0M8 12h8M9 3h6"/>
                            </svg>
                        </div>
                    </article>

                    <article class="employees-glass relative flex items-center justify-between overflow-hidden rounded-[20px] p-5">
                        <div class="absolute w-24 h-24 rounded-full -bottom-6 -left-6 bg-[#ffb1c7]/5 blur-2xl"></div>

                        <div>
                            <p class="mb-2 text-xs font-bold tracking-wider uppercase text-[#c3c6d7]">
                                المدراء
                            </p>

                            <h3 class="text-[48px] font-bold text-[#ffb1c7]">
                                {{ $adminsCount }}
                            </h3>

                            <span class="mt-2 flex items-center gap-1 text-[11px] text-[#ffb1c7]">
                                المسؤولون التنفيذيون
                            </span>
                        </div>

                        <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-[#be0062]/20 text-[#ffb1c7]">
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21a8 8 0 0 1 16 0M17 4l2 2 3-3"/>
                            </svg>
                        </div>
                    </article>
                </section>

                {{-- الجدول --}}
                <section id="employeesTable" class="overflow-hidden employees-glass rounded-[24px]">
                    <div class="flex items-center justify-between border-b border-[#434655] bg-[#222a3d]/50 p-4">
                        <h4 class="text-2xl font-bold text-[#dae2fd]">
                            قائمة الكوادر
                        </h4>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                id="downloadEmployeesCsv"
                                class="flex items-center justify-center p-2 transition rounded-lg hover:bg-[#2d3449]"
                                title="تنزيل CSV"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>
                                </svg>
                            </button>

                            <button
                                type="button"
                                id="refreshEmployees"
                                class="flex items-center justify-center p-2 transition rounded-lg hover:bg-[#2d3449]"
                                title="تحديث"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M20 6v5h-5M4 18v-5h5"/>
                                    <path d="M18 9a7 7 0 0 0-12-2M6 15a7 7 0 0 0 12 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto employees-scroll">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="bg-[#131b2e]/50">
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">الموظف</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">البريد الإلكتروني</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">الهاتف</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">الحالة</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">المشاريع</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">تاريخ الانضمام</th>
                                    <th class="px-4 py-4 text-xs font-bold uppercase text-[#8d90a0]">الإجراءات</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-[#434655]/30">
                                @forelse ($employees as $employee)
                                    @php
                                        $employeeEditUrl = null;
                                        $employeeDeleteUrl = null;

                                        if (Route::has('employees.edit')) {
                                            $employeeEditUrl = route('employees.edit', $employee);
                                        } elseif (Route::has('users.edit')) {
                                            $employeeEditUrl = route('users.edit', $employee);
                                        }

                                        if (Route::has('employees.destroy')) {
                                            $employeeDeleteUrl = route('employees.destroy', $employee);
                                        } elseif (Route::has('users.destroy')) {
                                            $employeeDeleteUrl = route('users.destroy', $employee);
                                        }

                                        $employeeRoleLabel =
                                            $roleLabels[$employee->role]
                                            ?? $employee->role;

                                        $employeeSearchText = strtolower(
                                            ($employee->name ?? '') . ' ' .
                                            ($employee->email ?? '') . ' ' .
                                            ($employee->phone ?? '') . ' ' .
                                            ($employeeRoleLabel ?? '') . ' ' .
                                            (
                                                $employee
                                                    ->employeeProfile
                                                    ?->specialty
                                                    ?->name
                                                ?? ''
                                            )
                                        );
                                    @endphp

                                    <tr
                                        data-employee-row
                                        data-search="{{ $employeeSearchText }}"
                                        data-role="{{ $employee->role }}"
                                        data-status="{{ $employee->status }}"
                                        class="employee-row hover:bg-[#2d3449]/30"
                                    >
                                        <td class="px-4 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#b4c5ff] to-[#d2bbff] p-[2px]">
                                                    <div class="flex items-center justify-center w-full h-full overflow-hidden rounded-full bg-[#222a3d]">
                                                        @if ($employee->profile_photo)
                                                            <img
                                                                src="{{ asset('storage/' . $employee->profile_photo) }}"
                                                                alt="{{ $employee->name }}"
                                                                class="object-cover w-full h-full"
                                                            >
                                                        @else
                                                            <span class="font-black text-white">
                                                                {{ mb_substr($employee->name, 0, 1) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div>
                                                    @if (
                                                        $employee->role === 'engineer'
                                                        && Route::has('engineers.show')
                                                    )
                                                        <a
                                                            href="{{ route('engineers.show', $employee) }}"
                                                            class="font-bold text-[#dae2fd] transition hover:text-[#b4c5ff]"
                                                        >
                                                            {{ $employee->name }}
                                                        </a>
                                                    @else
                                                        <p class="font-bold text-[#dae2fd]">
                                                            {{ $employee->name }}
                                                        </p>
                                                    @endif

                                                    <p class="mt-1 text-[11px] text-[#c3c6d7]">
                                                        @if (
                                                            $employee->role === 'engineer'
                                                            && $employee->employeeProfile?->specialty
                                                        )
                                                            {{ $employee->employeeProfile->specialty->name }}
                                                        @else
                                                            {{ $employeeRoleLabel }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-5 text-sm text-[#c3c6d7]">
                                            {{ $employee->email }}
                                        </td>

                                        <td class="px-4 py-5 text-sm font-mono text-[#c3c6d7]">
                                            {{ $employee->phone ?: '—' }}
                                        </td>

                                        <td class="px-4 py-5">
                                            @if ($employee->status === 'active')
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-[#b4c5ff]/20 bg-[#b4c5ff]/10 px-3 py-1 text-[11px] text-[#b4c5ff]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#b4c5ff] animate-pulse"></span>
                                                    نشط
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-[11px] text-red-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                    غير نشط
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-5 text-sm text-[#c3c6d7]">
                                            @if ($employee->role === 'engineer')
                                                {{ $employee->engineerWorks->count() }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="px-4 py-5 text-sm text-[#c3c6d7]">
                                            {{ $employee->created_at?->format('Y-m-d') }}
                                        </td>

                                        <td class="px-4 py-5">
                                            <div class="flex gap-2">
                                                @if ($employeeEditUrl)
                                                    <a
                                                        href="{{ $employeeEditUrl }}"
                                                        class="flex items-center justify-center w-8 h-8 transition rounded-lg bg-[#2d3449] hover:bg-[#2563eb] hover:text-white"
                                                        title="تعديل"
                                                    >
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                            <path d="m14 4 6 6L8 22H2v-6L14 4Z"/>
                                                            <path d="m12 6 6 6"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if (
                                                    $employeeDeleteUrl
                                                    && $employee->id !== auth()->id()
                                                )
                                                    <form
                                                        method="POST"
                                                        action="{{ $employeeDeleteUrl }}"
                                                        data-delete-employee
                                                        data-employee-name="{{ $employee->name }}"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="flex items-center justify-center w-8 h-8 transition rounded-lg bg-[#2d3449] hover:bg-red-700 hover:text-white"
                                                            title="حذف"
                                                        >
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                                <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center text-[#c3c6d7]">
                                            لا يوجد موظفون حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between p-4 border-t border-[#434655]/30 bg-[#222a3d]/20">
                        <p id="employeesResultCount" class="text-[11px] text-[#c3c6d7]">
                            عرض {{ $totalEmployees }} من أصل {{ $totalEmployees }} موظفين
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput =
                document.getElementById('employeesSearch');

            const roleFilter =
                document.getElementById('employeeRoleFilter');

            const statusFilter =
                document.getElementById('employeeStatusFilter');

            const resetButton =
                document.getElementById('resetEmployeeFilters');

            const filterPanel =
                document.getElementById('employeeFilterPanel');

            const toggleFilterButton =
                document.getElementById('toggleEmployeeFilter');

            const rows =
                Array.from(
                    document.querySelectorAll(
                        '[data-employee-row]'
                    )
                );

            const resultCount =
                document.getElementById(
                    'employeesResultCount'
                );

            const applyFilters = () => {
                const query =
                    (searchInput?.value || '')
                        .trim()
                        .toLowerCase();

                const role =
                    roleFilter?.value || 'all';

                const status =
                    statusFilter?.value || 'all';

                let visibleCount = 0;

                rows.forEach((row) => {
                    const matchesSearch =
                        query === ''
                        || (
                            row.dataset.search || ''
                        ).includes(query);

                    const matchesRole =
                        role === 'all'
                        || row.dataset.role === role;

                    const matchesStatus =
                        status === 'all'
                        || row.dataset.status === status;

                    const visible =
                        matchesSearch
                        && matchesRole
                        && matchesStatus;

                    row.classList.toggle(
                        'hidden',
                        !visible
                    );

                    if (visible) {
                        visibleCount++;
                    }
                });

                if (resultCount) {
                    resultCount.textContent =
                        `عرض ${visibleCount} من أصل ${rows.length} موظفين`;
                }
            };

            searchInput?.addEventListener(
                'input',
                applyFilters
            );

            roleFilter?.addEventListener(
                'change',
                applyFilters
            );

            statusFilter?.addEventListener(
                'change',
                applyFilters
            );

            toggleFilterButton?.addEventListener(
                'click',
                function () {
                    filterPanel?.classList.toggle(
                        'hidden'
                    );
                }
            );

            resetButton?.addEventListener(
                'click',
                function () {
                    if (searchInput) {
                        searchInput.value = '';
                    }

                    if (roleFilter) {
                        roleFilter.value = 'all';
                    }

                    if (statusFilter) {
                        statusFilter.value = 'all';
                    }

                    applyFilters();
                }
            );

            document
                .querySelectorAll(
                    '[data-scroll-target]'
                )
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        function () {
                            document
                                .getElementById(
                                    button.dataset.scrollTarget
                                )
                                ?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start',
                                });
                        }
                    );
                });

            document
                .querySelectorAll(
                    '[data-delete-employee]'
                )
                .forEach((form) => {
                    form.addEventListener(
                        'submit',
                        function (event) {
                            const name =
                                form.dataset.employeeName
                                || 'هذا الموظف';

                            const confirmed =
                                window.confirm(
                                    `هل أنت متأكد من حذف ${name}؟`
                                );

                            if (!confirmed) {
                                event.preventDefault();
                                return;
                            }

                            const button =
                                form.querySelector(
                                    'button[type="submit"]'
                                );

                            if (button) {
                                button.disabled = true;
                                button.classList.add(
                                    'opacity-50',
                                    'cursor-not-allowed'
                                );
                            }
                        }
                    );
                });

            document
                .getElementById('refreshEmployees')
                ?.addEventListener(
                    'click',
                    function () {
                        window.location.reload();
                    }
                );

            document
                .getElementById(
                    'downloadEmployeesCsv'
                )
                ?.addEventListener(
                    'click',
                    function () {
                        const headers = [
                            'الموظف',
                            'البريد الإلكتروني',
                            'الهاتف',
                            'الحالة',
                            'الصلاحية',
                        ];

                        const visibleRows =
                            rows.filter(
                                (row) =>
                                    !row.classList.contains(
                                        'hidden'
                                    )
                            );

                        const lines = [
                            headers,
                            ...visibleRows.map((row) => {
                                const cells =
                                    row.querySelectorAll('td');

                                return [
                                    cells[0]?.innerText.trim()
                                        || '',
                                    cells[1]?.innerText.trim()
                                        || '',
                                    cells[2]?.innerText.trim()
                                        || '',
                                    cells[3]?.innerText.trim()
                                        || '',
                                    row.dataset.role || '',
                                ];
                            }),
                        ];

                        const csv =
                            lines
                                .map((line) =>
                                    line
                                        .map((value) =>
                                            `"${String(value)
                                                .replaceAll(
                                                    '"',
                                                    '""'
                                                )}"`
                                        )
                                        .join(',')
                                )
                                .join('\n');

                        const blob =
                            new Blob(
                                ['\ufeff' + csv],
                                {
                                    type:
                                        'text/csv;charset=utf-8;',
                                }
                            );

                        const url =
                            URL.createObjectURL(blob);

                        const link =
                            document.createElement('a');

                        link.href = url;
                        link.download =
                            'employees.csv';

                        document.body.appendChild(link);
                        link.click();
                        link.remove();

                        URL.revokeObjectURL(url);
                    }
                );
        });
    </script>
</x-app-layout>
