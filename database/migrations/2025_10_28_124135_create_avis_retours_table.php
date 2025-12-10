<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis_retours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avis_id')->constrained('avis')->cascadeOnDelete();
            $table->unsignedInteger('nbr_jours_consumes')->default(0);
            $table->date('date_retour_declaree')->nullable();
            $table->date('date_retour_effectif')->nullable();
            $table->string('statut')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis_retours');
    }
};

 

