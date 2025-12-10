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
        // First, update existing data to map to enum values
        // Map 'Administration', 'Corps Administratif' and similar to 'support'
        // Map anything else that might be forestier-related to 'forestier'
        DB::table('user_infos')
            ->where(function($query) {
                $query->whereIn('corps', ['Administration', 'Corps Administratif', 'Administratif'])
                      ->orWhereNull('corps');
            })
            ->update(['corps' => 'support']);
        
        DB::table('user_infos')
            ->whereNotNull('corps')
            ->whereNotIn('corps', ['support', 'forestier'])
            ->update(['corps' => 'forestier']);
        
        // Now change the column type to enum
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('corps');
        });
        
        Schema::table('user_infos', function (Blueprint $table) {
            $table->enum('corps', ['forestier', 'support'])->nullable()->after('grade_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('corps');
        });
        
        Schema::table('user_infos', function (Blueprint $table) {
            $table->string('corps')->nullable()->after('grade_id');
        });
    }
};
