<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deplacement_outs', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->string('objet')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_retour')->nullable();
            $table->unsignedInteger('nb_jours')->default(0);
            $table->string('sommes')->nullable();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->foreignId('echelle_id')->nullable()->constrained('echelles')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deplacement_outs');
    }
};

 

