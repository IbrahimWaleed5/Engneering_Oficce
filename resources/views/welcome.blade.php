<!DOCTYPE html>

<html
    lang="ar"
    dir="rtl"
>

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        مكتب الوليد الهندسي | استشارات هندسية احترافية
    </title>

    <meta
        name="description"
        content="منصة متكاملة لطلب الاستشارات الهندسية واختيار المهندس المناسب ومتابعة المشاريع."
    >

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=almarai:400,500,700,800&display=swap"
        rel="stylesheet"
    >

   @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen overflow-x-hidden font-sans text-white bg-slate-950">

    <div
        x-data="{
            menuOpen: false,
            showScrollButton: false
        }"
        @scroll.window="showScrollButton = window.scrollY > 500"
        class="relative min-h-screen"
    >

        {{-- الخلفيات المتحركة --}}

        <div class="fixed inset-0 pointer-events-none">

            <div
                class="absolute rounded-full -top-40 -right-40 h-[500px] w-[500px] bg-blue-600/20 blur-[120px]"
            ></div>

            <div
                class="absolute rounded-full top-1/3 -left-52 h-[500px] w-[500px] bg-cyan-500/15 blur-[130px]"
            ></div>

            <div
                class="absolute rounded-full -bottom-60 right-1/3 h-[600px] w-[600px] bg-purple-600/10 blur-[150px]"
            ></div>

        </div>

        {{-- Navbar --}}

        <nav
            class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-slate-950/70 backdrop-blur-2xl"
        >

            <div class="flex items-center justify-between h-20 px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                <a
                    href="{{ route('home') }}"
                    class="flex items-center gap-3 group"
                >

                    <div
                        class="relative flex items-center justify-center w-12 h-12 overflow-hidden text-xl font-black transition border shadow-lg rounded-2xl border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-600/20 group-hover:scale-105"
                    >
                        م

                        <div
                            class="absolute inset-0 transition opacity-0 bg-white/10 group-hover:opacity-100"
                        ></div>
                    </div>

                    <div>

                        <p class="text-lg font-extrabold text-white">
                            مكتب الوليد الهندسي
                        </p>

                        <p class="text-xs text-slate-400">
                            حلول هندسية موثوقة
                        </p>

                    </div>

                </a>

                <div class="items-center hidden gap-1 lg:flex">

                    <a
                        href="#home"
                        class="px-4 py-2 text-sm font-semibold transition rounded-xl text-slate-300 hover:text-white hover:bg-white/5"
                    >
                        الرئيسية
                    </a>

                    <a
                        href="#services"
                        class="px-4 py-2 text-sm font-semibold transition rounded-xl text-slate-300 hover:text-white hover:bg-white/5"
                    >
                        خدماتنا
                    </a>

                    <a
                        href="#works"
                        class="px-4 py-2 text-sm font-semibold transition rounded-xl text-slate-300 hover:text-white hover:bg-white/5"
                    >
                        أعمال المهندسين
                    </a>

                    <a
                        href="#engineers"
                        class="px-4 py-2 text-sm font-semibold transition rounded-xl text-slate-300 hover:text-white hover:bg-white/5"
                    >
                        مهندسونا
                    </a>

                    <a
                        href="#how-it-works"
                        class="px-4 py-2 text-sm font-semibold transition rounded-xl text-slate-300 hover:text-white hover:bg-white/5"
                    >
                        كيف نعمل
                    </a>

                </div>

                <div class="items-center hidden gap-3 lg:flex">

                    @auth

                        <a
                            href="{{ route('dashboard') }}"
                            class="secondary-button"
                        >
                            لوحة التحكم
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-bold text-slate-300 hover:text-white"
                        >
                            تسجيل الدخول
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="primary-button"
                        >
                            إنشاء حساب
                        </a>

                    @endauth

                </div>

                <button
                    type="button"
                    @click="menuOpen = ! menuOpen"
                    class="flex items-center justify-center border w-11 h-11 lg:hidden rounded-xl border-white/10 bg-white/5"
                >
                    <svg
                        x-show="!menuOpen"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>

                    <svg
                        x-cloak
                        x-show="menuOpen"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>

            {{-- قائمة الهاتف --}}

            <div
                x-cloak
                x-show="menuOpen"
                x-transition
                class="px-4 pb-5 border-t lg:hidden border-white/10 bg-slate-950/95"
            >

                <div class="pt-4 space-y-2">

                    <a
                        href="#home"
                        @click="menuOpen = false"
                        class="block px-4 py-3 rounded-xl text-slate-200 hover:bg-white/5"
                    >
                        الرئيسية
                    </a>

                    <a
                        href="#services"
                        @click="menuOpen = false"
                        class="block px-4 py-3 rounded-xl text-slate-200 hover:bg-white/5"
                    >
                        خدماتنا
                    </a>

                    <a
                        href="#works"
                        @click="menuOpen = false"
                        class="block px-4 py-3 rounded-xl text-slate-200 hover:bg-white/5"
                    >
                        أعمال المهندسين
                    </a>

                    <a
                        href="#engineers"
                        @click="menuOpen = false"
                        class="block px-4 py-3 rounded-xl text-slate-200 hover:bg-white/5"
                    >
                        مهندسونا
                    </a>

                    <div class="pt-3 border-t border-white/10">

                        @auth

                            <a
                                href="{{ route('dashboard') }}"
                                class="flex w-full primary-button"
                            >
                                لوحة التحكم
                            </a>

                        @else

                            <div class="grid grid-cols-2 gap-3">

                                <a
                                    href="{{ route('login') }}"
                                    class="secondary-button"
                                >
                                    دخول
                                </a>

                                <a
                                    href="{{ route('register') }}"
                                    class="primary-button"
                                >
                                    حساب جديد
                                </a>

                            </div>

                        @endauth

                    </div>

                </div>

            </div>

        </nav>

        {{-- Hero --}}

        <main class="relative z-10">

            <section
                id="home"
                class="relative flex items-center min-h-screen pt-28"
            >

                <div class="grid items-center gap-16 px-4 py-20 mx-auto max-w-7xl lg:grid-cols-2 sm:px-6 lg:px-8">

                    <div class="relative z-10">

                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold border rounded-full mb-7 fade-up border-cyan-400/20 bg-cyan-400/10 text-cyan-200"
                        >
                            <span class="relative flex w-2 h-2">

                                <span
                                    class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-cyan-400 animate-ping"
                                ></span>

                                <span
                                    class="relative inline-flex w-2 h-2 rounded-full bg-cyan-400"
                                ></span>

                            </span>

                            منصة هندسية متكاملة

                        </div>

                        <h1
                            class="text-4xl font-black leading-tight delay-100 fade-up sm:text-5xl lg:text-7xl"
                        >
                            حوّل فكرتك إلى

                            <span class="block mt-3 gradient-text">
                                مشروع هندسي ناجح
                            </span>
                        </h1>

                        <p
                            class="max-w-2xl text-lg leading-9 delay-200 mt-7 fade-up text-slate-300"
                        >
                            اختر المهندس المناسب، أرسل تفاصيل مشروعك، تابع
                            الاستشارة والدفع واستلم ملفاتك النهائية من خلال
                            منصة واحدة آمنة وسهلة.
                        </p>

                        <div
                            class="flex flex-col gap-4 mt-10 delay-300 fade-up sm:flex-row"
                        >

                            @auth

                                @if (auth()->user()->role === 'customer')

                                    <a
                                        href="{{ route('engineer.works.public') }}"
                                        class="primary-button"
                                    >
                                        <span>استكشف المهندسين</span>
                                        <span>←</span>
                                    </a>

                                    <a
                                        href="{{ route('consultations.create') }}"
                                        class="secondary-button"
                                    >
                                        طلب استشارة مباشرة
                                    </a>

                                @else

                                    <a
                                        href="{{ route('dashboard') }}"
                                        class="primary-button"
                                    >
                                        الانتقال إلى لوحة التحكم
                                    </a>

                                @endif

                            @else

                                <a
                                    href="{{ route('register') }}"
                                    class="primary-button"
                                >
                                    ابدأ مشروعك الآن
                                    <span>←</span>
                                </a>

                                <a
                                    href="{{ route('engineer.works.public') }}"
                                    class="secondary-button"
                                >
                                    تصفح مكتبة الأعمال
                                </a>

                            @endauth

                        </div>

                        <div
                            class="flex flex-wrap items-center gap-6 mt-10 fade-up delay-400"
                        >

                            <div class="flex items-center gap-2">

                                <div class="flex -space-x-2 space-x-reverse">

                                    @foreach (['م', 'ع', 'ه', 'أ'] as $letter)

                                        <div
                                            class="flex items-center justify-center text-xs font-bold border-2 rounded-full w-9 h-9 border-slate-950 bg-gradient-to-br from-blue-500 to-cyan-500"
                                        >
                                            {{ $letter }}
                                        </div>

                                    @endforeach

                                </div>

                                <div>

                                    <p class="text-sm font-bold text-white">
                                        نخبة من المهندسين
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        خبرات متعددة
                                    </p>

                                </div>

                            </div>

                            <div class="hidden w-px h-10 bg-white/10 sm:block"></div>

                            <div>

                                <div class="flex text-yellow-400">
                                    ★★★★★
                                </div>

                                <p class="mt-1 text-xs text-slate-400">
                                    جودة ومتابعة مستمرة
                                </p>

                            </div>

                        </div>

                    </div>

                    {{-- الرسم الهندسي --}}

                    <div class="relative hidden lg:block fade-in">

                        <div
                            class="absolute inset-0 rounded-full bg-blue-500/20 blur-[100px]"
                        ></div>

                        <div
                            class="relative p-6 glass-panel rounded-[2rem] float-animation"
                        >

                            <div class="flex items-center justify-between mb-6">

                                <div>

                                    <p class="text-sm text-slate-400">
                                        متابعة المشروع
                                    </p>

                                    <p class="mt-1 text-lg font-bold">
                                        التصميم المعماري
                                    </p>

                                </div>

                                <div
                                    class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-green-200 rounded-full bg-green-500/10"
                                >
                                    <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                    قيد التنفيذ
                                </div>

                            </div>

                            <div
                                class="relative overflow-hidden border aspect-[4/3] rounded-3xl border-white/10 bg-gradient-to-br from-slate-900 to-slate-800"
                            >

                                {{-- شكل مبنى CSS --}}

                                <div class="absolute inset-x-0 bottom-0 h-20 bg-slate-950/70"></div>

                                <div class="absolute bottom-16 left-1/2 h-[280px] w-[330px] -translate-x-1/2">

                                    <div
                                        class="absolute bottom-0 left-0 w-full h-56 border shadow-2xl rounded-t-2xl border-cyan-400/20 bg-gradient-to-br from-slate-700 to-slate-900"
                                    ></div>

                                    <div
                                        class="absolute bottom-0 w-40 h-40 border right-14 border-blue-400/20 bg-slate-800"
                                    ></div>

                                    <div class="absolute grid grid-cols-4 gap-4 bottom-28 right-8 left-8">

                                        @for ($i = 0; $i < 8; $i++)

                                            <div
                                                class="h-10 border rounded-sm border-cyan-300/20 bg-cyan-400/20 shadow-[0_0_18px_rgba(34,211,238,0.08)]"
                                            ></div>

                                        @endfor

                                    </div>

                                    <div
                                        class="absolute bottom-0 w-20 h-24 translate-x-1/2 border border-b-0 right-1/2 border-cyan-300/20 bg-slate-950"
                                    ></div>

                                    <div
                                        class="absolute w-64 h-5 -translate-x-1/2 rounded-full bottom-56 left-1/2 bg-gradient-to-r from-blue-500 to-cyan-400"
                                    ></div>

                                </div>

                                <div
                                    class="absolute w-32 h-32 border rounded-full top-10 right-10 border-cyan-400/10"
                                ></div>

                                <div
                                    class="absolute w-20 h-20 border rounded-full top-16 right-16 border-cyan-400/20"
                                ></div>

                            </div>

                            <div class="grid grid-cols-3 gap-3 mt-5">

                                <div class="p-4 border rounded-2xl border-white/10 bg-white/5">

                                    <p class="text-xs text-slate-400">
                                        الإنجاز
                                    </p>

                                    <p class="mt-2 text-xl font-black text-cyan-300">
                                        75%
                                    </p>

                                </div>

                                <div class="p-4 border rounded-2xl border-white/10 bg-white/5">

                                    <p class="text-xs text-slate-400">
                                        المهندس
                                    </p>

                                    <p class="mt-2 text-sm font-bold">
                                        تم التعيين
                                    </p>

                                </div>

                                <div class="p-4 border rounded-2xl border-white/10 bg-white/5">

                                    <p class="text-xs text-slate-400">
                                        حالة الدفع
                                    </p>

                                    <p class="mt-2 text-sm font-bold text-green-300">
                                        مدفوع
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div
                            class="absolute p-4 glass-panel -right-12 top-32 rounded-2xl pulse-glow"
                        >
                            <p class="text-xs text-slate-400">
                                إشعار جديد
                            </p>

                            <p class="mt-1 text-sm font-bold">
                                تم تعيين مهندس لمشروعك
                            </p>
                        </div>

                        <div
                            class="absolute p-4 glass-panel -left-12 bottom-20 rounded-2xl"
                        >
                            <p class="text-xs text-slate-400">
                                الملف النهائي
                            </p>

                            <p class="mt-1 text-sm font-bold text-cyan-300">
                                جاهز للتحميل ✓
                            </p>
                        </div>

                    </div>

                </div>

                <div class="absolute -translate-x-1/2 bottom-8 left-1/2">

                    <a
                        href="#services"
                        class="flex items-center justify-center border rounded-full w-11 h-11 border-white/10 bg-white/5 animate-bounce"
                    >
                        ↓
                    </a>

                </div>

            </section>

            {{-- الإحصائيات --}}

            <section class="relative py-10">

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="grid grid-cols-2 gap-4 p-5 glass-panel rounded-3xl lg:grid-cols-4">

                        <div class="p-5 text-center">

                            <p
                                class="text-3xl font-black gradient-text md:text-4xl"
                                data-counter="{{ $statistics['engineers'] }}"
                            >
                                {{ $statistics['engineers'] }}
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                مهندس فعّال
                            </p>

                        </div>

                        <div class="p-5 text-center border-r border-white/5">

                            <p
                                class="text-3xl font-black gradient-text md:text-4xl"
                                data-counter="{{ $statistics['consultations'] }}"
                            >
                                {{ $statistics['consultations'] }}
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                استشارة مدفوعة
                            </p>

                        </div>

                        <div class="p-5 text-center lg:border-r border-white/5">

                            <p
                                class="text-3xl font-black gradient-text md:text-4xl"
                                data-counter="{{ $statistics['completed'] }}"
                            >
                                {{ $statistics['completed'] }}
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                مشروع مكتمل
                            </p>

                        </div>

                        <div class="p-5 text-center border-r border-white/5">

                            <p
                                class="text-3xl font-black gradient-text md:text-4xl"
                                data-counter="{{ $statistics['works'] }}"
                            >
                                {{ $statistics['works'] }}
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                عمل منشور
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            {{-- الخدمات --}}

            <section
                id="services"
                class="relative py-24"
            >

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="max-w-3xl mx-auto text-center mb-14">

                        <div
                            class="inline-flex px-4 py-2 mb-5 text-sm font-bold text-blue-200 border rounded-full border-blue-400/20 bg-blue-500/10"
                        >
                            خدماتنا
                        </div>

                        <h2 class="text-3xl font-black md:text-5xl">
                            خدمات هندسية

                            <span class="gradient-text">
                                متكاملة
                            </span>
                        </h2>

                        <p class="mt-5 leading-8 text-slate-400">
                            نوفر لك متخصصين في مجالات هندسية متعددة مع متابعة
                            كاملة من لحظة إرسال الطلب حتى استلام الملفات.
                        </p>

                    </div>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                        @php
                            $services = [
                                [
                                    'icon' => '🏛️',
                                    'title' => 'التصميم المعماري',
                                    'description' => 'تصميم مخططات معمارية حديثة تجمع بين الوظيفة والجمال.',
                                    'color' => 'blue',
                                ],
                                [
                                    'icon' => '🏗️',
                                    'title' => 'التصميم الإنشائي',
                                    'description' => 'حلول إنشائية آمنة ودقيقة وفق المعايير الهندسية.',
                                    'color' => 'cyan',
                                ],
                                [
                                    'icon' => '⚡',
                                    'title' => 'الهندسة الكهربائية',
                                    'description' => 'تصميم الأنظمة الكهربائية وتوزيع الأحمال بكفاءة.',
                                    'color' => 'yellow',
                                ],
                                [
                                    'icon' => '💧',
                                    'title' => 'الهندسة الميكانيكية',
                                    'description' => 'تصميم أنظمة التكييف والسباكة والحماية من الحريق.',
                                    'color' => 'emerald',
                                ],
                                [
                                    'icon' => '🎨',
                                    'title' => 'التصميم الداخلي',
                                    'description' => 'تحويل المساحات الداخلية إلى بيئات مريحة ومميزة.',
                                    'color' => 'purple',
                                ],
                                [
                                    'icon' => '📋',
                                    'title' => 'الاستشارات الفنية',
                                    'description' => 'مراجعة المخططات وتقديم الحلول والتقارير الفنية.',
                                    'color' => 'rose',
                                ],
                            ];
                        @endphp

                        @foreach ($services as $index => $service)

                            <article
                                class="p-7 glass-card rounded-3xl fade-up"
                                style="animation-delay: {{ $index * 0.08 }}s"
                            >

                                <div
                                    class="relative z-10 flex items-center justify-center mb-6 text-3xl border w-14 h-14 rounded-2xl border-white/10 bg-white/5"
                                >
                                    {{ $service['icon'] }}
                                </div>

                                <div class="relative z-10">

                                    <h3 class="text-xl font-extrabold">
                                        {{ $service['title'] }}
                                    </h3>

                                    <p class="mt-4 leading-7 text-slate-400">
                                        {{ $service['description'] }}
                                    </p>

                                    @auth

                                        @if (auth()->user()->role === 'customer')

                                            <a
                                                href="{{ route('consultations.create') }}"
                                                class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-cyan-300 hover:text-cyan-200"
                                            >
                                                اطلب الخدمة
                                                <span>←</span>
                                            </a>

                                        @endif

                                    @else

                                        <a
                                            href="{{ route('register') }}"
                                            class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-cyan-300 hover:text-cyan-200"
                                        >
                                            ابدأ الآن
                                            <span>←</span>
                                        </a>

                                    @endauth

                                </div>

                            </article>

                        @endforeach

                    </div>

                </div>

            </section>

            {{-- كيف نعمل --}}

            <section
                id="how-it-works"
                class="relative py-24 border-y border-white/5 bg-white/[0.02]"
            >

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="grid items-center gap-14 lg:grid-cols-2">

                        <div>

                            <div
                                class="inline-flex px-4 py-2 mb-5 text-sm font-bold border rounded-full border-cyan-400/20 bg-cyan-500/10 text-cyan-200"
                            >
                                خطوات بسيطة
                            </div>

                            <h2 class="text-3xl font-black leading-tight md:text-5xl">
                                من الفكرة إلى الإنجاز

                                <span class="block mt-3 gradient-text">
                                    في أربع خطوات
                                </span>
                            </h2>

                            <p class="mt-6 leading-8 text-slate-400">
                                صممنا النظام ليكون واضحًا وسهلًا، مع متابعة
                                مستمرة وإشعارات في كل مرحلة.
                            </p>

                            <div class="mt-9">

                                <a
                                    href="{{ route('engineer.works.public') }}"
                                    class="primary-button"
                                >
                                    اختر مهندسك الآن
                                </a>

                            </div>

                        </div>

                        <div class="space-y-4">

                            @php
                                $steps = [
                                    [
                                        'number' => '01',
                                        'title' => 'اختر المهندس',
                                        'description' => 'تصفح مكتبة الأعمال واختر المهندس المناسب لمشروعك.',
                                    ],
                                    [
                                        'number' => '02',
                                        'title' => 'أرسل تفاصيل المشروع',
                                        'description' => 'حدد نوع الاستشارة وارفع الملفات والمعلومات المطلوبة.',
                                    ],
                                    [
                                        'number' => '03',
                                        'title' => 'ارفع إيصال الدفع',
                                        'description' => 'اختر طريقة الدفع وأرسل الإيصال لمراجعة الإدارة.',
                                    ],
                                    [
                                        'number' => '04',
                                        'title' => 'تابع واستلم العمل',
                                        'description' => 'تابع حالة الطلب واستلم الملف النهائي من حسابك.',
                                    ],
                                ];
                            @endphp

                            @foreach ($steps as $step)

                                <div
                                    class="flex gap-5 p-5 glass-card rounded-2xl"
                                >

                                    <div
                                        class="flex items-center justify-center flex-none font-black border w-14 h-14 rounded-2xl border-cyan-400/20 bg-cyan-500/10 text-cyan-300"
                                    >
                                        {{ $step['number'] }}
                                    </div>

                                    <div>

                                        <h3 class="text-lg font-extrabold">
                                            {{ $step['title'] }}
                                        </h3>

                                        <p class="mt-2 leading-7 text-slate-400">
                                            {{ $step['description'] }}
                                        </p>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            </section>

            {{-- أحدث الأعمال --}}

            <section
                id="works"
                class="relative py-24"
            >

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="flex flex-col gap-5 mb-12 md:flex-row md:items-end md:justify-between">

                        <div>

                            <div
                                class="inline-flex px-4 py-2 mb-5 text-sm font-bold text-purple-200 border rounded-full border-purple-400/20 bg-purple-500/10"
                            >
                                معرض الأعمال
                            </div>

                            <h2 class="text-3xl font-black md:text-5xl">
                                أحدث أعمال

                                <span class="gradient-text">
                                    مهندسينا
                                </span>
                            </h2>

                            <p class="max-w-2xl mt-5 leading-8 text-slate-400">
                                استكشف بعض المشاريع التي أضافها مهندسو المكتب
                                وتمت مراجعتها واعتمادها.
                            </p>

                        </div>

                        <a
                            href="{{ route('engineer.works.public') }}"
                            class="secondary-button"
                        >
                            عرض جميع الأعمال
                            <span>←</span>
                        </a>

                    </div>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                        @forelse ($latestWorks as $work)

                            <article class="group glass-card rounded-3xl">

                                <div class="relative h-64 overflow-hidden">

                                    @if ($work->coverImage)

                                        <img
                                            src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                            alt="{{ $work->title }}"
                                            class="object-cover w-full h-full transition duration-700 group-hover:scale-110"
                                        >

                                    @else

                                        <div
                                            class="flex items-center justify-center w-full h-full text-5xl bg-gradient-to-br from-slate-800 to-slate-900"
                                        >
                                            🏗️
                                        </div>

                                    @endif

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"
                                    ></div>

                                    @if ($work->project_type)

                                        <span
                                            class="absolute px-3 py-2 text-xs font-bold border rounded-full top-4 right-4 border-white/10 bg-slate-950/70 backdrop-blur-lg"
                                        >
                                            {{ $work->project_type }}
                                        </span>

                                    @endif

                                </div>

                                <div class="relative z-10 p-6">

                                    <h3 class="text-xl font-extrabold">
                                        {{ $work->title }}
                                    </h3>

                                    <div class="flex items-center gap-3 mt-4">

                                        <div
                                            class="flex items-center justify-center w-10 h-10 font-bold rounded-full bg-gradient-to-br from-blue-500 to-cyan-500"
                                        >
                                            {{ mb_substr($work->engineer?->name ?? 'م', 0, 1) }}
                                        </div>

                                        <div>

                                            <p class="text-sm font-bold">
                                                {{ $work->engineer?->name ?? 'مهندس المكتب' }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $work->location ?? 'الموقع غير محدد' }}
                                            </p>

                                        </div>

                                    </div>

                                    <div class="flex gap-3 mt-6">

                                        <a
                                            href="{{ route('engineer.works.show', $work) }}"
                                            class="flex-1 primary-button"
                                        >
                                            عرض المشروع
                                        </a>

                                        @auth

                                            @if (
                                                auth()->user()->role === 'customer'
                                                && $work->engineer
                                            )

                                                <a
                                                    href="{{ route(
                                                        'consultations.create-for-engineer',
                                                        $work->engineer
                                                    ) }}"
                                                    class="flex items-center justify-center w-12 h-12 border rounded-xl border-white/10 bg-white/5 hover:bg-green-500/20"
                                                    title="اطلب هذا المهندس"
                                                >
                                                    ✉️
                                                </a>

                                            @endif

                                        @endauth

                                    </div>

                                </div>

                            </article>

                        @empty

                            <div
                                class="p-12 text-center col-span-full glass-panel rounded-3xl"
                            >

                                <div class="mb-4 text-5xl">
                                    🏗️
                                </div>

                                <h3 class="text-xl font-bold">
                                    لا توجد أعمال منشورة حاليًا
                                </h3>

                                <p class="mt-3 text-slate-400">
                                    ستظهر هنا أحدث أعمال المهندسين بعد اعتمادها.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </section>

            {{-- المهندسون --}}

            <section
                id="engineers"
                class="relative py-24 border-y border-white/5 bg-white/[0.02]"
            >

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div class="max-w-3xl mx-auto text-center mb-14">

                        <div
                            class="inline-flex px-4 py-2 mb-5 text-sm font-bold text-green-200 border rounded-full border-green-400/20 bg-green-500/10"
                        >
                            فريقنا
                        </div>

                        <h2 class="text-3xl font-black md:text-5xl">
                            مهندسون يمكنك

                            <span class="gradient-text">
                                الوثوق بهم
                            </span>
                        </h2>

                        <p class="mt-5 leading-8 text-slate-400">
                            تعرف على مهندسينا واختر صاحب الخبرة الأنسب لمشروعك.
                        </p>

                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                        @forelse ($featuredEngineers as $engineer)

                            <article
                                class="p-6 text-center glass-card rounded-3xl"
                            >

                                <div class="relative w-24 h-24 mx-auto">

    @if ($engineer->profile_photo_path)

        <img
            src="{{ asset('storage/' . $engineer->profile_photo_path) }}"
            alt="{{ $engineer->name }}"
            class="object-cover w-24 h-24 border rounded-full shadow-xl border-cyan-400/20"
        >

    @else

        <div
            class="flex items-center justify-center w-24 h-24 text-3xl font-black border rounded-full shadow-xl border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500 shadow-blue-600/20"
        >
            {{ mb_substr($engineer->name, 0, 1) }}
        </div>

    @endif

    <span
        class="absolute w-5 h-5 bg-green-400 border-4 rounded-full bottom-1 left-1 border-slate-900"
    ></span>

