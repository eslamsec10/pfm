<?php

namespace App\Models;

use App\Models\hierarchy\MainLedger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccruedIncome extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $connection = 'tenant';

    public function accrued_income_ledger(){
        return $this->belongsTo(MainLedger::class , 'ledger_id');
    }
    public function income_ledger(){
        return $this->belongsTo(MainLedger::class , 'income_ledger_id');
    }
}
