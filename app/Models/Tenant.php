<?php

namespace App\Models;

use App\Models\collections\Receipt;
use App\Models\hierarchy\MainLedger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $connection = 'tenant';

    public function country_master()
    {
        return $this->belongsTo(CountryMaster::class, 'country_id', 'id');
    }
    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'tenant_id', 'id');
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'tenant_id', 'id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant_id', 'id');
    }
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'tenant_id', 'id');
    }
    public static function storeTenant(array $data)
    {
        return self::create($data);
    }

    public function ledger(){
        return $this->belongsTo(MainLedger::class , 'ledger_id');
    }
    public function tenant_ledger()
    {
        return $this->hasOne(MainLedger::class, 'main_id', 'id')
            ->whereHas('group', function ($q) {
                $q->where('id', 49);
            });
    }
}
