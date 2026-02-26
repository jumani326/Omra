<?php

namespace App\Services;

use App\Models\Package;
use App\Repositories\PackageRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageService
{
    public function __construct(
        private PackageRepository $repository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function findById(int $id): ?Package
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Package
    {
        // Assigner la branche de l'utilisateur si non spécifiée
        if (!isset($data['branch_id']) && Auth::user()->branch_id) {
            $data['branch_id'] = Auth::user()->branch_id;
        }

        // Calculer le profit automatiquement
        if (isset($data['price']) && isset($data['cost'])) {
            // Le profit sera calculé dans le Model ou ici si nécessaire
        }

        return $this->repository->create($data);
    }

    public function update(Package $package, array $data): bool
    {
        return $this->repository->update($package, $data);
    }

    public function delete(Package $package): bool
    {
        return $this->repository->delete($package);
    }

    public function clone(Package $package, array $overrides = []): Package
    {
        $data = $package->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        
        // Appliquer les overrides (ex: nouvelle date de départ)
        $data = array_merge($data, $overrides);
        
        // Réinitialiser les slots
        $data['slots_remaining'] = $data['slots'];

        return $this->repository->create($data);
    }

    public function updateSlots(Package $package, int $pilgrimsCount): void
    {
        $package->update([
            'slots_remaining' => max(0, $package->slots - $pilgrimsCount)
        ]);
    }

    public function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getAvailable();
    }
}
