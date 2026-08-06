<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModerationAppeal;
use App\Models\UserWarning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModerationAppealController extends Controller
{
    /**
     * عرض جميع طلبات الطعن للمدير.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                'in:pending,under_review,approved,rejected,cancelled',
            ],
        ]);

        $appeals = ModerationAppeal::query()
            ->with([
                'user:id,name,email,phone,status,warnings_count',
                'warning:id,user_id,warning_number,category,reason,status',
                'reviewer:id,name',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('message', 'like', "%{$search}%")
                            ->orWhere('admin_response', 'like', "%{$search}%")
                            ->orWhereHas(
                                'user',
                                function ($userQuery) use ($search) {
                                    $userQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%")
                                        ->orWhere('phone', 'like', "%{$search}%");
                                }
                            );
                    });
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) =>
                    $query->where('status', $status)
            )
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $statistics = [
            'all' => ModerationAppeal::query()->count(),

            'pending' => ModerationAppeal::query()
                ->where('status', 'pending')
                ->count(),

            'under_review' => ModerationAppeal::query()
                ->where('status', 'under_review')
                ->count(),

            'approved' => ModerationAppeal::query()
                ->where('status', 'approved')
                ->count(),

            'rejected' => ModerationAppeal::query()
                ->where('status', 'rejected')
                ->count(),
        ];

        return view(
            'admin.moderation-appeals.index',
            compact(
                'appeals',
                'statistics',
                'filters'
            )
        );
    }

    /**
     * عرض تفاصيل طعن محدد.
     */
    public function show(
        ModerationAppeal $appeal
    ): View {
        $appeal->load([
            'user',
            'warning.moderation',
            'reviewer:id,name',
        ]);

        return view(
            'admin.moderation-appeals.show',
            compact('appeal')
        );
    }

    /**
     * بدء مراجعة الطعن.
     */
    public function startReview(
        Request $request,
        ModerationAppeal $appeal
    ): RedirectResponse {
        if ($appeal->status !== 'pending') {
            return back()->with(
                'error',
                'لا يمكن بدء مراجعة هذا الطعن.'
            );
        }

        $appeal->update([
            'status' => 'under_review',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with(
            'success',
            'تم تحويل الطعن إلى حالة تحت المراجعة.'
        );
    }

    /**
     * قبول الطعن وإعادة تفعيل حساب المستخدم.
     */
    public function approve(
        Request $request,
        ModerationAppeal $appeal
    ): RedirectResponse {
        $data = $request->validate([
            'admin_response' => [
                'required',
                'string',
                'min:10',
                'max:3000',
            ],
        ], [
            'admin_response.required' =>
                'يجب كتابة رد الإدارة.',

            'admin_response.min' =>
                'رد الإدارة يجب ألا يقل عن 10 أحرف.',
        ]);

        DB::transaction(function () use (
            $request,
            $appeal,
            $data
        ) {
            $lockedAppeal = ModerationAppeal::query()
                ->lockForUpdate()
                ->findOrFail($appeal->id);

            if (
                ! in_array(
                    $lockedAppeal->status,
                    [
                        'pending',
                        'under_review',
                    ],
                    true
                )
            ) {
                abort(
                    422,
                    'تمت معالجة هذا الطعن مسبقًا.'
                );
            }

            $user = $lockedAppeal->user()
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAppeal->update([
                'status' => 'approved',
                'admin_response' =>
                    trim($data['admin_response']),
                'reviewed_by' =>
                    $request->user()->id,
                'reviewed_at' =>
                    $lockedAppeal->reviewed_at ?? now(),
                'resolved_at' => now(),
            ]);

            if ($lockedAppeal->warning) {
                $lockedAppeal->warning->update([
                    'status' => 'cancelled',
                    'review_notes' =>
                        trim($data['admin_response']),
                    'reviewed_by' =>
                        $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            }

            $activeWarningsCount = UserWarning::query()
                ->where('user_id', $user->id)
                ->whereIn('status', [
                    'active',
                    'confirmed',
                    'appealed',
                ])
                ->count();

            $user->update([
                'status' => 'active',
                'warnings_count' =>
                    $activeWarningsCount,
                'suspended_at' => null,
                'suspension_reason' => null,
                'suspension_source' => null,
            ]);
        });

        return redirect()
            ->route(
                'admin.moderation-appeals.show',
                $appeal
            )
            ->with(
                'success',
                'تم قبول الطعن وإعادة تفعيل حساب المستخدم.'
            );
    }

    /**
     * رفض الطعن وتثبيت تعليق الحساب.
     */
    public function reject(
        Request $request,
        ModerationAppeal $appeal
    ): RedirectResponse {
        $data = $request->validate([
            'admin_response' => [
                'required',
                'string',
                'min:10',
                'max:3000',
            ],
        ], [
            'admin_response.required' =>
                'يجب كتابة سبب رفض الطعن.',

            'admin_response.min' =>
                'سبب الرفض يجب ألا يقل عن 10 أحرف.',
        ]);

        DB::transaction(function () use (
            $request,
            $appeal,
            $data
        ) {
            $lockedAppeal = ModerationAppeal::query()
                ->lockForUpdate()
                ->findOrFail($appeal->id);

            if (
                ! in_array(
                    $lockedAppeal->status,
                    [
                        'pending',
                        'under_review',
                    ],
                    true
                )
            ) {
                abort(
                    422,
                    'تمت معالجة هذا الطعن مسبقًا.'
                );
            }

            $user = $lockedAppeal->user()
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAppeal->update([
                'status' => 'rejected',
                'admin_response' =>
                    trim($data['admin_response']),
                'reviewed_by' =>
                    $request->user()->id,
                'reviewed_at' =>
                    $lockedAppeal->reviewed_at ?? now(),
                'resolved_at' => now(),
            ]);

            if ($lockedAppeal->warning) {
                $lockedAppeal->warning->update([
                    'status' => 'confirmed',
                    'review_notes' =>
                        trim($data['admin_response']),
                    'reviewed_by' =>
                        $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            }

            $user->update([
                'status' => 'suspended',
                'suspended_at' =>
                    $user->suspended_at ?? now(),
                'suspension_reason' =>
                    trim($data['admin_response']),
                'suspension_source' =>
                    'moderation_appeal_rejected',
            ]);
        });

        return redirect()
            ->route(
                'admin.moderation-appeals.show',
                $appeal
            )
            ->with(
                'success',
                'تم رفض الطعن وتثبيت تعليق الحساب.'
            );
    }
}
