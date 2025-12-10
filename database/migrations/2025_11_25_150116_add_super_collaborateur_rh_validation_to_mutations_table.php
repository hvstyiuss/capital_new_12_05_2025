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
        Schema::table('mutations', function (Blueprint $table) {
            $table->boolean('approved_by_super_collaborateur_rh')->default(false)->after('rejected_by_destination_direction_at');
            $table->string('approved_by_super_collaborateur_rh_ppr', 50)->nullable()->after('approved_by_super_collaborateur_rh');
            $table->foreign('approved_by_super_collaborateur_rh_ppr')->references('ppr')->on('users')->nullOnDelete();
            $table->timestamp('approved_by_super_collaborateur_rh_at')->nullable()->after('approved_by_super_collaborateur_rh_ppr');
            $table->date('date_debut_affectation')->nullable()->after('approved_by_super_collaborateur_rh_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['approved_by_super_collaborateur_rh_ppr']);
            $table->dropColumn([
                'approved_by_super_collaborateur_rh',
                'approved_by_super_collaborateur_rh_ppr',
                'approved_by_super_collaborateur_rh_at',
                'date_debut_affectation'
            ]);
        });
    }
};
