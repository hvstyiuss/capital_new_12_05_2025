<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
            
            $table->index('ordre');
        });

        Schema::create('criteres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('nom', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('ordre');
        });

        Schema::create('option_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('critere_id')->constrained('criteres')->cascadeOnDelete();
            $table->string('intitule', 255);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
            
            $table->index('critere_id');
            $table->index('ordre');
        });

        // Create evaluation_primes BEFORE evaluations for FK
        Schema::create('evaluation_primes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->string('evalue_par', 50)->nullable();
            $table->foreign('evalue_par')->references('ppr')->on('users')->nullOnDelete();
            $table->unsignedInteger('total_score')->default(0);
            $table->text('commentaire')->nullable();
            $table->string('annee', 4)->nullable();
            $table->foreignId('type_prime_id')->nullable()->constrained('evaluation_primes')->nullOnDelete();
            $table->boolean('observation')->default(false);
            $table->unsignedInteger('total_prime')->default(0);
            $table->timestamps();
            
            $table->index('ppr');
            $table->index('evalue_par');
            $table->index('annee');
        });

        Schema::create('response_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('critere_id')->constrained('criteres')->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('option_evaluations')->nullOnDelete();
            $table->unsignedInteger('score_obtenu')->default(0);
            $table->timestamps();
            $table->unique(['evaluation_id','critere_id']);
            
            $table->index('evaluation_id');
            $table->index('critere_id');
        });

        // already created above
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_primes');
        Schema::dropIfExists('response_evaluations');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('option_evaluations');
        Schema::dropIfExists('criteres');
        Schema::dropIfExists('categories');
    }
};


