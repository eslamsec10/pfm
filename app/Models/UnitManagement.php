<?php

namespace App\Models;

use App\Models\hierarchy\MainLedger;
use App\Models\property_management\RentPriceList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitManagement extends Model
{
    use HasFactory;
    protected $guarded = [];
    // protected $connection = 'tenant';
    public function unit_ledger()
    {
        return $this->belongsTo(MainLedger::class, "ledger_id", "id");
    }
    // public function main_ledger()
    // {
    //     return $this->hasOne(MainLedger::class, 'main_id', 'id')
    //         ->where('group_id', $this->property_management_id);
    // }
    // public function main_ledger(){
    //     return MainLedger::where('group_id' , $this->property_management_id)->where('main_id' , $this->id)->first();
    // }
    public function main_ledger()
    {
        return $this->hasOne(MainLedger::class, 'main_id', 'id');
    }

    public function property_unit_management()
    {
        return $this->belongsTo(PropertyManagement::class, "property_management_id", "id");
    }
    public function block()
    {
        return $this->belongsTo(Block::class, "block_id", "id");
    }

    public function block_unit_management()
    {
        return $this->belongsTo(BlockManagement::class, "block_management_id", "id");
    }
    public function floor_unit_management()
    {
        return $this->belongsTo(FloorManagement::class, "floor_management_id", "id");
    }
    public function unit_management_main()
    {
        return $this->belongsTo(Unit::class, "unit_id", "id");
    }
    public function unit_type()
    {
        return $this->belongsTo(UnitType::class, "unit_type_id", "id");
    }
    public function unit_condition()
    {
        return $this->belongsTo(UnitCondition::class, "unit_condition_id", "id");
    }
    public function unit_description()
    {
        return $this->belongsTo(UnitDescription::class, "unit_description_id", "id");
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'unit_id', 'id');
    }
    public function rent_schedules()
    {
        return $this->hasMany(RentPriceList::class, 'unit_management_id', 'id');
    }
    public function view()
    {
        return $this->belongsTo(View::class, "view_id", "id");
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeEmptyUnit($query)
    {
        return $query->where('booking_status', 'empty');
    }

    public function latest_rent_schedule()
    {
        return $this->hasOne(RentPriceList::class, 'unit_management_id')
            ->where('applicable_date', '<=', Carbon::today())
            ->orderBy('applicable_date', 'desc');
    }

    public function enquiry()
    {
        return $this->belongsTo(EnquiryUnitSearchDetails::class, "id", "unit_management_id");
    }

    public function proposal_main()
    {
        return $this->belongsTo(ProposalUnits::class, "id", "unit_id");
    }

    public function booking_main()
    {
        return $this->belongsTo(BookingUnits::class, "id", "unit_id");
    }

    public function agreement_main()
    {
        return $this->belongsTo(AgreementUnits::class, "id", "unit_id");
    }
    public function facilities()
    {
        return $this->belongsToMany(RoomFacility::class, 'unit_management_options', 'unit_management_id', 'facility_id');
    }
}
