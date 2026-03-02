<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_digitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compte_marchand_id')->constrained('compte_marchands')->onDelete('cascade');
            $table->foreignId('pilgrim_id')->nullable()->constrained('pilgrims')->onDelete('set null');
            $table->decimal('montant', 12, 2);
            $table->string('client_nom')->nullable(); // Nom du client si pas de pilgrim lié
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('compte_marchand_id');
            $table->index('statut');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_digitales');
    }
};
