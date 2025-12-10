<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solde_conges', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->unsignedInteger('solde_precedent')->default(0);
            $table->unsignedInteger('solde_fix')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solde_conges');
    }
};

 

