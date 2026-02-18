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
        Schema::create('sales_booking_installments', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id');
            $table->integer('sales_booking_unit_id');
            $table->integer('unit_management_id');
            $table->decimal('amount', 10, 5)->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_booking_installments');
    }
};
