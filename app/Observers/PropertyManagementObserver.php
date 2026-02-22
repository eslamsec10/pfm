<?php

namespace App\Observers;

use App\Models\general\Groups;
use App\Models\hierarchy\CostCenterCategory;
use App\Models\hierarchy\MainLedger;
use App\Models\PropertyManagement;
use App\Models\UnitManagement;
use Illuminate\Support\Facades\Log;

class PropertyManagementObserver
{

    public function created(PropertyManagement $property)
    {
        $master_group = (new Groups())
            ->setConnection('tenant')
            ->where('id', 48)
            ->first();

        if (!$master_group) {
            return;
        }

        // ================= CREATE GROUP =================
        $group = (new Groups())->setConnection('tenant')->create([
            'code'                     => $property->code,
            'property_id'              => $property->id,
            'name'                     => $property->name,
            'display_name'             => $property->name,
            'group_id'                 => $master_group->id,
            'is_projects_parent_group' => $master_group->is_projects_parent_group ?? 0,
            'enable_auto_code'         => $master_group->enable_auto_code ?? 0,
            'status'                   => 'active',
            'tax_applicable'           => $master_group->tax_applicable ?? 0,
            'is_taxable'               => $master_group->is_taxable ?? 0,
            'vat_applicable_from'      => $master_group->vat_applicable_from,
            'tax_rate'                 => $master_group->tax_rate ?? 0,
        ]);

        // ================= CREATE COST CENTER =================
        $costCenter = (new CostCenterCategory())->setConnection('tenant')->create([
            'code'      => $property->code,
            'name'      => $property->name,
            'main_id'   => $property->id,
            'main_type' => 'property',
            'status'    => 'active',
        ]);

        // ================= UPDATE PROPERTY =================
        $property->update([
            'group_id'        => $group->id,
            'cost_center_category_id'  => $costCenter->id,
        ]);
    }
    public function updated(PropertyManagement $property)
    {

        if (!$property->wasChanged('tax_rate')) {
            return;
        }
        $unit_management = UnitManagement::where('property_management_id', $property->id)->get();
        $group = Groups::where('property_id', $property->id)->first();
        if (!$group) {
            return;
        }
        $group->update([
            'is_taxable' => 1,
            'tax_rate' => $property->tax_rate,
        ]);
        foreach ($unit_management as $unit) {
            $ledger = MainLedger::where('group_id', $group->id)->where('main_id' , $unit->id)->first();
            Log::info($ledger);
            $ledger->update([
                'main_type'         => 'unit',
                'tax_rate'          =>  $group->tax_rate,
                'is_taxable'        => 1,
            ]);
        }
    }

    public function deleted(PropertyManagement $property)
    {
        Groups::where('property_id', $property->id)->delete();
    }
}
