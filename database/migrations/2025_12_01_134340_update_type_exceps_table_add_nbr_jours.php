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
        Schema::table('type_exceps', function (Blueprint $table) {
            if (!Schema::hasColumn('type_exceps', 'nbr_jours')) {
                $table->unsignedInteger('nbr_jours')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_exceps', function (Blueprint $table) {
            if (Schema::hasColumn('type_exceps', 'nbr_jours')) {
                $table->dropColumn('nbr_jours');
            }
        });
    }
};
