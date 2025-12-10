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
        // Drop old role/permission tables if they exist
        // Temporarily disable foreign key checks to avoid constraint issues
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Drop user_role pivot table (old system)
            Schema::dropIfExists('user_role');
            
            // Only drop old permissions table if it has the old structure (has role_id column)
            // Spatie's permissions table doesn't have role_id
            if (Schema::hasTable('permissions') && Schema::hasColumn('permissions', 'role_id')) {
                Schema::dropIfExists('permissions');
            }
            
            // Only drop old roles table if it doesn't have guard_name (Spatie's roles table has guard_name)
            if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'guard_name')) {
                Schema::dropIfExists('roles');
            }
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration drops old tables, so we don't recreate them
        // If you need to rollback, you would need to recreate the old structure
        // which is not recommended since we're migrating to Spatie
    }
};
