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
        if (!Schema::hasColumn('entites', 'entity_type')) {
            Schema::table('entites', function (Blueprint $table) {
                $table->enum('entity_type', ['direction', 'departement', 'service'])->nullable()->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('entites', 'entity_type')) {
            Schema::table('entites', function (Blueprint $table) {
                $table->dropColumn('entity_type');
            });
        }
    }
};






