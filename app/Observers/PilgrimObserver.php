<?php

namespace App\Observers;

use App\Models\Pilgrim;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class PilgrimObserver
{
    /**
     * Handle the Pilgrim "created" event.
     */
    public function created(Pilgrim $pilgrim): void
    {
        ActivityLog::create([
            'pilgrim_id' => $pilgrim->id,
            'user_id' => Auth::id(),
            'action' => 'created_pilgrim',
            'description' => "Pèlerin créé : {$pilgrim->first_name} {$pilgrim->last_name}",
        ]);
    }

    /**
     * Handle the Pilgrim "updated" event.
     */
    public function updated(Pilgrim $pilgrim): void
    {
        $changes = $pilgrim->getChanges();
        
        // Logger seulement les changements importants
        if (isset($changes['status'])) {
            ActivityLog::create([
                'pilgrim_id' => $pilgrim->id,
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => "Statut changé de '{$pilgrim->getOriginal('status')}' à '{$changes['status']}'",
            ]);
        }

        if (isset($changes['package_id'])) {
            ActivityLog::create([
                'pilgrim_id' => $pilgrim->id,
                'user_id' => Auth::id(),
                'action' => 'package_assigned',
                'description' => "Forfait assigné (ID: {$changes['package_id']})",
            ]);
        }
    }

    /**
     * Handle the Pilgrim "deleted" event.
     */
    public function deleted(Pilgrim $pilgrim): void
    {
        ActivityLog::create([
            'pilgrim_id' => $pilgrim->id,
            'user_id' => Auth::id(),
            'action' => 'deleted_pilgrim',
            'description' => "Pèlerin supprimé : {$pilgrim->first_name} {$pilgrim->last_name}",
        ]);
    }

    /**
     * Handle the Pilgrim "restored" event.
     */
    public function restored(Pilgrim $pilgrim): void
    {
        ActivityLog::create([
            'pilgrim_id' => $pilgrim->id,
            'user_id' => Auth::id(),
            'action' => 'restored_pilgrim',
            'description' => "Pèlerin restauré : {$pilgrim->first_name} {$pilgrim->last_name}",
        ]);
    }

    /**
     * Handle the Pilgrim "force deleted" event.
     */
    public function forceDeleted(Pilgrim $pilgrim): void
    {
        // Ne pas logger la suppression définitive (ne devrait jamais arriver)
    }
}
