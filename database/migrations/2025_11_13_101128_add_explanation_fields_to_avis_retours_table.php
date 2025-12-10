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
        Schema::table('avis_retours', function (Blueprint $table) {
            $table->boolean('explanation_required')->default(false)->after('statut');
            $table->timestamp('explanation_deadline')->nullable()->after('explanation_required');
            $table->text('explanation_provided')->nullable()->after('explanation_deadline');
            $table->string('explanation_pdf_path')->nullable()->after('explanation_provided');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avis_retours', function (Blueprint $table) {
            $table->dropColumn(['explanation_required', 'explanation_deadline', 'explanation_provided', 'explanation_pdf_path']);
        });
    }
};
