<x-app-layout>
    @php
        $currentUser = auth()->user();
        $supportEmployee = $setting?->supportEmployee;

        $dashboardUrl = route('dashboard');

        $projectsUrl = Route::has('consultations.index')
            ? route('consultations.index')
            : $dashboardUrl;

        $teamUrl = Route::has('users.index')
            ? route('users.index')
            : $dashboardUrl;

        $financeUrl = Route::has('payments.index')
            ? route('payments.index')
            : (Route::has('invoices.index')
                ? route('invoices.index')
                : $dashboardUrl);

        $adminUrl = auth()->user()->role === 'admin'
            && Route::has('admin.support.index')
                ? route('admin.support.index')
                : route('support.index');

        $notificationsUrl = Route::has('notifications.index')
            ? route('notifications.index')
            : $dashboardUrl;
    @endphp

    <style>
        .support-create-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #dae2fd;
            background-color: #0b1326;
            background-image:
                linear-gradient(
                    to bottom,
                    rgba(11, 19, 38, .92),
                    rgba(11, 19, 38, .98)
                ),
                radial-gradient(
                    circle at 50% 25%,
                    rgba(37, 99, 235, .08),
                    transparent 45%
                );
            font-family:
                'Noto Sans Arabic',
                'Be Vietnam Pro',
                sans-serif;
        }

        .support-create-glass {
            background: rgba(23, 31, 51, .6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(180, 197, 255, .1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .37);
        }

        .support-create-glow {
            position: relative;
        }

        .support-create-glow::after {
            position: absolute;
            z-index: -1;
            content: '';
            inset: -1px;
            border-radius: inherit;
            background:
                linear-gradient(
                    45deg,
                    transparent,
                    #2563eb,
                    transparent
                );
            opacity: .3;
        }

        .support-create-control {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .05);
            border-radius: .75rem;
            background: #131b2e;
            padding: .85rem 1rem;
            color: #fff;
            outline: none;
            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .support-create-control:focus {
            border-color: rgba(180, 197, 255, .5);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15);
        }

        .support-create-button {
            box-shadow: 0 0 10px rgba(37, 99, 235, .2);
            transition:
                box-shadow .3s ease,
                transform .3s ease,
                opacity .3s ease;
        }

        .support-create-button:hover {
            box-shadow: 0 0 20px rgba(37, 99, 235, .5);
            transform: translateY(-1px);
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
            .support-create-sidebar {
                display: none !important;
            }

            .support-create-main {
                margin-right: 0 !important;
                padding-right: 1rem !important;
                padding-left: 1rem !important;
                padding-top: 5.5rem !important;
            }

            .support-create-topbar {
                right: 0 !important;
            }
        }

        @media (max-width: 640px) {
            .support-create-main {
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .support-create-topbar {
                height: 4.25rem !important;
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .support-create-heading {
                font-size: 1.7rem !important;
                line-height: 2.2rem !important;
            }

            .support-create-card {
                padding: 1rem !important;
            }

            .support-create-actions {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .support-create-actions > * {
                width: 100% !important;
            }

            .support-create-actions > div {
                width: 100% !important;
            }

            .support-create-actions > div > * {
                flex: 1;
            }
        }
    </style>

    <div
        x-data="{
            mobileMenuOpen: false,
            profileMenuOpen: false,
            fileName: '',
            dragActive: false
        }"
        class="support-create-page"
        dir="rtl"
    >
        {{-- الشريط العلوي المخصص --}}
        <header
            class="support-create-topbar fixed left-0 right-64 top-0 z-40 flex h-16 items-center justify-between border-b border-white/5 bg-[#0b1326]/80 px-6 backdrop-blur-md"
        >
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

                <h2 class="font-black text-[#b4c5ff]">
                    لوحة الدعم الفني
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <a
                    href="{{ $notificationsUrl }}"
                    class="relative text-[#c3c6d7] transition hover:text-[#b4c5ff]"
                    title="الإشعارات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>

                    <span class="absolute -right-0.5 top-0 h-2 w-2 rounded-full bg-pink-400"></span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="text-[#c3c6d7] transition hover:text-[#b4c5ff]"
                    title="الإعدادات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>
                </a>

                <div class="relative">
                    <button
                        type="button"
                        @click="profileMenuOpen = ! profileMenuOpen"
                        class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-[#b4c5ff]/20 bg-blue-500/10 font-bold text-white"
                        title="الحساب"
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
                    </button>

                    <div
                        x-cloak
                        x-show="profileMenuOpen"
                        x-transition
                        @click.outside="profileMenuOpen = false"
                        class="absolute left-0 mt-3 w-52 overflow-hidden rounded-xl border border-white/10 bg-[#131b2e] shadow-2xl"
                    >
                        <a
                            href="{{ route('profile.edit') }}"
                            class="block px-4 py-3 text-sm text-white transition hover:bg-white/5"
                        >
                            الصفحة الشخصية
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="block w-full px-4 py-3 text-sm text-right text-red-300 transition hover:bg-red-500/10"
                            >
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- القائمة الجانبية المخصصة --}}
        <aside
            class="support-create-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-white/10 bg-[#0b1326]/90 px-4 py-6 backdrop-blur-xl"
        >
            <div class="flex flex-col items-center mb-8">
                <img
                    src="{{ asset('images/Mainlogo.png') }}"
                    alt="شعار المكتب"
                    class="object-contain w-20 h-20 mb-4"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                <div
                    style="display:none"
                    class="items-center justify-center w-20 h-20 mb-4 text-2xl font-black text-blue-300 border rounded-2xl border-blue-400/20 bg-blue-500/10"
                >
                    و
                </div>
            </div>

            <nav class="flex-1 space-y-2">
                <a
                    href="{{ $projectsUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-blue-500/10 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 20h16M6 20V8l6-4 6 4v12M9 12h6M9 16h6"/>
                    </svg>

                    المشاريع
                </a>

                <a
                    href="{{ $teamUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-blue-500/10 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    الفريق
                </a>

                <a
                    href="{{ $financeUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-blue-500/10 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M3 9h18M7 14h4"/>
                    </svg>

                    المالية
                </a>

                <a
                    href="{{ route('support.index') }}"
                    class="flex items-center gap-3 rounded-lg border-l-4 border-[#b4c5ff] bg-[#b4c5ff]/5 px-4 py-3 font-bold text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                    </svg>

                    التذاكر
                </a>

                <a
                    href="{{ $adminUrl }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-blue-500/10 hover:text-[#b4c5ff]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                    </svg>

                    الإدارة
                </a>
            </nav>

            <a
                href="{{ route('support.create') }}"
                class="mt-auto flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-4 py-3 font-bold text-white transition hover:opacity-90"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"/>
                </svg>

                تذكرة جديدة
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf

                <button
                    type="submit"
                    class="flex items-center justify-center w-full gap-2 px-4 py-3 font-bold text-red-300 transition rounded-xl bg-red-500/10 hover:bg-red-500/20"
                >
                    تسجيل الخروج
                </button>
            </form>
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
                <h2 class="font-black text-white">
                    الدعم الفني
                </h2>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/5"
                >
                    ✕
                </button>
            </div>

            <nav class="mt-8 space-y-3">
                <a href="{{ $projectsUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">المشاريع</a>
                <a href="{{ $teamUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">الفريق</a>
                <a href="{{ $financeUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">المالية</a>
                <a href="{{ route('support.index') }}" class="block px-4 py-3 text-blue-300 rounded-xl bg-blue-500/20">التذاكر</a>
                <a href="{{ $adminUrl }}" class="block px-4 py-3 rounded-xl bg-white/5">الإدارة</a>
            </nav>

            <a
                href="{{ route('support.create') }}"
                class="px-4 py-3 mt-auto font-bold text-center text-white bg-blue-600 rounded-xl"
            >
                تذكرة جديدة
            </a>
        </aside>

        <main class="min-h-screen px-6 pt-24 pb-12 support-create-main lg:mr-64">
            <div class="max-w-4xl mx-auto">
                <div class="flex flex-col justify-between gap-4 mb-8 md:flex-row md:items-end">
                    <div>
                        <h1 class="text-3xl font-black text-white support-create-heading">
                            إنشاء تذكرة دعم جديدة
                        </h1>

                        <p class="mt-2 leading-7 text-[#c3c6d7]">
                            يرجى ملء التفاصيل أدناه لمساعدتنا في معالجة طلبك بسرعة وكفاءة.
                        </p>
                    </div>

                    @if ($supportEmployee)
                        <div class="px-4 py-3 text-sm text-blue-200 border rounded-xl border-blue-500/20 bg-blue-500/10">
                            سيتم إرسال التذكرة إلى:
                            <span class="font-black">
                                {{ $supportEmployee->name }}
                            </span>
                        </div>
                    @endif
                </div>

                @if (session('error'))
                    <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="p-6 support-create-card support-create-glass support-create-glow rounded-2xl sm:p-8">
                    <form
                        id="supportCreateForm"
                        method="POST"
                        action="{{ route('support.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-8"
                    >
                        @csrf

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="subject" class="text-sm font-bold uppercase text-[#b4c5ff]">
                                    عنوان المشكلة
                                </label>

                                <input
                                    id="subject"
                                    name="subject"
                                    type="text"
                                    value="{{ old('subject') }}"
                                    required
                                    maxlength="255"
                                    placeholder="مثال: تأخير في تسليم المخططات الإنشائية"
                                    class="support-create-control"
                                >

                                @error('subject')
                                    <p class="text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="priority" class="text-sm font-bold uppercase text-[#b4c5ff]">
                                    الأولوية
                                </label>

                                <select
                                    id="priority"
                                    name="priority"
                                    required
                                    class="cursor-pointer support-create-control"
                                >
                                    <option value="low" @selected(old('priority') === 'low')>منخفضة</option>
                                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>متوسطة</option>
                                    <option value="high" @selected(old('priority') === 'high')>عالية (عاجل)</option>
                                    <option value="urgent" @selected(old('priority') === 'urgent')>حرج (توقف العمل)</option>
                                </select>

                                @error('priority')
                                    <p class="text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="message" class="text-sm font-bold uppercase text-[#b4c5ff]">
                                وصف التفاصيل
                            </label>

                            <textarea
                                id="message"
                                name="message"
                                rows="6"
                                placeholder="اشرح تفاصيل المشكلة أو طلب الدعم هنا..."
                                class="resize-none support-create-control"
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <p class="text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold uppercase text-[#b4c5ff]">
                                إرفاق ملف
                            </label>

                            <label
                                for="attachment"
                                class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-white/10 bg-[#060e20]/30 p-8 text-center transition hover:bg-[#060e20]"
                                :class="dragActive ? 'border-[#b4c5ff] bg-blue-500/5' : ''"
                                @dragover.prevent="dragActive = true"
                                @dragleave.prevent="dragActive = false"
                                @drop.prevent="
                                    dragActive = false;
                                    if ($event.dataTransfer.files.length) {
                                        const transfer = new DataTransfer();
                                        transfer.items.add($event.dataTransfer.files[0]);
                                        $refs.attachment.files = transfer.files;
                                        fileName = $event.dataTransfer.files[0].name;
                                    }
                                "
                            >
                                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-blue-500/10 text-[#b4c5ff] transition group-hover:scale-110">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                        <path d="M12 16V4M7 9l5-5 5 5M4 20h16"/>
                                    </svg>
                                </div>

                                <p class="font-bold text-[#c3c6d7]">
                                    اسحب وأفلت المخططات أو الصور هنا
                                </p>

                                <p class="mt-2 text-xs text-[#8d90a0]">
                                    الحد الأقصى 10MB — PDF, JPG, JPEG, PNG, DOC, DOCX, ZIP
                                </p>

                                <span
                                    x-cloak
                                    x-show="fileName"
                                    x-text="fileName"
                                    class="px-4 py-2 mt-4 text-sm font-bold text-blue-300 rounded-lg bg-blue-500/10"
                                ></span>
                            </label>

                            <input
                                x-ref="attachment"
                                id="attachment"
                                name="attachment"
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip"
                                class="hidden"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                            >

                            @error('attachment')
                                <p class="text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4 pt-5 border-t support-create-actions border-white/5">
                            <div class="flex items-center gap-2 text-sm text-[#c3c6d7]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 8v4M12 16h.01"/>
                                </svg>

                                سيتم الرد خلال 24 ساعة عمل
                            </div>

                            <div class="flex gap-3">
                                <a
                                    href="{{ route('support.index') }}"
                                    class="rounded-xl px-8 py-3 text-center font-bold text-[#c3c6d7] transition hover:bg-white/5"
                                >
                                    إلغاء
                                </a>

                                <button
                                    id="submitSupportTicketButton"
                                    type="submit"
                                    class="support-create-button flex items-center justify-center gap-2 rounded-xl bg-[#2563eb] px-10 py-3 font-black text-white"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <path d="m3 3 18 9-18 9 4-9-4-9Z"/>
                                        <path d="M7 12h14"/>
                                    </svg>

                                    إرسال التذكرة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 gap-4 mt-8 md:grid-cols-3">
                    <a
                        href="{{ route('support.index') }}"
                        class="flex items-start gap-4 p-5 transition support-create-glass rounded-xl hover:border-blue-400/30"
                    >
                        <div class="p-3 text-blue-300 rounded-lg bg-blue-500/10">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                            </svg>
                        </div>

                        <div>
                            <h4 class="font-bold text-white">
                                تاريخ التذاكر
                            </h4>

                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                عرض ومتابعة الطلبات السابقة.
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('support.index') }}"
                        class="flex items-start gap-4 p-5 transition support-create-glass rounded-xl hover:border-pink-400/30"
                    >
                        <div class="p-3 text-pink-300 rounded-lg bg-pink-500/10">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 12a8 8 0 0 1 16 0v5a3 3 0 0 1-3 3h-2v-7h5"/>
                                <path d="M4 12v5a3 3 0 0 0 3 3h2v-7H4"/>
                            </svg>
                        </div>

                        <div>
                            <h4 class="font-bold text-white">
                                الدعم المباشر
                            </h4>

                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                تابع تذاكرك وتواصل مع موظف الدعم.
                            </p>
                        </div>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-start gap-4 p-5 transition support-create-glass rounded-xl hover:border-purple-400/30"
                    >
                        <div class="p-3 text-purple-300 rounded-lg bg-purple-500/10">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36"/>
                            </svg>
                        </div>

                        <div>
                            <h4 class="font-bold text-white">
                                دليل المستخدم
                            </h4>

                            <p class="mt-1 text-sm text-[#c3c6d7]">
                                إعدادات الحساب والملف الشخصي.
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form =
                document.getElementById(
                    'supportCreateForm'
                );

            const button =
                document.getElementById(
                    'submitSupportTicketButton'
                );

            form?.addEventListener('submit', function () {
                if (! button) {
                    return;
                }

                button.disabled = true;
                button.innerHTML =
                    'جاري الإرسال...';

                button.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            });
        });
    </script>
</x-app-layout>
