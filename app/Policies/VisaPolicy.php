<?php

namespace App\Policies;

use App\Models\Visa;
use App\Models\User;

class VisaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_visas') || $user->hasPermissionTo('view-visas');
    }

    public function view(User $user, Visa $visa): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        $pilgrimAgencyId = $visa->pilgrim?->agence_id;
        if ($userAgencyId && $pilgrimAgencyId != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $visa->pilgrim->branch_id;
        }

        return $user->branch_id === $visa->pilgrim->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_visas') || $user->hasPermissionTo('create-visas');
    }

    public function update(User $user, Visa $visa): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $visa->pilgrim?->agence_id != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $visa->pilgrim->branch_id;
        }
        if (!$user->hasPermissionTo('edit-visas')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        return $user->hasAnyRole(['Officier Visa', 'Admin Branche']) 
            && $user->branch_id === $visa->pilgrim->branch_id;
    }

    public function delete(User $user, Visa $visa): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $visa->pilgrim?->agence_id != $userAgencyId) {
            return false;
        }

        if ($user->hasRole('agence')) {
            return $user->branch_id === null || $user->branch_id === $visa->pilgrim->branch_id;
        }
        if (!$user->hasPermissionTo('delete-visas')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        return $user->hasRole('Officier Visa') && $user->branch_id === $visa->pilgrim->branch_id;
    }

    public function restore(User $user, Visa $visa): bool
    {
        return $this->delete($user, $visa);
    }

    public function forceDelete(User $user, Visa $visa): bool
    {
        return false;
    }
}
