<x-app-layout>

    <div
        x-data="{
            search: '',
            selectedType: 'all'
        }"
        class="relative py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <x-page-header
                title="مكتبة أعمال المهندسين"
                description="استكشف مشاريع المهندسين واختر صاحب الخبرة المناسبة لمشروعك"
                icon="🏗️"
            >
                <x-slot name="actions">

                    @auth

                        @if (auth()->user()->role === 'engineer')

                            <a
                                href="{{ route('engineer.works.create') }}"
                                class="primary-button"
                            >
                                <span>➕</span>
                                إضافة عمل جديد
                            </a>

                        @endif

                    @endauth

                </x-slot>
            </x-page-header>

            <x-alerts />

            {{-- شريط البحث والفلاتر --}}

            <div class="p-5 mb-10 glass-panel rounded-3xl fade-up">

                <div class="grid gap-4 lg:grid-cols-[1fr_auto]">

                    <div class="relative">

                        <span
                            class="absolute text-xl -translate-y-1/2 pointer-events-none top-1/2 right-4"
                        >
                            🔎
                        </span>

                        <input
                            x-model="search"
                            type="search"
                            placeholder="ابحث باسم المشروع أو المهندس أو الموقع..."
                            class="pr-12 form-control"
                        >

                    </div>

                    <div class="flex flex-wrap gap-2">

                        <button
                            type="button"
                            @click="selectedType = 'all'"
                            :class="selectedType === 'all'
                                ? 'bg-cyan-500 text-slate-950'
                                : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-4 py-3 text-sm font-bold transition rounded-xl"
                        >
                            جميع الأعمال
                        </button>

                        <button
                            type="button"
                            @click="selectedType = 'architecture'"
                            :class="selectedType === 'architecture'
                                ? 'bg-cyan-500 text-slate-950'
                                : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-4 py-3 text-sm font-bold transition rounded-xl"
                        >
                            معماري
                        </button>

                        <button
                            type="button"
                            @click="selectedType = 'structural'"
                            :class="selectedType === 'structural'
                                ? 'bg-cyan-500 text-slate-950'
                                : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-4 py-3 text-sm font-bold transition rounded-xl"
                        >
                            إنشائي
                        </button>

                        <button
                            type="button"
                            @click="selectedType = 'interior'"
                            :class="selectedType === 'interior'
                                ? 'bg-cyan-500 text-slate-950'
                                : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                            class="px-4 py-3 text-sm font-bold transition rounded-xl"
                        >
                            داخلي
                        </button>

                    </div>

                </div>

            </div>

            {{-- البطاقات --}}

            <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-3">

                @forelse ($works as $work)

                    @php
                        $searchText = strtolower(
                            ($work->title ?? '') . ' ' .
                            ($work->engineer?->name ?? '') . ' ' .
                            ($work->location ?? '') . ' ' .
                            ($work->project_type ?? '')
                        );

                        $projectType = strtolower(
                            $work->project_type ?? ''
                        );
                    @endphp

                    <article
                        x-show="
                            (
                                search === ''
                                || @js($searchText).includes(search.toLowerCase())
                            )
                            &&
                            (
                                selectedType === 'all'
                                || (
                                    selectedType === 'architecture'
                                    && (
                                        @js($projectType).includes('معمار')
                                        || @js($projectType).includes('architect')
                                    )
                                )
                                || (
                                    selectedType === 'structural'
                                    && (
                                        @js($projectType).includes('إنشائ')
                                        || @js($projectType).includes('struct')
                                    )
                                )
                                || (
                                    selectedType === 'interior'
                                    && (
                                        @js($projectType).includes('داخل')
                                        || @js($projectType).includes('interior')
                                    )
                                )
                            )
                        "
                        x-transition
                        class="group glass-card rounded-[2rem]"
                    >

                        <div class="relative overflow-hidden h-72">

                            @if ($work->coverImage)

                                <img
                                    src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                    alt="{{ $work->title }}"
                                    class="object-cover w-full h-full transition duration-700 group-hover:scale-110"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-full h-full text-6xl bg-gradient-to-br from-slate-800 to-slate-950"
                                >
                                    🏢
                                </div>

                            @endif

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/10 to-transparent"
                            ></div>

                            <div class="absolute flex items-center gap-2 top-4 right-4">

                                @if ($work->project_type)

                                    <span
                                        class="px-3 py-2 text-xs font-bold border rounded-full border-white/10 bg-slate-950/75 backdrop-blur-xl"
                                    >
                                        {{ $work->project_type }}
                                    </span>

                                @endif

                                @if ($work->completion_year)

                                    <span
                                        class="px-3 py-2 text-xs font-bold border rounded-full border-white/10 bg-slate-950/75 backdrop-blur-xl"
                                    >
                                        {{ $work->completion_year }}
                                    </span>

                                @endif

                            </div>

                            <div class="absolute right-5 bottom-5 left-5">

                                <h2 class="text-2xl font-black text-white">
                                    {{ $work->title }}
                                </h2>

                                <div class="flex items-center gap-2 mt-3 text-sm text-slate-300">

                                    <span>📍</span>

                                    <span>
                                        {{ $work->location ?? 'الموقع غير محدد' }}
                                    </span>

                                </div>

                            </div>

                        </div>

                        <div class="relative z-10 p-6">

                            <div class="flex items-center justify-between gap-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex items-center justify-center flex-none w-12 h-12 font-black border rounded-full border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500"
                                    >
                                        {{ mb_substr(
                                            $work->engineer?->name ?? 'م',
                                            0,
                                            1
                                        ) }}
                                    </div>

                                    <div>

                                        <p class="font-extrabold text-white">
                                            {{ $work->engineer?->name ?? 'مهندس المكتب' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            مهندس معتمد في المنصة
                                        </p>

                                        @if ($work->engineer?->employeeProfile?->specialty)

                                            <span
                                                class="inline-flex items-center px-3 py-1 mt-2 text-xs font-bold border rounded-full border-cyan-500/20 bg-cyan-500/10 text-cyan-300"
                                            >
                                                {{ $work->engineer->employeeProfile->specialty->name }}
                                            </span>

                                        @else

                                            <p class="mt-2 text-xs text-slate-500">
                                                لم يحدد التخصص بعد
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div
                                    class="flex items-center gap-1 text-sm font-bold text-yellow-300"
                                >
                                    <span>★</span>
                                    <span>5.0</span>
                                </div>

                            </div>

                            @if ($work->description)

                                <p class="mt-5 leading-7 text-slate-400 line-clamp-3">
                                    {{ $work->description }}
                                </p>

                            @endif

                            <div class="grid grid-cols-2 gap-3 mt-6">

                                <a
                                    href="{{ route('engineer.works.show', $work) }}"
                                    class="primary-button"
                                >
                                    عرض التفاصيل
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
                                            class="secondary-button"
                                        >
                                            طلب المهندس
                                        </a>

                                    @elseif (auth()->user()->role === 'engineer')

                                        <span
                                            class="cursor-not-allowed secondary-button opacity-60"
                                        >
                                            مكتبة عامة
                                        </span>

                                    @else

                                        <a
                                            href="{{ route('login') }}"
                                            class="secondary-button"
                                        >
                                            سجّل للطلب
                                        </a>

                                    @endif

                                @else

                                    <a
                                        href="{{ route('login') }}"
                                        class="secondary-button"
                                    >
                                        سجّل للطلب
                                    </a>

                                @endauth

                            </div>

                        </div>

                    </article>

                @empty

                    <div
                        class="col-span-full p-14 text-center glass-panel rounded-[2rem]"
                    >

                        <div class="mb-5 text-6xl">
                            🏗️
                        </div>

                        <h2 class="text-2xl font-black">
                            لا توجد أعمال منشورة
                        </h2>

                        <p class="mt-3 text-slate-400">
                            ستظهر هنا أعمال المهندسين بعد مراجعتها واعتمادها.
                        </p>

                    </div>

                @endforelse

            </div>

            @if ($works->hasPages())

                <div class="mt-12">
                    {{ $works->links() }}
                </div>

            @endif

        </div>

    </div>

</x-app-layout>
