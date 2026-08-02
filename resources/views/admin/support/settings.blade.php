<x-app-layout>
    @php
        $currentUser = auth()->user();

        $selectedEmployee = $employees->firstWhere(
            'id',
            $setting->support_employee_id
        );
    @endphp

    <style>
        .support-settings-page {
            min-height: 100vh;
            overflow-x: hidden;
            color: #e2e8f0;
            background-color: #060e20;
            background-image:
                radial-gradient(
                    circle at 50% 50%,
                    rgba(37, 99, 235, .1) 0%,
                    transparent 50%
                ),
                linear-gradient(
                    rgba(255, 255, 255, .03) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, .03) 1px,
                    transparent 1px
                );
            background-size:
                100% 100%,
                40px 40px,
                40px 40px;
            font-family:
                'Noto Sans Arabic',
                'Almarai',
                sans-serif;
        }

        .support-settings-glass {
            background: rgba(11, 19, 38, .85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .1);
        }

        .support-settings-neon {
            box-shadow:
                0 0 10px rgba(37, 99, 235, .5),
                inset 0 0 5px rgba(37, 99, 235, .2);
        }

        .support-settings-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .support-settings-scroll::-webkit-scrollbar-track {
            background: #0b1326;
        }

        .support-settings-scroll::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background: #2563eb;
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .support-settings-sidebar {
                display: none !important;
            }

            .support-settings-main {
                margin-right: 0 !important;
                padding: 1rem !important;
                padding-top: 5.5rem !important;
            }

            .support-settings-topbar {
                right: 0 !important;
            }
        }

        @media (max-width: 640px) {
            .support-settings-main {
                padding-right: .75rem !important;
                padding-left: .75rem !important;
            }

            .support-settings-card {
                padding: 1rem !important;
                border-radius: 1rem !important;
            }

            .support-settings-title {
                font-size: 1.65rem !important;
                line-height: 2.2rem !important;
            }

            .support-settings-actions {
                flex-direction: column !important;
            }

            .support-settings-actions > * {
                width: 100% !important;
            }
        }
    </style>

    <div
        x-data="{
            mobileMenuOpen: false,
            dropdownOpen: false,
            search: '',
            selectedId: @js((string) old('support_employee_id', $setting->support_employee_id ?? '')),
            selectedName: @js(
                $selectedEmployee
                    ? $selectedEmployee->name . ' — ' . $selectedEmployee->email
                    : 'اختر الموظف'
            ),

            selectEmployee(id, name, email) {
                this.selectedId = String(id);
                this.selectedName = `${name} — ${email}`;
                this.dropdownOpen = false;
                this.search = '';
            },

            matches(name, email) {
                const query = this.search
                    .trim()
                    .toLowerCase();

                if (! query) {
                    return true;
                }

                return name
                    .toLowerCase()
                    .includes(query)
                    || email
                        .toLowerCase()
                        .includes(query);
            }
        }"
        class="support-settings-page"
        dir="rtl"
    >
        {{-- الشريط العلوي --}}
        <header
            class="support-settings-topbar fixed left-0 right-64 top-0 z-40 flex h-16 items-center justify-between border-b border-white/10 bg-[#0b1326]/85 px-5 backdrop-blur-md"
        >
            <div class="flex items-center gap-3">
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

                <div>
                    <h1 class="font-black text-white">
                        إعداد موظف الدعم
                    </h1>

                    <p class="text-[11px] text-slate-500">
                        لوحة إدارة الدعم الفني
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-[#93c5fd] transition hover:bg-white/5"
                    title="الإعدادات"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center justify-center w-10 h-10 overflow-hidden font-black text-white border rounded-full border-blue-400/40 bg-blue-500/10"
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
        <aside
            class="support-settings-sidebar fixed right-0 top-0 z-50 flex h-screen w-64 flex-col border-l border-white/10 bg-[#0b1326] px-4 py-8"
        >
            <div class="flex justify-center mb-12">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-blue-400/20 bg-blue-500/10 text-2xl font-black text-blue-300 shadow-[0_0_15px_rgba(37,99,235,.25)]"
                >
                    و
                </a>
            </div>

            <nav class="space-y-4">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-lg text-white/60 hover:bg-blue-500/10 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 12l2-2 7-7 7 7 2 2M5 10v10h4v-6h6v6h4V10"/>
                    </svg>

                    <span>الرئيسية</span>
                </a>

                <a
                    href="{{ route('admin.support.settings') }}"
                    class="flex items-center gap-3 px-4 py-3 text-blue-300 border rounded-lg border-blue-500/30 bg-blue-500/20"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path d="M3 20a6 6 0 0 1 12 0M14 20a5 5 0 0 1 7 0"/>
                    </svg>

                    <span>موظف الدعم</span>
                </a>

                <a
                    href="{{ route('admin.support.index') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-lg text-white/60 hover:bg-blue-500/10 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5h14v14H5zM8 9h8M8 13h5"/>
                    </svg>

                    <span>التذاكر</span>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-lg text-white/60 hover:bg-blue-500/10 hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06-2.12 2.12-.06-.06a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V20.5h-3v-.09a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.06.06-2.12-2.12.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.5v-3h.45A1.8 1.8 0 0 0 4.6 9a1.8 1.8 0 0 0-.36-1.98l-.06-.06 2.12-2.12.06.06A1.8 1.8 0 0 0 8.34 5.26 1.8 1.8 0 0 0 9.44 3.6V3.5h3v.1a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.06-.06 2.12 2.12-.06.06A1.8 1.8 0 0 0 19.4 9c.26.67.9 1.1 1.65 1.1h.45v3h-.45A1.8 1.8 0 0 0 19.4 15Z"/>
                    </svg>

                    <span>الإعدادات</span>
                </a>
            </nav>

            <div class="pt-5 mt-auto space-y-3 border-t border-white/10">
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 transition rounded-lg text-white/60 hover:bg-white/5 hover:text-white"
                >
                    <span>الدعم</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="flex items-center w-full gap-3 px-4 py-3 transition rounded-lg text-white/60 hover:bg-red-500/10 hover:text-red-300"
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
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg bg-white/5">الرئيسية</a>
                <a href="{{ route('admin.support.settings') }}" class="block px-4 py-3 text-blue-300 rounded-lg bg-blue-500/20">موظف الدعم</a>
                <a href="{{ route('admin.support.index') }}" class="block px-4 py-3 rounded-lg bg-white/5">التذاكر</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg bg-white/5">الإعدادات</a>
            </nav>
        </aside>

        <main class="min-h-screen p-12 overflow-y-auto support-settings-main lg:mr-64">
            @if (session('success'))
                <div class="max-w-3xl p-4 mx-auto mb-6 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-3xl p-4 mx-auto mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            <section class="max-w-3xl mx-auto mt-10">
                <div class="relative p-10 overflow-visible shadow-2xl support-settings-card support-settings-glass rounded-3xl">
                    <div class="absolute -top-24 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full bg-blue-500/20 blur-[80px]"></div>

                    <div class="relative z-10 mb-10 text-center">
                        <h1 class="mb-4 text-4xl font-bold tracking-tight text-white support-settings-title">
                            إعداد موظف الدعم
                        </h1>

                        <p class="max-w-xl mx-auto text-lg leading-8 text-slate-400">
                            اختر حسابًا واحدًا فقط.
                            <span class="text-sky-400">
                                كل التذاكر الجديدة ستُسند إليه تلقائيًا.
                            </span>
                        </p>
                    </div>

                    <form
                        id="supportEmployeeForm"
                        method="POST"
                        action="{{ route('admin.support.settings.update') }}"
                        class="relative z-20 space-y-8"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="support_employee_id"
                            :value="selectedId"
                        >

                        <div class="space-y-3">
                            <label class="block mr-2 text-sm font-medium tracking-widest uppercase text-slate-300">
                                موظف الدعم الفني المتاح
                            </label>

                            <div class="relative">
                                <button
                                    type="button"
                                    id="supportEmployeeDropdownToggle"
                                    @click="dropdownOpen = ! dropdownOpen"
                                    :aria-expanded="dropdownOpen ? 'true' : 'false'"
                                    class="support-settings-neon flex w-full items-center justify-between rounded-xl border-2 border-blue-500/30 bg-[#131b2e]/50 px-6 py-4 text-right transition hover:border-blue-500"
                                >
                                    <div class="flex items-center min-w-0 gap-4">
                                        <div class="flex items-center justify-center w-10 h-10 border rounded-full shrink-0 border-blue-500/40 bg-blue-500/20">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="12" cy="8" r="4"/>
                                                <path d="M5 21a7 7 0 0 1 14 0"/>
                                            </svg>
                                        </div>

                                        <span
                                            class="text-base text-white truncate sm:text-lg"
                                            x-text="selectedName"
                                        ></span>
                                    </div>

                                    <svg
                                        class="w-6 h-6 transition shrink-0 text-slate-500"
                                        :class="dropdownOpen ? 'rotate-180 text-blue-400' : ''"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="m19 9-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div
                                    x-cloak
                                    x-show="dropdownOpen"
                                    x-transition
                                    @click.outside="dropdownOpen = false"
                                    class="support-settings-scroll absolute right-0 top-full z-50 mt-3 max-h-96 w-full overflow-y-auto rounded-xl border border-white/10 bg-[#0b1326] shadow-2xl"
                                >
                                    <div class="sticky top-0 z-10 border-b border-white/10 bg-[#0b1326] p-3">
                                        <div class="relative">
                                            <input
                                                x-model="search"
                                                type="search"
                                                placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                                                class="w-full rounded-xl border border-white/10 bg-[#131b2e] py-3 pr-11 pl-4 text-white placeholder:text-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                @keydown.escape.stop="dropdownOpen = false"
                                            >

                                            <svg
                                                class="absolute w-5 h-5 -translate-y-1/2 right-4 top-1/2 text-slate-500"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <circle cx="11" cy="11" r="7"/>
                                                <path d="m20 20-3.5-3.5"/>
                                            </svg>
                                        </div>
                                    </div>

                                    @forelse ($employees as $employee)
                                        <button
                                            x-show="matches(
                                                @js($employee->name),
                                                @js($employee->email)
                                            )"
                                            type="button"
                                            @click="selectEmployee(
                                                @js($employee->id),
                                                @js($employee->name),
                                                @js($employee->email)
                                            )"
                                            class="flex items-center justify-between w-full gap-4 px-6 py-4 text-right transition border-b border-white/5 hover:bg-white/5"
                                            :class="selectedId === @js((string) $employee->id)
                                                ? 'bg-blue-600 text-white'
                                                : ''"
                                        >
                                            <div class="min-w-0">
                                                <p class="font-semibold text-white truncate">
                                                    {{ $employee->name }}
                                                </p>

                                                <p class="mt-1 text-sm truncate text-slate-400">
                                                    {{ $employee->email }}
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                @if (! empty($employee->role))
                                                    <span class="rounded-md border border-blue-500/20 bg-blue-500/10 px-2 py-1 text-[10px] font-bold text-blue-300">
                                                        {{ $employee->role }}
                                                    </span>
                                                @endif

                                                @if (! empty($employee->status))
                                                    <span
                                                        class="rounded-md border px-2 py-1 text-[10px] font-bold
                                                        {{
                                                            $employee->status === 'active'
                                                                ? 'border-green-500/20 bg-green-500/10 text-green-400'
                                                                : 'border-slate-500/20 bg-slate-500/10 text-slate-400'
                                                        }}"
                                                    >
                                                        {{ $employee->status }}
                                                    </span>
                                                @endif
                                            </div>
                                        </button>
                                    @empty
                                        <div class="px-6 py-10 text-center text-slate-400">
                                            لا توجد حسابات متاحة.
                                        </div>
                                    @endforelse

                                    <div
                                        x-show="
                                            search
                                            && ! Array.from(
                                                $el.parentElement.querySelectorAll(
                                                    'button[x-show]'
                                                )
                                            ).some(
                                                button =>
                                                    button.style.display !== 'none'
                                            )
                                        "
                                        class="px-6 py-10 text-center text-slate-400"
                                    >
                                        لا توجد نتيجة مطابقة.
                                    </div>
                                </div>
                            </div>

                            @error('support_employee_id')
                                <p class="mt-2 text-sm text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 support-settings-actions">
                            <a
                                href="{{ route('admin.support.index') }}"
                                class="px-8 py-4 font-semibold text-center transition rounded-xl text-slate-400 hover:bg-white/5 hover:text-white"
                            >
                                إلغاء الأمر
                            </a>

                            <button
                                id="saveSupportEmployeeButton"
                                type="submit"
                                class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-10 py-4 font-bold text-white shadow-[0_0_15px_rgba(37,99,235,.4)] transition hover:bg-blue-500 active:scale-95"
                            >
                                <span>حفظ الإعدادات</span>

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 7l-5 5 5 5M6 12h14"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-30"></div>
                </div>

                <p class="mt-8 text-sm text-center text-slate-500">
                    نظام إدارة الدعم الفني — مكتب الوليد الهندسي
                </p>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form =
                document.getElementById(
                    'supportEmployeeForm'
                );

            const saveButton =
                document.getElementById(
                    'saveSupportEmployeeButton'
                );

            form?.addEventListener('submit', function (event) {
                const selectedInput =
                    form.querySelector(
                        'input[name="support_employee_id"]'
                    );

                if (! selectedInput?.value) {
                    event.preventDefault();

                    window.alert(
                        'يرجى اختيار موظف الدعم الفني.'
                    );

                    return;
                }

                if (! saveButton) {
                    return;
                }

                saveButton.disabled = true;
                saveButton.innerHTML =
                    'جاري الحفظ...';

                saveButton.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            });
        });
    </script>
</x-app-layout>
