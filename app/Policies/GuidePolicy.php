<?php

namespace App\Policies;

use App\Models\Guide;
use App\Models\User;

class GuidePolicy
{
    protected function getAgencyId(User $user): ?int
    {
        return $user->agence_id ?? $user->branch?->agency_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_guides');
    }

    public function view(User $user, Guide $guide): bool
    {
        $agencyId = $this->getAgencyId($user);
        return $agencyId && $guide->agency_id === $agencyId;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence') || $user->hasPermissionTo('manage_own_guides');
    }

    public function update(User $user, Guide $guide): bool
    {
        $agencyId = $this->getAgencyId($user);
        return $agencyId && $guide->agency_id === $agencyId;
    }

    public function delete(User $user, Guide $guide): bool
    {
        $agencyId = $this->getAgencyId($user);
        return $agencyId && $guide->agency_id === $agencyId;
    }
}
