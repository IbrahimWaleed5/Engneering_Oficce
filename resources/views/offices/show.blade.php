<x-app-layout>
    <div class="min-h-screen bg-[#0b1326] py-10 text-[#dae2fd]" dir="rtl">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

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

            @php
                $isSuspended = $office->status === 'suspended';

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

            <div class="overflow-hidden border rounded-3xl border-[#424754]/60 bg-[#131b2e]/90">
                <div class="relative h-56 overflow-hidden sm:h-72">
                    @if ($office->cover_path)
                        <img
                            src="{{ asset('storage/' . $office->cover_path) }}"
                            alt="{{ $office->name }}"
                            class="object-cover w-full h-full"
                        >
                    @else
                        <div class="flex items-center justify-center w-full h-full text-7xl bg-gradient-to-br from-cyan-500/20 via-slate-900 to-slate-950">
                            🏢
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
                </div>

                <div class="relative px-6 pb-8 -mt-16 sm:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                            <div class="flex items-center justify-center flex-shrink-0 w-32 h-32 overflow-hidden text-5xl border-4 rounded-3xl border-slate-900 bg-[#222a3d]">
                                @if ($office->logo_path)
                                    <img
                                        src="{{ asset('storage/' . $office->logo_path) }}"
                                        alt="{{ $office->name }}"
                                        class="object-cover w-full h-full"
                                    >
                                @else
                                    🏢
                                @endif
                            </div>

                            <div class="pb-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1 class="text-3xl font-black text-white sm:text-4xl">
                                        {{ $office->name }}
                                    </h1>

                                    <span class="px-3 py-1 text-xs font-black border rounded-full text-[#adc6ff] border-[#4d8eff]/30 bg-[#4d8eff]/10">
                                        مكتب هندسي
                                    </span>
                                </div>

                                <p class="mt-3 text-[#c2c6d6]">
                                    {{ $office->city ?: 'مدينة غير محددة' }}

                                    @if ($office->country)
                                        —
                                        {{ $office->country }}
                                    @endif
                                </p>

                                <div class="flex flex-wrap gap-3 mt-4">
                                    <span class="inline-flex px-4 py-2 text-sm font-black border rounded-full {{ $officeStatus['class'] }}">
                                        {{ $officeStatus['label'] }}
                                    </span>

                                    @if ($isSubscriptionActive)
                                        <span class="inline-flex px-4 py-2 text-sm font-black text-green-200 border rounded-full border-green-500/20 bg-green-500/10">
                                            اشتراك فعال
                                        </span>
                                    @else
                                        <span class="inline-flex px-4 py-2 text-sm font-black text-yellow-200 border rounded-full border-yellow-500/20 bg-yellow-500/10">
                                            اشتراك غير فعال
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pb-2">
                            @guest
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 font-black text-white transition rounded-xl bg-[#4d8eff] text-[#00285d] hover:brightness-110"
                                >
                                    تسجيل الدخول لطلب الانضمام
                                </a>
                            @else
                                @if (auth()->user()->role === 'engineer')
                                    @if (
                                        $membership
                                        && $membership->status === 'active'
                                    )
                                        <div class="px-5 py-3 font-black text-green-100 border rounded-2xl border-green-500/20 bg-green-500/10">
                                            أنت عضو فعال في هذا المكتب
                                        </div>
                                    @elseif ($pendingApplication)
                                        <div class="px-5 py-3 font-black text-yellow-100 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                            طلب انضمامك قيد المراجعة
                                        </div>
                                    @elseif ($canApply)
                                        <a
                                            href="{{ route(
                                                'office-membership-applications.create',
                                                $office
                                            ) }}"
                                            class="inline-flex items-center justify-center px-6 py-3 font-black text-white transition rounded-xl bg-[#4d8eff] text-[#00285d] hover:brightness-110"
                                        >
                                            طلب الانضمام إلى المكتب
                                        </a>
                                    @elseif ($office->status === 'suspended')
                                        <div class="px-5 py-3 font-black text-red-200 border rounded-2xl border-red-500/20 bg-red-500/10">
                                            المكتب موقوف ولا يستقبل طلبات انضمام
                                        </div>
                                    @elseif (! $isSubscriptionActive)
                                        <div class="px-5 py-3 font-black text-yellow-200 border rounded-2xl border-yellow-500/20 bg-yellow-500/10">
                                            اشتراك المكتب غير فعال حاليًا
                                        </div>
                                    @else
                                        <div class="px-5 py-3 font-black border text-[#c2c6d6] rounded-2xl border-[#424754]/60 bg-[#171f33]">
                                            طلب الانضمام غير متاح حاليًا
                                        </div>
                                    @endif
                                @else
                                    <div class="px-5 py-3 font-black border text-cyan-100 rounded-2xl border-cyan-500/20 bg-cyan-500/10">
                                        هذه الصفحة متاحة للاطلاع، وطلبات الانضمام مخصصة للمهندسين
                                    </div>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            </div>

            @if ($isSuspended)
                <div class="p-6 mt-8 border rounded-3xl border-red-500/30 bg-red-500/10">
                    <h2 class="text-xl font-black text-red-200">
                        مكتب موقوف عن العمل
                    </h2>

                    <p class="mt-3 leading-8 text-red-100">
                        أوقف مدير النظام هذا المكتب مؤقتًا. لا يستطيع
                        المكتب استقبال استشارات أو طلبات انضمام جديدة.
                    </p>

                    @if ($office->suspension_reason)
                        <div class="p-4 mt-4 border rounded-2xl border-red-500/20 bg-red-950/20">
                            <p class="text-sm font-black text-red-200">
                                سبب الإيقاف
                            </p>

                            <p class="mt-2 leading-7 text-red-100">
                                {{ $office->suspension_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            @elseif (! $isSubscriptionActive)
                <div class="p-6 mt-8 border rounded-3xl border-yellow-500/20 bg-yellow-500/10">
                    <h2 class="text-xl font-black text-yellow-200">
                        اشتراك المكتب غير فعال
                    </h2>

                    <p class="mt-3 leading-8 text-yellow-100">
                        يمكن مشاهدة الملف الشخصي للمكتب، لكن تقديم طلب
                        الانضمام غير متاح حتى يتم تفعيل الاشتراك.
                    </p>
                </div>
            @endif

            <div class="grid gap-6 mt-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="p-6 border rounded-3xl border-[#424754]/60 bg-[#131b2e]/90 sm:p-8">
                        <h2 class="text-2xl font-black text-white">
                            نبذة عن المكتب
                        </h2>

                        <p class="mt-5 leading-9 text-[#c2c6d6]">
                            {{ $office->description
                                ?: 'لم يضف المكتب نبذة تعريفية حتى الآن.' }}
                        </p>
                    </div>

                    <div class="p-6 border rounded-3xl border-[#424754]/60 bg-[#131b2e]/90 sm:p-8">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-black text-white">
                                    فريق المكتب
                                </h2>

                                <p class="mt-2 text-[#8c909f]">
                                    يظهر هنا المهندسون المقبولون والفعالون داخل المكتب.
                                </p>
                            </div>

                            <span class="px-4 py-2 text-sm font-black border rounded-full text-[#adc6ff] border-[#4d8eff]/30 bg-[#4d8eff]/10">
                                {{ $office->active_members_count ?? 0 }}
                                عضو
                            </span>
                        </div>

                        <div class="grid gap-4 mt-7 sm:grid-cols-2">
                            @forelse ($office->activeMembers as $member)
                                <a
                                    href="{{ route(
                                        'engineers.show',
                                        $member->user
                                    ) }}"
                                    class="flex items-center gap-4 p-4 transition border rounded-2xl border-[#424754]/60 bg-[#171f33] hover:bg-[#222a3d]"
                                >
                                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 overflow-hidden text-xl border rounded-2xl border-[#424754]/60 bg-[#222a3d]">
                                        @if ($member->user?->profile_photo)
                                            <img
                                                src="{{ asset(
                                                    'storage/'
                                                    . $member->user->profile_photo
                                                ) }}"
                                                alt="{{ $member->user->name }}"
                                                class="object-cover w-full h-full"
                                            >
                                        @else
                                            👤
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-white truncate">
                                            {{ $member->user?->name
                                                ?? 'مهندس غير متاح' }}
                                        </p>

                                        <p class="mt-1 text-sm text-[#8c909f]">
                                            {{ $member->position
                                                ?: 'مهندس' }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#adc6ff]">
                                            {{ $member->specialty?->name
                                                ?: 'تخصص غير محدد' }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center border rounded-2xl border-[#424754]/60 bg-[#171f33] sm:col-span-2">
                                    <p class="text-[#8c909f]">
                                        لا يوجد مهندسون ظاهرون في فريق المكتب حاليًا.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="p-6 border rounded-3xl border-[#424754]/60 bg-[#131b2e]/90">
                        <h2 class="text-xl font-black text-white">
                            معلومات المكتب
                        </h2>

                        <div class="mt-6 space-y-4">
                            <div class="p-4 rounded-2xl bg-[#171f33]">
                                <p class="text-xs text-[#8c909f]">
                                    البريد الإلكتروني
                                </p>

                                <p class="mt-2 font-bold text-white break-all">
                                    {{ $office->email }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#171f33]">
                                <p class="text-xs text-[#8c909f]">
                                    رقم الهاتف
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $office->phone ?: 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#171f33]">
                                <p class="text-xs text-[#8c909f]">
                                    العنوان
                                </p>

                                <p class="mt-2 leading-7 text-white">
                                    {{ $office->address ?: 'غير محدد' }}
                                </p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#171f33]">
                                <p class="text-xs text-[#8c909f]">
                                    رقم الترخيص
                                </p>

                                <p class="mt-2 font-bold text-white">
                                    {{ $office->license_number
                                        ?: 'غير محدد' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-5 text-center border rounded-2xl border-[#424754]/60 bg-[#131b2e]/90">
                            <p class="text-3xl font-black text-white">
                                {{ $office->active_members_count ?? 0 }}
                            </p>

                            <p class="mt-2 text-xs text-[#8c909f]">
                                أعضاء المكتب
                            </p>
                        </div>

                        <div class="p-5 text-center border rounded-2xl border-[#424754]/60 bg-[#131b2e]/90">
                            <p class="text-3xl font-black text-white">
                                {{ $office->consultations_count ?? 0 }}
                            </p>

                            <p class="mt-2 text-xs text-[#8c909f]">
                                استشارات محولة
                            </p>
                        </div>
                    </div>

                    @if ($latestApplication)
                        <div class="p-5 border rounded-3xl border-[#424754]/60 bg-[#131b2e]/90">
                            <p class="text-sm text-[#8c909f]">
                                آخر طلب انضمام لك
                            </p>

                            <p class="mt-3 font-black text-white">
                                {{ match ($latestApplication->status) {
                                    'approved' => 'تم قبول الطلب',
                                    'rejected' => 'تم رفض الطلب',
                                    'cancelled' => 'تم إلغاء الطلب',
                                    default => 'قيد المراجعة',
                                } }}
                            </p>

                            @if (
                                $latestApplication->status === 'rejected'
                                && $latestApplication->rejection_reason
                            )
                                <div class="p-4 mt-4 border rounded-2xl border-red-500/20 bg-red-500/10">
                                    <p class="text-xs font-black text-red-200">
                                        سبب الرفض
                                    </p>

                                    <p class="mt-2 text-sm leading-7 text-red-100">
                                        {{ $latestApplication->rejection_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <a
                        href="{{ route('engineering-offices.index') }}"
                        class="inline-flex items-center justify-center w-full px-5 py-3 font-bold text-white transition border rounded-xl border-[#424754]/60 bg-[#171f33] hover:bg-[#222a3d]"
                    >
                        العودة إلى جميع المكاتب
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
