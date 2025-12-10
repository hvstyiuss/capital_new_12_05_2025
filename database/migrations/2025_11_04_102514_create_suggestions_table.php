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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->string('sujet');
            $table->text('message');
            $table->enum('statut', ['pending', 'read', 'replied', 'resolved'])->default('pending');
            $table->text('reponse')->nullable();
            $table->string('repondu_par', 50)->nullable();
            $table->foreign('repondu_par')->references('ppr')->on('users')->nullOnDelete();
            $table->timestamp('repondu_le')->nullable();
            $table->timestamps();
            
            $table->index('ppr');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
