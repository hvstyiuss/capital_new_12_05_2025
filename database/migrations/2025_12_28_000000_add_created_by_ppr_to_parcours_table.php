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
            if (!Schema::hasColumn('parcours', 'created_by_ppr')) {
                $table->string('created_by_ppr', 50)->nullable()->after('reason');
                $table->foreign('created_by_ppr')->references('ppr')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcours', function (Blueprint $table) {
            if (Schema::hasColumn('parcours', 'created_by_ppr')) {
                $table->dropForeign(['created_by_ppr']);
                $table->dropColumn('created_by_ppr');
            }
        });
    }
};








