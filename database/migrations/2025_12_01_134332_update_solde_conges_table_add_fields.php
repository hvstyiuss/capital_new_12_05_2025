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
        Schema::table('solde_conges', function (Blueprint $table) {
            // Add solde_actuel field
            if (!Schema::hasColumn('solde_conges', 'solde_actuel')) {
                $table->unsignedInteger('solde_actuel')->default(0)->after('solde_fix');
            }
            
            // Add annee field
            if (!Schema::hasColumn('solde_conges', 'annee')) {
                $table->unsignedSmallInteger('annee')->nullable()->after('solde_actuel');
            }
            
            // Add solde_maladie field
            if (!Schema::hasColumn('solde_conges', 'solde_maladie')) {
                $table->unsignedInteger('solde_maladie')->default(0)->after('annee');
            }
            
            // Add solde_exceptionnel field
            if (!Schema::hasColumn('solde_conges', 'solde_exceptionnel')) {
                $table->unsignedInteger('solde_exceptionnel')->default(0)->after('solde_maladie');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solde_conges', function (Blueprint $table) {
            if (Schema::hasColumn('solde_conges', 'solde_actuel')) {
                $table->dropColumn('solde_actuel');
            }
            
            if (Schema::hasColumn('solde_conges', 'annee')) {
                $table->dropColumn('annee');
            }
            
            if (Schema::hasColumn('solde_conges', 'solde_maladie')) {
                $table->dropColumn('solde_maladie');
            }
            
            if (Schema::hasColumn('solde_conges', 'solde_exceptionnel')) {
                $table->dropColumn('solde_exceptionnel');
            }
        });
    }
};
