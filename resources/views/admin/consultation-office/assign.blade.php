<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="max-w-5xl px-4 mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-6 text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 mb-6 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-8">
                <p class="text-sm font-bold text-cyan-300">
                    إدارة الاستشارات
                </p>

                <h1 class="mt-2 text-3xl font-black text-white">
                    تحويل الاستشارة إلى مكتب هندسي
                </h1>

                <p class="mt-3 leading-7 text-slate-400">
                    اختر مكتبًا فعالًا باشتراك ساري، وسيتم إلغاء تعيين
                    المهندس الحالي عند تحويل الاستشارة إلى المكتب.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            بيانات الاستشارة
                        </h2>

                        <div class="grid gap-4 mt-6 sm:grid-cols-2">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    رقم الاستشارة
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->number }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    العنوان
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->title }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    العميل
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->customer?->name ?? 'غير معروف' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $consultation->customer?->email }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    نوع الاستشارة
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    المهندس الحالي
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->engineer?->name ?? 'غير معين' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    المكتب الحالي
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->assignedOffice?->name ?? 'غير محولة إلى مكتب' }}
                                </p>
                            </div>
                        </div>

                        @if ($consultation->description)
                            <div class="p-4 mt-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    وصف الاستشارة
                                </p>

                                <p class="mt-2 leading-8 text-white">
                                    {{ $consultation->description }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            اختر المكتب الهندسي
                        </h2>

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.consultation-office.assign',
                                $consultation
                            ) }}"
                            class="mt-6 space-y-6"
                        >
                            @csrf
                            @method('PATCH')

                            <div>
                                <label
                                    for="office_id"
                                    class="block mb-2 font-bold text-white"
                                >
                                    المكتب
                                </label>

                                <select
                                    id="office_id"
                                    name="office_id"
                                    required
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                >
                                    <option value="">
                                        اختر المكتب
                                    </option>

                                    @foreach ($offices as $office)
                                        <option
                                            value="{{ $office->id }}"
                                            @selected(
                                                (string) old(
                                                    'office_id',
                                                    $consultation->assigned_office_id
                                                )
                                                === (string) $office->id
                                            )
                                        >
                                            {{ $office->name }}
                                            —
                                            {{ $office->city ?: 'مدينة غير محددة' }}
                                            —
                                            {{ $office->active_members_count ?? 0 }} عضو
                                            —
                                            {{ $office->consultations_count ?? 0 }} استشارة
                                        </option>
                                    @endforeach
                                </select>

                                @if ($offices->isEmpty())
                                    <p class="mt-3 text-sm leading-7 text-yellow-200">
                                        لا توجد مكاتب فعالة باشتراك ساري حاليًا.
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label
                                    for="notes"
                                    class="block mb-2 font-bold text-white"
                                >
                                    ملاحظات التحويل
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="6"
                                    maxlength="3000"
                                    placeholder="أضف أي ملاحظات لمدير المكتب..."
                                    class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                >{{ old('notes') }}</textarea>
                            </div>

                            <div class="p-5 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                <p class="font-black text-yellow-200">
                                    تنبيه
                                </p>

                                <p class="mt-2 text-sm leading-7 text-yellow-100">
                                    عند التحويل إلى مكتب جديد سيتم إزالة
                                    المهندس الحالي من الاستشارة، ثم يستطيع
                                    مدير المكتب تعيين مهندس من فريقه.
                                </p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <button
                                    type="submit"
                                    @disabled($offices->isEmpty())
                                    onclick="return confirm('هل تريد تحويل الاستشارة إلى المكتب المحدد؟')"
                                    class="inline-flex items-center justify-center px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    تأكيد تحويل الاستشارة
                                </button>

                                <a
                                    href="{{ route('consultations.index') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                                >
                                    العودة إلى الاستشارة
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <h2 class="text-xl font-black text-white">
                            المكاتب المتاحة
                        </h2>

                        <p class="mt-3 leading-7 text-slate-400">
                            تظهر فقط المكاتب الفعالة التي لديها اشتراك
                            شهري ساري.
                        </p>

                        <div class="mt-6 space-y-3">
                            @forelse ($offices as $office)
                                <div class="p-4 border rounded-2xl border-white/10 bg-white/5">
                                    <p class="font-black text-white">
                                        {{ $office->name }}
                                    </p>

                                    <p class="mt-2 text-sm text-slate-400">
                                        {{ $office->city ?: 'مدينة غير محددة' }}
                                    </p>

                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="px-3 py-1 text-xs font-black text-blue-200 border rounded-full border-blue-500/20 bg-blue-500/10">
                                            {{ $office->active_members_count ?? 0 }}
                                            عضو
                                        </span>

                                        <span class="px-3 py-1 text-xs font-black border rounded-full text-cyan-200 border-cyan-500/20 bg-cyan-500/10">
                                            {{ $office->consultations_count ?? 0 }}
                                            استشارة
                                        </span>
                                    </div>

                                    <p class="mt-3 text-xs text-green-300">
                                        الاشتراك حتى:
                                        {{ $office->subscription_ends_at?->format('Y-m-d') }}
                                    </p>
                                </div>
                            @empty
                                <div class="p-5 text-center border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                    <p class="text-sm leading-7 text-yellow-100">
                                        لا توجد مكاتب قابلة لاستقبال
                                        الاستشارة الآن.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
