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
        Schema::create('demande_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demandes')->cascadeOnDelete();
            $table->foreignId('entite_source_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->foreignId('entite_destination_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->boolean('is_validated_envoi')->default(false);
            $table->string('decision_collaborateur_rh')->nullable();
            $table->boolean('is_validated_reception')->default(false);
            $table->string('valide_par', 50)->nullable();
            $table->foreign('valide_par')->references('ppr')->on('users')->nullOnDelete();
            $table->string('poste_actuel')->nullable();
            $table->string('poste_souhaite')->nullable();
            $table->date('date_effet_souhaitee')->nullable();
            $table->text('motif_demande')->nullable();
            $table->timestamps();
            
            $table->index('demande_id');
            $table->index('entite_source_id');
            $table->index('entite_destination_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_mutations');
    }
};
