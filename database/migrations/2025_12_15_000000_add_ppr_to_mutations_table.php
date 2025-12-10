<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('mutations', 'ppr')) {
            Schema::table('mutations', function (Blueprint $table) {
                $table->string('ppr')->nullable()->after('id');
            });
        }
        
        // Add foreign key constraint if column exists and foreign key doesn't
        if (Schema::hasColumn('mutations', 'ppr')) {
            try {
                Schema::table('mutations', function (Blueprint $table) {
                    $table->foreign('ppr')->references('ppr')->on('users')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore the error
            }
        }
    }

    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['ppr']);
            $table->dropColumn('ppr');
        });
    }
};

