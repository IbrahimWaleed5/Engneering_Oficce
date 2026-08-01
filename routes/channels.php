<?php

use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| قناة محادثات الاستشارات الحالية
|--------------------------------------------------------------------------
*/

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
            'profile_photo_url' =>
                $user->profile_photo
                    ? asset(
                        'storage/'
                        . $user->profile_photo
                    )
                    : null,
        ];
    }
);

/*
|--------------------------------------------------------------------------
| قناة نظام المحادثات الموحد
|--------------------------------------------------------------------------
*/

Broadcast::channel(
    'conversation.{conversationId}',
    function (
        User $user,
        int $conversationId
    ): array|false {
        $conversation = Conversation::query()
            ->with('consultation')
            ->find($conversationId);

        if (! $conversation) {
            return false;
        }

        if (! Gate::forUser($user)->allows(
            'view',
            $conversation
        )) {
            return false;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'profile_photo' => $user->profile_photo,
            'profile_photo_url' =>
                $user->profile_photo
                    ? asset(
                        'storage/'
                        . $user->profile_photo
                    )
                    : null,
        ];
    }
);
