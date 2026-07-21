<x-app-layout>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- معلومات المهندس --}}

            <section class="p-8 glass-panel-strong rounded-[2rem]">

                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                    <div class="flex flex-col items-center gap-6 sm:flex-row">

                        <div class="overflow-hidden rounded-full w-28 h-28 ring-4 ring-cyan-500/20">

                            @if ($user->profile_photo)

                                <img
                                    src="{{ asset('storage/' . $user->profile_photo) }}"
                                    alt="{{ $user->name }}"
                                    class="object-cover w-full h-full"
                                >

                            @else

                                <div
                                    class="flex items-center justify-center w-full h-full text-4xl font-black text-white bg-gradient-to-br from-blue-600 to-cyan-500"
                                >
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>

                            @endif

                        </div>

                        <div class="text-center sm:text-right">

                            <h1 class="text-3xl font-black text-white">
                                {{ $user->name }}
                            </h1>

                            <p class="mt-2 text-lg font-bold text-cyan-300">
                                {{ $user->employeeProfile?->specialty?->name ?? 'التخصص غير محدد' }}
                            </p>

                            <div class="flex flex-wrap justify-center gap-2 mt-4 sm:justify-start">

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

                            </div>

                        </div>

                    </div>

                    @auth

                        @if (
                            auth()->user()->role === 'customer'
                            || auth()->user()->role === 'admin'
                        )

                            <a
                                href="{{ route('consultations.create-for-engineer', $user) }}"
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

            <div class="grid gap-4 mt-8 md:grid-cols-3">

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        التخصص
                    </p>

                    <p class="mt-3 text-xl font-black text-white">
                        {{ $user->employeeProfile?->specialty?->name ?? 'غير محدد' }}
                    </p>

                </div>

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        المشاريع المنشورة
                    </p>

                    <p class="mt-3 text-3xl font-black text-cyan-300">
                        {{ $user->engineerWorks->count() }}
                    </p>

                </div>

                <div class="p-6 glass-panel-strong rounded-[2rem]">

                    <p class="text-sm text-slate-400">
                        تاريخ الانضمام
                    </p>

                    <p class="mt-3 text-xl font-black text-white">
                        {{ $user->created_at?->format('Y-m-d') }}
                    </p>

                </div>

            </div>

            {{-- نبذة المهندس --}}

            <section class="p-8 mt-8 glass-panel-strong rounded-[2rem]">

                <div class="flex items-center gap-3 mb-5">

                    <div
                        class="flex items-center justify-center text-xl w-11 h-11 rounded-2xl bg-cyan-500/10"
                    >
                        👨‍💼
                    </div>

                    <h2 class="text-2xl font-black text-white">
                        نبذة عن المهندس
                    </h2>

                </div>

                <p class="text-base leading-8 whitespace-pre-line text-slate-300">
                    {{ $user->employeeProfile?->bio ?: 'لم تتم إضافة نبذة عن المهندس حتى الآن.' }}
                </p>

            </section>

            {{-- أعمال المهندس --}}

            <section class="mt-10">

                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">

                    <div>

                        <h2 class="text-2xl font-black text-white">
                            أعمال المهندس
                        </h2>

                        <p class="mt-1 text-sm text-slate-400">
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

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

                    @forelse ($user->engineerWorks as $work)

                        <article
                            class="overflow-hidden transition duration-300 glass-panel-strong rounded-[2rem] hover:-translate-y-1"
                        >

                            <a
                                href="{{ route('engineer.works.show', $work) }}"
                                class="block"
                            >

                                <div class="relative overflow-hidden h-60 bg-slate-900">

                                    @if ($work->coverImage)

                                        <img
                                            src="{{ asset('storage/' . $work->coverImage->image_path) }}"
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

                                <div class="p-6">

                                    <h3 class="text-lg font-black text-white">
                                        {{ $work->title }}
                                    </h3>
                                    @if ($work->pdf_file || $work->dwg_file)

    <div class="flex flex-wrap gap-3 mt-5">

        @if ($work->pdf_file)

            <a
                href="{{ asset('storage/' . $work->pdf_file) }}"
                target="_blank"
                class="px-4 py-2 text-sm font-bold text-red-300 rounded-xl bg-red-500/10"
            >
                📄 عرض ملف PDF
            </a>

        @endif

        @if ($work->dwg_file)

            <a
                href="{{ asset('storage/' . $work->dwg_file) }}"
                download
                class="px-4 py-2 text-sm font-bold text-cyan-300 rounded-xl bg-cyan-500/10"
            >
                📐 تحميل ملف DWG
            </a>

        @endif

    </div>

@endif

                                    @if ($work->description)

                                        <p class="mt-3 text-sm leading-7 text-slate-400">
                                            {{ \Illuminate\Support\Str::limit($work->description, 120) }}
                                        </p>

                                    @endif

                                    <div class="flex items-center justify-between mt-5">

                                        <span class="text-sm font-bold text-cyan-300">
                                            عرض المشروع
                                        </span>

                                        <span class="text-cyan-300">
                                            ←
                                        </span>

                                    </div>

                                </div>

                            </a>

                        </article>

                    @empty

                        <div
                            class="p-12 text-center glass-panel-strong rounded-[2rem] md:col-span-2 xl:col-span-3"
                        >

                            <div class="mb-4 text-5xl">
                                🏗️
                            </div>

                            <h3 class="text-xl font-black text-white">
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
