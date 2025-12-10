<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hors_baremes', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->unsignedInteger('nb_jours')->default(0);
            $table->foreignId('deplacement_periode_id')->nullable()->constrained('deplacement_periodes')->nullOnDelete();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->string('categorie')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hors_baremes');
    }
};

 

