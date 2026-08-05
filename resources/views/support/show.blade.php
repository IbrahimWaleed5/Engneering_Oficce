<x-app-layout>
    @php
        $currentUser = auth()->user();

        $statusLabels = [
            'open' => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'محلولة',
            'closed' => 'مغلقة',
        ];

        $priorityLabels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'مرتفعة',
            'urgent' => 'عاجلة',
        ];

        /*
         * رسائل البوت والنظام يكون sender_id فيها null،
         * لذلك لا يجوز استخدام $message->sender->name مباشرة.
         */
        $senderName = function ($message): string {
            if ($message->sender) {
                return $message->sender->name;
            }

            return match ($message->sender_type) {
                'bot' => 'مساعد الوليد الهندسي',
                'system' => 'النظام',
                'admin' => 'إدارة المنصة',
                'employee' => 'موظف الدعم',
                'customer' => 'المستخدم',
                default => 'الدعم الفني',
            };
        };
    @endphp

    <style>
        .support-show-page {
            min-height: 100vh;
            overflow: hidden;
            color: #dae2fd;
            background: #0b1326;
            font-family:
                'Noto Sans Arabic',
                'Be Vietnam Pro',
                sans-serif;
        }

        .support-show-glass {
            background: rgba(23, 31, 51, .72);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(180, 197, 255, .1);
        }

        .support-show-blueprint {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: .08;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 38px 38px;
        }

        .support-show-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .support-show-scroll::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }

        [x-cloak] {
            display: none !important;
        }

        /*
         * إخفاء الـ Navbar/Header الأصلي القادم من x-app-layout
         * في هذه الصفحة فقط.
         */
        body > div.min-h-screen > nav,
        body > div.min-h-screen > header,
        body > div > nav.bg-white,
        body > div > nav.dark\:bg-gray-800,
        body > div > header.bg-white,
        body > div > header.dark\:bg-gray-800,
        body nav[data-layout-navigation],
        body header[data-layout-header] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .support-show-sidebar {
                display: none !important;
            }

            .support-show-main {
                margin-right: 0 !important;
            }

            .support-show-topbar {
                right: 0 !important;
            }
        }

        @media (max-width: 1279px) {
            .support-show-grid {
                grid-template-columns: 1fr !important;
            }

            .support-show-info {
                order: 2;
            }

            .support-show-chat {
                order: 1;
                height: auto !important;
                min-height: calc(100vh - 9rem);
            }
        }

        @media (max-width: 640px) {
            .support-show-topbar {
                height: 4.25rem !important;
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .support-show-main {
                padding: 5rem .75rem 5rem !important;
            }

            .support-show-chat {
                border-radius: 1rem !important;
            }

            .support-show-message {
                max-width: 92% !important;
            }

            .support-show-composer-actions {
                flex-direction: column !important;
            }

            .support-show-composer-actions > * {
                width: 100% !important;
            }
        }
    </style>

    <div
        x-data="{
            mobileMenuOpen: false,
            selectedFileName: '',
            detailsOpen: false
        }"
        class="support-show-page"
        dir="rtl"
    >
        <div class="support-show-blueprint"></div>

        {{-- الشريط العلوي --}}
        <header class="support-show-topbar fixed left-0 right-64 top-0 z-40 flex h-16 items-center justify-between border-b border-[#434655]/10 bg-[#0b1326]/85 px-6 backdrop-blur-xl">
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    @click="mobileMenuOpen = true"
                    class="flex items-center justify-center w-10 h-10 text-white rounded-xl bg-white/5 lg:hidden"
                    title="فتح القائمة"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center justify-center w-10 h-10 font-black text-blue-300 rounded-xl bg-blue-500/10">
                    و
                </div>

                <h1 class="hidden font-black text-[#dbe1ff] sm:block">
                    مكتب الوليد الهندسي
                </h1>
            </div>

            <nav class="items-center hidden gap-6 md:flex">
                <a href="{{ route('dashboard') }}" class="text-[#c3c6d7] transition hover:text-[#b4c5ff]">لوحة التحكم</a>
                <a href="{{ route('support.index') }}" class="border-b-2 border-[#b4c5ff] pb-1 font-bold text-[#b4c5ff]">التذاكر</a>
                <a href="{{ route('profile.edit') }}" class="text-[#c3c6d7] transition hover:text-[#b4c5ff]">الإعدادات</a>
            </nav>

            <div class="flex items-center gap-3">
                <a
                    href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                    class="text-[#b4c5ff]"
                    title="الإشعارات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="text-[#b4c5ff]"
                    title="الإعدادات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                    </svg>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center justify-center overflow-hidden font-bold text-white bg-blue-600 rounded-full h-9 w-9"
                    title="الصفحة الشخصية"
                >
                    @if ($currentUser->profile_photo)
                        <img
                            src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                            alt="{{ $currentUser->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        {{ mb_substr($currentUser->name, 0, 1) }}
                    @endif
                </a>
            </div>
        </header>

        {{-- القائمة الجانبية --}}
        <aside class="support-show-sidebar support-show-glass fixed right-0 top-16 z-40 flex h-[calc(100vh-4rem)] w-64 flex-col p-4">
            <div class="px-4 py-2 mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#b4c5ff]">
                    Engineering Support
                </h2>

                <p class="mt-1 text-xs text-[#c3c6d7]/70">
                    {{ $supportTicket->ticket_number }}
                </p>
            </div>

            <nav class="space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>

                    لوحة التحكم
                </a>

                <a
                    href="{{ route('support.index') }}"
                    class="flex items-center gap-3 rounded-xl bg-[#2563eb] px-4 py-3 font-bold text-white shadow-[0_0_15px_rgba(37,99,235,.3)]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                    </svg>

                    التذاكر
                </a>

                <a
                    href="{{ route('support.create') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>

                    تذكرة جديدة
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-[#c3c6d7] transition hover:bg-[#2d3449]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                    </svg>

                    الإعدادات
                </a>
            </nav>

            <div class="mt-auto space-y-2 border-t border-[#434655]/10 pt-4">
                <a
                    href="{{ route('support.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-2 text-sm text-[#c3c6d7] hover:text-white"
                >
                    مركز المساعدة
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-2 text-sm text-red-300 rounded-xl"
                    >
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- قائمة الجوال --}}
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 z-[90] bg-black/70 lg:hidden"
            @click="mobileMenuOpen = false"
        ></div>

        <aside
            x-cloak
            x-show="mobileMenuOpen"
            x-transition
            class="fixed right-0 top-0 z-[100] flex h-screen w-72 flex-col bg-[#0b1326] p-5 lg:hidden"
        >
            <div class="flex items-center justify-between">
                <h2 class="font-black text-white">تفاصيل التذكرة</h2>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/5"
                >
                    ✕
                </button>
            </div>

            <nav class="mt-8 space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl bg-white/5">لوحة التحكم</a>
                <a href="{{ route('support.index') }}" class="block px-4 py-3 text-blue-300 rounded-xl bg-blue-500/20">التذاكر</a>
                <a href="{{ route('support.create') }}" class="block px-4 py-3 rounded-xl bg-white/5">تذكرة جديدة</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="relative z-10 flex flex-col min-h-screen px-6 pt-20 pb-6 support-show-main lg:mr-64">
            @if (session('success'))
                <div class="mb-5 flex items-center justify-center gap-3 rounded-xl border border-green-500/30 bg-[#222a3d]/50 p-4 text-green-400">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m8 12 2.5 2.5L16 9"/>
                    </svg>

                    {{ session('success') }}
                </div>
            @endif

            <div class="grid flex-1 grid-cols-1 gap-6 overflow-hidden support-show-grid xl:grid-cols-4">
                {{-- بيانات التذكرة --}}
                <aside class="flex flex-col gap-6 support-show-info xl:col-span-1">
                    <section class="p-5 support-show-glass rounded-2xl">
                        <div class="mb-5 flex items-center justify-between border-b border-[#434655]/10 pb-4">
                            <span class="font-bold text-[#b4c5ff]">
                                معلومات التذكرة
                            </span>

                            <span class="rounded-full border border-[#b4c5ff]/30 bg-[#2563eb]/20 px-2 py-1 text-[10px] text-[#b4c5ff]">
                                {{ $supportTicket->ticket_number }}
                            </span>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <p class="mb-1 text-[11px] uppercase text-[#c3c6d7]">
                                    حالة الطلب
                                </p>

                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>

                                    <span class="font-bold text-white">
                                        {{ $statusLabels[$supportTicket->status] ?? $supportTicket->status }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <p class="mb-1 text-[11px] uppercase text-[#c3c6d7]">
                                    الأولوية
                                </p>

                                <span class="inline-flex px-3 py-1 text-xs font-bold text-white rounded-lg bg-red-950">
                                    {{ $priorityLabels[$supportTicket->priority] ?? $supportTicket->priority }}
                                </span>
                            </div>

                            <div>
                                <p class="mb-1 text-[11px] uppercase text-[#c3c6d7]">
                                    تاريخ الإنشاء
                                </p>

                                <p class="text-sm text-white">
                                    {{ $supportTicket->created_at->format('Y-m-d | H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 border-t border-[#434655]/10 pt-4">
                            <p class="mb-3 text-[11px] uppercase text-[#c3c6d7]">
                                موظف الدعم
                            </p>

                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#b4c5ff]/20 bg-blue-500/10 font-bold text-blue-300">
                                    {{ mb_substr($supportTicket->assignedEmployee?->name ?? 'غ', 0, 1) }}
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-white">
                                        {{ $supportTicket->assignedEmployee?->name ?? 'غير معيّن' }}
                                    </p>

                                    <p class="text-[10px] text-[#c3c6d7]">
                                        موظف الدعم الفني
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($supportTicket->is_escalated)
                            <div class="p-4 mt-5 border rounded-2xl border-violet-500/25 bg-violet-500/10">
                                <p class="font-black text-violet-200">
                                    تم تحويل التذكرة إلى المدير
                                </p>

                                <p class="mt-2 text-sm leading-7 text-violet-100">
                                    {{ $supportTicket->escalation_reason }}
                                </p>

                                @if ($supportTicket->escalated_at)
                                    <p class="mt-2 text-[10px] text-violet-200/70">
                                        {{ $supportTicket->escalated_at->format('Y-m-d H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </section>

                    @if (
                        auth()->id() === $supportTicket->assigned_employee_id
                        && ! $supportTicket->is_escalated
                        && $supportTicket->status !== 'closed'
                    )
                        <section class="p-5 support-show-glass rounded-2xl">
                            <h3 class="font-black text-amber-200">
                                تحويل المشكلة إلى المدير
                            </h3>

                            <p class="mt-2 text-xs leading-6 text-[#c3c6d7]">
                                استخدم هذا الخيار فقط عندما تحتاج المشكلة إلى قرار أو تدخل إداري.
                            </p>

                            <form
                                method="POST"
                                action="{{ route('support.escalate', $supportTicket) }}"
                                class="mt-4"
                            >
                                @csrf
                                @method('PATCH')

                                <textarea
                                    name="escalation_reason"
                                    rows="4"
                                    required
                                    maxlength="2000"
                                    class="w-full resize-none rounded-xl border border-amber-500/20 bg-[#060e20] p-3 text-sm text-white"
                                    placeholder="اكتب سبب تحويل المشكلة إلى المدير..."
                                >{{ old('escalation_reason') }}</textarea>

                                <button
                                    type="submit"
                                    class="w-full px-4 py-3 mt-3 font-black transition rounded-xl bg-amber-500/15 text-amber-200 hover:bg-amber-500/25"
                                    onclick="return confirm('هل تريد تحويل هذه المشكلة إلى المدير؟')"
                                >
                                    تحويل إلى المدير
                                </button>
                            </form>
                        </section>
                    @endif

                    <section class="p-5 support-show-glass rounded-2xl">
                        <h3 class="mb-4 font-bold text-white">
                            بيانات العميل
                        </h3>

                        <p class="text-sm text-white">
                            {{ $supportTicket->user?->name ?? 'مستخدم غير متاح' }}
                        </p>

                        <p class="mt-1 text-xs text-[#c3c6d7]">
                            {{ $supportTicket->user?->email ?? 'لا يوجد بريد إلكتروني' }}
                        </p>
                    </section>
                </aside>

                {{-- المحادثة --}}
                <section class="support-show-chat support-show-glass flex h-[calc(100vh-7rem)] flex-col overflow-hidden rounded-2xl xl:col-span-3">
                    <header class="flex items-center justify-between border-b border-[#434655]/10 bg-[#222a3d]/30 p-4">
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-bold text-[#b4c5ff]">
                                {{ $supportTicket->subject }}
                            </h2>

                            <p class="mt-1 text-xs text-[#c3c6d7]">
                                {{ $supportTicket->ticket_number }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            @if (
                                auth()->id() === $supportTicket->assigned_employee_id
                                || (
                                    auth()->user()->role === 'admin'
                                    && $supportTicket->is_escalated
                                )
                            )
                                <form
                                    method="POST"
                                    action="{{ route('support.status.update', $supportTicket) }}"
                                    class="flex items-center gap-2"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        class="rounded-lg border border-[#434655]/30 bg-[#060e20] px-3 py-2 text-sm text-white"
                                    >
                                        @foreach ($statusLabels as $value => $label)
                                            <option
                                                value="{{ $value }}"
                                                @selected($supportTicket->status === $value)
                                            >
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-[#b4c5ff]/10 px-4 py-2 text-sm font-bold text-[#b4c5ff]"
                                    >
                                        تحديث
                                    </button>
                                </form>
                            @endif
                        </div>
                    </header>

                    <div
                        id="supportMessagesContainer"
                        class="flex-1 p-6 space-y-6 overflow-y-auto support-show-scroll"
                    >
                        <div class="flex justify-center">
                            <span class="rounded-full bg-[#2d3449] px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-[#c3c6d7]">
                                {{ $supportTicket->created_at->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        @foreach ($supportTicket->messages as $message)
                            @php
                                $isMine = $message->sender_id === auth()->id();
                            @endphp

                            <div class="flex {{ $isMine ? 'justify-start' : 'justify-end' }}">
                                <div class="support-show-message max-w-[80%]">
                                    <p class="mb-2 text-[10px] font-bold {{
                                        $isMine
                                            ? 'text-[#b4c5ff]'
                                            : 'text-pink-300'
                                    }}">
                                        {{ $senderName($message) }}
                                    </p>

                                    <div class="rounded-2xl p-4 shadow-md {{
                                        $isMine
                                            ? 'rounded-tr-none border border-blue-400/20 bg-[#2563eb]/80 text-white'
                                            : 'rounded-tl-none border border-[#434655]/20 bg-[#2d3449]/90 text-white'
                                    }}">
                                        @if ($message->message)
                                            <p class="text-sm leading-7 whitespace-pre-wrap">
                                                {{ $message->message }}
                                            </p>
                                        @endif

                                        @if ($message->hasAttachment())
                                            <a
                                                href="{{ route('support.messages.attachment', $message) }}"
                                                class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-[#b4c5ff]/20 bg-[#2563eb]/10 p-3 transition hover:bg-[#2563eb]/20"
                                            >
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold truncate">
                                                        {{ $message->attachment_name ?? 'تحميل المرفق' }}
                                                    </p>

                                                    @if ($message->attachment_size)
                                                        <p class="mt-1 text-[10px] opacity-60">
                                                            {{ number_format($message->attachment_size / 1024, 1) }} KB
                                                        </p>
                                                    @endif
                                                </div>

                                                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <path d="M12 3v12M7 10l5 5 5-5M5 21h14"/>
                                                </svg>
                                            </a>
                                        @endif

                                        <span class="mt-2 block text-[9px] opacity-60">
                                            {{ $message->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($supportTicket->status !== 'closed')
                        <form
                            id="supportMessageForm"
                            method="POST"
                            action="{{ route('support.messages.store', $supportTicket) }}"
                            enctype="multipart/form-data"
                            class="border-t border-[#434655]/10 bg-[#131b2e]/60 p-4"
                        >
                            @csrf

                            <textarea
                                name="message"
                                rows="3"
                                placeholder="اكتب رسالتك هنا..."
                                class="w-full resize-none rounded-2xl border border-[#434655]/30 bg-[#060e20] p-4 text-sm text-white placeholder:text-[#c3c6d7]/40 focus:border-[#b4c5ff] focus:ring-2 focus:ring-[#b4c5ff]/20"
                            ></textarea>

                            <div class="flex items-center justify-between gap-4 mt-3 support-show-composer-actions">
                                <label
                                    for="supportAttachment"
                                    class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border border-[#434655]/30 bg-[#060e20] px-4 py-3"
                                >
                                    <svg class="h-5 w-5 text-[#c3c6d7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="m21 11-8.5 8.5a5 5 0 0 1-7-7L14 4a3.5 3.5 0 1 1 5 5l-8.5 8.5a2 2 0 0 1-3-3L15 7"/>
                                    </svg>

                                    <span
                                        class="truncate text-xs text-[#c3c6d7]"
                                        x-text="selectedFileName || 'لم يتم اختيار أي ملف'"
                                    ></span>

                                    <span class="mr-auto text-xs font-bold text-[#b4c5ff]">
                                        اختيار ملف
                                    </span>
                                </label>

                                <input
                                    id="supportAttachment"
                                    name="attachment"
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                                    class="hidden"
                                    @change="selectedFileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                >

                                <button
                                    id="sendSupportMessageButton"
                                    type="submit"
                                    class="flex h-12 w-28 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#b4c5ff] to-[#2563eb] font-bold text-[#002a78] shadow-lg transition hover:scale-[1.03]"
                                >
                                    إرسال

                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="m3 3 18 9-18 9 4-9-4-9Z"/>
                                        <path d="M7 12h14"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="border-t border-[#434655]/10 bg-[#131b2e]/60 p-5 text-center text-[#c3c6d7]">
                            هذه التذكرة مغلقة.
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container =
                document.getElementById(
                    'supportMessagesContainer'
                );

            if (container) {
                container.scrollTop =
                    container.scrollHeight;
            }

            const form =
                document.getElementById(
                    'supportMessageForm'
                );

            const sendButton =
                document.getElementById(
                    'sendSupportMessageButton'
                );

            form?.addEventListener('submit', function () {
                if (! sendButton) {
                    return;
                }

                sendButton.disabled = true;
                sendButton.textContent =
                    'جاري الإرسال...';

                sendButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            });
        });
    </script>
</x-app-layout>
