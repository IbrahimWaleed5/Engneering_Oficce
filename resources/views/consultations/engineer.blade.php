<x-app-layout>

    <div
        x-data="{ activeUpload: null }"
        class="relative py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <x-page-header
                title="استشاراتي الهندسية"
                description="تابع المشاريع المعينة لك وارفع الملفات النهائية"
                icon="👷"
            />

            <x-alerts />

            <div class="grid gap-6">

                @forelse ($consultations as $consultation)

                    <article class="p-6 glass-card rounded-[2rem]">

                        <div class="flex flex-col gap-7 xl:flex-row xl:items-center">

                            <div class="flex-1">

                                <div class="flex flex-wrap items-center gap-3">

                                    <span
                                        class="px-3 py-2 text-xs font-bold rounded-xl bg-white/5 text-slate-300"
                                    >
                                        {{ $consultation->consultation_number }}
                                    </span>

                                    @if ($consultation->status === 'pending')

                                        <span
                                            class="text-yellow-200 status-badge bg-yellow-500/10"
                                        >
                                            جاهزة للبدء
                                        </span>

                                    @elseif ($consultation->status === 'in_progress')

                                        <span
                                            class="text-blue-200 status-badge bg-blue-500/10"
                                        >
                                            قيد التنفيذ
                                        </span>

                                    @elseif ($consultation->status === 'completed')

                                        <span
                                            class="text-green-200 status-badge bg-green-500/10"
                                        >
                                            مكتملة
                                        </span>

                                    @endif

                                </div>

                                <h2 class="mt-5 text-2xl font-black text-white">
                                    {{ $consultation->title }}
                                </h2>

                                <p class="mt-4 leading-7 text-slate-400">
                                    {{ Str::limit(
                                        $consultation->description,
                                        180
                                    ) }}
                                </p>

                                <div class="flex flex-wrap gap-5 mt-5 text-sm text-slate-400">

                                    <p>
                                        👤 العميل:
                                        <span class="font-bold text-white">
                                            {{ $consultation->customer?->name }}
                                        </span>
                                    </p>

                                    <p>
                                        📐 النوع:
                                        <span class="font-bold text-white">
                                            {{ $consultation->consultationType?->name }}
                                        </span>
                                    </p>

                                    <p>
                                        📅 التاريخ:
                                        <span class="font-bold text-white">
                                            {{ $consultation->created_at->format('Y-m-d') }}
                                        </span>
                                    </p>

                                </div>

                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 xl:w-80 xl:grid-cols-1">

    @if ($consultation->customer_file)

        <a
            href="{{ asset(
                'storage/' .
                $consultation->customer_file
            ) }}"
            target="_blank"
            class="secondary-button"
        >
            📎 تحميل ملف العميل
        </a>

    @endif

    @if (
        $consultation->payment_status === 'paid'
        && $consultation->engineer_id
    )

        <a
            href="{{ route(
                'consultations.messages.index',
                $consultation
            ) }}"
            class="secondary-button"
        >
            💬 المحادثة
        </a>

    @endif

    @if ($consultation->status !== 'completed')

        <button
            type="button"
            @click="
                activeUpload =
                    activeUpload === {{ $consultation->id }}
                        ? null
                        : {{ $consultation->id }}
            "
            class="primary-button"
        >
            📤 رفع الملف النهائي
        </button>

    @elseif ($consultation->engineer_file)

        <a
            href="{{ asset(
                'storage/' .
                $consultation->engineer_file
            ) }}"
            target="_blank"
            class="primary-button"
        >
            ✅ عرض الملف النهائي
        </a>

    @endif

</div>

                        </div>

                        @if ($consultation->status !== 'completed')

                            <div
                                x-cloak
                                x-show="activeUpload === {{ $consultation->id }}"
                                x-transition
                                class="pt-6 mt-6 border-t border-white/10"
                            >

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'consultations.upload-engineer-file',
                                        $consultation
                                    ) }}"
                                    enctype="multipart/form-data"
                                    x-data="{ fileName: '' }"
                                >

                                    @csrf

                                    <div class="grid gap-4 lg:grid-cols-[1fr_auto]">

                                        <label
                                            class="flex items-center gap-4 p-4 transition border-2 border-dashed cursor-pointer rounded-2xl border-white/10 bg-white/[0.03] hover:border-cyan-400/30"
                                        >

                                            <input
                                                type="file"
                                                name="engineer_file"
                                                class="hidden"
                                                required
                                                accept=".pdf,.zip,.rar,.dwg,.jpg,.jpeg,.png"
                                                @change="
                                                    fileName =
                                                        $event.target.files[0]
                                                            ? $event.target.files[0].name
                                                            : ''
                                                "
                                            >

                                            <div class="text-3xl">
                                                📁
                                            </div>

                                            <div>

                                                <p class="font-bold text-white">
                                                    اختر الملف النهائي
                                                </p>

                                                <p class="mt-1 text-xs text-slate-400">
                                                    PDF، ZIP، DWG أو صور المشروع
                                                </p>

                                                <p
                                                    x-show="fileName"
                                                    class="mt-2 text-sm font-bold text-cyan-300"
                                                    x-text="fileName"
                                                ></p>

                                            </div>

                                        </label>

                                        <button
                                            type="submit"
                                            class="primary-button lg:px-10"
                                        >
                                            إرسال الملف
                                        </button>

                                    </div>

                                </form>

                            </div>

                        @endif

                    </article>

                @empty

                    <div class="p-14 text-center glass-panel rounded-[2rem]">

                        <div class="text-6xl">
                            📭
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            لا توجد استشارات معينة لك
                        </h2>

                        <p class="mt-3 text-slate-400">
                            ستظهر هنا الاستشارات بعد تعيينها لك من الإدارة.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>
   

</x-app-layout>
