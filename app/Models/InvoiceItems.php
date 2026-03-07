<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $connection = 'tenant';

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function unit_management()
    {
        return $this->belongsTo(UnitManagement::class, 'unit_id');
    }
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    public function building()
    {
        return $this->belongsTo(PropertyManagement::class, 'building_id');
    }
    public function service_master()
    {
        return $this->belongsTo(ServiceMaster::class, 'service_id');
    }
}
