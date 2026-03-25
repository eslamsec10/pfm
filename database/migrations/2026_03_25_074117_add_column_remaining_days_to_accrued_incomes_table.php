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
        Schema::table('accrued_incomes', function (Blueprint $table) {
            $table->string('remaining_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accrued_incomes', function (Blueprint $table) {
            $table->dropColumn('remaining_days');
        });
    }
};
