<x-app-layout>

    <div
        x-data="{
            tab: 'profile'
        }"
        class="py-10"
        dir="rtl"
    >

        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- التبويبات --}}

            <div class="flex flex-wrap gap-3 p-3 mb-8 border rounded-2xl border-white/10 bg-slate-900/70">

                <button
                    type="button"
                    @click="tab = 'profile'"
                    :class="tab === 'profile'
                        ? 'bg-cyan-500 text-slate-950'
                        : 'bg-white/5 text-slate-300'"
                    class="px-5 py-3 text-sm font-black transition rounded-xl"
                >
                    👤 البيانات الشخصية
                </button>

                <button
                    type="button"
                    @click="tab = 'password'"
                    :class="tab === 'password'
                        ? 'bg-cyan-500 text-slate-950'
                        : 'bg-white/5 text-slate-300'"
                    class="px-5 py-3 text-sm font-black transition rounded-xl"
                >
                    🔐 كلمة المرور
                </button>

                @if (auth()->user()->role === 'engineer')

                    <button
                        type="button"
                        @click="tab = 'specialty'"
                        :class="tab === 'specialty'
                            ? 'bg-cyan-500 text-slate-950'
                            : 'bg-white/5 text-slate-300'"
                        class="px-5 py-3 text-sm font-black transition rounded-xl"
                    >
                        🏗️ التخصص والنبذة
                    </button>

                @endif

                <button
                    type="button"
                    @click="tab = 'delete'"
                    :class="tab === 'delete'
                        ? 'bg-red-500 text-white'
                        : 'bg-white/5 text-red-300'"
                    class="px-5 py-3 text-sm font-black transition rounded-xl"
                >
                    ⚠️ حذف الحساب
                </button>

            </div>

            {{-- البيانات الشخصية --}}

            <div
                x-show="tab === 'profile'"
                x-transition
            >
                <div class="p-8 glass-panel-strong rounded-[2rem]">

                    @include('profile.partials.update-profile-information-form')

                </div>
            </div>

            {{-- كلمة المرور --}}

            <div
                x-show="tab === 'password'"
                x-transition
            >
                <div class="p-8 glass-panel-strong rounded-[2rem]">

                    @include('profile.partials.update-password-form')

                </div>
            </div>

            {{-- التخصص والنبذة --}}

            @if (auth()->user()->role === 'engineer')

                <div
                    x-show="tab === 'specialty'"
                    x-transition
                >
                    <div class="p-8 glass-panel-strong rounded-[2rem]">

                        <div class="mb-6">

                            <h2 class="text-2xl font-black text-white">
                                التخصص والنبذة
                            </h2>

                            <p class="mt-2 text-sm text-slate-400">
                                عدّل تخصصك والنبذة التي تظهر في ملفك الشخصي.
                            </p>

                        </div>

                        <a
                            href="{{ route('engineer.specialty.edit') }}"
                            class="inline-flex primary-button"
                        >
                            تعديل التخصص والنبذة
                        </a>

                    </div>
                </div>

            @endif

            {{-- حذف الحساب --}}

            <div
                x-show="tab === 'delete'"
                x-transition
            >
                <div class="p-8 border glass-panel-strong rounded-[2rem] border-red-500/20">

                    @include('profile.partials.delete-user-form')

                </div>
            </div>

        </div>

    </div>

</x-app-layout>
