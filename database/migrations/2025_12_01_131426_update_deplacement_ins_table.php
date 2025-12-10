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
        Schema::table('deplacement_ins', function (Blueprint $table) {
            // Drop old foreign keys and columns
            if (Schema::hasColumn('deplacement_ins', 'ppr')) {
                $table->dropForeign(['ppr']);
                $table->dropColumn('ppr');
            }
            if (Schema::hasColumn('deplacement_ins', 'date_debut')) {
                $table->dropColumn('date_debut');
            }
            if (Schema::hasColumn('deplacement_ins', 'date_retour')) {
                $table->dropColumn('date_retour');
            }
            if (Schema::hasColumn('deplacement_ins', 'nb_jours')) {
                $table->dropColumn('nb_jours');
            }
            if (Schema::hasColumn('deplacement_ins', 'sommes')) {
                $table->dropColumn('sommes');
            }
            if (Schema::hasColumn('deplacement_ins', 'annee')) {
                $table->dropColumn('annee');
            }
            if (Schema::hasColumn('deplacement_ins', 'entite_id')) {
                $table->dropForeign(['entite_id']);
                $table->dropColumn('entite_id');
            }
            if (Schema::hasColumn('deplacement_ins', 'echelle_id')) {
                $table->dropForeign(['echelle_id']);
                $table->dropColumn('echelle_id');
            }
            
            // Add new columns
            if (!Schema::hasColumn('deplacement_ins', 'deplacement_id')) {
                $table->foreignId('deplacement_id')->after('id')->constrained('deplacements')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('deplacement_ins', 'mois')) {
                $table->string('mois')->nullable()->after('objet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deplacement_ins', function (Blueprint $table) {
            // Restore old columns
            $table->dropForeign(['deplacement_id']);
            $table->dropColumn('deplacement_id');
            $table->dropColumn('mois');
            
            $table->string('ppr')->after('id');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_retour')->nullable();
            $table->unsignedInteger('nb_jours')->default(0);
            $table->string('sommes')->nullable();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->foreignId('echelle_id')->nullable()->constrained('echelles')->nullOnDelete();
        });
    }
};
