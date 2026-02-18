<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAgreement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $connection = 'tenant';

    public function customer()
    {
        return $this->belongsTo(PropertyCustomer::class, 'customer_id');
    }

    public function agreement_units()
    {
        return $this->hasMany(SalesAgreementUnit::class, 'agreement_id');
    }
}
