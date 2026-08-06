<x-app-layout>
    @php
        $isOperational =
            $office->status === 'active'
            && $office->subscription_status === 'active'
            && $office->subscription_ends_at
            && $office->subscription_ends_at->isFuture();

        $officeStatus = match ($office->status) {
            'active' => [
                'label' => 'مكتب فعال',
                'class' => 'text-green-200 border-green-500/30 bg-green-500/10',
            ],
            'suspended' => [
                'label' => 'مكتب موقوف',
                'class' => 'text-red-200 border-red-500/30 bg-red-500/10',
            ],
            'closed' => [
                'label' => 'مكتب مغلق',
                'class' => 'text-slate-200 border-slate-500/30 bg-slate-500/10',
            ],
            default => [
                'label' => 'قيد المراجعة',
                'class' => 'text-yellow-200 border-yellow-500/30 bg-yellow-500/10',
            ],
        };

        $subscriptionStatus = match ($office->subscription_status) {
            'active' => [
                'label' => 'اشتراك فعال',
                'class' => 'text-green-200 border-green-500/30 bg-green-500/10',
            ],
            'expired' => [
                'label' => 'اشتراك منتهي',
                'class' => 'text-red-200 border-red-500/30 bg-red-500/10',
            ],
            'cancelled' => [
                'label' => 'اشتراك ملغي',
                'class' => 'text-red-200 border-red-500/30 bg-red-500/10',
            ],
            default => [
                'label' => 'بانتظار التفعيل',
                'class' => 'text-yellow-200 border-yellow-500/30 bg-yellow-500/10',
            ],
        };
    @endphp

    <div class="py-10" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- بطاقة الترحيب بنفس تصميم Dashboard الأساسي --}}
            <div class="relative p-8 mb-8 overflow-hidden border shadow-xl rounded-2xl bg-gradient-to-l from-blue-700 to-cyan-600 border-blue-500/30">
                <div class="absolute w-48 h-48 rounded-full -top-20 -left-10 bg-white/10"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="mb-2 text-blue-100">
                            لوحة المكتب الهندسي
                        </p>

                        <h1 class="mb-3 text-3xl font-bold text-white">
                            {{ $office->name }}
                        </h1>

                        <p class="max-w-2xl leading-7 text-blue-100">
                            إدارة الاستشارات والأعضاء وطلبات انضمام المهندسين والاشتراك الشهري وبيانات المكتب.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $officeStatus['class'] }}">
                            {{ $officeStatus['label'] }}
                        </span>

                        <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $subscriptionStatus['class'] }}">
                            {{ $subscriptionStatus['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- الرسائل --}}
            @if (session('success'))
                <div class="p-4 mb-6 text-green-200 border border-green-700 rounded-xl bg-green-900/30">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-200 border border-red-700 rounded-xl bg-red-900/30">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-200 border border-red-700 rounded-xl bg-red-900/30">
                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- تنبيهات حالة المكتب --}}
            @if ($office->status === 'suspended')
                <div class="p-6 mb-8 border rounded-2xl border-red-500/30 bg-red-500/10">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="flex items-center justify-center flex-none w-12 h-12 text-2xl rounded-xl bg-red-500/20">
                            ⛔
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-red-200">
                                المكتب موقوف عن العمل
                            </h2>

                            <p class="mt-2 leading-7 text-red-100">
                                لا يمكن استقبال استشارات أو طلبات انضمام جديدة حتى يعيد مدير النظام تفعيل المكتب.
                            </p>

                            @if ($office->suspension_reason)
                                <p class="p-4 mt-4 leading-7 text-red-100 border rounded-xl border-red-500/20 bg-red-950/20">
                                    {{ $office->suspension_reason }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif (! $isOperational)
                <div class="p-6 mb-8 border rounded-2xl border-orange-500/30 bg-orange-500/10">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center flex-none w-12 h-12 text-2xl rounded-xl bg-orange-500/20">
                                ⚠️
                            </div>

                            <div>
                                <h2 class="text-xl font-black text-orange-200">
                                    الاشتراك غير فعال
                                </h2>

                                <p class="mt-2 leading-7 text-orange-100">
                                    ارفع إيصال الاشتراك الشهري لتفعيل جميع خصائص المكتب.
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('office.subscription') }}"
                            class="inline-flex items-center justify-center px-5 py-3 font-black text-white transition bg-orange-600 rounded-xl hover:bg-orange-500"
                        >
                            إدارة الاشتراك
                        </a>
                    </div>
                </div>
            @endif

            {{-- بطاقات الوصول السريع --}}
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4">
                <a
                    href="{{ route('office.consultations.index') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-blue-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-blue-600/20">
                        📐
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        استشارات المكتب
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        متابعة الاستشارات وتعيين المهندسين.
                    </p>
                </a>

                <a
                    href="{{ route('office.members.index') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-cyan-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-cyan-600/20">
                        👥
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        أعضاء المكتب
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        إدارة المهندسين وأدوارهم داخل المكتب.
                    </p>
                </a>

                <a
                    href="{{ route('office-membership-applications.index') }}"
                    class="relative p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-yellow-500"
                >
                    @if (($statistics['pending_applications'] ?? 0) > 0)
                        <span class="absolute px-2 py-1 text-xs font-black text-white bg-red-600 rounded-full top-4 left-4">
                            {{ $statistics['pending_applications'] }}
                        </span>
                    @endif

                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-yellow-600/20">
                        📄
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        طلبات الانضمام
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        قبول أو رفض طلبات المهندسين.
                    </p>
                </a>

                <a
                    href="{{ route('office.subscription') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-green-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-green-600/20">
                        💳
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        اشتراك المكتب
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        رفع الإيصال ومتابعة حالة الاشتراك.
                    </p>
                </a>

                <a
                    href="{{ route('office.profile') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-purple-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-purple-600/20">
                        🏢
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        بيانات المكتب
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        تعديل معلومات المكتب وبيانات التواصل.
                    </p>
                </a>

                {{-- تذاكر الدعم الفني --}}
                <a
                    href="{{ route('support.index') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-rose-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-rose-600/20">
                        🎧
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        الدعم الفني
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        افتح تذكرة دعم وتابع الرد مع موظف الدعم.
                    </p>
                </a>

                {{-- المساعد الذكي --}}
                <a
                    href="{{ route('support.center') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-violet-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-violet-600/20">
                        🤖
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        المساعد الذكي
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        اسأل مساعد الوليد الهندسي أو اطلب التحويل لموظف الدعم.
                    </p>
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-orange-500"
                >
                    <div class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-orange-600/20">
                        🏠
                    </div>

                    <h2 class="text-lg font-bold text-white">
                        اللوحة الرئيسية
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        العودة إلى لوحة النظام الأساسية.
                    </p>
                </a>
            </div>

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 xl:grid-cols-4">
                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">
                                أعضاء فعالون
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-white">
                                {{ $statistics['active_members'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-blue-600/20">
                            👥
                        </div>
                    </div>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">
                                طلبات انضمام معلقة
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-yellow-300">
                                {{ $statistics['pending_applications'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-yellow-600/20">
                            📄
                        </div>
                    </div>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">
                                استشارات قيد التنفيذ
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-blue-300">
                                {{ $statistics['in_progress_consultations'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-blue-600/20">
                            🛠️
                        </div>
                    </div>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-slate-400">
                                استشارات مكتملة
                            </p>

                            <h3 class="mt-2 text-3xl font-black text-green-300">
                                {{ $statistics['completed_consultations'] ?? 0 }}
                            </h3>
                        </div>

                        <div class="flex items-center justify-center w-12 h-12 text-2xl rounded-xl bg-green-600/20">
                            ✅
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
                <div class="space-y-8 xl:col-span-2">

                    {{-- أحدث الاستشارات --}}
                    <section class="overflow-hidden border shadow rounded-2xl bg-slate-900 border-slate-800">
                        <div class="flex flex-col gap-4 p-6 border-b sm:flex-row sm:items-center sm:justify-between border-slate-800">
                            <div>
                                <h2 class="text-2xl font-black text-white">
                                    أحدث الاستشارات
                                </h2>

                                <p class="mt-2 text-sm text-slate-400">
                                    آخر الاستشارات المحولة إلى المكتب.
                                </p>
                            </div>

                            <a
                                href="{{ route('office.consultations.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                            >
                                عرض الكل
                            </a>
                        </div>

                        <div class="divide-y divide-slate-800">
                            @forelse ($latestConsultations as $consultation)
                                @php
                                    $consultationStatus = match ($consultation->status) {
                                        'completed' => [
                                            'label' => 'مكتملة',
                                            'class' => 'text-green-300 bg-green-500/10',
                                        ],
                                        'in_progress' => [
                                            'label' => 'قيد التنفيذ',
                                            'class' => 'text-blue-300 bg-blue-500/10',
                                        ],
                                        'cancelled' => [
                                            'label' => 'ملغاة',
                                            'class' => 'text-red-300 bg-red-500/10',
                                        ],
                                        default => [
                                            'label' => 'قيد الانتظار',
                                            'class' => 'text-yellow-300 bg-yellow-500/10',
                                        ],
                                    };
                                @endphp

                                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-black text-white">
                                            {{ $consultation->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $consultation->customer?->name ?? 'عميل غير معروف' }}
                                            —
                                            {{ $consultation->consultationType?->name ?? 'نوع غير محدد' }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-black rounded-full {{ $consultationStatus['class'] }}">
                                            {{ $consultationStatus['label'] }}
                                        </span>

                                        <span class="text-sm text-slate-300">
                                            {{ $consultation->engineer?->name ?? 'بدون مهندس' }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center text-slate-400">
                                    لا توجد استشارات محولة إلى المكتب بعد.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    {{-- أحدث طلبات الانضمام --}}
                    <section class="overflow-hidden border shadow rounded-2xl bg-slate-900 border-slate-800">
                        <div class="flex flex-col gap-4 p-6 border-b sm:flex-row sm:items-center sm:justify-between border-slate-800">
                            <div>
                                <h2 class="text-2xl font-black text-white">
                                    أحدث طلبات الانضمام
                                </h2>

                                <p class="mt-2 text-sm text-slate-400">
                                    آخر المهندسين الذين تقدموا للمكتب.
                                </p>
                            </div>

                            <a
                                href="{{ route('office-membership-applications.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white transition border rounded-xl border-slate-700 bg-slate-800 hover:bg-slate-700"
                            >
                                عرض الكل
                            </a>
                        </div>

                        <div class="divide-y divide-slate-800">
                            @forelse ($latestApplications as $application)
                                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-black text-white">
                                            {{ $application->engineer?->name ?? 'مهندس غير موجود' }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            {{ $application->specialty?->name ?? 'تخصص غير محدد' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-black rounded-full
                                            {{ $application->status === 'approved'
                                                ? 'text-green-300 bg-green-500/10'
                                                : ($application->status === 'rejected'
                                                    ? 'text-red-300 bg-red-500/10'
                                                    : 'text-yellow-300 bg-yellow-500/10') }}">
                                            {{ match ($application->status) {
                                                'approved' => 'مقبول',
                                                'rejected' => 'مرفوض',
                                                default => 'قيد المراجعة',
                                            } }}
                                        </span>

                                        <a
                                            href="{{ route('office-membership-applications.show', $application) }}"
                                            class="text-sm font-bold text-blue-300 hover:text-blue-200"
                                        >
                                            عرض الطلب
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center text-slate-400">
                                    لا توجد طلبات انضمام حتى الآن.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="space-y-8">

                    {{-- حالة الاشتراك --}}
                    <section class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                        <h2 class="text-xl font-black text-white">
                            حالة الاشتراك
                        </h2>

                        <div class="mt-5 space-y-4">
                            <div class="p-4 rounded-xl bg-slate-800/70">
                                <p class="text-xs text-slate-400">
                                    القيمة الشهرية
                                </p>

                                <p class="mt-2 text-xl font-black text-white">
                                    {{ number_format((float) ($office->monthly_subscription_amount ?? 1000), 2) }}
                                    {{ $office->subscription_currency ?? 'SAR' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-800/70">
                                <p class="text-xs text-slate-400">
                                    نهاية الاشتراك
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $office->subscription_ends_at?->format('Y-m-d') ?? 'غير مفعل' }}
                                </p>
                            </div>

                            @if ($latestSubscription)
                                <div class="p-4 rounded-xl bg-slate-800/70">
                                    <p class="text-xs text-slate-400">
                                        آخر طلب اشتراك
                                    </p>

                                    <p class="mt-2 font-black text-white">
                                        {{ match ($latestSubscription->status) {
                                            'active' => 'فعال',
                                            'under_review' => 'قيد المراجعة',
                                            'rejected' => 'مرفوض',
                                            'expired' => 'منتهي',
                                            default => 'بانتظار الإيصال',
                                        } }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <a
                            href="{{ route('office.subscription') }}"
                            class="inline-flex items-center justify-center w-full px-5 py-3 mt-5 font-black text-white transition bg-blue-600 rounded-xl hover:bg-blue-500"
                        >
                            إدارة الاشتراك
                        </a>
                    </section>

                    {{-- معلومات المكتب --}}
                    <section class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                        <h2 class="text-xl font-black text-white">
                            معلومات المكتب
                        </h2>

                        <div class="mt-5 space-y-5 text-sm">
                            <div>
                                <p class="text-slate-400">
                                    مالك المكتب
                                </p>

                                <p class="mt-1 font-bold text-white">
                                    {{ $office->owner?->name ?? 'غير معروف' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-slate-400">
                                    البريد الإلكتروني
                                </p>

                                <p class="mt-1 font-bold text-white break-all">
                                    {{ $office->email }}
                                </p>
                            </div>

                            <div>
                                <p class="text-slate-400">
                                    الموقع
                                </p>

                                <p class="mt-1 font-bold text-white">
                                    {{ $office->city ?: 'غير محدد' }}

                                    @if ($office->country)
                                        — {{ $office->country }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('office.profile') }}"
                            class="inline-flex items-center justify-center w-full px-5 py-3 mt-5 font-bold text-white transition border rounded-xl border-slate-700 bg-slate-800 hover:bg-slate-700"
                        >
                            تعديل بيانات المكتب
                        </a>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
