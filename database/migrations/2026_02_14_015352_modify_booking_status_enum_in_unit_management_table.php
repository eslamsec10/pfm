<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('unit_management', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE bookings 
            MODIFY booking_status 
            ENUM('empty','enquiry','booked','agreement','booking','proposal','maintenance' ,'sale_proposal','sale_agreement','sale_booked','sale_enquiry')
            DEFAULT 'empty'
        ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_management', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE bookings 
            MODIFY booking_status 
            ENUM('empty','enquiry','booked','agreement','proposal','maintenance')
            DEFAULT 'empty'
        ");
        });
    }
};
