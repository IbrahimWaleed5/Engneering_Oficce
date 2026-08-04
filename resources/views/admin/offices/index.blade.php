<x-app-layout>
    @php
        $search = $search ?? request('search', '');
        $status = $status ?? request('status', '');
        $statistics = $statistics ?? [
            'all' => isset($offices) ? $offices->total() : 0,
            'active' => 0,
            'suspended' => 0,
        ];
    @endphp
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

            @if (session('info'))
                <div class="p-4 mb-6 border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    {{ session('info') }}
                </div>
            @endif

            <div class="flex flex-col gap-5 mb-8 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-bold text-cyan-300">
                        إدارة المكاتب الهندسية
                    </p>

                    <h1 class="mt-2 text-3xl font-black text-white">
                        جميع المكاتب الهندسية
                    </h1>

                    <p class="mt-3 leading-7 text-slate-400">
                        استعرض جميع المكاتب الهندسية، ثم افتح الملف
                        الشخصي للمكتب وقدّم طلب انضمام.
                    </p>
                </div>


            </div>

            <div class="grid gap-4 mb-8 sm:grid-cols-3">
                <div class="p-5 border rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                    <p class="text-sm text-cyan-100">
                        جميع المكاتب الظاهرة
                    </p>

                    <p class="mt-2 text-3xl font-black text-cyan-300">
                        {{ $statistics['all'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-green-500/20 bg-green-500/10">
                    <p class="text-sm text-green-100">
                        مكاتب فعالة
                    </p>

                    <p class="mt-2 text-3xl font-black text-green-300">
                        {{ $statistics['active'] ?? 0 }}
                    </p>
                </div>

                <div class="p-5 border rounded-2xl border-red-500/20 bg-red-500/10">
                    <p class="text-sm text-red-100">
                        مكاتب موقوفة
                    </p>

                    <p class="mt-2 text-3xl font-black text-red-300">
                        {{ $statistics['suspended'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="p-5 mb-8 border rounded-3xl border-white/10 bg-slate-900/70">
                <form
                    method="GET"
                    action="{{ route('admin.offices.index') }}"
                    class="grid gap-4 md:grid-cols-[1fr_220px_auto]"
                >
                    <div>
                        <label
                            for="search"
                            class="block mb-2 text-sm font-bold text-white"
                        >
                            البحث
                        </label>

                        <input
                            id="search"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="اسم المكتب، المدينة، الدولة..."
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block mb-2 text-sm font-bold text-white"
                        >
                            حالة المكتب
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full px-4 py-3 text-white border rounded-xl border-white/10 bg-slate-800"
                        >
                            <option value="">
                                جميع الحالات
                            </option>

                            <option
                                value="active"
                                @selected($status === 'active')
                            >
                                مكاتب فعالة
                            </option>

                            <option
                                value="suspended"
                                @selected($status === 'suspended')
                            >
                                مكاتب موقوفة
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                        >
                            بحث
                        </button>

                        @if ($search !== '' || $status)
                            <a
                                href="{{ route('admin.offices.index') }}"
                                class="inline-flex items-center justify-center px-5 py-3 font-bold text-white transition border rounded-xl border-white/10 bg-white/5 hover:bg-white/10"
                            >
                                مسح
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($offices as $office)
                    @php
                        $isSuspended =
                            $office->status === 'suspended';

                        $isSubscriptionActive =
                            $office->subscription_status === 'active'
                            && $office->subscription_ends_at
                            && $office->subscription_ends_at->isFuture();

                        $officeStatus = $isSuspended
                            ? [
                                'label' => 'مكتب موقوف عن العمل',
                                'class' => 'text-red-200 border-red-500/20 bg-red-500/10',
                            ]
                            : [
                                'label' => 'مكتب فعال',
                                'class' => 'text-green-200 border-green-500/20 bg-green-500/10',
                            ];
                    @endphp

                    <div class="relative overflow-hidden border rounded-3xl border-white/10 bg-slate-900/70">
                        @if ($office->cover_path)
                            <div class="h-40 overflow-hidden bg-slate-800">
                                <img
                                    src="{{ asset('storage/' . $office->cover_path) }}"
                                    alt="{{ $office->name }}"
                                    class="object-cover w-full h-full"
                                >
                            </div>
                        @else
                            <div class="flex items-center justify-center h-40 text-5xl bg-gradient-to-br from-cyan-500/20 to-slate-900">
                                🏢
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden border rounded-2xl border-white/10 bg-slate-800">
                                    @if ($office->logo_path)
                                        <img
                                            src="{{ asset('storage/' . $office->logo_path) }}"
                                            alt="{{ $office->name }}"
                                            class="object-cover w-full h-full"
                                        >
                                    @else
                                        <span class="text-2xl">
                                            🏢
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl font-black text-white truncate">
                                            {{ $office->name }}
                                        </h2>

                                        <span class="px-3 py-1 text-xs font-black border rounded-full text-cyan-200 border-cyan-500/20 bg-cyan-500/10">
                                            مكتب هندسي
                                        </span>
                                    </div>

                                    <p class="mt-2 text-sm text-slate-400">
                                        {{ $office->city ?: 'مدينة غير محددة' }}

                                        @if ($office->country)
                                            —
                                            {{ $office->country }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5">
                                <span class="inline-flex px-3 py-1 text-xs font-black border rounded-full {{ $officeStatus['class'] }}">
                                    {{ $officeStatus['label'] }}
                                </span>
                            </div>

                            @if ($isSuspended)
                                <div class="p-4 mt-4 border rounded-2xl border-red-500/20 bg-red-500/10">
                                    <p class="font-black text-red-200">
                                        مكتب موقوف عن العمل
                                    </p>

                                    <p class="mt-2 text-sm leading-7 text-red-100">
                                        لا يستقبل المكتب طلبات انضمام أو
                                        استشارات جديدة حاليًا.
                                    </p>
                                </div>
                            @elseif (! $isSubscriptionActive)
                                <div class="p-4 mt-4 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                    <p class="font-black text-yellow-200">
                                        اشتراك المكتب غير فعال
                                    </p>

                                    <p class="mt-2 text-sm leading-7 text-yellow-100">
                                        يمكنك مشاهدة الملف الشخصي، لكن لا
                                        يمكن إرسال طلب انضمام حاليًا.
                                    </p>
                                </div>
                            @endif

                            <p class="mt-5 text-sm leading-7 text-slate-400">
                                {{ \Illuminate\Support\Str::limit(
                                    $office->description
                                        ?: 'لا توجد نبذة مضافة عن المكتب حتى الآن.',
                                    150
                                ) }}
                            </p>

                            <div class="grid grid-cols-2 gap-3 mt-6">
                                <div class="p-4 text-center rounded-2xl bg-white/5">
                                    <p class="text-2xl font-black text-white">
                                        {{ $office->active_members_count ?? 0 }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        مهندس في المكتب
                                    </p>
                                </div>

                                <div class="p-4 text-center rounded-2xl bg-white/5">
                                    <p class="text-2xl font-black text-white">
                                        {{ $office->consultations_count ?? 0 }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        استشارة محولة
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route(
                                    'admin.offices.show',
                                    $office
                                ) }}"
                                class="inline-flex items-center justify-center w-full px-5 py-3 mt-6 font-black text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-500"
                            >
                                إدارة المكتب
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center border rounded-3xl border-white/10 bg-slate-900/70 md:col-span-2 xl:col-span-3">
                        <div class="text-6xl">
                            🏢
                        </div>

                        <h2 class="mt-5 text-2xl font-black text-white">
                            لا توجد مكاتب مطابقة
                        </h2>

                        <p class="mt-3 text-slate-400">
                            جرّب تغيير كلمة البحث أو حالة المكتب.
                        </p>
                    </div>
                @endforelse
            </div>

            @if ($offices->hasPages())
                <div class="mt-8">
                    {{ $offices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
