<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnuellesTable extends Migration
{
    public function up(): void
    {
        Schema::create('annuelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->timestamps();
            $table->index('annee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annuelles');
    }
}

 