<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Structure des 4 rôles : agence, ministere, guide, pelerin.
     * Guide créé par l'agence, un guide n'appartient qu'à une agence.
     */
    public function up(): void
    {
        // Agencies : validation par le ministère
        Schema::table('agencies', function (Blueprint $table) {
            $table->boolean('validated')->default(false)->after('ministry_status');
            $table->foreignId('validated_by')->nullable()->after('validated')->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable()->after('validated_by');
        });

        // Users : agence_id pour guide/pèlerin, activation pour inscription
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agence_id')->nullable()->after('active')->constrained('agencies')->onDelete('set null');
            $table->string('activation_code', 64)->nullable()->after('active');
            $table->timestamp('activation_code_expires_at')->nullable()->after('activation_code');
            $table->timestamp('activated_at')->nullable()->after('activation_code_expires_at');
            $table->index('agence_id');
        });

        // Groupes (par agence) — assignation des guides et pèlerins
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            $table->index('agency_id');
        });

        // Guides : créés par l'agence, un guide n'appartient qu'à une agence
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
            $table->timestamps();
            $table->index('agency_id');
            $table->index('group_id');
        });

        // Pilgrims : user_id (compte client), agence_id, group_id, guide_id
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->foreignId('agence_id')->nullable()->after('branch_id')->constrained('agencies')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->after('agence_id')->constrained('groups')->onDelete('set null');
            $table->foreignId('guide_id')->nullable()->after('group_id')->constrained('guides')->onDelete('set null');
        });

        // Check-ins : traçabilité check-in/check-out par le guide
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilgrim_id')->constrained('pilgrims')->onDelete('cascade');
            $table->foreignId('guide_id')->constrained('guides')->onDelete('cascade');
            $table->enum('type', ['checkin', 'checkout']);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['pilgrim_id', 'guide_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins');
        Schema::table('pilgrims', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['agence_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['guide_id']);
            $table->dropColumn(['user_id', 'agence_id', 'group_id', 'guide_id']);
        });
        Schema::dropIfExists('guides');
        Schema::dropIfExists('groups');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
            $table->dropColumn(['agence_id', 'activation_code', 'activation_code_expires_at', 'activated_at']);
        });
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validated', 'validated_by', 'validated_at']);
        });
    }
};
