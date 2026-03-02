<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    protected function getAgencyId(User $user): ?int
    {
        return $user->agence_id ?? $user->branch?->agency_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('agence');
    }

    public function view(User $user, Group $group): bool
    {
        return $this->getAgencyId($user) && $group->agency_id === $this->getAgencyId($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agence');
    }

    public function update(User $user, Group $group): bool
    {
        return $this->getAgencyId($user) && $group->agency_id === $this->getAgencyId($user);
    }

    public function delete(User $user, Group $group): bool
    {
        return $this->getAgencyId($user) && $group->agency_id === $this->getAgencyId($user);
    }
}
