<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesBooking extends Model
{
    use HasFactory;
        protected $guarded = [];

    protected $connection = 'tenant';

    public function customer()
    {
        return $this->belongsTo(PropertyCustomer::class, 'customer_id');
    }

    public function booking_units(){
        return $this->hasMany(SalesBookingUnit::class , 'booking_id');
    }
}
