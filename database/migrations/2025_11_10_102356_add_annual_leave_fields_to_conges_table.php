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
        Schema::table('conges', function (Blueprint $table) {
            $table->string('reference_decision')->nullable()->after('annee');
            $table->unsignedSmallInteger('reliquat_annee_anterieure')->default(0)->after('reference_decision');
            $table->unsignedSmallInteger('reliquat_annee_courante')->default(0)->after('reliquat_annee_anterieure');
            $table->unsignedSmallInteger('cumul_jours_consommes')->default(0)->after('reliquat_annee_courante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conges', function (Blueprint $table) {
            $table->dropColumn([
                'reference_decision',
                'reliquat_annee_anterieure',
                'reliquat_annee_courante',
                'cumul_jours_consommes',
            ]);
        });
    }
};
