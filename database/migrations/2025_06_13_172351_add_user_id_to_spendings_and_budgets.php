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
        // Trong migration:
    Schema::table('expenses', function (Blueprint $table) {
        if (!Schema::hasColumn('expenses', 'user_id')) {
            $table->unsignedBigInteger('user_id')->after('id');
        }
    });
    Schema::table('budgets', function (Blueprint $table) {
        if (!Schema::hasColumn('budgets', 'user_id')) {
            $table->unsignedBigInteger('user_id')->after('id');
        }
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }
};
