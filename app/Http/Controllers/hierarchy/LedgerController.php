<?php

namespace App\Http\Controllers\hierarchy;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Country;
use App\Models\general\Groups;
use App\Models\hierarchy\CostCenter;
use App\Models\hierarchy\CostCenterCategory;
use App\Models\hierarchy\MainLedger;
use App\Models\Tenant;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('complaints');
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $main_ledger = (new MainLedger())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);
        $countries = (new Country())->setConnection('tenant')->get();
        $groups    = (new Groups())->setConnection('tenant')->get();
        $data      = [
            'main'      => $main_ledger,
            'search'    => $search,
            'countries' => $countries,
            'groups'    => $groups,

        ];
        return view("admin-views.hierarchy.ledgers.ledgers_list", $data);
    }

    public function create()
    {
        $groups    = (new Groups())->setConnection('tenant')->get();
        $countries = (new Country())->setConnection('tenant')->get();

        $data = [
            'groups'    => $groups,
            'countries' => $countries,

        ];
        return view("admin-views.hierarchy.ledgers.create", $data);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:255',
            'currency' => 'required|string|max:255',
            'group_id' => 'required',

        ]);
        try {
            ($request->vat_applicable_from != null) ? $vat_applicable_from = Carbon::createFromFormat('d/m/Y', $request->vat_applicable_from)->format('Y-m-d') : $vat_applicable_from = null;
            $ledger                                                        = (new MainLedger())->setConnection('tenant')->create([
                'code'                   => $request->code,
                'name'                   => $request->name,
                'currency'               => $request->currency,
                'contact_person'         => $request->contact_person,
                'phone'                  => $request->phone,
                'email'                  => $request->email,
                'nature'                 => $request->nature,
                'address'                => $request->address,
                'country_id'             => $request->country_id,
                'group_id'               => $request->group_id,
                'is_taxable'             => $request->is_taxable ?: 0,
                'vat_applicable_from'    => $vat_applicable_from,
                'tax_rate'               => $request->tax_rate ?: 0,
                'is_discount'            => $request->is_discount ?: 0,
                'is_cash'                => $request->is_cash ?: 0,
                'project_general_ledger' => $request->project_general_ledger ?: 0,
                'maintain_bill_by_bill'  => $request->maintain_bill_by_bill ?: 0,
                'tax_applicable'         => $request->tax_applicable ?: 0,
                'is_custom_vat'          => $request->is_custom_vat ?: 0,
                'status'                 => $request->status ?: 'active',
                'iban_no'                => $request->iban_no ?? null,
                'swift_code'             => $request->swift_code ?? null,
                'account_no'             => $request->account_no ?? null,
                'branch'                 => $request->branch ?? null,
                'bank_name'              => $request->bank_name ?? null,
                'account_name'           => $request->account_name ?? null,
            ]);

            return redirect()->route("ledgers.index")->with("success", __('general.added_successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }

    public function edit($id)
    {
        $main      = (new MainLedger())->setConnection('tenant')->findOrFail($id);
        $groups    = (new Groups())->setConnection('tenant')->get();
        $countries = (new Country())->setConnection('tenant')->get();

        $data = [
            'groups'    => $groups,
            'ledger'    => $main,
            'countries' => $countries,

        ];
        return view("admin-views.hierarchy.ledgers.edit", $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:255',
            'currency' => 'required|string|max:255',
            'group_id' => 'required',
        ]);

        try {
            $ledger              = (new MainLedger())->setConnection('tenant')->findOrFail($id);
            $vat_applicable_from = $request->vat_applicable_from
                ? Carbon::createFromFormat('d/m/Y', $request->vat_applicable_from)->format('Y-m-d')
                : null;

            $ledger->update([
                'code'                   => $request->code,
                'name'                   => $request->name,
                'currency'               => $request->currency,
                'contact_person'         => $request->contact_person,
                'phone'                  => $request->phone,
                'email'                  => $request->email,
                'nature'                 => $request->nature,
                'address'                => $request->address,
                'country_id'             => $request->country_id,
                'group_id'               => $request->group_id,
                'is_taxable'             => $request->is_taxable ?: 0,
                'vat_applicable_from'    => $vat_applicable_from,
                'tax_rate'               => $request->tax_rate ?: 0,
                'is_discount'            => $request->is_discount ?: 0,
                'is_cash'                => $request->is_cash ?: 0,
                'project_general_ledger' => $request->project_general_ledger ?: 0,
                'maintain_bill_by_bill'  => $request->maintain_bill_by_bill ?: 0,
                'tax_applicable'         => $request->tax_applicable ?: 0,
                'is_custom_vat'          => $request->is_custom_vat ?: 0,
                'status'                 => $request->status ?: 'active',
                'iban_no'                => $request->iban_no ?? $ledger->iban_no,
                'swift_code'             => $request->swift_code ?? $ledger->swift_code,
                'account_no'             => $request->account_no ?? $ledger->account_no,
                'branch'                 => $request->branch ?? $ledger->branch,
                'bank_name'              => $request->bank_name ?? $ledger->bank_name,
                'account_name'           => $request->account_name ?? $ledger->account_name,
            ]);

            return redirect()->route("ledgers.index")->with("success", __('general.updated_successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }
    public function delete(Request $request)
    {
        $ledger = (new MainLedger())->setConnection('tenant')->findOrFail($request->id);
        $ledger->delete();
        return redirect()->route("ledgers.index")->with("success", __('general.deleted_successfully'));
    }
    public function show($id)
    {
        $ledger = (new MainLedger())->setConnection('tenant')->findOrFail($id);
        // $sub_groups = Groups::where('group_id' , $id)->get();
        // $countries = Country::get();
        // $parent_group = Groups::where('id' , $group->group_id)->first();
        $data = [
            // 'parent_group' => $parent_group,
            'ledger' => $ledger,
            // 'sub_groups' => $sub_groups,
            // 'countries' => $countries,
        ];
        return view("admin-views.hierarchy.ledgers.show", $data);
    }

    public function update_unit_ledger(Request $request)
    {
        $units = UnitManagement::whereNull('ledger_id')->get();

        $company =   (new Company())->setConnection('tenant')->first();

        // ================= GROUP (PROPERTY GROUP) =================
        foreach ($units as $unitManagement) {


            $group = Groups::where('property_id', $unitManagement->property_management_id)
                ->first();

            // ================= LEDGER =================
            $ledger = MainLedger::create([
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
                'main_type'           => 'unit',
                'is_taxable'          => $group?->is_taxable ?? 0,
                'vat_applicable_from' => $group?->vat_applicable_from,
                'tax_rate'            => $group?->tax_rate ?? $company?->tax_rate,
                'tax_applicable'      => $group?->tax_applicable ?? 0,
                'status'              => 'active',
            ]);

            // ================= COST CENTER =================
            $propertyCost = CostCenterCategory::where('main_id', $unitManagement->property_management_id)
                ->where('main_type', 'property')
                ->first();

            $costCenter = CostCenter::create([
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

            // ================= UPDATE UNIT =================
            $unitManagement->update([
                'ledger_id'       => $ledger->id,
                'cost_center_id'  => $costCenter->id,
            ]);
        }
    }

    public function update_tenant_ledger(Request $request)
    {
        $tenants = Tenant::WhereNull('ledger_id')->get();
        $company = Company::on('tenant')
            ->where('id', optional(auth()->user())->company_id)
            ->first()
            ?? Company::on('tenant')->first();
        foreach ($tenants as $tenant) {





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
}
