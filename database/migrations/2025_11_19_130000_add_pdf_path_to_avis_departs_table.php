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
        Schema::table('avis_departs', function (Blueprint $table) {
            if (!Schema::hasColumn('avis_departs', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('statut');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avis_departs', function (Blueprint $table) {
            if (Schema::hasColumn('avis_departs', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
};






