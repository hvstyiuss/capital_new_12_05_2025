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
        Schema::create('dismissed_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('demande_id');
            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
            $table->string('alert_type')->default('status_change'); // 'status_change' or other types
            $table->timestamps();
            
            // Ensure a user can only dismiss an alert once per demande
            $table->unique(['ppr', 'demande_id', 'alert_type']);
            $table->index('ppr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dismissed_alerts');
    }
};
