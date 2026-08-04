<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 text-center border rounded-3xl border-white/10 bg-slate-900/70 sm:p-8">
                @foreach (['success' => 'green', 'info' => 'cyan', 'error' => 'red'] as $messageType => $color)
                    @if (session($messageType))
                        <div class="p-4 mb-6 border rounded-2xl text-{{ $color }}-100 border-{{ $color }}-500/20 bg-{{ $color }}-500/10">
                            {{ session($messageType) }}
                        </div>
                    @endif
                @endforeach

                @if (! $application)
                    <div class="text-6xl">🏢</div>
                    <h1 class="mt-5 text-2xl font-black text-white">لا يوجد لديك طلب مكتب</h1>
                    <p class="mt-3 leading-7 text-slate-400">
                        يمكنك تقديم طلب جديد لتسجيل مكتب هندسي داخل النظام.
                    </p>
                    <a href="{{ route('office-applications.create') }}"
                       class="inline-flex items-center justify-center px-6 py-3 mt-6 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500">
                        تقديم طلب مكتب
                    </a>
                @else
                    @php
                        $statusData = match ($application->status) {
                            'approved' => [
                                'title' => 'تم قبول طلب المكتب',
                                'description' => 'تم إنشاء المكتب بنجاح. راجع تفاصيل الاشتراك وأكمل خطوة الدفع.',
                                'class' => 'text-green-300',
                                'box' => 'border-green-500/20 bg-green-500/10',
                                'icon' => '✅',
                            ],
                            'rejected' => [
                                'title' => 'تم رفض طلب المكتب',
                                'description' => 'راجع سبب الرفض الموضح أدناه.',
                                'class' => 'text-red-300',
                                'box' => 'border-red-500/20 bg-red-500/10',
                                'icon' => '⛔',
                            ],
                            'cancelled' => [
                                'title' => 'تم إلغاء الطلب',
                                'description' => 'طلب تسجيل المكتب ملغي حاليًا.',
                                'class' => 'text-slate-300',
                                'box' => 'border-white/10 bg-white/5',
                                'icon' => '✖',
                            ],
                            default => [
                                'title' => 'الطلب قيد المراجعة',
                                'description' => 'تم استلام طلبك وسيقوم مدير النظام بمراجعته.',
                                'class' => 'text-yellow-300',
                                'box' => 'border-yellow-500/20 bg-yellow-500/10',
                                'icon' => '⏳',
                            ],
                        };

                        $durationLabel = $latestSubscription?->durationLabel();
                    @endphp

                    <div class="text-6xl">{{ $statusData['icon'] }}</div>

                    <h1 class="mt-5 text-2xl font-black {{ $statusData['class'] }}">
                        {{ $statusData['title'] }}
                    </h1>

                    <p class="mt-3 text-lg font-bold text-white">
                        {{ $application->office_name }}
                    </p>

                    <p class="mt-3 leading-7 text-slate-400">
                        {{ $statusData['description'] }}
                    </p>

                    <div class="grid gap-4 mt-8 text-right sm:grid-cols-2">
                        <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                            <p class="text-xs text-slate-400">رقم الطلب</p>
                            <p class="mt-2 font-black text-white">#{{ $application->id }}</p>
                        </div>

                        <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                            <p class="text-xs text-slate-400">تاريخ تقديم الطلب</p>
                            <p class="mt-2 font-black text-white">{{ $application->created_at?->format('Y-m-d H:i') }}</p>
                        </div>

                        <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                            <p class="text-xs text-slate-400">المدينة</p>
                            <p class="mt-2 font-black text-white">{{ $application->city ?: 'غير محددة' }}</p>
                        </div>

                        <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                            <p class="text-xs text-slate-400">آخر تحديث</p>
                            <p class="mt-2 font-black text-white">{{ $application->updated_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    @if ($application->status === 'pending')
                        <div class="p-5 mt-7 text-right border rounded-2xl {{ $statusData['box'] }}">
                            <p class="font-black text-yellow-200">الطلب تحت المراجعة</p>
                            <p class="mt-2 leading-7 text-slate-300">
                                لا تحتاج إلى إرسال طلب جديد. عند اتخاذ القرار ستظهر النتيجة في هذه الصفحة.
                            </p>
                        </div>
                    @endif

                    @if ($application->status === 'approved')
                        <div class="p-5 mt-7 text-right border rounded-2xl {{ $statusData['box'] }}">
                            <p class="font-black text-green-200">تفاصيل الاشتراك</p>

                            @if ($latestSubscription)
                                <div class="grid gap-3 mt-4 sm:grid-cols-2">
                                    <div class="p-3 border rounded-xl border-white/10 bg-black/10">
                                        <p class="text-xs text-green-100/70">القيمة</p>
                                        <p class="mt-1 font-black text-white">
                                            {{ number_format((float) $latestSubscription->amount, 2) }}
                                            {{ $latestSubscription->currency }}
                                        </p>
                                    </div>

                                    <div class="p-3 border rounded-xl border-white/10 bg-black/10">
                                        <p class="text-xs text-green-100/70">المدة</p>
                                        <p class="mt-1 font-black text-white">{{ $durationLabel }}</p>
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('office.subscription') }}"
                               class="inline-flex items-center justify-center px-6 py-3 mt-5 font-black text-white transition bg-green-600 rounded-xl hover:bg-green-500">
                                فتح صفحة الاشتراك ورفع الإيصال
                            </a>
                        </div>
                    @endif

                    @if ($application->status === 'rejected' && $application->rejection_reason)
                        <div class="p-5 mt-7 text-right border rounded-2xl {{ $statusData['box'] }}">
                            <p class="font-black text-red-200">سبب الرفض</p>
                            <p class="mt-2 leading-8 text-red-100">{{ $application->rejection_reason }}</p>
                        </div>
                    @endif

                    @if ($application->reviewer)
                        <div class="p-4 mt-6 text-right border rounded-2xl border-white/10 bg-white/5">
                            <p class="text-xs text-slate-400">تمت المراجعة بواسطة</p>
                            <p class="mt-2 font-bold text-white">{{ $application->reviewer->name }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
