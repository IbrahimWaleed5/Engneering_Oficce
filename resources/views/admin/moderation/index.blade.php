@extends('layouts.app')

@section('title', 'مراجعة مخالفات المحتوى')

@section('content')
<div class="container py-4" dir="rtl">

    <div class="flex-wrap gap-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1 h3">
                مراجعة مخالفات المحتوى
            </h1>

            <p class="mb-0 text-muted">
                متابعة تحذيرات المستخدمين ونتائج فحص المحتوى الصادرة من النظام الذكي.
            </p>
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="btn btn-outline-secondary"
        >
            العودة إلى لوحة التحكم
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

    <div class="mb-4 row g-3">
        <div class="col-12 col-md-6 col-xl">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="mb-2 text-muted small">
                        جميع التحذيرات
                    </div>

                    <div class="fs-3 fw-bold">
                        {{ number_format($statistics['all_warnings']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="mb-2 text-muted small">
                        التحذيرات الفعالة
                    </div>

                    <div class="fs-3 fw-bold text-warning">
                        {{ number_format($statistics['active_warnings']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="mb-2 text-muted small">
                        بانتظار المراجعة
                    </div>

                    <div class="fs-3 fw-bold text-info">
                        {{ number_format($statistics['pending_reviews']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="mb-2 text-muted small">
                        محتوى مرفوض
                    </div>

                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format($statistics['rejected_content']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl">
            <div class="border-0 shadow-sm card h-100">
                <div class="card-body">
                    <div class="mb-2 text-muted small">
                        حسابات معلقة للمراجعة
                    </div>

                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format($statistics['suspended_accounts']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 border-0 shadow-sm card">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('admin.moderation.index') }}"
                class="row g-3 align-items-end"
            >
                <div class="col-12 col-lg-4">
                    <label
                        for="search"
                        class="form-label"
                    >
                        البحث
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="الاسم أو البريد أو الهاتف أو سبب التحذير"
                    >
                </div>

                <div class="col-12 col-md-4 col-lg-2">
                    <label
                        for="status"
                        class="form-label"
                    >
                        حالة التحذير
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select"
                    >
                        <option value="">
                            الكل
                        </option>

                        <option
                            value="active"
                            @selected(($filters['status'] ?? '') === 'active')
                        >
                            فعال
                        </option>

                        <option
                            value="confirmed"
                            @selected(($filters['status'] ?? '') === 'confirmed')
                        >
                            مؤكد
                        </option>

                        <option
                            value="cancelled"
                            @selected(($filters['status'] ?? '') === 'cancelled')
                        >
                            ملغي
                        </option>

                        <option
                            value="appealed"
                            @selected(($filters['status'] ?? '') === 'appealed')
                        >
                            اعتراض
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-2">
                    <label
                        for="account_status"
                        class="form-label"
                    >
                        حالة الحساب
                    </label>

                    <select
                        id="account_status"
                        name="account_status"
                        class="form-select"
                    >
                        <option value="">
                            الكل
                        </option>

                        <option
                            value="active"
                            @selected(($filters['account_status'] ?? '') === 'active')
                        >
                            نشط
                        </option>

                        <option
                            value="inactive"
                            @selected(($filters['account_status'] ?? '') === 'inactive')
                        >
                            غير نشط
                        </option>

                        <option
                            value="suspended_pending_review"
                            @selected(($filters['account_status'] ?? '') === 'suspended_pending_review')
                        >
                            معلق للمراجعة
                        </option>

                        <option
                            value="suspended"
                            @selected(($filters['account_status'] ?? '') === 'suspended')
                        >
                            معلق
                        </option>

                        <option
                            value="blocked"
                            @selected(($filters['account_status'] ?? '') === 'blocked')
                        >
                            محظور
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-4 col-lg-2">
                    <label
                        for="category"
                        class="form-label"
                    >
                        نوع المخالفة
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="form-select"
                    >
                        <option value="">
                            الكل
                        </option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category }}"
                                @selected(($filters['category'] ?? '') === $category)
                            >
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="gap-2 col-12 col-lg-2 d-grid">
                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        تطبيق الفلاتر
                    </button>

                    <a
                        href="{{ route('admin.moderation.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        مسح الفلاتر
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="border-0 shadow-sm card">
        <div class="py-3 bg-white card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0 h5">
                    سجل التحذيرات
                </h2>

                <span class="badge bg-secondary">
                    {{ $warnings->total() }} نتيجة
                </span>
            </div>
        </div>

        <div class="p-0 card-body">
            @if ($warnings->isEmpty())
                <div class="py-5 text-center">
                    <div class="mb-3 fs-1">
                        ✅
                    </div>

                    <h3 class="h5">
                        لا توجد تحذيرات
                    </h3>

                    <p class="mb-0 text-muted">
                        لم يتم تسجيل أي مخالفة مطابقة للفلاتر الحالية.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    المستخدم
                                </th>

                                <th>
                                    المخالفة
                                </th>

                                <th>
                                    رقم التحذير
                                </th>

                                <th>
                                    حالة التحذير
                                </th>

                                <th>
                                    حالة الحساب
                                </th>

                                <th>
                                    المصدر
                                </th>

                                <th>
                                    التاريخ
                                </th>

                                <th class="text-center">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($warnings as $warning)
                                @php
                                    $user = $warning->user;
                                    $moderation = $warning->moderation;

                                    $warningStatusClasses = [
                                        'active' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-danger',
                                        'cancelled' => 'bg-secondary',
                                        'appealed' => 'bg-info text-dark',
                                    ];

                                    $accountStatusClasses = [
                                        'active' => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'suspended_pending_review' => 'bg-warning text-dark',
                                        'suspended' => 'bg-danger',
                                        'blocked' => 'bg-dark',
                                    ];

                                    $warningStatusLabels = [
                                        'active' => 'فعال',
                                        'confirmed' => 'مؤكد',
                                        'cancelled' => 'ملغي',
                                        'appealed' => 'اعتراض',
                                    ];

                                    $accountStatusLabels = [
                                        'active' => 'نشط',
                                        'inactive' => 'غير نشط',
                                        'suspended_pending_review' => 'معلق للمراجعة',
                                        'suspended' => 'معلق',
                                        'blocked' => 'محظور',
                                    ];
                                @endphp

                                <tr>
                                    <td>
                                        @if ($user)
                                            <div class="gap-3 d-flex align-items-center">
                                                <div
                                                    class="overflow-hidden border rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 44px; height: 44px; min-width: 44px;"
                                                >
                                                    @if ($user->profile_photo)
                                                        <img
                                                            src="{{ asset('storage/' . $user->profile_photo) }}"
                                                            alt="{{ $user->name }}"
                                                            class="w-100 h-100"
                                                            style="object-fit: cover;"
                                                        >
                                                    @else
                                                        <span class="fw-bold">
                                                            {{ mb_substr($user->name, 0, 1) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div>
                                                    <div class="fw-bold">
                                                        {{ $user->name }}
                                                    </div>

                                                    <div class="small text-muted">
                                                        {{ $user->email }}
                                                    </div>

                                                    @if ($user->phone)
                                                        <div class="small text-muted">
                                                            {{ $user->phone }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                مستخدم محذوف
                                            </span>
                                        @endif
                                    </td>

                                    <td style="min-width: 250px;">
                                        <div class="mb-1 fw-semibold">
                                            {{ $warning->category ?: 'غير محدد' }}
                                        </div>

                                        <div class="small text-muted">
                                            {{ \Illuminate\Support\Str::limit($warning->reason, 120) }}
                                        </div>

                                        @if ($moderation?->risk_level)
                                            <div class="mt-2">
                                                <span class="border badge bg-light text-dark">
                                                    الخطورة:
                                                    {{ $moderation->risk_level }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            {{ $warning->warning_number }} / 3
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="badge {{ $warningStatusClasses[$warning->status] ?? 'bg-secondary' }}"
                                        >
                                            {{ $warningStatusLabels[$warning->status] ?? $warning->status }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($user)
                                            <span
                                                class="badge {{ $accountStatusClasses[$user->status] ?? 'bg-secondary' }}"
                                            >
                                                {{ $accountStatusLabels[$user->status] ?? $user->status }}
                                            </span>

                                            <div class="mt-1 small text-muted">
                                                {{ $user->warnings_count }} تحذير
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                —
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="border badge bg-light text-dark">
                                            {{ $warning->issued_by_type }}
                                        </span>

                                        @if ($warning->issuer)
                                            <div class="mt-1 small text-muted">
                                                {{ $warning->issuer->name }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div>
                                            {{ $warning->created_at?->format('Y-m-d') }}
                                        </div>

                                        <div class="small text-muted">
                                            {{ $warning->created_at?->format('H:i') }}
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="flex-wrap gap-2 d-flex justify-content-center">
                                            <a
                                                href="{{ route('admin.moderation.show', $warning) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                التفاصيل
                                            </a>

                                            @if (
                                                in_array(
                                                    $warning->status,
                                                    ['active', 'appealed'],
                                                    true
                                                )
                                            )
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmWarningModal{{ $warning->id }}"
                                                >
                                                    تأكيد
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#cancelWarningModal{{ $warning->id }}"
                                                >
                                                    إلغاء
                                                </button>
                                            @endif

                                            @if (
                                                $user &&
                                                $user->status === 'suspended_pending_review'
                                            )
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reactivateUserModal{{ $warning->id }}"
                                                >
                                                    إعادة التفعيل
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#keepSuspendedModal{{ $warning->id }}"
                                                >
                                                    تثبيت التعليق
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <div
                                    class="modal fade"
                                    id="confirmWarningModal{{ $warning->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form
                                                method="POST"
                                                action="{{ route('admin.moderation.confirm', $warning) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        تأكيد التحذير
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="إغلاق"
                                                    ></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p>
                                                        سيتم تثبيت التحذير رقم
                                                        <strong>
                                                            {{ $warning->warning_number }}
                                                        </strong>
                                                        على حساب المستخدم.
                                                    </p>

                                                    <label
                                                        class="form-label"
                                                        for="confirm_notes_{{ $warning->id }}"
                                                    >
                                                        ملاحظات المراجعة
                                                    </label>

                                                    <textarea
                                                        id="confirm_notes_{{ $warning->id }}"
                                                        name="review_notes"
                                                        class="form-control"
                                                        rows="4"
                                                        maxlength="3000"
                                                    ></textarea>
                                                </div>

                                                <div class="modal-footer">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        تراجع
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-success"
                                                    >
                                                        تأكيد التحذير
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="modal fade"
                                    id="cancelWarningModal{{ $warning->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form
                                                method="POST"
                                                action="{{ route('admin.moderation.cancel', $warning) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        إلغاء التحذير
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="إغلاق"
                                                    ></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        سيتم إلغاء التحذير وإعادة حساب عدد مخالفات المستخدم.
                                                    </div>

                                                    <label
                                                        class="form-label"
                                                        for="cancel_notes_{{ $warning->id }}"
                                                    >
                                                        سبب إلغاء التحذير
                                                    </label>

                                                    <textarea
                                                        id="cancel_notes_{{ $warning->id }}"
                                                        name="review_notes"
                                                        class="form-control"
                                                        rows="4"
                                                        maxlength="3000"
                                                        required
                                                    ></textarea>
                                                </div>

                                                <div class="modal-footer">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        تراجع
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        إلغاء التحذير
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @if ($user)
                                    <div
                                        class="modal fade"
                                        id="reactivateUserModal{{ $warning->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.moderation.reactivate', $user) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            إعادة تفعيل الحساب
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="إغلاق"
                                                        ></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <p>
                                                            سيتم إعادة تفعيل حساب:
                                                            <strong>
                                                                {{ $user->name }}
                                                            </strong>
                                                        </p>

                                                        <label
                                                            class="form-label"
                                                            for="reactivate_notes_{{ $warning->id }}"
                                                        >
                                                            ملاحظات القرار
                                                        </label>

                                                        <textarea
                                                            id="reactivate_notes_{{ $warning->id }}"
                                                            name="review_notes"
                                                            class="form-control"
                                                            rows="4"
                                                            maxlength="3000"
                                                            required
                                                        ></textarea>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal"
                                                        >
                                                            تراجع
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-success"
                                                        >
                                                            إعادة التفعيل
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="modal fade"
                                        id="keepSuspendedModal{{ $warning->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.moderation.keep-suspended', $user) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            تثبيت تعليق الحساب
                                                        </h5>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="إغلاق"
                                                        ></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            سيبقى المستخدم غير قادر على استخدام المنصة.
                                                        </div>

                                                        <label
                                                            class="form-label"
                                                            for="suspend_notes_{{ $warning->id }}"
                                                        >
                                                            سبب تثبيت التعليق
                                                        </label>

                                                        <textarea
                                                            id="suspend_notes_{{ $warning->id }}"
                                                            name="review_notes"
                                                            class="form-control"
                                                            rows="4"
                                                            maxlength="3000"
                                                            required
                                                        ></textarea>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal"
                                                        >
                                                            تراجع
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-danger"
                                                        >
                                                            تثبيت التعليق
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($warnings->hasPages())
            <div class="bg-white card-footer">
                {{ $warnings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
