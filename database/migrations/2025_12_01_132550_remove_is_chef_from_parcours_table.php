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
        Schema::table('parcours', function (Blueprint $table) {
            if (Schema::hasColumn('parcours', 'is_chef')) {
                $table->dropColumn('is_chef');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcours', function (Blueprint $table) {
            if (!Schema::hasColumn('parcours', 'is_chef')) {
                $table->boolean('is_chef')->default(false)->after('reason');
            }
        });
    }
};
