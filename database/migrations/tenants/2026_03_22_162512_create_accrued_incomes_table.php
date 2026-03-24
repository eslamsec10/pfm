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
        Schema::create('accrued_incomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ledger_id');
            $table->unsignedBigInteger('income_ledger_id');
            $table->string('status')->default('unpaid');
            $table->date('voucher_date')->nullable();
            $table->date('applicable_date')->nullable();
            $table->date('receivable_upto')->nullable();
            $table->decimal('accrued_amount' ,10 ,5)->nullable();
            $table->decimal('received_amount' ,10 ,5)->nullable();
            $table->decimal('balance_amount' ,10 ,5)->nullable();
            $table->string('balance_for')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accrued_incomes');
    }
};
