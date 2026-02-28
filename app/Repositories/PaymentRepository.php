<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PaymentRepository
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::with(['pilgrim.branch', 'pilgrim.package', 'processedBy']);

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

        if (isset($filters['method'])) {
            $query->where('method', $filters['method']);
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

        if (!empty($filters['ref_no'])) {
            $query->where('ref_no', 'like', '%' . $filters['ref_no'] . '%');
        }

        return $query->orderBy('payment_date', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Payment
    {
        return Payment::with(['pilgrim.branch', 'pilgrim.package', 'processedBy'])->find($id);
    }

    public function getNextRefNo(): string
    {
        $year = date('Y');
        $last = Payment::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $seq = $last ? (int) substr($last->ref_no, -4) + 1 : 1;
        return 'INV-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }
}
