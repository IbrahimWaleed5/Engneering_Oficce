@props([
    'title',
    'description' => null,
    'icon' => '✨',
])

<div class="flex flex-col gap-5 mb-8 md:flex-row md:items-center md:justify-between">

    <div class="fade-up">

        <div class="flex items-center gap-3">

            <div
                class="flex items-center justify-center text-2xl border w-14 h-14 rounded-2xl border-white/10 bg-white/5"
            >
                {{ $icon }}
            </div>

            <div>

                <h1 class="text-2xl font-extrabold text-white md:text-3xl">
                    {{ $title }}
                </h1>

                @if ($description)

                    <p class="mt-2 text-sm text-slate-400 md:text-base">
                        {{ $description }}
                    </p>

                @endif

            </div>

        </div>

    </div>

    @isset($actions)

        <div class="delay-100 fade-up">
            {{ $actions }}
        </div>

    @endisset

</div>
