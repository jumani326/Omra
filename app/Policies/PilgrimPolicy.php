<?php

namespace App\Policies;

use App\Models\Pilgrim;
use App\Models\User;

class PilgrimPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Rôle agence (dashboard agence) ou permission dédiée
        if ($user->hasRole('agence') || $user->hasPermissionTo('manage_own_pilgrims')) {
            return true;
        }
        return $user->hasAnyPermission(['view-pilgrims']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pilgrim $pilgrim): bool
    {
        // Chaque agence ne voit que ses propres pèlerins
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $pilgrim->agence_id != $userAgencyId) {
            return false;
        }

        // Utilisateur agence sans sous-rôle : accès selon sa branche (ou tout si pas de branche)
        if ($user->hasRole('agence') && !$user->hasAnyRole(['Super Admin Agence', 'Admin Branche', 'Agent Commercial', 'Comptable', 'Officier Visa', 'Guide / Mourchid'])) {
            return $user->branch_id === null || $user->branch_id === $pilgrim->branch_id;
        }

        // Super Admin Agence : limité à son agence (déjà vérifié ci-dessus)
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        // Superviseur Ministère et Auditeur voient tout (lecture seule)
        if ($user->hasAnyRole(['Superviseur Ministériel National', 'Auditeur National'])) {
            return true;
        }

        // Admin Branche voit sa branche
        if ($user->hasRole('Admin Branche') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        // Agent Commercial voit sa branche
        if ($user->hasRole('Agent Commercial') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        // Comptable voit sa branche
        if ($user->hasRole('Comptable') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        // Officier Visa voit sa branche
        if ($user->hasRole('Officier Visa') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        // Guide voit son groupe assigné (simplifié : sa branche)
        if ($user->hasRole('Guide / Mourchid') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        // Pèlerin voit seulement son propre dossier (liaison par email)
        if ($user->hasRole('Pèlerin (Client)')) {
            return $pilgrim->email === $user->email;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_pilgrims') || $user->hasPermissionTo('create-pilgrims');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pilgrim $pilgrim): bool
    {
        // Chaque agence ne modifie que ses propres pèlerins
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $pilgrim->agence_id != $userAgencyId) {
            return false;
        }

        // Rôle agence (sans sous-rôle) : modifier uniquement les pèlerins de sa branche
        if ($user->hasRole('agence') && !$user->hasAnyRole(['Super Admin Agence', 'Admin Branche', 'Agent Commercial', 'Comptable', 'Officier Visa', 'Guide / Mourchid'])) {
            return ($user->branch_id === null || $user->branch_id === $pilgrim->branch_id) && $user->hasPermissionTo('manage_own_pilgrims');
        }

        if (!$user->hasPermissionTo('edit-pilgrims')) {
            return false;
        }

        // Super Admin Agence : limité à son agence (déjà vérifié ci-dessus)
        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        // Admin Branche et Agent Commercial peuvent modifier leur branche
        if ($user->branch_id === $pilgrim->branch_id) {
            return $user->hasAnyRole(['Admin Branche', 'Agent Commercial']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pilgrim $pilgrim): bool
    {
        // Chaque agence ne supprime que ses propres pèlerins
        $userAgencyId = $user->agence_id ?? $user->branch?->agency_id;
        if ($userAgencyId && $pilgrim->agence_id != $userAgencyId) {
            return false;
        }

        // Rôle agence avec manage_own_pilgrims : supprimer uniquement dans sa branche (ou tout si pas de branche)
        if ($user->hasRole('agence') && $user->hasPermissionTo('manage_own_pilgrims')) {
            if ($user->hasRole('Super Admin Agence')) {
                return true;
            }
            if ($user->hasRole('Admin Branche') && $user->branch_id === $pilgrim->branch_id) {
                return true;
            }
            if (!$user->hasAnyRole(['Super Admin Agence', 'Admin Branche']) && ($user->branch_id === null || $user->branch_id === $pilgrim->branch_id)) {
                return true;
            }
        }

        if (!$user->hasPermissionTo('delete-pilgrims')) {
            return false;
        }

        if ($user->hasRole('Super Admin Agence')) {
            return true;
        }

        if ($user->hasRole('Admin Branche') && $user->branch_id === $pilgrim->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pilgrim $pilgrim): bool
    {
        return $this->delete($user, $pilgrim);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pilgrim $pilgrim): bool
    {
        // Soft delete uniquement, pas de suppression permanente
        return false;
    }
}
