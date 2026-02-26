<?php

namespace App\Policies;

use App\Models\User as UserModel;

class UserPolicy
{
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermissionTo('view-users');
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Admin Branche voit sa branche
        if ($user->hasRole('Admin Branche')) {
            return $user->branch_id === $model->branch_id;
        }

        return false;
    }

    public function create(UserModel $user): bool
    {
        return $user->hasPermissionTo('create-users');
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        if (!$user->hasPermissionTo('edit-users')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        // Admin Branche peut modifier les utilisateurs de sa branche
        if ($user->hasRole('Admin Branche')) {
            return $user->branch_id === $model->branch_id;
        }

        return false;
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        if (!$user->hasPermissionTo('delete-users')) {
            return false;
        }

        // Ne pas pouvoir se supprimer soi-même
        if ($user->id === $model->id) {
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
