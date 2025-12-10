<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcours', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->foreignId('entite_id')->constrained('entites')->cascadeOnDelete();
            $table->string('poste', 255)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->string('reason', 100)->nullable();
            $table->timestamps();
            $table->unique(['ppr','entite_id']);
            
            $table->index('ppr');
            $table->index('entite_id');
            $table->index('date_debut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcours');
    }
};

 