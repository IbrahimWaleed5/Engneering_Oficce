<x-app-layout>
    @php
        $currentUser = auth()->user();
    @endphp

    <style>
        .consultation-create-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
            background: radial-gradient(circle at 50% 50%, #131b2e 0%, #060e20 100%);
            font-family: 'Cairo', 'Be Vietnam Pro', sans-serif;
        }

        .consultation-create-glass {
            background: rgba(19, 27, 46, .7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .37);
        }

        .consultation-create-control {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 8px;
            background: rgba(49, 57, 77, .3);
            padding: .75rem 1rem;
            color: white;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .consultation-create-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 15px rgba(37, 99, 235, .4);
        }

        .consultation-create-upload {
            border: 2px dashed #3b82f6;
            border-radius: 12px;
        }

        .consultation-create-step-active::after {
            position: absolute;
            top: 50%;
            right: -24px;
            width: 4px;
            height: 24px;
            content: '';
            border-radius: 2px;
            background: #2563eb;
            box-shadow: 0 0 10px #2563eb;
            transform: translateY(-50%);
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .consultation-create-sidebar {
                display: none !important;
            }

            .consultation-create-main {
                margin-right: 0 !important;
            }
        }
    </style>

    <div
        x-data="{
            mobileMenuOpen: false,
            fileName: '',
            dragActive: false,
            descriptionCount: {{ mb_strlen(old('description', '')) }},
            selectedType: @js(old('consultation_type_id', ''))
        }"
        class="consultation-create-page"
        dir="rtl"
    >
        <div class="flex min-h-screen overflow-hidden">
            {{-- القائمة الجانبية --}}
            <aside
                class="consultation-create-sidebar fixed right-0 top-0 z-50 flex h-screen w-72 flex-col border-l border-white/5 bg-[#060e20] lg:flex"
            >
                <div class="flex items-center gap-3 p-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-[#2563eb] rounded-lg shadow-lg shadow-blue-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-lg font-bold leading-tight text-white">
                            CreativeHome
                        </h1>

                        <p class="text-xs text-slate-500">
                            نظام الاستشارات الذكي
                        </p>
                    </div>
                </div>

                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-all rounded-lg text-slate-400 hover:bg-white/5"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 12l2-2 7-7 7 7 2 2M5 10v10a1 1 0 0 0 1 1h3m10-11v10a1 1 0 0 1-1 1h-3m-6 0v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>

                        <span>لوحة القيادة</span>
                    </a>

                    <a
                        href="{{ Route::has('consultations.mine') ? route('consultations.mine') : route('dashboard') }}"
                        class="flex items-center gap-3 rounded-lg border border-[#2563eb]/20 bg-[#2563eb]/10 px-4 py-3 text-[#3b82f6]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>

                        <span>المشاريع</span>
                    </a>

                    <a
                        href="{{ Route::has('conversations.index') ? route('conversations.index') : route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-all rounded-lg text-slate-400 hover:bg-white/5"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>

                        <span>المحادثات</span>
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-all rounded-lg text-slate-400 hover:bg-white/5"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                        </svg>

                        <span>الإعدادات</span>
                    </a>
                </nav>

                <div class="p-6 border-t border-white/5">
                    <a
                        href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 transition-colors text-slate-400 hover:text-white"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M9.7 9a2.5 2.5 0 1 1 3.5 2.3c-.8.35-1.2.8-1.2 1.7M12 17h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                        </svg>

                        <span>المساعدة</span>
                    </a>
                </div>
            </aside>

            {{-- القائمة الجانبية للجوال --}}
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
                class="fixed right-0 top-0 z-[100] flex h-screen w-72 flex-col border-l border-white/5 bg-[#060e20] p-5 lg:hidden"
            >
                <div class="flex items-center justify-between">
                    <h2 class="font-black text-white">CreativeHome</h2>

                    <button
                        type="button"
                        @click="mobileMenuOpen = false"
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/5"
                    >
                        ✕
                    </button>
                </div>

                <nav class="mt-8 space-y-3">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">لوحة القيادة</a>
                    <a href="{{ Route::has('consultations.mine') ? route('consultations.mine') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-[#2563eb]/10 text-[#3b82f6]">المشاريع</a>
                    <a href="{{ Route::has('conversations.index') ? route('conversations.index') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">المحادثات</a>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg bg-white/5">الإعدادات</a>
                </nav>
            </aside>

            <main class="flex flex-col flex-1 min-h-screen overflow-y-auto consultation-create-main lg:mr-72">
                {{-- الشريط العلوي --}}
                <header class="sticky top-0 z-40 flex items-center justify-between h-16 px-4 border-b consultation-create-glass border-white/5 sm:px-8">
                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="mobileMenuOpen = true"
                            class="text-slate-400 lg:hidden"
                            title="فتح القائمة"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M13 18h7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        </button>

                        <div class="relative hidden md:block">
                            <input
                                id="consultationPageSearch"
                                class="w-64 rounded-full border-none bg-[#31394d]/50 px-10 py-2 text-sm placeholder-slate-500 focus:ring-1 focus:ring-[#2563eb]"
                                placeholder="البحث في الطلبات..."
                                type="text"
                            >

                            <svg class="absolute w-4 h-4 text-slate-500 right-4 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <a
                            href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                            class="relative transition-colors text-slate-400 hover:text-white"
                            title="الإشعارات"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 17h5l-1.405-1.405A2 2 0 0 1 18 14.158V11a6 6 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159a2 2 0 0 1-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>

                            <span class="absolute top-0 left-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 pr-6 border-r border-white/10">
                            <div class="hidden text-left sm:block">
                                <p class="text-sm font-semibold text-white">
                                    {{ $currentUser->name }}
                                </p>

                                <p class="text-[10px] text-slate-500">
                                    {{ $currentUser->role === 'customer' ? 'عميل' : 'مستخدم النظام' }}
                                </p>
                            </div>

                            <div class="flex items-center justify-center w-10 h-10 overflow-hidden border-2 rounded-full border-[#2563eb]/20">
                                @if ($currentUser->profile_photo)
                                    <img
                                        src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                                        alt="{{ $currentUser->name }}"
                                        class="object-cover w-full h-full"
                                    >
                                @else
                                    <span class="flex items-center justify-center w-full h-full font-bold text-white bg-gradient-to-br from-blue-600 to-cyan-500">
                                        {{ mb_substr($currentUser->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                </header>

                <div class="w-full p-4 mx-auto max-w-7xl sm:p-8">
                    {{-- عنوان الصفحة --}}
                    <div class="flex flex-col items-start justify-between gap-5 mb-8 md:flex-row md:items-end">
                        <div>
                            <nav class="flex gap-2 mb-2 text-xs text-slate-500">
                                <a href="{{ route('dashboard') }}" class="hover:text-[#3b82f6]">لوحة القيادة</a>
                                <span>/</span>
                                <a href="{{ Route::has('consultations.mine') ? route('consultations.mine') : route('dashboard') }}" class="hover:text-[#3b82f6]">طلبات الاستشارة</a>
                                <span>/</span>
                                <span class="text-[#3b82f6]">طلب جديد</span>
                            </nav>

                            <h2 class="flex items-center gap-3 text-3xl font-bold text-white">
                                طلب استشارة جديدة

                                <span class="rounded-full border border-[#2563eb]/30 bg-[#2563eb]/20 px-3 py-1 text-xs text-[#3b82f6]">
                                    جديد
                                </span>
                            </h2>

                            <p class="mt-2 text-slate-400">
                                أدخل تفاصيل مشروعك وارفع الملفات اللازمة، ثم أكمل عملية الدفع
                            </p>
                        </div>

                        <a
                            href="{{ Route::has('consultations.mine') ? route('consultations.mine') : route('dashboard') }}"
                            class="flex items-center gap-2 rounded-lg bg-[#2563eb] px-6 py-2.5 font-semibold text-white shadow-lg shadow-blue-500/30 transition-all hover:bg-[#3b82f6]"
                        >
                            <span>أعمالي</span>

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 11H5m14 0-7 7m7-7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                            </svg>
                        </a>
                    </div>

                    <x-alerts />

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                        {{-- الجانب --}}
                        <div class="space-y-6 lg:col-span-3">
                            <div class="p-6 consultation-create-glass rounded-2xl">
                                <h4 class="pb-4 mb-6 text-sm font-bold border-b text-slate-300 border-white/5">
                                    مراحل الطلب
                                </h4>

                                <ul class="relative space-y-8">
                                    <li class="relative flex items-center gap-4">
                                        <div class="consultation-create-step-active relative z-10 flex h-10 w-10 items-center justify-center rounded-full bg-[#2563eb] font-bold text-white shadow-lg shadow-blue-500/40">
                                            1
                                        </div>

                                        <div>
                                            <p class="text-sm font-bold text-white">إضافة التفاصيل</p>
                                            <p class="text-[11px] font-medium text-[#3b82f6]">أنت هنا الآن</p>
                                        </div>
                                    </li>

                                    @foreach ([
                                        ['2', 'رفع إيصال الدفع'],
                                        ['3', 'مراجعة المدير'],
                                        ['4', 'بدء الاستشارة'],
                                    ] as [$stepNumber, $stepLabel])
                                        <li class="relative flex items-center gap-4">
                                            <div class="z-10 flex items-center justify-center w-10 h-10 font-bold border rounded-full bg-[#31394d] text-slate-500 border-white/5">
                                                {{ $stepNumber }}
                                            </div>

                                            <p class="text-sm font-bold text-slate-500">
                                                {{ $stepLabel }}
                                            </p>

                                            <div class="absolute right-[19px] -top-8 h-8 w-0.5 bg-white/5"></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="border-r-4 border-r-[#2563eb] bg-[#2563eb]/5 p-6 consultation-create-glass rounded-2xl">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 rounded-lg bg-yellow-500/20">
                                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M11 3a1 1 0 1 0-2 0v1a1 1 0 1 0 2 0V3ZM15.657 5.757a1 1 0 0 0-1.414-1.414l-.707.707a1 1 0 0 0 1.414 1.414l.707-.707ZM18 10a1 1 0 0 1-1 1h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 1 1ZM5.05 6.464A1 1 0 1 0 6.464 5.05l-.707-.707a1 1 0 0 0-1.414 1.414l.707.707ZM5 10a1 1 0 0 1-1 1H3a1 1 0 1 1 0-2h1a1 1 0 0 1 1 1Z"/>
                                        </svg>
                                    </div>

                                    <h5 class="text-sm font-bold text-white">
                                        قبل إرسال الطلب
                                    </h5>
                                </div>

                                <ul class="space-y-3 text-xs text-slate-400">
                                    <li class="flex items-start gap-2">
                                        <span class="mt-1 text-[#3b82f6]">✓</span>
                                        <span>اكتب وصفًا واضحًا ومفصلًا للمشروع.</span>
                                    </li>

                                    <li class="flex items-start gap-2">
                                        <span class="mt-1 text-[#3b82f6]">✓</span>
                                        <span>أرفق المخططات أو الصور المتوفرة.</span>
                                    </li>

                                    <li class="flex items-start gap-2">
                                        <span class="mt-1 text-[#3b82f6]">✓</span>
                                        <span>سعر جميع الاستشارات ثابت: 40 دولارًا.</span>
                                    </li>

                                    <li class="flex items-start gap-2">
                                        <span class="mt-1 text-[#3b82f6]">✓</span>
                                        <span>سيتم حفظ الطلب أولًا ثم تحويلك للدفع.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- النموذج --}}
                        <div class="lg:col-span-9">
                            <form
                                id="newConsultationForm"
                                method="POST"
                                action="{{ route('consultations.store') }}"
                                enctype="multipart/form-data"
                                class="p-6 space-y-8 consultation-create-glass rounded-2xl sm:p-8"
                            >
                                @csrf

                                @if (isset($engineer) && $engineer)
                                    <div class="flex flex-wrap items-center justify-between gap-4 p-5 border rounded-2xl border-green-400/20 bg-green-500/10">
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center justify-center flex-none text-xl font-black border rounded-full w-14 h-14 border-green-300/20 bg-gradient-to-br from-green-500 to-cyan-500">
                                                {{ mb_substr($engineer->name, 0, 1) }}
                                            </div>

                                            <div>
                                                <p class="text-xs font-bold text-green-200">المهندس المختار</p>
                                                <h3 class="mt-1 text-lg font-black text-white">{{ $engineer->name }}</h3>
                                                <p class="mt-1 text-sm text-green-100/70">سيتم إرسال الطلب لهذا المهندس بعد تأكيد الدفع</p>
                                            </div>
                                        </div>

                                        <span class="px-3 py-2 text-sm font-bold text-green-200 rounded-full bg-green-400/10">
                                            تم الاختيار ✓
                                        </span>
                                    </div>

                                    <input type="hidden" name="engineer_id" value="{{ $engineer->id }}">
                                @else
                                    <input type="hidden" name="engineer_id" value="">
                                @endif

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <label for="consultation_type_id" class="flex items-center gap-1 text-sm font-semibold text-slate-300">
                                            نوع الاستشارة <span class="text-red-500">*</span>
                                        </label>

                                        <div class="relative">
                                            <select
                                                id="consultation_type_id"
                                                name="consultation_type_id"
                                                required
                                                x-model="selectedType"
                                                class="appearance-none consultation-create-control"
                                            >
                                                <option value="">اختر نوع الاستشارة</option>

                                                @foreach ($types as $type)
                                                    <option
                                                        value="{{ $type->id }}"
                                                        @selected(old('consultation_type_id') == $type->id)
                                                    >
                                                        {{ $type->name }} — 40.00 دولار
                                                    </option>
                                                @endforeach
                                            </select>

                                            <div class="absolute -translate-y-1/2 pointer-events-none text-slate-500 left-4 top-1/2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="m19 9-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                </svg>
                                            </div>
                                        </div>

                                        @error('consultation_type_id')
                                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="title" class="flex items-center gap-1 text-sm font-semibold text-slate-300">
                                            عنوان المشروع <span class="text-red-500">*</span>
                                        </label>

                                        <input
                                            id="title"
                                            type="text"
                                            name="title"
                                            value="{{ old('title') }}"
                                            maxlength="255"
                                            required
                                            placeholder="مثال: تصميم منزل سكني أو تطوير نظام برمجي"
                                            class="consultation-create-control"
                                        >

                                        @error('title')
                                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <label for="description" class="flex items-center gap-1 text-sm font-semibold text-slate-300">
                                                وصف المشروع <span class="text-red-500">*</span>
                                            </label>

                                            <span class="text-[10px] text-slate-500">
                                                <span x-text="descriptionCount"></span> حرف
                                            </span>
                                        </div>

                                        <textarea
                                            id="description"
                                            name="description"
                                            rows="6"
                                            required
                                            @input="descriptionCount = $event.target.value.length"
                                            placeholder="اشرح تفاصيل المشروع، المتطلبات، والملاحظات المهمة..."
                                            class="resize-none consultation-create-control"
                                        >{{ old('description') }}</textarea>

                                        @error('description')
                                            <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="rounded-xl border border-[#2563eb]/20 bg-[#2563eb]/10 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-blue-100">سعر الاستشارة</span>
                                        <span class="text-2xl font-black text-[#3b82f6]">$40.00</span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label class="text-sm font-semibold text-slate-300">
                                        ملفات المشروع
                                    </label>

                                    <label
                                        class="consultation-create-upload group flex cursor-pointer flex-col items-center justify-center p-10 text-center transition-colors hover:bg-[#2563eb]/5"
                                        :class="dragActive ? 'bg-[#2563eb]/10' : ''"
                                        @dragover.prevent="dragActive = true"
                                        @dragleave.prevent="dragActive = false"
                                        @drop.prevent="
                                            dragActive = false;
                                            if ($event.dataTransfer.files.length) {
                                                const transfer = new DataTransfer();
                                                transfer.items.add($event.dataTransfer.files[0]);
                                                $refs.customerFile.files = transfer.files;
                                                fileName = $event.dataTransfer.files[0].name;
                                            }
                                        "
                                    >
                                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#2563eb]/10 transition-transform group-hover:scale-110">
                                            <svg class="w-8 h-8 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="m15.172 7-6.586 6.586a2 2 0 1 0 2.828 2.828l6.414-6.586a4 4 0 0 0-5.656-5.656l-6.415 6.585a6 6 0 1 0 8.486 8.486L20.5 13" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                            </svg>
                                        </div>

                                        <h5 class="mb-1 font-bold text-white">
                                            اضغط لاختيار ملف
                                        </h5>

                                        <p class="text-xs text-slate-500">
                                            أو اسحب الملف هنا مباشرة
                                        </p>

                                        <div class="mt-4 flex flex-wrap justify-center gap-2 text-[10px] font-medium uppercase tracking-wider text-slate-400">
                                            <span class="px-2 py-1 rounded bg-white/5">PDF</span>
                                            <span class="px-2 py-1 rounded bg-white/5">DWG</span>
                                            <span class="px-2 py-1 rounded bg-white/5">JPG</span>
                                            <span class="px-2 py-1 rounded bg-white/5">PNG</span>
                                            <span class="px-2 py-1 rounded bg-white/5">حتى 10MB</span>
                                        </div>

                                        <input
                                            x-ref="customerFile"
                                            class="hidden"
                                            type="file"
                                            name="customer_file"
                                            accept=".pdf,.jpg,.jpeg,.png,.dwg"
                                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                        >

                                        <div
                                            x-cloak
                                            x-show="fileName"
                                            x-transition
                                            class="px-4 py-2 mt-4 text-sm font-bold text-blue-200 rounded-xl bg-blue-500/10"
                                        >
                                            الملف المختار:
                                            <span x-text="fileName"></span>
                                        </div>
                                    </label>

                                    @error('customer_file')
                                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-4 pt-6 border-t sm:flex-row border-white/5">
                                    <button
                                        id="saveConsultationButton"
                                        class="flex flex-1 items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-[#2563eb] to-[#0891b2] py-4 font-bold text-white transition-all hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-blue-500/20"
                                        type="submit"
                                    >
                                        حفظ والمتابعة للدفع

                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="m10 19-7-7 7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                        </svg>
                                    </button>

                                    <a
                                        href="{{ auth()->user()->role === 'customer' && Route::has('consultations.mine') ? route('consultations.mine') : route('dashboard') }}"
                                        class="px-10 py-4 font-semibold text-center transition-all border rounded-xl border-white/5 bg-[#31394d]/20 text-slate-300 hover:bg-[#31394d]/40"
                                    >
                                        إلغاء
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form =
                document.getElementById(
                    'newConsultationForm'
                );

            const submitButton =
                document.getElementById(
                    'saveConsultationButton'
                );

            form?.addEventListener('submit', function () {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML =
                    'جاري الحفظ...';

                submitButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            });
        });
    </script>
</x-app-layout>
