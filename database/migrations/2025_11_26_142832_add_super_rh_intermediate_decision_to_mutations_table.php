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
            // Fields for Super RH intermediate decision (for external mutations)
            $table->boolean('sent_to_destination_by_super_rh')->default(false)->after('approved_by_current_direction_at');
            $table->string('sent_to_destination_by_super_rh_ppr', 50)->nullable()->after('sent_to_destination_by_super_rh');
            $table->foreign('sent_to_destination_by_super_rh_ppr')->references('ppr')->on('users')->nullOnDelete();
            $table->timestamp('sent_to_destination_by_super_rh_at')->nullable()->after('sent_to_destination_by_super_rh_ppr');
            
            $table->boolean('rejected_by_super_rh')->default(false)->after('sent_to_destination_by_super_rh_at');
            $table->string('rejected_by_super_rh_ppr', 50)->nullable()->after('rejected_by_super_rh');
            $table->foreign('rejected_by_super_rh_ppr')->references('ppr')->on('users')->nullOnDelete();
            $table->text('rejection_reason_super_rh')->nullable()->after('rejected_by_super_rh_ppr');
            $table->timestamp('rejected_by_super_rh_at')->nullable()->after('rejection_reason_super_rh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['sent_to_destination_by_super_rh_ppr']);
            $table->dropForeign(['rejected_by_super_rh_ppr']);
            $table->dropColumn([
                'sent_to_destination_by_super_rh',
                'sent_to_destination_by_super_rh_ppr',
                'sent_to_destination_by_super_rh_at',
                'rejected_by_super_rh',
                'rejected_by_super_rh_ppr',
                'rejection_reason_super_rh',
                'rejected_by_super_rh_at',
            ]);
        });
    }
};
