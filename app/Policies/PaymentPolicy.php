<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function before(
        User $user,
        string $ability
    ): bool|null {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function view(
        User $user,
        Payment $payment
    ): bool {
        return $user->role === 'customer'
            && (int) $payment->customer_id
                === (int) $user->id;
    }

    public function downloadReceipt(
        User $user,
        Payment $payment
    ): bool {
        return $user->role === 'admin';
    }

    public function confirm(
        User $user,
        Payment $payment
    ): bool {
        return $user->role === 'admin'
            && $payment->status === 'pending';
    }

    public function reject(
        User $user,
        Payment $payment
    ): bool {
        return $user->role === 'admin'
            && $payment->status === 'pending';
    }
}
