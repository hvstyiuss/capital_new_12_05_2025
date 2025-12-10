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
        Schema::table('entite_infos', function (Blueprint $table) {
            // Check if column exists before dropping
            if (Schema::hasColumn('entite_infos', 'lieu')) {
                $table->dropColumn('lieu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entite_infos', function (Blueprint $table) {
            // Re-add lieu column if needed (nullable string)
            if (!Schema::hasColumn('entite_infos', 'lieu')) {
                $table->string('lieu')->nullable()->after('description');
            }
        });
    }
};
