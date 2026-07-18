<x-app-layout>

    <div
        x-data="{
            search: '',
            statusFilter: 'all',
            paymentFilter: 'all'
        }"
        class="relative py-12"
        dir="rtl"
    >

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <x-page-header
                title="إدارة الاستشارات"
                description="متابعة جميع الطلبات، الدفع، المهندسين وحالة التنفيذ"
                icon="🗂️"
            >
                <x-slot name="actions">

                    <a
                        href="{{ route('consultations.create') }}"
                        class="primary-button"
                    >
                        <span>➕</span>
                        استشارة جديدة
                    </a>

                </x-slot>
            </x-page-header>

            <x-alerts />

            {{-- الإحصائيات --}}

            <div class="grid gap-5 mb-8 sm:grid-cols-2 xl:grid-cols-4">

                <div class="p-6 glass-card rounded-3xl">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                جميع الاستشارات
                            </p>

                            <p class="mt-3 text-3xl font-black text-white">
                                {{ $consultations->count() }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-blue-500/10"
                        >
                            📋
                        </div>

                    </div>

                </div>

                <div class="p-6 glass-card rounded-3xl">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                بانتظار الدفع
                            </p>

                            <p class="mt-3 text-3xl font-black text-orange-300">
                                {{ $consultations->where('payment_status', 'unpaid')->count() }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-orange-500/10"
                        >
                            💳
                        </div>

                    </div>

                </div>

                <div class="p-6 glass-card rounded-3xl">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                قيد التنفيذ
                            </p>

                            <p class="mt-3 text-3xl font-black text-cyan-300">
                                {{ $consultations->where('status', 'in_progress')->count() }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-cyan-500/10"
                        >
                            ⚙️
                        </div>

                    </div>

                </div>

                <div class="p-6 glass-card rounded-3xl">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-400">
                                مكتملة
                            </p>

                            <p class="mt-3 text-3xl font-black text-green-300">
                                {{ $consultations->where('status', 'completed')->count() }}
                            </p>

                        </div>

                        <div
                            class="flex items-center justify-center text-2xl w-14 h-14 rounded-2xl bg-green-500/10"
                        >
                            ✅
                        </div>

                    </div>

                </div>

            </div>
            <section
    class="p-6 mb-8 glass-panel-strong rounded-[2rem]"
    dir="rtl"
>

    <div class="mb-6">

        <h3 class="text-xl font-black text-white">
            البحث والفلترة
        </h3>

        <p class="mt-1 text-sm text-slate-400">
            ابحث عن الاستشارات حسب العميل أو الرقم أو الحالة أو المهندس
        </p>

    </div>

    <form
        method="GET"
        action="{{ route('consultations.index') }}"
        class="grid gap-5 md:grid-cols-2 xl:grid-cols-4"
    >

        {{-- البحث --}}
        <div class="md:col-span-2">

            <label
                for="search"
                class="block mb-2 text-sm font-bold text-slate-300"
            >
                اسم العميل أو رقم الاستشارة
            </label>

            <input
                id="search"
                name="search"
                type="text"
                value="{{ request('search') }}"
                placeholder="مثال: CONS-123 أو اسم العميل"
                class="form-control"
            >

        </div>

        {{-- الحالة --}}
        <div>

            <label
                for="status"
                class="block mb-2 text-sm font-bold text-slate-300"
            >
                حالة الاستشارة
            </label>

            <select
                id="status"
                name="status"
                class="form-control"
            >

                <option value="">
                    جميع الحالات
                </option>

                <option
                    value="waiting_payment"
                    @selected(
                        request('status') === 'waiting_payment'
                    )
                >
                    بانتظار الدفع
                </option>

                <option
                    value="pending"
                    @selected(
                        request('status') === 'pending'
                    )
                >
                    قيد الانتظار
                </option>

                <option
                    value="in_progress"
                    @selected(
                        request('status') === 'in_progress'
                    )
                >
                    قيد التنفيذ
                </option>

                <option
                    value="completed"
                    @selected(
                        request('status') === 'completed'
                    )
                >
                    مكتملة
                </option>

                <option
                    value="cancelled"
                    @selected(
                        request('status') === 'cancelled'
                    )
                >
                    ملغاة
                </option>

            </select>

        </div>

        {{-- المهندس --}}
        <div>

            <label
                for="engineer_id"
                class="block mb-2 text-sm font-bold text-slate-300"
            >
                المهندس
            </label>

            <select
                id="engineer_id"
                name="engineer_id"
                class="form-control"
            >

                <option value="">
                    جميع المهندسين
                </option>

                @foreach ($engineers as $engineer)

                    <option
                        value="{{ $engineer->id }}"
                        @selected(
                            (string) request('engineer_id')
                            === (string) $engineer->id
                        )
                    >
                        {{ $engineer->name }}
                    </option>

                @endforeach

            </select>

        </div>

        {{-- من تاريخ --}}
        <div>

            <label
                for="date_from"
                class="block mb-2 text-sm font-bold text-slate-300"
            >
                من تاريخ
            </label>

            <input
                id="date_from"
                name="date_from"
                type="date"
                value="{{ request('date_from') }}"
                class="form-control"
            >

        </div>

        {{-- إلى تاريخ --}}
        <div>

            <label
                for="date_to"
                class="block mb-2 text-sm font-bold text-slate-300"
            >
                إلى تاريخ
            </label>

            <input
                id="date_to"
                name="date_to"
                type="date"
                value="{{ request('date_to') }}"
                class="form-control"
            >

        </div>

        {{-- الأزرار --}}
        <div
            class="flex flex-wrap items-end gap-3 md:col-span-2"
        >

            <button
                type="submit"
                class="primary-button"
            >
                تطبيق الفلاتر
            </button>

            <a
                href="{{ route('consultations.index') }}"
                class="px-5 py-3 font-bold transition border rounded-2xl border-slate-600 text-slate-300 hover:bg-slate-800"
            >
                مسح الفلاتر
            </a>

        </div>

    </form>

</section>
<div
    class="flex flex-wrap items-center justify-between gap-4 p-6 border-b border-white/10"
>

    <div>

        <h3 class="text-xl font-black text-white">
            نتائج الاستشارات
        </h3>

        <p class="mt-1 text-sm text-slate-400">
            تم العثور على
            <span class="font-black text-cyan-300">
                {{ $consultations->total() }}
            </span>
            استشارة
        </p>

    </div>

</div>

            {{-- البحث والفلاتر --}}


            {{-- سطح المكتب --}}

            <div class="hidden overflow-x-auto lg:block">

                <table class="table-glass">

                    <thead>

                        <tr>
                            <th>رقم الاستشارة</th>
                            <th>العميل</th>
                            <th>عنوان الطلب</th>
                            <th>المهندس</th>
                            <th>السعر</th>
                            <th>الدفع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>

                    </thead>

                    <tbody>
                        @if ($consultations->hasPages())

    <div class="p-6 border-t border-white/10">
        {{ $consultations->links() }}
    </div>

@endif
                        @forelse ($consultations as $consultation)

                            @php
                                $searchText = strtolower(
                                    ($consultation->consultation_number ?? '') . ' ' .
                                    ($consultation->title ?? '') . ' ' .
                                    ($consultation->customer?->name ?? '') . ' ' .
                                    ($consultation->engineer?->name ?? '')
                                );
                            @endphp

                            <tr
                                x-show="
                                    (
                                        search === ''
                                        || @js($searchText).includes(
                                            search.toLowerCase()
                                        )
                                    )
                                    &&
                                    (
                                        statusFilter === 'all'
                                        || statusFilter === @js($consultation->status)
                                    )
                                    &&
                                    (
                                        paymentFilter === 'all'
                                        || paymentFilter === @js($consultation->payment_status)
                                    )
                                "
                                x-transition
                            >

                                <td>

                                    <span
                                        class="px-3 py-2 text-xs font-bold rounded-xl bg-white/5 text-slate-300"
                                    >
                                        {{ $consultation->consultation_number }}
                                    </span>

                                </td>

                                <td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex items-center justify-center flex-none w-10 h-10 font-bold rounded-full bg-gradient-to-br from-blue-600 to-cyan-500"
                                        >
                                            {{ mb_substr(
                                                $consultation->customer?->name ?? 'ع',
                                                0,
                                                1
                                            ) }}
                                        </div>

                                        <div>

                                            <p class="font-bold text-white">
                                                {{ $consultation->customer?->name ?? 'غير معروف' }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                عميل
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    <p class="max-w-xs font-bold text-white truncate">
                                        {{ $consultation->title }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $consultation->consultationType?->name ?? 'غير محدد' }}
                                    </p>

                                </td>

                                <td>

                                    @if ($consultation->engineer)

                                        <span class="font-bold text-cyan-200">
                                            {{ $consultation->engineer->name }}
                                        </span>

                                    @else

                                        <span class="text-sm text-slate-500">
                                            غير معيّن
                                        </span>

                                    @endif

                                </td>

                                <td class="font-black text-cyan-300">

                                    {{ number_format(
                                        $consultation->final_price,
                                        2
                                    ) }}
                                    ₪

                                </td>

                                <td>

                                    @if ($consultation->payment_status === 'paid')

                                        <span
                                            class="text-green-200 status-badge bg-green-500/10"
                                        >
                                            مدفوع
                                        </span>

                                    @elseif ($consultation->payment_status === 'pending')

                                        <span
                                            class="text-yellow-200 status-badge bg-yellow-500/10"
                                        >
                                            قيد الفحص
                                        </span>

                                    @else

                                        <span
                                            class="text-red-200 status-badge bg-red-500/10"
                                        >
                                            غير مدفوع
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if ($consultation->status === 'waiting_payment')

                                        <span
                                            class="text-orange-200 status-badge bg-orange-500/10"
                                        >
                                            بانتظار الدفع
                                        </span>

                                    @elseif ($consultation->status === 'pending')

                                        <span
                                            class="text-yellow-200 status-badge bg-yellow-500/10"
                                        >
                                            قيد المراجعة
                                        </span>

                                    @elseif ($consultation->status === 'in_progress')

                                        <span
                                            class="text-blue-200 status-badge bg-blue-500/10"
                                        >
                                            قيد التنفيذ
                                        </span>

                                    @elseif ($consultation->status === 'completed')

                                        <span
                                            class="text-green-200 status-badge bg-green-500/10"
                                        >
                                            مكتملة
                                        </span>

                                    @elseif ($consultation->status === 'cancelled')

                                        <span
                                            class="text-red-200 status-badge bg-red-500/10"
                                        >
                                            ملغاة
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="flex items-center gap-2">

                                        @if (!$consultation->engineer)

                                            <a
                                                href="{{ route(
                                                    'consultations.assign.form',
                                                    $consultation
                                                ) }}"
                                                class="px-3 py-2 text-xs font-bold text-blue-200 transition rounded-xl bg-blue-500/10 hover:bg-blue-500/20"
                                            >
                                                تعيين مهندس
                                            </a>

                                        @else

                                            <a
                                                href="{{ route(
                                                    'consultations.assign.form',
                                                    $consultation
                                                ) }}"
                                                class="px-3 py-2 text-xs font-bold text-purple-200 transition rounded-xl bg-purple-500/10 hover:bg-purple-500/20"
                                            >
                                                تغيير المهندس
                                            </a>

                                        @endif

                                        @if ($consultation->customer_file)

                                            <a
                                                href="{{ asset(
                                                    'storage/' .
                                                    $consultation->customer_file
                                                ) }}"
                                                target="_blank"
                                                class="px-3 py-2 text-xs font-bold text-cyan-200 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20"
                                            >
                                                ملف العميل
                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="py-12 text-center text-slate-400"
                                >
                                    لا توجد استشارات حتى الآن.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- الموبايل --}}

            <div class="grid gap-5 lg:hidden">

                @foreach ($consultations as $consultation)

                    @php
                        $mobileSearchText = strtolower(
                            ($consultation->consultation_number ?? '') . ' ' .
                            ($consultation->title ?? '') . ' ' .
                            ($consultation->customer?->name ?? '')
                        );
                    @endphp

                    <article
                        x-show="
                            (
                                search === ''
                                || @js($mobileSearchText).includes(
                                    search.toLowerCase()
                                )
                            )
                            &&
                            (
                                statusFilter === 'all'
                                || statusFilter === @js($consultation->status)
                            )
                            &&
                            (
                                paymentFilter === 'all'
                                || paymentFilter === @js($consultation->payment_status)
                            )
                        "
                        x-transition
                        class="p-6 glass-card rounded-3xl"
                    >

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-xs text-slate-500">
                                    {{ $consultation->consultation_number }}
                                </p>

                                <h2 class="mt-2 text-lg font-black text-white">
                                    {{ $consultation->title }}
                                </h2>

                                <p class="mt-2 text-sm text-slate-400">
                                    العميل:
                                    {{ $consultation->customer?->name ?? 'غير معروف' }}
                                </p>

                            </div>

                            <p class="font-black text-cyan-300">
                                {{ number_format(
                                    $consultation->final_price,
                                    2
                                ) }}
                                ₪
                            </p>

                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-5">

                            <div class="p-3 rounded-xl bg-white/5">

                                <p class="text-xs text-slate-500">
                                    المهندس
                                </p>

                                <p class="mt-2 text-sm font-bold">
                                    {{ $consultation->engineer?->name ?? 'غير معيّن' }}
                                </p>

                            </div>

                            <div class="p-3 rounded-xl bg-white/5">

                                <p class="text-xs text-slate-500">
                                    الدفع
                                </p>

                                <p class="mt-2 text-sm font-bold">
                                    {{ $consultation->payment_status }}
                                </p>

                            </div>

                        </div>

                        <div class="grid gap-3 mt-5 sm:grid-cols-2">

                            <a
                                href="{{ route(
                                    'consultations.assign.form',
                                    $consultation
                                ) }}"
                                class="primary-button"
                            >
                                {{ $consultation->engineer
                                    ? 'تغيير المهندس'
                                    : 'تعيين مهندس' }}
                            </a>

                            @if ($consultation->customer_file)

                                <a
                                    href="{{ asset(
                                        'storage/' .
                                        $consultation->customer_file
                                    ) }}"
                                    target="_blank"
                                    class="secondary-button"
                                >
                                    ملف العميل
                                </a>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>

        </div>

    </div>

</x-app-layout>
