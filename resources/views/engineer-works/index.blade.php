<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">

            <div>
                <h2 class="text-xl font-black text-white">
                    مراجعة أعمال المهندسين
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    إدارة الأعمال الهندسية واعتمادها قبل النشر
                </p>
            </div>

            <a
                href="{{ route('engineer.works.public') }}"
                class="secondary-button"
            >
                عرض المكتبة العامة
            </a>

        </div>
    </x-slot>

    @php
        $totalWorks = $works->count();

        $pendingWorks = $works
            ->where('status', 'pending')
            ->count();

        $approvedWorks = $works
            ->where('status', 'approved')
            ->count();

        $rejectedWorks = $works
            ->where('status', 'rejected')
            ->count();
    @endphp

    <div
        x-data="{
            search: '',
            selectedStatus: 'all',
            rejectOpen: false,
            rejectUrl: '',
            rejectTitle: ''
        }"
        class="relative py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- الرسائل --}}

            @if (session('success'))

                <div
                    class="flex items-center gap-3 p-5 mb-8 border shadow-lg rounded-2xl border-emerald-500/20 bg-emerald-500/10 text-emerald-200 shadow-emerald-950/20"
                >
                    <div class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-emerald-500/15">
                        ✓
                    </div>

                    <div>
                        <p class="font-black">
                            تمت العملية بنجاح
                        </p>

                        <p class="mt-1 text-sm text-emerald-200/80">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>

            @endif

            @if ($errors->any())

                <div
                    class="p-5 mb-8 border shadow-lg rounded-2xl border-red-500/20 bg-red-500/10 shadow-red-950/20"
                >
                    <div class="flex items-center gap-3 mb-3">

                        <div class="flex items-center justify-center text-xl w-11 h-11 rounded-xl bg-red-500/15">
                            !
                        </div>

                        <p class="font-black text-red-200">
                            يرجى تصحيح الأخطاء التالية
                        </p>

                    </div>

                    <div class="space-y-2 text-sm text-red-200/90">

                        @foreach ($errors->all() as $error)

                            <p>
                                • {{ $error }}
                            </p>

                        @endforeach

                    </div>
                </div>

            @endif

            {{-- عنوان الصفحة --}}

            <section
                class="relative p-8 overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-950/60"
            >

                <div
                    class="absolute rounded-full pointer-events-none -top-32 -left-24 w-80 h-80 bg-cyan-500/10 blur-3xl"
                ></div>

                <div
                    class="absolute rounded-full pointer-events-none -bottom-40 -right-24 w-80 h-80 bg-blue-500/10 blur-3xl"
                ></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div>

                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold border rounded-full border-cyan-500/20 bg-cyan-500/10 text-cyan-300"
                        >
                            <span>🛡️</span>
                            لوحة مراجعة الأعمال
                        </div>

                        <h1 class="mt-5 text-3xl font-black text-white md:text-4xl">
                            مراجعة أعمال المهندسين
                        </h1>

                        <p class="max-w-2xl mt-3 leading-8 text-slate-400">
                            راجع المشاريع المضافة، اطلع على تفاصيلها وملفاتها،
                            ثم وافق عليها أو ارفضها مع إرسال ملاحظة واضحة للمهندس.
                        </p>

                    </div>

                    <div class="flex-none">

                        <div
                            class="flex items-center justify-center w-24 h-24 text-5xl border shadow-xl rounded-[2rem] border-cyan-500/20 bg-cyan-500/10 shadow-cyan-950/40"
                        >
                            🏗️
                        </div>

                    </div>

                </div>

            </section>

            {{-- الإحصائيات --}}

            <section class="grid gap-5 mt-8 sm:grid-cols-2 xl:grid-cols-4">

                <button
                    type="button"
                    @click="selectedStatus = 'all'"
                    :class="selectedStatus === 'all'
                        ? 'border-cyan-400/50 ring-2 ring-cyan-400/10'
                        : 'border-white/10'"
                    class="p-6 text-right transition border shadow-xl rounded-[2rem] bg-slate-900/80 hover:-translate-y-1"
                >

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-bold text-slate-400">
                                جميع الأعمال
                            </p>

                            <p class="mt-3 text-4xl font-black text-white">
                                {{ $totalWorks }}
                            </p>
                        </div>

                        <div class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-cyan-500/10">
                            📚
                        </div>

                    </div>

                </button>

                <button
                    type="button"
                    @click="selectedStatus = 'pending'"
                    :class="selectedStatus === 'pending'
                        ? 'border-amber-400/50 ring-2 ring-amber-400/10'
                        : 'border-white/10'"
                    class="p-6 text-right transition border shadow-xl rounded-[2rem] bg-slate-900/80 hover:-translate-y-1"
                >

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-bold text-slate-400">
                                قيد المراجعة
                            </p>

                            <p class="mt-3 text-4xl font-black text-amber-300">
                                {{ $pendingWorks }}
                            </p>
                        </div>

                        <div class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-amber-500/10">
                            ⏳
                        </div>

                    </div>

                </button>

                <button
                    type="button"
                    @click="selectedStatus = 'approved'"
                    :class="selectedStatus === 'approved'
                        ? 'border-emerald-400/50 ring-2 ring-emerald-400/10'
                        : 'border-white/10'"
                    class="p-6 text-right transition border shadow-xl rounded-[2rem] bg-slate-900/80 hover:-translate-y-1"
                >

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-bold text-slate-400">
                                الأعمال المقبولة
                            </p>

                            <p class="mt-3 text-4xl font-black text-emerald-300">
                                {{ $approvedWorks }}
                            </p>
                        </div>

                        <div class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-emerald-500/10">
                            ✓
                        </div>

                    </div>

                </button>

                <button
                    type="button"
                    @click="selectedStatus = 'rejected'"
                    :class="selectedStatus === 'rejected'
                        ? 'border-red-400/50 ring-2 ring-red-400/10'
                        : 'border-white/10'"
                    class="p-6 text-right transition border shadow-xl rounded-[2rem] bg-slate-900/80 hover:-translate-y-1"
                >

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-bold text-slate-400">
                                الأعمال المرفوضة
                            </p>

                            <p class="mt-3 text-4xl font-black text-red-300">
                                {{ $rejectedWorks }}
                            </p>
                        </div>

                        <div class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-red-500/10">
                            ✕
                        </div>

                    </div>

                </button>

            </section>

            {{-- البحث والفلاتر --}}

            <section
                class="p-5 mt-8 border shadow-xl rounded-[2rem] border-white/10 bg-slate-900/75"
            >

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
                            placeholder="ابحث باسم المشروع أو المهندس أو الموقع أو النوع..."
                            class="w-full py-4 pl-4 pr-12 text-white transition border outline-none rounded-2xl border-white/10 bg-slate-950/60 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/10"
                        >

                    </div>

                    <div class="flex flex-wrap gap-2">

                        <button
                            type="button"
                            @click="selectedStatus = 'all'"
                            :class="selectedStatus === 'all'
                                ? 'bg-cyan-500 text-slate-950'
                                : 'bg-white/5 text-slate-300'"
                            class="px-5 py-3 text-sm font-black transition rounded-xl hover:bg-cyan-500 hover:text-slate-950"
                        >
                            الكل
                        </button>

                        <button
                            type="button"
                            @click="selectedStatus = 'pending'"
                            :class="selectedStatus === 'pending'
                                ? 'bg-amber-500 text-slate-950'
                                : 'bg-white/5 text-slate-300'"
                            class="px-5 py-3 text-sm font-black transition rounded-xl hover:bg-amber-500 hover:text-slate-950"
                        >
                            قيد المراجعة
                        </button>

                        <button
                            type="button"
                            @click="selectedStatus = 'approved'"
                            :class="selectedStatus === 'approved'
                                ? 'bg-emerald-500 text-slate-950'
                                : 'bg-white/5 text-slate-300'"
                            class="px-5 py-3 text-sm font-black transition rounded-xl hover:bg-emerald-500 hover:text-slate-950"
                        >
                            مقبول
                        </button>

                        <button
                            type="button"
                            @click="selectedStatus = 'rejected'"
                            :class="selectedStatus === 'rejected'
                                ? 'bg-red-500 text-white'
                                : 'bg-white/5 text-slate-300'"
                            class="px-5 py-3 text-sm font-black transition rounded-xl hover:bg-red-500 hover:text-white"
                        >
                            مرفوض
                        </button>

                    </div>

                </div>

            </section>

            {{-- الأعمال --}}

            <section class="mt-8">

                <div class="grid gap-7 xl:grid-cols-2">

                    @forelse ($works as $work)

                        @php
                            $searchText = strtolower(
                                ($work->title ?? '') . ' ' .
                                ($work->engineer?->name ?? '') . ' ' .
                                ($work->location ?? '') . ' ' .
                                ($work->project_type ?? '') . ' ' .
                                ($work->description ?? '')
                            );
                        @endphp

                        <article
                            x-show="
                                (
                                    selectedStatus === 'all'
                                    || selectedStatus === @js($work->status)
                                )
                                &&
                                (
                                    search === ''
                                    || @js($searchText)
                                        .includes(search.toLowerCase())
                                )
                            "
                            x-transition
                            class="overflow-hidden transition duration-300 border shadow-2xl group rounded-[2rem] border-white/10 bg-slate-900/85 hover:-translate-y-1 hover:border-cyan-400/20"
                        >

                            {{-- صورة المشروع --}}

                            <div class="relative overflow-hidden h-72 bg-slate-950">

                                @if ($work->coverImage)

                                    <img
                                        src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                        alt="{{ $work->title }}"
                                        class="object-cover w-full h-full transition duration-700 group-hover:scale-105"
                                    >

                                @else

                                    <div
                                        class="flex items-center justify-center w-full h-full text-7xl bg-gradient-to-br from-slate-800 to-slate-950"
                                    >
                                        🏗️
                                    </div>

                                @endif

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"
                                ></div>

                                <div class="absolute flex flex-wrap gap-2 top-5 right-5">

                                    @if ($work->status === 'approved')

                                        <span
                                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black border rounded-full text-emerald-300 border-emerald-500/20 bg-emerald-950/80 backdrop-blur-xl"
                                        >
                                            <span>✓</span>
                                            مقبول
                                        </span>

                                    @elseif ($work->status === 'rejected')

                                        <span
                                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black text-red-300 border rounded-full border-red-500/20 bg-red-950/80 backdrop-blur-xl"
                                        >
                                            <span>✕</span>
                                            مرفوض
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-black border rounded-full text-amber-300 border-amber-500/20 bg-amber-950/80 backdrop-blur-xl"
                                        >
                                            <span>⏳</span>
                                            قيد المراجعة
                                        </span>

                                    @endif

                                    @if ($work->project_type)

                                        <span
                                            class="px-4 py-2 text-xs font-bold text-blue-200 border rounded-full border-white/10 bg-slate-950/75 backdrop-blur-xl"
                                        >
                                            {{ $work->project_type }}
                                        </span>

                                    @endif

                                </div>

                                <div class="absolute right-6 bottom-6 left-6">

                                    <h2 class="text-2xl font-black text-white">
                                        {{ $work->title }}
                                    </h2>

                                    <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-slate-300">

                                        <span class="inline-flex items-center gap-2">
                                            📍
                                            {{ $work->location ?? 'الموقع غير محدد' }}
                                        </span>

                                        @if ($work->completion_year)

                                            <span class="inline-flex items-center gap-2">
                                                📅
                                                {{ $work->completion_year }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                            {{-- محتوى البطاقة --}}

                            <div class="p-6">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center min-w-0 gap-4">

                                        @if ($work->engineer?->profile_photo)

                                            <img
                                                src="{{ asset('storage/' . $work->engineer->profile_photo) }}"
                                                alt="{{ $work->engineer->name }}"
                                                class="flex-none object-cover border-2 rounded-full w-14 h-14 border-cyan-400/20"
                                            >

                                        @else

                                            <div
                                                class="flex items-center justify-center flex-none text-xl font-black text-white border-2 rounded-full w-14 h-14 border-cyan-400/20 bg-gradient-to-br from-blue-600 to-cyan-500"
                                            >
                                                {{ mb_substr(
                                                    $work->engineer?->name ?? 'م',
                                                    0,
                                                    1
                                                ) }}
                                            </div>

                                        @endif

                                        <div class="min-w-0">

                                            <p class="text-xs text-slate-500">
                                                صاحب العمل
                                            </p>

                                            <p class="mt-1 font-black text-white truncate">
                                                {{ $work->engineer?->name ?? 'مهندس غير محدد' }}
                                            </p>

                                            <p class="mt-1 text-xs text-cyan-300">
                                                {{
                                                    $work
                                                        ->engineer
                                                        ?->employeeProfile
                                                        ?->specialty
                                                        ?->name
                                                    ?? 'التخصص غير محدد'
                                                }}
                                            </p>

                                        </div>

                                    </div>

                                    <div class="text-left">

                                        <p class="text-xs text-slate-500">
                                            تاريخ الإضافة
                                        </p>

                                        <p class="mt-1 text-sm font-bold text-slate-300">
                                            {{ $work->created_at->format('Y-m-d') }}
                                        </p>

                                    </div>

                                </div>

                                @if ($work->description)

                                    <p class="mt-6 leading-8 text-slate-400 line-clamp-3">
                                        {{ $work->description }}
                                    </p>

                                @endif

                                {{-- الملفات --}}

                                @if ($work->pdf_file || $work->dwg_file)

                                    <div class="flex flex-wrap gap-3 mt-5">

                                        @if ($work->pdf_file)

                                            <a
                                                href="{{ asset('storage/' . $work->pdf_file) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-300 transition rounded-xl bg-red-500/10 hover:bg-red-500/20"
                                            >
                                                📄 PDF
                                            </a>

                                        @endif

                                        @if ($work->dwg_file)

                                            <a
                                                href="{{ asset('storage/' . $work->dwg_file) }}"
                                                download
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold transition rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20"
                                            >
                                                📐 DWG
                                            </a>

                                        @endif

                                    </div>

                                @endif

                                @if ($work->admin_note)

                                    <div
                                        class="p-4 mt-5 border rounded-2xl border-red-500/20 bg-red-500/10"
                                    >

                                        <p class="text-sm font-black text-red-200">
                                            ملاحظة المدير
                                        </p>

                                        <p class="mt-2 text-sm leading-7 text-red-200/80">
                                            {{ $work->admin_note }}
                                        </p>

                                    </div>

                                @endif

                                {{-- الأزرار --}}

                                <div class="grid gap-3 mt-6 sm:grid-cols-3">

                                    <a
                                        href="{{ route('engineer.works.show', $work) }}"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                                    >
                                        👁️ عرض العمل
                                    </a>

                                    @if ($work->status !== 'approved')

                                        <form
                                            method="POST"
                                            action="{{ route('admin.engineer-works.approve', $work) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                onclick="return confirm('هل تريد الموافقة على هذا العمل؟')"
                                                class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition rounded-xl bg-emerald-600 hover:bg-emerald-500"
                                            >
                                                ✓ موافقة
                                            </button>

                                        </form>

                                    @else

                                        <div
                                            class="inline-flex items-center justify-center gap-2 px-5 py-3 font-black rounded-xl bg-emerald-500/10 text-emerald-300"
                                        >
                                            ✓ تم الاعتماد
                                        </div>

                                    @endif

                                    @if ($work->status !== 'rejected')

                                        <button
                                            type="button"
                                            @click="
                                                rejectOpen = true;
                                                rejectUrl = @js(route(
                                                    'admin.engineer-works.reject',
                                                    $work
                                                ));
                                                rejectTitle = @js($work->title);
                                            "
                                            class="inline-flex items-center justify-center w-full gap-2 px-5 py-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                                        >
                                            ✕ رفض
                                        </button>

                                    @else

                                        <div
                                            class="inline-flex items-center justify-center gap-2 px-5 py-3 font-black text-red-300 rounded-xl bg-red-500/10"
                                        >
                                            ✕ مرفوض
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </article>

                    @empty

                        <div
                            class="p-16 text-center border xl:col-span-2 rounded-[2rem] border-white/10 bg-slate-900/75"
                        >

                            <div class="text-7xl">
                                🏗️
                            </div>

                            <h3 class="mt-6 text-2xl font-black text-white">
                                لا توجد أعمال للمراجعة
                            </h3>

                            <p class="mt-3 text-slate-400">
                                ستظهر هنا الأعمال التي يضيفها المهندسون.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>

        {{-- نافذة الرفض --}}

        <div
            x-cloak
            x-show="rejectOpen"
            x-transition.opacity
            @keydown.escape.window="rejectOpen = false"
            class="fixed inset-0 z-[100] flex items-center justify-center p-5 bg-slate-950/90 backdrop-blur-xl"
        >

            <div
                @click.outside="rejectOpen = false"
                x-transition.scale
                class="w-full max-w-xl p-7 border shadow-2xl rounded-[2rem] border-white/10 bg-slate-900"
            >

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <div
                            class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-red-500/10"
                        >
                            ⚠️
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            رفض العمل
                        </h2>

                        <p class="mt-2 text-sm text-slate-400">
                            العمل:
                            <span
                                class="font-bold text-white"
                                x-text="rejectTitle"
                            ></span>
                        </p>

                    </div>

                    <button
                        type="button"
                        @click="rejectOpen = false"
                        class="flex items-center justify-center text-white transition border rounded-full w-11 h-11 border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        ✕
                    </button>

                </div>

                <form
                    method="POST"
                    :action="rejectUrl"
                    class="mt-7"
                >

                    @csrf
                    @method('PATCH')

                    <label class="block mb-2 text-sm font-bold text-slate-300">
                        سبب رفض العمل
                    </label>

                    <textarea
                        name="admin_note"
                        rows="5"
                        required
                        placeholder="اكتب للمهندس سبب رفض العمل والتعديلات المطلوبة..."
                        class="w-full px-4 py-4 text-white transition border outline-none resize-none rounded-2xl border-white/10 bg-slate-950/60 placeholder:text-slate-500 focus:border-red-400 focus:ring-2 focus:ring-red-400/10"
                    ></textarea>

                    <div class="grid gap-3 mt-6 sm:grid-cols-2">

                        <button
                            type="button"
                            @click="rejectOpen = false"
                            class="px-5 py-3 font-black text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                        >
                            إلغاء
                        </button>

                        <button
                            type="submit"
                            class="px-5 py-3 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                        >
                            تأكيد رفض العمل
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
