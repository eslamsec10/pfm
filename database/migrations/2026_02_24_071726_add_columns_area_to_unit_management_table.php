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
        Schema::table('unit_management', function (Blueprint $table) {
            $table->string('area')->after('children')->nullable();
            $table->string('area_unit')->after('area')->nullable();
            $table->string('area_inside')->after('area_unit')->nullable();
            $table->string('area_inside_unit')->after('area_inside')->nullable();
            $table->string('area_terrace')->after('area_inside_unit')->nullable();
            $table->string('area_terrace_unit')->after('area_terrace')->nullable();
            $table->string('rate')->after('area_terrace_unit')->nullable();
            $table->string('rate_unit')->after('rate')->nullable();
            $table->string('security_deposit_amount')->after('rate_unit')->nullable();
            $table->string('municipality_nos')->after('security_deposit_amount')->nullable();
            $table->date('installation_date')->after('municipality_nos')->nullable();
            $table->string('electricity_meter_no')->after('installation_date')->nullable();
            $table->date('installation_date_1')->after('electricity_meter_no')->nullable();
            $table->string('water_meter_no')->after('installation_date_1')->nullable();
            $table->string('electricity_ac_no')->after('water_meter_no')->nullable();
            $table->date('rent_applicable_date')->after('electricity_ac_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_management', function (Blueprint $table) {
            $table->dropColumn([
                'area',
                'area_unit',
                'area_inside',
                'area_inside_unit',
                'area_terrace',
                'area_terrace_unit',
                'rate',
                'rate_unit',
                'security_deposit_amount',
                'municipality_nos',
                'installation_date',
                'electricity_meter_no',
                'installation_date_1',
                'water_meter_no',
                'electricity_ac_no',
                'rent_applicable_date',
            ]);
        });
    }
};
