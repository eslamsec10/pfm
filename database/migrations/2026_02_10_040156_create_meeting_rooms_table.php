<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('property_management_id')->nullable();
            $table->integer('block_management_id')->nullable();
            $table->integer('floor_management_id')->nullable();
            $table->decimal('rent_amount', 10, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_rooms');
    }
};
