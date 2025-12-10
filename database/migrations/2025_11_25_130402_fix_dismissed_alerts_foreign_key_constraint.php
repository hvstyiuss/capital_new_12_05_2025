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
        // Check if the foreign key constraint exists before trying to drop it
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'dismissed_alerts' 
            AND COLUMN_NAME = 'demande_id' 
            AND CONSTRAINT_NAME != 'PRIMARY'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($foreignKeyExists)) {
            $constraintName = $foreignKeyExists[0]->CONSTRAINT_NAME;
            
            Schema::table('dismissed_alerts', function (Blueprint $table) use ($constraintName) {
                // Drop the foreign key using the actual constraint name
                DB::statement("ALTER TABLE `dismissed_alerts` DROP FOREIGN KEY `{$constraintName}`");
            });
        }

        // Check if demande_id is already nullable
        $columnInfo = DB::select("
            SELECT IS_NULLABLE 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'dismissed_alerts' 
            AND COLUMN_NAME = 'demande_id'
        ");

        // Only make it nullable if it's not already nullable
        if (!empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'NO') {
            Schema::table('dismissed_alerts', function (Blueprint $table) {
                $table->unsignedBigInteger('demande_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dismissed_alerts', function (Blueprint $table) {
            // Make demande_id not nullable again
            $table->unsignedBigInteger('demande_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('demande_id')->references('id')->on('demandes')->cascadeOnDelete();
        });
    }
};
