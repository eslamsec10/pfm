<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesBookingUnit extends Model
{
    use HasFactory;
    protected $connection = 'tenant';

    protected $guarded = [];
    public function booking()
    {
        return $this->belongsTo(SalesBooking::class, 'booking_id');
    }
    public function unit_management()
    {
        return $this->belongsTo(UnitManagement::class, 'unit_management_id');
    }

    public function installments()
    {
        return $this->hasMany(SalesBookingInstallment::class, 'sales_booking_unit_id');
    }
}
