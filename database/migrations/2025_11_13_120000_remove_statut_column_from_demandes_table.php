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
        Schema::table('demandes', function (Blueprint $table) {
            // Drop the index first
            $table->dropIndex(['statut']);
            // Then drop the column
            $table->dropColumn('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->enum('statut', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->after('date_debut');
            $table->index('statut');
        });
    }
};











