<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis_departs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avis_id')->constrained('avis')->cascadeOnDelete();
            $table->unsignedInteger('nb_jours_demandes')->default(0);
            $table->date('date_depart')->nullable();
            $table->date('date_retour')->nullable();
            $table->string('odf')->nullable();
            $table->string('statut')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis_departs');
    }
};

 

