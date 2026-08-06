<x-app-layout>
    @php
        $currentUser = auth()->user();
        $consultation = $conversation->consultation;
        $messages = $conversation->messages?->sortBy('created_at') ?? collect();
        $otherUser = $otherParticipant;

        $roleLabels = [
            'admin' => 'مدير',
            'engineer' => 'مهندس',
            'customer' => 'عميل',
            'employee' => 'موظف',
        ];

        $statusLabels = [
            'waiting_payment' => 'بانتظار الدفع',
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
        ];

        $conversationTitle = $conversation->type === 'consultation'
            ? ($consultation?->title ?? 'محادثة استشارة')
            : ($otherUser?->name ?? 'محادثة مباشرة');

        $conversationDescription = $conversation->type === 'consultation'
            ? ($consultation?->description ?? 'لا يوجد وصف')
            : 'محادثة مباشرة داخل النظام';

        $conversationNumber = $conversation->type === 'consultation'
            ? ($consultation?->consultation_number ?? '—')
            : 'محادثة مباشرة';

        $conversationDate = $conversation->created_at?->format('Y-m-d');
        $conversationStatus = $conversation->type === 'consultation'
            ? ($statusLabels[$consultation?->status] ?? ($consultation?->status ?? 'نشطة'))
            : 'نشطة';

        $conversationPaymentText = $conversation->type === 'consultation'
            ? ($consultation?->payment_status === 'paid' ? 'تم تأكيد الدفع' : 'الدفع غير مؤكد')
            : 'لا تتطلب دفعًا';

        $conversationTypeName = $conversation->type === 'consultation'
            ? ($consultation?->consultationType?->name ?? 'غير محدد')
            : ($roleLabels[$otherUser?->role] ?? 'محادثة مباشرة');

        $canReviewEngineer =
            $conversation->type === 'consultation'
            && $consultation
            && (int) $currentUser->id === (int) $consultation->customer_id
            && $consultation->engineer_id
            && $consultation->status === 'completed'
            && $consultation->payment_status === 'paid';
    @endphp

    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

    <style>
        .cp-page {
            --cp-primary: #b4c5ff;
            --cp-secondary: #ffb1c7;
            --cp-tertiary: #d2bbff;
            --cp-surface: #0b1326;
            --cp-low: #131b2e;
            --cp-container: #171f33;
            --cp-high: #222a3d;
            --cp-highest: #2d3449;
            --cp-outline: #434655;
            --cp-text: #dae2fd;
            --cp-muted: #c3c6d7;
            min-height: 100vh;
            background: var(--cp-surface);
            color: var(--cp-text);
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .cp-glass {
            background: rgba(23, 31, 51, .64);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(141, 144, 160, .1);
        }

        .cp-neon {
            background: linear-gradient(135deg, #be0062 0%, #8343f4 100%);
        }

        .cp-scroll::-webkit-scrollbar { width: 6px; }
        .cp-scroll::-webkit-scrollbar-track { background: #0b1326; }
        .cp-scroll::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 10px; }

        .cp-mobile-nav {
            padding-bottom: max(.5rem, env(safe-area-inset-bottom));
        }

        .cp-mobile-drawer,
        .cp-info-panel {
            transition: transform .28s ease, opacity .28s ease;
        }

        .cp-panel-backdrop {
            transition: opacity .25s ease;
        }

        .cp-nav-label {
            font-size: 10px;
            line-height: 1;
        }

        @media (min-width: 1024px) {
            .cp-mobile-nav,
            .cp-mobile-menu-button {
                display: none !important;
            }
        }

@media (max-width: 1023px) {
            .cp-desktop-sidebar { display: none !important; }
            .cp-main { margin-right: 0 !important; }
            .cp-topbar { right: 0 !important; }
        }
    </style>

    <div class="cp-page" dir="rtl">
        {{-- القائمة الجانبية: مطابقة للتصميم الأصلي --}}
        <aside class="cp-desktop-sidebar fixed right-0 top-0 z-50 hidden h-full w-64 flex-col border-l border-white/5 bg-[#171f33] py-8 shadow-lg lg:flex">
            <div class="flex items-center gap-3 px-6 mb-10">
                <div class="flex items-center justify-center w-10 h-10 cp-neon rounded-xl">
                    <svg class="inline-block w-5 h-5 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.5 4.5A3.5 3.5 0 0 0 6 8v1a3 3 0 0 0-2 2.83V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5V4.5Z"/><path d="M14.5 4.5A3.5 3.5 0 0 1 18 8v1a3 3 0 0 1 2 2.83V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5V4.5Z"/><path d="M9.5 9H7.8M16.2 9h-1.7M9.5 15H7.8M16.2 15h-1.7"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-[#b4c5ff]">نظام الاستشارات</h1>
                    <p class="text-[10px] text-[#c3c6d7]">لوحة التحكم الرئيسية</p>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    <span>لوحة القيادة</span>
                </a>
                <a href="{{ route('consultations.mine') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1M8 9h8M8 13h8M8 17h5"/></svg>
                    <span>الطلبات</span>
                </a>
                <a href="#messagesContainer" class="flex items-center gap-4 rounded-xl border-r-4 border-[#b4c5ff] bg-[#b4c5ff]/5 px-4 py-3 text-[#b4c5ff] shadow-[0_0_15px_rgba(180,197,255,.15)]">
                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/><path d="M8 9h8M8 13h5"/></svg>
                    <span>المحادثات</span>
                </a>
<a href="{{ route('profile.edit') }}" class="flex items-center gap-4 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]">
                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/></svg>
                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="px-4 mt-auto">
                <a href="{{ route('consultations.create') }}" class="flex items-center justify-center w-full gap-2 py-3 text-xs font-bold text-white transition cp-neon rounded-xl active:scale-95">
                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    <span>طلب جديد</span>
                </a>
            </div>
        </aside>

        <main class="min-h-screen pb-20 cp-main lg:mr-64 lg:pb-0">
            {{-- الشريط العلوي --}}
            <header class="cp-topbar fixed left-0 right-0 top-0 z-40 flex h-16 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-6 shadow-sm backdrop-blur-md lg:right-64">
                <button type="button" id="openMobileMenu" class="cp-mobile-menu-button flex h-10 w-10 items-center justify-center rounded-xl bg-[#171f33] text-[#c3c6d7] lg:hidden" aria-label="فتح القائمة">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <div class="hidden items-center rounded-full border border-white/5 bg-[#2d3449]/50 px-4 py-2 md:flex">
                    <svg class="inline-block w-5 h-5 shrink-0 ml-2 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
                    <input class="w-64 border-none bg-transparent text-sm text-white placeholder:text-[#c3c6d7]/50 focus:ring-0" placeholder="بحث عن طلبات، عملاء..." type="text">
                </div>

                <div class="flex items-center gap-4">
                   <div class="relative flex items-center gap-2">

    {{-- الإشعارات --}}
    <button
        type="button"
        id="toggleChatNotifications"
        class="relative flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50 hover:text-white"
        title="الإشعارات"
    >
        <svg
            class="w-5 h-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.9"
            stroke-linecap="round"
        >
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
            <path d="M10 21h4"/>
        </svg>

        @if ($currentUser->unreadNotifications()->exists())
            <span
                class="absolute right-2 top-2 h-2 w-2 rounded-full bg-[#ffb1c7]"
            ></span>
        @endif
    </button>

    {{-- تفاصيل المحادثة والملفات --}}
    <button
        type="button"
        data-open-info
        class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50 hover:text-white"
        title="تفاصيل المحادثة والملفات"
    >
        <svg
            class="w-5 h-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
        </svg>
    </button>

    {{-- المساعدة --}}
    <button
        type="button"
        id="toggleChatHelp"
        class="flex h-10 w-10 items-center justify-center rounded-full text-[#c3c6d7] transition hover:bg-[#2d3449]/50 hover:text-white"
        title="مساعدة"
    >
        <svg
            class="w-5 h-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.9"
            stroke-linecap="round"
        >
            <circle cx="12" cy="12" r="9"/>
            <path d="M9.7 9a2.5 2.5 0 1 1 3.5 2.3c-.8.35-1.2.8-1.2 1.7"/>
            <path d="M12 17h.01"/>
        </svg>
    </button>

    {{-- قائمة الإشعارات --}}
    <div
        id="chatNotificationsMenu"
        class="absolute left-0 top-12 z-50 hidden w-80 overflow-hidden rounded-2xl border border-white/10 bg-[#171f33] shadow-2xl"
    >
        <div class="px-4 py-3 border-b border-white/10">
            <p class="font-bold text-white">الإشعارات</p>
        </div>

        <div class="p-3 overflow-y-auto max-h-80">
            @forelse ($currentUser->notifications()->latest()->take(8)->get() as $notification)

                <a
                    href="{{ $notification->data['url'] ?? '#' }}"
                    class="block px-3 py-3 transition rounded-xl hover:bg-white/5"
                >
                    <p class="text-sm font-bold text-white">
                        {{ $notification->data['title'] ?? 'إشعار جديد' }}
                    </p>

                    <p class="mt-1 text-xs leading-6 text-[#c3c6d7]">
                        {{ $notification->data['message'] ?? '' }}
                    </p>

                    <p class="mt-1 text-[10px] text-[#7f8ba3]">
                        {{ $notification->created_at?->diffForHumans() }}
                    </p>
                </a>

            @empty

                <p class="py-8 text-center text-sm text-[#c3c6d7]">
                    لا توجد إشعارات حاليًا
                </p>

            @endforelse
        </div>
    </div>

    {{-- نافذة المساعدة --}}
    <div
        id="chatHelpMenu"
        class="absolute left-0 top-12 z-50 hidden w-80 rounded-2xl border border-white/10 bg-[#171f33] p-4 shadow-2xl"
    >
        <div class="flex items-center justify-between">
            <p class="font-bold text-white">مساعدة المحادثة</p>

            <button
                type="button"
                id="closeChatHelp"
                class="flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-[#c3c6d7]"
            >
                ×
            </button>
        </div>

        <div class="mt-4 space-y-3 text-sm leading-7 text-[#c3c6d7]">
            <p>يمكنك إرسال رسالة نصية أو ملف أو تسجيل صوتي.</p>
            <p>زر الإعدادات يفتح تفاصيل المحادثة والملفات المشتركة.</p>
            <p>جميع المرفقات محمية ولا تظهر إلا لأطراف الاستشارة.</p>
        </div>
    </div>

</div>

                    <div class="w-px h-8 mx-2 bg-white/10"></div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-bold text-[#dae2fd]">{{ $currentUser->name }}</p>
                            <p class="text-[10px] text-[#c3c6d7]">{{ $currentUser->role }}</p>
                        </div>
                        <div class="h-10 w-10 overflow-hidden rounded-full border-2 border-[#b4c5ff]/20">
                            @if ($currentUser->profile_photo)
                                <img class="object-cover w-full h-full" src="{{ asset('storage/' . $currentUser->profile_photo) }}" alt="{{ $currentUser->name }}">
                            @else
                                <div class="flex items-center justify-center w-full h-full font-bold text-white cp-neon">{{ mb_substr($currentUser->name, 0, 1) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="w-full px-4 pt-24 pb-10 mx-auto space-y-8 max-w-7xl sm:px-6">
                {{-- عنوان الصفحة --}}
                <section class="flex flex-col gap-1">
                    <h2 class="text-3xl font-bold text-[#dae2fd]">أهلًا بك، {{ $currentUser->name }}</h2>
                    <div class="flex items-center gap-2 text-[#c3c6d7]">
                        <svg class="inline-block w-4 h-4 shrink-0 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        <p>أنت الآن داخل المحادثة رقم {{ $conversationNumber }}.</p>
                    </div>
                </section>

                {{-- أعلى الصفحة --}}
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="cp-neon relative flex min-h-[250px] flex-col justify-between overflow-hidden rounded-3xl p-8 shadow-2xl lg:col-span-2">
                        <div class="absolute rounded-full -right-20 -top-20 h-80 w-80 bg-white/10 blur-3xl"></div>
                        <div class="relative z-10 flex items-start justify-between">
                            <div>
                                <span class="inline-block px-4 py-1 mb-4 text-xs font-bold text-white rounded-full bg-white/20 backdrop-blur-md">استشارة نشطة</span>
                                <h3 class="text-3xl font-bold text-white">{{ $conversationTitle }}</h3>
                                <p class="max-w-md mt-2 text-white/80">{{ $conversationDescription }}</p>
                            </div>
                            <svg class="inline-block w-16 h-16 text-white shrink-0 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5c2.5-2.5 5.5-2 5.5-2S20 6 17.5 8.5L12 14l-4-4 6-5Z"/><path d="M9 11 5 12l-2 3 6-1M13 15l-1 4 3-2 1-4M7 17l-2 2M8.5 18.5 7 20"/><circle cx="16" cy="6.5" r="1.3"/></svg>
                        </div>
                        <div class="relative z-10 flex items-center gap-4 mt-8">
                            <a href="#messagesContainer" class="rounded-xl bg-white px-6 py-2.5 text-xs font-bold text-[#2563eb] transition hover:shadow-lg">فتح المحادثة</a>
                            <button data-open-info class="rounded-xl border border-white/30 px-6 py-2.5 text-xs font-bold text-white transition hover:bg-white/10">عرض التفاصيل</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center justify-between p-6 cp-glass rounded-3xl">
                            <div>
                                <p class="text-xs font-bold uppercase text-[#c3c6d7]">الرسائل</p>
                                <h4 class="mt-1 text-2xl font-semibold text-[#b4c5ff]">{{ $messages->count() }}</h4>
                                <p class="mt-2 text-xs text-[#c3c6d7]">إجمالي رسائل الاستشارة</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#b4c5ff]/10 text-[#b4c5ff]">
                                <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/><path d="M8 9h8M8 13h5"/></svg>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-6 cp-glass rounded-3xl">
                            <div>
                                <p class="text-xs font-bold uppercase text-[#c3c6d7]">حالة الاستشارة</p>
                                <h4 class="mt-1 text-lg font-semibold text-[#ffb1c7]">{{ $conversationStatus }}</h4>
                                <p class="mt-2 text-xs text-[#c3c6d7]">{{ $conversationPaymentText }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ffb1c7]/10 text-[#ffb1c7]">
                                <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/><path d="m4 8 5-4 6 5 5-4"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- المحادثة الرئيسية --}}
                <section class="overflow-hidden cp-glass rounded-3xl">
                    <div class="flex items-center justify-between p-6 border-b border-white/5">
                        <div class="flex items-center gap-4">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="relative">
                            @else
                                <div class="relative">
                            @endif
                                <div class="w-16 h-16 overflow-hidden shadow-lg rounded-2xl">
                                    @if ($otherUser?->profile_photo)
                                        <img class="object-cover w-full h-full" src="{{ asset('storage/' . $otherUser->profile_photo) }}" alt="{{ $otherUser?->name }}">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full text-xl font-bold text-white cp-neon">{{ mb_substr($otherUser?->name ?? 'م', 0, 1) }}</div>
                                    @endif
                                </div>
                                <span class="presence-dot absolute -bottom-1 -left-1 h-4 w-4 rounded-full border-2 border-[#171f33] bg-slate-500"></span>
                            @if ($otherUser && $otherUser->role === 'engineer')
                                </a>
                            @else
                                </div>
                            @endif

                            <div>
                                @if ($otherUser && $otherUser->role === 'engineer')
                                    <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="font-bold text-[#dae2fd] transition hover:text-[#b4c5ff]">{{ $otherUser->name }}</a>
                                @else
                                    <h5 class="font-bold text-[#dae2fd]">{{ $otherUser?->name ?? 'المستخدم' }}</h5>
                                @endif
                                <p class="text-sm text-[#c3c6d7]">{{ $otherUser?->role === 'engineer'
    ? 'المهندس المسؤول'
    : ($roleLabels[$otherUser?->role] ?? 'المستخدم الآخر') }}</p>
                                <p class="mt-1 text-xs text-[#c3c6d7]"><span class="presence-status">غير متصل</span><span id="headerTypingStatus" class="hidden text-[#b4c5ff]"> · يكتب الآن...</span></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if ($otherUser && $otherUser->role === 'engineer')
                                <a href="{{ route('engineers.show', ['user' => $otherUser->id]) }}" class="rounded-xl bg-[#b4c5ff]/10 p-2 text-[#b4c5ff] transition hover:bg-[#b4c5ff]/20">
                                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div id="messagesContainer" class="cp-scroll h-[560px] overflow-y-auto p-4 sm:p-6">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="flex-1 h-px bg-white/10"></div>
                            <span class="rounded-full border border-white/10 bg-[#060e20]/60 px-4 py-2 text-xs font-bold text-[#c3c6d7]">{{ $conversationDate }}</span>
                            <div class="flex-1 h-px bg-white/10"></div>
                        </div>

                        <div id="messagesList" class="space-y-4">
                            @forelse ($messages as $message)
                                @php
                                    $isMine = (int) $message->sender_id === (int) auth()->id();
                                    $sender = $message->sender;
                                    $extension = $message->attachment_name ? strtolower(pathinfo($message->attachment_name, PATHINFO_EXTENSION)) : null;
                                    $isImage = $message->message_type === 'image';
                                @endphp

                                <div data-message-id="{{ $message->id }}" class="flex items-end gap-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-none overflow-hidden border rounded-full h-9 w-9 border-white/10">
                                        @if ($sender?->profile_photo)
                                            <img class="object-cover w-full h-full" src="{{ asset('storage/' . $sender->profile_photo) }}" alt="{{ $sender->name }}">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-xs font-bold text-white cp-neon">{{ mb_substr($sender?->name ?? 'م', 0, 1) }}</div>
                                        @endif
                                    </div>

                                    <div class="max-w-[82%] sm:max-w-[62%]">
                                        <div class="rounded-2xl px-4 py-3 shadow-lg {{ $isMine ? 'rounded-br-md bg-gradient-to-br from-[#2563eb] to-[#8343f4] text-white' : 'rounded-bl-md border border-white/5 bg-[#222a3d] text-[#dae2fd]' }}">
                                            <div class="flex items-center justify-between gap-4 mb-2">
                                                <p class="text-xs font-bold">{{ $isMine ? 'أنت' : ($sender?->name ?? 'المستخدم') }}</p>
                                                <span class="text-[10px] opacity-60">{{ $message->created_at?->format('H:i') }}</span>
                                            </div>

                                            @if ($message->message)
                                                <p class="text-sm leading-7 whitespace-pre-line">{{ $message->message }}</p>
                                            @endif

                                            @if ($message->attachment_path)
                                                @if ($message->message_type === 'voice')
                                                    <div class="p-3 mt-3 border rounded-2xl border-white/10 bg-black/15">
                                                        <audio controls preload="metadata" class="w-full min-w-[220px]">
                                                            <source
                                                                src="{{ route('conversations.messages.attachment', [$conversation, $message]) }}"
                                                                type="{{ $message->attachment_mime }}"
                                                            >
                                                        </audio>

                                                        @if ($message->audio_duration)
                                                            <p class="mt-2 text-[10px] opacity-60">
                                                                المدة: {{ gmdate('i:s', $message->audio_duration) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @elseif ($isImage)
                                                    <a href="{{ route('conversations.messages.attachment', [$conversation, $message]) }}" target="_blank" rel="noopener noreferrer" class="block mt-3 overflow-hidden border rounded-2xl border-white/10">
                                                        <img src="{{ route('conversations.messages.attachment', [$conversation, $message]) }}" alt="مرفق" class="object-cover w-full max-h-64">
                                                    </a>
                                                @else
                                                    <a href="{{ route('conversations.messages.download', [$conversation, $message]) }}" class="flex items-center justify-between gap-4 p-3 mt-3 border rounded-2xl border-white/10 bg-black/15">
                                                        <div class="flex items-center min-w-0 gap-3">
                                                            <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl bg-[#8343f4]/20 text-xs font-bold text-[#d2bbff]">{{ strtoupper($extension ?: 'FILE') }}</div>
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-bold truncate">{{ $message->attachment_name ?? 'ملف مرفق' }}</p>
                                                                <p class="mt-1 text-xs opacity-60">اضغط لتحميل الملف</p>
                                                            </div>
                                                        </div>
                                                        <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"/></svg>
                                                    </a>
                                                @endif
                                            @endif

                                            @if ($isMine)
                                                <div class="mt-2 text-left text-xs text-[#b4c5ff]">✓✓</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex h-[420px] flex-col items-center justify-center text-center">
                                    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-[#b4c5ff]/10 text-4xl">💬</div>
                                    <h3 class="text-xl font-bold text-white">لا توجد رسائل حتى الآن</h3>
                                    <p class="mt-2 text-sm text-[#c3c6d7]">ابدأ المحادثة بإرسال أول رسالة</p>
                                </div>
                            @endforelse
                        </div>

                        <div id="typingIndicator" class="mt-5 hidden text-sm font-bold text-[#b4c5ff]">يكتب الآن...</div>
                    </div>

                    <div class="border-t border-white/5 bg-[#131b2e] p-4">
                        <form id="chatForm" method="POST" action="{{ route('conversations.messages.store', $conversation) }}" enctype="multipart/form-data" class="space-y-3" x-data="{ fileName: '', selectFile(event) { this.fileName = event.target.files[0] ? event.target.files[0].name : ''; } }">
                            @csrf
                            <div class="flex items-end gap-2 rounded-3xl border border-white/10 bg-[#222a3d] p-2">
                                <textarea id="message" name="message" rows="1" placeholder="اكتب رسالتك هنا..." class="min-h-[48px] max-h-32 flex-1 resize-none border-0 bg-transparent px-3 py-3 text-sm text-white placeholder:text-[#c3c6d7]/50 focus:ring-0">{{ old('message') }}</textarea>
                                <label for="attachment" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-xl bg-white/5 text-[#c3c6d7] transition hover:bg-white/10" title="إرفاق ملف">
                                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="m20 11.5-8.5 8.5a6 6 0 0 1-8.5-8.5l9-9a4 4 0 0 1 5.7 5.7l-9 9a2 2 0 0 1-2.8-2.8l8.3-8.3"/></svg>
                                </label>
                                <input id="attachment" type="file" name="attachment" class="hidden" accept=".pdf,.dwg,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx,.zip" @change="selectFile($event)">

                                <input id="voiceMessage" type="file" name="voice_message" class="hidden">
                                <input id="audioDuration" type="hidden" name="audio_duration">

                                <button id="recordVoiceButton" type="button" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/5 text-[#c3c6d7] transition hover:bg-white/10" title="تسجيل صوتي">
                                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                                </button>

                                <button id="sendButton" type="submit" class="flex items-center justify-center flex-none text-white transition rounded-full shadow-lg cp-neon h-11 w-11 hover:scale-105" aria-label="إرسال">
                                    <svg class="inline-block w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></svg>
                                </button>
                            </div>
                            <span x-show="fileName" x-text="fileName" class="block max-w-full truncate text-xs text-[#c3c6d7]"></span>

                            <div id="voicePreview" class="items-center hidden gap-3 p-3 border rounded-2xl border-[#ffb1c7]/20 bg-[#be0062]/10">
                                <span id="recordingIndicator" class="w-3 h-3 rounded-full bg-[#ffb1c7] animate-pulse"></span>
                                <span id="recordingTimer" class="text-sm font-bold text-[#ffb1c7]">00:00</span>
                                <audio id="recordedAudio" controls class="flex-1 hidden"></audio>
                                <button id="deleteVoiceButton" type="button" class="px-3 py-2 text-xs font-bold rounded-xl bg-white/5 text-[#ffb1c7]">حذف</button>
                            </div>
                        </form>
                    </div>
                </section>

                {{-- تم نقل الملفات والتفاصيل إلى لوحة المعلومات الجانبية --}}
            </div>
        </main>


        {{-- خلفية اللوحات --}}
        <div id="cpPanelBackdrop" class="cp-panel-backdrop fixed inset-0 z-[70] hidden bg-[#060e20]/75 opacity-0 backdrop-blur-sm"></div>

        {{-- قائمة الجوال --}}
        <aside id="mobileMenuDrawer" class="cp-mobile-drawer fixed inset-y-0 right-0 z-[80] w-[86%] max-w-xs translate-x-full overflow-y-auto border-l border-white/10 bg-[#131b2e] p-5 shadow-2xl lg:hidden">
            <div class="flex items-center justify-between mb-7">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 cp-neon rounded-xl">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M9.5 4.5A3.5 3.5 0 0 0 6 8v1a3 3 0 0 0-2 2.83V13a3 3 0 0 0 2 2.83V17a3.5 3.5 0 0 0 3.5 3.5V4.5Z"/><path d="M14.5 4.5A3.5 3.5 0 0 1 18 8v1a3 3 0 0 1 2 2.83V13a3 3 0 0 1-2 2.83V17a3.5 3.5 0 0 1-3.5 3.5V4.5Z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-[#dae2fd]">نظام الاستشارات</p>
                        <p class="text-[10px] text-[#c3c6d7]">المحادثة</p>
                    </div>
                </div>
                <button type="button" data-close-panels class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-xl text-[#c3c6d7]">×</button>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-[#2d3449]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    <span>لوحة التحكم</span>
                </a>
                <a href="{{ route('consultations.mine') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-[#2d3449]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                    <span>استشاراتي</span>
                </a>
                <a href="#messagesContainer" data-close-panels class="flex items-center gap-3 rounded-xl bg-[#b4c5ff]/10 px-4 py-3 text-[#b4c5ff]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/></svg>
                    <span>المحادثة</span>
                </a>
                <button type="button" data-open-info class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-[#2d3449]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/></svg>
                    <span>ملفات وتفاصيل المحادثة</span>
                </button>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] hover:bg-[#2d3449]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <span>الملف الشخصي</span>
                </a>
            </nav>
        </aside>

        {{-- لوحة معلومات المحادثة --}}
        <aside id="consultationInfoPanel" class="cp-info-panel fixed inset-y-0 left-0 z-[80] w-full max-w-lg -translate-x-full overflow-y-auto border-r border-white/10 bg-[#0b1326] shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-white/10 bg-[#0b1326]/95 px-5 py-4 backdrop-blur-xl">
                <div>
                    <h3 class="text-lg font-bold text-[#dae2fd]">معلومات المحادثة</h3>
                    <p class="mt-1 text-xs text-[#c3c6d7]">{{ $conversationNumber }}</p>
                </div>
                <button type="button" data-close-panels class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-xl text-[#c3c6d7]">×</button>
            </div>

            <div class="p-5 space-y-5">
                <section class="p-5 cp-glass rounded-3xl">
                    <div class="flex items-center gap-3 mb-5">
                        <svg class="h-5 w-5 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 19V5M4 19h16"/><path d="m7 15 4-4 3 3 5-7"/></svg>
                        <h4 class="font-bold text-[#dae2fd]">تفاصيل المحادثة</h4>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-center justify-between pb-3 border-b border-white/5"><span class="text-[#c3c6d7]">رقم الطلب</span><span class="max-w-[58%] break-all text-left font-bold text-[#dae2fd]">{{ $conversationNumber }}</span></div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/5"><span class="text-[#c3c6d7]">العنوان</span><span class="max-w-[58%] text-left font-bold text-[#dae2fd]">{{ $conversationTitle }}</span></div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/5"><span class="text-[#c3c6d7]">نوع الخدمة</span><span class="rounded-full bg-[#8343f4]/20 px-3 py-1 text-xs font-bold text-[#d2bbff]">{{ $conversationTypeName }}</span></div>
                        <div class="flex items-center justify-between pb-3 border-b border-white/5"><span class="text-[#c3c6d7]">الحالة</span><span class="font-bold text-[#dae2fd]">{{ $conversationStatus }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-[#c3c6d7]">تاريخ الطلب</span><span class="font-bold text-[#dae2fd]">{{ $conversationDate }}</span></div>
                    </div>
                    <div class="mt-5 rounded-2xl bg-[#2d3449]/25 p-4">
                        <p class="mb-2 text-xs font-bold text-[#b4c5ff]">وصف المحادثة</p>
                        <p class="text-xs leading-7 text-[#c3c6d7]">{{ $conversationDescription }}</p>
                    </div>
                </section>

                <section class="p-5 cp-glass rounded-3xl">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-[#d2bbff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 6.5A2.5 2.5 0 0 1 5.5 4H10l2 2h6.5A2.5 2.5 0 0 1 21 8.5v9A2.5 2.5 0 0 1 18.5 20h-13A2.5 2.5 0 0 1 3 17.5v-11Z"/></svg>
                            <h4 class="font-bold text-[#dae2fd]">الملفات المشتركة</h4>
                        </div>
                        <span class="rounded-full bg-white/5 px-3 py-1 text-xs text-[#c3c6d7]">{{ $messages->whereNotNull('attachment_path')->count() }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($messages->whereNotNull('attachment_path') as $fileMessage)
                            @php
                                $fileExtension = strtolower(pathinfo($fileMessage->attachment_name ?? '', PATHINFO_EXTENSION));
                                $isVoiceFile = ($fileMessage->message_type ?? null) === 'voice';
                            @endphp
                            <a href="{{ ($fileMessage->message_type === 'voice' || $fileMessage->message_type === 'image')
                                    ? route('conversations.messages.attachment', [$conversation, $fileMessage])
                                    : route('conversations.messages.download', [$conversation, $fileMessage]) }}" target="_blank" rel="noopener noreferrer" class="group flex items-center justify-between rounded-2xl border border-white/5 bg-white/[.025] p-3 transition hover:border-[#b4c5ff]/30 hover:bg-[#2d3449]/35">
                                <div class="flex items-center min-w-0 gap-3">
                                    <div class="flex h-11 w-11 flex-none items-center justify-center rounded-xl {{ $isVoiceFile ? 'bg-[#be0062]/20 text-[#ffb1c7]' : 'bg-[#8343f4]/20 text-[#d2bbff]' }}">
                                        @if ($isVoiceFile)
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/></svg>
                                        @else
                                            <span class="text-[10px] font-bold">{{ strtoupper($fileExtension ?: 'FILE') }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-[#dae2fd]">{{ $isVoiceFile ? 'رسالة صوتية' : $fileMessage->attachment_name ?? 'ملف مرفق' }}</p>
                                        <p class="mt-1 text-[10px] text-[#c3c6d7]">{{ $fileMessage->created_at?->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 flex-none text-[#c3c6d7] opacity-60 transition group-hover:text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"/></svg>
                            </a>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 px-4 py-8 text-center text-sm text-[#c3c6d7]">لا توجد ملفات مشتركة حتى الآن</div>
                        @endforelse
                    </div>
                </section>

                <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#b4c5ff] px-5 py-3 font-bold text-[#00174b] transition hover:brightness-110">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    العودة إلى لوحة التحكم
                </a>
            </div>
        </aside>

        {{-- شريط تنقل ثابت للجوال --}}
        <nav class="cp-mobile-nav fixed inset-x-0 bottom-0 z-[60] grid grid-cols-4 border-t border-white/10 bg-[#171f33]/95 px-2 pt-2 shadow-[0_-12px_35px_rgba(0,0,0,.35)] backdrop-blur-xl lg:hidden">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 rounded-xl px-2 py-2 text-[#c3c6d7]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span class="cp-nav-label">الرئيسية</span>
            </a>
            <a href="{{ route('consultations.mine') }}" class="flex flex-col items-center gap-1 rounded-xl px-2 py-2 text-[#c3c6d7]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                <span class="cp-nav-label">استشاراتي</span>
            </a>
            <a href="#messagesContainer" class="flex flex-col items-center gap-1 rounded-xl bg-[#b4c5ff]/10 px-2 py-2 text-[#b4c5ff]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"/></svg>
                <span class="cp-nav-label">المحادثة</span>
            </a>
            <button type="button" data-open-info class="flex flex-col items-center gap-1 rounded-xl px-2 py-2 text-[#c3c6d7]">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/></svg>
                <span class="cp-nav-label">التفاصيل</span>
            </button>
        </nav>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const notificationsButton =
    document.getElementById('toggleChatNotifications');

const notificationsMenu =
    document.getElementById('chatNotificationsMenu');

const helpButton =
    document.getElementById('toggleChatHelp');

const helpMenu =
    document.getElementById('chatHelpMenu');

const closeHelpButton =
    document.getElementById('closeChatHelp');

notificationsButton?.addEventListener('click', (event) => {
    event.stopPropagation();

    notificationsMenu?.classList.toggle('hidden');
    helpMenu?.classList.add('hidden');
});

helpButton?.addEventListener('click', (event) => {
    event.stopPropagation();

    helpMenu?.classList.toggle('hidden');
    notificationsMenu?.classList.add('hidden');
});

closeHelpButton?.addEventListener('click', () => {
    helpMenu?.classList.add('hidden');
});

notificationsMenu?.addEventListener('click', (event) => {
    event.stopPropagation();
});

helpMenu?.addEventListener('click', (event) => {
    event.stopPropagation();
});

document.addEventListener('click', () => {
    notificationsMenu?.classList.add('hidden');
    helpMenu?.classList.add('hidden');
});
            const conversationId = @json($conversation->id);
            const currentUserId = @json(auth()->id());
            const form = document.getElementById('chatForm');
            const textarea = document.getElementById('message');
            const attachment = document.getElementById('attachment');
            const recordVoiceButton = document.getElementById('recordVoiceButton');
            const voiceMessageInput = document.getElementById('voiceMessage');
            const audioDurationInput = document.getElementById('audioDuration');
            const voicePreview = document.getElementById('voicePreview');
            const recordedAudio = document.getElementById('recordedAudio');
            const recordingTimer = document.getElementById('recordingTimer');
            const recordingIndicator = document.getElementById('recordingIndicator');
            const deleteVoiceButton = document.getElementById('deleteVoiceButton');
            const sendButton = document.getElementById('sendButton');
            const messagesContainer =
                document.getElementById('messagesContainer');
            const messagesList =
                document.getElementById('messagesList');
            const typingIndicator =
                document.getElementById('typingIndicator');
            const presenceDots =
                document.querySelectorAll('.presence-dot');
            const presenceStatuses =
                document.querySelectorAll('.presence-status');
            const headerTypingStatus =
                document.getElementById('headerTypingStatus');
            const panelBackdrop =
                document.getElementById('cpPanelBackdrop');
            const mobileMenuDrawer =
                document.getElementById('mobileMenuDrawer');
            const consultationInfoPanel =
                document.getElementById('consultationInfoPanel');
            const openMobileMenu =
                document.getElementById('openMobileMenu');

            let channel = null;
            let typingTimer = null;
            let mediaRecorder = null;
            let audioChunks = [];
            let recordingStartedAt = null;
            let recordingInterval = null;
            let recordedAudioUrl = null;

            const showBackdrop = () => {
                panelBackdrop?.classList.remove('hidden');

                requestAnimationFrame(() => {
                    panelBackdrop?.classList.remove('opacity-0');
                });

                document.body.classList.add('overflow-hidden');
            };

            const closePanels = () => {
                mobileMenuDrawer?.classList.add('translate-x-full');
                consultationInfoPanel?.classList.add('-translate-x-full');
                panelBackdrop?.classList.add('opacity-0');
                document.body.classList.remove('overflow-hidden');

                window.setTimeout(() => {
                    panelBackdrop?.classList.add('hidden');
                }, 250);
            };

            const openMenuPanel = () => {
                closePanels();
                showBackdrop();
                mobileMenuDrawer?.classList.remove('translate-x-full');
            };

            const openInfoPanel = () => {
                closePanels();
                showBackdrop();
                consultationInfoPanel?.classList.remove('-translate-x-full');
            };

            openMobileMenu?.addEventListener('click', openMenuPanel);

            document
                .querySelectorAll('[data-open-info]')
                .forEach((button) => {
                    button.addEventListener('click', openInfoPanel);
                });

            document
                .querySelectorAll('[data-close-panels]')
                .forEach((button) => {
                    button.addEventListener('click', closePanels);
                });

            panelBackdrop?.addEventListener('click', closePanels);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closePanels();
                }
            });


            const formatRecordingTime = (seconds) => {
                const minutes = Math.floor(seconds / 60);
                const remaining = seconds % 60;

                return `${String(minutes).padStart(2, '0')}:${String(remaining).padStart(2, '0')}`;
            };

            const resetVoiceRecording = () => {
                clearInterval(recordingInterval);

                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }

                mediaRecorder = null;
                audioChunks = [];
                recordingStartedAt = null;

                if (voiceMessageInput) {
                    voiceMessageInput.value = '';
                }

                if (audioDurationInput) {
                    audioDurationInput.value = '';
                }

                if (recordedAudioUrl) {
                    URL.revokeObjectURL(recordedAudioUrl);
                    recordedAudioUrl = null;
                }

                if (recordedAudio) {
                    recordedAudio.pause();
                    recordedAudio.removeAttribute('src');
                    recordedAudio.classList.add('hidden');
                }

                if (recordingTimer) {
                    recordingTimer.textContent = '00:00';
                }

                voicePreview?.classList.add('hidden');
                voicePreview?.classList.remove('flex');
                recordingIndicator?.classList.remove('hidden');
                recordVoiceButton?.classList.remove('bg-[#be0062]/20', 'text-[#ffb1c7]');
            };

            recordVoiceButton?.addEventListener('click', async () => {
                if (!navigator.mediaDevices?.getUserMedia || typeof MediaRecorder === 'undefined') {
                    alert('التسجيل الصوتي غير مدعوم في هذا المتصفح.');
                    return;
                }

                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                    return;
                }

                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    audioChunks = [];

                    mediaRecorder = new MediaRecorder(stream);

                    mediaRecorder.addEventListener('dataavailable', (event) => {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    });

                    mediaRecorder.addEventListener('stop', () => {
                        clearInterval(recordingInterval);
                        stream.getTracks().forEach((track) => track.stop());

                        const mimeType = mediaRecorder.mimeType || 'audio/webm';
                        const blob = new Blob(audioChunks, { type: mimeType });
                        const extension = mimeType.includes('ogg') ? 'ogg' : 'webm';
                        const file = new File([blob], `voice-${Date.now()}.${extension}`, {
                            type: mimeType,
                        });

                        const transfer = new DataTransfer();
                        transfer.items.add(file);

                        if (voiceMessageInput) {
                            voiceMessageInput.files = transfer.files;
                        }

                        const duration = Math.max(
                            1,
                            Math.round((Date.now() - recordingStartedAt) / 1000)
                        );

                        if (audioDurationInput) {
                            audioDurationInput.value = duration;
                        }

                        recordedAudioUrl = URL.createObjectURL(blob);

                        if (recordedAudio) {
                            recordedAudio.src = recordedAudioUrl;
                            recordedAudio.classList.remove('hidden');
                        }

                        recordingIndicator?.classList.add('hidden');
                        recordVoiceButton?.classList.remove('bg-[#be0062]/20', 'text-[#ffb1c7]');
                    });

                    recordingStartedAt = Date.now();
                    mediaRecorder.start();

                    voicePreview?.classList.remove('hidden');
                    voicePreview?.classList.add('flex');
                    recordedAudio?.classList.add('hidden');
                    recordingIndicator?.classList.remove('hidden');
                    recordVoiceButton.classList.add('bg-[#be0062]/20', 'text-[#ffb1c7]');

                    recordingInterval = setInterval(() => {
                        const seconds = Math.floor((Date.now() - recordingStartedAt) / 1000);

                        if (recordingTimer) {
                            recordingTimer.textContent = formatRecordingTime(seconds);
                        }
                    }, 1000);
                } catch (error) {
                    alert('تعذر الوصول إلى الميكروفون. تأكد من السماح للموقع باستخدامه.');
                }
            });

            deleteVoiceButton?.addEventListener('click', resetVoiceRecording);

const scrollToBottom = () => {
                if (messagesContainer) {
                    messagesContainer.scrollTop =
                        messagesContainer.scrollHeight;
                }
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const setPresence = (online) => {
                presenceDots.forEach((dot) => {
                    dot.classList.toggle('bg-green-400', online);
                    dot.classList.toggle('bg-[#3A4A66]', !online);
                });

                presenceStatuses.forEach((status) => {
                    status.textContent = online
                        ? 'نشط الآن'
                        : 'غير متصل';

                    status.classList.toggle(
                        'text-green-400',
                        online
                    );
                });
            };

            const messageExists = (id) => {
                return document.querySelector(
                    `[data-message-id="${id}"]`
                ) !== null;
            };

            const appendMessage = (message, mine) => {
                if (!messagesList || messageExists(message.id)) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.dataset.messageId = message.id;
                wrapper.className =
                    `flex items-end gap-3 ${
                        mine
                            ? 'flex-row-reverse justify-start'
                            : 'justify-start'
                    }`;

                const initial = escapeHtml(
                    (message.sender_name || 'م').charAt(0)
                );

                let avatar = `
                    <div class="flex-none">
                        ${
                            message.sender_profile_photo_url
                                ? `<img
                                    src="${escapeHtml(
                                        message.sender_profile_photo_url
                                    )}"
                                    alt="${escapeHtml(
                                        message.sender_name
                                    )}"
                                    class="object-cover w-8 h-8 border rounded-full border-[#25344C]"
                                >`
                                : `<div
                                    class="flex items-center justify-center w-8 h-8 text-xs font-black text-white border rounded-full border-[#25344C] ${
                                        mine
                                            ? 'bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6]'
                                            : 'bg-gradient-to-br from-[#8B7CF6] to-[#5B4BB8]'
                                    }"
                                >${initial}</div>`
                        }
                    </div>
                `;

                let attachmentHtml = '';

                if (message.attachment_url) {
                    if (message.message_type === 'voice') {
                        attachmentHtml = `
                            <div class="p-3 mt-3 border rounded-2xl border-white/10 bg-black/15">
                                <audio controls preload="metadata" class="w-full min-w-[220px]" src="${escapeHtml(message.attachment_url)}"></audio>
                            </div>
                        `;
                    } else if (message.message_type === 'image' || message.attachment_is_image) {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block mt-3 overflow-hidden border rounded-2xl border-[#25344C] bg-black/10"
                            >
                                <img
                                    src="${escapeHtml(
                                        message.attachment_url
                                    )}"
                                    alt="مرفق"
                                    class="object-cover w-auto max-w-[220px] sm:max-w-[280px] max-h-56"
                                >
                            </a>
                        `;
                    } else {
                        attachmentHtml = `
                            <a
                                href="${escapeHtml(
                                    message.attachment_url
                                )}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-between gap-4 p-3 mt-4 transition border rounded-2xl border-[#25344C] bg-black/15 hover:bg-black/25"
                            >
                                <div class="flex items-center min-w-0 gap-3">
                                    <div
                                        class="flex items-center justify-center flex-none text-xs font-black w-11 h-11 rounded-xl bg-[#8B7CF6]/20 text-[#D9D2FF]"
                                    >
                                        ${escapeHtml(
                                            (
                                                message.attachment_extension
                                                || 'FILE'
                                            ).toUpperCase()
                                        )}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-bold truncate">
                                            ${escapeHtml(
                                                message.attachment_name
                                            )}
                                        </p>
                                        <p class="mt-1 text-xs opacity-60">
                                            اضغط لفتح الملف
                                        </p>
                                    </div>
                                </div>

                                <span
                                    class="flex items-center justify-center flex-none w-10 h-10 border rounded-full border-[#25344C]"
                                >↓</span>
                            </a>
                        `;
                    }
                }

                wrapper.innerHTML = `
                    ${avatar}

                    <div class="w-auto max-w-[84%] sm:max-w-[58%]">
                        <div
                            class="px-4 py-2.5 shadow-lg rounded-[20px] ${
                                mine
                                    ? 'rounded-br-md bg-gradient-to-br from-[#FF6B5B] to-[#8B7CF6] text-white ring-1 ring-white/10'
                                    : 'rounded-bl-md border border-[#25344C] bg-[#16233A]/85 text-[#F4F1FA] backdrop-blur-xl'
                            }"
                        >
                            <div
                                class="flex items-center justify-between gap-4 mb-1.5"
                            >
                                <p class="text-sm font-black">
                                    ${
                                        mine
                                            ? 'أنت'
                                            : escapeHtml(
                                                message.sender_name
                                            )
                                    }
                                </p>

                                <span
                                    class="text-[11px] ${
                                        mine
                                            ? 'text-white/70'
                                            : 'text-[#6B7A93]'
                                    }"
                                >
                                    ${escapeHtml(message.time)}
                                </span>
                            </div>

                            ${
                                (message.body || message.message)
                                    ? `<p
                                        class="text-sm leading-6 whitespace-pre-line sm:text-[15px]"
                                    >${escapeHtml(message.body || message.message)}</p>`
                                    : ''
                            }

                            ${attachmentHtml}

                            ${
                                mine
                                    ? `<div
                                        class="mt-2 text-xs text-left text-white/70"
                                    >✓</div>`
                                    : ''
                            }
                        </div>
                    </div>
                `;

                messagesList.appendChild(wrapper);
                scrollToBottom();
            };

            scrollToBottom();

            if (window.Echo) {
                channel = window.Echo.join(
                    `conversation.${conversationId}`
                )
                    .here((users) => {
                        setPresence(
                            users.some(
                                (user) =>
                                    Number(user.id)
                                    !== Number(currentUserId)
                            )
                        );
                    })
                    .joining((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(true);
                        }
                    })
                    .leaving((user) => {
                        if (
                            Number(user.id)
                            !== Number(currentUserId)
                        ) {
                            setPresence(false);
                        }
                    })
                    .listen(
                        '.conversation.message.sent',
                        (event) => {
                            appendMessage(
                                event.message,
                                Number(event.message.sender_id)
                                    === Number(currentUserId)
                            );
                        }
                    )
                    .listenForWhisper(
                        'typing',
                        (event) => {
                            if (
                                Number(event.user_id)
                                === Number(currentUserId)
                            ) {
                                return;
                            }

                            typingIndicator?.classList.remove(
                                'hidden'
                            );
                            headerTypingStatus?.classList.remove(
                                'hidden'
                            );

                            clearTimeout(typingTimer);

                            typingTimer = setTimeout(() => {
                                typingIndicator?.classList.add(
                                    'hidden'
                                );
                                headerTypingStatus?.classList.add(
                                    'hidden'
                                );
                            }, 1500);
                        }
                    );
            }

            textarea?.addEventListener('input', () => {
                channel?.whisper('typing', {
                    user_id: currentUserId,
                });
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();

                const hasMessage =
                    textarea?.value.trim().length > 0;
                const hasAttachment =
                    attachment?.files?.length > 0;
                const hasVoice =
                    voiceMessageInput?.files?.length > 0;

                if (!hasMessage && !hasAttachment && !hasVoice) {
                    return;
                }

                sendButton.disabled = true;
                sendButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );

                try {
                    const formData = new FormData(form);

                    const response = await window.axios.post(
                        form.action,
                        formData,
                        {
                            headers: {
                                Accept: 'application/json',
                                'Content-Type':
                                    'multipart/form-data',
                            },
                        }
                    );

                    appendMessage(
                        response.data.message,
                        true
                    );

                    form.reset();
                    resetVoiceRecording();

                    if (window.Alpine) {
                        form.dispatchEvent(
                            new Event(
                                'reset',
                                { bubbles: true }
                            )
                        );
                    }
                } catch (error) {
                    const responseData =
                        error.response?.data ?? {};

                    const errors =
                        responseData.errors;

                    let message =
                        responseData.message
                        ?? 'تعذر إرسال الرسالة. حاول مرة أخرى.';

                    if (errors) {
                        message = Object.values(errors)
                            .flat()
                            .join('\n');
                    }

                    alert(message);

                    if (
                        responseData.account_suspended
                        === true
                    ) {
                        window.location.href =
                            @json(
                                route(
                                    'moderation.appeal.create'
                                )
                            );
                    }
                } finally {
                    sendButton.disabled = false;
                    sendButton.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );
                }
            });
        });
    </script>
</x-app-layout>
