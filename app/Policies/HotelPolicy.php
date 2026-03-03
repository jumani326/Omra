<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;

class HotelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('view-packages') || $user->hasPermissionTo('create-packages');
    }

    public function view(User $user, Hotel $hotel): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $hotel->agency_id != $userAgencyId) {
            return false;
        }
        return $user->hasRole('agence') || $user->hasPermissionTo('view-packages') || $user->hasPermissionTo('create-packages');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('create-packages');
    }

    public function update(User $user, Hotel $hotel): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $hotel->agency_id != $userAgencyId) {
            return false;
        }
        return $user->hasRole('agence') || $user->hasPermissionTo('edit-packages');
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $hotel->agency_id != $userAgencyId) {
            return false;
        }
        return $user->hasRole('agence') || $user->hasPermissionTo('delete-packages');
    }

    public function restore(User $user, Hotel $hotel): bool
    {
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $hotel->agency_id != $userAgencyId) {
            return false;
        }
        return $user->hasPermissionTo('delete-packages');
    }

    public function forceDelete(User $user, Hotel $hotel): bool
    {
        return false;
    }
}
