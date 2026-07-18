@extends('layouts.app')

@section('content')

<div class="flex items-center justify-center min-h-screen px-6 bg-slate-950" dir="rtl">

    <div class="max-w-xl w-full text-center p-10 glass-panel-strong rounded-[2rem]">

        <div class="mb-6 text-7xl">🔒</div>

        <h1 class="text-6xl font-black text-red-400">
            403
        </h1>

        <h2 class="mt-4 text-2xl font-bold text-white">
            ليس لديك صلاحية للوصول
        </h2>

        <p class="mt-4 leading-8 text-slate-400">
            عذرًا، لا تملك الصلاحيات اللازمة للوصول إلى هذه الصفحة.
        </p>

        <a
            href="{{ route('dashboard') }}"
            class="inline-block mt-8 primary-button"
        >
            العودة إلى لوحة التحكم
        </a>

    </div>

</div>

@endsection
