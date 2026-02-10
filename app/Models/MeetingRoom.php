<?php

namespace App\Models;

use App\Models\hierarchy\MainLedger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $connection = 'tenant';

    public function unit_ledger()
    {
        return $this->belongsTo(MainLedger::class, "ledger_id", "id");
    }
     public function property_unit_management()
    {
        return $this->belongsTo(PropertyManagement::class, "property_management_id", "id");
    }
    public function block_unit_management()
    {
        return $this->belongsTo(BlockManagement::class, "block_management_id", "id");
    }
    public function floor_unit_management()
    {
        return $this->belongsTo(FloorManagement::class, "floor_management_id", "id");
    }



}
