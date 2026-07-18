<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-bold text-white">
            مراجعة أعمال المهندسين
        </h2>
    </x-slot>

    <div class="py-10" dir="rtl">

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-6 border text-emerald-200 rounded-xl border-emerald-500/30 bg-emerald-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-6 text-red-200 border rounded-xl border-red-500/30 bg-red-500/10">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 glass-panel rounded-2xl">

                <div class="mb-8">

                    <h1 class="text-3xl font-extrabold text-white">
                        مراجعة أعمال المهندسين
                    </h1>

                    <p class="mt-2 text-slate-400">
                        الموافقة على الأعمال أو رفضها مع كتابة ملاحظة للمهندس
                    </p>

                </div>

                <div class="space-y-5">

                    @forelse($works as $work)

                        <div class="p-5 glass-card rounded-2xl">

                            <div class="flex flex-col justify-between gap-5 lg:flex-row">

                                <div class="flex-1">

                                    <div class="flex flex-wrap items-center gap-3">

                                        <h2 class="text-xl font-bold text-white">
                                            {{ $work->title }}
                                        </h2>

                                        @if($work->status === 'approved')

                                            <span class="status-badge text-emerald-300 bg-emerald-500/10">
                                                مقبول
                                            </span>

                                        @elseif($work->status === 'rejected')

                                            <span class="text-red-300 status-badge bg-red-500/10">
                                                مرفوض
                                            </span>

                                        @else

                                            <span class="status-badge text-amber-300 bg-amber-500/10">
                                                قيد المراجعة
                                            </span>

                                        @endif

                                    </div>

                                    <p class="mt-3 text-sm text-slate-400">
                                        المهندس:
                                        <span class="font-bold text-slate-200">
                                            {{ $work->engineer->name ?? 'غير محدد' }}
                                        </span>
                                    </p>

                                    <p class="mt-4 leading-8 text-slate-300">
                                        {{ $work->description }}
                                    </p>

                                    <p class="mt-3 text-sm text-slate-500">
                                        تاريخ الإضافة:
                                        {{ $work->created_at->format('Y-m-d H:i') }}
                                    </p>

                                    @if($work->admin_note)

                                        <div class="p-4 mt-4 text-red-200 border rounded-xl border-red-500/20 bg-red-500/10">

                                            <p class="mb-1 font-bold">
                                                ملاحظة المدير
                                            </p>

                                            <p>
                                                {{ $work->admin_note }}
                                            </p>

                                        </div>

                                    @endif

                                </div>

                                <div class="flex flex-col gap-3 lg:w-72">

                                    <a
                                        href="{{ route('engineer.works.show', $work) }}"
                                        class="secondary-button"
                                    >
                                        عرض العمل
                                    </a>

                                    @if($work->status !== 'approved')

                                        <form
                                            method="POST"
                                            action="{{ route('admin.engineer-works.approve', $work) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="w-full primary-button"
                                                onclick="return confirm('هل تريد الموافقة على هذا العمل؟')"
                                            >
                                                الموافقة على العمل
                                            </button>

                                        </form>

                                    @endif

                                    @if($work->status !== 'rejected')

                                        <form
                                            method="POST"
                                            action="{{ route('admin.engineer-works.reject', $work) }}"
                                            class="space-y-3"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <textarea
                                                name="admin_note"
                                                rows="3"
                                                required
                                                class="form-control"
                                                placeholder="اكتب سبب رفض العمل..."
                                            ></textarea>

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center w-full px-5 py-3 font-bold text-white transition bg-red-600 rounded-xl hover:bg-red-700"
                                                onclick="return confirm('هل تريد رفض هذا العمل؟')"
                                            >
                                                رفض العمل
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-16 text-center glass-card rounded-2xl">

                            <div class="mb-4 text-5xl">
                                🏗️
                            </div>

                            <h3 class="text-xl font-bold text-white">
                                لا توجد أعمال للمراجعة
                            </h3>

                            <p class="mt-2 text-slate-400">
                                ستظهر أعمال المهندسين المضافة هنا
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
