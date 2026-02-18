<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProposalUnit extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $connection = 'tenant';

    public function proposal(){
        return $this->belongsTo(SalesProposal::class , 'proposal_id');
    }
     public function unit_management()
    {
        return $this->belongsTo(UnitManagement::class, 'unit_management_id');
    }

    public function installments(){
        return $this->hasMany(SalesProposalInstallment::class,'sales_proposal_unit_id');
    }
}