</div>

                                <h3 class="mt-5 text-lg font-extrabold">
                                    {{ $engineer->name }}
                                </h3>

                                <p class="mt-2 text-sm text-slate-400">
                                    مهندس معتمد في المنصة
                                </p>

                                <div class="grid grid-cols-2 gap-3 mt-6">

                                    <div class="p-3 rounded-xl bg-white/5">

                                        <p class="font-black text-cyan-300">
                                            {{ $engineer->engineerWorks->count() }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            أعمال
                                        </p>

                                    </div>

                                    <div class="p-3 rounded-xl bg-white/5">

                                        <p class="font-black text-yellow-300">
                                            ★ 5
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            التقييم
                                        </p>

                                    </div>

                                </div>

                                @auth

                                    @if (auth()->user()->role === 'customer')

                                        <a
                                            href="{{ route(
                                                'consultations.create-for-engineer',
                                                $engineer
                                            ) }}"
                                            class="flex w-full mt-5 primary-button"
                                        >
                                            اطلب هذا المهندس
                                        </a>

                                    @endif

                                @else

                                    <a
                                        href="{{ route('login') }}"
                                        class="flex w-full mt-5 primary-button"
                                    >
                                        سجّل لاختيار المهندس
                                    </a>

                                @endauth

                            </article>

                        @empty

                            <div
                                class="p-10 text-center col-span-full glass-panel rounded-3xl"
                            >
                                لا يوجد مهندسون متاحون حاليًا.
                            </div>

                        @endforelse

                    </div>

                </div>

            </section>

            {{-- CTA --}}

            <section class="relative py-24">

                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                    <div
                        class="relative px-6 py-16 overflow-hidden text-center border rounded-[2.5rem] border-blue-400/20 bg-gradient-to-l from-blue-700/80 via-blue-600/70 to-cyan-600/70 shadow-2xl shadow-blue-950/40 md:px-14"
                    >

                        <div
                            class="absolute w-64 h-64 rounded-full -top-32 -right-20 bg-white/10 blur-2xl"
                        ></div>

                        <div
                            class="absolute w-64 h-64 rounded-full -bottom-32 -left-20 bg-purple-500/20 blur-2xl"
                        ></div>

                        <div class="relative z-10">

                            <p class="font-bold text-cyan-100">
                                هل لديك مشروع جديد؟
                            </p>

                            <h2 class="mt-4 text-3xl font-black md:text-5xl">
                                دعنا نساعدك في تحويله إلى واقع
                            </h2>

                            <p class="max-w-2xl mx-auto mt-6 leading-8 text-blue-100">
                                أرسل طلبك الآن واختر المهندس المناسب، وتابع جميع
                                مراحل المشروع من حسابك.
                            </p>

                            <div class="flex flex-col justify-center gap-4 mt-9 sm:flex-row">

                                @auth

                                    @if (auth()->user()->role === 'customer')

                                        <a
                                            href="{{ route('engineer.works.public') }}"
                                            class="py-4 font-extrabold text-blue-700 transition bg-white px-7 rounded-2xl hover:-translate-y-1 hover:shadow-xl"
                                        >
                                            اختر مهندسًا
                                        </a>

                                        <a
                                            href="{{ route('consultations.create') }}"
                                            class="py-4 font-extrabold text-white transition border px-7 rounded-2xl border-white/30 bg-white/10 hover:bg-white/20"
                                        >
                                            طلب مباشر
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('dashboard') }}"
                                            class="py-4 font-extrabold text-blue-700 bg-white px-7 rounded-2xl"
                                        >
                                            لوحة التحكم
                                        </a>

                                    @endif

                                @else

                                    <a
                                        href="{{ route('register') }}"
                                        class="py-4 font-extrabold text-blue-700 transition bg-white px-7 rounded-2xl hover:-translate-y-1"
                                    >
                                        إنشاء حساب مجاني
                                    </a>

                                    <a
                                        href="{{ route('login') }}"
                                        class="py-4 font-extrabold text-white border px-7 rounded-2xl border-white/30 bg-white/10"
                                    >
                                        تسجيل الدخول
                                    </a>

                                @endauth

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </main>

        {{-- Footer --}}

        <footer class="relative z-10 border-t border-white/10">

            <div class="px-4 mx-auto py-14 max-w-7xl sm:px-6 lg:px-8">

                <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

                    <div>

                        <div class="flex items-center gap-3">

                            <div
                                class="flex items-center justify-center font-black w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-500"
                            >
                                م
                            </div>

                            <div>

                                <p class="font-extrabold">
                                    المكتب الهندسي
                                </p>

                                <p class="text-xs text-slate-400">
                                    إدارة الاستشارات والمشاريع
                                </p>

                            </div>

                        </div>

                        <p class="mt-5 leading-7 text-slate-400">
                            منصة تجمع العملاء والمهندسين وتسهّل إدارة
                            الاستشارات والمدفوعات والملفات.
                        </p>

                    </div>

                    <div>

                        <h3 class="font-extrabold">
                            روابط سريعة
                        </h3>

                        <div class="mt-5 space-y-3 text-sm text-slate-400">

                            <a
                                href="#services"
                                class="block hover:text-cyan-300"
                            >
                                خدماتنا
                            </a>

                            <a
                                href="#works"
                                class="block hover:text-cyan-300"
                            >
                                معرض الأعمال
                            </a>

                            <a
                                href="#engineers"
                                class="block hover:text-cyan-300"
                            >
                                المهندسون
                            </a>

                            <a
                                href="{{ route('engineer.works.public') }}"
                                class="block hover:text-cyan-300"
                            >
                                المكتبة الكاملة
                            </a>

                        </div>

                    </div>

                    <div>

                        <h3 class="font-extrabold">
                            الحساب
                        </h3>

                        <div class="mt-5 space-y-3 text-sm text-slate-400">

                            @auth

                                <a
                                    href="{{ route('dashboard') }}"
                                    class="block hover:text-cyan-300"
                                >
                                    لوحة التحكم
                                </a>

                                <a
                                    href="{{ route('notifications.index') }}"
                                    class="block hover:text-cyan-300"
                                >
                                    الإشعارات
                                </a>

                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="block hover:text-cyan-300"
                                >
                                    الملف الشخصي
                                </a>

                            @else

                                <a
                                    href="{{ route('login') }}"
                                    class="block hover:text-cyan-300"
                                >
                                    تسجيل الدخول
                                </a>

                                <a
                                    href="{{ route('register') }}"
                                    class="block hover:text-cyan-300"
                                >
                                    إنشاء حساب
                                </a>

                            @endauth

                        </div>

                    </div>

                    <div>

                        <h3 class="font-extrabold">
                            تواصل معنا
                        </h3>

                        <div class="mt-5 space-y-4 text-sm text-slate-400">

                            <p class="flex items-center gap-3">
                                <span>📍</span>
                                فلسطين
                            </p>

                            <p class="flex items-center gap-3">
                                <span>✉️</span>
                                info@example.com
                            </p>

                            <p class="flex items-center gap-3">
                                <span>📞</span>
                                0590000000
                            </p>

                        </div>

                    </div>

                </div>

                <div
                    class="flex flex-col gap-3 mt-10 text-sm border-t pt-7 md:flex-row md:items-center md:justify-between border-white/10 text-slate-500"
                >

                    <p>
                        © {{ date('Y') }} المكتب الهندسي. جميع الحقوق محفوظة.
                    </p>

                    <p>
                        صُمم لإدارة المشاريع الهندسية بكفاءة.
                    </p>

                </div>

            </div>

        </footer>

        {{-- زر الصعود --}}

        <button
            x-cloak
            x-show="showScrollButton"
            x-transition
            type="button"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed z-50 flex items-center justify-center w-12 h-12 text-lg border rounded-full shadow-xl bottom-6 left-6 border-cyan-400/20 bg-cyan-500 text-slate-950 hover:-translate-y-1"
        >
            ↑
        </button>

    </div>

</body>

</html>
