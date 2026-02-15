<?php

namespace App\Models\sales;

use App\Models\sales\SalesEnquiry;
use App\Models\UnitManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesEnquiryUnitSearchDetails extends Model
{
    use HasFactory;

    use HasFactory;
    protected $connection = 'tenant';
    protected $guarded = [];
    public function unit_management()
    {
        return $this->belongsTo(UnitManagement::class, 'unit_management_id');
    }

    public function main_enquiry()
    {
        return $this->belongsTo(SalesEnquiry::class, 'enquiry_id');
    }
}
