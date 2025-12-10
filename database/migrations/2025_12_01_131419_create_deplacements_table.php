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
        Schema::create('deplacements', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->unsignedInteger('nbr_jours')->default(0);
            $table->foreignId('echelle_tarifs_id')->nullable()->constrained('echelle_tarifs')->nullOnDelete();
            $table->decimal('somme', 10, 2)->nullable();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->enum('type_in_out', ['in', 'out'])->default('in');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deplacements');
    }
};
