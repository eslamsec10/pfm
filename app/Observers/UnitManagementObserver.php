<?php

namespace App\Observers;

use App\Models\User;
use App\Models\general\Groups;
use App\Models\UnitManagement;
use App\Models\hierarchy\CostCenter;
use App\Models\hierarchy\MainLedger;
use App\Models\hierarchy\CostCenterCategory;
 

class UnitManagementObserver
{
    public function created(UnitManagement $unitManagement)
    {
        // ================= COMPANY =================
        $company = auth()->user()
            ?? (new User())->setConnection('tenant')->first();

        // ================= GROUP (PROPERTY GROUP) =================
        $group = (new Groups())
            ->setConnection('tenant')
            ->where('property_id', $unitManagement->property_management_id)
            ->first();

        // ================= LEDGER =================
        (new MainLedger())
            ->setConnection('tenant')
            ->create([
                'code' => $unitManagement->unit_management_main?->name,

                'name' =>
                    $unitManagement->property_unit_management?->code . '-' .
                    $unitManagement->block_unit_management?->block?->code . '-' .
                    $unitManagement->floor_unit_management?->floor_management_main?->name . '-' .
                    $unitManagement->unit_management_main?->name,

                'currency'  => $company?->currency_code,

                'country_id' =>
                    $unitManagement->property_unit_management
                        ?->country_master
                        ?->country
                        ?->id ?? 1,

                'group_id'            => $group?->id,
                'main_id'             => $unitManagement->id,
                'is_taxable'          => $group?->is_taxable ?? 0,
                'vat_applicable_from' => $group?->vat_applicable_from,
                'tax_rate'            => $group?->tax_rate ?? 0,
                'tax_applicable'      => $group?->tax_applicable ?? 0,
                'status'              => 'active',
            ]);

        // ================= COST CENTER =================
        $propertyCost = (new CostCenterCategory())
            ->setConnection('tenant')
            ->where('main_id', $unitManagement->property_management_id)
            ->where('main_type', 'property')
            ->first();

        (new CostCenter())
            ->setConnection('tenant')
            ->create([
                'name' =>
                    $unitManagement->property_unit_management?->name . '-' .
                    $unitManagement->unit_management_main?->name . '-' .
                    $unitManagement->block_unit_management?->block?->name . '-' .
                    $unitManagement->floor_unit_management?->floor_management_main?->name,

                'main_id'   => $unitManagement->id,
                'main_type' => 'unit',
                'cost_center_category_id' => $propertyCost?->id,
                'status'    => 'active',
            ]);
    }
}
