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
        Schema::create('sales_proposals', function (Blueprint $table) {
            $table->id();
            $table->integer('enquiry_id')->nullable();
            $table->string('proposal_no')->unique();
            $table->string('booking_no')->nullable();
            $table->string('agreement_no')->nullable();
            $table->date('proposal_date');
            $table->integer('customer_id')->nullable();
            $table->enum('status', ['proposal', 'booking', 'agreement', 'canceled', 'completed', 'pending']);
            $table->enum('booking_status', ['proposal', 'booking', 'agreement'])->default('proposal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_proposals');
    }
};
