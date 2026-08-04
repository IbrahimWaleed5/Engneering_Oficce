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
            <div class="mx-auto max-w-[1700px]">
                @if(session('success'))<div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">{{ session('error') }}</div>@endif

                <section class="mb-9">
                    <p class="text-sm font-black text-[#b4c5ff]">إدارة المكاتب</p>
                    <h1 class="mt-2 text-3xl font-black text-white sm:text-4xl">طلبات انضمام المكاتب الهندسية</h1>
                    <p class="mt-3 max-w-3xl leading-7 text-[#c3c6d7]">راجع طلبات المكاتب الجديدة، ثم وافق عليها أو ارفضها.</p>
                </section>

                <section class="grid gap-5 mb-8 sm:grid-cols-2 xl:grid-cols-3">
                    <article class="p-6 office-apps-card office-apps-glass rounded-2xl border-yellow-500/20">
                        <p class="text-sm font-black text-[#c3c6d7]">قيد المراجعة</p>
                        <div class="flex items-end justify-between mt-6"><span class="text-4xl font-black text-yellow-300">{{ $statistics['pending'] ?? 0 }}</span><span class="flex items-center justify-center w-12 h-12 text-yellow-300 rounded-2xl bg-yellow-500/10">⏳</span></div>
                    </article>
                    <article class="p-6 office-apps-card office-apps-glass rounded-2xl border-green-500/20">
                        <p class="text-sm font-black text-[#c3c6d7]">الطلبات المقبولة</p>
                        <div class="flex items-end justify-between mt-6"><span class="text-4xl font-black text-green-300">{{ $statistics['approved'] ?? 0 }}</span><span class="flex items-center justify-center w-12 h-12 text-green-300 rounded-2xl bg-green-500/10">✓</span></div>
                    </article>
                    <article class="p-6 office-apps-card office-apps-glass rounded-2xl border-red-500/20 sm:col-span-2 xl:col-span-1">
                        <p class="text-sm font-black text-[#c3c6d7]">الطلبات المرفوضة</p>
                        <div class="flex items-end justify-between mt-6"><span class="text-4xl font-black text-red-300">{{ $statistics['rejected'] ?? 0 }}</span><span class="flex items-center justify-center w-12 h-12 text-red-300 rounded-2xl bg-red-500/10">✕</span></div>
                    </article>
                </section>

                <section class="overflow-hidden office-apps-glass rounded-3xl">
                    <div class="flex flex-col gap-3 p-6 border-b border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div><h2 class="text-2xl font-black text-white">سجل الطلبات</h2><p class="mt-1 text-sm text-[#c3c6d7]">جميع طلبات إنشاء المكاتب وحالاتها.</p></div>
                        <a href="{{ $applicationsRoute }}" class="inline-flex items-center justify-center rounded-xl border border-[#b4c5ff]/30 px-5 py-3 font-black text-[#b4c5ff] hover:bg-[#b4c5ff]/10">تحديث القائمة</a>
                    </div>
                    <div class="overflow-x-auto office-apps-scroll">
                        <table class="w-full min-w-[1050px] text-sm">
                            <thead class="bg-[#2d3449]/45 text-[#c3c6d7]">
                                <tr><th class="p-4 text-right">رقم الطلب</th><th class="p-4 text-right">اسم المكتب</th><th class="p-4 text-right">صاحب الطلب</th><th class="p-4 text-right">المدينة</th><th class="p-4 text-right">الحالة</th><th class="p-4 text-right">تاريخ الطلب</th><th class="p-4 text-right">الإجراء</th></tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($applications as $application)
                                    @php
                                        $statusData = match($application->status){
                                            'approved'=>['label'=>'مقبول','class'=>'text-green-200 bg-green-500/10 border-green-500/20'],
                                            'rejected'=>['label'=>'مرفوض','class'=>'text-red-200 bg-red-500/10 border-red-500/20'],
                                            'cancelled'=>['label'=>'ملغي','class'=>'text-slate-300 bg-white/5 border-white/10'],
                                            default=>['label'=>'قيد المراجعة','class'=>'text-yellow-200 bg-yellow-500/10 border-yellow-500/20'],
                                        };
                                    @endphp
                                    <tr class="transition hover:bg-white/[0.03]">
                                        <td class="p-4 font-bold text-[#c3c6d7]">#{{ $application->id }}</td>
                                        <td class="p-4"><div class="font-black text-white">{{ $application->office_name }}</div><div class="mt-1 text-xs text-[#8d90a0]">{{ $application->email }}</div></td>
                                        <td class="p-4"><div class="font-bold text-white">{{ $application->applicant?->name ?? 'غير معروف' }}</div><div class="mt-1 text-xs text-[#8d90a0]">{{ $application->applicant?->email }}</div></td>
                                        <td class="p-4 text-[#c3c6d7]">{{ $application->city ?: 'غير محددة' }}</td>
                                        <td class="p-4"><span class="inline-flex rounded-full border px-3 py-1 text-xs font-black {{ $statusData['class'] }}">{{ $statusData['label'] }}</span></td>
                                        <td class="p-4 text-[#c3c6d7]">{{ $application->created_at?->format('Y-m-d H:i') }}</td>
                                        <td class="p-4">
                                            @if(Route::has('admin.office-applications.show'))
                                                <a href="{{ route('admin.office-applications.show',$application) }}" class="inline-flex items-center justify-center rounded-xl bg-[#2563eb] px-4 py-2 font-black text-white hover:brightness-110">عرض الطلب</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center p-14"><div class="flex items-center justify-center w-24 h-24 mx-auto text-5xl border rounded-3xl border-white/10 bg-white/5">🏢</div><h3 class="mt-6 text-2xl font-black text-white">لا توجد طلبات مكاتب هندسية حتى الآن</h3><p class="mt-3 text-[#c3c6d7]">ستظهر الطلبات الجديدة هنا فور تقديمها.</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                @if($applications->hasPages())
                    <div class="p-5 mt-8 office-apps-glass rounded-2xl">{{ $applications->links() }}</div>
                @endif
            </div>
        </main>

    </div>
</x-app-layout>
