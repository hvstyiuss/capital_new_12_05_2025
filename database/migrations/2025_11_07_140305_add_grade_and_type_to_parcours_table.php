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
        Schema::table('parcours', function (Blueprint $table) {
            if (!Schema::hasColumn('parcours', 'grade_id')) {
                $table->foreignId('grade_id')->nullable()->after('poste')->constrained('grades')->nullOnDelete();
            }
            if (!Schema::hasColumn('parcours', 'reason')) {
                $table->string('reason', 100)->nullable()->after('grade_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcours', function (Blueprint $table) {
            if (Schema::hasColumn('parcours', 'grade_id')) {
                $table->dropForeign(['grade_id']);
                $table->dropColumn('grade_id');
            }
            if (Schema::hasColumn('parcours', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }
};
