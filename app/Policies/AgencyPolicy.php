<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

class AgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-agencies');
    }

    public function view(User $user, Agency $agency): bool
    {
        // Super Admin voit son agence
        if ($user->hasRole('Super Admin Agence')) {
            return $user->branch && $user->branch->agency_id === $agency->id;
        }

        // Ministère voit toutes les agences
        return $user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National']);
    }

    public function create(User $user): bool
    {
        // Seulement le ministère peut créer des agences
        return $user->hasRole('Superviseur Ministériel National');
    }

    public function update(User $user, Agency $agency): bool
    {
        if (!$user->hasPermissionTo('edit-agencies')) {
            return false;
        }

        // Super Admin peut modifier son agence
        if ($user->hasRole('Super Admin Agence')) {
            return $user->branch && $user->branch->agency_id === $agency->id;
        }

        // Ministère peut modifier toutes les agences
        return $user->hasRole('Superviseur Ministériel National');
    }

    public function delete(User $user, Agency $agency): bool
    {
        // Les agences ne sont jamais supprimées, seulement désactivées
        return false;
    }

    public function approve(User $user, Agency $agency): bool
    {
        // Seulement le ministère peut approuver/révoquer
        return $user->hasPermissionTo('approve-agencies');
    }

    public function restore(User $user, Agency $agency): bool
    {
        return false;
    }

    public function forceDelete(User $user, Agency $agency): bool
    {
        return false;
    }
}
