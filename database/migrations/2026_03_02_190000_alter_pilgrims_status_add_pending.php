<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ajouter le statut 'pending' au champ enum pilgrims.status pour les demandes en attente de validation.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `pilgrims`
            MODIFY `status` ENUM(
                'pending',
                'registered',
                'dossier_complete',
                'visa_submitted',
                'visa_approved',
                'departed',
                'returned'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    /**
     * Revenir à la définition d'origine (sans 'pending').
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `pilgrims`
            MODIFY `status` ENUM(
                'registered',
                'dossier_complete',
                'visa_submitted',
                'visa_approved',
                'departed',
                'returned'
            ) NOT NULL DEFAULT 'registered'
        ");
    }
};

