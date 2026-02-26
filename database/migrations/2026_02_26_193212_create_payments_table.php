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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'transfer', 'tpe', 'mobile_money']);
            $table->enum('status', ['pending', 'completed', 'refunded'])->default('pending');
            $table->string('ref_no')->unique(); // numéro facture
            $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
            $table->date('payment_date');
            $table->timestamps();
            
            $table->index('pilgrim_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
