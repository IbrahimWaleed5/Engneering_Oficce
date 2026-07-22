<x-app-layout>

    @php
        $ratingAverage = (float) (
            $user->received_engineer_reviews_avg_rating
            ?? 0
        );

        $reviewsCount = (int) (
            $user->received_engineer_reviews_count
            ?? 0
        );
    @endphp

    <div
        class="py-10"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- معلومات المهندس --}}
            <section
                class="p-8 glass-panel-strong rounded-[2rem]"
            >

                <div
                    class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
                >

                    <div
                        class="flex flex-col items-center gap-6 sm:flex-row"
                    >

                        {{-- صورة المهندس --}}
                        <div
                            class="overflow-hidden rounded-full w-28 h-28 ring-4 ring-cyan-500/20"
                        >

                            @if ($user->profile_photo)

                                <img
                                    src="{{ asset(
                                        'storage/' .
                                        $user->profile_photo
                                    ) }}"
                                    alt="{{ $user->name }}"
                                    class="object-cover w-full h-full"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-full h-full text-4xl font-black text-white bg-gradient-to-br from-blue-600 to-cyan-500"
                                >
                                    {{ mb_substr(
                                        $user->name,
                                        0,
                                        1
                                    ) }}
                                </div>

                            @endif

                        </div>

                        {{-- الاسم والتخصص --}}
                        <div class="text-center sm:text-right">

                            <h1
                                class="text-3xl font-black text-white"
                            >
                                {{ $user->name }}
                            </h1>

                            <p
                                class="mt-2 text-lg font-bold text-cyan-300"
                            >
                                {{ $user
                                    ->employeeProfile
                                    ?->specialty
                                    ?->name
                                    ?? 'التخصص غير محدد' }}
                            </p>

                            {{-- التقييم المختصر --}}
                            <div
                                class="flex flex-wrap items-center justify-center gap-2 mt-4 sm:justify-start"
                            >

                                <span
                                    class="inline-flex px-3 py-1 text-xs font-bold text-green-300 rounded-full bg-green-500/10"
                                >
                                    مهندس نشط
                                </span>

                                <span
                                    class="inline-flex px-3 py-1 text-xs font-bold rounded-full text-slate-300 bg-white/5"
                                >
                                    عضو منذ
                                    {{ $user->created_at?->format('Y') }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold text-yellow-300 rounded-full bg-yellow-500/10"
                                >
                                    ⭐
                                    {{ number_format(
                                        $ratingAverage,
                                        1
                                    ) }}

                                    من 5
                                </span>

                                <span
                                    class="inline-flex px-3 py-1 text-xs font-bold rounded-full text-slate-300 bg-white/5"
                                >
                                    {{ $reviewsCount }}
                                    تقييم
                                </span>

                            </div>

                        </div>

                    </div>

                    {{-- طلب استشارة --}}
                    @auth

                        @if (
                            auth()->id() !== $user->id
                            && in_array(
                                auth()->user()->role,
                                [
                                    'customer',
                                    'engineer',
                                    'admin',
                                ],
                                true
                            )
                        )

                            <a
                                href="{{ route(
                                    'consultations.create-for-engineer',
                                    $user
                                ) }}"
                                class="primary-button"
                            >
                                اطلب استشارة
                            </a>

                        @endif

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="primary-button"
                        >
                            سجل الدخول لطلب استشارة
                        </a>

                    @endauth

                </div>

            </section>

            {{-- الإحصائيات --}}
            <div
                class="grid gap-4 mt-8 sm:grid-cols-2 lg:grid-cols-4"
            >

                {{-- التخصص --}}
                <div
                    class="p-6 glass-panel-strong rounded-[2rem]"
                >

                    <p class="text-sm text-slate-400">
                        التخصص
                    </p>

                    <p
                        class="mt-3 text-xl font-black text-white"
                    >
                        {{ $user
                            ->employeeProfile
                            ?->specialty
                            ?->name
                            ?? 'غير محدد' }}
                    </p>

                </div>

                {{-- عدد المشاريع --}}
                <div
                    class="p-6 glass-panel-strong rounded-[2rem]"
                >

                    <p class="text-sm text-slate-400">
                        المشاريع المنشورة
                    </p>

                    <p
                        class="mt-3 text-3xl font-black text-cyan-300"
                    >
                        {{ $user->engineerWorks->count() }}
                    </p>

                </div>

                {{-- متوسط التقييم --}}
                <div
                    class="p-6 glass-panel-strong rounded-[2rem]"
                >

                    <p class="text-sm text-slate-400">
                        متوسط التقييم
                    </p>

                    <div
                        class="flex items-center gap-2 mt-3"
                    >

                        <p
                            class="text-3xl font-black text-yellow-300"
                        >
                            {{ number_format(
                                $ratingAverage,
                                1
                            ) }}
                        </p>

                        <span class="text-2xl text-yellow-400">
                            ★
                        </span>

                    </div>

                    <p class="mt-2 text-xs text-slate-500">
                        بناءً على
                        {{ $reviewsCount }}
                        تقييم
                    </p>

                </div>

                {{-- تاريخ الانضمام --}}
                <div
                    class="p-6 glass-panel-strong rounded-[2rem]"
                >

                    <p class="text-sm text-slate-400">
                        تاريخ الانضمام
                    </p>

                    <p
                        class="mt-3 text-xl font-black text-white"
                    >
                        {{ $user->created_at?->format(
                            'Y-m-d'
                        ) }}
                    </p>

                </div>

            </div>

            {{-- نبذة المهندس --}}
            <section
                class="p-8 mt-8 glass-panel-strong rounded-[2rem]"
            >

                <div class="flex items-center gap-3 mb-5">

                    <div
                        class="flex items-center justify-center text-xl w-11 h-11 rounded-2xl bg-cyan-500/10"
                    >
                        👨‍💼
                    </div>

                    <h2
                        class="text-2xl font-black text-white"
                    >
                        نبذة عن المهندس
                    </h2>

                </div>

                <p
                    class="text-base leading-8 whitespace-pre-line text-slate-300"
                >
                    {{ $user->employeeProfile?->bio
                        ?: 'لم تتم إضافة نبذة عن المهندس حتى الآن.' }}
                </p>

            </section>

            {{-- تقييمات وآراء العملاء --}}
            <section class="mt-10">

                <div
                    class="flex flex-wrap items-center justify-between gap-4 mb-6"
                >

                    <div>

                        <h2
                            class="text-2xl font-black text-white"
                        >
                            تقييمات وآراء العملاء
                        </h2>

                        <p
                            class="mt-1 text-sm text-slate-400"
                        >
                            تقييمات العملاء بعد إكمال الاستشارات
                        </p>

                    </div>

                    <div
                        class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-yellow-500/10"
                    >

                        <span
                            class="text-xl font-black text-yellow-300"
                        >
                            {{ number_format(
                                $ratingAverage,
                                1
                            ) }}
                        </span>

                        <span class="text-yellow-400">
                            ★
                        </span>

                        <span class="text-sm text-slate-400">
                            {{ $reviewsCount }}
                            تقييم
                        </span>

                    </div>

                </div>

                <div class="grid gap-5 lg:grid-cols-2">

                    @forelse (
                        $user->receivedEngineerReviews
                        as $review
                    )

                        <article
                            class="p-6 glass-panel-strong rounded-[2rem]"
                        >

                            <div
                                class="flex flex-wrap items-center justify-between gap-4"
                            >

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="flex items-center justify-center flex-none font-black text-white rounded-full w-11 h-11 bg-gradient-to-br from-purple-600 to-blue-600"
                                    >
                                        {{ mb_substr(
                                            $review
                                                ->customer
                                                ?->name
                                                ?? 'ع',
                                            0,
                                            1
                                        ) }}
                                    </div>

                                    <div>

                                        <p
                                            class="font-black text-white"
                                        >
                                            {{ $review
                                                ->customer
                                                ?->name
                                                ?? 'عميل' }}
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-slate-500"
                                        >
                                            {{ $review
                                                ->created_at
                                                ?->format(
                                                    'Y-m-d'
                                                ) }}
                                        </p>

                                    </div>

                                </div>

                                <div
                                    class="flex items-center gap-1 px-3 py-2 rounded-xl bg-yellow-500/10"
                                >

                                    @for (
                                        $star = 1;
                                        $star <= 5;
                                        $star++
                                    )

                                        <span
                                            class="{{ $star <= $review->rating
                                                ? 'text-yellow-400'
                                                : 'text-slate-700' }}"
                                        >
                                            ★
                                        </span>

                                    @endfor

                                    <span
                                        class="mr-2 text-sm font-black text-yellow-300"
                                    >
                                        {{ $review->rating }}/5
                                    </span>

                                </div>

                            </div>

                            @if ($review->comment)

                                <p
                                    class="mt-5 leading-8 text-slate-300"
                                >
                                    {{ $review->comment }}
                                </p>

                            @else

                                <p
                                    class="mt-5 text-sm text-slate-500"
                                >
                                    لم يكتب العميل تعليقًا.
                                </p>

                            @endif

                        </article>

                    @empty

                        <div
                            class="p-10 text-center glass-panel-strong rounded-[2rem] lg:col-span-2"
                        >

                            <div class="mb-4 text-5xl">
                                ⭐
                            </div>

                            <h3
                                class="text-xl font-black text-white"
                            >
                                لا توجد تقييمات بعد
                            </h3>

                            <p class="mt-2 text-slate-400">
                                ستظهر تقييمات العملاء هنا بعد
                                إكمال الاستشارات.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

            {{-- أعمال المهندس --}}
            <section class="mt-10">

                <div
                    class="flex flex-wrap items-center justify-between gap-4 mb-6"
                >

                    <div>

                        <h2
                            class="text-2xl font-black text-white"
                        >
                            أعمال المهندس
                        </h2>

                        <p
                            class="mt-1 text-sm text-slate-400"
                        >
                            المشاريع والأعمال الهندسية المنشورة
                        </p>

                    </div>

                    <span
                        class="inline-flex px-4 py-2 text-sm font-bold rounded-full text-cyan-300 bg-cyan-500/10"
                    >
                        {{ $user->engineerWorks->count() }}
                        مشروع
                    </span>

                </div>

                <div
                    class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
                >

                    @forelse ($user->engineerWorks as $work)

                        <article
                            class="overflow-hidden transition duration-300 glass-panel-strong rounded-[2rem] hover:-translate-y-1"
                        >

                            {{-- صورة العمل --}}
                            <a
                                href="{{ route(
                                    'engineer.works.show',
                                    $work
                                ) }}"
                                class="block"
                            >

                                <div
                                    class="relative overflow-hidden h-60 bg-slate-900"
                                >

                                    @if ($work->coverImage)

                                        <img
                                            src="{{ asset(
                                                'storage/' .
                                                $work
                                                    ->coverImage
                                                    ->image_path
                                            ) }}"
                                            alt="{{ $work->title }}"
                                            class="object-cover w-full h-full transition duration-500 hover:scale-105"
                                        >

                                    @else

                                        <div
                                            class="flex items-center justify-center w-full h-full text-5xl bg-gradient-to-br from-slate-900 to-slate-800"
                                        >
                                            🏗️
                                        </div>

                                    @endif

                                    <div
                                        class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/80 to-transparent"
                                    ></div>

                                </div>

                            </a>

                            <div class="p-6">

                                <a
                                    href="{{ route(
                                        'engineer.works.show',
                                        $work
                                    ) }}"
                                >

                                    <h3
                                        class="text-lg font-black text-white transition hover:text-cyan-300"
                                    >
                                        {{ $work->title }}
                                    </h3>

                                </a>

                                @if ($work->description)

                                    <p
                                        class="mt-3 text-sm leading-7 text-slate-400"
                                    >
                                        {{ \Illuminate\Support\Str::limit(
                                            $work->description,
                                            120
                                        ) }}
                                    </p>

                                @endif

                                {{-- ملفات العمل --}}
                                @if (
                                    $work->pdf_file
                                    || $work->dwg_file
                                )

                                    <div
                                        class="flex flex-wrap gap-3 mt-5"
                                    >

                                        @if ($work->pdf_file)

                                            <a
                                                href="{{ asset(
                                                    'storage/' .
                                                    $work->pdf_file
                                                ) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="px-4 py-2 text-sm font-bold text-red-300 transition rounded-xl bg-red-500/10 hover:bg-red-500/20"
                                            >
                                                📄 عرض ملف PDF
                                            </a>

                                        @endif

                                        @if ($work->dwg_file)

                                            <a
                                                href="{{ asset(
                                                    'storage/' .
                                                    $work->dwg_file
                                                ) }}"
                                                download
                                                class="px-4 py-2 text-sm font-bold transition text-cyan-300 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20"
                                            >
                                                📐 تحميل ملف DWG
                                            </a>

                                        @endif

                                    </div>

                                @endif

                                <a
                                    href="{{ route(
                                        'engineer.works.show',
                                        $work
                                    ) }}"
                                    class="flex items-center justify-between mt-5"
                                >

                                    <span
                                        class="text-sm font-bold text-cyan-300"
                                    >
                                        عرض المشروع
                                    </span>

                                    <span class="text-cyan-300">
                                        ←
                                    </span>

                                </a>

                            </div>

                        </article>

                    @empty

                        <div
                            class="p-12 text-center glass-panel-strong rounded-[2rem] md:col-span-2 xl:col-span-3"
                        >

                            <div class="mb-4 text-5xl">
                                🏗️
                            </div>

                            <h3
                                class="text-xl font-black text-white"
                            >
                                لا توجد أعمال منشورة
                            </h3>

                            <p class="mt-2 text-slate-400">
                                لم يضف المهندس أي مشاريع معتمدة حتى الآن.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </div>

</x-app-layout>
