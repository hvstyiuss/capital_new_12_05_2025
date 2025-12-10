<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_annuelles', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->unsignedTinyInteger('note')->nullable();
            $table->string('note_par', 50)->nullable();
            $table->foreign('note_par')->references('ppr')->on('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('ppr');
            $table->index('annee');
            $table->unique(['ppr', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_annuelles');
    }
};

 