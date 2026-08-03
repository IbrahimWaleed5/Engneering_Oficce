<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $statusData = match ($application->status) {
                    'approved' => [
                        'label' => 'تم قبول الطلب',
                        'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                    ],

                    'rejected' => [
                        'label' => 'تم رفض الطلب',
                        'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                    ],

                    'cancelled' => [
                        'label' => 'تم إلغاء الطلب',
                        'class' => 'text-slate-300 border-white/10 bg-white/5',
                    ],

                    default => [
                        'label' => 'قيد المراجعة',
                        'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                    ],
                };
            @endphp

            <div class="flex flex-col gap-4 mb-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">
                        مراجعة طلب انضمام
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        {{ $application->engineer?->name ?? 'مهندس غير موجود' }}
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        طلب انضمام إلى مكتب
                        <span class="font-bold text-white">
                            {{ $application->office?->name ?? 'غير معروف' }}
                        </span>
                    </p>
                </div>

                <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $statusData['class'] }}">
                    {{ $statusData['label'] }}
                </span>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">

                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            بيانات المهندس
                        </h2>

                        <div class="grid gap-4 mt-6 sm:grid-cols-2">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    الاسم
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $application->engineer?->name ?? 'غير متوفر' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    البريد الإلكتروني
                                </p>

                                <p class="mt-2 font-bold text-white break-all">
                                    {{ $application->engineer?->email ?? 'غير متوفر' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    رقم الهاتف
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->engineer?->phone ?: 'غير متوفر' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    التخصص
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->specialty?->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    المسمى المطلوب
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->requested_position ?: 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    سنوات الخبرة
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $application->years_of_experience !== null
                                        ? $application->years_of_experience . ' سنة'
                                        : 'غير محددة' }}
                                </p>
                            </div>
                        </div>

                        @if ($application->message)
                            <div class="p-5 mt-5 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    رسالة المهندس
                                </p>

                                <p class="mt-3 leading-8 text-white">
                                    {{ $application->message }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            ملفات الطلب
                        </h2>

                        <div class="grid gap-4 mt-6 sm:grid-cols-2">
                            <a
                                href="{{ route(
                                    'office-membership-applications.file',
                                    [
                                        'officeMembershipApplication' => $application,
                                        'type' => 'cv',
                                    ]
                                ) }}"
                                class="flex items-center justify-between p-5 font-bold text-white transition border rounded-2xl border-white/10 bg-white/5 hover:bg-white/10"
                            >
                                <span>
                                    السيرة الذاتية CV
                                </span>

                                <span class="text-cyan-300">
                                    عرض الملف
                                </span>
                            </a>

                            <a
                                href="{{ route(
                                    'office-membership-applications.file',
                                    [
                                        'officeMembershipApplication' => $application,
                                        'type' => 'certificate',
                                    ]
                                ) }}"
                                class="flex items-center justify-between p-5 font-bold text-white transition border rounded-2xl border-white/10 bg-white/5 hover:bg-white/10"
                            >
                                <span>
                                    الشهادة
                                </span>

                                <span class="text-cyan-300">
                                    عرض الملف
                                </span>
                            </a>
                        </div>
                    </div>

                    @if ($application->status === 'rejected')
                        <div class="p-6 border rounded-3xl border-red-500/20 bg-red-500/10">
                            <h2 class="text-xl font-black text-red-200">
                                سبب رفض الطلب
                            </h2>

                            <p class="mt-3 leading-8 text-red-100">
                                {{ $application->rejection_reason ?: 'لم يتم تسجيل سبب.' }}
                            </p>
                        </div>
                    @endif

                    @if ($application->status === 'approved')
                        <div class="p-6 border rounded-3xl border-green-500/20 bg-green-500/10">
                            <h2 class="text-xl font-black text-green-200">
                                تم قبول المهندس
                            </h2>

                            <p class="mt-3 leading-8 text-green-100">
                                تمت إضافة المهندس إلى أعضاء المكتب بنجاح.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @if ($application->status === 'pending')
                        <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                            <h2 class="text-xl font-black text-white">
                                قبول الطلب
                            </h2>

                            <form
                                method="POST"
                                action="{{ route(
                                    'office-membership-applications.review',
                                    $application
                                ) }}"
                                class="mt-6"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="decision"
                                    value="approve"
                                >

                                <label
                                    for="position"
                                    class="block mb-2 font-bold text-white"
                                >
                                    المسمى الوظيفي داخل المكتب
                                </label>

                                <input
                                    id="position"
                                    type="text"
                                    name="position"
                                    value="{{ old(
                                        'position',
                                        $application->requested_position
                                    ) }}"
                                    required
                                    maxlength="150"
                                    placeholder="مثال: مهندس معماري"
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                >

                                <button
                                    type="submit"
                                    onclick="return confirm('هل تريد قبول المهندس وإضافته إلى المكتب؟')"
                                    class="w-full px-5 py-3 mt-4 font-black text-white transition bg-green-600 rounded-xl hover:bg-green-500"
                                >
                                    قبول وإضافة المهندس
                                </button>
                            </form>
                        </div>

                        <div class="p-6 border rounded-3xl border-red-500/20 bg-red-500/5">
                            <h2 class="text-xl font-black text-red-200">
                                رفض الطلب
                            </h2>

                            <form
                                method="POST"
                                action="{{ route(
                                    'office-membership-applications.review',
                                    $application
                                ) }}"
                                class="mt-6"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="decision"
                                    value="reject"
                                >

                                <label
                                    for="rejection_reason"
                                    class="block mb-2 font-bold text-red-100"
                                >
                                    سبب الرفض
                                </label>

                                <textarea
                                    id="rejection_reason"
                                    name="rejection_reason"
                                    rows="6"
                                    required
                                    maxlength="3000"
                                    placeholder="اكتب سبب رفض الطلب..."
                                    class="w-full px-4 py-3 text-white border rounded-xl border-red-500/20 bg-slate-800"
                                >{{ old('rejection_reason') }}</textarea>

                                <button
                                    type="submit"
                                    onclick="return confirm('هل تريد رفض طلب المهندس؟')"
                                    class="w-full px-5 py-3 mt-4 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                                >
                                    رفض الطلب
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                            <h2 class="text-xl font-black text-white">
                                معلومات المراجعة
                            </h2>

                            <div class="mt-5 space-y-4">
                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-slate-400">
                                        تمت المراجعة بواسطة
                                    </p>

                                    <p class="mt-2 font-bold text-white">
                                        {{ $application->reviewer?->name ?? 'غير معروف' }}
                                    </p>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-slate-400">
                                        تاريخ المراجعة
                                    </p>

                                    <p class="mt-2 font-bold text-white">
                                        {{ $application->reviewed_at?->format('Y-m-d H:i') ?? 'غير محدد' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <a
                        href="{{ route(
                            'office-membership-applications.index'
                        ) }}"
                        class="inline-flex items-center justify-center w-full px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        العودة إلى جميع الطلبات
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
