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
        // Add is_chef to parcours table
        Schema::table('parcours', function (Blueprint $table) {
            $table->boolean('is_chef')->default(false)->after('reason');
        });

        // Remove chef from entites table
        Schema::table('entites', function (Blueprint $table) {
            $table->dropForeign(['chef']);
            $table->dropIndex(['chef']);
            $table->dropColumn('chef');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore chef to entites table
        Schema::table('entites', function (Blueprint $table) {
            $table->string('chef', 50)->nullable()->after('name');
            $table->foreign('chef')->references('ppr')->on('users')->nullOnDelete();
            $table->index('chef');
        });

        // Remove is_chef from parcours table
        Schema::table('parcours', function (Blueprint $table) {
            $table->dropColumn('is_chef');
        });
    }
};
