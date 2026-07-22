<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Console\Command;

class ExpireEngineerMemberships extends Command
{
    protected $signature =
        'engineers:expire-memberships';

    protected $description =
        'Deactivate expired engineer memberships';

    public function handle(): int
    {
        $expiredCount = 0;

        User::query()
            ->where('role', 'engineer')
            ->where(
                'engineer_membership_status',
                'active'
            )
            ->whereNotNull(
                'engineer_active_until'
            )
            ->where(
                'engineer_active_until',
                '<=',
                now()
            )
            ->chunkById(
                100,
                function ($engineers) use (
                    &$expiredCount
                ) {
                    foreach ($engineers as $engineer) {
                        $engineer->update([
                            'engineer_membership_status' =>
                                'inactive',
                        ]);

                        $engineer->notify(
                            new SystemNotification(
                                'انتهى اشتراك المهندس',
                                'انتهت مدة تفعيل حسابك كمهندس. يمكنك استخدام النظام كعميل ورفع دفعة جديدة لإعادة التفعيل.',
                                '/dashboard'
                            )
                        );

                        $expiredCount++;
                    }
                }
            );

        $this->info(
            "Expired memberships: {$expiredCount}"
        );

        return self::SUCCESS;
    }
}
