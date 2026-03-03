<?php

namespace App\Repositories;

use App\Models\Pilgrim;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PilgrimRepository
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Pilgrim::with(['branch', 'agent', 'package', 'visa'])
            ->withCount('payments');

        // Filtre par branche ou par agence (chaque agence ne voit que ses pèlerins)
        $user = Auth::user();
        $branchId = $user->hasRole('Super Admin Agence')
            ? session('current_branch_id')
            : $user->branch_id;
        if ($branchId) {
            $query->where('branch_id', $branchId);
        } else {
            $agencyId = $user->agence_id ?? $user->branch?->agency_id;
            if ($agencyId) {
                $query->where('agence_id', $agencyId);
            }
        }

        // Filtres
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        if (isset($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (isset($filters['nationality'])) {
            $query->where('nationality', $filters['nationality']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('passport_no', 'like', "%{$search}%");
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Pilgrim
    {
        return Pilgrim::with(['branch', 'agent', 'package', 'visa', 'payments', 'documents', 'activityLogs'])
            ->find($id);
    }

    public function create(array $data): Pilgrim
    {
        return Pilgrim::create($data);
    }

    public function update(Pilgrim $pilgrim, array $data): bool
    {
        return $pilgrim->update($data);
    }

    public function delete(Pilgrim $pilgrim): bool
    {
        return $pilgrim->delete();
    }

    public function getByStatus(string $status): Collection
    {
        $query = Pilgrim::where('status', $status);
        $user = Auth::user();
        $branchId = $user->hasRole('Super Admin Agence')
            ? session('current_branch_id')
            : $user->branch_id;
        if ($branchId) {
            $query->where('branch_id', $branchId);
        } else {
            $agencyId = $user->agence_id ?? $user->branch?->agency_id;
            if ($agencyId) {
                $query->where('agence_id', $agencyId);
            }
        }
        return $query->get();
    }
}
