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
        {{ config('app.name', 'المكتب الهندسي') }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=almarai:400,500,700,800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body
    class="min-h-screen font-sans antialiased text-slate-100"
>

    <div class="relative min-h-screen overflow-x-hidden">

        <div
            class="fixed rounded-full pointer-events-none -top-32 -right-32 h-96 w-96 bg-blue-600/10 blur-3xl"
        ></div>

        <div
            class="fixed rounded-full pointer-events-none -bottom-36 -left-28 h-96 w-96 bg-cyan-500/10 blur-3xl"
        ></div>

        @include('layouts.navigation')

        @isset($header)

            <header class="border-b border-white/5 bg-slate-950/40 backdrop-blur-xl">

                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>

            </header>

        @endisset
        @if(auth()->user()->role === 'engineer')

    <a
        href="{{ route('engineer.works.mine') }}"
        class="nav-link"
    >
        أعمالي
    </a>

@endif

      <main class="relative z-10">

    @if(isset($slot))
        {{ $slot }}
    @else
        @yield('content')
    @endif

</main>

    </div>


</body>

</html>
