<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Voir seulement les paiements de sa branche
        return $user->branch_id === $payment->pilgrim->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-payments');
    }

    public function update(User $user, Payment $payment): bool
    {
        if (!$user->hasPermissionTo('edit-payments')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        // Comptable et Admin Branche peuvent modifier
        return $user->hasAnyRole(['Comptable', 'Admin Branche']) 
            && $user->branch_id === $payment->pilgrim->branch_id;
    }

    public function delete(User $user, Payment $payment): bool
    {
        // Les paiements ne doivent pas être supprimés, seulement remboursés
        return false;
    }

    public function restore(User $user, Payment $payment): bool
    {
        return false;
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return false;
    }
}
