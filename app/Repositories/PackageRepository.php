<?php

namespace App\Repositories;

use App\Models\Package;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PackageRepository
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Package::with(['branch'])
            ->withCount('pilgrims');

        // Filtre par branche
        if (Auth::user()->branch_id && !Auth::user()->hasRole('Super Admin Agence')) {
            $query->where('branch_id', Auth::user()->branch_id);
        }

        // Filtres
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['available'])) {
            $query->where('slots_remaining', '>', 0);
        }

        return $query->orderBy('departure_date', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Package
    {
        return Package::with(['branch', 'pilgrims'])
            ->find($id);
    }

    public function create(array $data): Package
    {
        // Initialiser slots_remaining = slots
        if (!isset($data['slots_remaining'])) {
            $data['slots_remaining'] = $data['slots'];
        }

        return Package::create($data);
    }

    public function update(Package $package, array $data): bool
    {
        return $package->update($data);
    }

    public function delete(Package $package): bool
    {
        return $package->delete();
    }

    public function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        $query = Package::where('slots_remaining', '>', 0)
            ->where('departure_date', '>=', now());

        if (Auth::user()->branch_id && !Auth::user()->hasRole('Super Admin Agence')) {
            $query->where('branch_id', Auth::user()->branch_id);
        }

        return $query->get();
    }
}
