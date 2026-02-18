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
        Schema::create('sales_agreements', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id')->nullable();
            $table->string('proposal_no')->nullable();
            $table->string('booking_no')->nullable();
            $table->string('agreement_no')->unique();
            $table->date('agreement_date');
            $table->integer('customer_id')->nullable();
            $table->enum('status', [ 'agreement', 'canceled', 'completed', 'pending']);
            $table->enum('booking_status', [  'agreement'])->default('agreement');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_agreements');
    }
};
