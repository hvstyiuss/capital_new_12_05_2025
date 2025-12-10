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
        Schema::table('user_infos', function (Blueprint $table) {
            // Add gsm field if it doesn't exist
            if (!Schema::hasColumn('user_infos', 'gsm')) {
                $table->string('gsm')->nullable()->after('adresse');
            }
            
            // Add echelle_id field if it doesn't exist
            if (!Schema::hasColumn('user_infos', 'echelle_id')) {
                $table->foreignId('echelle_id')->nullable()->after('grade_id')->constrained('echelles')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            if (Schema::hasColumn('user_infos', 'echelle_id')) {
                $table->dropForeign(['echelle_id']);
                $table->dropColumn('echelle_id');
            }
            
            if (Schema::hasColumn('user_infos', 'gsm')) {
                $table->dropColumn('gsm');
            }
        });
    }
};
