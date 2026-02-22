<?php

namespace App\Http\Controllers\property_management;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\facility\Supplier;
use App\Models\general\Groups;
use App\Models\hierarchy\CostCenterCategory;
use App\Models\Ownership;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyManagementController extends Controller
{

    public function index(Request $request)
    {
        // $this->authorize('complaints');
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $property    = (new PropertyManagement())->setConnection('tenant')->forUser()->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        $data = [
            'main'   => $property,
            'search' => $search,

        ];
        return view("admin-views.property_management.property.index", $data);
    }
    public function create()
    {
        $suppliers = Supplier::select('id', 'name')->get();
        $country_master = (new CountryMaster())->setConnection('tenant')->all();
        $property_type  = (new PropertyType())->setConnection('tenant')->all();
        $owner_ship     = (new Ownership())->setConnection('tenant')->all();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data = [
            "property_type"  => $property_type,
            "owner_ship"     => $owner_ship,
            "country_master" => $country_master,
            'dail_code_main' => $dail_code_main,
            'suppliers'      => $suppliers,            
            'company'        => $company,

        ];
        return view("admin-views.property_management.property.create", $data);
    }
    public function edit($id)
    {
        $suppliers = Supplier::select('id', 'name')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $country_master      = (new CountryMaster())->setConnection('tenant')->all();
        $property_type       = (new PropertyType())->setConnection('tenant')->all();
        $owner_ship          = (new Ownership())->setConnection('tenant')->all();
        $property_management = (new PropertyManagement())->setConnection('tenant')->forUser()->findOrFail($id);
        $dail_code_main      = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            "property_type"       => $property_type,
            "owner_ship"          => $owner_ship,
            "country_master"      => $country_master,
            "property_management" => $property_management,
            'dail_code_main'      => $dail_code_main,
            'suppliers'           => $suppliers,
            'company'             => $company,

        ];
        return view("admin-views.property_management.property.edit", $data);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'              => 'required',
            'code'              => 'required',
            'ownership_id'      => 'required',
            'country_master_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $buildingCount             = DB::connection('tenant')->table('companies')->value('building_count');
            $building_management_count = DB::connection('tenant')->table('property_management')->count();
            if ($buildingCount <= $building_management_count) {
                return redirect()->back()->with("error", __('general.you_have_reached_the_maximum_limit'));
            }
            // $master_group                                                      = (new Groups())->setConnection('tenant')->where('id', 48)->first();
            ($request->insurance_period_to != null) ? $insurance_period_to     = Carbon::createFromFormat('d/m/Y', $request->insurance_period_to)->format('Y-m-d') : $insurance_period_to     = null;
            ($request->insurance_period_from != null) ? $insurance_period_from = Carbon::createFromFormat('d/m/Y', $request->insurance_period_from)->format('Y-m-d') : $insurance_period_from = null;
            ($request->established_on != null) ? $established_on               = Carbon::createFromFormat('d/m/Y', $request->established_on)->format('Y-m-d') : $established_on               = null;
            ($request->registration_on != null) ? $registration_on             = Carbon::createFromFormat('d/m/Y', $request->registration_on)->format('Y-m-d') : $registration_on             = null;
            $property_management                                               = (new PropertyManagement())->setConnection('tenant')->create([
                'name'                  => $request->input('name'),
                'code'                  => $request->input('code'),
                'ownership_id'          => $request->input('ownership_id'),
                // 'property_type_id'      => $request->input('property_type_id'),
                'description'           => $request->description,
                'building_no'           => $request->input('building_no'),
                'block_no'              => $request->input('block_no'),
                'road'                  => $request->input('road'),
                'location'              => $request->input('location'),
                'city'                  => $request->input('city'),
                'country_master_id'     => $request->input('country_master_id'),
                'established_on'        => $established_on,
                'registration_on'       => $registration_on,
                'tax_no'                => $request->input('tax_no'),
                'tax_rate'              => $request->input('tax_rate'),
                'municipality_no'       => $request->input('municipality_no'),
                'electricity_no'        => $request->input('electricity_no'),
                'land_lord_name'        => $request->input('land_lord_name'),
                'bank_name'             => $request->input('bank_name'),
                'bank_no'               => $request->input('bank_no'),
                'contact_person'        => $request->input('contact_person'),
                'dail_code_telephone'   => $request->input('dail_code_telephone'),
                'dail_code_fax'         => $request->input('dail_code_fax'),
                'dail_code_mobile'      => $request->input('dail_code_mobile'),
                'mobile'                => $request->input('mobile'),
                'fax'                   => $request->input('fax'),
                'telephone'             => $request->input('telephone'),
                'email'                 => $request->input('email'),
                'total_area'            => $request->input('total_area'),
                'insurance_provider'    => $request->input('insurance_provider'),
                'insurance_period_from' => $insurance_period_from,
                'insurance_period_to'   => $insurance_period_to,
                'insurance_type'        => $request->input('insurance_type'),
                'insurance_policy_no'   => $request->input('insurance_policy_no'),
                'insurance_holder'      => $request->input('insurance_holder'),
                'premium_amount'        => $request->input('premium_amount'),
                'status'                => $request->input('status'),
            ]);
            // master_group
            // $group = (new Groups())->setConnection('tenant')->create([
            //     'code'                     => $request->input('code'),
            //     'property_id'              => $property_management->id,
            //     'name'                     => $request->input('name'),
            //     'display_name'             => $request->input('name'),
            //     'group_id'                 => $master_group->id,
            //     'is_projects_parent_group' => $master_group->is_projects_parent_group ?: 0,
            //     'enable_auto_code'         => $master_group->enable_auto_code ?: 0,
            //     'status'                   => 'active',
            //     'tax_applicable'           => $master_group->tax_applicable ?: 0,
            //     'is_taxable'               => $master_group->is_taxable ?: 0,
            //     'vat_applicable_from'      => $master_group->vat_applicable_from ?? null,
            //     'tax_rate'                 => $master_group->tax_rate ?: 0,
            // ]);
            // $cost_center = (new CostCenterCategory())->setConnection('tenant')->create([
            //     'code'      => $request->input('code'),
            //     'name'      => $request->input('name'),
            //     'main_id'   => $property_management->id,
            //     'main_type' => 'property',
            //     'status'    => 'active',
            // ]);
            if ($property_management) {
                $property_management->property_types()->sync($request->property_type_id);
            }
            DB::commit();
            return redirect()->route("property_management.index")->with("success", __('property_master.added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'              => 'required',
            'code'              => 'required',
            'ownership_id'      => 'required|integer',
            'country_master_id' => 'required|integer',
        ]);
        DB::beginTransaction();
        try {
            // $master_group = (new Groups())->setConnection('tenant')->where('id', 48)->first();

            $property_management                                               = (new PropertyManagement())->setConnection('tenant')->findOrFail($id);
            ($request->insurance_period_to != null) ? $insurance_period_to     = Carbon::createFromFormat('d/m/Y', $request->insurance_period_to)->format('Y-m-d') : $insurance_period_to     = null;
            ($request->insurance_period_from != null) ? $insurance_period_from = Carbon::createFromFormat('d/m/Y', $request->insurance_period_from)->format('Y-m-d') : $insurance_period_from = null;
            ($request->established_on != null) ? $established_on               = Carbon::createFromFormat('d/m/Y', $request->established_on)->format('Y-m-d') : $established_on               = null;
            ($request->registration_on != null) ? $registration_on             = Carbon::createFromFormat('d/m/Y', $request->registration_on)->format('Y-m-d') : $registration_on             = null;
            $is_update                                                         = $property_management->update([
                'name'                  => $request->input('name'),
                'code'                  => $request->input('code'),
                'ownership_id'          => $request->input('ownership_id'),
                // 'property_type_id'      => $request->input('property_type_id'),
                'description'           => $request->description,

                'building_no'           => $request->input('building_no'),
                'block_no'              => $request->input('block_no'),
                'road'                  => $request->input('road'),
                'location'              => $request->input('location'),
                'city'                  => $request->input('city'),
                'country_master_id'     => $request->input('country_master_id'),
                'established_on'        => $established_on,
                'registration_on'       => $registration_on,
                'tax_no'                => $request->input('tax_no'),
                'tax_rate'              => $request->input('tax_rate'),
                'municipality_no'       => $request->input('municipality_no'),
                'electricity_no'        => $request->input('electricity_no'),
                'land_lord_name'        => $request->input('land_lord_name'),
                'bank_name'             => $request->input('bank_name'),
                'bank_no'               => $request->input('bank_no'),
                'contact_person'        => $request->input('contact_person'),
                'dail_code_telephone'   => $request->input('dail_code_telephone'),
                'dail_code_fax'         => $request->input('dail_code_fax'),
                'dail_code_mobile'      => $request->input('dail_code_mobile'),
                'mobile'                => $request->input('mobile'),
                'fax'                   => $request->input('fax'),
                'telephone'             => $request->input('telephone'),
                'email'                 => $request->input('email'),
                'total_area'            => $request->input('total_area'),
                'insurance_provider'    => $request->input('insurance_provider'),
                'insurance_period_from' => $insurance_period_from,
                'insurance_period_to'   => $insurance_period_to,
                'insurance_type'        => $request->input('insurance_type'),
                'insurance_policy_no'   => $request->input('insurance_policy_no'),
                'insurance_holder'      => $request->input('insurance_holder'),
                'premium_amount'        => $request->input('premium_amount'),
                'status'                => $request->input('status'),
            ]);
            if ($is_update) {
                $property_management->property_types()->sync($request->property_type_id);
            }

            DB::commit();
            return redirect()->route("property_management.index")->with("success", __('property_master.added_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function show($id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->findOrFail($id);
        $data     = [
            'property' => $property,
        ];
        return view('admin-views.property_management.property.show', $data);
    }

    public function delete(Request $request)
    {
        $property    = (new PropertyManagement())->setConnection('tenant')->findOrFail($request->id);
        $cost_center = (new CostCenterCategory())->setConnection('tenant')->where('main_type', 'property')->where('main_id', $request->id)->first();
        if ($cost_center) {
            $cost_center->delete();
        }
        $group = (new Groups())->setConnection('tenant')->where('property_id', $request->id)->first();
        if ($group) {
            $group->delete();
        }
        (new PropertyManagement())->setConnection('tenant')->findOrFail($request->id)->delete();
        return redirect()->back()->with('success', __('general.deleted_successfully'));
    }

    public function view_image($id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        // $property = (new PropertyManagement())->setConnection('tenant')->findOrFail($id);
        $data = [
            'property_item' => $property,
        ];
        return view('admin-views.property_management.property.view_image', $data);
    }
    public function list_view($id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->forUser()->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        // $property = (new PropertyManagement())->setConnection('tenant')->findOrFail($id);
        $data = [
            'property_item' => $property,
        ];
        return view('admin-views.property_management.property.list_view', $data);
    }
}
