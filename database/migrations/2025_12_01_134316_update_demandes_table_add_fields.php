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
        Schema::table('demandes', function (Blueprint $table) {
            // Add type field (conge or mutation)
            if (!Schema::hasColumn('demandes', 'type')) {
                $table->enum('type', ['conge', 'mutation'])->nullable()->after('ppr');
            }
            
            // Add entite_id field
            if (!Schema::hasColumn('demandes', 'entite_id')) {
                $table->foreignId('entite_id')->nullable()->after('type')->constrained('entites')->nullOnDelete();
            }
            
            // Add created_by field (ppr of creator)
            if (!Schema::hasColumn('demandes', 'created_by')) {
                $table->string('created_by', 50)->nullable()->after('entite_id');
                $table->foreign('created_by')->references('ppr')->on('users')->nullOnDelete();
            }
            
            // Restore statut field if it was removed
            if (!Schema::hasColumn('demandes', 'statut')) {
                $table->enum('statut', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->after('created_by');
                $table->index('statut');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            if (Schema::hasColumn('demandes', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('demandes', 'entite_id')) {
                $table->dropForeign(['entite_id']);
                $table->dropColumn('entite_id');
            }
            
            if (Schema::hasColumn('demandes', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            
            // Note: We don't drop statut in down() as it may have existed before
        });
    }
};
