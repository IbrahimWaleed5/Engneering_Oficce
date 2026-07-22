<x-app-layout>

    @auth

        @php
            $currentUser = auth()->user();

            $isActiveEngineer =
                $currentUser->role === 'engineer'
                && $currentUser->hasActiveEngineerMembership();

            $isInactiveEngineer =
                $currentUser->role === 'engineer'
                && ! $currentUser->hasActiveEngineerMembership();

            $actsAsCustomer =
                $currentUser->role === 'customer'
                || $isInactiveEngineer;
        @endphp

        <div
            class="py-10"
            dir="rtl"
        >

            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                {{-- بطاقة الترحيب --}}
                <div
                    class="relative p-8 mb-8 overflow-hidden border shadow-xl rounded-2xl bg-gradient-to-l from-blue-700 to-cyan-600 border-blue-500/30"
                >

                    <div
                        class="absolute w-48 h-48 rounded-full -top-20 -left-10 bg-white/10"
                    ></div>

                    <div class="relative">

                        <p class="mb-2 text-blue-100">
                            أهلًا بك
                        </p>

                        <h1 class="mb-3 text-3xl font-bold text-white">
                            {{ $currentUser->name }}
                        </h1>

                        <p class="text-blue-100">
                            يمكنك إدارة حسابك وطلباتك من لوحة التحكم.
                        </p>

                    </div>

                </div>

                {{-- حالة اشتراك المهندس --}}
                @if ($currentUser->role === 'engineer')

                    <div
                        class="p-5 mb-6 border rounded-2xl
                        {{ $isActiveEngineer
                            ? 'border-green-500/30 bg-green-500/10'
                            : 'border-orange-500/30 bg-orange-500/10' }}"
                    >

                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex items-center justify-center flex-none w-12 h-12 text-2xl rounded-xl
                                    {{ $isActiveEngineer
                                        ? 'bg-green-500/20'
                                        : 'bg-orange-500/20' }}"
                                >
                                    {{ $isActiveEngineer ? '✅' : '⚠️' }}
                                </div>

                                <div>

                                    <h2
                                        class="text-xl font-black
                                        {{ $isActiveEngineer
                                            ? 'text-green-200'
                                            : 'text-orange-200' }}"
                                    >
                                        {{ $isActiveEngineer
                                            ? 'مهندس نشط'
                                            : 'مهندس غير نشط' }}
                                    </h2>

                                    <p class="mt-2 text-sm leading-7 text-slate-300">

                                        @if ($isActiveEngineer)

                                            اشتراك المهندس فعال حتى:

                                            <span class="font-black text-white">
                                                {{ $currentUser
                                                    ->engineer_active_until
                                                    ?->format('Y-m-d H:i') }}
                                            </span>

                                        @else

                                            انتهت مدة اشتراك المهندس.
                                            يمكنك استخدام الموقع كعميل ورفع
                                            إيصال دفع جديد لإعادة جميع صلاحياتك.

                                        @endif

                                    </p>

                                </div>

                            </div>

                            <a
                                href="{{ route('engineer-applications.create') }}"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 font-black text-white transition bg-purple-600 rounded-xl hover:bg-purple-500"
                            >
                                <span>💳</span>

                                {{ $isActiveEngineer
                                    ? 'تجديد الاشتراك'
                                    : 'إعادة تفعيل الحساب' }}
                            </a>

                        </div>

                    </div>

                @endif

                {{-- رسالة النجاح --}}
                @if (session('success'))

                    <div
                        class="p-4 mb-6 text-green-200 border border-green-700 rounded-xl bg-green-900/30"
                    >
                        {{ session('success') }}
                    </div>

                @endif

                {{-- رسالة الخطأ --}}
                @if (session('error'))

                    <div
                        class="p-4 mb-6 text-red-200 border border-red-700 rounded-xl bg-red-900/30"
                    >
                        {{ session('error') }}
                    </div>

                @endif

                {{-- أخطاء التحقق --}}
                @if ($errors->any())

                    <div
                        class="p-4 mb-6 text-red-200 border border-red-700 rounded-xl bg-red-900/30"
                    >

                        <ul class="space-y-2">

                            @foreach ($errors->all() as $error)

                                <li>
                                    • {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                {{-- =========================
                    لوحة العميل أو المهندس غير النشط
                ========================== --}}
                @if ($actsAsCustomer)

                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                    >

                        {{-- طلب استشارة --}}
                        <a
                            href="{{ route('consultations.create') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-blue-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-blue-600/20"
                            >
                                📝
                            </div>

                            <h2 class="mb-2 text-xl font-bold text-white">
                                طلب استشارة
                            </h2>

                            <p class="text-sm leading-7 text-slate-400">
                                أرسل تفاصيل مشروعك واختر نوع الخدمة.
                            </p>

                        </a>

                        {{-- استشارات العميل --}}
                        <a
                            href="{{ route('consultations.mine') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-cyan-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-cyan-600/20"
                            >
                                📋
                            </div>

                            <h2 class="mb-2 text-xl font-bold text-white">
                                استشاراتي
                            </h2>

                            <p class="text-sm leading-7 text-slate-400">
                                تابع الدفع والحالة والملفات النهائية.
                            </p>

                        </a>

                        {{-- اختيار مهندس --}}
                        <a
                            href="{{ route('engineer.works.public') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-emerald-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-emerald-600/20"
                            >
                                🏗️
                            </div>

                            <h2 class="mb-2 text-xl font-bold text-white">
                                اختر مهندسًا
                            </h2>

                            <p class="text-sm leading-7 text-slate-400">
                                تصفح أعمال المهندسين النشطين وأرسل طلبك
                                لمهندس محدد.
                            </p>

                        </a>

                        {{-- طلب الانضمام أو التجديد --}}
                        <a
                            href="{{ route('engineer-applications.create') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-purple-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-purple-600/20"
                            >
                                {{ $isInactiveEngineer ? '💳' : '👷' }}
                            </div>

                            <h2 class="mb-2 text-xl font-bold text-white">

                                {{ $isInactiveEngineer
                                    ? 'تجديد اشتراك المهندس'
                                    : 'طلب التوظيف كمهندس' }}

                            </h2>

                            <p class="text-sm leading-7 text-slate-400">

                                {{ $isInactiveEngineer
                                    ? 'ارفع إيصال دفع جديد لإعادة تفعيل جميع صلاحيات المهندس مع الاحتفاظ ببياناتك وأعمالك.'
                                    : 'ارفع الشهادة والسيرة الذاتية وإيصال الدفع لتقديم طلب الانضمام.' }}

                            </p>

                        </a>

                    </div>

                {{-- =========================
                    لوحة المهندس النشط
                ========================== --}}
                @elseif ($isActiveEngineer)

                    <div
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
                    >

                        {{-- الاستشارات المسندة --}}
                        <a
                            href="{{ route('engineer.consultations') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-blue-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-blue-600/20"
                            >
                                📐
                            </div>

                            <h2 class="text-xl font-bold text-white">
                                الاستشارات المسندة إلي
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                تابع طلبات العملاء وارفع الملف النهائي.
                            </p>

                        </a>

                        {{-- أعمال المهندس --}}
                        <a
                            href="{{ route('engineer.works.mine') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-cyan-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-cyan-600/20"
                            >
                                🖼️
                            </div>

                            <h2 class="text-xl font-bold text-white">
                                أعمالي وإنجازاتي
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                تابع مشاريعك وصور الإنجازات السابقة.
                            </p>

                        </a>

                        {{-- إضافة عمل --}}
                        <a
                            href="{{ route('engineer.works.create') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-emerald-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-emerald-600/20"
                            >
                                ➕
                            </div>

                            <h2 class="text-xl font-bold text-white">
                                إضافة عمل جديد
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                ارفع صور عمل جديد للمراجعة والنشر.
                            </p>

                        </a>

                        {{-- طلب استشارة كعميل --}}
                        <a
                            href="{{ route('consultations.create') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-orange-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-orange-600/20"
                            >
                                📝
                            </div>

                            <h2 class="text-xl font-bold text-white">
                                طلب استشارة
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                اطلب استشارة من مهندس آخر.
                            </p>

                        </a>

                        {{-- استشاراته كعميل --}}
                        <a
                            href="{{ route('consultations.mine') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-purple-500"
                        >

                            <div
                                class="flex items-center justify-center w-12 h-12 mb-5 text-2xl rounded-xl bg-purple-600/20"
                            >
                                📋
                            </div>

                            <h2 class="text-xl font-bold text-white">
                                استشاراتي كعميل
                            </h2>

                            <p class="mt-2 text-sm leading-7 text-slate-400">
                                تابع الاستشارات التي طلبتها من مهندسين آخرين.
                            </p>

                        </a>

                    </div>

                {{-- =========================
                    لوحة المدير
                ========================== --}}
                @elseif ($currentUser->role === 'admin')

                    <div
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
                    >

                        {{-- جميع الاستشارات --}}
                        <a
                            href="{{ route('consultations.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-blue-500"
                        >

                            <div class="mb-3 text-3xl">
                                📄
                            </div>

                            <h2 class="font-bold text-white">
                                جميع الاستشارات
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                متابعة جميع الطلبات والاستشارات.
                            </p>

                        </a>

                        {{-- مراجعة الدفعات --}}
                        <a
                            href="{{ route('payments.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-green-500"
                        >

                            <div class="mb-3 text-3xl">
                                💳
                            </div>

                            <h2 class="font-bold text-white">
                                مراجعة الدفعات
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                قبول أو رفض إيصالات دفع الاستشارات.
                            </p>

                        </a>

                        {{-- الموظفون --}}
                        <a
                            href="{{ route('employees.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-cyan-500"
                        >

                            <div class="mb-3 text-3xl">
                                👷
                            </div>

                            <h2 class="font-bold text-white">
                                الموظفون
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                إدارة بيانات الموظفين والمهندسين.
                            </p>

                        </a>

                        {{-- إدارة المستخدمين --}}
                        <a
                            href="{{ route('users.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-emerald-500"
                        >

                            <div class="mb-3 text-3xl">
                                ⚙️
                            </div>

                            <h2 class="font-bold text-white">
                                إدارة المستخدمين
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                تعديل حسابات المستخدمين وأدوارهم.
                            </p>

                        </a>

                        {{-- أعمال المهندسين --}}
                        <a
                            href="{{ route('admin.engineer-works.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-purple-500"
                        >

                            <div class="mb-3 text-3xl">
                                🏗️
                            </div>

                            <h2 class="font-bold text-white">
                                أعمال المهندسين
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                مراجعة الأعمال والموافقة على نشرها.
                            </p>

                        </a>

                        {{-- طلبات المهندسين --}}
                        <a
                            href="{{ route('engineer-applications.index') }}"
                            class="p-6 transition border shadow rounded-2xl bg-slate-900 border-slate-800 hover:-translate-y-1 hover:border-orange-500"
                        >

                            <div class="mb-3 text-3xl">
                                🧑‍💼
                            </div>

                            <h2 class="font-bold text-white">
                                طلبات واشتراكات المهندسين
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                مراجعة طلبات الانضمام والتجديد وتحديد مدة التفعيل.
                            </p>

                        </a>

                    </div>

                {{-- =========================
                    لوحة الموظف
                ========================== --}}
                @else

                    <div
                        class="p-6 border rounded-2xl bg-slate-900 border-slate-800"
                    >

                        <h2 class="text-xl font-bold text-white">
                            حساب موظف
                        </h2>

                        <p class="mt-2 text-slate-400">
                            يمكنك متابعة الاستشارات والطلبات حسب صلاحياتك.
                        </p>

                        <a
                            href="{{ route('consultations.index') }}"
                            class="inline-flex items-center px-5 py-3 mt-5 font-bold text-white transition bg-blue-600 rounded-lg hover:bg-blue-500"
                        >
                            عرض الاستشارات
                        </a>

                    </div>

                @endif

            </div>

        </div>

    @endauth

</x-app-layout>
