<?php

namespace App\Services;

use App\Models\Pilgrim;
use App\Repositories\PilgrimRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PilgrimService
{
    public function __construct(
        private PilgrimRepository $repository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function findById(int $id): ?Pilgrim
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Pilgrim
    {
        // Assigner la branche de l'utilisateur si non spécifiée
        if (!isset($data['branch_id']) && Auth::user()->branch_id) {
            $data['branch_id'] = Auth::user()->branch_id;
        }

        // Assigner l'agent si non spécifié
        if (!isset($data['agent_id']) && Auth::user()->hasRole(['Agent Commercial', 'Admin Branche'])) {
            $data['agent_id'] = Auth::user()->id;
        }

        // Statut initial
        $data['status'] = $data['status'] ?? 'registered';

        return DB::transaction(function () use ($data) {
            $pilgrim = $this->repository->create($data);
            
            // Upload des documents si présents
            if (isset($data['documents'])) {
                $this->uploadDocuments($pilgrim, $data['documents']);
            }

            return $pilgrim;
        });
    }

    public function update(Pilgrim $pilgrim, array $data): bool
    {
        return DB::transaction(function () use ($pilgrim, $data) {
            $updated = $this->repository->update($pilgrim, $data);
            
            // Upload de nouveaux documents si présents
            if (isset($data['documents'])) {
                $this->uploadDocuments($pilgrim, $data['documents']);
            }

            return $updated;
        });
    }

    public function delete(Pilgrim $pilgrim): bool
    {
        return $this->repository->delete($pilgrim);
    }

    public function updateStatus(Pilgrim $pilgrim, string $status): bool
    {
        return $this->repository->update($pilgrim, ['status' => $status]);
    }

    private function uploadDocuments(Pilgrim $pilgrim, array $documents): void
    {
        foreach ($documents as $type => $file) {
            if ($file && $file->isValid()) {
                $path = $file->store("pilgrims/{$pilgrim->id}/documents", 'public');
                
                $pilgrim->documents()->create([
                    'type' => $type,
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }
    }

    public function export(array $filters = [])
    {
        // À implémenter avec Maatwebsite Excel
        return $this->repository->getAll($filters, 1000);
    }
}
