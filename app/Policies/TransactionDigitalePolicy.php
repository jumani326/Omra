<?php

namespace App\Policies;

use App\Models\TransactionDigitale;
use App\Models\User;

class TransactionDigitalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence');
    }

    public function view(User $user, TransactionDigitale $transaction): bool
    {
        if (!$user->hasRole('agence')) {
            return false;
        }
        $compte = $transaction->compteMarchand;
        return $user->branch_id === null || $user->branch_id === $compte->branch_id;
    }
}
