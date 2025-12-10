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
        Schema::table('annonces', function (Blueprint $table) {
            // Add type_annonce_id column
            $table->foreignId('type_annonce_id')->nullable()->after('id')->constrained('type_annonces')->nullOnDelete();
            
            // Remove old columns if they exist
            if (Schema::hasColumn('annonces', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('annonces', 'entite_id')) {
                $table->dropForeign(['entite_id']);
                $table->dropColumn('entite_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annonces', function (Blueprint $table) {
            // Revert changes
            if (Schema::hasColumn('annonces', 'type_annonce_id')) {
                $table->dropForeign(['type_annonce_id']);
                $table->dropColumn('type_annonce_id');
            }
            
            // Re-add old columns
            if (!Schema::hasColumn('annonces', 'type')) {
                $table->string('type')->nullable()->after('image');
            }
            if (!Schema::hasColumn('annonces', 'entite_id')) {
                $table->foreignId('entite_id')->nullable()->after('ppr')->constrained('entites')->nullOnDelete();
            }
        });
    }
};
