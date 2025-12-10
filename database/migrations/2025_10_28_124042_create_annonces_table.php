<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->string('statut')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->nullable();
            $table->string('ppr');
            $table->string('created_by');
            $table->foreign('created_by')->references('ppr')->on('users');
            $table->foreign('ppr')->references('ppr')->on('users')->nullable();
            $table->foreignId('entite_id')->nullable()->constrained('entites')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};

 