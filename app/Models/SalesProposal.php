<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProposal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $connection = 'tenant';

    public function customer()
    {
        return $this->belongsTo(PropertyCustomer::class, 'customer_id');
    }

    public function proposal_units(){
        return $this->hasMany(SalesProposalUnit::class , 'proposal_id');
    }
}
