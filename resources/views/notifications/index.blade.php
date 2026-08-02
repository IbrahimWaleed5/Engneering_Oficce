<x-app-layout>
    @php
        $currentUser = auth()->user();

        $totalNotifications = $notifications->count();
        $unreadNotifications = $currentUser
            ->unreadNotifications()
            ->count();

        $readNotifications = max(
            0,
            $totalNotifications - $unreadNotifications
        );
    @endphp

    <style>
        .notifications-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
            background: #0b1326;
            font-family: 'Cairo', 'Be Vietnam Pro', sans-serif;
        }

        .notifications-glass {
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .37);
            transition:
                border-color .25s ease,
                box-shadow .25s ease,
                transform .25s ease;
        }

        .notifications-glass:hover {
            border-color: rgba(0, 242, 255, .3);
            box-shadow: 0 0 20px rgba(0, 242, 255, .1);
        }

        .notifications-glow-cyan {
            box-shadow: 0 0 15px rgba(0, 242, 255, .4);
        }

        .notifications-glow-blue {
            box-shadow: 0 0 15px rgba(59, 130, 246, .4);
        }

        .notifications-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .notifications-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .notifications-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .1);
            border-radius: 10px;
        }

        .notifications-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 242, 255, .3);
        }


        /*
         * إخفاء الـ Navbar/Header الرئيسي القادم من x-app-layout
         * في صفحة الإشعارات فقط.
         */
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800,
        body nav[data-layout-navigation],
        body header[data-layout-header] {
            display: none !important;
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .notifications-sidebar {
                display: none !important;
            }

            .notifications-main {
                margin-right: 0 !important;
            }
        }
    </style>

    <div
        x-data="{ mobileMenuOpen: false }"
        class="notifications-page"
        dir="rtl"
    >
        <div class="fixed top-[-100px] left-[-100px] -z-10 h-[400px] w-[400px] rounded-full bg-[radial-gradient(circle,rgba(59,130,246,.15)_0%,rgba(11,19,38,0)_70%)] blur-[60px]"></div>

        <div class="fixed bottom-[-100px] right-[-100px] -z-10 h-[400px] w-[400px] rounded-full bg-[radial-gradient(circle,rgba(59,130,246,.15)_0%,rgba(11,19,38,0)_70%)] blur-[60px]"></div>

        {{-- القائمة الجانبية --}}
        <aside class="notifications-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col items-center border-l border-white/10 bg-white/[0.03] py-8 backdrop-blur-xl">
            <div class="mb-12">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center justify-center w-12 h-12 rounded-full notifications-glow-cyan bg-gradient-to-tr from-cyan-400 to-blue-500"
                    title="CreativeHome"
                >
                    <span class="text-xl font-bold text-white">
                        C
                    </span>
                </a>
            </div>

            <nav class="flex-1 w-full px-4 space-y-4">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 p-3 transition-all group rounded-xl text-slate-400 hover:bg-white/5 hover:text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    <span>لوحة التحكم</span>
                </a>

                <a
                    href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                    class="flex items-center gap-4 p-3 transition-all group rounded-xl text-slate-400 hover:bg-white/5 hover:text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    <span>المشاريع</span>
                </a>

                <a
                    href="{{ Route::has('users.index') ? route('users.index') : route('dashboard') }}"
                    class="flex items-center gap-4 p-3 transition-all group rounded-xl text-slate-400 hover:bg-white/5 hover:text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>المستخدمون</span>
                </a>

                <a
                    href="{{ Route::has('payments.index') ? route('payments.index') : route('dashboard') }}"
                    class="flex items-center gap-4 p-3 transition-all group rounded-xl text-slate-400 hover:bg-white/5 hover:text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 10h18M7 15h4"/>
                    </svg>

                    <span>المدفوعات</span>
                </a>

                <a
                    href="{{ route('notifications.index') }}"
                    class="flex items-center gap-4 p-3 transition-all border notifications-glow-cyan group rounded-xl border-cyan-300/20 bg-cyan-300/10 text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <span>الإشعارات</span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-4 p-3 transition-all group rounded-xl text-slate-400 hover:bg-white/5 hover:text-cyan-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="w-full p-4">
                <div class="flex items-center gap-3 p-2 border-none notifications-glass rounded-xl">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="w-10 h-10 overflow-hidden border rounded-full shrink-0 border-cyan-300/50 bg-slate-700"
                    >
                        @if ($currentUser->profile_photo)
                            <img
                                src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                alt="{{ $currentUser->name }}"
                                class="object-cover w-full h-full"
                            >
                        @else
                            <span class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-cyan-500 to-blue-600">
                                {{ mb_substr($currentUser->name, 0, 1) }}
                            </span>
                        @endif
                    </a>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate">
                            {{ $currentUser->name }}
                        </p>

                        <p class="truncate text-[10px] uppercase text-slate-500">
                            {{ $currentUser->role === 'admin' ? 'مدير المكتب' : 'مستخدم النظام' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="p-2 transition rounded-lg text-slate-400 hover:bg-white/5 hover:text-red-300"
                            title="تسجيل الخروج"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- قائمة الجوال --}}
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 z-[90] bg-black/70 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition
            class="fixed right-0 top-0 z-[100] flex h-screen w-72 flex-col border-l border-white/10 bg-[#0b1326] p-5 lg:hidden"
        >
            <div class="flex items-center justify-between">
                <h2 class="font-black text-white">CreativeHome</h2>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/5"
                >
                    ✕
                </button>
            </div>

            <nav class="mt-8 space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">لوحة التحكم</a>
                <a href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">المشاريع</a>
                <a href="{{ Route::has('users.index') ? route('users.index') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">المستخدمون</a>
                <a href="{{ Route::has('payments.index') ? route('payments.index') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">المدفوعات</a>
                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 rounded-lg bg-cyan-300/10 text-cyan-300">الإشعارات</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="min-h-screen p-6 notifications-main lg:mr-64 lg:p-10">
            <button
                type="button"
                @click="mobileMenuOpen = true"
                class="flex items-center justify-center mb-6 text-white h-11 w-11 rounded-xl bg-white/5 lg:hidden"
                title="فتح القائمة"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            @if (session('success'))
                <div class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- رأس الصفحة --}}
            <header class="flex flex-col items-start justify-between gap-5 mb-10 sm:flex-row sm:items-end">
                <div>
                    <h1 class="mb-2 text-3xl font-bold tracking-tight text-white">
                        مركز الإشعارات
                    </h1>

                    <p class="text-slate-400">
                        تابع آخر التطورات، المدفوعات، وحالة الطلبات الفنية
                    </p>
                </div>

                @if ($unreadNotifications > 0)
                    <form
                        method="POST"
                        action="{{ route('notifications.read-all') }}"
                        data-submit-once
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="notifications-glass flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm text-slate-300 transition-all hover:text-white"
                        >
                            <span>تعليم الكل كمقروء</span>

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </form>
                @endif
            </header>

            {{-- الإحصائيات --}}
            <section class="grid grid-cols-1 gap-6 mb-10 md:grid-cols-3">
                <article class="relative p-6 overflow-hidden notifications-glass group rounded-xl">
                    <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 transition-all rounded-full bg-blue-500/10 blur-3xl group-hover:bg-blue-500/20"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 text-blue-400 rounded-xl bg-blue-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M7 8h10M7 12h4m1 8-4-4H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3l-4 4Z"/>
                            </svg>
                        </div>

                        <span class="text-3xl font-bold">
                            {{ $totalNotifications }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-400">
                        إجمالي الإشعارات
                    </p>
                </article>

                <article class="relative p-6 overflow-hidden border-r-4 notifications-glass group rounded-xl border-r-amber-500">
                    <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 transition-all rounded-full bg-amber-500/10 blur-3xl group-hover:bg-amber-500/20"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-amber-500/20 text-amber-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3Z"/>
                            </svg>
                        </div>

                        <span class="text-3xl font-bold text-amber-500">
                            {{ $unreadNotifications }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-400">
                        غير مقروءة
                    </p>
                </article>

                <article class="relative p-6 overflow-hidden border-r-4 notifications-glass group rounded-xl border-r-emerald-500">
                    <div class="absolute top-0 right-0 w-32 h-32 -mt-16 -mr-16 transition-all rounded-full bg-emerald-500/10 blur-3xl group-hover:bg-emerald-500/20"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl bg-emerald-500/20 text-emerald-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </div>

                        <span class="text-3xl font-bold text-emerald-500">
                            {{ $readNotifications }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-400">
                        مقروءة / مكتملة
                    </p>
                </article>
            </section>

            {{-- قائمة الإشعارات --}}
            <section class="space-y-4">
                @forelse ($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $notificationUrl = $notification->data['url'] ?? null;
                        $notificationTitle = $notification->data['title'] ?? 'إشعار جديد';
                        $notificationMessage = $notification->data['message'] ?? 'لديك إشعار جديد';
                    @endphp

                    <article
                        class="notifications-glass group flex flex-col items-start justify-between gap-5 rounded-xl border-r-4 p-5 transition-all hover:scale-[1.01] sm:flex-row
                        {{
                            $isUnread
                                ? 'border-r-cyan-300'
                                : 'border-r-emerald-500 opacity-80'
                        }}"
                    >
                        <div class="flex flex-1 min-w-0 gap-5">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl
                                {{
                                    $isUnread
                                        ? 'bg-cyan-300/10 text-cyan-300'
                                        : 'bg-emerald-500/10 text-emerald-500'
                                }}"
                            >
                                @if ($isUnread)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="m8 12 2.5 2.5L16 9"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold {{ $isUnread ? 'text-white' : 'text-emerald-400' }}">
                                        {{ $notificationTitle }}
                                    </h3>

                                    @if ($isUnread)
                                        <span class="rounded-full bg-cyan-300/20 px-2 py-0.5 text-[10px] font-bold text-cyan-300">
                                            جديد
                                        </span>
                                    @endif
                                </div>

                                <p class="mb-3 text-sm leading-7 break-words text-slate-400">
                                    {{ $notificationMessage }}
                                </p>

                                <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 7v5l3 2"/>
                                        </svg>

                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap w-full gap-2 sm:w-auto sm:flex-col">
                            @if ($notificationUrl)
                                <form
                                    method="POST"
                                    action="{{ route('notifications.read', $notification->id) }}"
                                    data-submit-once
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="w-full px-5 py-2 text-sm font-bold text-white transition-all bg-blue-500 rounded-lg notifications-glow-blue hover:bg-blue-500/80"
                                    >
                                        عرض التفاصيل
                                    </button>
                                </form>
                            @elseif ($isUnread)
                                <form
                                    method="POST"
                                    action="{{ route('notifications.read', $notification->id) }}"
                                    data-submit-once
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="w-full px-5 py-2 text-sm font-bold transition-all border rounded-lg border-cyan-300/30 bg-cyan-300/20 text-cyan-300 hover:bg-cyan-300/30"
                                    >
                                        تعليم كمقروء
                                    </button>
                                </form>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('notifications.destroy', $notification->id) }}"
                                data-delete-notification
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="w-full px-5 py-2 text-sm font-bold transition-all border rounded-lg border-rose-500/20 bg-rose-500/10 text-rose-500 hover:bg-rose-500/20"
                                >
                                    حذف
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="p-10 text-center notifications-glass rounded-xl">
                        <div class="flex items-center justify-center w-20 h-20 mx-auto rounded-full bg-cyan-300/10 text-cyan-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                <path d="M10 21h4M4 4l16 16"/>
                            </svg>
                        </div>

                        <h3 class="mt-4 text-xl font-bold text-white">
                            لا توجد إشعارات
                        </h3>

                        <p class="mt-2 text-slate-400">
                            ستظهر الإشعارات الجديدة هنا
                        </p>
                    </div>
                @endforelse
            </section>

            @if (
                method_exists($notifications, 'hasPages')
                && $notifications->hasPages()
            )
                <div class="mt-10">
                    {{ $notifications->withQueryString()->links() }}
                </div>
            @endif
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document
                .querySelectorAll('[data-submit-once]')
                .forEach((form) => {
                    form.addEventListener('submit', function () {
                        const button =
                            form.querySelector(
                                'button[type="submit"]'
                            );

                        if (!button) {
                            return;
                        }

                        button.disabled = true;
                        button.classList.add(
                            'opacity-60',
                            'cursor-not-allowed'
                        );
                    });
                });

            document
                .querySelectorAll(
                    '[data-delete-notification]'
                )
                .forEach((form) => {
                    form.addEventListener('submit', function (event) {
                        const confirmed =
                            window.confirm(
                                'هل تريد حذف هذا الإشعار؟'
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
                            button.textContent =
                                'جاري الحذف...';

                            button.classList.add(
                                'opacity-60',
                                'cursor-not-allowed'
                            );
                        }
                    });
                });
        });
    </script>
</x-app-layout>
