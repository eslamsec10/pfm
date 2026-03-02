<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\general\Groups;
use App\Models\hierarchy\MainLedger;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {


        $company = Company::on('tenant')
            ->where('id', optional(auth()->user())->company_id)
            ->first()
            ?? Company::on('tenant')->first();

        if (!$company) {
            return;
        }

        $group = Groups::on('tenant')
            ->where('name', 'LIKE', '%Tenants%')
            ->first();

        if (!$group) {
            return;
        }
        if (MainLedger::on('tenant')
            ->where('main_id', $tenant->id)->where('group_id', $group->id)
            ->exists()
        ) {
            return;
        }
        $ledger = MainLedger::on('tenant')->create([
            'code'                => $tenant->type === 'individual'
                ? $tenant->nick_name
                : $tenant->company_name,

            'name'                => $tenant->type === 'individual'
                ? $tenant->name
                : $tenant->company_name,

            'currency'            => $company->currency_code,
            'country_id'          => $company->countryid,
            'group_id'            => $group->id,
            'main_id'             => $tenant->id,

            'is_taxable'          => $group->is_taxable ?? 0,
            'vat_applicable_from' => $group->vat_applicable_from,
            'tax_rate'            => $group->tax_rate ?? 0,
            'tax_applicable'      => $group->tax_applicable ?? 0,

            'status'              => 'active',
        ]);
        $tenant->update([
            'ledger_id' => $ledger->id,
        ]);
    }
}
