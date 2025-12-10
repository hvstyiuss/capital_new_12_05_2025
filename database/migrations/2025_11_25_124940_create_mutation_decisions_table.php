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
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['mutation', 'other'])->default('mutation'); // Type de décision
            $table->unsignedBigInteger('reference_id'); // ID de référence (mutation_id pour type='mutation', etc.)
            $table->string('collaborateur_rh_ppr', 50);
            $table->foreign('collaborateur_rh_ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->date('date_affectation'); // Date choisie par le collaborateur RH pour l'affectation
            $table->text('decision_text')->nullable(); // Texte de la décision/instructions
            $table->timestamps();
            
            // Un collaborateur RH ne peut créer qu'une seule décision par référence
            $table->unique(['type', 'reference_id']);
            $table->index('collaborateur_rh_ppr');
            $table->index(['type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
