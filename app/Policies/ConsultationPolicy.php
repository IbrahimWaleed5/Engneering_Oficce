<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function view(User $user, Consultation $consultation): bool
    {
        return (int) $consultation->customer_id === (int) $user->id
            || (int) $consultation->engineer_id === (int) $user->id
            || $user->role === 'employee';
    }

    public function pay(User $user, Consultation $consultation): bool
    {
        return $user->role === 'customer'
            && (int) $consultation->customer_id === (int) $user->id
            && $consultation->payment_status === 'unpaid';
    }

    public function viewConversation(User $user, Consultation $consultation): bool
    {
        return $consultation->payment_status === 'paid'
            && $this->view($user, $consultation);
    }

    public function sendMessage(User $user, Consultation $consultation): bool
    {
        return $this->viewConversation($user, $consultation)
            && ! in_array($consultation->status, ['cancelled', 'closed'], true);
    }

    public function uploadEngineerFile(User $user, Consultation $consultation): bool
    {
        return $consultation->payment_status === 'paid'
            && $user->role === 'engineer'
            && (int) $consultation->engineer_id === (int) $user->id
            && $user->hasActiveEngineerMembership();
    }

    public function downloadCustomerFile(User $user, Consultation $consultation): bool
    {
        return $this->view($user, $consultation);
    }

    public function downloadEngineerFile(User $user, Consultation $consultation): bool
    {
        return $consultation->status === 'completed'
            && $this->view($user, $consultation);
    }

    public function assignEngineer(User $user, Consultation $consultation): bool
    {
        return $user->role === 'admin';
    }
}
