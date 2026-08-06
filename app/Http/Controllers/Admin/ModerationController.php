<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentModeration;
use App\Models\User;
use App\Models\UserWarning;
use App\Services\ModerationWarningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    /**
     * عرض جميع التحذيرات ونتائج فحص المحتوى.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:150',
            ],

            'status' => [
                'nullable',
                'in:active,confirmed,cancelled,appealed',
            ],

            'account_status' => [
                'nullable',
                'in:active,inactive,suspended_pending_review,suspended,blocked',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $warningsQuery = UserWarning::query()
            ->with([
                'user:id,name,email,phone,profile_photo,role,status,warnings_count,suspended_at,suspension_reason',
                'moderation',
                'issuer:id,name',
                'reviewer:id,name',
            ])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $warningsQuery->where(function ($query) use ($search) {
                $query
                    ->where('reason', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $warningsQuery->where(
                'status',
                $filters['status']
            );
        }

        if (! empty($filters['category'])) {
            $warningsQuery->where(
                'category',
                $filters['category']
            );
        }

        if (! empty($filters['account_status'])) {
            $accountStatus =
                $filters['account_status'];

            $warningsQuery->whereHas(
                'user',
                fn ($query) =>
                    $query->where(
                        'status',
                        $accountStatus
                    )
            );
        }

        $warnings = $warningsQuery
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'all_warnings' =>
                UserWarning::query()->count(),

            'active_warnings' =>
                UserWarning::query()
                    ->whereIn('status', [
                        'active',
                        'confirmed',
                    ])
                    ->count(),

            'pending_reviews' =>
                ContentModeration::query()
                    ->where('decision', 'needs_review')
                    ->count(),

            'rejected_content' =>
                ContentModeration::query()
                    ->where('decision', 'rejected')
                    ->count(),

            'suspended_accounts' =>
                User::query()
                    ->where(
                        'status',
                        'suspended_pending_review'
                    )
                    ->count(),
        ];

        $categories = UserWarning::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'admin.moderation.index',
            compact(
                'warnings',
                'statistics',
                'categories',
                'filters'
            )
        );
    }

    /**
     * عرض تفاصيل تحذير واحد.
     */
    public function show(
        UserWarning $warning
    ): View {
        $warning->load([
            'user',
            'moderation',
            'issuer:id,name',
            'reviewer:id,name',
        ]);

        return view(
            'admin.moderation.show',
            compact('warning')
        );
    }

    /**
     * تأكيد التحذير بعد مراجعة المدير.
     */
    public function confirm(
        Request $request,
        UserWarning $warning,
        ModerationWarningService $service
    ): RedirectResponse {
        $data = $request->validate([
            'review_notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $service->confirmWarning(
            $warning,
            $request->user(),
            $data['review_notes'] ?? null
        );

        return back()->with(
            'success',
            'تم تأكيد التحذير بنجاح.'
        );
    }

    /**
     * إلغاء تحذير خاطئ.
     */
    public function cancel(
        Request $request,
        UserWarning $warning,
        ModerationWarningService $service
    ): RedirectResponse {
        $data = $request->validate([
            'review_notes' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $service->cancelWarning(
            $warning,
            $request->user(),
            $data['review_notes']
        );

        return back()->with(
            'success',
            'تم إلغاء التحذير وإعادة حساب حالة المستخدم.'
        );
    }

    /**
     * إعادة تفعيل حساب بعد مراجعة الإدارة.
     */
    public function reactivate(
        Request $request,
        User $user,
        ModerationWarningService $service
    ): RedirectResponse {
        abort_unless(
            $user->status ===
                'suspended_pending_review',
            422,
            'هذا الحساب ليس بانتظار مراجعة.'
        );

        $data = $request->validate([
            'review_notes' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $user->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
            'suspension_source' => null,
        ]);

        return back()->with(
            'success',
            'تمت إعادة تفعيل الحساب بنجاح.'
        );
    }

    /**
     * إبقاء الحساب معلّقًا بعد مراجعة المدير.
     */
    public function keepSuspended(
        Request $request,
        User $user
    ): RedirectResponse {
        $data = $request->validate([
            'review_notes' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $user->update([
            'status' => 'suspended',
            'suspended_at' =>
                $user->suspended_at ?? now(),

            'suspension_reason' =>
                $data['review_notes'],

            'suspension_source' =>
                'admin_review',
        ]);

        return back()->with(
            'success',
            'تم تثبيت تعليق الحساب.'
        );
    }
}
