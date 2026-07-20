<x-app-layout>

    <div
        x-data="{
            activeImage: 0,
            lightboxOpen: false
        }"
        class="py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <x-alerts />

            <div class="grid gap-8 lg:grid-cols-[1.25fr_0.75fr]">

                {{-- معرض الصور --}}

                <section class="p-5 glass-panel rounded-[2rem] fade-up">

                    @if ($engineerWork->images->count() > 0)

                        <div class="relative overflow-hidden h-[520px] rounded-3xl">

                            @foreach ($engineerWork->images as $index => $image)

                                <img
                                    x-show="activeImage === {{ $index }}"
                                    x-transition.opacity
                                    src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="{{ $image->caption ?? $engineerWork->title }}"
                                    @click="lightboxOpen = true"
                                    class="object-cover w-full h-full cursor-zoom-in"
                                >

                            @endforeach

                            <button
                                type="button"
                                @click="
                                    activeImage =
                                        activeImage === 0
                                            ? {{ $engineerWork->images->count() - 1 }}
                                            : activeImage - 1
                                "
                                class="absolute flex items-center justify-center w-12 h-12 -translate-y-1/2 border rounded-full top-1/2 right-5 border-white/10 bg-slate-950/65 backdrop-blur-xl hover:bg-cyan-500 hover:text-slate-950"
                            >
                                ›
                            </button>

                            <button
                                type="button"
                                @click="
                                    activeImage =
                                        activeImage === {{ $engineerWork->images->count() - 1 }}
                                            ? 0
                                            : activeImage + 1
                                "
                                class="absolute flex items-center justify-center w-12 h-12 -translate-y-1/2 border rounded-full top-1/2 left-5 border-white/10 bg-slate-950/65 backdrop-blur-xl hover:bg-cyan-500 hover:text-slate-950"
                            >
                                ‹
                            </button>

                            <div
                                class="absolute px-4 py-2 text-sm font-bold border rounded-full bottom-5 left-5 border-white/10 bg-slate-950/70 backdrop-blur-xl"
                            >
                                <span x-text="activeImage + 1"></span>
                                /
                                {{ $engineerWork->images->count() }}
                            </div>

                        </div>

                        <div class="grid grid-cols-4 gap-3 mt-4 sm:grid-cols-6">

                            @foreach ($engineerWork->images as $index => $image)

                                <button
                                    type="button"
                                    @click="activeImage = {{ $index }}"
                                    :class="activeImage === {{ $index }}
                                        ? 'border-cyan-400 ring-2 ring-cyan-400/20'
                                        : 'border-white/10 opacity-65 hover:opacity-100'"
                                    class="h-20 overflow-hidden transition border rounded-xl"
                                >
                                    <img
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt=""
                                        class="object-cover w-full h-full"
                                    >
                                </button>

                            @endforeach

                        </div>

                    @else

                        <div
                            class="flex flex-col items-center justify-center h-[520px] rounded-3xl bg-white/5"
                        >
                            <div class="text-6xl">
                                🏗️
                            </div>

                            <p class="mt-4 text-slate-400">
                                لا توجد صور لهذا المشروع
                            </p>
                        </div>

                    @endif

                </section>

                {{-- معلومات المشروع --}}

                <aside class="space-y-6 delay-100 fade-up">

                    <div class="p-7 glass-panel rounded-[2rem]">

                        <div class="flex flex-wrap gap-2">

                            @if ($engineerWork->project_type)

                                <span
                                    class="text-blue-200 status-badge bg-blue-500/10"
                                >
                                    {{ $engineerWork->project_type }}
                                </span>

                            @endif

                            @if ($engineerWork->completion_year)

                                <span
                                    class="text-purple-200 status-badge bg-purple-500/10"
                                >
                                    {{ $engineerWork->completion_year }}
                                </span>

                            @endif

                        </div>

                        <h1 class="mt-5 text-3xl font-black leading-tight text-white">
                            {{ $engineerWork->title }}
                        </h1>

                        @if ($engineerWork->location)

                            <p class="flex items-center gap-2 mt-4 text-slate-400">
                                <span>📍</span>
                                {{ $engineerWork->location }}
                            </p>

                        @endif

                        @if ($engineerWork->description)

                            <p class="mt-6 leading-8 text-slate-300">
                                {{ $engineerWork->description }}
                            </p>

                        @endif

                    </div>

                    <div class="p-6 glass-card rounded-[2rem]">

                        <p class="mb-4 text-sm font-bold text-slate-400">
                            صاحب المشروع
                        </p>

                        <div class="flex items-center gap-4">

                            <div
                                class="flex items-center justify-center flex-none w-16 h-16 text-2xl font-black border rounded-full border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500"
                            >
                                {{ mb_substr(
                                    $engineerWork->engineer?->name ?? 'م',
                                    0,
                                    1
                                ) }}
                            </div>

                            <div>

                                <h2 class="text-xl font-extrabold text-white">
                                    {{ $engineerWork->engineer?->name ?? 'مهندس المكتب' }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-400">
                                    مهندس فعّال ومعتمد
                                </p>

                                <div class="mt-3">
                                    <p class="text-xs text-slate-500">
                                        التخصص الهندسي
                                    </p>

                                    <span
                                        class="inline-flex items-center px-3 py-1 mt-1 text-xs font-bold border rounded-full border-cyan-500/20 bg-cyan-500/10 text-cyan-300"
                                    >
                                        {{
                                            $engineerWork
                                                ->engineer
                                                ?->employeeProfile
                                                ?->specialty
                                                ?->name
                                            ?? 'لم يحدد التخصص'
                                        }}
                                    </span>
                                </div>

                                <div class="flex mt-3 text-sm text-yellow-300">
                                    ★★★★★
                                </div>

                            </div>

                        </div>

                        @auth

                            @if (
                                auth()->user()->role === 'customer'
                                && $engineerWork->engineer
                            )

                                <a
                                    href="{{ route(
                                        'consultations.create-for-engineer',
                                        $engineerWork->engineer
                                    ) }}"
                                    class="flex w-full mt-6 primary-button"
                                >
                                    إرسال طلب لهذا المهندس
                                </a>

                            @elseif (auth()->user()->role !== 'customer')

                                <a
                                    href="{{ route('engineer.works.public') }}"
                                    class="flex w-full mt-6 secondary-button"
                                >
                                    العودة إلى المكتبة
                                </a>

                            @endif

                        @else

                            <a
                                href="{{ route('login') }}"
                                class="flex w-full mt-6 primary-button"
                            >
                                سجّل لإرسال طلب
                            </a>

                        @endauth

                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div class="p-5 text-center glass-card rounded-2xl">

                            <p class="text-2xl font-black text-cyan-300">
                                {{ $engineerWork->images->count() }}
                            </p>

                            <p class="mt-2 text-xs text-slate-400">
                                صور المشروع
                            </p>

                        </div>

                        <div class="p-5 text-center glass-card rounded-2xl">

                            <p class="text-2xl font-black text-green-300">
                                معتمد
                            </p>

                            <p class="mt-2 text-xs text-slate-400">
                                حالة النشر
                            </p>

                        </div>

                    </div>

                </aside>

            </div>

        </div>

        {{-- Lightbox --}}

        @if ($engineerWork->images->count() > 0)

            <div
                x-cloak
                x-show="lightboxOpen"
                x-transition.opacity
                @keydown.escape.window="lightboxOpen = false"
                class="fixed inset-0 z-[100] flex items-center justify-center p-5 bg-slate-950/95 backdrop-blur-xl"
            >

                <button
                    type="button"
                    @click="lightboxOpen = false"
                    class="absolute flex items-center justify-center w-12 h-12 text-xl border rounded-full top-6 left-6 border-white/10 bg-white/5"
                >
                    ✕
                </button>

                @foreach ($engineerWork->images as $index => $image)

                    <img
                        x-show="activeImage === {{ $index }}"
                        src="{{ asset('storage/' . $image->image_path) }}"
                        alt="{{ $engineerWork->title }}"
                        class="max-h-[88vh] max-w-[92vw] object-contain rounded-2xl"
                    >

                @endforeach

            </div>

        @endif

    </div>

</x-app-
