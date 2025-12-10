<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('to_entite_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->boolean('is_validated_ent')->default(false);
            $table->string('decision_conducteur_rh')->nullable();
            $table->boolean('valide_reception')->default(false);
            $table->string('valide_par')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutations');
    }
};

 