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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('responsable')->default(false)->after('is_deleted');
        });

        Schema::table('user_infos', function (Blueprint $table) {
            $table->boolean('responsable')->default(false)->after('corps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('responsable');
        });

        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('responsable');
        });
    }
};
