<?php

namespace App\Services;

use App\Models\EmailTwoFactorCode;
use App\Models\User;
use App\Notifications\EmailTwoFactorCodeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmailTwoFactorService
{
    public const CODE_TTL_MINUTES = 10;
    public const RESEND_SECONDS = 60;
    public const MAX_ATTEMPTS = 5;

    public function send(User $user): void
    {
        $latest = EmailTwoFactorCode::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (
            $latest?->last_sent_at
            && $latest->last_sent_at->gt(now()->subSeconds(self::RESEND_SECONDS))
        ) {
            throw ValidationException::withMessages([
                'code' => 'انتظر 60 ثانية قبل طلب رمز جديد.',
            ]);
        }

        EmailTwoFactorCode::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = (string) random_int(100000, 999999);

        EmailTwoFactorCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::CODE_TTL_MINUTES),
            'last_sent_at' => now(),
        ]);

        $user->notify(
            new EmailTwoFactorCodeNotification($code, self::CODE_TTL_MINUTES)
        );
    }

    public function verify(User $user, string $code): bool
    {
        $record = EmailTwoFactorCode::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (! $record || $record->expires_at->isPast()) {
            return false;
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempts');
            return false;
        }

        $record->update(['used_at' => now()]);
        return true;
    }
}
