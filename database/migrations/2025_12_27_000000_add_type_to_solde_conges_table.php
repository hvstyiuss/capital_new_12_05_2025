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
            $table->string('type')->default('Congé Administratif Annuel')->after('ppr');
        });

        // Add unique constraint on ppr and type combination
        Schema::table('solde_conges', function (Blueprint $table) {
            $table->unique(['ppr', 'type'], 'solde_conges_ppr_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solde_conges', function (Blueprint $table) {
            $table->dropUnique('solde_conges_ppr_type_unique');
            $table->dropColumn('type');
        });
    }
};

