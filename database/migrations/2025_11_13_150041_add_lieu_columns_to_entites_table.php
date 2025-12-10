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
        Schema::table('entites', function (Blueprint $table) {
            $table->string('lieu_affectation')->nullable()->after('type');
            $table->string('lieu_direction')->nullable()->after('lieu_affectation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entites', function (Blueprint $table) {
            $table->dropColumn(['lieu_affectation', 'lieu_direction']);
        });
    }
};
