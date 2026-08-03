<?php

namespace App\Console\Commands;

use App\Models\Office;
use App\Models\OfficeSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireOfficeSubscriptions extends Command
{
    /**
     * اسم الأمر الذي سنشغله يدويًا أو من المجدول.
     */
    protected $signature = 'offices:expire-subscriptions';

    /**
     * وصف الأمر.
     */
    protected $description =
        'تحديث اشتراكات المكاتب المنتهية وتعطيل حالة الاشتراك الخاصة بالمكتب';

    public function handle(): int
    {
        $now = now();

        $expiredSubscriptions = OfficeSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('لا توجد اشتراكات منتهية.');

            return self::SUCCESS;
        }

        $expiredCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            DB::transaction(function () use (
                $subscription,
                $now,
                &$expiredCount
            ) {
                $subscription->update([
                    'status' => 'expired',
                ]);

                $office = Office::query()
                    ->lockForUpdate()
                    ->find($subscription->office_id);

                if (! $office) {
                    return;
                }

                /*
                | نتحقق من عدم وجود اشتراك فعال آخر للمكتب.
                */
                $hasAnotherActiveSubscription =
                    OfficeSubscription::query()
                        ->where('office_id', $office->id)
                        ->where('id', '!=', $subscription->id)
                        ->where('status', 'active')
                        ->whereNotNull('ends_at')
                        ->where('ends_at', '>', $now)
                        ->exists();

                if (! $hasAnotherActiveSubscription) {
                    $office->update([
                        'subscription_status' => 'expired',
                    ]);
                }

                $expiredCount++;
            });
        }

        $this->info(
            "تم تحديث {$expiredCount} اشتراك منتهي."
        );

        return self::SUCCESS;
    }
}
