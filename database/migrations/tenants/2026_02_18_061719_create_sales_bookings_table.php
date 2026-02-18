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
        Schema::create('sales_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('enquiry_id')->nullable();
            $table->string('proposal_no')->nullable();
            $table->string('booking_no')->unique(); 
            $table->string('agreement_no')->nullable();
            $table->date('booking_date');
            $table->integer('customer_id')->nullable();
            $table->enum('status', [ 'booking', 'agreement', 'canceled', 'completed', 'pending']);
            $table->enum('booking_status', [ 'booking', 'agreement'])->default('booking');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_bookings');
    }
};
