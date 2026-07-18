<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <h2 class="text-xl font-bold text-white">
                أعمالي الهندسية
            </h2>

            <a
                href="{{ route('engineer.works.create') }}"
                class="primary-button"
            >
                إضافة عمل جديد
            </a>

        </div>
    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div
                    class="p-4 mb-6 border rounded-xl text-emerald-200 border-emerald-500/30 bg-emerald-500/10"
                >
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 glass-panel rounded-2xl">

                <div class="mb-8">

                    <h1 class="text-3xl font-extrabold text-white">
                        أعمالي الهندسية
                    </h1>

                    <p class="mt-2 text-slate-400">
                        يمكنك متابعة حالة الأعمال المرسلة للمراجعة
                    </p>

                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

                    @forelse($works as $work)

                        <div
                            class="overflow-hidden glass-card rounded-2xl"
                        >

                            {{-- صورة الغلاف --}}
                            <div class="overflow-hidden h-52 bg-slate-800">

                                @if($work->coverImage)

                                    <img
                                        src="{{ asset(
                                            'storage/'
                                            . $work->coverImage->image_path
                                        ) }}"
                                        alt="{{ $work->title }}"
                                        class="object-cover w-full h-full"
                                    >

                                @else

                                    <div
                                        class="flex items-center justify-center w-full h-full text-slate-500"
                                    >
                                        لا توجد صورة
                                    </div>

                                @endif

                            </div>

                            <div class="p-5">

                                <div
                                    class="flex flex-wrap items-center justify-between gap-3"
                                >

                                    <h2 class="text-xl font-bold text-white">
                                        {{ $work->title }}
                                    </h2>

                                    @if($work->status === 'approved')

                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full text-emerald-300 bg-emerald-500/10"
                                        >
                                            مقبول
                                        </span>

                                    @elseif($work->status === 'rejected')

                                        <span
                                            class="px-3 py-1 text-xs font-bold text-red-300 rounded-full bg-red-500/10"
                                        >
                                            مرفوض
                                        </span>

                                    @else

                                        <span
                                            class="px-3 py-1 text-xs font-bold rounded-full text-amber-300 bg-amber-500/10"
                                        >
                                            قيد المراجعة
                                        </span>

                                    @endif

                                </div>

                                @if($work->description)

                                    <p class="mt-4 leading-7 text-slate-300">
                                        {{ \Illuminate\Support\Str::limit(
                                            $work->description,
                                            120
                                        ) }}
                                    </p>

                                @endif

                                <div
                                    class="mt-4 space-y-2 text-sm text-slate-400"
                                >

                                    @if($work->project_type)

                                        <p>
                                            نوع المشروع:
                                            <span class="text-slate-200">
                                                {{ $work->project_type }}
                                            </span>
                                        </p>

                                    @endif

                                    @if($work->location)

                                        <p>
                                            الموقع:
                                            <span class="text-slate-200">
                                                {{ $work->location }}
                                            </span>
                                        </p>

                                    @endif

                                    @if($work->completion_year)

                                        <p>
                                            سنة الإنجاز:
                                            <span class="text-slate-200">
                                                {{ $work->completion_year }}
                                            </span>
                                        </p>

                                    @endif

                                </div>

                                {{-- ملاحظة المدير عند الرفض --}}
                                @if(
                                    $work->status === 'rejected'
                                    && $work->admin_note
                                )

                                    <div
                                        class="p-4 mt-5 text-red-200 border rounded-xl border-red-500/30 bg-red-500/10"
                                    >

                                        <p class="mb-2 font-bold">
                                            سبب الرفض:
                                        </p>

                                        <p class="leading-7">
                                            {{ $work->admin_note }}
                                        </p>

                                    </div>

                                @endif

                                <div
                                    class="flex flex-wrap gap-3 pt-5 mt-5 border-t border-white/10"
                                >

                                    <a
                                        href="{{ route(
                                            'engineer.works.show',
                                            $work
                                        ) }}"
                                        class="secondary-button"
                                    >
                                        عرض التفاصيل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'engineer.works.destroy',
                                            $work
                                        ) }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center px-4 py-2 font-bold text-white transition bg-red-600 rounded-xl hover:bg-red-700"
                                            onclick="
                                                return confirm(
                                                    'هل تريد حذف هذا العمل؟'
                                                )
                                            "
                                        >
                                            حذف
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div
                            class="py-16 text-center glass-card rounded-2xl md:col-span-2 lg:col-span-3"
                        >

                            <div class="mb-4 text-5xl">
                                🏗️
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                لم تضف أي أعمال بعد
                            </h3>

                            <p class="mt-2 text-slate-400">
                                أضف أول مشروع هندسي لعرضه في ملفك
                            </p>

                            <a
                                href="{{ route(
                                    'engineer.works.create'
                                ) }}"
                                class="inline-flex mt-6 primary-button"
                            >
                                إضافة عمل جديد
                            </a>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
