<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobilites', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('statut')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobilites');
    }
};

 

