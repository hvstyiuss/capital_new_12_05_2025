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
        Schema::create('conge_exceptionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_conge_id')->constrained('demande_conges')->cascadeOnDelete();
            $table->foreignId('type_exceptionnel_id')->nullable()->constrained('type_exceps')->nullOnDelete();
            $table->timestamps();
            
            $table->index('demande_conge_id');
            $table->index('type_exceptionnel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conge_exceptionnels');
    }
};
