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
        // Chaque agence ne voit que les utilisateurs de sa propre agence
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $modelAgencyId = $model->agence_id ?? $model->branch?->agency_id;
        if ($userAgencyId && $modelAgencyId != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

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
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $modelAgencyId = $model->agence_id ?? $model->branch?->agency_id;
        if ($userAgencyId && $modelAgencyId != $userAgencyId) {
            return false;
        }

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

        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $modelAgencyId = $model->agence_id ?? $model->branch?->agency_id;
        if ($userAgencyId && $modelAgencyId != $userAgencyId) {
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
