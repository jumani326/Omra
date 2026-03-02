<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_digitales', function (Blueprint $table) {
            $table->timestamp('validation_email_sent_at')->nullable()->after('pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_digitales', function (Blueprint $table) {
            $table->dropColumn('validation_email_sent_at');
        });
    }
};
