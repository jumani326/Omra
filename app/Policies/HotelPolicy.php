<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HotelPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('view-packages') || $user->hasPermissionTo('create-packages');
    }

    public function view(User $user, Hotel $hotel): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view-packages') || $user->hasPermissionTo('create-packages');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('view_own_data') || $user->hasPermissionTo('create-packages');
    }

    public function update(User $user, Hotel $hotel): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('edit-packages');
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('delete-packages');
    }

    public function restore(User $user, Hotel $hotel): bool
    {
        return $user->hasPermissionTo('delete-packages');
    }

    public function forceDelete(User $user, Hotel $hotel): bool
    {
        return false;
    }
}
