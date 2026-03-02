<?php

namespace App\Services;

use App\Models\Visa;
use App\Models\Pilgrim;
use App\Repositories\VisaRepository;
use App\Mail\VisaApprovedMail;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class VisaService
{
    public function __construct(
        private VisaRepository $repository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function findById(int $id): ?Visa
    {
        return $this->repository->findById($id);
    }

    public function findByPilgrimId(int $pilgrimId): ?Visa
    {
        return $this->repository->findByPilgrimId($pilgrimId);
    }

    public function create(array $data): Visa
    {
        return DB::transaction(function () use ($data) {
            $visa = $this->repository->create($data);
            if (!empty($data['documents_upload'])) {
                $this->uploadDocuments($visa, $data['documents_upload']);
            }
            return $visa;
        });
    }

    public function update(Visa $visa, array $data): bool
    {
        return DB::transaction(function () use ($visa, $data) {
            $wasApproved = $visa->status === 'approved';
            $newStatus = $data['status'] ?? $visa->status;

            if ($newStatus === 'submitted' && !isset($data['submitted_at'])) {
                $data['submitted_at'] = now();
            }
            if (in_array($newStatus, ['approved', 'refused']) && !isset($data['decision_at'])) {
                $data['decision_at'] = now();
            }

            $updated = $this->repository->update($visa, $data);
            if (!empty($data['documents_upload'])) {
                $this->uploadDocuments($visa, $data['documents_upload']);
            }

            // Quand le statut passe à "approved" depuis le formulaire Modifier : notification + email PDF
            if ($updated && !$wasApproved && $newStatus === 'approved') {
                $visa->refresh();
                $pilgrim = $visa->pilgrim()->with(['user', 'package'])->first();
                if ($pilgrim) {
                    if ($pilgrim->status !== 'visa_approved') {
                        $pilgrim->update(['status' => 'visa_approved']);
                    }
                    if ($pilgrim->user_id) {
                        Notification::create([
                            'user_id' => $pilgrim->user_id,
                            'type' => 'visa',
                            'channel' => 'in_app',
                            'content' => sprintf(
                                'Votre visa pour le forfait "%s" a été approuvé. Vous pouvez maintenant consulter vos documents et procéder au paiement du solde.',
                                $pilgrim->package?->name ?? 'Omra'
                            ),
                            'sent_at' => now(),
                        ]);
                    }
                    $email = $pilgrim->email ?: $pilgrim->user?->email;
                    if ($email) {
                        Mail::to($email)->send(new VisaApprovedMail($pilgrim, $visa));
                    }
                }
            }

            return $updated;
        });
    }

    public function delete(Visa $visa): bool
    {
        if ($visa->documents) {
            foreach ((array) $visa->documents as $path) {
                if (is_string($path) && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
        return $this->repository->delete($visa);
    }

    public function updateStatus(Visa $visa, string $status, array $extra = []): bool
    {
        return DB::transaction(function () use ($visa, $status, $extra) {
            $wasApproved = $visa->status === 'approved';

            $data = array_merge(['status' => $status], $extra);
            if ($status === 'submitted') {
                $data['submitted_at'] = $data['submitted_at'] ?? now();
            }
            if (in_array($status, ['approved', 'refused'])) {
                $data['decision_at'] = $data['decision_at'] ?? now();
            }

            $updated = $this->repository->update($visa, $data);

            if ($updated && !$wasApproved && $status === 'approved') {
                $visa->refresh();
                $pilgrim = $visa->pilgrim()->with(['user', 'package'])->first();

                if ($pilgrim) {
                    // Synchroniser le statut du pèlerin
                    if ($pilgrim->status !== 'visa_approved') {
                        $pilgrim->update(['status' => 'visa_approved']);
                    }

                    // Créer une notification dans l'espace pèlerin
                    if ($pilgrim->user_id) {
                        Notification::create([
                            'user_id' => $pilgrim->user_id,
                            'type' => 'visa',
                            'channel' => 'in_app',
                            'content' => sprintf(
                                'Votre visa pour le forfait "%s" a été approuvé. Vous pouvez maintenant consulter vos documents et procéder au paiement du solde.',
                                $pilgrim->package?->name ?? 'Omra'
                            ),
                            'sent_at' => now(),
                        ]);
                    }

                    // Envoyer le visa par email au client (pièces jointes)
                    $email = $pilgrim->email ?: $pilgrim->user?->email;
                    if ($email) {
                        Mail::to($email)->send(new VisaApprovedMail($pilgrim, $visa));
                    }
                }
            }

            return $updated;
        });
    }

    private function uploadDocuments(Visa $visa, array $files): void
    {
        $paths = $visa->documents ?? [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store("visas/{$visa->id}/documents", 'public');
            }
        }
        $visa->update(['documents' => $paths]);
    }
}
