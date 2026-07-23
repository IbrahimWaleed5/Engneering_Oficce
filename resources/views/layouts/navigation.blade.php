<nav
    x-data="{ open: false }"
    class="border-b border-slate-700 bg-slate-900"
    dir="rtl"
>
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            {{-- ===== الشعار + روابط الديسكتوب ===== --}}
            <div class="flex items-center gap-8">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3"
                >
                    <div class="flex items-center justify-center w-12 h-12 overflow-hidden border rounded-xl border-cyan-500/30 bg-slate-800">
                        <img
                            src="{{ asset('images/Mainlogo.png') }}"
                            alt="شعار مكتب الوليد الهندسي"
                            class="object-contain w-full h-full p-1"
                        >
                    </div>

                    <div class="hidden sm:block">
                        <p class="font-bold text-white">
                            مكتب الوليد الهندسي
                        </p>
                        <p class="text-xs text-slate-400">
                            إدارة الاستشارات
                        </p>
                    </div>
                </a>

                {{-- روابط الديسكتوب --}}
                <div class="items-center hidden gap-2 md:flex">

                    <a
                        href="{{ route('dashboard') }}"
                        class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        لوحة التحكم
                    </a>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('engineer.works.public') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        مكتبة المهندسين
                    </a>

                    @auth

                        {{-- ملفي الشخصي - للجميع --}}
                        <a
                            href="{{ route('profile.edit') }}"
                            class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            ملفي الشخصي
                        </a>

                        {{-- روابط العميل --}}
                        @if (auth()->user()->role === 'customer')
                            <a
                                href="{{ route('consultations.mine') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('consultations.mine') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                استشاراتي
                            </a>
                        @endif

                        {{-- روابط المهندس --}}
                        @if (auth()->user()->role === 'engineer')
                            <a
                                href="{{ route('engineer.consultations') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('engineer.consultations') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                طلباتي
                            </a>

                            <a
                                href="{{ route('engineer.works.mine') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('engineer.works.mine') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                أعمالي
                            </a>

                            <a
                                href="{{ route('engineer.specialty.edit') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('engineer.specialty.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                تخصصي
                            </a>
                        @endif

                        {{-- روابط الأدمن --}}
                        @if (auth()->user()->role === 'admin')
                            <a
                                href="{{ route('consultations.index') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('consultations.index') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                الاستشارات
                            </a>

                            <a
                                href="{{ route('payments.index') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                الدفعات
                            </a>

                            <a
                                href="{{ route('employees.index') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('employees.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                الموظفون
                            </a>

                            <a
                                href="{{ route('users.index') }}"
                                class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                إدارة المستخدمين
                            </a>
                        @endif

                    @endauth

                </div>

            </div>

            {{-- ===== الجانب الأيمن: إشعارات + بطاقات + قائمة المستخدم ===== --}}
            <div class="items-center hidden gap-3 md:flex">

                @auth

                    {{-- زر الإشعارات --}}
                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative flex items-center justify-center w-10 h-10 rounded-lg bg-slate-800 text-slate-300 hover:text-white"
                        title="الإشعارات"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        @if (auth()->user()->unreadNotifications()->count() > 0)
                            <span class="absolute flex items-center justify-center w-5 h-5 text-xs text-white bg-red-600 rounded-full -top-1 -left-1">
                                {{ auth()->user()->unreadNotifications()->count() }}
                            </span>
                        @endif
                    </a>

                    <div class="flex items-center gap-3">

                        {{-- بطاقة التخصص - للمهندس فقط --}}
                        @if (auth()->user()->role === 'engineer' && auth()->user()->employeeProfile?->specialty)
                            <a
                                href="{{ route('engineer.specialty.edit') }}"
                                class="items-center hidden gap-3 px-4 py-2 transition border lg:flex rounded-2xl border-cyan-400/20 bg-cyan-500/10 hover:bg-cyan-500/20"
                            >
                                <span class="flex items-center justify-center w-10 h-10 text-sm font-bold text-cyan-300 rounded-xl bg-cyan-500/20">
                                    HND
                                </span>

                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400">
                                        التخصص الهندسي
                                    </p>
                                    <p class="mt-1 text-sm font-black text-cyan-300">
                                        {{ auth()->user()->employeeProfile->specialty->name }}
                                    </p>
                                </div>
                            </a>
                        @endif

                        {{-- بطاقة نوع الحساب --}}
                        @php
                            $accountType = match (auth()->user()->role) {
                                'admin'    => ['label' => 'مدير النظام',  'badge' => 'ADM', 'class' => 'text-emerald-200 border-emerald-400/20 bg-emerald-500/10'],
                                'engineer' => ['label' => 'حساب مهندس',  'badge' => 'ENG', 'class' => 'text-violet-200 border-violet-400/20 bg-violet-500/10'],
                                'employee' => ['label' => 'حساب موظف',   'badge' => 'EMP', 'class' => 'text-amber-200 border-amber-400/20 bg-amber-500/10'],
                                default    => ['label' => 'حساب عميل',   'badge' => 'USR', 'class' => 'text-blue-200 border-blue-400/20 bg-blue-500/10'],
                            };
                        @endphp

                        <div class="hidden xl:flex items-center gap-3 px-4 py-2 border rounded-2xl {{ $accountType['class'] }}">
                            <span class="flex items-center justify-center w-8 h-8 text-xs font-black rounded-lg bg-white/10">
                                {{ $accountType['badge'] }}
                            </span>
                            <div class="text-right">
                                <p class="text-[10px] font-bold opacity-70">نوع الحساب</p>
                                <p class="mt-1 text-sm font-black">{{ $accountType['label'] }}</p>
                            </div>
                        </div>

                        {{-- قائمة إعدادات الحساب --}}
                        <x-dropdown align="left" width="56">

                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="flex items-center gap-3 px-3 py-2 transition border rounded-2xl border-white/10 bg-slate-800/80 hover:bg-slate-700 hover:border-cyan-400/30"
                                >
                                    <div class="relative">
                                        @if (auth()->user()->profile_photo)
                                            <img
                                                src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                                alt="{{ auth()->user()->name }}"
                                                class="object-cover border-2 rounded-full w-11 h-11 border-cyan-500"
                                            >
                                        @else
                                            <img
                                                src="{{ asset('images/Mainlogo.png') }}"
                                                alt="{{ auth()->user()->name }}"
                                                class="object-contain p-1 border-2 rounded-full w-11 h-11 border-cyan-500 bg-slate-900"
                                            >
                                        @endif
                                        <span class="absolute bottom-0 left-0 w-3 h-3 bg-green-400 border-2 rounded-full border-slate-800"></span>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-black text-white">
                                            {{ auth()->user()->name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            إعدادات الحساب
                                        </p>
                                    </div>

                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                {{-- رأس القائمة --}}
                                <div class="px-4 py-3 border-b border-slate-700">
                                    <p class="text-sm font-black text-white">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ auth()->user()->email }}
                                    </p>
                                    @if (auth()->user()->phone)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ auth()->user()->phone }}
                                        </p>
                                    @endif
                                </div>

                                {{-- روابط للجميع --}}
                                <x-dropdown-link :href="route('profile.edit')">
                                    الملف الشخصي
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('notifications.index')">
                                    الإشعارات
                                    @if (auth()->user()->unreadNotifications()->count() > 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs text-white bg-red-600 rounded-full">
                                            {{ auth()->user()->unreadNotifications()->count() }}
                                        </span>
                                    @endif
                                </x-dropdown-link>

                                {{-- روابط المهندس --}}
                                @if (auth()->user()->role === 'engineer')
                                    <x-dropdown-link :href="route('engineer.consultations')">
                                        طلباتي
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('engineer.works.mine')">
                                        أعمالي
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('engineers.show', auth()->user())">
                                        صفحتي العامة
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('engineer.specialty.edit')">
                                        تخصصي الهندسي
                                    </x-dropdown-link>
                                @endif

                                {{-- روابط العميل --}}
                                @if (auth()->user()->role === 'customer')
                                    <x-dropdown-link :href="route('consultations.mine')">
                                        استشاراتي
                                    </x-dropdown-link>
                                @endif

                                {{-- روابط الأدمن --}}
                                @if (auth()->user()->role === 'admin')
                                    <x-dropdown-link :href="route('consultations.index')">
                                        جميع الاستشارات
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('payments.index')">
                                        الدفعات
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('employees.index')">
                                        الموظفون
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('users.index')">
                                        إدارة المستخدمين
                                    </x-dropdown-link>
                                @endif

                                {{-- تسجيل الخروج --}}
                                <div class="border-t border-slate-700">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link
                                            :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="text-red-400 hover:text-red-300"
                                        >
                                            تسجيل الخروج
                                        </x-dropdown-link>
                                    </form>
                                </div>

                            </x-slot>

                        </x-dropdown>

                    </div>

                @endauth

            </div>

            {{-- ===== زر الهامبرغر - موبايل فقط ===== --}}
            <details class="relative flex items-center md:hidden">

                <summary
                    class="inline-flex items-center justify-center p-3 list-none transition cursor-pointer rounded-xl text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </summary>

                {{-- قائمة الموبايل --}}
                <div
                    class="fixed right-0 z-50 w-full mt-2 overflow-y-auto border-t shadow-2xl top-16 max-h-[calc(100vh-4rem)] border-slate-700 bg-slate-900"
                    dir="rtl"
                >
                    <div class="px-4 py-5 space-y-1">

                        {{-- معلومات المستخدم في الأعلى عند تسجيل الدخول --}}
                        @auth
                            <div class="flex items-center gap-3 px-4 py-3 mb-3 border rounded-xl border-slate-700 bg-slate-800">
                                @if (auth()->user()->profile_photo)
                                    <img
                                        src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                        alt="{{ auth()->user()->name }}"
                                        class="object-cover w-12 h-12 border-2 rounded-full border-cyan-500"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/Mainlogo.png') }}"
                                        alt="{{ auth()->user()->name }}"
                                        class="object-contain w-12 h-12 p-1 border-2 rounded-full border-cyan-500 bg-slate-900"
                                    >
                                @endif
                                <div>
                                    <p class="font-black text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                    @if (auth()->user()->phone)
                                        <p class="text-xs text-slate-500">{{ auth()->user()->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        @endauth

                        {{-- روابط عامة --}}
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                            لوحة التحكم
                        </a>

                        <a href="{{ route('engineer.works.public') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                            مكتبة المهندسين
                        </a>

                        @auth

                            {{-- ملف شخصي وإشعارات للجميع --}}
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                ملفي الشخصي
                            </a>

                            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                <span>الإشعارات</span>
                                @if (auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="flex items-center justify-center w-6 h-6 text-xs text-white bg-red-600 rounded-full">
                                        {{ auth()->user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </a>

                            {{-- روابط العميل --}}
                            @if (auth()->user()->role === 'customer')
                                <a href="{{ route('consultations.mine') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    استشاراتي
                                </a>
                            @endif

                            {{-- روابط المهندس --}}
                            @if (auth()->user()->role === 'engineer')
                                <a href="{{ route('engineer.consultations') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    استشارات المهندس
                                </a>

                                <a href="{{ route('engineer.works.mine') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    أعمالي
                                </a>

                                <a href="{{ route('engineers.show', auth()->user()) }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    صفحتي العامة
                                </a>

                                <a href="{{ route('engineer.specialty.edit') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    تخصصي الهندسي
                                </a>
                            @endif

                            {{-- روابط الأدمن --}}
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('consultations.index') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    جميع الاستشارات
                                </a>

                                <a href="{{ route('payments.index') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    الدفعات
                                </a>

                                <a href="{{ route('employees.index') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    الموظفون
                                </a>

                                <a href="{{ route('users.index') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                    إدارة المستخدمين
                                </a>
                            @endif

                            {{-- فاصل + تسجيل الخروج --}}
                            <div class="h-px my-3 bg-slate-700"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="block w-full px-4 py-3 font-bold text-right text-red-300 transition rounded-xl hover:bg-red-500/10"
                                >
                                    تسجيل الخروج
                                </button>
                            </form>

                        @else

                            {{-- غير مسجل الدخول --}}
                            <div class="h-px my-2 bg-slate-700"></div>

                            <a href="{{ route('login') }}" class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800">
                                تسجيل الدخول
                            </a>

                            <a href="{{ route('register') }}" class="block px-4 py-3 font-bold text-white transition bg-blue-600 rounded-xl hover:bg-blue-500">
                                إنشاء حساب
                            </a>

                        @endauth

                    </div>
                </div>

            </details>

        </div>

    </div>

</nav>
