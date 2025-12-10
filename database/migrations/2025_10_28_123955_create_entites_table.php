<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entites', function (Blueprint $table) {
            $table->id();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('name', 255);
            $table->string('chef', 50)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('chef')->references('ppr')->on('users')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('entites')->cascadeOnDelete();
            
            $table->index('name');
            $table->index('chef');
            $table->index('parent_id');
            $table->index('date_debut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entites');
    }
};

 