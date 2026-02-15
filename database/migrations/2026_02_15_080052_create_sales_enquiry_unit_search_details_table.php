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
        Schema::create('sales_enquiry_unit_search_details', function (Blueprint $table) {
            $table->id();
             $table->integer('enquiry_id');
            $table->integer('property_management_id')->nullable();
            $table->integer('unit_description_id')->nullable();
            $table->integer('unit_type_id')->nullable();
            $table->integer('unit_condition_id')->nullable();
            $table->integer('view_id')->nullable();
            $table->integer('unit_management_id')->nullable(); 
            $table->string('property_type')->nullable();
            $table->string('city')->nullable();
            $table->string('total_area_required')->nullable();
            $table->string('area_measurement')->nullable();
            $table->text('comment')->nullable(); 
            $table->string('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_enquiry_unit_search_details');
    }
};
