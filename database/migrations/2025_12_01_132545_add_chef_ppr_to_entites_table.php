<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entites', function (Blueprint $table) {
            $table->string('chef_ppr', 50)->nullable()->after('name');
            $table->foreign('chef_ppr')->references('ppr')->on('users')->nullOnDelete();
            $table->index('chef_ppr');
        });

        // Migrate data from parcours to entites
        // Set chef_ppr in entites based on active parcours with is_chef = true
        DB::statement("
            UPDATE entites e
            INNER JOIN (
                SELECT p.entite_id, p.ppr
                FROM parcours p
                WHERE p.is_chef = 1
                AND (p.date_fin IS NULL OR p.date_fin >= CURDATE())
                AND p.id = (
                    SELECT p2.id
                    FROM parcours p2
                    WHERE p2.entite_id = p.entite_id
                    AND p2.is_chef = 1
                    AND (p2.date_fin IS NULL OR p2.date_fin >= CURDATE())
                    ORDER BY p2.date_debut DESC
                    LIMIT 1
                )
            ) AS chef_data ON e.id = chef_data.entite_id
            SET e.chef_ppr = chef_data.ppr
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entites', function (Blueprint $table) {
            $table->dropForeign(['chef_ppr']);
            $table->dropIndex(['chef_ppr']);
            $table->dropColumn('chef_ppr');
        });
    }
};
