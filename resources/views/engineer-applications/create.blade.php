<x-app-layout>
    @php
        $isRenewal = $isRenewal ?? false;
        $lastApproved = $lastApproved ?? null;

        $savedSpecialtyId =
            auth()->user()->employeeProfile?->specialty_id
            ?? $lastApproved?->specialty_id;

        $currentUser = auth()->user();
    @endphp

    <style>
        .subscription-page {
            min-height: 100vh;
            overflow-x: hidden;
            background: #0b1326;
            color: #dae2fd;
            font-family: 'Noto Sans Arabic', 'Almarai', sans-serif;
        }

        .subscription-glass {
            background: rgba(23, 31, 51, .6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .subscription-glow {
            box-shadow: 0 0 20px rgba(37, 99, 235, .2);
        }

        .subscription-hero {
            background: linear-gradient(105deg, #2563eb 0%, #8343f4 100%);
        }

        .subscription-control {
            width: 100%;
            border: 1px solid rgba(67, 70, 85, .7);
            border-radius: .75rem;
            background: #131b2e;
            padding: .85rem 1rem;
            color: #fff;
            outline: none;
        }

        .subscription-control:focus {
            border-color: #b4c5ff;
            box-shadow: 0 0 0 2px rgba(180, 197, 255, .12);
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .subscription-sidebar {
                display: none !important;
            }

            .subscription-main {
                margin-right: 0 !important;
            }

            .subscription-topbar {
                right: 0 !important;
            }
        }
    </style>

    <div
        x-data="{
            mobileMenuOpen: false,
            certificateName: '',
            cvName: '',
            receiptName: '',
            receiptDrag: false
        }"
        class="subscription-page"
        dir="rtl"
    >
        {{-- الشريط العلوي --}}
        <header class="subscription-topbar fixed left-0 right-64 top-0 z-40 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/80 px-6 backdrop-blur-md">
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

                <h1 class="text-xl font-black text-[#b4c5ff]">
                    CreativeHome Engineering
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ Route::has('notifications.index') ? route('notifications.index') : route('dashboard') }}"
                    class="rounded-full p-2 text-[#b4c5ff] transition hover:bg-white/5"
                    title="الإشعارات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                        <path d="M10 21h4"/>
                    </svg>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="rounded-full p-2 text-[#b4c5ff] transition hover:bg-white/5"
                    title="الإعدادات"
                >
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>
                </a>

                <a href="{{ route('profile.edit') }}" class="h-10 w-10 overflow-hidden rounded-full border-2 border-[#2563eb]">
                    @if ($currentUser->profile_photo)
                        <img
                            src="{{ asset('storage/' . $currentUser->profile_photo) }}"
                            alt="{{ $currentUser->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        <span class="flex items-center justify-center w-full h-full font-black text-white bg-gradient-to-br from-blue-600 to-purple-600">
                            {{ mb_substr($currentUser->name, 0, 1) }}
                        </span>
                    @endif
                </a>
            </div>
        </header>

        {{-- القائمة الجانبية --}}
        <aside class="subscription-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-white/5 bg-[#131b2e] px-4 pb-6 pt-20 shadow-xl">
            <div class="px-4 mb-10">
                <h2 class="text-2xl font-bold text-[#b4c5ff]">
                    CreativeHome
                </h2>

                <p class="text-xs font-bold text-[#c3c6d7]/70">
                    Engineering Hub
                </p>
            </div>

            <nav class="flex-grow space-y-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-white"
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
                    href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}"
                    class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/>
                    </svg>

                    المشاريع
                </a>

                @if (Route::has('engineer.works.index'))
                    <a
                        href="{{ route('engineer.works.index') }}"
                        class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-white"
                    >
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-7h6v7"/>
                        </svg>

                        أعمالي
                    </a>
                @endif

                <a
                    href="{{ request()->url() }}"
                    class="flex items-center gap-4 rounded-lg bg-[#2563eb] px-4 py-3 font-bold text-[#eeefff] shadow-[0_0_15px_rgba(37,99,235,.3)]"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="5" y="3" width="14" height="18" rx="2"/>
                        <path d="M9 7h6M8 11h8M8 15h8"/>
                    </svg>

                    الاشتراك
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-white"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    الإعدادات
                </a>
            </nav>

            <div class="pt-6 mt-auto space-y-2 border-t border-white/5">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-white"
                >
                    الدعم
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-4 rounded-lg px-4 py-3 text-[#c3c6d7] transition hover:bg-[#222a3d] hover:text-red-300"
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
            class="fixed right-0 top-0 z-[100] flex h-screen w-72 flex-col bg-[#131b2e] p-5 lg:hidden"
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
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">لوحة التحكم</a>
                <a href="{{ Route::has('consultations.index') ? route('consultations.index') : route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">المشاريع</a>
                <a href="{{ request()->url() }}" class="block rounded-lg bg-[#2563eb] px-4 py-3">الاشتراك</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="min-h-screen px-4 pt-24 pb-12 subscription-main lg:mr-64 lg:px-6">
            <div class="mx-auto max-w-[1500px]">
                {{-- رسائل النظام --}}
                @if (session('success'))
                    <div class="p-4 mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="p-4 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                        <h2 class="mb-3 font-black">يرجى تصحيح الأخطاء التالية:</h2>

                        <ul class="space-y-2 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- البطاقة الرئيسية --}}
                <section class="relative flex items-center px-8 mb-8 overflow-hidden shadow-2xl subscription-hero min-h-48 rounded-xl lg:px-12">
                    <div class="relative z-10">
                        <h1 class="mb-2 text-3xl font-black text-white">
                            {{ $isRenewal
                                ? 'تجديد اشتراك المهندس'
                                : 'طلب الانضمام كمهندس' }}
                        </h1>

                        <p class="max-w-3xl leading-8 text-white/80">
                            @if ($isRenewal)
                                نرجو إكمال الدفع للتمتع بجميع ميزات المنصة. ستتم مراجعة طلبكم فوراً من قبل الإدارة بعد رفع إيصال التحويل.
                            @else
                                أدخل بيانات تخصصك وارفع الشهادة والسيرة الذاتية وإيصال الدفع. ستقوم الإدارة بمراجعة الطلب وتحديد مدة تفعيل حساب المهندس.
                            @endif
                        </p>
                    </div>

                    <div class="absolute w-64 h-64 rounded-full -left-20 -top-20 bg-white/10 blur-3xl"></div>

                    <div class="absolute -translate-y-1/2 left-10 top-1/2 opacity-20">
                        <svg class="text-white h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <rect x="5" y="3" width="14" height="18" rx="2"/>
                            <path d="M9 7h6M8 11h8M8 15h8"/>
                        </svg>
                    </div>
                </section>

                <form
                    id="engineerApplicationForm"
                    method="POST"
                    action="{{ route('engineer-applications.store') }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    @if ($isRenewal)
                        <input
                            type="hidden"
                            name="specialty_id"
                            value="{{ old('specialty_id', $savedSpecialtyId) }}"
                        >
                    @endif

                    <div class="grid grid-cols-12 gap-6">
                        {{-- العمود الجانبي --}}
                        <aside class="col-span-12 space-y-6 lg:col-span-4">
                            <div class="p-5 subscription-glass subscription-glow rounded-xl">
                                <h3 class="mb-4 text-xs font-bold uppercase tracking-widest text-[#c3c6d7]">
                                    {{ $isRenewal ? 'قيمة الاشتراك' : 'رسوم تقديم الطلب' }}
                                </h3>

                                <div class="flex items-baseline gap-2">
                                    <span class="text-5xl font-black text-[#b4c5ff]">150</span>
                                    <span class="text-xl font-bold text-[#b4c5ff]">ريال سعودي</span>
                                </div>

                                <p class="mt-2 text-sm leading-7 text-[#c3c6d7]/70">
                                    تشمل الوصول الكامل إلى مميزات المنصة بعد موافقة الإدارة.
                                </p>
                            </div>

                            <div class="p-5 subscription-glass rounded-xl">
                                <h3 class="mb-6 text-xs font-bold uppercase tracking-widest text-[#c3c6d7]">
                                    مراحل الطلب
                                </h3>

                                <div class="space-y-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#b4c5ff] font-bold text-[#002a78] ring-4 ring-[#b4c5ff]/20">
                                            1
                                        </div>

                                        <div>
                                            <h4 class="text-sm font-bold text-white">
                                                {{ $isRenewal ? 'رفع إيصال التحويل' : 'إرسال البيانات' }}
                                            </h4>

                                            <p class="text-[11px] text-[#c3c6d7]/60">
                                                {{ $isRenewal ? 'يرجى رفع صورة واضحة' : 'اختيار التخصص ورفع المستندات' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 opacity-50">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#434655] font-bold">2</div>

                                        <div>
                                            <h4 class="text-sm font-bold text-white">مراجعة الإدارة</h4>
                                            <p class="text-[11px] text-[#c3c6d7]">فحص الطلب وإيصال الدفع</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 opacity-50">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-[#434655] font-bold">3</div>

                                        <div>
                                            <h4 class="text-sm font-bold text-white">تفعيل حساب المهندس</h4>
                                            <p class="text-[11px] text-[#c3c6d7]">يصلك إشعار فور التفعيل</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>

                        {{-- محتوى النموذج --}}
                        <div class="col-span-12 space-y-6 lg:col-span-8">
                            @if (! $isRenewal)
                                <section class="p-5 subscription-glass rounded-xl">
                                    <h2 class="mb-6 text-2xl font-bold text-white">
                                        بيانات طلب الانضمام
                                    </h2>

                                    <div class="space-y-6">
                                        <div>
                                            <label for="specialty_id" class="mb-2 block text-sm font-bold text-[#c3c6d7]">
                                                التخصص الهندسي <span class="text-red-400">*</span>
                                            </label>

                                            <select
                                                id="specialty_id"
                                                name="specialty_id"
                                                required
                                                class="subscription-control"
                                            >
                                                <option value="">اختر التخصص الهندسي</option>

                                                @foreach ($specialties as $specialty)
                                                    <option
                                                        value="{{ $specialty->id }}"
                                                        @selected((string) old('specialty_id') === (string) $specialty->id)
                                                    >
                                                        {{ $specialty->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('specialty_id')
                                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="grid gap-5 md:grid-cols-2">
                                            <div>
                                                <label for="certificate_file" class="mb-2 block text-sm font-bold text-[#c3c6d7]">
                                                    الشهادة الهندسية <span class="text-red-400">*</span>
                                                </label>

                                                <label
                                                    for="certificate_file"
                                                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#434655] bg-[#222a3d]/20 p-8 text-center transition hover:border-[#b4c5ff]/50"
                                                >
                                                    <svg class="mb-3 h-10 w-10 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="M6 2h9l5 5v15H6zM14 2v6h6M12 18v-7M9 14l3-3 3 3"/>
                                                    </svg>

                                                    <span class="font-bold text-white">اضغط لرفع الشهادة</span>
                                                    <span class="mt-2 text-xs text-[#c3c6d7]/60">PDF أو JPG أو JPEG أو PNG</span>

                                                    <span
                                                        x-cloak
                                                        x-show="certificateName"
                                                        x-text="certificateName"
                                                        class="mt-3 text-sm font-bold text-cyan-300"
                                                    ></span>
                                                </label>

                                                <input
                                                    id="certificate_file"
                                                    name="certificate_file"
                                                    type="file"
                                                    required
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    class="hidden"
                                                    @change="certificateName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                                >

                                                @error('certificate_file')
                                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="cv_file" class="mb-2 block text-sm font-bold text-[#c3c6d7]">
                                                    السيرة الذاتية
                                                    <span class="font-normal text-[#8d90a0]">— اختياري</span>
                                                </label>

                                                <label
                                                    for="cv_file"
                                                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#434655] bg-[#222a3d]/20 p-8 text-center transition hover:border-[#d2bbff]/50"
                                                >
                                                    <svg class="mb-3 h-10 w-10 text-[#d2bbff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                                        <path d="M6 2h9l5 5v15H6zM14 2v6h6M9 13h6M9 17h6"/>
                                                    </svg>

                                                    <span class="font-bold text-white">اضغط لرفع السيرة الذاتية</span>
                                                    <span class="mt-2 text-xs text-[#c3c6d7]/60">PDF أو DOC أو DOCX</span>

                                                    <span
                                                        x-cloak
                                                        x-show="cvName"
                                                        x-text="cvName"
                                                        class="mt-3 text-sm font-bold text-purple-300"
                                                    ></span>
                                                </label>

                                                <input
                                                    id="cv_file"
                                                    name="cv_file"
                                                    type="file"
                                                    accept=".pdf,.doc,.docx"
                                                    class="hidden"
                                                    @change="cvName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                                >

                                                @error('cv_file')
                                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            @endif

                            <section class="p-5 subscription-glass rounded-xl">
                                <div class="flex items-center gap-2 mb-6">
                                    <svg class="h-6 w-6 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                                        <path d="M3 10h18M7 15h4"/>
                                    </svg>

                                    <h2 class="text-2xl font-bold text-white">
                                        بيانات الدفع المتاحة
                                    </h2>
                                </div>

                                <x-payment-information />
                            </section>

                            <section class="p-5 border rounded-xl border-red-400/20 bg-red-950/20">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 8v5M12 17h.01"/>
                                    </svg>

                                    <h4 class="text-sm font-bold text-red-300">
                                        ملاحظات هامة جداً
                                    </h4>
                                </div>

                                <ul class="list-inside list-disc space-y-2 text-sm text-[#c3c6d7]/80">
                                    <li>تأكد من كتابة اسمك الثلاثي في وصف عملية التحويل.</li>
                                    <li>يجب أن يكون إيصال التحويل واضحاً وبصيغة JPG أو PNG أو PDF.</li>
                                    <li>تتم مراجعة الطلب من الإدارة بعد وصول الإيصال.</li>
                                </ul>
                            </section>

                            <section class="p-5 subscription-glass rounded-xl">
                                <div class="mb-4">
                                    <h2 class="mb-1 text-sm font-bold text-white">
                                        {{ $isRenewal
                                            ? 'إرفاق إيصال دفع تجديد الاشتراك'
                                            : 'إرفاق إيصال دفع رسوم الطلب' }}
                                        <span class="text-red-400">*</span>
                                    </h2>

                                    <p class="text-sm text-[#c3c6d7]/60">
                                        الحد الأقصى للملف: 10 ميجابايت
                                    </p>
                                </div>

                                <label
                                    for="payment_receipt"
                                    class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-[#434655] bg-[#222a3d]/20 p-10 text-center transition hover:border-[#b4c5ff]/50"
                                    :class="receiptDrag ? 'border-[#b4c5ff] bg-[#b4c5ff]/5' : ''"
                                    @dragover.prevent="receiptDrag = true"
                                    @dragleave.prevent="receiptDrag = false"
                                    @drop.prevent="
                                        receiptDrag = false;
                                        if ($event.dataTransfer.files.length) {
                                            const transfer = new DataTransfer();
                                            transfer.items.add($event.dataTransfer.files[0]);
                                            $refs.receiptInput.files = transfer.files;
                                            receiptName = $event.dataTransfer.files[0].name;
                                        }
                                    "
                                >
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#b4c5ff]/10 transition group-hover:scale-110">
                                        <svg class="h-8 w-8 text-[#b4c5ff]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                            <path d="M12 16V4M7 9l5-5 5 5M4 20h16"/>
                                        </svg>
                                    </div>

                                    <p class="mb-2 text-base text-white">
                                        اضغط لرفع الإيصال أو اسحبه هنا
                                    </p>

                                    <p class="text-[11px] text-[#c3c6d7]">
                                        الملفات المدعومة: PNG, JPG, PDF
                                    </p>

                                    <span
                                        x-cloak
                                        x-show="receiptName"
                                        x-text="receiptName"
                                        class="mt-4 rounded-lg bg-[#b4c5ff]/10 px-4 py-2 text-sm font-bold text-[#b4c5ff]"
                                    ></span>
                                </label>

                                <input
                                    x-ref="receiptInput"
                                    id="payment_receipt"
                                    name="payment_receipt"
                                    type="file"
                                    required
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    class="hidden"
                                    @change="receiptName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                >

                                @error('payment_receipt')
                                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </section>

                            <div class="flex flex-col items-stretch justify-end gap-4 pt-4 sm:flex-row">
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="rounded-lg border border-[#434655] px-8 py-3 text-center font-bold text-[#c3c6d7] transition hover:bg-[#222a3d]"
                                >
                                    إلغاء
                                </a>

                                <button
                                    id="submitApplicationButton"
                                    type="submit"
                                    class="flex items-center justify-center gap-2 rounded-lg bg-[#b4c5ff] px-10 py-3 font-bold text-[#002a78] shadow-lg shadow-blue-500/30 transition hover:scale-[1.02] active:scale-95"
                                >
                                    <span>
                                        {{ $isRenewal
                                            ? 'إرسال وتأكيد التجديد'
                                            : 'إرسال طلب الانضمام' }}
                                    </span>

                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 18l6-6-6-6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form =
                document.getElementById(
                    'engineerApplicationForm'
                );

            const submitButton =
                document.getElementById(
                    'submitApplicationButton'
                );

            form?.addEventListener('submit', function () {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML =
                    'جاري الإرسال...';

                submitButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            });

            document
                .querySelectorAll('[data-copy-value]')
                .forEach((button) => {
                    button.addEventListener(
                        'click',
                        async function () {
                            const value =
                                button.dataset.copyValue || '';

                            if (!value) {
                                return;
                            }

                            try {
                                await navigator.clipboard.writeText(value);

                                const originalText =
                                    button.textContent;

                                button.textContent = 'تم النسخ';

                                setTimeout(() => {
                                    button.textContent =
                                        originalText;
                                }, 1500);
                            } catch (error) {
                                window.prompt(
                                    'انسخ القيمة التالية:',
                                    value
                                );
                            }
                        }
                    );
                });
        });
    </script>
</x-app-layout>
