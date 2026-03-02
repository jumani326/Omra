<?php

namespace App\Policies;

use App\Models\CompteMarchand;
use App\Models\User;

class CompteMarchandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence');
    }

    public function view(User $user, CompteMarchand $compteMarchand): bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }
        if (!$user->hasRole('agence')) {
            return false;
        }
        return $user->branch_id === null || $user->branch_id === $compteMarchand->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence');
    }

    public function update(User $user, CompteMarchand $compteMarchand): bool
    {
        return $this->view($user, $compteMarchand);
    }

    public function delete(User $user, CompteMarchand $compteMarchand): bool
    {
        return $this->view($user, $compteMarchand);
    }
}
