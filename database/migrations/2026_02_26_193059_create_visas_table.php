<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->onDelete('cascade');
            $table->enum('status', ['not_submitted', 'submitted', 'processing', 'approved', 'refused'])->default('not_submitted');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('decision_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('refusal_reason')->nullable();
            $table->string('reference_no')->nullable();
            $table->json('documents')->nullable(); // upload documents consulaires
            $table->timestamps();
            
            $table->index('pilgrim_id');
            $table->index('status');
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visas');
    }
};
