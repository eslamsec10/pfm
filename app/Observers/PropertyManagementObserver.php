<?php

namespace App\Observers;

use App\Models\general\Groups;
use App\Models\PropertyManagement;
use App\Models\hierarchy\CostCenterCategory;

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
}
