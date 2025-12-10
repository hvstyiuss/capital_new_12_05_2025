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
        // Fix existing data: set is_chef to false for all parcours where date_fin is not null
        DB::table('parcours')
            ->whereNotNull('date_fin')
            ->where('is_chef', true)
            ->update(['is_chef' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only fixes data, no schema changes to reverse
    }
};












