<?php

namespace App\Repositories;

use App\Models\Visa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class VisaRepository
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Visa::with(['pilgrim.branch', 'pilgrim.agent', 'pilgrim.package']);

        $user = Auth::user();
        $branchId = $user->hasRole('Super Admin Agence') ? session('current_branch_id')
            : ($user->hasRole(['Superviseur Ministériel National', 'Auditeur National']) ? null : $user->branch_id);
        if ($branchId) {
            $query->whereHas('pilgrim', fn ($q) => $q->where('branch_id', $branchId));
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['pilgrim_id'])) {
            $query->where('pilgrim_id', $filters['pilgrim_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('pilgrim', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('passport_no', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['expiring_soon'])) {
            $query->where('expiry_date', '<=', now()->addDays(30))
                  ->where('expiry_date', '>=', now());
        }

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Visa
    {
        return Visa::with(['pilgrim.branch', 'pilgrim.agent', 'pilgrim.package'])->find($id);
    }

    public function findByPilgrimId(int $pilgrimId): ?Visa
    {
        return Visa::with('pilgrim')->where('pilgrim_id', $pilgrimId)->first();
    }

    public function create(array $data): Visa
    {
        return Visa::create($data);
    }

    public function update(Visa $visa, array $data): bool
    {
        return $visa->update($data);
    }

    public function delete(Visa $visa): bool
    {
        return $visa->delete();
    }
}
