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
        if (Schema::hasTable('avis_departs')) {
            Schema::table('avis_departs', function (Blueprint $table) {
                $table->string('verification_code', 50)->nullable()->unique()->after('pdf_path');
            });
        }
        
        if (Schema::hasTable('avis_retours')) {
            Schema::table('avis_retours', function (Blueprint $table) {
                $table->string('verification_code', 50)->nullable()->unique()->after('pdf_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('avis_departs')) {
            Schema::table('avis_departs', function (Blueprint $table) {
                $table->dropColumn('verification_code');
            });
        }
        
        if (Schema::hasTable('avis_retours')) {
            Schema::table('avis_retours', function (Blueprint $table) {
                $table->dropColumn('verification_code');
            });
        }
    }
};
