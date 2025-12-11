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
            // Add reference_decision column if it doesn't exist
            if (!Schema::hasColumn('solde_conges', 'reference_decision')) {
                $table->string('reference_decision')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solde_conges', function (Blueprint $table) {
            // Drop reference_decision column if it exists
            if (Schema::hasColumn('solde_conges', 'reference_decision')) {
                $table->dropColumn('reference_decision');
            }
        });
    }
};
