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
        Schema::create('daily_price_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->integer('block_management_id');
            $table->integer('floor_management_id');
            $table->foreignId('unit_management_id')->constrained('unit_management')->cascadeOnDelete();
            $table->date('applicable_date');
            $table->decimal('rent_amount', 10, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_price_lists');
    }
};
