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
                        'label' => 'مقبول',
                        'class' => 'text-green-200 bg-green-500/10 border-green-500/20',
                    ],

                    'rejected' => [
                        'label' => 'مرفوض',
                        'class' => 'text-red-200 bg-red-500/10 border-red-500/20',
                    ],

                    'cancelled' => [
                        'label' => 'ملغي',
                        'class' => 'text-slate-300 bg-white/5 border-white/10',
                    ],

                    default => [
                        'label' => 'قيد المراجعة',
                        'class' => 'text-yellow-200 bg-yellow-500/10 border-yellow-500/20',
                    ],
                };
            @endphp

            <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70 sm:p-8">
                <div class="flex flex-col gap-5 pb-6 border-b border-white/10 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-cyan-300">
                            طلب مكتب هندسي
                        </p>

                        <h1 class="mt-2 text-3xl font-black text-white">
                            {{ $application->office_name }}
                        </h1>

                        <p class="mt-3 text-slate-400">
                            مقدّم الطلب:
                            <span class="font-bold text-white">
                                {{ $application->applicant?->name ?? 'غير معروف' }}
                            </span>
                        </p>
                    </div>

                    <span
                        class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $statusData['class'] }}"
                    >
                        {{ $statusData['label'] }}
                    </span>
                </div>

                <div class="grid gap-5 mt-8 md:grid-cols-2">
                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            البريد الإلكتروني للمكتب
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->email }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            رقم الهاتف
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->phone ?: 'غير محدد' }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            رقم السجل التجاري
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->commercial_registration }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            رقم الترخيص
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->license_number }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            الدولة
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->country ?: 'غير محددة' }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            المدينة
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->city ?: 'غير محددة' }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            تاريخ تقديم الطلب
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->created_at?->format('Y-m-d H:i') }}
                        </p>
                    </div>

                    <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            حساب مقدم الطلب
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->applicant?->email ?? 'غير معروف' }}
                        </p>
                    </div>
                </div>

                <div class="p-5 mt-5 border rounded-2xl border-white/10 bg-white/5">
                    <p class="text-xs text-slate-400">
                        عنوان المكتب
                    </p>

                    <p class="mt-2 leading-8 text-white">
                        {{ $application->address }}
                    </p>
                </div>

                @if ($application->notes)
                    <div class="p-5 mt-5 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            نبذة أو ملاحظات المكتب
                        </p>

                        <p class="mt-2 leading-8 text-white">
                            {{ $application->notes }}
                        </p>
                    </div>
                @endif

                <div class="grid gap-5 mt-6 md:grid-cols-2">
                    <div class="p-5 border rounded-2xl border-white/10 bg-white/5">
                        <p class="font-black text-white">
                            ملف السجل التجاري
                        </p>

                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            سيتم إضافة زر تنزيل آمن للملف بعد تجهيز
                            Controller الملفات المحمية.
                        </p>
                    </div>

                    <div class="p-5 border rounded-2xl border-white/10 bg-white/5">
                        <p class="font-black text-white">
                            ملف ترخيص المكتب
                        </p>

                        <p class="mt-2 text-sm leading-7 text-slate-400">
                            سيتم إضافة زر تنزيل آمن للملف بعد تجهيز
                            Controller الملفات المحمية.
                        </p>
                    </div>
                </div>

                @if ($application->status === 'pending')
                    <div class="grid gap-6 mt-8 lg:grid-cols-2">
                        <form
                            method="POST"
                            action="{{ route(
                                'admin.office-applications.review',
                                $application
                            ) }}"
                            class="p-5 border rounded-2xl border-green-500/20 bg-green-500/5"
                        >
                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="decision"
                                value="approve"
                            >

                            <h2 class="text-xl font-black text-green-200">
                                قبول المكتب
                            </h2>

                            <p class="mt-3 text-sm leading-7 text-slate-300">
                                سيتم إنشاء المكتب وتحويل حساب مقدم الطلب
                                إلى مالك مكتب، وإنشاء اشتراك مبدئي بقيمة
                                1000 ريال وحالته قيد الانتظار.
                            </p>

                            <button
                                type="submit"
                                onclick="return confirm('هل أنت متأكد من قبول المكتب؟')"
                                class="w-full px-5 py-3 mt-5 font-black text-white transition bg-green-600 rounded-xl hover:bg-green-500"
                            >
                                قبول وإنشاء المكتب
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.office-applications.review',
                                $application
                            ) }}"
                            class="p-5 border rounded-2xl border-red-500/20 bg-red-500/5"
                        >
                            @csrf
                            @method('PATCH')

                            <input
                                type="hidden"
                                name="decision"
                                value="reject"
                            >

                            <h2 class="text-xl font-black text-red-200">
                                رفض الطلب
                            </h2>

                            <label
                                for="rejection_reason"
                                class="block mt-4 mb-2 font-bold text-white"
                            >
                                سبب الرفض
                            </label>

                            <textarea
                                id="rejection_reason"
                                name="rejection_reason"
                                rows="5"
                                required
                                class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                placeholder="اكتب سبب رفض طلب المكتب..."
                            >{{ old('rejection_reason') }}</textarea>

                            <button
                                type="submit"
                                onclick="return confirm('هل أنت متأكد من رفض الطلب؟')"
                                class="w-full px-5 py-3 mt-5 font-black text-white transition bg-red-600 rounded-xl hover:bg-red-500"
                            >
                                رفض الطلب
                            </button>
                        </form>
                    </div>
                @endif

                @if (
                    $application->status === 'rejected'
                    && $application->rejection_reason
                )
                    <div class="p-5 mt-8 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <p class="font-black text-red-200">
                            سبب رفض الطلب
                        </p>

                        <p class="mt-2 leading-8 text-red-100">
                            {{ $application->rejection_reason }}
                        </p>
                    </div>
                @endif

                @if ($application->reviewer)
                    <div class="p-5 mt-6 border rounded-2xl border-white/10 bg-white/5">
                        <p class="text-xs text-slate-400">
                            تمت مراجعة الطلب بواسطة
                        </p>

                        <p class="mt-2 font-bold text-white">
                            {{ $application->reviewer->name }}
                        </p>

                        @if ($application->reviewed_at)
                            <p class="mt-1 text-sm text-slate-400">
                                {{ $application->reviewed_at->format('Y-m-d H:i') }}
                            </p>
                        @endif
                    </div>
                @endif

                <div class="mt-8">
                    <a
                        href="{{ route('admin.office-applications.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        العودة إلى جميع الطلبات
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
