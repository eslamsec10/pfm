<?php

namespace App\Models\sales;

use App\Models\PropertyCustomer;
use App\Models\sales\SalesEnquiryUnitSearchDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesEnquiry extends Model
{
    use HasFactory;

      protected $guarded = [];
    protected $connection = 'tenant';

    public function customer(){
        return $this->belongsTo(PropertyCustomer::class , 'customer_id');
    }

 

    public function enquiry_unit_search(){
        return $this->hasMany(SalesEnquiryUnitSearchDetails::class , 'enquiry_id');
    }
}
