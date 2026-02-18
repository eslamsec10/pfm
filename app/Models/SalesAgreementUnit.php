<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAgreementUnit extends Model
{
    use HasFactory;

      protected $connection = 'tenant';

    protected $guarded = [];
    public function agreement()
    {
        return $this->belongsTo(SalesAgreement::class, 'agreement_id');
    }
    public function unit_management()
    {
        return $this->belongsTo(UnitManagement::class, 'unit_management_id');
    }

    public function installments()
    {
        return $this->hasMany(SalesAgreementInstallment::class, 'sales_agreement_unit_id');
    }
}
