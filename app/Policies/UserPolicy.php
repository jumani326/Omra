<?php

namespace App\Policies;

use App\Models\User as UserModel;

class UserPolicy
{
    public function viewAny(UserModel $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_guides') || $user->hasPermissionTo('view-users');
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Rôle agence : voir les utilisateurs de sa branche (ou tout si pas de branche)
        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $model->branch_id;
        }

        if ($user->hasRole('Admin Branche')) {
            return $user->branch_id === $model->branch_id;
        }

        return false;
    }

    public function create(UserModel $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_guides') || $user->hasPermissionTo('create-users');
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $model->branch_id;
        }
        if (!$user->hasPermissionTo('edit-users')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasRole('Admin Branche')) {
            return $user->branch_id === $model->branch_id;
        }

        return false;
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $model->branch_id;
        }
        if (!$user->hasPermissionTo('delete-users')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasRole('Admin Branche')) {
            return $user->branch_id === $model->branch_id;
        }

        return false;
    }

    public function restore(UserModel $user, UserModel $model): bool
    {
        return $this->delete($user, $model);
    }

    public function forceDelete(UserModel $user, UserModel $model): bool
    {
        return false;
    }
}
