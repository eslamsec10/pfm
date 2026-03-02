<?php

namespace App\Http\Controllers\property_transactions;

use App\Exports\TenantTemplate;
use App\Http\Controllers\Controller;
use App\Models\BusinessActivity;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\general\Groups;
use App\Models\hierarchy\MainLedger;
use App\Models\LiveWith;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            (new Tenant())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $tenants     = (new Tenant())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        $data = [
            'tenants' => $tenants,
            'search'  => $search,

        ];
        return view("admin-views.property_transactions.tenants.tenant_list", $data);
    }

    public function create()
    {

        $country_master      = (new CountryMaster())->setConnection('tenant')->get();
        $live_withs          = (new LiveWith())->setConnection('tenant')->get();
        $business_activities = (new BusinessActivity())->setConnection('tenant')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'country_master'      => $country_master,
            'live_withs'          => $live_withs,
            'business_activities' => $business_activities,
            'dail_code_main'      => $dail_code_main,
        ];
        return view("admin-views.property_transactions.tenants.create", $data);
    }

    public function edit($id)
    {
        $tenant              = (new Tenant())->setConnection('tenant')->findOrFail($id);
        $country_master      = (new CountryMaster())->setConnection('tenant')->get();
        $live_withs          = (new LiveWith())->setConnection('tenant')->get();
        $business_activities = (new BusinessActivity())->setConnection('tenant')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'country_master'      => $country_master,
            'live_withs'          => $live_withs,
            'business_activities' => $business_activities,
            'tenant'              => $tenant,
            'dail_code_main'      => $dail_code_main,
        ];
        return view("admin-views.property_transactions.tenants.edit", $data);
    }

    public function store(Request $request)
    {
        if ($request->type == 'individual') {
            $request->validate([
                'name'           => 'required|string|max:255|unique:tenants,name',
                'gender'         => 'required|string|max:10',
                'live_with_id'   => 'required|integer',
                'country_id'     => 'required|integer',
                'nationality_id' => 'required|integer',
            ]);
        } elseif ($request->type == 'company') {
            $request->validate([
                'company_name'         => 'required|string|max:255|unique:tenants,company_name',
                'business_activity_id' => 'required|integer',
                'country_id'           => 'required|integer',
                'contact_person'       => 'required|string|max:255',
            ]);
        }

        $validatedData = $request->validate([
            'name'                 => 'nullable|string|max:255',
            'gender'               => 'nullable|string|max:10',
            'tax_registration'     => 'nullable|string',
            'vat_no'               => 'nullable|string',
            'id_number'            => 'nullable|string|max:50',
            'registration_no'      => 'nullable|string|max:50',
            'nick_name'            => 'nullable|string|max:255',
            'group_company_name'   => 'nullable|string|max:255',
            'contact_person'       => 'nullable|string|max:255',
            'designation'          => 'nullable|string|max:255',
            'contact_no'           => 'nullable|string|max:20',
            'whatsapp_no'          => 'nullable|string|max:20',
            'whatsapp_dail_code'   => 'nullable|string|max:20',
            'company_name'         => 'nullable|string|max:255',
            'fax_no'               => 'nullable|string|max:20',
            'telephone_no'         => 'nullable|string|max:20',
            'other_contact_no'     => 'nullable|string|max:20',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'state'                => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:255',
            'country_id'           => 'nullable|integer',
            'nationality_id'       => 'nullable|integer',
            'passport_no'          => 'nullable|string|max:50',
            'email1'               => 'nullable|email|max:255',
            'email2'               => 'nullable|email|max:255',
            'live_with_id'         => 'nullable|integer',
            'business_activity_id' => 'nullable|integer',
            'document'             => 'nullable|string|max:255',
            'type'                 => 'required|string|max:50',
        ]);
        DB::beginTransaction();
        try {

            $tenant = (new Tenant())->setConnection('tenant')->storeTenant($validatedData);
            // $company = auth()->user() ?? (new User())->setConnection()->first();
            

            DB::commit();
            return redirect()->route('tenant.index')->with('success', __('property_master.added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function store_for_anything(Request $request)
    {
        if ($request->type == 'individual') {
            $request->validate([
                'name'           => 'required|string|max:255',
                'gender'         => 'required|string|max:10',
                'live_with_id'   => 'required|integer',
                'country_id'     => 'required|integer',
                'nationality_id' => 'required|integer',
            ]);
        } elseif ($request->type == 'company') {
            $request->validate([
                'company_name'         => 'required|string|max:255',
                'business_activity_id' => 'required|integer',
                'country_id'           => 'required|integer',
                'contact_person'       => 'required|string|max:255',
            ]);
        }
        $validatedData = $request->validate([
            'name'                 => [
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('company_name', $request->input('company_name'));
                }),
            ],
            'gender'               => 'string|max:10',
            'id_number'            => 'nullable|string|max:50',
            'registration_no'      => 'nullable|string|max:50',
            'nick_name'            => 'nullable|string|max:255',
            'group_company_name'   => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('name', $request->input('name'));
                }),
            ],
            'company_name'         => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('name', $request->input('name'));
                }),
            ],
            'contact_person'       => 'nullable|string|max:255',
            'designation'          => 'nullable|string|max:255',
            'contact_no'           => 'nullable|string|max:20',
            'whatsapp_no'          => 'nullable|string|max:20',
            'whatsapp_dail_code'   => 'nullable|string|max:20',
            'fax_no'               => 'nullable|string|max:20',
            'telephone_no'         => 'nullable|string|max:20',
            'other_contact_no'     => 'nullable|string|max:20',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'state'                => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:255',
            'country_id'           => 'required|nullable|integer',
            'nationality_id'       => 'nullable|integer',
            'passport_no'          => 'nullable|string|max:50',
            'email1'               => 'nullable|email|max:255',
            'email2'               => 'nullable|email|max:255',
            'live_with_id'         => 'nullable|integer',
            'business_activity_id' => 'nullable|integer',
            'document'             => 'nullable|string|max:255',
            'type'                 => 'required|string|max:50',
        ]);
        DB::beginTransaction();
        try {

            $tenant = (new Tenant())->setConnection('tenant')->storeTenant($validatedData);
            // $company = auth()->user() ?? (new User())->setConnection('tenant')->first();
            // $company = (new Company())->setConnection('tenant')->where('id', auth()->user()?->company_id)->first() ?? (new Company())->setConnection('tenant')->first();
            // $group   = (new Groups())->setConnection('tenant')->where('id', 49)->first();
            // $ledger  = (new MainLedger())->setConnection('tenant')->create([
            //     'code'                => ($tenant->type == 'individual') ? $tenant->nick_name : $tenant->group_company_name,
            //     'name'                => ($tenant->type == 'individual') ? $tenant->name : $tenant->company_name,
            //     'currency'            => $company->currency_code,
            //     'country_id'          => $company->countryid,
            //     'group_id'            => $group->id,
            //     'is_taxable'          => $group->is_taxable ?: 0,
            //     'vat_applicable_from' => $group->vat_applicable_from ?? null,
            //     'tax_rate'            => $group->tax_rate ?: 0,
            //     'tax_applicable'      => $group->tax_applicable ?: 0,
            //     'status'              => 'active',
            // ]);

            DB::commit();
            return redirect()->back()->with('success', __('general.added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        if ($request->type == 'individual') {
            $request->validate([
                'name'           => 'required|string|max:255',
                'gender'         => 'required|string|max:10',
                'live_with_id'   => 'required|integer',
                'country_id'     => 'required|integer',
                'nationality_id' => 'required|integer',
            ]);
        } elseif ($request->type == 'company') {
            $request->validate([
                'company_name'         => 'required|string|max:255',
                'business_activity_id' => 'required|integer',
                'country_id'           => 'required|integer',
                'contact_person'       => 'required|string|max:255',
            ]);
        }
        $request->request->remove('q');
        $validatedData = $request->validate([
            'name'                 => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('company_name', $request->input('company_name'));
                })->ignore($id),
            ],
            'gender'               => 'string|max:10',
            'id_number'            => 'nullable|string|max:50',
            'registration_no'      => 'nullable|string|max:50',
            'nick_name'            => 'nullable|string|max:255',
            'group_company_name'   => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('company_name', $request->input('company_name'));
                })->ignore($id),
            ],

            'contact_person'       => 'nullable|string|max:255',
            'designation'          => 'nullable|string|max:255',
            'contact_no'           => 'nullable|string|max:20',
            'whatsapp_no'          => 'nullable|string|max:20',
            'whatsapp_dail_code'   => 'nullable|string|max:20',
            'company_name'         => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants')->where(function ($query) use ($request) {
                    return $query->where('name', $request->input('name'));
                })->ignore($id),
            ],
            'fax_no'               => 'nullable|string|max:20',
            'telephone_no'         => 'nullable|string|max:20',
            'other_contact_no'     => 'nullable|string|max:20',
            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'state'                => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:255',
            'country_id'           => 'required|nullable|integer',
            'nationality_id'       => 'nullable|integer',
            'passport_no'          => 'nullable|string|max:50',
            'email1'               => 'nullable|email|max:255',
            'email2'               => 'nullable|email|max:255',
            'live_with_id'         => 'nullable|integer',
            'business_activity_id' => 'nullable|integer',
            'document'             => 'nullable|string|max:255',
            'type'                 => 'required|string|max:50',
        ]);
        try {

            $tenant = (new Tenant())->setConnection('tenant')->findOrFail($id);

            $tenant->update($validatedData);

            return redirect()->route('tenant.index')->with('success', __('property_master.added_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function delete(Request $request)
    {
        $tenant = (new Tenant())->setConnection('tenant')->findOrFail($request->id);
        $tenant->delete();
        return to_route('tenant.index')->with('success', __('general.deleted_successfully'));
    }
    public function statusUpdate(Request $request)
    {
        $main = (new Tenant())->setConnection('tenant')->findOrFail($request->id);
        $main->update([
            'status' => ($request->status == 1) ? 'active' : 'inactive',
        ]);
        return redirect()->back()->with('success', __('property_master.updated_successfully'));
    }

    public function exportTenants()
    {
        return Excel::download(new TenantTemplate, 'tenants.xlsx');
    }
}
