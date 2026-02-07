<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryUnitSearchDetails extends Model
{
    use HasFactory;
    protected $connection = 'tenant';
    protected $guarded = [];
    public function unit_management(){
        return $this->belongsTo(UnitManagement::class , 'unit_management_id');
    }

    public function main_enquiry(){
        return $this->belongsTo(Enquiry::class , 'enquiry_id');
    }

}
