<x-app-layout>

    <div
        class="max-w-3xl px-4 py-10 mx-auto"
        dir="rtl"
    >

        <div class="p-8 glass-panel-strong rounded-[2rem]">

            <div class="mb-8">

                <h1 class="text-3xl font-black text-white">
                    اختيار التخصص الهندسي
                </h1>

                <p class="mt-3 text-sm leading-7 text-slate-400">
                    اختر القسم الهندسي الذي تعمل ضمنه. سيظهر هذا التخصص
                    للإدارة عند توزيع الاستشارات.
                </p>

            </div>

            @if (session('success'))

                <div
                    class="p-4 mb-6 text-sm font-bold text-green-200 border border-green-500/20 rounded-2xl bg-green-500/10"
                >
                    {{ session('success') }}
                </div>

            @endif

            @if ($errors->any())

                <div
                    class="p-4 mb-6 text-sm text-red-200 border border-red-500/20 rounded-2xl bg-red-500/10"
                >
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @endif

            <form
                method="POST"
                action="{{ route('engineer.specialty.update') }}"
                class="space-y-6"
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
                        class="form-control"
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

                    @error('specialty_id')
                        <p class="mt-2 text-sm text-red-300">
                            {{ $message }}
                        </p>
                    @enderror

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

                <button
                    type="submit"
                    class="w-full primary-button"
                >
                    حفظ التخصص
                </button>

            </form>

        </div>

    </div>

</x-app-layout>
