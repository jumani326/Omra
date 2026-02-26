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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['economic', 'standard', 'premium', 'vip']);
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2); // coût de base
            $table->integer('slots'); // capacité max
            $table->integer('slots_remaining');
            $table->date('departure_date');
            $table->date('return_date');
            $table->foreignId('hotel_mecca_id')->nullable()->constrained('hotels')->onDelete('set null');
            $table->foreignId('hotel_medina_id')->nullable()->constrained('hotels')->onDelete('set null');
            $table->integer('nights_mecca');
            $table->integer('nights_medina');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('branch_id');
            $table->index('departure_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
