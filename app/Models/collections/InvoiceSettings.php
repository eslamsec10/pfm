<?php

namespace App\Models\collections;

use App\Models\hierarchy\MainLedger;
use App\Models\ServiceMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceSettings extends Model
{
  use HasFactory;
  protected $connection = 'tenant';

  protected $guarded = [];
  public function ledger()
  {
    return $this->belongsTo(MainLedger::class, 'ledger_id');
  }
  
}
