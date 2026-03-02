<?php

namespace App\Observers;

use App\Models\Pilgrim;
use App\Models\ActivityLog;
use App\Models\Notification;
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
            $old = $pilgrim->getOriginal('status');
            $new = $changes['status'];

            ActivityLog::create([
                'pilgrim_id' => $pilgrim->id,
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'description' => "Statut changé de '{$old}' à '{$new}'",
            ]);

            // Créer une notification pour le pèlerin lorsque l'agence met à jour son statut
            if ($pilgrim->user_id) {
                $labelOld = $old ? ucfirst(str_replace('_', ' ', $old)) : '—';
                $labelNew = ucfirst(str_replace('_', ' ', $new));

                $message = "Votre dossier a été mis à jour : statut « {$labelOld} » → « {$labelNew} ».";

                Notification::create([
                    'user_id' => $pilgrim->user_id,
                    'type' => 'push',
                    'channel' => 'push',
                    'content' => $message,
                    'sent_at' => now(),
                ]);
            }
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
