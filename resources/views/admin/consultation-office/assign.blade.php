<x-app-layout>
    @php
        $currentUser = auth()->user();
        $dashboardRoute = Route::has('dashboard') ? route('dashboard') : url('/dashboard');
        $consultationsRoute = Route::has('consultations.index') ? route('consultations.index') : url('/consultations');
        $officesRoute = Route::has('engineering-offices.index') ? route('engineering-offices.index') : url('/engineering-offices');
        $profileRoute = Route::has('profile.edit') ? route('profile.edit') : url('/profile');
        $notificationsRoute = Route::has('notifications.index') ? route('notifications.index') : '#';
    @endphp

    <style>
        [x-cloak]{display:none!important}
        .transfer-page{--surface:#0b1326;--surface-lowest:#060e20;--surface-low:#131b2e;--surface-high:#222a3d;--surface-highest:#2d3449;--primary:#b4c5ff;--primary-container:#2563eb;--tertiary:#d2bbff;--secondary:#ffb1c7;--on-surface:#dae2fd;--on-surface-variant:#c3c6d7;--outline:#8d90a0;min-height:100vh;color:var(--on-surface);background:linear-gradient(rgba(11,19,38,.88),rgba(11,19,38,.94)),radial-gradient(circle at 15% 20%,rgba(37,99,235,.16),transparent 34%),radial-gradient(circle at 85% 12%,rgba(131,67,244,.14),transparent 30%),var(--surface);font-family:"Be Vietnam Pro","Almarai",system-ui,sans-serif}
        .transfer-glass{border:1px solid rgba(141,144,160,.12);background:rgba(45,52,73,.30);box-shadow:0 0 15px rgba(180,197,255,.08);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)}
        .transfer-detail{border:1px solid rgba(141,144,160,.10);background:rgba(45,52,73,.40)}
        .transfer-input{width:100%;border:1px solid rgba(141,144,160,.30);border-radius:.75rem;background:var(--surface-lowest);color:var(--on-surface);transition:border-color .2s ease,box-shadow .2s ease}
        .transfer-input:focus{border-color:var(--primary);box-shadow:0 0 0 1px var(--primary);outline:none}.transfer-input:disabled{cursor:not-allowed;opacity:.58}
        .transfer-sidebar-link{display:flex;align-items:center;gap:.75rem;border-radius:.75rem;padding:.75rem 1rem;color:var(--on-surface-variant);font-size:.875rem;font-weight:700;transition:.2s}
        .transfer-sidebar-link:hover{background:rgba(45,52,73,.55);color:white;transform:translateX(-2px)}.transfer-sidebar-link.active{background:var(--primary-container);color:white}
        .transfer-mobile-drawer{width:min(88vw,360px)}body.transfer-menu-open{overflow:hidden}
        @media(min-width:1024px){.transfer-desktop-sidebar{display:flex!important}.transfer-mobile-header,.transfer-mobile-overlay,.transfer-mobile-drawer{display:none!important}}
        @media(max-width:1023px){.transfer-desktop-sidebar{display:none!important}.transfer-main{margin-right:0!important;padding-top:6.5rem!important}}
        @media(max-width:640px){.transfer-main{padding-right:1rem!important;padding-left:1rem!important}.transfer-actions>*{width:100%}}
    </style>

    <div class="transfer-page" dir="rtl" x-data="{ mobileMenuOpen:false }" x-init="$watch('mobileMenuOpen',v=>document.body.classList.toggle('transfer-menu-open',v))" @keydown.escape.window="mobileMenuOpen=false">
        <header class="transfer-mobile-header fixed inset-x-0 top-0 z-[70] border-b border-white/10 bg-[#060e20]/95 px-4 py-3 shadow-2xl backdrop-blur-xl lg:hidden">
            <div class="flex items-center justify-between gap-3">
                <button type="button" @click="mobileMenuOpen=true" aria-label="فتح القائمة" class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-[#b4c5ff]/30 bg-[#2563eb] text-white shadow-lg active:scale-95">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <div class="min-w-0 text-center"><p class="truncate text-lg font-black text-[#b4c5ff]">Al-Waleed Engineering</p><p class="truncate text-xs text-[#c3c6d7]">تحويل الاستشارة</p></div>
                <a href="{{ $notificationsRoute }}" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-[#c3c6d7]" aria-label="الإشعارات">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9m10-2H5l1.5-2V9a5.5 5.5 0 0 1 11 0v4L19 15ZM10 20h4"/></svg>
                </a>
            </div>
        </header>

        <div x-cloak x-show="mobileMenuOpen" x-transition.opacity @click="mobileMenuOpen=false" class="transfer-mobile-overlay fixed inset-0 z-[80] bg-black/75 backdrop-blur-sm lg:hidden"></div>

        <aside x-cloak x-show="mobileMenuOpen" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-250 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="transfer-mobile-drawer fixed right-0 top-0 z-[90] flex h-dvh flex-col border-l border-white/10 bg-[#0b1326] shadow-2xl lg:hidden">
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <div><h2 class="text-2xl font-black text-[#b4c5ff]">Al-Waleed</h2><p class="mt-1 text-sm text-[#c3c6d7]">Engineering Office</p></div>
                <button type="button" @click="mobileMenuOpen=false" class="flex items-center justify-center w-12 h-12 text-white border rounded-2xl border-white/10 bg-white/5" aria-label="إغلاق القائمة"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg></button>
            </div>
            <nav class="flex-1 p-5 space-y-3 overflow-y-auto">
                <a href="{{ $dashboardRoute }}" class="transfer-sidebar-link" @click="mobileMenuOpen=false">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="transfer-sidebar-link active" @click="mobileMenuOpen=false">الاستشارات</a>
                <a href="{{ $officesRoute }}" class="transfer-sidebar-link" @click="mobileMenuOpen=false">المكاتب الهندسية</a>
                <a href="{{ $profileRoute }}" class="transfer-sidebar-link" @click="mobileMenuOpen=false">الإعدادات</a>
            </nav>
            @auth
                <div class="p-5 border-t border-white/10">
                    <div class="p-4 mb-4 rounded-2xl bg-white/5"><p class="font-black text-white">{{ $currentUser->name }}</p><p class="mt-1 truncate text-xs text-[#c3c6d7]">{{ $currentUser->email }}</p></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full px-4 py-3 font-black text-red-200 border rounded-xl border-red-500/20 bg-red-500/10">تسجيل الخروج</button></form>
                </div>
            @endauth
        </aside>

        <aside class="transfer-desktop-sidebar fixed right-0 top-0 z-50 hidden h-screen w-72 flex-col border-l border-white/10 bg-[#131b2e]/90 shadow-xl backdrop-blur-xl">
            <div class="p-6"><h2 class="text-2xl font-black text-[#b4c5ff]">Al-Waleed</h2><p class="mt-1 text-sm text-[#c3c6d7]">Engineering Office</p></div>
            <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
                <a href="{{ $dashboardRoute }}" class="transfer-sidebar-link">لوحة التحكم</a>
                <a href="{{ $consultationsRoute }}" class="transfer-sidebar-link active">الاستشارات</a>
                <a href="{{ $officesRoute }}" class="transfer-sidebar-link">المكاتب الهندسية</a>
                <a href="{{ $profileRoute }}" class="transfer-sidebar-link">الإعدادات</a>
            </nav>
            @auth
                <div class="p-4 border-t border-white/10">
                    <div class="p-3 mb-3 rounded-xl bg-white/5"><p class="font-bold text-white truncate">{{ $currentUser->name }}</p><p class="mt-1 truncate text-xs text-[#c3c6d7]">{{ $currentUser->email }}</p></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full px-4 py-3 font-bold text-right text-red-200 rounded-xl hover:bg-red-500/10">تسجيل الخروج</button></form>
                </div>
            @endauth
        </aside>

        <main class="min-h-screen px-6 py-8 transfer-main lg:mr-72 lg:px-8">
            <div class="flex flex-col w-full gap-6 mx-auto max-w-7xl">
                @if(session('success'))<div class="p-4 text-green-100 border rounded-xl border-green-500/20 bg-green-500/10">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="p-4 text-red-100 border rounded-xl border-red-500/20 bg-red-500/10">{{ session('error') }}</div>@endif
                @if(session('info'))<div class="p-4 border rounded-xl border-cyan-500/20 bg-cyan-500/10 text-cyan-100">{{ session('info') }}</div>@endif
                @if($errors->any())<div class="p-4 text-red-100 border rounded-xl border-red-500/20 bg-red-500/10"><ul class="space-y-2 text-sm">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul></div>@endif

                <section class="mb-2 text-center md:text-right">
                    <p class="text-xs font-black uppercase tracking-[.24em] text-[#b4c5ff]">إدارة الاستشارات</p>
                    <h1 class="mt-2 text-2xl font-black text-white sm:text-3xl">تحويل الاستشارة إلى مكتب هندسي</h1>
                    <p class="mt-3 max-w-2xl leading-7 text-[#c3c6d7]">اختر مكتبًا فعالًا باشتراك ساري، وسيتم إلغاء تعيين المهندس الحالي عند تحويل الاستشارة إلى المكتب.</p>
                </section>

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                    <aside class="order-2 xl:order-1 xl:col-span-4">
                        <section class="p-5 transfer-glass rounded-xl">
                            <div class="flex items-center gap-3 pb-4 border-b border-white/10"><div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#2563eb]/15 text-[#b4c5ff]"><svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linejoin="round" d="M4 21V7l8-4 8 4v14M8 10h2m4 0h2M8 14h2m4 0h2M8 18h2m4 0h2"/></svg></div><h2 class="text-xl font-black text-white">المكاتب المتاحة</h2></div>
                            <p class="mt-4 text-sm leading-6 text-[#c3c6d7]">تظهر فقط المكاتب الفعالة التي لديها اشتراك شهري ساري.</p>
                            <div class="mt-5 space-y-3">
                                @forelse($offices as $office)
                                    <button type="button" onclick="document.getElementById('office_id').value='{{ $office->id }}';document.getElementById('office_id').dispatchEvent(new Event('change'));document.getElementById('office_id').focus();" class="w-full rounded-xl border border-white/10 bg-[#222a3d]/55 p-4 text-right transition hover:border-[#b4c5ff]/30 hover:bg-[#2d3449]/75">
                                        <p class="font-black text-white">{{ $office->name }}</p><p class="mt-2 text-sm text-[#c3c6d7]">{{ $office->city ?: 'مدينة غير محددة' }}</p>
                                        <div class="flex flex-wrap gap-2 mt-3"><span class="px-3 py-1 text-xs font-black text-blue-200 border rounded-full border-blue-500/20 bg-blue-500/10">{{ $office->active_members_count ?? 0 }} عضو</span><span class="px-3 py-1 text-xs font-black border rounded-full border-cyan-500/20 bg-cyan-500/10 text-cyan-200">{{ $office->consultations_count ?? 0 }} استشارة</span></div>
                                        <p class="mt-3 text-xs text-green-300">الاشتراك حتى: {{ $office->subscription_ends_at?->format('Y-m-d') ?? 'غير محدد' }}</p>
                                    </button>
                                @empty
                                    <div class="rounded-lg border border-[#ffb1c7]/20 bg-[#222a3d]/55 p-4 text-center"><p class="text-sm font-bold leading-6 text-[#c3c6d7]">لا توجد مكاتب قابلة لاستقبال الاستشارة الآن.</p></div>
                                @endforelse
                            </div>
                        </section>
                    </aside>

                    <div class="flex flex-col order-1 gap-6 xl:order-2 xl:col-span-8">
                        <section class="p-5 transfer-glass rounded-xl sm:p-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-white/10"><div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#8343f4]/15 text-[#d2bbff]"><svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linejoin="round" d="M7 3h7l4 4v14H7zM14 3v5h5M10 12h5m-5 4h5"/></svg></div><h2 class="text-xl font-black text-white">بيانات الاستشارة</h2></div>
                            <div class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-2">
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">رقم الاستشارة</p><p class="mt-2 font-bold text-white">{{ $consultation->number }}</p></div>
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">العنوان</p><p class="mt-2 font-bold text-white">{{ $consultation->title }}</p></div>
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">العميل</p><p class="mt-2 font-bold text-white">{{ $consultation->customer?->name ?? 'غير معروف' }}</p><p class="mt-1 break-all text-sm text-[#c3c6d7]">{{ $consultation->customer?->email }}</p></div>
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">نوع الاستشارة</p><div class="flex items-center gap-2 mt-2"><span class="h-2 w-2 rounded-full bg-[#b4c5ff]"></span><p class="font-bold text-white">{{ $consultation->consultationType?->name ?? 'غير محدد' }}</p></div></div>
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">المهندس الحالي</p><p class="mt-2 font-bold {{ $consultation->engineer ? 'text-white' : 'text-[#ffb4ab]' }}">{{ $consultation->engineer?->name ?? 'غير معين' }}</p></div>
                                <div class="p-4 rounded-lg transfer-detail"><p class="text-[11px] text-[#c3c6d7]">المكتب الحالي</p><p class="mt-2 font-bold text-[#c3c6d7]">{{ $consultation->assignedOffice?->name ?? 'غير محولة إلى مكتب' }}</p></div>
                                @if($consultation->description)<div class="p-4 rounded-lg transfer-detail md:col-span-2"><p class="text-[11px] text-[#c3c6d7]">وصف الاستشارة</p><p class="mt-2 text-sm leading-7 text-white whitespace-pre-line">{{ $consultation->description }}</p></div>@endif
                            </div>
                        </section>

                        <section class="p-5 transfer-glass rounded-xl sm:p-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-white/10"><div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ffb1c7]/10 text-[#ffb1c7]"><svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10m0 0-3-3m3 3-3 3M17 17H7m0 0 3 3m-3-3 3-3"/></svg></div><h2 class="text-xl font-black text-white">اختر المكتب الهندسي</h2></div>
                            <form method="POST" action="{{ route('admin.consultation-office.assign',$consultation) }}" class="mt-6 space-y-6">@csrf @method('PATCH')
                                <div><label for="office_id" class="block mb-2 text-sm font-black text-white">المكتب <span class="text-[#ffb4ab]">*</span></label><div class="relative"><select id="office_id" name="office_id" required @disabled($offices->isEmpty()) class="h-12 px-4 py-3 pl-12 appearance-none transfer-input"><option value="">اختر المكتب...</option>@foreach($offices as $office)<option value="{{ $office->id }}" @selected((string)old('office_id',$consultation->assigned_office_id)===(string)$office->id)>{{ $office->name }} — {{ $office->city ?: 'مدينة غير محددة' }} — {{ $office->active_members_count ?? 0 }} عضو — {{ $office->consultations_count ?? 0 }} استشارة</option>@endforeach</select><svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m7 10 5 5 5-5"/></svg></div>@error('office_id')<p class="mt-2 text-sm text-[#ffb4ab]">{{ $message }}</p>@enderror @if($offices->isEmpty())<p class="mt-2 text-xs font-bold text-[#ffb4ab]">لا توجد مكاتب فعالة باشتراك ساري حاليًا.</p>@endif</div>
                                <div><label for="notes" class="block mb-2 text-sm font-black text-white">ملاحظات التحويل</label><textarea id="notes" name="notes" rows="5" maxlength="3000" placeholder="أضف أي ملاحظات لمدير المكتب..." class="transfer-input min-h-32 resize-y p-4 placeholder:text-[#8d90a0]/65">{{ old('notes') }}</textarea>@error('notes')<p class="mt-2 text-sm text-[#ffb4ab]">{{ $message }}</p>@enderror</div>
                                <div class="flex items-start gap-3 rounded-lg border-r-4 border-[#ffb1c7]/50 bg-[#222a3d]/80 p-4"><svg class="mt-0.5 h-6 w-6 shrink-0 text-[#ffb1c7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.8 2.7 18a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 4.8a2 2 0 0 0-3.4 0Z"/></svg><div><h3 class="font-black text-[#ffb1c7]">تنبيه</h3><p class="mt-1 text-sm leading-7 text-[#c3c6d7]">عند التحويل إلى مكتب جديد سيتم إزالة المهندس الحالي من الاستشارة، ثم يستطيع مدير المكتب تعيين مهندس من فريقه.</p></div></div>
                                <div class="flex flex-col-reverse gap-3 pt-5 border-t transfer-actions border-white/10 sm:flex-row sm:justify-end"><a href="{{ $consultationsRoute }}" class="inline-flex items-center justify-center rounded-lg border border-[#434655]/70 px-6 py-3 text-sm font-black text-white hover:bg-[#2d3449]/55">العودة إلى الاستشارات</a><button type="submit" @disabled($offices->isEmpty()) onclick="return confirm('هل تريد تحويل الاستشارة إلى المكتب المحدد؟')" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#2563eb] px-6 py-3 text-sm font-black text-white shadow-lg hover:brightness-110 disabled:cursor-not-allowed disabled:bg-[#222a3d] disabled:text-[#c3c6d7] disabled:opacity-55">تأكيد تحويل الاستشارة<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg></button></div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
