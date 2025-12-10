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
        Schema::create('conge_maladies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_conge_id')->constrained('demande_conges')->cascadeOnDelete();
            $table->foreignId('type_maladie_id')->nullable()->constrained('type_maladies')->nullOnDelete();
            $table->date('date_declaration')->nullable();
            $table->date('date_constatation')->nullable();
            $table->date('date_prolongation')->nullable();
            $table->date('date_reprise_travail')->nullable();
            $table->unsignedInteger('nbr_jours_arret')->default(0);
            $table->unsignedInteger('nbr_jours_prolongation')->default(0);
            $table->unsignedInteger('nbr_jours_total')->default(0);
            $table->string('reference_arret')->nullable();
            $table->string('reference_prolongation')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();
            
            $table->index('demande_conge_id');
            $table->index('type_maladie_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conge_maladies');
    }
};
