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
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->integer('ledger_id')->nullable()->after('rent_amount'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->dropColumn('ledger_id');
        });
    }
};
