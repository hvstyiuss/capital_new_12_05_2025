<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->string('ppr');
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->string('photo')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->nullable();
            $table->string('cin')->nullable();
            $table->string('rib')->nullable();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->string('corps')->nullable();
            $table->string('statut_retrait')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};

 