<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>الوليد الهندسي | استشارات هندسية احترافية</title>

    <meta
        name="description"
        content="CreativeHome منصة متكاملة لطلب الاستشارات الهندسية واختيار المهندس المناسب ومتابعة المشاريع."
    >

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=almarai:400,500,700,800&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --surface: #0b1326;
            --surface-lowest: #060e20;
            --surface-low: #131b2e;
            --surface-container: #171f33;
            --surface-high: #222a3d;
            --surface-highest: #2d3449;
            --on-surface: #dae2fd;
            --on-surface-variant: #c3c6d7;
            --primary: #b4c5ff;
            --primary-container: #2563eb;
            --secondary: #ffb1c7;
            --tertiary: #d2bbff;
            --outline: #8d90a0;
            --outline-variant: #434655;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            color: var(--on-surface);
            background: var(--surface);
            font-family: Almarai, sans-serif;
        }

        .glass-card {
            background: rgba(45, 52, 73, .4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
            transition: all .3s ease;
        }

        .glass-card:hover {
            background: rgba(45, 52, 73, .6);
            border-color: rgba(180, 197, 255, .3);
            transform: translateY(-4px);
        }

        .gradient-text {
            background: linear-gradient(135deg, #b4c5ff 0%, #d2bbff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all .8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .nav-link::after {
            display: block;
            width: 0;
            height: 2px;
            content: "";
            background: var(--primary);
            transition: width .3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        #welcome-mobile-menu {
            animation: welcomeMenuIn .24s ease-out;
        }

        @keyframes welcomeMenuIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    {{-- الخلفية المتحركة --}}
    <div class="fixed inset-0 w-full h-full -z-10 opacity-40">
        <canvas
            id="creativehome-shader"
            class="block w-full h-full"
        ></canvas>
    </div>

    {{-- شريط التنقل --}}
    <header
        class="fixed top-0 z-50 flex items-center w-full h-16 px-6 border-b shadow-sm bg-[#0b1326]/80 backdrop-blur-md border-[#434655]/10"
    >
        <div class="flex items-center justify-between w-full mx-auto max-w-7xl">
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3"
            >
                <span
                    class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-blue-600/20"
                    aria-hidden="true"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="w-6 h-6"
                    >
                        <path d="M3 11.5 12 4l9 7.5"/>
                        <path d="M5.5 10.5V21h13V10.5"/>
                        <path d="M9 21v-6h6v6"/>
                    </svg>
                </span>

                <span class="text-xl font-extrabold text-[#b4c5ff]">
                    الوليد الهندسي
                </span>
            </a>

            <nav class="items-center hidden gap-8 text-sm font-bold md:flex text-[#c3c6d7]">
                <a class="nav-link text-[#b4c5ff]" href="#home">
                    الرئيسية
                </a>

                <a class="nav-link hover:text-white" href="#services">
                    خدماتنا
                </a>

                <a class="nav-link hover:text-white" href="#works">
                    أعمالنا
                </a>

                <a class="nav-link hover:text-white" href="#engineers">
                    المهندسون
                </a>

                <a class="nav-link hover:text-white" href="#how-it-works">
                    كيف نعمل
                </a>
            </nav>

            <div class="items-center hidden gap-3 md:flex">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-6 py-2 text-sm font-bold text-white transition bg-blue-600 rounded-full active:scale-95"
                    >
                        لوحة التحكم
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-bold text-[#c3c6d7] hover:text-white"
                    >
                        تسجيل الدخول
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="px-6 py-2 text-sm font-bold text-white transition bg-blue-600 rounded-full active:scale-95"
                    >
                        إنشاء حساب
                    </a>
                @endauth
            </div>

            <button
                id="welcome-mobile-menu-button"
                type="button"
                aria-controls="welcome-mobile-menu"
                aria-expanded="false"
                class="flex items-center justify-center border w-11 h-11 md:hidden rounded-xl border-white/10 bg-white/5"
            >
                <svg
                    id="welcome-menu-open-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="w-6 h-6"
                >
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <svg
                    id="welcome-menu-close-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="hidden w-6 h-6"
                >
                    <path d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- قائمة الهاتف --}}
    <div
        id="welcome-mobile-menu"
        class="fixed top-16 right-0 left-0 z-40 hidden px-4 py-5 border-b shadow-2xl md:hidden border-white/10 bg-[#0b1326]/95 backdrop-blur-2xl"
    >
        <div class="space-y-2">
            <a data-welcome-mobile-link href="#home" class="block px-4 py-3 rounded-xl hover:bg-white/5">
                الرئيسية
            </a>

            <a data-welcome-mobile-link href="#services" class="block px-4 py-3 rounded-xl hover:bg-white/5">
                خدماتنا
            </a>

            <a data-welcome-mobile-link href="#works" class="block px-4 py-3 rounded-xl hover:bg-white/5">
                أعمالنا
            </a>

            <a data-welcome-mobile-link href="#engineers" class="block px-4 py-3 rounded-xl hover:bg-white/5">
                المهندسون
            </a>

            <a data-welcome-mobile-link href="#how-it-works" class="block px-4 py-3 rounded-xl hover:bg-white/5">
                كيف نعمل
            </a>

            <div class="pt-3 border-t border-white/10">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex justify-center w-full px-6 py-3 font-bold text-white bg-blue-600 rounded-xl"
                    >
                        لوحة التحكم
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a
                            href="{{ route('login') }}"
                            class="flex justify-center px-4 py-3 font-bold border rounded-xl border-white/10 bg-white/5"
                        >
                            دخول
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="flex justify-center px-4 py-3 font-bold text-white bg-blue-600 rounded-xl"
                        >
                            حساب جديد
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <main class="pt-16">
        {{-- Hero --}}
        <section
            id="home"
            class="relative flex items-center justify-center min-h-[921px] px-6 overflow-hidden"
        >
            <div class="z-10 max-w-4xl text-center reveal">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-bold text-blue-200 border rounded-full border-blue-300/20 bg-blue-400/10"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="w-4 h-4"
                    >
                        <path d="M12 3v18M3 12h18"/>
                    </svg>

                    منصة هندسية متكاملة
                </div>

                <h1 class="mb-6 text-5xl font-black leading-tight md:text-7xl">
                    <span class="gradient-text">حوّل فكرتك</span>
                    إلى مشروع هندسي ناجح
                </h1>

                <p class="max-w-2xl mx-auto mb-10 text-lg leading-9 md:text-xl text-[#c3c6d7]">
                    نحن نوفر لك أفضل الخبرات الهندسية في جميع التخصصات،
                    وتستطيع متابعة مراحل مشروعك والحصول على استشارات
                    احترافية من مهندسين معتمدين.
                </p>

                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @auth
                        @if (auth()->user()->role === 'customer')
                            <a
                                href="{{ route('engineer.works.public') }}"
                                class="inline-flex items-center gap-2 px-8 py-4 text-lg font-bold text-white transition bg-blue-600 shadow-lg rounded-xl active:scale-95"
                            >
                                <span>استكشف المهندسين</span>

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="w-5 h-5"
                                >
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                            </a>

                            <a
                                href="{{ route('consultations.create') }}"
                                class="px-8 py-4 text-lg font-bold transition border rounded-xl border-[#434655] bg-[#2d3449]/50 hover:bg-[#2d3449] active:scale-95"
                            >
                                طلب استشارة مباشرة
                            </a>
                        @else
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 px-8 py-4 text-lg font-bold text-white transition bg-blue-600 shadow-lg rounded-xl active:scale-95"
                            >
                                الانتقال إلى لوحة التحكم
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 text-lg font-bold text-white transition bg-blue-600 shadow-lg rounded-xl active:scale-95"
                        >
                            <span>ابدأ مشروعك الآن</span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="w-5 h-5"
                            >
                                <path d="M4 13h10"/>
                                <path d="m10 7 6 6-6 6"/>
                            </svg>
                        </a>

                        <a
                            href="{{ route('engineer.works.public') }}"
                            class="px-8 py-4 text-lg font-bold transition border rounded-xl border-[#434655] bg-[#2d3449]/50 hover:bg-[#2d3449] active:scale-95"
                        >
                            تصفح مكتبة الأعمال
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- الإحصائيات --}}
        <section class="px-6 py-20">
            <div class="grid grid-cols-1 gap-8 mx-auto max-w-7xl sm:grid-cols-2 lg:grid-cols-4">
                <article class="p-6 text-center glass-card rounded-2xl reveal">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-blue-400/10 text-[#b4c5ff]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                        </svg>
                    </div>

                    <h3 class="text-4xl font-black text-[#b4c5ff]">
                        {{ $statistics['engineers'] }}
                    </h3>

                    <p class="mt-2 text-sm font-bold text-[#c3c6d7]">
                        مهندس فعّال
                    </p>
                </article>

                <article class="p-6 text-center glass-card rounded-2xl reveal">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-pink-400/10 text-[#ffb1c7]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/>
                        </svg>
                    </div>

                    <h3 class="text-4xl font-black text-[#ffb1c7]">
                        {{ $statistics['consultations'] }}
                    </h3>

                    <p class="mt-2 text-sm font-bold text-[#c3c6d7]">
                        استشارة مدفوعة
                    </p>
                </article>

                <article class="p-6 text-center glass-card rounded-2xl reveal">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-purple-400/10 text-[#d2bbff]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                            <path d="m5 12 4 4L19 6"/>
                            <circle cx="12" cy="12" r="9"/>
                        </svg>
                    </div>

                    <h3 class="text-4xl font-black text-[#d2bbff]">
                        {{ $statistics['completed'] }}
                    </h3>

                    <p class="mt-2 text-sm font-bold text-[#c3c6d7]">
                        مشروع مكتمل
                    </p>
                </article>

                <article class="p-6 text-center glass-card rounded-2xl reveal">
                    <div class="flex items-center justify-center mx-auto mb-4 w-14 h-14 rounded-2xl bg-cyan-400/10 text-cyan-300">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                            <rect x="3" y="3" width="18" height="18" rx="3"/>
                            <path d="m7 15 3-3 2 2 4-5 2 3"/>
                        </svg>
                    </div>

                    <h3 class="text-4xl font-black text-cyan-300">
                        {{ $statistics['works'] }}
                    </h3>

                    <p class="mt-2 text-sm font-bold text-[#c3c6d7]">
                        عمل منشور
                    </p>
                </article>
            </div>
        </section>

        {{-- الخدمات --}}
        <section
            id="services"
            class="px-6 py-24 bg-[#131b2e]/30"
        >
            <div class="mx-auto max-w-7xl">
                <div class="mb-16 text-center reveal">
                    <h2 class="mb-4 text-4xl font-black">
                        خدماتنا الهندسية المتكاملة
                    </h2>

                    <p class="max-w-xl mx-auto text-[#c3c6d7]">
                        نقدم حلولًا هندسية مبتكرة تغطي احتياجات مشروعك من التخطيط إلى التنفيذ.
                    </p>
                </div>

                @php
$services = [
    [
        'title' => 'التصميم المعماري',
        'description' => 'تصاميم عصرية تجمع بين الجمال والوظيفة، مع مراعاة أدق التفاصيل.',
        'icon' => 'architecture',
    ],
    [
        'title' => 'التصميم الإنشائي',
        'description' => 'دراسات إنشائية دقيقة تضمن أمان واستدامة المبنى.',
        'icon' => 'building',
    ],
    [
        'title' => 'التصميم الكهربائي',
        'description' => 'أنظمة كهربائية ذكية وآمنة تدعم كفاءة الطاقة.',
        'icon' => 'bolt',
    ],
    [
        'title' => 'الهندسة الميكانيكية',
        'description' => 'تصميم أنظمة التكييف والتهوية والصرف الصحي ومكافحة الحريق بكفاءة واحترافية.',
        'icon' => 'mechanical',
    ],
    [
        'title' => 'الحلول البرمجية',
        'description' => 'تطوير أنظمة إدارة وربط العمليات التقنية بالعمل الميداني.',
        'icon' => 'code',
    ],
    [
        'title' => 'التصميم الداخلي',
        'description' => 'ابتكار مساحات داخلية تعكس شخصيتك وتستغل المساحة بكفاءة.',
        'icon' => 'paint',
    ],
    [
        'title' => 'استشارات تقنية',
        'description' => 'دعم واستشارات تخصصية لمراجعة المخططات وحل المشكلات.',
        'icon' => 'support',
    ],
    [
    'title' => 'تصميم الواجهات',
    'description' => 'تصميم واجهات معمارية حديثة تجمع بين الهوية الجمالية والوظيفة وتناسب طبيعة المشروع.',
    'icon' => 'facade',
],
[
    'title' => 'تصميم اللاند سكيب',
    'description' => 'تصميم الحدائق والمساحات الخارجية والممرات والجلسات بما يحقق الراحة والجمال والاستدامة.',
    'icon' => 'landscape',
],
];
                @endphp

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $index => $service)
                        <article
                            class="p-8 glass-card rounded-3xl reveal"
                            style="transition-delay: {{ $index * 80 }}ms"
                        >
                            <div class="flex items-center justify-center w-14 h-14 mb-6 rounded-2xl bg-blue-400/10 text-[#b4c5ff]">
                                @switch($service['icon'])
                                    @case('architecture')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                                        </svg>
                                        @break

                                    @case('building')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <rect x="4" y="3" width="16" height="18" rx="2"/>
                                            <path d="M8 7h2M14 7h2M8 11h2M14 11h2M8 15h2M14 15h2"/>
                                        </svg>
                                        @break

                                    @case('bolt')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <path d="m13 2-8 12h7l-1 8 8-12h-7l1-8Z"/>
                                        </svg>
                                        @break

                                    @case('code')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <path d="m8 9-4 3 4 3M16 9l4 3-4 3M14 5l-4 14"/>
                                        </svg>
                                        @break

                                    @case('paint')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <path d="m14 4 6 6-9 9H5v-6l9-9Z"/>
                                            <path d="m12 6 6 6"/>
                                        </svg>
                                        @break
@case('mechanical')
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        class="w-7 h-7"
    >
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
    </svg>
    @break
    @case('facade')
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        class="w-7 h-7"
    >
        <path d="M4 21V5h16v16"/>
        <path d="M8 21v-5h8v5"/>
        <path d="M8 9h2M14 9h2M8 13h2M14 13h2"/>
        <path d="M2 21h20"/>
    </svg>
    @break

@case('landscape')
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        class="w-7 h-7"
    >
        <path d="M3 20h18"/>
        <path d="M6 20v-6"/>
        <path d="M6 14c-2 0-3-1.5-3-3 0-2 1.5-3.5 3.5-3.5S10 9 10 11c0 1.5-1 3-4 3Z"/>
        <path d="M16 20v-8"/>
        <path d="M16 12c-2.5 0-4-1.8-4-4 0-2.5 1.8-4.5 4.5-4.5S21 5.5 21 8c0 2.2-1.5 4-5 4Z"/>
    </svg>
    @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-7 h-7">
                                            <path d="M4 12a8 8 0 0 1 16 0v5a3 3 0 0 1-3 3h-2v-7h5M4 12v5a3 3 0 0 0 3 3h2v-7H4"/>
                                        </svg>
                                @endswitch
                            </div>

                            <h3 class="mb-3 text-xl font-bold">
                                {{ $service['title'] }}
                            </h3>

                            <p class="text-sm leading-7 text-[#c3c6d7]">
                                {{ $service['description'] }}
                            </p>

                            @auth
                                @if (auth()->user()->role === 'customer')
                                    <a
                                        href="{{ route('consultations.create') }}"
                                        class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-[#b4c5ff]"
                                    >
                                        اطلب الخدمة

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                            <path d="m15 18-6-6 6-6"/>
                                        </svg>
                                    </a>
                                @endif
                            @else
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center gap-2 mt-6 text-sm font-bold text-[#b4c5ff]"
                                >
                                    ابدأ الآن

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                        <path d="m15 18-6-6 6-6"/>
                                    </svg>
                                </a>
                            @endauth
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- كيف نعمل --}}
        <section
            id="how-it-works"
            class="px-6 py-24"
        >
            <div class="mx-auto max-w-7xl">
                <div class="mb-20 text-center reveal">
                    <h2 class="mb-4 text-4xl font-black">
                        رحلتك نحو التميز
                    </h2>

                    <p class="text-[#c3c6d7]">
                        أربع خطوات بسيطة تفصلك عن بدء مشروع أحلامك
                    </p>
                </div>

                @php
                    $steps = [
                        [
                            'number' => '١',
                            'title' => 'اختر تخصصك',
                            'description' => 'حدد المجال الهندسي الذي يتناسب مع احتياجات مشروعك.',
                        ],
                        [
                            'number' => '٢',
                            'title' => 'أرسل التفاصيل',
                            'description' => 'زودنا بالمعلومات والمخططات الأولية للبدء في الدراسة.',
                        ],
                        [
                            'number' => '٣',
                            'title' => 'الدفع الإلكتروني',
                            'description' => 'ارفع إيصال الدفع ليتم مراجعته من الإدارة.',
                        ],
                        [
                            'number' => '٤',
                            'title' => 'استلم مشروعك',
                            'description' => 'احصل على ملفاتك بجودة عالية مع متابعة كاملة.',
                        ],
                    ];
                @endphp

                <div class="relative grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div class="absolute left-0 right-0 hidden h-px top-8 md:block bg-white/10"></div>

                    @foreach ($steps as $index => $step)
                        <article
                            class="relative z-10 flex flex-col items-center text-center reveal"
                            style="transition-delay: {{ $index * 100 }}ms"
                        >
                            <div
                                class="flex items-center justify-center w-16 h-16 mb-6 text-2xl font-bold border rounded-full {{
                                    $index === 0
                                        ? 'bg-[#b4c5ff] text-[#002a78] border-[#b4c5ff]'
                                        : 'bg-[#2d3449] text-[#b4c5ff] border-[#b4c5ff]/30'
                                }}"
                            >
                                {{ $step['number'] }}
                            </div>

                            <h3 class="mb-2 text-lg font-bold">
                                {{ $step['title'] }}
                            </h3>

                            <p class="px-4 text-sm leading-7 text-[#c3c6d7]">
                                {{ $step['description'] }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- أحدث الأعمال --}}
        <section
            id="works"
            class="px-6 py-24 bg-[#131b2e]/30"
        >
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col items-start justify-between gap-6 mb-16 md:flex-row md:items-end reveal">
                    <div>
                        <h2 class="mb-4 text-4xl font-black">
                            أحدث أعمال مهندسينا
                        </h2>

                        <p class="max-w-2xl text-[#c3c6d7]">
                            استكشف بعض المشاريع التي أضافها مهندسو CreativeHome وتمت مراجعتها واعتمادها.
                        </p>
                    </div>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="inline-flex items-center gap-2 font-bold text-[#b4c5ff]"
                    >
                        عرض جميع الأعمال

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($latestWorks as $work)
                        <article class="overflow-hidden glass-card rounded-3xl reveal">
                            <div class="relative h-64 overflow-hidden">
                                @if ($work->coverImage)
                                    <img
                                        src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                        alt="{{ $work->title }}"
                                        class="object-cover w-full h-full transition duration-700 hover:scale-110"
                                    >
                                @else
                                    <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-[#222a3d] to-[#0b1326]">
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            class="w-16 h-16 text-[#b4c5ff]"
                                        >
                                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                                        </svg>
                                    </div>
                                @endif

                                @if ($work->project_type)
                                    <span
                                        class="absolute px-3 py-2 text-xs font-bold border rounded-full top-4 right-4 border-white/10 bg-[#060e20]/80"
                                    >
                                        {{ $work->project_type }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-6">
                                <h3 class="text-xl font-bold">
                                    {{ $work->title }}
                                </h3>

                                <div class="flex items-center gap-3 mt-4">
                                    <div class="flex items-center justify-center w-11 h-11 font-bold rounded-full bg-blue-600/20 text-[#b4c5ff]">
                                        {{ mb_substr($work->engineer?->name ?? 'م', 0, 1) }}
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold">
                                            {{ $work->engineer?->name ?? 'مهندس CreativeHome' }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#c3c6d7]">
                                            {{ $work->location ?? 'الموقع غير محدد' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-3 mt-6">
                                    <a
                                        href="{{ route('engineer.works.show', $work) }}"
                                        class="flex items-center justify-center flex-1 px-5 py-3 font-bold text-white bg-blue-600 rounded-xl"
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
                                                class="flex items-center justify-center w-12 h-12 border rounded-xl border-white/10 bg-white/5"
                                                title="اطلب هذا المهندس"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-5 h-5">
                                                    <path d="M4 5h16v14H4z"/>
                                                    <path d="m4 7 8 6 8-6"/>
                                                </svg>
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="p-12 text-center col-span-full glass-card rounded-3xl">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                class="w-14 h-14 mx-auto mb-4 text-[#b4c5ff]"
                            >
                                <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                            </svg>

                            <h3 class="text-xl font-bold">
                                لا توجد أعمال منشورة حاليًا
                            </h3>

                            <p class="mt-3 text-[#c3c6d7]">
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
            class="px-6 py-24"
        >
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col items-start justify-between gap-6 mb-16 md:flex-row md:items-end reveal">
                    <div>
                        <h2 class="mb-4 text-4xl font-black">
                            نخبة المهندسين
                        </h2>

                        <p class="text-[#c3c6d7]">
                            تعاون مع خبراء معتمدين ذوي خبرة واسعة في مجالات متعددة.
                        </p>
                    </div>

                    <a
                        href="{{ route('engineer.works.public') }}"
                        class="inline-flex items-center gap-2 font-bold text-[#b4c5ff]"
                    >
                        عرض جميع المهندسين

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($featuredEngineers as $engineer)
                        <article class="p-6 glass-card rounded-3xl reveal">
                            <div class="flex items-center gap-4 mb-6">
                                @if ($engineer->profile_photo)
                                    <img
                                        src="{{ asset('storage/' . $engineer->profile_photo) }}"
                                        alt="{{ $engineer->name }}"
                                        class="object-cover w-16 h-16 border rounded-full border-white/10"
                                    >
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 text-xl font-black rounded-full bg-blue-600/20 text-[#b4c5ff]">
                                        {{ mb_substr($engineer->name, 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <h3 class="text-lg font-bold">
                                        {{ $engineer->name }}
                                    </h3>

                                    <p class="mt-1 text-xs font-bold text-[#b4c5ff]">
                                        مهندس معتمد في الوليد الهندسي
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between px-2 mb-6">
                                <div class="text-center">
                                    <span class="block text-lg font-bold">
                                        {{ $engineer->engineerWorks->count() }}
                                    </span>

                                    <span class="text-[10px] text-[#c3c6d7]">
                                        أعمال
                                    </span>
                                </div>

                                <div class="w-px h-8 bg-white/10"></div>

                                <div class="text-center">
                                    <span class="block text-lg font-bold">
                                        5.0
                                    </span>

                                    <span class="text-[10px] text-[#b4c5ff]">
                                        تقييم
                                    </span>
                                </div>

                                <div class="w-px h-8 bg-white/10"></div>

                                <div class="text-center">
                                    <span class="block text-lg font-bold">
                                        نشط
                                    </span>

                                    <span class="text-[10px] text-[#c3c6d7]">
                                        الحالة
                                    </span>
                                </div>
                            </div>

                            @auth
                                @if (auth()->user()->role === 'customer')
                                    <a
                                        href="{{ route(
                                            'consultations.create-for-engineer',
                                            $engineer
                                        ) }}"
                                        class="flex items-center justify-center w-full px-5 py-3 font-bold rounded-xl bg-[#2d3449] hover:bg-blue-600/20"
                                    >
                                        اطلب هذا المهندس
                                    </a>
                                @endif
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="flex items-center justify-center w-full px-5 py-3 font-bold rounded-xl bg-[#2d3449] hover:bg-blue-600/20"
                                >
                                    سجّل لاختيار المهندس
                                </a>
                            @endauth
                        </article>
                    @empty
                        <div class="p-10 text-center col-span-full glass-card rounded-3xl">
                            لا يوجد مهندسون متاحون حاليًا.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="px-6 py-24">
            <div class="mx-auto max-w-7xl">
                <div
                    class="relative p-12 overflow-hidden text-center border rounded-[2rem] border-white/10 bg-gradient-to-br from-[#171f33] to-[#222a3d]"
                >
                    <div class="absolute rounded-full -top-20 -right-20 w-60 h-60 bg-blue-600/20 blur-3xl"></div>
                    <div class="absolute rounded-full -bottom-20 -left-20 w-60 h-60 bg-purple-600/20 blur-3xl"></div>

                    <div class="relative z-10">
                        <h2 class="mb-6 text-4xl font-black">
                            مستعد لبدء مشروعك؟
                        </h2>

                        <p class="max-w-2xl mx-auto mb-10 text-lg text-[#c3c6d7]">
                            انضم إلى الوليد الهندسي واحصل على استشارة هندسية احترافية من نخبة المهندسين.
                        </p>

                        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                            @auth
                                @if (auth()->user()->role === 'customer')
                                    <a
                                        href="{{ route('engineer.works.public') }}"
                                        class="px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl"
                                    >
                                        اختر مهندسًا
                                    </a>

                                    <a
                                        href="{{ route('consultations.create') }}"
                                        class="px-8 py-4 text-lg font-bold border rounded-xl border-white/10 bg-white/5"
                                    >
                                        طلب مباشر
                                    </a>
                                @else
                                    <a
                                        href="{{ route('dashboard') }}"
                                        class="px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl"
                                    >
                                        لوحة التحكم
                                    </a>
                                @endif
                            @else
                                <a
                                    href="{{ route('register') }}"
                                    class="px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-xl"
                                >
                                    إنشاء حساب مجاني
                                </a>

                                <a
                                    href="{{ route('login') }}"
                                    class="px-8 py-4 text-lg font-bold border rounded-xl border-white/10 bg-white/5"
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
    <footer class="px-6 py-16 border-t bg-[#060e20] border-white/10">
        <div class="grid gap-12 mx-auto max-w-7xl md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-blue-600/20 text-[#b4c5ff]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-6 h-6">
                            <path d="M3 11.5 12 4l9 7.5"/>
                            <path d="M5.5 10.5V21h13V10.5"/>
                            <path d="M9 21v-6h6v6"/>
                        </svg>
                    </span>

                    <span class="text-xl font-black text-[#b4c5ff]">
                        الوليد الهندسي
                    </span>
                </div>

                <p class="max-w-md mt-5 leading-8 text-[#c3c6d7]">
                    منصة هندسية متكاملة تجمع العملاء والمهندسين وتسهّل طلب الاستشارات ومتابعة المشاريع.
                </p>
            </div>

            <div>
                <h3 class="mb-5 font-bold">
                    روابط سريعة
                </h3>

                <div class="space-y-3 text-sm text-[#c3c6d7]">
                    <a href="#services" class="block hover:text-white">خدماتنا</a>
                    <a href="#works" class="block hover:text-white">أعمالنا</a>
                    <a href="#engineers" class="block hover:text-white">المهندسون</a>
                </div>
            </div>

            <div>
                <h3 class="mb-5 font-bold">
                    الحساب
                </h3>

                <div class="space-y-3 text-sm text-[#c3c6d7]">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block hover:text-white">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block hover:text-white">
                            تسجيل الدخول
                        </a>

                        <a href="{{ route('register') }}" class="block hover:text-white">
                            إنشاء حساب
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="pt-8 mt-12 text-sm text-center border-t border-white/10 text-[#8d90a0]">
            © {{ now()->year }} الوليد الهندسي. جميع الحقوق محفوظة.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuButton =
                document.getElementById(
                    'welcome-mobile-menu-button'
                );

            const menu =
                document.getElementById(
                    'welcome-mobile-menu'
                );

            const openIcon =
                document.getElementById(
                    'welcome-menu-open-icon'
                );

            const closeIcon =
                document.getElementById(
                    'welcome-menu-close-icon'
                );

            const closeMenu = () => {
                menu?.classList.add('hidden');
                openIcon?.classList.remove('hidden');
                closeIcon?.classList.add('hidden');
                menuButton?.setAttribute(
                    'aria-expanded',
                    'false'
                );
            };

            menuButton?.addEventListener(
                'click',
                function () {
                    const isOpen =
                        ! menu.classList.contains(
                            'hidden'
                        );

                    menu.classList.toggle(
                        'hidden'
                    );

                    openIcon.classList.toggle(
                        'hidden',
                        ! isOpen
                    );

                    closeIcon.classList.toggle(
                        'hidden',
                        isOpen
                    );

                    menuButton.setAttribute(
                        'aria-expanded',
                        String(! isOpen)
                    );
                }
            );

            document
                .querySelectorAll(
                    '[data-welcome-mobile-link]'
                )
                .forEach((link) => {
                    link.addEventListener(
                        'click',
                        closeMenu
                    );
                });

            const observer =
                new IntersectionObserver(
                    (entries) => {
                        entries.forEach(
                            (entry) => {
                                if (
                                    entry.isIntersecting
                                ) {
                                    entry.target
                                        .classList
                                        .add('active');

                                    observer.unobserve(
                                        entry.target
                                    );
                                }
                            }
                        );
                    },
                    {
                        threshold: 0.12,
                    }
                );

            document
                .querySelectorAll('.reveal')
                .forEach((element) => {
                    observer.observe(element);
                });

            const canvas =
                document.getElementById(
                    'creativehome-shader'
                );

            if (! canvas) {
                return;
            }

            const gl =
                canvas.getContext('webgl')
                || canvas.getContext(
                    'experimental-webgl'
                );

            if (! gl) {
                return;
            }

            const syncSize = () => {
                const width =
                    canvas.clientWidth
                    || window.innerWidth;

                const height =
                    canvas.clientHeight
                    || window.innerHeight;

                if (
                    canvas.width !== width
                    || canvas.height !== height
                ) {
                    canvas.width = width;
                    canvas.height = height;
                }
            };

            const vertexShaderSource = `
                attribute vec2 a_position;
                varying vec2 v_texCoord;

                void main() {
                    v_texCoord =
                        a_position * 0.5 + 0.5;

                    gl_Position =
                        vec4(
                            a_position,
                            0.0,
                            1.0
                        );
                }
            `;

            const fragmentShaderSource = `
                precision highp float;

                varying vec2 v_texCoord;

                uniform float u_time;
                uniform vec2 u_resolution;

                void main() {
                    vec2 uv = v_texCoord;

                    float noise =
                        sin(
                            uv.x * 3.0
                            + u_time * 0.5
                        )
                        * cos(
                            uv.y * 2.0
                            + u_time * 0.3
                        );

                    noise +=
                        sin(
                            uv.y * 5.0
                            - u_time * 0.4
                        ) * 0.5;

                    vec3 color1 =
                        vec3(
                            0.043,
                            0.075,
                            0.149
                        );

                    vec3 color2 =
                        vec3(
                            0.145,
                            0.388,
                            0.922
                        );

                    vec3 color3 =
                        vec3(
                            0.537,
                            0.122,
                            0.941
                        );

                    vec3 finalColor =
                        mix(
                            color1,
                            color2,
                            noise * 0.2 + 0.1
                        );

                    finalColor =
                        mix(
                            finalColor,
                            color3,
                            clamp(
                                sin(
                                    u_time * 0.2
                                    + uv.x * 2.0
                                ) * 0.1,
                                0.0,
                                1.0
                            )
                        );

                    gl_FragColor =
                        vec4(
                            finalColor,
                            1.0
                        );
                }
            `;

            const compileShader = (
                type,
                source
            ) => {
                const shader =
                    gl.createShader(type);

                gl.shaderSource(
                    shader,
                    source
                );

                gl.compileShader(shader);

                return shader;
            };

            const program =
                gl.createProgram();

            gl.attachShader(
                program,
                compileShader(
                    gl.VERTEX_SHADER,
                    vertexShaderSource
                )
            );

            gl.attachShader(
                program,
                compileShader(
                    gl.FRAGMENT_SHADER,
                    fragmentShaderSource
                )
            );

            gl.linkProgram(program);
            gl.useProgram(program);

            const buffer =
                gl.createBuffer();

            gl.bindBuffer(
                gl.ARRAY_BUFFER,
                buffer
            );

            gl.bufferData(
                gl.ARRAY_BUFFER,
                new Float32Array([
                    -1,
                    -1,
                    1,
                    -1,
                    -1,
                    1,
                    1,
                    1,
                ]),
                gl.STATIC_DRAW
            );

            const position =
                gl.getAttribLocation(
                    program,
                    'a_position'
                );

            gl.enableVertexAttribArray(
                position
            );

            gl.vertexAttribPointer(
                position,
                2,
                gl.FLOAT,
                false,
                0,
                0
            );

            const timeUniform =
                gl.getUniformLocation(
                    program,
                    'u_time'
                );

            const resolutionUniform =
                gl.getUniformLocation(
                    program,
                    'u_resolution'
                );

            const render = (time) => {
                syncSize();

                gl.viewport(
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                gl.uniform1f(
                    timeUniform,
                    time * 0.001
                );

                gl.uniform2f(
                    resolutionUniform,
                    canvas.width,
                    canvas.height
                );

                gl.drawArrays(
                    gl.TRIANGLE_STRIP,
                    0,
                    4
                );

                requestAnimationFrame(
                    render
                );
            };

            window.addEventListener(
                'resize',
                syncSize
            );

            requestAnimationFrame(
                render
            );
        });
    </script>

    {{-- مساعد الوليد الهندسي للزائر فقط --}}
    @guest
        <x-support-bot mode="guest" />
    @endguest

    {{-- لأن welcome صفحة مستقلة ولا تستخدم app-layout --}}
    @stack('styles')
    @stack('scripts')

</body>
</html>
