<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.id
 

     */
    public function up(): void
    {
        Schema::create('meeting_room_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('meeting_room_id');
            $table->integer('tenant_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at'); 
            $table->string('status');
            $table->decimal('price', 10, 3)->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->time('working_from')->nullable();
            $table->time('working_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_room_bookings');
    }
};
