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

            <div class="flex flex-col gap-4 mb-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">
                        إدارة فريق المكتب
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        أعضاء {{ $office->name }}
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        تعديل المسمى والتخصص والدور الوظيفي، أو تعطيل عضوية أحد أعضاء المكتب.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('office-membership-applications.index') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                    >
                        طلبات الانضمام
                    </a>

                    <a
                        href="{{ route('office.dashboard') }}"
                        class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                    >
                        لوحة المكتب
                    </a>
                </div>
            </div>

            <div class="grid gap-4 mb-8 sm:grid-cols-2 xl:grid-cols-4">
                <div class="p-5 border rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    <p class="text-sm text-cyan-100">
                        جميع العضويات
                    </p>

                    <p class="mt-2 text-3xl font-black text-cyan-300">
                        {{ $statistics['all'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-green-500/20 bg-green-500/10">
                    <p class="text-sm text-green-100">
                        أعضاء فعالون
                    </p>

                    <p class="mt-2 text-3xl font-black text-green-300">
                        {{ $statistics['active'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-blue-500/20 bg-blue-500/10">
                    <p class="text-sm text-blue-100">
                        المهندسون
                    </p>

                    <p class="mt-2 text-3xl font-black text-blue-300">
                        {{ $statistics['engineers'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-purple-500/20 bg-purple-500/10">
                    <p class="text-sm text-purple-100">
                        المديرون والمالك
                    </p>

                    <p class="mt-2 text-3xl font-black text-purple-300">
                        {{ $statistics['managers'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="space-y-5">
                @forelse ($members as $member)
                    @php
                        $isOwner = $member->office_role === 'owner';

                        $roleData = match ($member->office_role) {
                            'owner' => [
                                'label' => 'مالك المكتب',
                                'class' => 'text-purple-200 border-purple-500/20 bg-purple-500/10',
                            ],

                            'manager' => [
                                'label' => 'مدير المكتب',
                                'class' => 'text-cyan-200 border-cyan-500/20 bg-cyan-500/10',
                            ],

                            'employee' => [
                                'label' => 'موظف',
                                'class' => 'text-slate-200 border-white/10 bg-white/5',
                            ],

                            default => [
                                'label' => 'مهندس',
                                'class' => 'text-blue-200 border-blue-500/20 bg-blue-500/10',
                            ],
                        };

                        $statusData = $member->status === 'active'
                            ? [
                                'label' => 'عضو فعال',
                                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                            ]
                            : [
                                'label' => 'عضوية غير فعالة',
                                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                            ];

                        $canManageManager =
                            $managerMembership->office_role === 'owner';
                    @endphp

                    <div class="p-6 border rounded-3xl border-white/10 bg-slate-900/70">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden text-2xl border rounded-2xl border-white/10 bg-slate-800">
                                    @if ($member->user?->profile_photo)
                                        <img
                                            src="{{ asset('storage/' . $member->user->profile_photo) }}"
                                            alt="{{ $member->user->name }}"
                                            class="object-cover w-full h-full"
                                        >
                                    @else
                                        👤
                                    @endif
                                </div>

                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h2 class="text-xl font-black text-white">
                                            {{ $member->user?->name ?? 'مستخدم غير موجود' }}
                                        </h2>

                                        <span class="px-3 py-1 text-xs font-black border rounded-full {{ $roleData['class'] }}">
                                            {{ $roleData['label'] }}
                                        </span>

                                        <span class="px-3 py-1 text-xs font-black border rounded-full {{ $statusData['class'] }}">
                                            {{ $statusData['label'] }}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm text-slate-400">
                                        {{ $member->user?->email }}
                                    </p>

                                    @if ($member->user?->phone)
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $member->user->phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-sm text-slate-400">
                                تاريخ الانضمام:
                                <span class="font-bold text-white">
                                    {{ $member->joined_at?->format('Y-m-d') ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>

                        @if ($isOwner)
                            <div class="grid gap-4 mt-6 md:grid-cols-3">
                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-slate-400">
                                        المسمى الوظيفي
                                    </p>

                                    <p class="mt-2 font-bold text-white">
                                        {{ $member->position ?: 'مالك المكتب' }}
                                    </p>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-slate-400">
                                        التخصص
                                    </p>

                                    <p class="mt-2 font-bold text-white">
                                        {{ $member->specialty?->name ?: 'غير محدد' }}
                                    </p>
                                </div>

                                <div class="p-4 rounded-2xl bg-white/5">
                                    <p class="text-xs text-slate-400">
                                        الصلاحية
                                    </p>

                                    <p class="mt-2 font-bold text-purple-200">
                                        لا يمكن تعديل مالك المكتب
                                    </p>
                                </div>
                            </div>
                        @else
                            <form
                                method="POST"
                                action="{{ route('office.members.update', $member) }}"
                                class="mt-6"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-bold text-white">
                                            المسمى الوظيفي
                                        </label>

                                        <input
                                            type="text"
                                            name="position"
                                            value="{{ old('position', $member->position) }}"
                                            maxlength="150"
                                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-bold text-white">
                                            التخصص
                                        </label>

                                        <select
                                            name="specialty_id"
                                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        >
                                            <option value="">
                                                بدون تخصص
                                            </option>

                                            @foreach ($specialties as $specialty)
                                                <option
                                                    value="{{ $specialty->id }}"
                                                    @selected(
                                                        (string) old('specialty_id', $member->specialty_id)
                                                        === (string) $specialty->id
                                                    )
                                                >
                                                    {{ $specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-bold text-white">
                                            الدور داخل المكتب
                                        </label>

                                        <select
                                            name="office_role"
                                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        >
                                            @if (
                                                $canManageManager
                                                || $member->office_role !== 'manager'
                                            )
                                                @if ($canManageManager)
                                                    <option
                                                        value="manager"
                                                        @selected(
                                                            old('office_role', $member->office_role)
                                                            === 'manager'
                                                        )
                                                    >
                                                        مدير
                                                    </option>
                                                @endif

                                                <option
                                                    value="engineer"
                                                    @selected(
                                                        old('office_role', $member->office_role)
                                                        === 'engineer'
                                                    )
                                                >
                                                    مهندس
                                                </option>

                                                <option
                                                    value="employee"
                                                    @selected(
                                                        old('office_role', $member->office_role)
                                                        === 'employee'
                                                    )
                                                >
                                                    موظف
                                                </option>
                                            @else
                                                <option value="manager" selected>
                                                    مدير
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-bold text-white">
                                            حالة العضوية
                                        </label>

                                        <select
                                            name="status"
                                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                                        >
                                            <option
                                                value="active"
                                                @selected(
                                                    old('status', $member->status)
                                                    === 'active'
                                                )
                                            >
                                                فعالة
                                            </option>

                                            <option
                                                value="inactive"
                                                @selected(
                                                    old('status', $member->status)
                                                    === 'inactive'
                                                )
                                            >
                                                غير فعالة
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 mt-5 sm:flex-row">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center px-5 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                                    >
                                        حفظ التعديلات
                                    </button>
                                </div>
                            </form>

                            @if (
                                (int) $member->user_id !== (int) auth()->id()
                                && (
                                    $member->office_role !== 'manager'
                                    || $managerMembership->office_role === 'owner'
                                )
                            )
                                <form
                                    method="POST"
                                    action="{{ route('office.members.destroy', $member) }}"
                                    class="mt-4"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        onclick="return confirm('هل تريد إزالة هذا العضو من المكتب؟')"
                                        class="inline-flex items-center justify-center px-5 py-3 font-black text-red-100 transition border rounded-xl border-red-500/20 bg-red-500/10 hover:bg-red-500/20"
                                    >
                                        إزالة العضو من المكتب
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="p-10 text-center border rounded-3xl border-white/10 bg-slate-900/70">
                        <div class="text-6xl">
                            👥
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            لا يوجد أعضاء
                        </h2>

                        <p class="mt-3 text-slate-400">
                            لم تتم إضافة أعضاء إلى المكتب حتى الآن.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($members->hasPages())
                <div class="mt-8">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
