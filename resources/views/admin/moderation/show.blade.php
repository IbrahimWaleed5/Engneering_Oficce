@extends('layouts.app')

@section('title', 'تفاصيل مخالفة المحتوى')

@section('content')
<div class="container py-4" dir="rtl">

    @php
        $user = $warning->user;
        $moderation = $warning->moderation;

        $warningStatusLabels = [
            'active' => 'فعال',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'appealed' => 'اعتراض',
        ];

        $warningStatusClasses = [
            'active' => 'bg-warning text-dark',
            'confirmed' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            'appealed' => 'bg-info text-dark',
        ];

        $accountStatusLabels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended_pending_review' => 'معلق للمراجعة',
            'suspended' => 'معلق',
            'blocked' => 'محظور',
        ];

        $accountStatusClasses = [
            'active' => 'bg-success',
            'inactive' => 'bg-secondary',
            'suspended_pending_review' => 'bg-warning text-dark',
            'suspended' => 'bg-danger',
            'blocked' => 'bg-dark',
        ];

        $decisionLabels = [
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'needs_review' => 'يحتاج مراجعة',
        ];

        $decisionClasses = [
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'needs_review' => 'bg-warning text-dark',
        ];

        $riskLabels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج',
        ];

        $riskClasses = [
            'low' => 'bg-success',
            'medium' => 'bg-warning text-dark',
            'high' => 'bg-danger',
            'critical' => 'bg-dark',
        ];
    @endphp

    <div class="flex-wrap gap-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1 h3">
                تفاصيل التحذير رقم {{ $warning->id }}
            </h1>

            <p class="mb-0 text-muted">
                مراجعة بيانات المستخدم ونتيجة الفحص وقرار النظام الذكي.
            </p>
        </div>

        <a
            href="{{ route('admin.moderation.index') }}"
            class="btn btn-outline-secondary"
        >
            العودة إلى سجل التحذيرات
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-4">

            <div class="mb-4 border-0 shadow-sm card">
                <div class="py-3 bg-white card-header">
                    <h2 class="mb-0 h5">
                        بيانات المستخدم
                    </h2>
                </div>

                <div class="card-body">
                    @if ($user)
                        <div class="mb-4 text-center">
                            <div
                                class="mx-auto mb-3 overflow-hidden border rounded-circle bg-light d-flex align-items-center justify-content-center"
                                style="width: 96px; height: 96px;"
                            >
                                @if ($user->profile_photo)
                                    <img
                                        src="{{ asset('storage/' . $user->profile_photo) }}"
                                        alt="{{ $user->name }}"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                    >
                                @else
                                    <span class="fs-1 fw-bold">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="mb-1 h5">
                                {{ $user->name }}
                            </h3>

                            <div class="text-muted">
                                {{ $user->email }}
                            </div>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    رقم المستخدم
                                </span>

                                <strong>
                                    #{{ $user->id }}
                                </strong>
                            </div>

                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    الهاتف
                                </span>

                                <strong>
                                    {{ $user->phone ?: 'غير متوفر' }}
                                </strong>
                            </div>

                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    الدور
                                </span>

                                <strong>
                                    {{ $user->role }}
                                </strong>
                            </div>

                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    حالة الحساب
                                </span>

                                <span
                                    class="badge {{ $accountStatusClasses[$user->status] ?? 'bg-secondary' }}"
                                >
                                    {{ $accountStatusLabels[$user->status] ?? $user->status }}
                                </span>
                            </div>

                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    عدد التحذيرات
                                </span>

                                <strong class="text-danger">
                                    {{ $user->warnings_count ?? 0 }} / 3
                                </strong>
                            </div>

                            <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                                <span class="text-muted">
                                    تاريخ التعليق
                                </span>

                                <strong>
                                    {{ $user->suspended_at?->format('Y-m-d H:i') ?? '—' }}
                                </strong>
                            </div>
                        </div>

                        @if ($user->suspension_reason)
                            <div class="mt-3 mb-0 alert alert-warning">
                                <strong class="mb-1 d-block">
                                    سبب التعليق
                                </strong>

                                {{ $user->suspension_reason }}
                            </div>
                        @endif
                    @else
                        <div class="py-4 text-center text-muted">
                            هذا المستخدم لم يعد موجودًا.
                        </div>
                    @endif
                </div>
            </div>

            <div class="border-0 shadow-sm card">
                <div class="py-3 bg-white card-header">
                    <h2 class="mb-0 h5">
                        بيانات التحذير
                    </h2>
                </div>

                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                رقم التحذير
                            </span>

                            <span class="badge bg-primary fs-6">
                                {{ $warning->warning_number }} / 3
                            </span>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                الحالة
                            </span>

                            <span
                                class="badge {{ $warningStatusClasses[$warning->status] ?? 'bg-secondary' }}"
                            >
                                {{ $warningStatusLabels[$warning->status] ?? $warning->status }}
                            </span>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                التصنيف
                            </span>

                            <strong>
                                {{ $warning->category ?: 'غير محدد' }}
                            </strong>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                مصدر التحذير
                            </span>

                            <strong>
                                {{ $warning->issued_by_type }}
                            </strong>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                أصدره
                            </span>

                            <strong>
                                {{ $warning->issuer?->name ?? 'النظام الذكي' }}
                            </strong>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                تاريخ الإنشاء
                            </span>

                            <strong>
                                {{ $warning->created_at?->format('Y-m-d H:i') }}
                            </strong>
                        </div>

                        <div class="gap-3 px-0 list-group-item d-flex justify-content-between">
                            <span class="text-muted">
                                تمت المراجعة بواسطة
                            </span>

                            <strong>
                                {{ $warning->reviewer?->name ?? 'لم تتم المراجعة' }}
                            </strong>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="mb-1 text-muted small">
                            سبب التحذير
                        </div>

                        <div class="p-3 border rounded bg-light">
                            {{ $warning->reason }}
                        </div>
                    </div>

                    @if ($warning->review_notes)
                        <div class="mt-3">
                            <div class="mb-1 text-muted small">
                                ملاحظات الإدارة
                            </div>

                            <div class="p-3 border rounded">
                                {{ $warning->review_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">

            <div class="mb-4 border-0 shadow-sm card">
                <div class="py-3 bg-white card-header">
                    <h2 class="mb-0 h5">
                        نتيجة فحص المحتوى
                    </h2>
                </div>

                <div class="card-body">
                    @if ($moderation)
                        <div class="mb-4 row g-3">
                            <div class="col-12 col-md-4">
                                <div class="p-3 border rounded h-100">
                                    <div class="mb-2 text-muted small">
                                        قرار الفحص
                                    </div>

                                    <span
                                        class="badge {{ $decisionClasses[$moderation->decision] ?? 'bg-secondary' }}"
                                    >
                                        {{ $decisionLabels[$moderation->decision] ?? ($moderation->decision ?: 'غير محدد') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="p-3 border rounded h-100">
                                    <div class="mb-2 text-muted small">
                                        مستوى الخطورة
                                    </div>

                                    <span
                                        class="badge {{ $riskClasses[$moderation->risk_level] ?? 'bg-secondary' }}"
                                    >
                                        {{ $riskLabels[$moderation->risk_level] ?? ($moderation->risk_level ?: 'غير محدد') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="p-3 border rounded h-100">
                                    <div class="mb-2 text-muted small">
                                        مقدم الخدمة
                                    </div>

                                    <strong>
                                        {{ $moderation->provider ?: 'غير محدد' }}
                                    </strong>

                                    @if ($moderation->model)
                                        <div class="mt-1 small text-muted">
                                            {{ $moderation->model }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <h3 class="h6">
                                    الملف الذي تم فحصه
                                </h3>

                                <div class="p-3 border rounded">
                                    <div class="mb-2">
                                        <strong>
                                            الاسم الأصلي:
                                        </strong>

                                        {{ $moderation->original_name ?: 'غير متوفر' }}
                                    </div>

                                    <div class="mb-2">
                                        <strong>
                                            نوع الملف:
                                        </strong>

                                        {{ $moderation->mime_type ?: 'غير متوفر' }}
                                    </div>

                                    <div class="mb-2">
                                        <strong>
                                            الحجم:
                                        </strong>

                                        @if ($moderation->file_size)
                                            {{ number_format($moderation->file_size / 1024, 2) }} KB
                                        @else
                                            غير متوفر
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <strong>
                                            مصدر الرفع:
                                        </strong>

                                        {{ $moderation->source_type }}
                                    </div>

                                    <div>
                                        <strong>
                                            المسار:
                                        </strong>

                                        <code>
                                            {{ $moderation->file_path }}
                                        </code>
                                    </div>
                                </div>

                                @if (
                                    $moderation->mime_type &&
                                    str_starts_with(
                                        $moderation->mime_type,
                                        'image/'
                                    )
                                )
                                    <div class="mt-3">
                                        <h3 class="h6">
                                            معاينة الصورة
                                        </h3>

                                        <div class="p-2 text-center border rounded bg-light">
                                            <img
                                                src="{{ asset('storage/' . $moderation->file_path) }}"
                                                alt="المحتوى محل المراجعة"
                                                class="rounded img-fluid"
                                                style="max-height: 500px; object-fit: contain;"
                                            >
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-6">
                                <h3 class="h6">
                                    سبب قرار البوت
                                </h3>

                                <div class="p-3 mb-4 border rounded bg-light">
                                    {{ $moderation->reason ?: 'لم يتم تسجيل سبب.' }}
                                </div>

                                <h3 class="h6">
                                    التصنيفات المكتشفة
                                </h3>

                                <div class="p-3 mb-4 border rounded">
                                    @if (! empty($moderation->detected_categories))
                                        <div class="flex-wrap gap-2 d-flex">
                                            @foreach ($moderation->detected_categories as $category)
                                                <span class="badge bg-danger">
                                                    {{ is_array($category) ? json_encode($category, JSON_UNESCAPED_UNICODE) : $category }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            لا توجد تصنيفات مسجلة.
                                        </span>
                                    @endif
                                </div>

                                <h3 class="h6">
                                    نسب التصنيف
                                </h3>

                                <div class="p-3 border rounded">
                                    @if (! empty($moderation->category_scores))
                                        @foreach ($moderation->category_scores as $key => $score)
                                            <div class="py-2 d-flex justify-content-between border-bottom">
                                                <span>
                                                    {{ $key }}
                                                </span>

                                                <strong>
                                                    @if (is_numeric($score))
                                                        {{ round((float) $score * 100, 2) }}%
                                                    @else
                                                        {{ is_array($score) ? json_encode($score, JSON_UNESCAPED_UNICODE) : $score }}
                                                    @endif
                                                </strong>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">
                                            لا توجد نسب مسجلة.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (! empty($moderation->provider_response))
                            <div class="mt-4">
                                <h3 class="h6">
                                    الرد التقني الكامل
                                </h3>

                                <pre
                                    class="p-3 mb-0 text-white rounded bg-dark"
                                    style="white-space: pre-wrap; max-height: 450px; overflow: auto;"
                                >{{ json_encode(
                                    $moderation->provider_response,
                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                ) }}</pre>
                            </div>
                        @endif
                    @else
                        <div class="mb-0 alert alert-warning">
                            لا توجد نتيجة فحص مرتبطة بهذا التحذير.
                        </div>
                    @endif
                </div>
            </div>

            <div class="border-0 shadow-sm card">
                <div class="py-3 bg-white card-header">
                    <h2 class="mb-0 h5">
                        قرارات الإدارة
                    </h2>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        @if (
                            in_array(
                                $warning->status,
                                ['active', 'appealed'],
                                true
                            )
                        )
                            <div class="col-12 col-lg-6">
                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation.confirm', $warning) }}"
                                    class="p-3 border rounded h-100"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="h6 text-success">
                                        تأكيد التحذير
                                    </h3>

                                    <p class="small text-muted">
                                        يثبت هذا القرار أن المخالفة صحيحة.
                                    </p>

                                    <textarea
                                        name="review_notes"
                                        class="mb-3 form-control"
                                        rows="4"
                                        maxlength="3000"
                                        placeholder="ملاحظات المدير"
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-success w-100"
                                    >
                                        تأكيد التحذير
                                    </button>
                                </form>
                            </div>

                            <div class="col-12 col-lg-6">
                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation.cancel', $warning) }}"
                                    class="p-3 border rounded h-100"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="h6 text-danger">
                                        إلغاء التحذير
                                    </h3>

                                    <p class="small text-muted">
                                        استخدم هذا الخيار عندما يكون قرار البوت خاطئًا.
                                    </p>

                                    <textarea
                                        name="review_notes"
                                        class="mb-3 form-control"
                                        rows="4"
                                        maxlength="3000"
                                        required
                                        placeholder="سبب إلغاء التحذير"
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger w-100"
                                    >
                                        إلغاء التحذير
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if (
                            $user &&
                            $user->status === 'suspended_pending_review'
                        )
                            <div class="col-12 col-lg-6">
                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation.reactivate', $user) }}"
                                    class="p-3 border rounded h-100"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="h6 text-success">
                                        إعادة تفعيل الحساب
                                    </h3>

                                    <p class="small text-muted">
                                        يعاد فتح الحساب بعد مراجعة المدير.
                                    </p>

                                    <textarea
                                        name="review_notes"
                                        class="mb-3 form-control"
                                        rows="4"
                                        maxlength="3000"
                                        required
                                        placeholder="ملاحظات قرار إعادة التفعيل"
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-success w-100"
                                    >
                                        إعادة تفعيل الحساب
                                    </button>
                                </form>
                            </div>

                            <div class="col-12 col-lg-6">
                                <form
                                    method="POST"
                                    action="{{ route('admin.moderation.keep-suspended', $user) }}"
                                    class="p-3 border rounded h-100"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <h3 class="h6 text-danger">
                                        تثبيت تعليق الحساب
                                    </h3>

                                    <p class="small text-muted">
                                        يبقى المستخدم ممنوعًا من استخدام المنصة.
                                    </p>

                                    <textarea
                                        name="review_notes"
                                        class="mb-3 form-control"
                                        rows="4"
                                        maxlength="3000"
                                        required
                                        placeholder="سبب تثبيت تعليق الحساب"
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-danger w-100"
                                    >
                                        تثبيت التعليق
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if (
                            ! in_array(
                                $warning->status,
                                ['active', 'appealed'],
                                true
                            ) &&
                            (
                                ! $user ||
                                $user->status !== 'suspended_pending_review'
                            )
                        )
                            <div class="col-12">
                                <div class="mb-0 alert alert-info">
                                    تمت مراجعة هذه الحالة ولا توجد إجراءات متاحة حاليًا.
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
