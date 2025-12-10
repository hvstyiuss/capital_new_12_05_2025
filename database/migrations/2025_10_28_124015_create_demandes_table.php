<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->date('date_debut')->nullable();
            $table->enum('statut', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('ppr');
            $table->index('statut');
            $table->index('date_debut');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};

 