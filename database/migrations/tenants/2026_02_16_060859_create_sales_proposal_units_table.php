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
        Schema::create('sales_proposal_units', function (Blueprint $table) {
            $table->id();
            $table->integer('proposal_id')->nullable();
            $table->integer('property_management_id')->nullable();
            $table->integer('unit_description_id')->nullable();
            $table->integer('unit_type_id')->nullable();
            $table->integer('unit_condition_id')->nullable();
            $table->integer('view_id')->nullable();
            $table->integer('unit_management_id')->nullable();
            $table->string('property_type')->nullable(); 
            $table->text('comment')->nullable();
            $table->string('price')->nullable();
            $table->string('advance_percentage')->nullable();
            $table->string('advance_amount')->nullable();
            $table->integer('number_of_installments')->nullable();
            $table->integer('payment_plan')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_proposal_units');
    }
};
