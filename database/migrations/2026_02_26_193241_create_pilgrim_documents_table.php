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
        Schema::create('pilgrim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->onDelete('cascade');
            $table->enum('type', ['passport', 'photo', 'medical_certificate', 'other']);
            $table->string('file_path');
            $table->dateTime('uploaded_at');
            $table->timestamps();
            
            $table->index('pilgrim_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilgrim_documents');
    }
};
