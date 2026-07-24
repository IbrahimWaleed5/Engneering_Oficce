<x-app-layout>

    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_25%),linear-gradient(to_bottom,_#020617,_#071132,_#020617)]"
        dir="rtl"
    >
        <div class="max-w-5xl px-4 py-10 mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 mb-4 border rounded-full bg-white/5 border-white/10"
                >
                    <span>🧾</span>
                    <span class="text-sm font-bold text-slate-300">
                        الملف المهني
                    </span>
                </div>

                <h1 class="text-3xl font-black text-white sm:text-4xl">
                    التخصص والنبذة
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    اختر تخصصك الهندسي واكتب نبذة تظهر للعملاء والإدارة.
                </p>
            </div>

            @include('profile.partials.settings-navigation')

            @if (session('success'))
                <div
                    class="p-4 mb-6 font-bold text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10"
                >
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="p-5 mb-6 text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10"
                >
                    <ul class="space-y-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section
                class="overflow-hidden border shadow-2xl rounded-[2rem] border-white/10 bg-slate-950/40 backdrop-blur-xl"
            >
                <div class="p-6 border-b sm:p-8 border-white/10">
                    <h2 class="text-2xl font-black text-white">
                        الملف الهندسي
                    </h2>

                    <p class="mt-2 text-sm text-slate-400">
                        ستظهر هذه المعلومات في صفحتك العامة.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('engineer.specialty.update') }}"
                    class="p-6 space-y-6 sm:p-8"
                >
                    @csrf
                    @method('PUT')

                    <div>
                        <label
                            for="specialty_id"
                            class="block mb-3 text-sm font-bold text-slate-200"
                        >
                            التخصص الهندسي
                        </label>

                        <select
                            id="specialty_id"
                            name="specialty_id"
                            required
                            class="w-full px-5 py-4 text-white border rounded-2xl border-white/10 bg-slate-900/70 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                        >
                            <option value="">
                                اختر التخصص
                            </option>

                            @foreach ($specialties as $specialty)
                                <option
                                    value="{{ $specialty->id }}"
                                    @selected(
                                        old(
                                            'specialty_id',
                                            $employeeProfile?->specialty_id
                                        ) == $specialty->id
                                    )
                                >
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($employeeProfile?->specialty)
                        <div
                            class="p-5 border rounded-2xl border-cyan-500/20 bg-cyan-500/5"
                        >
                            <p class="text-sm text-slate-400">
                                تخصصك الحالي
                            </p>

                            <p class="mt-2 text-lg font-black text-cyan-300">
                                {{ $employeeProfile->specialty->name }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <label
                            for="bio"
                            class="block mb-3 text-sm font-bold text-slate-200"
                        >
                            نبذة عن المهندس
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            rows="7"
                            maxlength="2000"
                            class="w-full px-5 py-4 text-white border resize-none rounded-2xl border-white/10 bg-slate-900/70 focus:border-cyan-400/40 focus:ring-2 focus:ring-cyan-500/20"
                            placeholder="اكتب نبذة عن خبرتك ومشاريعك ومجالات عملك..."
                        >{{ old('bio', $employeeProfile?->bio) }}</textarea>

                        <p class="mt-2 text-xs text-slate-500">
                            الحد الأقصى 2000 حرف.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-7 py-4 font-bold text-white transition shadow-xl bg-gradient-to-l from-cyan-500 to-blue-600 rounded-2xl hover:scale-[1.02]"
                        >
                            <span>💾</span>
                            <span>حفظ التخصص والنبذة</span>
                        </button>
                    </div>

                </form>
            </section>

        </div>
    </div>

</x-app-layout>
