<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change description column from string to text
        DB::statement('ALTER TABLE `entite_infos` MODIFY `description` TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to string (255 characters)
        DB::statement('ALTER TABLE `entite_infos` MODIFY `description` VARCHAR(255) NULL');
    }
};
