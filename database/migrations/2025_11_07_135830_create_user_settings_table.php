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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ppr', 50);
            $table->foreign('ppr')->references('ppr')->on('users')->cascadeOnDelete();
            $table->string('language', 10)->default('fr');
            $table->string('theme', 20)->default('light');
            $table->string('timezone', 50)->default('Africa/Casablanca');
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_sms')->default(false);
            $table->boolean('dark_mode')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamps();
            
            $table->unique('ppr');
            $table->index('ppr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
