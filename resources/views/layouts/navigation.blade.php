<nav
    x-data="{ open: false }"
    class="border-b border-slate-700 bg-slate-900"
    dir="rtl"
>
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="flex justify-between h-16">

            <div class="flex items-center gap-8">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3"
                >
                   <div
    class="flex items-center justify-center w-12 h-12 overflow-hidden border rounded-xl border-cyan-500/30 bg-slate-800"
>
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

                <div class="items-center hidden gap-2 md:flex">

                    <a
                        href="{{ route('dashboard') }}"
                        class="px-3 py-2 text-sm rounded-lg
                        {{ request()->routeIs('dashboard')
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        لوحة التحكم
                    </a>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="px-3 py-2 text-sm rounded-lg
                        {{ request()->routeIs('engineer.works.public')
                            ? 'bg-blue-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    >
                        مكتبة المهندسين
                    </a>

                    @auth

                        @if (auth()->user()->role === 'customer')

                            <a
                                href="{{ route('consultations.mine') }}"
                                class="px-3 py-2 text-sm rounded-lg
                                {{ request()->routeIs('consultations.mine')
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                استشاراتي
                            </a>

                        @endif
                        <a
    href="{{ route('profile.edit') }}"
    class="px-3 py-2 text-sm rounded-lg
    {{ request()->routeIs('profile.edit')
        ? 'bg-blue-600 text-white'
        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
>
    ملفي الشخصي
</a>

                        @if (auth()->user()->role === 'engineer')

                            <a
                                href="{{ route('engineer.consultations') }}"
                                class="px-3 py-2 text-sm rounded-lg"
                            >
                                طلباتي
                            </a>

                            <a
                                href="{{ route('engineer.works.mine') }}"
                                class="px-3 py-2 text-sm rounded-lg"
                            >
                                أعمالي
                            </a>

                            <a
                                href="{{ route('engineer.specialty.edit') }}"
                                class="px-3 py-2 text-sm rounded-lg
                                {{ request()->routeIs('engineer.specialty.*')
                                    ? 'bg-blue-600 text-white'
                                    : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                            >
                                تخصصي
                            </a>

                        @endif

                        @if (auth()->user()->role === 'admin')

                            <a
                                href="{{ route('consultations.index') }}"
                                class="px-3 py-2 text-sm rounded-lg"
                            >
                                الاستشارات
                            </a>

                            <a
                                href="{{ route('payments.index') }}"
                                class="px-3 py-2 text-sm rounded-lg"
                            >
                                الدفعات
                            </a>

                        @endif

                    @endauth

                </div>

            </div>

            <div class="items-center hidden gap-3 md:flex">

                @auth

                    <a
                        href="{{ route('notifications.index') }}"
                        class="relative flex items-center justify-center w-10 h-10 rounded-lg bg-slate-800 text-slate-300 hover:text-white"
                        title="الإشعارات"
                    >
                        <span class="text-xl">🔔</span>

                        @if (auth()->user()->unreadNotifications()->count() > 0)

                            <span
                                class="absolute flex items-center justify-center w-5 h-5 text-xs text-white bg-red-600 rounded-full -top-1 -left-1"
                            >
                                {{ auth()->user()->unreadNotifications()->count() }}
                            </span>

                        @endif
                    </a>

                    <div class="flex items-center gap-3">

                        {{-- بطاقة التخصص --}}
                        @if (
                            auth()->user()->role === 'engineer'
                            && auth()->user()->employeeProfile?->specialty
                        )
                            <a
                                href="{{ route('engineer.specialty.edit') }}"
                                class="items-center hidden gap-3 px-4 py-2 transition border lg:flex rounded-2xl border-cyan-400/20 bg-cyan-500/10 hover:bg-cyan-500/20"
                            >
                                <span class="flex items-center justify-center w-10 h-10 text-xl rounded-xl bg-cyan-500/10">
                                    🏗️
                                </span>

                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-slate-400">
                                        التخصص الهندسي
                                    </p>

                                    <p class="mt-1 text-sm font-black text-cyan-300">
                                        {{ auth()->user()->employeeProfile?->specialty?->name }}
                                    </p>
                                </div>
                            </a>
                        @endif

                        {{-- بطاقة نوع الحساب --}}
                        @php
                            $accountType = match (auth()->user()->role) {
                                'admin' => [
                                    'label' => 'مدير النظام',
                                    'icon' => '🛡️',
                                    'class' => 'text-emerald-200 border-emerald-400/20 bg-emerald-500/10',
                                ],
                                'engineer' => [
                                    'label' => 'حساب مهندس',
                                    'icon' => '👷',
                                    'class' => 'text-violet-200 border-violet-400/20 bg-violet-500/10',
                                ],
                                'employee' => [
                                    'label' => 'حساب موظف',
                                    'icon' => '🧑‍💼',
                                    'class' => 'text-amber-200 border-amber-400/20 bg-amber-500/10',
                                ],
                                default => [
                                    'label' => 'حساب عميل',
                                    'icon' => '👤',
                                    'class' => 'text-blue-200 border-blue-400/20 bg-blue-500/10',
                                ],
                            };
                        @endphp

                        <div
                            class="hidden xl:flex items-center gap-3 px-4 py-2 border rounded-2xl {{ $accountType['class'] }}"
                        >
                            <span class="text-xl">
                                {{ $accountType['icon'] }}
                            </span>

                            <div class="text-right">
                                <p class="text-[10px] font-bold opacity-70">
                                    نوع الحساب
                                </p>

                                <p class="mt-1 text-sm font-black">
                                    {{ $accountType['label'] }}
                                </p>
                            </div>
                        </div>

                        {{-- إعدادات الحساب --}}
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

                                        <span
                                            class="absolute bottom-0 left-0 w-3 h-3 bg-green-400 border-2 rounded-full border-slate-800"
                                        ></span>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-black text-white">
                                            {{ auth()->user()->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            إعدادات الحساب
                                        </p>
                                    </div>

                                    <span class="text-xs text-slate-400">
                                        ▼
                                    </span>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <div class="px-4 py-3 border-b border-slate-700">
                                    <p class="text-sm font-black text-white">
                                        {{ auth()->user()->name }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ auth()->user()->email }}
                                    </p>
                                </div>

                                <x-dropdown-link :href="route('profile.edit')">
                                    👤 تعديل الملف الشخصي
                                </x-dropdown-link>

                                @if (auth()->user()->role === 'engineer')
                                    <x-dropdown-link :href="route('engineer.specialty.edit')">
                                        🎓 تعديل التخصص
                                    </x-dropdown-link>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >
                                    @csrf

                                    <x-dropdown-link
                                        :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                    >
                                        🚪 تسجيل الخروج
                                    </x-dropdown-link>
                                </form>

                            </x-slot>

                        </x-dropdown>

                    </div>

                @endauth

            </div>

            <details class="relative md:hidden">

    <summary
        class="inline-flex items-center justify-center p-3 list-none transition cursor-pointer rounded-xl text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white"
        aria-label="فتح القائمة"
    >
        <svg
            class="w-7 h-7"
            stroke="currentColor"
            fill="none"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
            />
        </svg>
    </summary>

    <div
        class="fixed right-0 z-50 w-full mt-4 overflow-y-auto border-t shadow-2xl top-16 max-h-[calc(100vh-4rem)] border-slate-700 bg-slate-900"
        dir="rtl"
    >

        <div class="px-4 py-5 space-y-2">

            <a
                href="{{ route('dashboard') }}"
                class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
            >
                🏠 لوحة التحكم
            </a>

            <a
                href="{{ route('engineer.works.public') }}"
                class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
            >
                👷 مكتبة المهندسين
            </a>

            @auth

                <a
                    href="{{ route('profile.edit') }}"
                    class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                >
                    👤 ملفي الشخصي
                </a>

                @if (auth()->user()->role === 'customer')

                    <a
                        href="{{ route('consultations.mine') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        📋 استشاراتي
                    </a>

                @endif

                @if (auth()->user()->role === 'engineer')

                    <a
                        href="{{ route('engineer.consultations') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        📐 استشارات المهندس
                    </a>

                    <a
                        href="{{ route('engineer.works.mine') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        🏗️ أعمالي
                    </a>

                    <a
                        href="{{ route('engineers.show', auth()->user()) }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        ⭐ صفحتي العامة
                    </a>

                    <a
                        href="{{ route('engineer.specialty.edit') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        🎓 تخصصي الهندسي
                    </a>

                @endif

                @if (auth()->user()->role === 'admin')

                    <a
                        href="{{ route('consultations.index') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        📋 جميع الاستشارات
                    </a>

                    <a
                        href="{{ route('payments.index') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        💳 الدفعات
                    </a>

                    <a
                        href="{{ route('employees.index') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        👥 الموظفون
                    </a>

                    <a
                        href="{{ route('users.index') }}"
                        class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                    >
                        ⚙️ إدارة المستخدمين
                    </a>

                @endif

                <a
                    href="{{ route('notifications.index') }}"
                    class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                >
                    🔔 الإشعارات
                </a>

                <div class="h-px my-3 bg-slate-700"></div>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="block w-full px-4 py-3 font-bold text-right text-red-300 transition rounded-xl hover:bg-red-500/10"
                    >
                        🚪 تسجيل الخروج
                    </button>

                </form>

            @else

                <a
                    href="{{ route('login') }}"
                    class="block px-4 py-3 font-bold text-white transition rounded-xl hover:bg-slate-800"
                >
                    تسجيل الدخول
                </a>

                <a
                    href="{{ route('register') }}"
                    class="block px-4 py-3 font-bold text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                >
                    إنشاء حساب
                </a>

            @endauth

        </div>

    </div>

</details>

        </div>

    </div>
@auth

    @if (auth()->user()->role === 'admin')

        <x-nav-link
            :href="route('users.index')"
            :active="request()->routeIs('users.*')"
        >
            إدارة المستخدمين
        </x-nav-link>

    @endif

@endauth
</nav>
