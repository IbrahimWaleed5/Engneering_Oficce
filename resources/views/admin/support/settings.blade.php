<x-app-layout>
    <div class="min-h-screen py-10 bg-slate-950" dir="rtl">
        <div class="max-w-3xl px-4 mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-5 text-green-200 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 border shadow-2xl rounded-3xl border-white/10 bg-slate-900 sm:p-8">
                <h1 class="text-3xl font-black text-white">إعداد موظف الدعم</h1>

                <p class="mt-2 leading-7 text-slate-400">
                    اختر موظفًا واحدًا فقط. كل التذاكر الجديدة ستُسند إليه تلقائيًا.
                </p>

                <form
                    method="POST"
                    action="{{ route('admin.support.settings.update') }}"
                    class="mt-8 space-y-6"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="support_employee_id" class="block mb-2 font-bold text-slate-300">
                            موظف الدعم الفني
                        </label>

                        <select
                            id="support_employee_id"
                            name="support_employee_id"
                            required
                            class="w-full px-4 py-3 text-white border rounded-xl border-slate-700 bg-slate-950"
                        >
                            <option value="">اختر الموظف</option>

                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    @selected($setting->support_employee_id === $employee->id)
                                >
                                    {{ $employee->name }} — {{ $employee->email }}
                                </option>
                            @endforeach
                        </select>

                        @error('support_employee_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full px-6 py-3 font-black text-white bg-blue-600 rounded-xl"
                    >
                        حفظ موظف الدعم
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
