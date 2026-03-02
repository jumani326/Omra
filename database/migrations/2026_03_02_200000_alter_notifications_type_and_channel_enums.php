<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étend les valeurs possibles pour coller aux usages actuels de l'application
        DB::statement("
            ALTER TABLE notifications
            MODIFY COLUMN type ENUM('email', 'sms', 'push', 'visa', 'agency') NOT NULL
        ");

        DB::statement("
            ALTER TABLE notifications
            MODIFY COLUMN channel ENUM('email', 'sms', 'push', 'in_app') NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revient à la définition initiale
        DB::statement("
            ALTER TABLE notifications
            MODIFY COLUMN type ENUM('email', 'sms', 'push') NOT NULL
        ");

        DB::statement("
            ALTER TABLE notifications
            MODIFY COLUMN channel ENUM('email', 'sms', 'push') NOT NULL
        ");
    }
};

