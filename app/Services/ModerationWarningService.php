<?php

namespace App\Services;

use App\Models\ContentModeration;
use App\Models\User;
use App\Models\UserWarning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class ModerationWarningService
{
    public const WARNING_LIMIT = 3;

    /**
     * إصدار تحذير لمستخدم بسبب مخالفة مؤكدة.
     */
    public function issueWarning(
        User $user,
        string $reason,
        ?ContentModeration $moderation = null,
        ?string $category = null,
        string $issuedByType = 'ai',
        ?User $issuer = null
    ): UserWarning {
        try {
            return DB::transaction(function () use (
                $user,
                $reason,
                $moderation,
                $category,
                $issuedByType,
                $issuer
            ): UserWarning {
                /*
                 * قفل سجل المستخدم أثناء حساب التحذيرات
                 * لتجنب إصدار تحذيرين في اللحظة نفسها.
                 */
                $lockedUser = User::query()
                    ->lockForUpdate()
                    ->findOrFail($user->id);

                /*
                 * منع إصدار أكثر من تحذير لنفس نتيجة الفحص.
                 */
                if ($moderation) {
                    $existingWarning = UserWarning::query()
                        ->where(
                            'content_moderation_id',
                            $moderation->id
                        )
                        ->whereIn('status', [
                            'active',
                            'confirmed',
                        ])
                        ->first();

                    if ($existingWarning) {
                        return $existingWarning;
                    }
                }

                $activeWarningsCount = UserWarning::query()
                    ->where('user_id', $lockedUser->id)
                    ->whereIn('status', [
                        'active',
                        'confirmed',
                    ])
                    ->count();

                $warningNumber = min(
                    $activeWarningsCount + 1,
                    self::WARNING_LIMIT
                );

                $shouldSuspend =
                    $warningNumber >= self::WARNING_LIMIT;

                $warning = UserWarning::create([
                    'user_id' => $lockedUser->id,

                    'content_moderation_id' =>
                        $moderation?->id,

                    'warning_number' =>
                        $warningNumber,

                    'category' =>
                        $category,

                    'reason' =>
                        $reason,

                    'issued_by_type' =>
                        $issuedByType,

                    'issued_by' =>
                        $issuer?->id,

                    'status' =>
                        'active',

                    'account_suspended' =>
                        $shouldSuspend,
                ]);

                $newWarningsCount = UserWarning::query()
                    ->where('user_id', $lockedUser->id)
                    ->whereIn('status', [
                        'active',
                        'confirmed',
                    ])
                    ->count();

                $userUpdate = [
                    'warnings_count' =>
                        $newWarningsCount,
                ];

                if ($shouldSuspend) {
                    $userUpdate = array_merge(
                        $userUpdate,
                        [
                            'status' =>
                                'suspended_pending_review',

                            'suspended_at' =>
                                now(),

                            'suspension_reason' =>
                                'تم تعليق الحساب تلقائيًا بعد تسجيل ثلاث مخالفات محتوى مؤكدة.',

                            'suspension_source' =>
                                'content_moderation',
                        ]
                    );
                }

                $lockedUser->update($userUpdate);

                if ($moderation) {
                    $moderation->update([
                        'warning_issued' => true,
                    ]);
                }

                Log::info(
                    'Moderation warning issued.',
                    [
                        'user_id' =>
                            $lockedUser->id,

                        'warning_id' =>
                            $warning->id,

                        'warning_number' =>
                            $warningNumber,

                        'account_suspended' =>
                            $shouldSuspend,

                        'moderation_id' =>
                            $moderation?->id,
                    ]
                );

                return $warning;
            });
        } catch (Throwable $exception) {
            Log::error(
                'Failed to issue moderation warning.',
                [
                    'user_id' => $user->id,
                    'moderation_id' =>
                        $moderation?->id,
                    'error' =>
                        $exception->getMessage(),
                ]
            );

            throw $exception;
        }
    }

    /**
     * إلغاء تحذير بعد مراجعة الإدارة.
     */
    public function cancelWarning(
        UserWarning $warning,
        User $reviewer,
        string $reviewNotes
    ): UserWarning {
        if (! in_array(
            $reviewer->role,
            ['admin'],
            true
        )) {
            throw new RuntimeException(
                'غير مصرح لك بإلغاء التحذير.'
            );
        }

        return DB::transaction(function () use (
            $warning,
            $reviewer,
            $reviewNotes
        ): UserWarning {
            $lockedWarning = UserWarning::query()
                ->lockForUpdate()
                ->findOrFail($warning->id);

            if ($lockedWarning->status === 'cancelled') {
                return $lockedWarning;
            }

            $lockedWarning->update([
                'status' => 'cancelled',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $reviewNotes,
                'cancelled_at' => now(),
                'account_suspended' => false,
            ]);

            $this->refreshUserWarningStatus(
                $lockedWarning->user
            );

            return $lockedWarning->fresh();
        });
    }

    /**
     * تأكيد التحذير بعد مراجعة الإدارة.
     */
    public function confirmWarning(
        UserWarning $warning,
        User $reviewer,
        ?string $reviewNotes = null
    ): UserWarning {
        if (! in_array(
            $reviewer->role,
            ['admin'],
            true
        )) {
            throw new RuntimeException(
                'غير مصرح لك بتأكيد التحذير.'
            );
        }

        $warning->update([
            'status' => 'confirmed',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $reviewNotes,
        ]);

        $this->refreshUserWarningStatus(
            $warning->user
        );

        return $warning->fresh();
    }

    /**
     * إعادة حساب عدد التحذيرات وحالة الحساب.
     */
    public function refreshUserWarningStatus(
        User $user
    ): User {
        return DB::transaction(function () use (
            $user
        ): User {
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $activeWarningsCount = UserWarning::query()
                ->where('user_id', $lockedUser->id)
                ->whereIn('status', [
                    'active',
                    'confirmed',
                ])
                ->count();

            $update = [
                'warnings_count' =>
                    $activeWarningsCount,
            ];

            if (
                $activeWarningsCount >=
                self::WARNING_LIMIT
            ) {
                $update = array_merge(
                    $update,
                    [
                        'status' =>
                            'suspended_pending_review',

                        'suspended_at' =>
                            $lockedUser->suspended_at
                            ?? now(),

                        'suspension_reason' =>
                            $lockedUser->suspension_reason
                            ?? 'تم تعليق الحساب بسبب تكرار مخالفات المحتوى.',

                        'suspension_source' =>
                            'content_moderation',
                    ]
                );
            } elseif (
                $lockedUser->status ===
                'suspended_pending_review'
                && $lockedUser->suspension_source ===
                    'content_moderation'
            ) {
                /*
                 * عند إلغاء تحذير خاطئ وانخفاض العدد عن 3،
                 * يعاد الحساب إلى الحالة النشطة.
                 */
                $update = array_merge(
                    $update,
                    [
                        'status' => 'active',
                        'suspended_at' => null,
                        'suspension_reason' => null,
                        'suspension_source' => null,
                    ]
                );
            }

            $lockedUser->update($update);

            return $lockedUser->fresh();
        });
    }
}
