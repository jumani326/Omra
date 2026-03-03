<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('view-packages');
    }

    public function view(User $user, Package $package): bool
    {
        // Chaque agence ne voit que ses propres forfaits (via la branche)
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $packageAgencyId = $package->branch?->agency_id;
        if ($userAgencyId && $packageAgencyId != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Rôle agence : voir sa branche (ou tout si pas de branche)
        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $package->branch_id;
        }

        return $user->branch_id === $package->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('create-packages');
    }

    public function update(User $user, Package $package): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $packageAgencyId = $package->branch?->agency_id;
        if ($userAgencyId && $packageAgencyId != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $package->branch_id;
        }
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
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $packageAgencyId = $package->branch?->agency_id;
        if ($userAgencyId && $packageAgencyId != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $package->branch_id;
        }
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
