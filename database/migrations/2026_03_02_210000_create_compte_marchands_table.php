<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compte_marchands', function (Blueprint $table) {
            $table->id();
            $table->string('nom_methode', 50); // D-money, Waafi, MyCac
            $table->string('numero_compte');
            $table->string('nom_agence');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->decimal('solde', 12, 2)->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('nom_methode');
            $table->index('branch_id');
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compte_marchands');
    }
};
