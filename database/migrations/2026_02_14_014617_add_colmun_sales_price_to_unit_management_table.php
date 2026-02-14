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
        Schema::table('unit_management', function (Blueprint $table) {
            $table->decimal('sales_price', 10, 2)->nullable()->after('rent_amount');
            $table->decimal('daily_rent_price', 10, 2)->nullable()->after('sales_price');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_management', function (Blueprint $table) {
            $table->dropColumn('sales_price');
            $table->dropColumn('daily_rent_price');
        });
    }
};
