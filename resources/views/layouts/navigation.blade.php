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
                        class="flex items-center justify-center w-10 h-10 text-xl font-bold text-white rounded-lg bg-gradient-to-br from-blue-600 to-cyan-500"
                    >
                        م
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

                    <x-dropdown align="left" width="48">

                        <x-slot name="trigger">

                            <button
                                class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700"
                            >
                                @if(auth()->user()->profile_photo)

                                <img
                                    src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="object-cover w-10 h-10 border-2 rounded-full border-slate-600"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-10 h-10 font-bold text-white bg-blue-600 rounded-full"
                                >
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>

                            @endif

                                <div class="text-right">

                                    <p class="text-sm font-medium text-white">
                                        {{ auth()->user()->name }}
                                    </p>

                                   <p class="text-xs text-slate-400">

    @if (
        auth()->user()->role === 'engineer'
        && auth()->user()->employeeProfile?->specialty
    )

        مهندس
        {{ auth()->user()->employeeProfile->specialty->name }}

    @elseif (auth()->user()->role === 'admin')

        مدير النظام

    @elseif (auth()->user()->role === 'customer')

        عميل

    @else

        {{ auth()->user()->role }}

    @endif

</p>

                                </div>

                                <span class="text-slate-400">
                                    ▾
                                </span>

                            </button>

                        </x-slot>

                        <x-slot name="content">

                            <x-dropdown-link
                                :href="route('profile.edit')"
                            >
                                الملف الشخصي
                            </x-dropdown-link>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >
                                @csrf

                                <x-dropdown-link
                                    :href="route('logout')"
                                    onclick="event.preventDefault();
                                        this.closest('form').submit();"
                                >
                                    تسجيل الخروج
                                </x-dropdown-link>
                            </form>

                        </x-slot>

                    </x-dropdown>

                @endauth

            </div>

            <div class="flex items-center md:hidden">

                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800"
                >
                    <svg
                        class="w-6 h-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>

        </div>

    </div>

    <div
        :class="{'block': open, 'hidden': ! open}"
        class="hidden border-t border-slate-700 md:hidden"
    >
        <div class="px-4 pt-2 pb-3 space-y-1">

            <a
                href="{{ route('dashboard') }}"
                class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
            >
                لوحة التحكم
            </a>

            <a
                href="{{ route('engineer.works.public') }}"
                class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
            >
                مكتبة المهندسين
            </a>

            @auth

                @if (auth()->user()->role === 'customer')
                    <a
                        href="{{ route('consultations.mine') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        استشاراتي
                    </a>
                @endif

                @if (auth()->user()->role === 'engineer')

                    <a
                        href="{{ route('engineer.consultations') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        استشارات المهندس
                    </a>

                    <a
                        href="{{ route('engineer.works.mine') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        أعمالي
                    </a>
                    @if (auth()->user()->role === 'engineer')

    <a
        href="{{ route('engineers.show', auth()->user()) }}"
        class="nav-link"
    >
        ملفك الشخصي
    </a>

@endif
                    <a
                        href="{{ route('engineer.specialty.edit') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        اختيار التخصص الهندسي
                    </a>
                @endif

                @if (auth()->user()->role === 'admin')

                    <a
                        href="{{ route('consultations.index') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        جميع الاستشارات
                    </a>

                    <a
                        href="{{ route('payments.index') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        الدفعات
                    </a>

                    <a
                        href="{{ route('employees.index') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        الموظفون
                    </a>

                    <a
                       href="{{ route('engineer.works.public') }}"
                        class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                    >
                        مراجعة أعمال المهندسين
                    </a>

                @endif

                <a
                    href="{{ route('notifications.index') }}"
                    class="block px-3 py-2 text-white rounded-lg hover:bg-slate-800"
                >
                    الإشعارات
                </a>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="block w-full px-3 py-2 text-right text-red-400 rounded-lg hover:bg-slate-800"
                    >
                        تسجيل الخروج
                    </button>
                </form>

            @endauth

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
