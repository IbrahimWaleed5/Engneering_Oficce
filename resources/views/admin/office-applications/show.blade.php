<x-app-layout>

    @php
        $currentUser = auth()->user();
        $dashboardRoute = Route::has('dashboard') ? route('dashboard') : url('/dashboard');
        $consultationsRoute = Route::has('consultations.index') ? route('consultations.index') : url('/consultations');
        $officesRoute = Route::has('admin.offices.index') ? route('admin.offices.index') : url('/admin/offices');
        $applicationsRoute = Route::has('admin.office-applications.index') ? route('admin.office-applications.index') : url('/admin/office-applications');
        $subscriptionsRoute = Route::has('admin.office-subscriptions.index') ? route('admin.office-subscriptions.index') : '#';
        $profileRoute = Route::has('profile.edit') ? route('profile.edit') : url('/profile');
        $notificationsRoute = Route::has('notifications.index') ? route('notifications.index') : $dashboardRoute;
    @endphp

    <style>
        [x-cloak]{display:none!important}
        body.office-apps-menu-open{overflow:hidden}
        .office-apps-page{min-height:100vh;overflow-x:hidden;color:#dae2fd;background:
            radial-gradient(circle at 12% 12%,rgba(37,99,235,.16),transparent 32%),
            radial-gradient(circle at 88% 10%,rgba(131,67,244,.12),transparent 30%),#0b1326;
            font-family:'Be Vietnam Pro','Almarai',system-ui,sans-serif}
        .office-apps-glass{background:rgba(23,31,51,.52);border:1px solid rgba(255,255,255,.08);
            box-shadow:0 18px 45px rgba(0,0,0,.18);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
        .office-apps-card{transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease}
        .office-apps-card:hover{transform:translateY(-4px);border-color:rgba(180,197,255,.24);box-shadow:0 22px 55px rgba(0,0,0,.26)}
        .office-apps-link{transition:background-color .2s ease,color .2s ease,transform .2s ease}
        .office-apps-link:hover{transform:translateX(-2px)}
        .office-apps-mobile-drawer{width:min(88vw,390px)}
        .office-apps-scroll::-webkit-scrollbar{width:8px;height:8px}
        .office-apps-scroll::-webkit-scrollbar-track{background:rgba(11,19,38,.55)}
        .office-apps-scroll::-webkit-scrollbar-thumb{background:rgba(67,70,85,.70);border-radius:999px}
        @media(max-width:1023px){
            .office-apps-desktop-sidebar,.office-apps-desktop-topbar{display:none!important}
            .office-apps-main{margin-right:0!important;padding-top:7rem!important}
        }
    </style>

    <div class="office-apps-page" dir="rtl" x-data="{ mobileMenuOpen:false }"
         x-init="$watch('mobileMenuOpen',v=>document.body.classList.toggle('office-apps-menu-open',v))"
         @keydown.escape.window="mobileMenuOpen=false">

        <header class="fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button type="button" @click="mobileMenuOpen=true" aria-label="فتح القائمة"
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] text-white shadow-lg active:scale-95">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
                <div class="min-w-0 text-center">
                    <p class="truncate text-lg font-black text-[#b4c5ff]">صرح الهندسة</p>
                    <p class="truncate text-xs text-[#c3c6d7]">طلبات المكاتب</p>
                </div>
                <a href="{{ $notificationsRoute }}" aria-label="الإشعارات"
                   class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-[#c3c6d7]">🔔</a>
            </div>
        </header>

        <div x-cloak x-show="mobileMenuOpen" x-transition.opacity @click="mobileMenuOpen=false"
             class="fixed inset-0 z-[80] bg-black/75 backdrop-blur-sm lg:hidden"></div>

        <aside x-cloak x-show="mobileMenuOpen"
               x-transition:enter="transition duration-300 ease-out"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition duration-200 ease-in"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full"
               class="office-apps-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326]/98 shadow-2xl backdrop-blur-2xl lg:hidden">
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <div><h2 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h2><p class="mt-1 text-sm text-[#c3c6d7]">قائمة إدارة النظام</p></div>
                <button type="button" @click="mobileMenuOpen=false" aria-label="إغلاق القائمة"
                        class="flex items-center justify-center w-12 h-12 text-white border rounded-2xl border-white/10 bg-white/5">✕</button>
            </div>
            <nav class="flex-1 p-5 space-y-3 overflow-y-auto office-apps-scroll">
                <a href="{{ $dashboardRoute }}" @click="mobileMenuOpen=false" class="office-apps-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⌂ <span>لوحة التحكم</span></a>
                <a href="{{ $consultationsRoute }}" @click="mobileMenuOpen=false" class="office-apps-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">📄 <span>الاستشارات</span></a>
                <a href="{{ $officesRoute }}" @click="mobileMenuOpen=false" class="office-apps-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">🏢 <span>المكاتب الهندسية</span></a>
                <a href="{{ $applicationsRoute }}" @click="mobileMenuOpen=false" class="flex items-center gap-4 rounded-2xl border border-blue-400/20 bg-gradient-to-l from-blue-600/25 to-violet-600/20 px-5 py-4 font-black text-[#dbe1ff] shadow-lg shadow-blue-950/30">📋 <span>طلبات إنشاء المكاتب</span></a>
                @if($subscriptionsRoute !== '#')
                    <a href="{{ $subscriptionsRoute }}" @click="mobileMenuOpen=false" class="office-apps-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">💳 <span>اشتراكات المكاتب</span></a>
                @endif
                <a href="{{ $profileRoute }}" @click="mobileMenuOpen=false" class="office-apps-link flex items-center gap-4 rounded-2xl px-5 py-4 font-black text-[#c3c6d7] hover:bg-white/5 hover:text-white">⚙ <span>الإعدادات</span></a>
            </nav>
            <div class="p-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 break-all text-xs text-[#c3c6d7]">{{ $currentUser->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="w-full px-5 py-4 font-black text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">تسجيل الخروج</button>
                </form>
            </div>
        </aside>

        <aside class="office-apps-desktop-sidebar fixed right-0 top-0 z-50 hidden h-screen w-72 flex-col border-l border-white/10 bg-[#131b2e]/90 p-5 shadow-2xl backdrop-blur-xl lg:flex">
            <div class="px-3 mb-8"><h1 class="text-2xl font-black text-[#b4c5ff]">صرح الهندسة</h1><p class="mt-1 text-sm text-[#c3c6d7]/65">نظام الإدارة الفاخر</p></div>
            <nav class="flex-1 space-y-2 overflow-y-auto office-apps-scroll">
                <a href="{{ $dashboardRoute }}" class="office-apps-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="office-apps-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الاستشارات</a>
                <a href="{{ $officesRoute }}" class="office-apps-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">المكاتب الهندسية</a>
                <a href="{{ $applicationsRoute }}" class="block rounded-xl border-r-4 border-[#b4c5ff] bg-blue-600/20 px-4 py-3 text-sm font-black text-[#b4c5ff]">طلبات إنشاء المكاتب</a>
                @if($subscriptionsRoute !== '#')<a href="{{ $subscriptionsRoute }}" class="office-apps-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">اشتراكات المكاتب</a>@endif
                <a href="{{ $profileRoute }}" class="office-apps-link block rounded-xl px-4 py-3 text-sm font-bold text-[#c3c6d7] hover:bg-white/5 hover:text-white">الإعدادات</a>
            </nav>
            <div class="pt-5 mt-5 border-t border-white/10">
                <div class="p-4 mb-4 border rounded-2xl border-white/10 bg-white/5">
                    <p class="font-black text-white">{{ $currentUser->name }}</p>
                    <p class="mt-1 break-all text-xs text-[#8d90a0]">{{ $currentUser->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="w-full px-4 py-3 font-bold text-red-200 border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20">تسجيل الخروج</button>
                </form>
            </div>
        </aside>

        <header class="office-apps-desktop-topbar fixed left-0 right-72 top-0 z-40 hidden h-20 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-8 backdrop-blur-xl lg:flex">
            <div><h2 class="text-xl font-black text-white">طلبات إنشاء المكاتب</h2><p class="mt-1 text-xs text-[#8d90a0]">لوحة مدير النظام</p></div>
            <div class="flex items-center gap-3">
                <a href="{{ $notificationsRoute }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-[#c3c6d7]">🔔</a>
                <div class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 font-black text-[#b4c5ff]">{{ mb_substr($currentUser->name ?? 'م',0,1) }}</div>
            </div>
        </header>

        <main class="min-h-screen px-4 office-apps-main pt-28 pb-14 sm:px-6 lg:mr-72 lg:px-8">
            <div class="max-w-6xl mx-auto">
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
                                إلى مالك مكتب، وإنشاء اشتراك مبدئي بالقيمة
                                والمدة اللتين تحددهما الآن.
                            </p>


                            <div class="grid gap-4 mt-5 sm:grid-cols-2">
                                <div>
                                    <label for="subscription_amount" class="block mb-2 text-sm font-bold text-white">
                                        قيمة الاشتراك
                                    </label>

                                    <input
                                        id="subscription_amount"
                                        name="subscription_amount"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('subscription_amount', 1000) }}"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >
                                </div>

                                <div>
                                    <label for="subscription_currency" class="block mb-2 text-sm font-bold text-white">
                                        العملة
                                    </label>

                                    <select
                                        id="subscription_currency"
                                        name="subscription_currency"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >
                                        @foreach (['SAR', 'USD', 'ILS', 'JOD', 'EUR'] as $currency)
                                            <option value="{{ $currency }}" @selected(old('subscription_currency', 'SAR') === $currency)>
                                                {{ $currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="duration_value" class="block mb-2 text-sm font-bold text-white">
                                        مقدار المدة
                                    </label>

                                    <input
                                        id="duration_value"
                                        name="duration_value"
                                        type="number"
                                        min="1"
                                        max="120"
                                        value="{{ old('duration_value', 1) }}"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >
                                </div>

                                <div>
                                    <label for="duration_unit" class="block mb-2 text-sm font-bold text-white">
                                        وحدة المدة
                                    </label>

                                    <select
                                        id="duration_unit"
                                        name="duration_unit"
                                        required
                                        class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    >
                                        <option value="day" @selected(old('duration_unit') === 'day')>يوم</option>
                                        <option value="month" @selected(old('duration_unit', 'month') === 'month')>شهر</option>
                                        <option value="year" @selected(old('duration_unit') === 'year')>سنة</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="subscription_notes" class="block mb-2 text-sm font-bold text-white">
                                    ملاحظات الاشتراك
                                </label>

                                <textarea
                                    id="subscription_notes"
                                    name="subscription_notes"
                                    rows="3"
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                    placeholder="ملاحظات اختيارية عن قيمة أو مدة الاشتراك"
                                >{{ old('subscription_notes') }}</textarea>
                            </div>

                            <button
                                type="submit"
                                onclick="return confirm('هل أنت متأكد من قبول المكتب وإنشاء الاشتراك بهذه القيمة والمدة؟')"
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
        </main>

    </div>
</x-app-layout>
