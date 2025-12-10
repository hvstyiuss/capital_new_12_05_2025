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
            // Add PPR if it doesn't exist
            if (!Schema::hasColumn('mutations', 'ppr')) {
                $table->string('ppr', 50)->nullable()->after('id');
                $table->foreign('ppr')->references('ppr')->on('users')->nullOnDelete();
            }
            
            // Add motif if it doesn't exist
            if (!Schema::hasColumn('mutations', 'motif')) {
                $table->string('motif')->nullable()->after('to_entite_id');
            }
            
            // Add mutation_type if it doesn't exist
            if (!Schema::hasColumn('mutations', 'mutation_type')) {
                $table->enum('mutation_type', ['interne', 'externe'])->nullable()->after('motif');
            }
            
            // Add direction approval fields
            $table->boolean('approved_by_current_direction')->default(false)->after('valide_reception');
            $table->string('approved_by_current_direction_ppr', 50)->nullable()->after('approved_by_current_direction');
            $table->timestamp('approved_by_current_direction_at')->nullable()->after('approved_by_current_direction_ppr');
            
            $table->boolean('approved_by_destination_direction')->default(false)->after('approved_by_current_direction_at');
            $table->string('approved_by_destination_direction_ppr', 50)->nullable()->after('approved_by_destination_direction');
            $table->timestamp('approved_by_destination_direction_at')->nullable()->after('approved_by_destination_direction_ppr');
            
            $table->boolean('rejected_by_current_direction')->default(false)->after('approved_by_destination_direction_at');
            $table->string('rejected_by_current_direction_ppr', 50)->nullable()->after('rejected_by_current_direction');
            $table->text('rejection_reason_current')->nullable()->after('rejected_by_current_direction_ppr');
            $table->timestamp('rejected_by_current_direction_at')->nullable()->after('rejection_reason_current');
            
            $table->boolean('rejected_by_destination_direction')->default(false)->after('rejected_by_current_direction_at');
            $table->string('rejected_by_destination_direction_ppr', 50)->nullable()->after('rejected_by_destination_direction');
            $table->text('rejection_reason_destination')->nullable()->after('rejected_by_destination_direction_ppr');
            $table->timestamp('rejected_by_destination_direction_at')->nullable()->after('rejection_reason_destination');
            
            // Add foreign keys
            if (Schema::hasColumn('mutations', 'ppr')) {
                $table->foreign('approved_by_current_direction_ppr')->references('ppr')->on('users')->nullOnDelete();
                $table->foreign('approved_by_destination_direction_ppr')->references('ppr')->on('users')->nullOnDelete();
                $table->foreign('rejected_by_current_direction_ppr')->references('ppr')->on('users')->nullOnDelete();
                $table->foreign('rejected_by_destination_direction_ppr')->references('ppr')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['approved_by_current_direction_ppr']);
            $table->dropForeign(['approved_by_destination_direction_ppr']);
            $table->dropForeign(['rejected_by_current_direction_ppr']);
            $table->dropForeign(['rejected_by_destination_direction_ppr']);
            
            $table->dropColumn([
                'approved_by_current_direction',
                'approved_by_current_direction_ppr',
                'approved_by_current_direction_at',
                'approved_by_destination_direction',
                'approved_by_destination_direction_ppr',
                'approved_by_destination_direction_at',
                'rejected_by_current_direction',
                'rejected_by_current_direction_ppr',
                'rejection_reason_current',
                'rejected_by_current_direction_at',
                'rejected_by_destination_direction',
                'rejected_by_destination_direction_ppr',
                'rejection_reason_destination',
                'rejected_by_destination_direction_at',
            ]);
        });
    }
};
