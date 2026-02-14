<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPriceList extends Model
{
    use HasFactory;
        protected $guarded =[];

    public function property(){
        return $this->belongsTo(PropertyManagement::class , 'property_id'  );
    }
    public function block_management(){
        return $this->belongsTo(BlockManagement::class , 'block_management_id' );
    }
    public function floor_management(){
        return $this->belongsTo(FloorManagement::class , 'floor_management_id'  );
    }
    public function unit_management(){
        return $this->belongsTo(UnitManagement::class , 'unit_management_id' );
    }
}
