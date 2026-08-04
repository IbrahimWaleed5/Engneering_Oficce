<x-app-layout>
    <div class="py-10" dir="rtl">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

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

            @if ($errors->any())
                <div class="p-4 mb-6 text-red-100 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <ul class="space-y-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="relative p-8 mb-8 overflow-hidden border shadow-xl rounded-2xl bg-gradient-to-l from-blue-700 to-cyan-600 border-blue-500/30">
                <div class="absolute w-48 h-48 rounded-full -top-20 -left-10 bg-white/10"></div>

                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="mb-2 text-blue-100">
                            إدارة أعمال المكتب
                        </p>

                        <h1 class="mb-3 text-3xl font-bold text-white">
                            استشارات {{ $office->name }}
                        </h1>

                        <p class="max-w-3xl leading-7 text-blue-100">
                            عرض الاستشارات المحولة إلى المكتب وتعيين مهندس فعال من فريق المكتب لكل استشارة.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('office.members.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                    >
                        أعضاء المكتب
                    </a>

                    <a
                        href="{{ route('office.dashboard') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        لوحة المكتب
                    </a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 mb-8 sm:grid-cols-2 xl:grid-cols-4">
                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <p class="text-sm text-cyan-100">
                        جميع الاستشارات
                    </p>

                    <p class="mt-2 text-3xl font-black text-cyan-300">
                        {{ $statistics['all'] ?? 0 }}
                    </p>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <p class="text-sm text-yellow-100">
                        قيد الانتظار
                    </p>

                    <p class="mt-2 text-3xl font-black text-yellow-300">
                        {{ $statistics['pending'] ?? 0 }}
                    </p>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <p class="text-sm text-blue-100">
                        قيد التنفيذ
                    </p>

                    <p class="mt-2 text-3xl font-black text-blue-300">
                        {{ $statistics['in_progress'] ?? 0 }}
                    </p>
                </div>

                <div class="p-6 border shadow rounded-2xl bg-slate-900 border-slate-800">
                    <p class="text-sm text-green-100">
                        مكتملة
                    </p>

                    <p class="mt-2 text-3xl font-black text-green-300">
                        {{ $statistics['completed'] ?? 0 }}
                    </p>
                </div>
            </div>

            @php
                $officeEngineers = $office
                    ->members()
                    ->where('office_role', 'engineer')
                    ->where('status', 'active')
                    ->with('user:id,name,email')
                    ->get();
            @endphp

            <div class="space-y-5">
                @forelse ($consultations as $consultation)
                    @php
                        $statusData = match ($consultation->status) {
                            'in_progress' => [
                                'label' => 'قيد التنفيذ',
                                'class' => 'text-blue-200 border-blue-500/20 bg-blue-500/10',
                            ],

                            'completed' => [
                                'label' => 'مكتملة',
                                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                            ],

                            'cancelled' => [
                                'label' => 'ملغاة',
                                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                            ],

                            default => [
                                'label' => 'قيد الانتظار',
                                'class' => 'text-yellow-200 border-yellow-500/20 bg-yellow-500/10',
                            ],
                        };

                    @endphp

                    <div id="consultation-{{ $consultation->id }}" class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:border-blue-500/40">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-black text-white">
                                        {{ $consultation->title }}
                                    </h2>

                                    <span class="px-3 py-1 text-xs font-black border rounded-full {{ $statusData['class'] }}">
                                        {{ $statusData['label'] }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-slate-400">
                                    رقم الاستشارة:
                                    <span class="font-bold text-white">
                                        {{ $consultation->number }}
                                    </span>
                                </p>
                            </div>

                            <div class="text-sm text-slate-400">
                                تاريخ الإنشاء:
                                <span class="font-bold text-white">
                                    {{ $consultation->created_at?->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-4 mt-6 md:grid-cols-2 xl:grid-cols-4">
                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    العميل
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->customer?->name ?? 'غير معروف' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
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
                                    السعر النهائي
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ number_format((float) $consultation->final_price, 2) }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white/5">
                                <p class="text-xs text-slate-400">
                                    المهندس الحالي
                                </p>

                                <p class="mt-2 font-black text-white">
                                    {{ $consultation->engineer?->name ?? 'لم يتم التعيين' }}
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

                        @if ($consultation->status !== 'completed' && $consultation->status !== 'cancelled')
                            <form
                                method="POST"
                                action="{{ route(
                                    'office.consultations.assign-engineer',
                                    $consultation
                                ) }}"
                                class="p-5 mt-6 border rounded-2xl border-cyan-500/20 bg-cyan-500/5"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                                    <div>
                                        <label
                                            for="engineer_id_{{ $consultation->id }}"
                                            class="block mb-2 font-bold text-white"
                                        >
                                            تعيين مهندس من المكتب
                                        </label>

                                        <select
                                            id="engineer_id_{{ $consultation->id }}"
                                            name="engineer_id"
                                            required
                                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        >
                                            <option value="">
                                                اختر المهندس
                                            </option>

                                            @foreach ($officeEngineers as $member)
                                                <option
                                                    value="{{ $member->user_id }}"
                                                    @selected(
                                                        (string) old('engineer_id', $consultation->engineer_id)
                                                        === (string) $member->user_id
                                                    )
                                                >
                                                    {{ $member->user?->name ?? 'مهندس غير موجود' }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ($officeEngineers->isEmpty())
                                            <p class="mt-2 text-sm text-yellow-200">
                                                لا يوجد مهندسون فعالون في المكتب حاليًا.
                                            </p>
                                        @endif
                                    </div>

                                    <button
                                        type="submit"
                                        @disabled($officeEngineers->isEmpty())
                                        class="px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        حفظ التعيين
                                    </button>
                                </div>
                            </form>
                        @endif

                        <div class="flex flex-wrap gap-3 mt-6">
                            <a
                                href="{{ route('office.consultations.index') }}#consultation-{{ $consultation->id }}"
                                class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                            >
                                الاستشارة الحالية
                            </a>

                            @if ($consultation->engineer)
                                <span class="inline-flex items-center px-4 py-3 text-sm text-green-200 border rounded-xl border-green-500/20 bg-green-500/10">
                                    مسندة إلى:
                                    <span class="mr-1 font-black">
                                        {{ $consultation->engineer->name }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center border shadow rounded-2xl bg-slate-900 border-slate-800">
                        <div class="text-6xl">
                            📋
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            لا توجد استشارات محولة
                        </h2>

                        <p class="mt-3 text-slate-400">
                            لم يحول مدير النظام أي استشارة إلى المكتب حتى الآن.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($consultations->hasPages())
                <div class="mt-8">
                    {{ $consultations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
