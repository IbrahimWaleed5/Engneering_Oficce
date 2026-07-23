<x-app-layout>

    <div
        class="min-h-screen py-10 bg-slate-950"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- رأس الصفحة --}}
            <div
                class="relative p-6 mb-8 overflow-hidden border shadow-2xl sm:p-8 rounded-3xl border-white/10 bg-gradient-to-l from-slate-900 via-slate-900 to-blue-950"
            >

                <div
                    class="absolute w-40 h-40 rounded-full -top-16 -left-16 bg-blue-500/10 blur-2xl"
                ></div>

                <div
                    class="absolute w-40 h-40 rounded-full -bottom-20 -right-12 bg-cyan-500/10 blur-2xl"
                ></div>

                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div
                            class="inline-flex items-center justify-center mb-4 text-2xl border w-14 h-14 rounded-2xl border-blue-400/20 bg-blue-500/10"
                        >
                            🏗️
                        </div>

                        <h1 class="text-2xl font-black text-white sm:text-3xl">
                            مراجعة أعمال المهندسين
                        </h1>

                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            راجع الأعمال المضافة، وافق عليها أو ارفضها أو احذفها نهائيًا.
                        </p>

                    </div>

                    <div
                        class="flex items-center gap-3 px-4 py-3 border rounded-2xl border-white/10 bg-white/[0.04]"
                    >

                        <span class="text-2xl">
                            📊
                        </span>

                        <div>

                            <p class="text-xs text-slate-500">
                                إجمالي الأعمال
                            </p>

                            <p class="text-xl font-black text-white">
                                {{ $works->count() }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            {{-- رسائل النجاح --}}
            @if (session('success'))

                <div
                    class="flex items-center gap-3 p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    <span class="text-xl">✅</span>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>

            @endif

            {{-- رسائل الخطأ --}}
            @if (session('error'))

                <div
                    class="flex items-center gap-3 p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <span class="text-xl">⚠️</span>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>

            @endif

            {{-- أخطاء التحقق --}}
            @if ($errors->any())

                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >

                    <p class="mb-3 font-black">
                        يرجى تصحيح الأخطاء التالية:
                    </p>

                    <ul class="space-y-1 text-sm list-disc list-inside">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            {{-- الجدول --}}
            <div
                class="overflow-hidden border shadow-2xl rounded-3xl border-white/10 bg-slate-900/90"
            >

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1180px]">

                        <thead>

                            <tr
                                class="text-sm text-slate-300 bg-white/[0.05]"
                            >

                                <th class="px-5 py-4 font-black text-right">
                                    الصورة
                                </th>

                                <th class="px-5 py-4 font-black text-right">
                                    العنوان
                                </th>

                                <th class="px-5 py-4 font-black text-right">
                                    المهندس
                                </th>

                                <th class="px-5 py-4 font-black text-right">
                                    النوع
                                </th>

                                <th class="px-5 py-4 font-black text-right">
                                    الموقع
                                </th>

                                <th class="px-5 py-4 font-black text-center">
                                    الحالة
                                </th>

                                <th class="px-5 py-4 font-black text-center">
                                    الإجراءات
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-white/10">

                            @forelse ($works as $work)

                                <tr
                                    class="transition hover:bg-white/[0.03]"
                                >

                                    {{-- الصورة --}}
                                    <td class="px-5 py-4">

                                        @if ($work->coverImage)

                                            <a
                                                href="{{ route('engineer.works.show', $work) }}"
                                                class="block w-24 h-20 overflow-hidden border rounded-2xl border-white/10 bg-slate-950"
                                            >

                                                <img
                                                    src="{{ asset('storage/' . $work->coverImage->image_path) }}"
                                                    alt="{{ $work->title }}"
                                                    class="object-cover w-full h-full transition duration-300 hover:scale-110"
                                                >

                                            </a>

                                        @else

                                            <div
                                                class="flex items-center justify-center w-24 h-20 text-2xl border rounded-2xl border-white/10 bg-slate-950 text-slate-500"
                                            >
                                                🖼️
                                            </div>

                                        @endif

                                    </td>

                                    {{-- العنوان --}}
                                    <td class="px-5 py-4">

                                        <p class="max-w-[220px] font-black text-white truncate">
                                            {{ $work->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            #{{ $work->id }}
                                        </p>

                                    </td>

                                    {{-- المهندس --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="flex items-center justify-center flex-none w-10 h-10 font-black text-blue-200 border rounded-full border-blue-400/20 bg-blue-500/10"
                                            >
                                                {{ mb_substr(
                                                    $work->engineer?->name ?? '؟',
                                                    0,
                                                    1
                                                ) }}
                                            </div>

                                            <div>

                                                <p class="font-bold text-white">
                                                    {{ $work->engineer?->name ?? 'غير معروف' }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $work->engineer?->employeeProfile?->specialty?->name ?? 'بدون تخصص' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- النوع --}}
                                    <td class="px-5 py-4 text-sm text-slate-300">
                                        {{ $work->project_type ?? 'غير محدد' }}
                                    </td>

                                    {{-- الموقع --}}
                                    <td class="px-5 py-4 text-sm text-slate-300">
                                        {{ $work->location ?? 'غير محدد' }}
                                    </td>

                                    {{-- الحالة --}}
                                    <td class="px-5 py-4 text-center">

                                        @if ($work->status === 'pending')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-black text-yellow-200 border rounded-full border-yellow-500/20 bg-yellow-500/10"
                                            >
                                                ⏳ قيد المراجعة
                                            </span>

                                        @elseif ($work->status === 'approved')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-black text-green-200 border rounded-full border-green-500/20 bg-green-500/10"
                                            >
                                                ✅ مقبول
                                            </span>

                                        @elseif ($work->status === 'rejected')

                                            <span
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-black text-red-200 border rounded-full border-red-500/20 bg-red-500/10"
                                            >
                                                ❌ مرفوض
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex items-center px-3 py-2 text-xs font-black border rounded-full text-slate-300 border-white/10 bg-white/5"
                                            >
                                                {{ $work->status }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- الإجراءات --}}
                                    <td class="px-5 py-4">

                                        <div class="flex flex-wrap items-center justify-center gap-2">

                                            <a
                                                href="{{ route('engineer.works.show', $work) }}"
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-black text-blue-100 transition border rounded-xl border-blue-500/20 bg-blue-500/10 hover:bg-blue-500/20"
                                            >
                                                👁️ عرض
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
                                                        class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-black text-green-100 transition border rounded-xl border-green-500/20 bg-green-500/10 hover:bg-green-500/20"
                                                    >
                                                        ✅ موافقة
                                                    </button>

                                                </form>

                                            @endif

                                            @if ($work->status !== 'rejected')

                                                <button
                                                    type="button"
                                                    onclick="
                                                        document.getElementById(
                                                            'reject-modal-{{ $work->id }}'
                                                        ).classList.remove('hidden')
                                                    "
                                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-black text-red-100 transition border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                                                >
                                                    ❌ رفض
                                                </button>

                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route('admin.engineer-works.destroy', $work) }}"
                                                onsubmit="return confirm(
                                                    'هل أنت متأكد من حذف هذا العمل نهائيًا؟ لا يمكن التراجع عن الحذف.'
                                                )"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-black transition border rounded-xl text-slate-200 border-white/10 bg-slate-950 hover:bg-black"
                                                >
                                                    🗑️ حذف
                                                </button>

                                            </form>

                                        </div>

                                        {{-- نافذة سبب الرفض --}}
                                        @if ($work->status !== 'rejected')

                                            <div
                                                id="reject-modal-{{ $work->id }}"
                                                class="fixed inset-0 z-50 hidden p-4 bg-black/70 backdrop-blur-sm"
                                            >

                                                <div class="flex items-center justify-center min-h-full">

                                                    <div
                                                        class="w-full max-w-lg p-6 text-right border shadow-2xl rounded-3xl border-white/10 bg-slate-900"
                                                    >

                                                        <div class="flex items-start justify-between gap-4">

                                                            <div>

                                                                <h3 class="text-xl font-black text-white">
                                                                    رفض العمل
                                                                </h3>

                                                                <p class="mt-2 text-sm text-slate-400">
                                                                    اكتب سبب رفض العمل حتى يصل للمهندس.
                                                                </p>

                                                            </div>

                                                            <button
                                                                type="button"
                                                                onclick="
                                                                    document.getElementById(
                                                                        'reject-modal-{{ $work->id }}'
                                                                    ).classList.add('hidden')
                                                                "
                                                                class="flex items-center justify-center w-10 h-10 transition text-slate-400 rounded-xl bg-white/5 hover:text-white hover:bg-white/10"
                                                            >
                                                                ✕
                                                            </button>

                                                        </div>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('admin.engineer-works.reject', $work) }}"
                                                            class="mt-6"
                                                        >
                                                            @csrf
                                                            @method('PATCH')

                                                            <label
                                                                for="admin_note_{{ $work->id }}"
                                                                class="block mb-2 text-sm font-bold text-slate-300"
                                                            >
                                                                سبب الرفض
                                                            </label>

                                                            <textarea
                                                                id="admin_note_{{ $work->id }}"
                                                                name="admin_note"
                                                                rows="5"
                                                                required
                                                                maxlength="1000"
                                                                placeholder="اكتب سبب الرفض بوضوح..."
                                                                class="w-full px-4 py-3 text-white border outline-none resize-none rounded-2xl border-white/10 bg-slate-950 focus:border-red-400 focus:ring-2 focus:ring-red-400/10"
                                                            ></textarea>

                                                            <div class="flex gap-3 mt-5">

                                                                <button
                                                                    type="submit"
                                                                    class="flex-1 px-5 py-3 font-black text-white transition bg-red-600 rounded-2xl hover:bg-red-500"
                                                                >
                                                                    تأكيد الرفض
                                                                </button>

                                                                <button
                                                                    type="button"
                                                                    onclick="
                                                                        document.getElementById(
                                                                            'reject-modal-{{ $work->id }}'
                                                                        ).classList.add('hidden')
                                                                    "
                                                                    class="px-5 py-3 font-bold transition border rounded-2xl border-white/10 text-slate-300 hover:bg-white/5"
                                                                >
                                                                    إلغاء
                                                                </button>

                                                            </div>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-6 py-16 text-center"
                                    >

                                        <div class="text-5xl">
                                            📭
                                        </div>

                                        <h3 class="mt-4 text-xl font-black text-white">
                                            لا توجد أعمال للمراجعة
                                        </h3>

                                        <p class="mt-2 text-sm text-slate-500">
                                            ستظهر أعمال المهندسين هنا عند إضافتها.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
