<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(
    'consultation.{consultationId}',
    function (
        User $user,
        int $consultationId
    ): array|false {
        $consultation = Consultation::query()
            ->find($consultationId);

        if (! $consultation) {
            return false;
        }

        $allowed =
            $user->role === 'admin'
            || (int) $consultation->customer_id
                === (int) $user->id
            || (int) $consultation->engineer_id
                === (int) $user->id;

        if (! $allowed) {
            return false;
        }

        /*
         * لا تُفتح المحادثة قبل تأكيد الدفع.
         */
        if (
            $user->role !== 'admin'
            && $consultation->payment_status !== 'paid'
        ) {
            return false;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'profile_photo' => $user->profile_photo,
        ];
    }
);
