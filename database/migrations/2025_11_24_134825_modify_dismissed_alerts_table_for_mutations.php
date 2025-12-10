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
        Schema::table('dismissed_alerts', function (Blueprint $table) {
            // Drop the foreign key constraint on demande_id
            $table->dropForeign(['demande_id']);
            
            // Make demande_id nullable so it can be used for mutations too
            $table->unsignedBigInteger('demande_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dismissed_alerts', function (Blueprint $table) {
            // Make demande_id not nullable again
            $table->unsignedBigInteger('demande_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
        });
    }
};
