<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-packages');
    }

    public function view(User $user, Package $package): bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Voir seulement sa branche
        return $user->branch_id === $package->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-packages');
    }

    public function update(User $user, Package $package): bool
    {
        if (!$user->hasPermissionTo('edit-packages')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        return $user->hasRole('Admin Branche') && $user->branch_id === $package->branch_id;
    }

    public function delete(User $user, Package $package): bool
    {
        if (!$user->hasPermissionTo('delete-packages')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        return $user->hasRole('Admin Branche') && $user->branch_id === $package->branch_id;
    }

    public function restore(User $user, Package $package): bool
    {
        return $this->delete($user, $package);
    }

    public function forceDelete(User $user, Package $package): bool
    {
        return false;
    }
}
