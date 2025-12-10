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
        Schema::table('echelle_tarifs', function (Blueprint $table) {
            $table->unsignedInteger('max_jours')->nullable()->after('montant_deplacement')->default(9);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echelle_tarifs', function (Blueprint $table) {
            $table->dropColumn('max_jours');
        });
    }
};



