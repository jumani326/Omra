<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Commission;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    private const COMMISSION_RATE = 5; // 5% pour l'agent

    public function __construct(
        private PaymentRepository $repository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function findById(int $id): ?Payment
    {
        return $this->repository->findById($id);
    }

    public function getNextRefNo(): string
    {
        return $this->repository->getNextRefNo();
    }

    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['ref_no'])) {
                $data['ref_no'] = $this->repository->getNextRefNo();
            }
            $data['processed_by'] = $data['processed_by'] ?? Auth::id();
            $payment = $this->repository->create($data);

            if (($data['status'] ?? '') === 'completed' && $payment->pilgrim->agent_id) {
                $this->createCommission($payment);
            }
            return $payment;
        });
    }

    public function update(Payment $payment, array $data): bool
    {
        $wasCompleted = $payment->status === 'completed';
        return DB::transaction(function () use ($payment, $data, $wasCompleted) {
            $updated = $this->repository->update($payment, $data);
            $payment->refresh();
            if ($payment->status === 'completed' && !$wasCompleted && $payment->pilgrim->agent_id) {
                $this->createCommission($payment);
            }
            return $updated;
        });
    }

    private function createCommission(Payment $payment): void
    {
        $amount = $payment->amount * (self::COMMISSION_RATE / 100);
        if ($amount <= 0) {
            return;
        }
        Commission::firstOrCreate(
            [
                'pilgrim_id' => $payment->pilgrim_id,
                'agent_id' => $payment->pilgrim->agent_id,
            ],
            [
                'amount' => $amount,
                'rate' => self::COMMISSION_RATE,
                'status' => 'pending',
            ]
        );
    }
}
