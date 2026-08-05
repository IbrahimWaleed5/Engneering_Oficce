<x-app-layout>
    @php
        $currentUser = auth()->user();

        $dashboardRoute = Route::has('dashboard')
            ? route('dashboard')
            : url('/dashboard');

        $usersRoute = Route::has('users.index')
            ? route('users.index')
            : url('/users');

        $consultationsRoute = Route::has('consultations.index')
            ? route('consultations.index')
            : url('/consultations');

        $officesRoute = Route::has('admin.offices.index')
            ? route('admin.offices.index')
            : '#';

        $applicationsRoute = Route::has('admin.office-applications.index')
            ? route('admin.office-applications.index')
            : '#';

        $subscriptionsRoute = Route::has('admin.office-subscriptions.index')
            ? route('admin.office-subscriptions.index')
            : '#';

        $profileRoute = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $notificationsRoute = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardRoute;
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        body.users-edit-menu-open {
            overflow: hidden;
        }

        .users-edit-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background:
                radial-gradient(circle at 12% 12%, rgba(37, 99, 235, .16), transparent 32%),
                radial-gradient(circle at 88% 10%, rgba(131, 67, 244, .12), transparent 30%),
                #0b1326;
            font-family: 'Be Vietnam Pro', 'Almarai', system-ui, sans-serif;
        }

        .users-edit-glass {
            background: rgba(34, 42, 61, .62);
            border: 1px solid rgba(255, 255, 255, .10);
            box-shadow: 0 22px 60px rgba(0, 0, 0, .30);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .users-edit-panel {
            background: rgba(45, 52, 73, .35);
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .users-edit-link {
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }

        .users-edit-link:hover {
            transform: translateX(-2px);
        }

        .users-edit-mobile-drawer {
            width: min(88vw, 390px);
        }

        .users-edit-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .users-edit-scroll::-webkit-scrollbar-track {
            background: rgba(11, 19, 38, .55);
        }

        .users-edit-scroll::-webkit-scrollbar-thumb {
            background: rgba(67, 70, 85, .70);
            border-radius: 999px;
        }

        .users-edit-input {
            width: 100%;
            border-radius: 0.9rem;
            border: 1px solid rgba(141, 144, 160, .38);
            background: rgba(6, 14, 32, .55);
            color: white;
            padding: .9rem 1rem;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .users-edit-input:focus {
            border-color: #b4c5ff;
            box-shadow: 0 0 0 3px rgba(180, 197, 255, .15);
            outline: none;
        }

        @media (max-width: 1023px) {
            .users-edit-desktop-sidebar,
            .users-edit-desktop-topbar {
                display: none !important;
            }

            .users-edit-main {
                margin-right: 0 !important;
                padding-top: 7rem !important;
            }
        }
    </style>

    <div
        class="users-edit-page"
        dir="rtl"
        x-data="{
            mobileMenuOpen: false,
            role: @js(old('role', $user->role)),
            jobTitle: @js(old('job_title', $user->employeeProfile?->job_title)),
            ownerAction: @js(old('office_owner_action', 'keep'))
        }"
        x-init="
            $watch('mobileMenuOpen', value => document.body.classList.toggle('users-edit-menu-open', value));

            $watch('jobTitle', value => {
                if (
                    value
                    && value.trim() !== ''
                    && role === 'customer'
                ) {
                    role = 'employee';
                }
            });
        "
        @keydown.escape.window="mobileMenuOpen = false"
    >
        {{-- شريط الجوال --}}
        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-14 h-14 text-white rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] shadow-lg active:scale-95"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>

                <div class="min-w-0 text-center">
                    <p class="truncate text-lg font-black text-[#b4c5ff]">صرح الهندسة</p>
                    <p class="truncate text-xs text-[#c3c6d7]">تعديل المستخدم</p>
                </div>

                <a
                    href="{{ $notificationsRoute }}"
                    class="flex items-center justify-center w-12 h-12 text-[#c3c6d7] border rounded-2xl border-white/10 bg-white/5"
                    aria-label="الإشعارات"
                >
                    🔔
                </a>
            </div>
        </header>

        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            @click="mobileMenuOpen = false"
            class="fixed inset-0 z-[80] bg-black/75 backdrop-blur-sm lg:hidden"
        ></div>

        {{-- قائمة الجوال --}}
        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="users-edit-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden"
        >
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <div>
                    <h2 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h2>
                    <p class="mt-1 text-sm text-[#c3c6d7]">قائمة إدارة النظام</p>
                </div>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-12 h-12 text-white border rounded-2xl border-white/10 bg-white/5"
                    aria-label="إغلاق القائمة"
                >
                    ✕
                </button>
            </div>

            <nav class="flex-1 p-5 space-y-3 overflow-y-auto users-edit-scroll">
                <a href="{{ $dashboardRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⌂ <span>لوحة التحكم</span></a>
                <a href="{{ $consultationsRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">📄 <span>الاستشارات</span></a>
                <a href="{{ $usersRoute }}" @click="mobileMenuOpen = false" class="flex items-center gap-4 rounded-2xl border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 px-5 py-4 font-black text-[#dbe1ff] shadow-lg shadow-blue-950/30">👥 <span>المستخدمون</span></a>

                @if ($officesRoute !== '#')
                    <a href="{{ $officesRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">🏢 <span>المكاتب الهندسية</span></a>
                @endif

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">📋 <span>طلبات إنشاء المكاتب</span></a>
                @endif

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">💳 <span>اشتراكات المكاتب</span></a>
                @endif

                <a href="{{ $profileRoute }}" @click="mobileMenuOpen = false" class="users-edit-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⚙ <span>الإعدادات</span></a>
            </nav>

            <div class="p-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#c3c6d7] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-5 py-4 font-black text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- Sidebar الكمبيوتر --}}
        <aside class="users-edit-desktop-sidebar fixed right-0 top-0 z-50 hidden h-screen w-72 flex-col border-l border-white/10 bg-[#131b2e]/90 p-5 shadow-2xl backdrop-blur-xl lg:flex">
            <div class="px-3 mb-8">
                <h1 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h1>
                <p class="mt-1 text-sm text-[#c3c6d7]/65">نظام الإدارة الفاخر</p>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto users-edit-scroll">
                <a href="{{ $dashboardRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاستشارات</a>
                <a href="{{ $usersRoute }}" class="block rounded-xl border-r-4 border-[#b4c5ff] bg-blue-600/20 px-4 py-3 text-sm font-black text-[#b4c5ff]">المستخدمون</a>

                @if ($officesRoute !== '#')
                    <a href="{{ $officesRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">المكاتب الهندسية</a>
                @endif

                @if ($applicationsRoute !== '#')
                    <a href="{{ $applicationsRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">طلبات إنشاء المكاتب</a>
                @endif

                @if ($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">اشتراكات المكاتب</a>
                @endif

                <a href="{{ $profileRoute }}" class="users-edit-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الإعدادات</a>
            </nav>

            <div class="pt-5 mt-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 text-xs text-[#8d90a0] break-all">{{ $currentUser->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 font-bold text-red-200 border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <header class="users-edit-desktop-topbar fixed top-0 left-0 right-72 z-40 hidden h-20 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-8 backdrop-blur-xl lg:flex">
            <div>
                <h2 class="text-xl font-black text-white">تعديل المستخدم</h2>
                <p class="mt-1 text-xs text-[#8d90a0]">تعديل البيانات والصلاحيات وملكية المكتب</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $notificationsRoute }}" class="flex items-center justify-center w-10 h-10 text-[#c3c6d7] border rounded-full border-white/10 bg-white/5">🔔</a>
                <div class="flex items-center justify-center w-10 h-10 font-black text-[#b4c5ff] border rounded-full border-white/10 bg-white/5">
                    {{ mb_substr($currentUser->name ?? 'م', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="min-h-screen px-4 users-edit-main pt-28 pb-14 sm:px-6 lg:mr-72 lg:px-8">
            <div class="max-w-6xl mx-auto">
                @if (session('success'))
                    <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="mb-8">
                    <p class="text-sm font-black text-[#b4c5ff]">إدارة المستخدمين</p>
                    <h1 class="mt-2 text-3xl font-black text-white sm:text-4xl">تعديل المستخدم</h1>
                    <p class="mt-3 text-[#c3c6d7]">
                        تعديل بيانات وصلاحيات {{ $user->name }}
                    </p>
                </section>

                <form
                    method="POST"
                    action="{{ route('users.update', $user) }}"
                    class="overflow-hidden users-edit-glass rounded-3xl"
                >
                    @csrf
                    @method('PATCH')

                    <div class="grid lg:grid-cols-[1fr_330px]">
                        {{-- القسم الرئيسي --}}
                        <div class="p-6 sm:p-8 lg:p-10 lg:border-l lg:border-white/10">
                            <div class="flex flex-col items-center mb-10 text-center">
                                <div class="relative mb-5">
                                    <div class="absolute rounded-full -inset-2 bg-gradient-to-br from-blue-600/50 to-violet-600/50 blur"></div>
                                    <div class="relative flex items-center justify-center w-28 h-28 text-4xl font-black text-[#b4c5ff] border-4 rounded-full border-[#0b1326] bg-[#2d3449]">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-4 rounded-full border-[#222a3d]"></span>
                                </div>

                                <h2 class="text-2xl font-black text-white">{{ $user->name }}</h2>
                                <p class="mt-2 text-sm text-[#c3c6d7]">{{ $user->email }}</p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block mb-2 text-sm font-black text-[#c3c6d7]">
                                        الاسم الكامل
                                    </label>
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="users-edit-input">
                                </div>

                                <div>
                                    <label for="email" class="block mb-2 text-sm font-black text-[#c3c6d7]">
                                        البريد الإلكتروني
                                    </label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="users-edit-input">
                                </div>

                                <div>
                                    <label for="phone" class="block mb-2 text-sm font-black text-[#c3c6d7]">
                                        رقم الهاتف
                                    </label>
                                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="users-edit-input">
                                </div>

                                <section
                                    x-show="role === 'engineer' || role === 'employee'"
                                    x-cloak
                                    class="p-5 border rounded-2xl border-cyan-500/20 bg-cyan-500/5"
                                >
                                    <h3 class="mb-5 text-lg font-black text-white">بيانات الوظيفة</h3>

                                    <div class="grid gap-5 md:grid-cols-3">
                                        <div>
                                            <label for="job_title" class="block mb-2 text-sm font-bold text-slate-200">المسمى الوظيفي</label>
                                            <input
                                                id="job_title"
                                                name="job_title"
                                                type="text"
                                                value="{{ old('job_title', $user->employeeProfile?->job_title) }}"
                                                x-model="jobTitle"
                                                placeholder="مثال: دعم فني"
                                                class="users-edit-input"
                                            >

                                            <p class="mt-2 text-[11px] leading-6 text-cyan-200/80">
                                                عند كتابة مسمى وظيفي لمستخدم دوره عميل، سيتم تحويل دوره تلقائيًا إلى موظف.
                                            </p>
                                        </div>

                                        <div>
                                            <label for="salary" class="block mb-2 text-sm font-bold text-slate-200">الراتب</label>
                                            <input id="salary" name="salary" type="number" min="0" step="0.01" value="{{ old('salary', $user->employeeProfile?->salary ?? 0) }}" class="users-edit-input">
                                        </div>

                                        <div>
                                            <label for="hire_date" class="block mb-2 text-sm font-bold text-slate-200">تاريخ التعيين</label>
                                            <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date', $user->employeeProfile?->hire_date?->format('Y-m-d')) }}" class="users-edit-input">
                                        </div>
                                    </div>
                                </section>

                                <section class="p-6 border rounded-3xl border-violet-500/20 bg-violet-500/5">
                                    <div class="flex flex-col gap-3 mb-6 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h3 class="text-xl font-black text-white">ملكية المكتب الهندسي</h3>
                                            <p class="mt-2 text-sm leading-7 text-slate-300">
                                                مدير النظام فقط يستطيع تعيين أو نقل أو إزالة مالك المكتب.
                                            </p>
                                        </div>

                                        @if ($user->ownedOffice)
                                            <span class="inline-flex px-4 py-2 text-sm font-black text-green-200 border rounded-full border-green-500/20 bg-green-500/10">
                                                مالك حالي
                                            </span>
                                        @endif
                                    </div>

                                    @if ($user->ownedOffice)
                                        <div class="p-4 mb-5 border rounded-2xl border-white/10 bg-white/5">
                                            <p class="text-xs text-slate-400">المكتب الذي يملكه حاليًا</p>
                                            <p class="mt-2 text-lg font-black text-white">{{ $user->ownedOffice->name }}</p>
                                            <p class="mt-1 text-sm text-slate-400">
                                                {{ $user->ownedOffice->city ?: 'مدينة غير محددة' }}
                                                @if ($user->ownedOffice->country)
                                                    — {{ $user->ownedOffice->country }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif

                                    <div class="grid gap-5 md:grid-cols-2">
                                        <div>
                                            <label for="office_owner_action" class="block mb-2 text-sm font-bold text-white">الإجراء</label>

                                            <select
                                                id="office_owner_action"
                                                name="office_owner_action"
                                                class="users-edit-input"
                                                x-model="ownerAction"
                                            >
                                                <option value="keep">بدون تغيير</option>
                                                <option value="assign">تعيين أو نقل ملكية مكتب</option>

                                                @if ($user->ownedOffice)
                                                    <option value="remove">إزالة ملكية المكتب</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div x-show="ownerAction === 'assign'" x-cloak>
                                            <label for="office_id" class="block mb-2 text-sm font-bold text-white">المكتب الهندسي</label>

                                            <select
                                                id="office_id"
                                                name="office_id"
                                                class="users-edit-input"
                                                :required="ownerAction === 'assign'"
                                            >
                                                <option value="">اختر المكتب...</option>

                                                @foreach ($offices as $office)
                                                    <option
                                                        value="{{ $office->id }}"
                                                        @selected((string) old('office_id', $user->ownedOffice?->id) === (string) $office->id)
                                                    >
                                                        {{ $office->name }}
                                                        — {{ $office->city ?: 'مدينة غير محددة' }}

                                                        @if ($office->owner)
                                                            — المالك الحالي: {{ $office->owner->name }}
                                                        @else
                                                            — بدون مالك
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="p-4 mt-5 border-r-4 rounded-xl border-amber-400/70 bg-amber-500/10">
                                        <p class="font-black text-amber-200">تنبيه</p>
                                        <p class="mt-2 text-sm leading-7 text-amber-100">
                                            عند اختيار مكتب له مالك حالي، سيتم نقل الملكية إلى هذا المستخدم وتعطيل عضوية المالك السابق.
                                        </p>
                                    </div>
                                </section>
                            </div>
                        </div>

                        {{-- لوحة الصلاحيات --}}
                        <aside class="p-6 sm:p-8 bg-[#2d3449]/25">
                            <div>
                                <h3 class="flex items-center gap-2 text-xl font-black text-white">
                                    <span>🛡️</span>
                                    صلاحيات الحساب
                                </h3>

                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="role" class="block mb-2 text-sm font-black text-[#c3c6d7]">الدور</label>

                                        <select
                                            id="role"
                                            name="role"
                                            required
                                            class="users-edit-input"
                                            x-model="role"
                                        >
                                            <option value="admin">مدير النظام</option>
                                            <option value="office_owner">مالك مكتب</option>
                                            <option value="engineer">مهندس</option>
                                            <option value="employee">موظف</option>
                                            <option value="customer">عميل</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="status" class="block mb-2 text-sm font-black text-[#c3c6d7]">حالة الحساب</label>

                                        <select
                                            id="status"
                                            name="status"
                                            required
                                            class="users-edit-input"
                                        >
                                            <option value="active" @selected(old('status', $user->status) === 'active')>نشط</option>
                                            <option value="inactive" @selected(old('status', $user->status) === 'inactive')>غير نشط</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 border-t pt-7 border-white/10">
                                <h3 class="text-lg font-black text-white">ملخص المستخدم</h3>

                                <div class="mt-5 space-y-4">
                                    <div class="p-4 rounded-2xl users-edit-panel">
                                        <p class="text-xs text-[#8d90a0]">الدور الحالي</p>
                                        <p
                                            class="mt-2 font-black text-white"
                                            x-text="{
                                                admin: 'مدير النظام',
                                                office_owner: 'مالك مكتب',
                                                engineer: 'مهندس',
                                                employee: 'موظف',
                                                customer: 'عميل'
                                            }[role] ?? role"
                                        ></p>
                                    </div>

                                    <div class="p-4 rounded-2xl users-edit-panel">
                                        <p class="text-xs text-[#8d90a0]">حالة الحساب</p>
                                        <p class="mt-2 font-black {{ $user->status === 'active' ? 'text-green-300' : 'text-red-300' }}">
                                            {{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-2xl users-edit-panel">
                                        <p class="text-xs text-[#8d90a0]">المكتب المملوك</p>
                                        <p class="mt-2 font-black text-white">
                                            {{ $user->ownedOffice?->name ?? 'لا يملك مكتبًا' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 p-6 border-t border-white/10 sm:p-8">
                        <a
                            href="{{ $usersRoute }}"
                            class="px-6 py-3 font-black transition border rounded-xl border-white/10 text-[#c3c6d7] hover:bg-white/5 hover:text-white"
                        >
                            إلغاء
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-7 py-3 font-black text-white transition rounded-xl bg-gradient-to-l from-[#2563eb] to-[#8343f4] shadow-lg shadow-blue-950/30 hover:brightness-110"
                            onclick="return confirm('هل أنت متأكد من حفظ التعديلات وتحديث ملكية المكتب؟')"
                        >
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>
