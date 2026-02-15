<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\BusinessActivity;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\general\Groups;
use App\Models\hierarchy\MainLedger;
use App\Models\LiveWith;
use App\Models\PropertyCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PropertyCustomerController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            PropertyCustomer::whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $customers     = PropertyCustomer::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        $data = [
            'customers' => $customers,
            'search'  => $search,

        ];
        return view("admin-views.sales.customer.customer_list", $data);
    }

    public function create()
    {

        $country_master      = CountryMaster::get();
        $live_withs          = LiveWith::get();
        $business_activities = BusinessActivity::get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $data = [
            'country_master'      => $country_master,
            'live_withs'          => $live_withs,
            'business_activities' => $business_activities,
            'dail_code_main'       => $dail_code_main,
        ];
        return view("admin-views.sales.customer.create", $data);
    }

    public function edit($id)
    {
        $customer              = PropertyCustomer::findOrFail($id);
        $country_master      = CountryMaster::get();
        $live_withs          = LiveWith::get();
        $business_activities = BusinessActivity::get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $data = [
            'country_master'      => $country_master,
            'live_withs'          => $live_withs,
            'business_activities' => $business_activities,
            'customer'              => $customer,
            'dail_code_main'       => $dail_code_main,
        ];
        return view("admin-views.sales.customer.edit", $data);
    }

    public function store(Request $request)
    {
        if ($request->type == 'individual') {
            $request->validate([
                'name'           => 'required|string|max:255|unique:property_customers,name',
                'gender'         => 'required|string|max:10',
                'live_with_id'   => 'required|integer',
                'country_id'     => 'required|integer',
                'nationality_id' => 'required|integer',
            ]);
        } elseif ($request->type == 'company') {
            $request->validate([
                'company_name'         => 'required|string|max:255|unique:property_customers,company_name',
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
            $customer = PropertyCustomer::storeCustomer($validatedData);
            $main_group = Groups::where('name', 'LIKE', '%Accounts Receivable%')->first();
            $company = (new Company())->setConnection('tenant')->first(); 
            if ($main_group) {
                $group = Groups::firstOrCreate(['name' => 'Property Customer', 'group_id' => $main_group->id  ]);
                $ledger = MainLedger::create([
                    'code'                => ($customer->type == 'individual') ? $customer->name : $customer->company_name,
                    'name'                => ($customer->type == 'individual') ? $customer->name : $customer->company_name,
                    'group_id'            => $group->id,
                    'main_id'             => $customer->id,
                    'main_type'           => 'property_customer',
                    'currency'            => $company->currency_code,
                    'country_id'          => $company->countryid,
                    'is_taxable'          => $group->is_taxable ?: 0,
                    'vat_applicable_from' => $group->vat_applicable_from ?? null,
                    'tax_rate'            => $group->tax_rate ?: 0,
                    'tax_applicable'      => $group->tax_applicable ?: 0,
                    'status'              => 'active',
                ]);
                $customer->update(['ledger_id' => $ledger->id]);    
            }
            DB::commit();
            return redirect()->route('sales.customer.index')->with('success', ui_change('added_successfully'));
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
                Rule::unique('property_customers')->where(function ($query) use ($request) {
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
                Rule::unique('property_customers')->where(function ($query) use ($request) {
                    return $query->where('name', $request->input('name'));
                }),
            ],
            'company_name'         => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('property_customers')->where(function ($query) use ($request) {
                    return $query->where('company_name', $request->input('company_name'));
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

            $customer = PropertyCustomer::storeCustomer($validatedData);

            $main_group = Groups::where('name', 'LIKE', '%Accounts Receivable%')->first();
            $company = (new Company())->setConnection('tenant')->first();
            if ($main_group) {
                $group = Groups::firstOrCreate(['name' => 'Property Customer', 'group_id' => $main_group->id]);
                $ledger = MainLedger::create([
                    'code'                => ($customer->type == 'individual') ? $customer->name : $customer->company_name,
                    'name'                => ($customer->type == 'individual') ? $customer->name : $customer->company_name,
                    'group_id'              => $group->id,
                    'main_id'             => $customer->id,
                    'main_type'           => 'property_customer',
                    'currency'            => $company->currency_code,
                    'country_id'          => $company->countryid,
                    'is_taxable'          => $group->is_taxable ?: 0,
                    'vat_applicable_from' => $group->vat_applicable_from ?? null,
                    'tax_rate'            => $group->tax_rate ?: 0,
                    'tax_applicable'      => $group->tax_applicable ?: 0,
                    'status'              => 'active',
                ]);
            }
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
                Rule::unique('property_customers')->where(function ($query) use ($request) {
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
                Rule::unique('property_customers')->where(function ($query) use ($request) {
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
                Rule::unique('property_customers')->where(function ($query) use ($request) {
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

            $customer = PropertyCustomer::findOrFail($id);

            $customer->update($validatedData);

            return redirect()->route('sales.customer.index')->with('success', ui_change('updated_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function delete(Request $request)
    {
        $customer = PropertyCustomer::findOrFail($request->id);
        $customer->delete();
        return to_route('customer.index')->with('success', ui_change('deleted_successfully'));
    }
    public function statusUpdate(Request $request)
    {
        $main = PropertyCustomer::findOrFail($request->id);
        $main->update([
            'status' => ($request->status == 1) ? 'active' : 'inactive',
        ]);
        return redirect()->back()->with('success', ui_change('updated_successfully'));
    }
}
