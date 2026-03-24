<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\general\Groups;
use App\Models\hierarchy\CostCenter;
use App\Models\hierarchy\CostCenterCategory;
use App\Models\hierarchy\MainLedger;
use App\Models\UnitManagement;



class UnitManagementObserver
{
    public function created(UnitManagement $unitManagement)
    {
        // ================= COMPANY =================
        $company =   (new Company())->setConnection('tenant')->first();

        // ================= GROUP (PROPERTY GROUP) =================
        $group = (new Groups())
            ->setConnection('tenant')
            ->where('property_id', $unitManagement->property_management_id)
            ->first();
        $second_master_group = (new Groups())
            ->setConnection('tenant')
            ->where('name', 'LIKE', '%Advances Received from Tenants%')
            ->first();
        $master_group = (new Groups())
            ->setConnection('tenant')
            ->where('property_id', $unitManagement->property_management_id)
            ->where('group_id', $second_master_group->id)
            ->first();

        // ================= LEDGER =================
        $ledger = (new MainLedger())
            ->setConnection('tenant')
            ->create([
                'code' => $unitManagement->unit_management_main?->name,
                'name' =>
                $unitManagement->property_unit_management?->name . '-' .
                    $unitManagement->block_unit_management?->block?->name . '-' .
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
                'main_type'           => 'unit',
                'is_taxable'          => $group?->is_taxable ?? 0,
                'vat_applicable_from' => $group?->vat_applicable_from,
                'tax_rate'            => $group?->tax_rate ?? $company?->tax_rate,
                'tax_applicable'      => $group?->tax_applicable ?? 0,
                'status'              => 'active',
            ]);

        // ================= COST CENTER =================
        $propertyCost = (new CostCenterCategory())
            ->setConnection('tenant')
            ->where('main_id', $unitManagement->property_management_id)
            ->where('main_type', 'property')
            ->first();

        $costCenter = (new CostCenter())
            ->setConnection('tenant')
            ->create([
                'name' =>
                $unitManagement->property_unit_management?->name . '-' .
                    $unitManagement->block_unit_management?->block?->name . '-' .
                    $unitManagement->floor_unit_management?->floor_management_main?->name
                    . '-' .
                    $unitManagement->unit_management_main?->name,
                'main_id'   => $unitManagement->id,
                'main_type' => 'unit',
                'cost_center_category_id' => $propertyCost?->id,
                'status'    => 'active',
            ]);
        // ================= LEDGER =================
        $advanced_ledger = (new MainLedger())
            ->setConnection('tenant')
            ->create([
                'code' => $unitManagement->unit_management_main?->name,
                'name' =>
                $unitManagement->property_unit_management?->name . '-' .
                    $unitManagement->block_unit_management?->block?->name . '-' .
                    $unitManagement->floor_unit_management?->floor_management_main?->name . '-' .
                    $unitManagement->unit_management_main?->name,
                'currency'  => $company?->currency_code,
                'country_id' =>
                $unitManagement->property_unit_management
                    ?->country_master
                    ?->country
                    ?->id ?? 1,
                'group_id'            => $master_group?->id,
                'main_id'             => $unitManagement->id,
                'main_type'           => 'unit',
                'is_taxable'          => $master_group?->is_taxable ?? 0,
                'vat_applicable_from' => $master_group?->vat_applicable_from,
                'tax_rate'            => $master_group?->tax_rate ?? $company?->tax_rate,
                'tax_applicable'      => $master_group?->tax_applicable ?? 0,
                'status'              => 'active',
            ]);
        // ================= UPDATE UNIT =================
        $unitManagement->update([
            'ledger_id'                 => $ledger->id,
            'advanced_group_id'         => $advanced_ledger->id,
            'cost_center_id'            => $costCenter->id,
        ]);
    }

    public function deleted(UnitManagement $unitManagement)
    {
        $ledger = MainLedger::where('main_id', $unitManagement->id)->where('main_type', 'unit')->first();

        if (!$ledger) {

            $ledger->delete();
        }
    }
}
