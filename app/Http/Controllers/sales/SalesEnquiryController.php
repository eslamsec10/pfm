<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\BusinessActivity;
use App\Models\BusinessSetting;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\LiveWith;
use App\Models\PropertyCustomer;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\RoomFacility;
use App\Models\sales\SalesEnquiry;
use App\Models\sales\SalesEnquiryUnitSearchDetails;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\UnitType;
use App\Models\View;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $ids     = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $enquiries = SalesEnquiry::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('enquiry_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param);

        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = SalesEnquiry::query();
            if ($request->booking_status && $request->booking_status != -1) {
                $report_query->where('booking_status', $request->booking_status);
            }
            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
                $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
                $report_query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $enquiries = $report_query->orderBy('created_at', 'desc')->paginate();
        }
        $data = [
            'enquiries'              => $enquiries,
            'search'                 => $search,
        ];

        return view("admin-views.sales.enquiries.enquiry_list", $data);
    }

    public function create()
    {
        $customers                  = PropertyCustomer::select('id', 'name', 'company_name', 'type')->paginate();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = UnitDescription::get();
        $unit_conditions          = UnitCondition::get();
        $unit_types               = UnitType::get();
        $views                    = View::get();
        $property_types           = PropertyType::get();
        $country_master           = CountryMaster::get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();
        $data                     = [
            'unit_types'               => $unit_types,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'customers'                  => $customers,
            'company'                    => $company,
            'country_master'             => $country_master,
            'live_withs'                 => $live_withs,
            'business_activities'        => $business_activities,
            'dail_code_main'             => $dail_code_main,
        ];
        return view('admin-views.sales.enquiries.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'enquiry_no' => 'required|unique:tenant.sales_enquiries,enquiry_no',
            'enquiry_date' => 'required',
            'customer_id' => 'required',
        ]);
        $data       = $request->all();
        $unitFields = collect($data)->filter(function ($value, $key) {
            return Str::startsWith($key, 'no_of_unit-');
        });
        $hasAtLeastOne = $unitFields->filter(function ($value) {
            return ! is_null($value) && $value !== '' && $value > 0;
        })->isNotEmpty();
        if (! $hasAtLeastOne) {
            return back()->withErrors(['no_of_unit' => ui_change('At_least_one_unit_must_be_entered.')])->withInput();
        }
        $rules = [];
        foreach ($unitFields as $key => $value) {
            if (! empty($value)) {
                $rules[$key] = 'numeric|min:1';
            }
        }

        $request->validate($rules);
        $request->validate($rules);
        if ($request->enquiry_date) {
            $enquiry_date = Carbon::createFromFormat('d/m/Y', $request->enquiry_date)->format('Y-m-d');
        }

        DB::beginTransaction();
        try {
            $enquiry = DB::connection('tenant')->table('sales_enquiries')->insertGetId([
                'enquiry_no'           => $request->enquiry_no,
                'enquiry_date'         => $enquiry_date,
                'customer_id'            => $request->customer_id,
                'booking_status'       => 'enquiry',
                'created_at'           => Carbon::now(),
            ]);
            if ($enquiry) {
                if ($enquiry && $request->has('property_id')) {

                    foreach ($request->property_id as $key => $property) {

                        $unit_description_id = $request->unit_description_id[$key] ?? null;
                        $rent_amount         = $request->input('rent_amount-' . $unit_description_id);
                        SalesEnquiryUnitSearchDetails::create([
                            'enquiry_id'             => $enquiry,
                            'property_management_id' => $request->property_id[$key],
                            'unit_management_id'     => $request->unit_management_id[$key] ?? null,
                            'unit_description_id'    => $unit_description_id,
                            'unit_type_id'           => $request->unit_type_id[$key] ?? null,
                            'unit_condition_id'      => $request->unit_condition_id[$key] ?? null,
                            'view_id'                => (
                                isset($request->view_id[$key]) &&
                                $request->view_id[$key] != -1
                            ) ? $request->view_id[$key] : null,
                            'property_type'          => $request->property_type[$key] ?? null,
                            'total_area_required'    => $request->total_area_required[$key] ?? null,
                            'area_measurement'       => $request->area_measurement[$key] ?? null,
                            'city'                   => $request->city_unit_desc[$key] ?? null,
                            'comment'                => $request->comment[$key] ?? null,
                            'price'                  => $request->rent_amount[$key] ?? $rent_amount,
                        ]);

                        if (!empty($request->unit_management_id[$key])) {
                            $unit_management = (new UnitManagement())
                                ->setConnection('tenant')
                                ->where('id', $request->unit_management_id[$key])
                                ->first();

                            if ($unit_management) {
                                $unit_management->update([
                                    'sales_status' => 'enquiry',
                                    'tenant_id'      => $request->tenant_id
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
        return to_route('sales.enquiry.index')->with('success', ui_change('added_successfully'));
    }

    public function edit($id)
    {
        $enquiry              = SalesEnquiry::findOrFail($id);
        $enquiry_unit_details = SalesEnquiryUnitSearchDetails::where('enquiry_id', $id)->get();
        $customers                  = PropertyCustomer::select('id', 'name', 'company_name', 'type')->paginate();
        $unit_management_all  = UnitManagement::select('id', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id')
            ->with(
                'block_unit_management',
                'property_unit_management',
                'block_unit_management.block',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main',
                'unit_description',
                'unit_type',
                'view',
                'unit_condition'
            )->lazy();

        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $country_master           = (new CountryMaster())->setConnection('tenant')->get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $data                     = [
            'enquiry'                  => $enquiry,
            'unit_types'               => $unit_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'customers'                  => $customers,
            'property_types'           => $property_types,
            'enquiry_unit_details'     => $enquiry_unit_details,
            'unit_management_all'      => $unit_management_all,
            'dail_code_main'            => $dail_code_main,
            'company'                   => $company,
        ];
        return view('admin-views.sales.enquiries.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'enquiry_no' => "required|unique:tenant.sales_enquiries,enquiry_no,{$id}",
            'enquiry_date' => 'required',
            'customer_id' => 'required',
        ]);

        $unitFields = collect($request->all())->filter(fn($value, $key) => Str::startsWith($key, 'no_of_unit-'));

        $hasAtLeastOne = $unitFields->filter(fn($value) => !is_null($value) && $value !== '' && $value > 0)->isNotEmpty();

        if (!$hasAtLeastOne) {
            return back()->withErrors(['no_of_unit' => ui_change('At_least_one_unit_must_be_entered.')])->withInput();
        }

        $rules = [];
        foreach ($unitFields as $key => $value) {
            if (!empty($value)) {
                $rules[$key] = 'numeric|min:1';
            }
        }

        $request->validate($rules);

        $enquiry_date = Carbon::createFromFormat('d/m/Y', $request->enquiry_date)->format('Y-m-d');

        DB::beginTransaction();
        try {
            DB::connection('tenant')->table('sales_enquiries')
                ->where('id', $id)
                ->update([
                    'enquiry_no' => $request->enquiry_no,
                    'enquiry_date' => $enquiry_date,
                    'customer_id' => $request->customer_id,
                    'updated_at' => Carbon::now(),
                ]);

            if ($request->has('property_id')) {
                foreach ($request->property_id as $key => $property) {
                    $unit_description_id = $request->unit_description_id[$key] ?? null;
                    $rent_amount = $request->input('rent_amount-' . $unit_description_id);
                    $unit_id = $request->unit_id[$key] ?? null;

                    if ($unit_id) {
                        $unit = SalesEnquiryUnitSearchDetails::find($unit_id);
                        if ($unit) {
                            $unit->update([
                                'property_management_id' => $property,
                                'unit_management_id' => $request->unit_management_id[$key] ?? null,
                                'unit_description_id' => $unit_description_id,
                                'unit_type_id' => $request->unit_type_id[$key] ?? null,
                                'unit_condition_id' => $request->unit_condition_id[$key] ?? null,
                                'view_id' => (isset($request->view_id[$key]) && $request->view_id[$key] != -1) ? $request->view_id[$key] : null,
                                'property_type' => $request->property_type[$key] ?? null,
                                'total_area_required' => $request->total_area_required[$key] ?? null,
                                'area_measurement' => $request->area_measurement[$key] ?? null,
                                'city' => $request->city_unit_desc[$key] ?? null,
                                'comment' => $request->comment[$key] ?? null,
                                'price' => $request->rent_amount[$key] ?? $rent_amount,
                            ]);
                        }
                    } else {
                        SalesEnquiryUnitSearchDetails::create([
                            'enquiry_id' => $id,
                            'property_management_id' => $property,
                            'unit_management_id' => $request->unit_management_id[$key] ?? null,
                            'unit_description_id' => $unit_description_id,
                            'unit_type_id' => $request->unit_type_id[$key] ?? null,
                            'unit_condition_id' => $request->unit_condition_id[$key] ?? null,
                            'view_id' => (isset($request->view_id[$key]) && $request->view_id[$key] != -1) ? $request->view_id[$key] : null,
                            'property_type' => $request->property_type[$key] ?? null,
                            'total_area_required' => $request->total_area_required[$key] ?? null,
                            'area_measurement' => $request->area_measurement[$key] ?? null,
                            'city' => $request->city_unit_desc[$key] ?? null,
                            'comment' => $request->comment[$key] ?? null,
                            'price' => $request->rent_amount[$key] ?? $rent_amount,
                        ]);
                    }

                    if (!empty($request->unit_management_id[$key])) {
                        $unit_management = (new UnitManagement())
                            ->setConnection('tenant')
                            ->where('id', $request->unit_management_id[$key])
                            ->first();

                        if ($unit_management) {
                            $unit_management->update([
                                'booking_status' => 'enquiry',
                                'tenant_id' => $request->tenant_id
                            ]);
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }

        return to_route('sales.enquiry.index')->with('success', ui_change('updated_successfully'));
    }


    public function delete(Request $request)
    {
        $enquiry = SalesEnquiry::findOrFail($request->id);

        $enquiry_units = SalesEnquiryUnitSearchDetails::select('id', 'unit_management_id')->where('enquiry_id', $request->id)->get();

        foreach ($enquiry_units as $enquiry_unit_item) {

            $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $enquiry_unit_item->unit_management_id)->select('id', 'booking_status')->first();
            if (isset($unit_management)) {
                $unit_management->update([
                    'booking_status' => 'empty',
                ]);
            }
            $enquiry_unit_item->delete();
        }
        $enquiry->delete();
        return redirect()->route('enquiry.index')->with("success", ui_change('deleted_successfully'));
    }

    public function create_with_select_unit(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($ids == null) {
            return redirect()->back()->with('error', 'Please Select Unit');
        }

        $customers = PropertyCustomer::select('id', 'name', 'company_name', 'type')
            ->with('country_master')->orderBy('created_at', 'desc')
            ->paginate();

        $country_master           =   CountryMaster::select('id', 'country_id')->with('country')->lazy();
        $all_units                =   UnitManagement::select('id', 'sales_price', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id')->whereIn('id', $ids)
            ->with(
                'latest_rent_schedule',
                'block_unit_management',
                'property_unit_management',
                'block_unit_management.block',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main',
                'unit_description',
                'unit_type',
                'view',
                'unit_condition'
            )->lazy();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $live_withs          = LiveWith::select('id', 'name')->lazy();
        $business_activities = BusinessActivity::select('id', 'name')->lazy();
        $buildings           = PropertyManagement::forUser()->select('id', 'name')->lazy();
        $unit_descriptions   = UnitDescription::select('id', 'name')->lazy();
        $unit_conditions     = UnitCondition::select('id', 'name')->lazy();
        $unit_types          = UnitType::select('id', 'name')->lazy();
        $views               = View::select('id', 'name')->lazy();
        $property_types      = PropertyType::select('id', 'name')->lazy();
        $data                = [
            'all_units'                => $all_units,
            'unit_types'               => $unit_types,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'customers'                  => $customers,
            'dail_code_main'            => $dail_code_main,
        ];
        return view('admin-views.sales.enquiries.create_with_select_unit', $data);
    }

    public function book_now(Request $request)
    {
        $customers = PropertyCustomer::select('id', 'name', 'company_name')->get();

        $propertyQuery = PropertyManagement::with(
            'blocks_management_child:id,block_id',
            'blocks_management_child.block:id,name',
            'blocks_management_child.floors_management_child:id,floor_id',
            'blocks_management_child.floors_management_child.floor_management_main:id,name',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->forUser();

        $unit_descriptions = UnitDescription::select('id', 'name')->get();
        $unit_types = UnitType::select('id', 'name')->get();
        $unit_conditions = UnitCondition::select('id', 'name')->get();
        $unit_facilities = RoomFacility::select('id', 'name')->get();

        $filterUnitDescriptionId = $request->get('unit_description_id', -1);
        $filterUnitTypeId = $request->get('unit_type_id', -1);
        $filterUnitConditionId = $request->get('unit_condition_id', -1);
        $filterUnitFacilityId = $request->get('unit_facility_id', -1);
        $filterAdults = $request->get('adults', null);
        $filterChildren = $request->get('children', null);
        // -------------------------------
        // Apply filters
        // -------------------------------

        $propertyQuery->with(['blocks_management_child.floors_management_child.unit_management_child' => function ($q) use (
            $filterUnitDescriptionId,
            $filterUnitTypeId,
            $filterUnitConditionId,
            $filterUnitFacilityId,
        ) {

            if ($filterUnitDescriptionId != -1) {
                $q->where('unit_description_id', $filterUnitDescriptionId);
            }

            if ($filterUnitTypeId != -1) {
                $q->where('unit_type_id', $filterUnitTypeId);
            }

            if ($filterUnitConditionId != -1) {
                $q->where('unit_condition_id', $filterUnitConditionId);
            }

            if ($filterUnitFacilityId != -1) {
                $q->whereHas('facilities', function ($q2) use ($filterUnitFacilityId) {
                    $q2->where('facility_id', $filterUnitFacilityId);
                });
            }
        }]);


        $property = $propertyQuery->get();
        $agreement_color = BusinessSetting::whereType('agreement_color')->value('value');
        $booking_color   = BusinessSetting::whereType('booking_color')->value('value');
        $proposal_color  = BusinessSetting::whereType('proposal_color')->value('value');
        $enquiry_color   = BusinessSetting::whereType('enquiry_color')->value('value');

        $data = [
            'property_items' => $property,
            'customers' => $customers,
            'unit_descriptions' => $unit_descriptions,
            'unit_types' => $unit_types,
            'unit_conditions' => $unit_conditions,
            'unit_facilities' => $unit_facilities,
            'filterUnitDescriptionId' => $filterUnitDescriptionId,
            'filterUnitTypeId' => $filterUnitTypeId,
            'filterUnitConditionId' => $filterUnitConditionId,
            'filterUnitFacilityId' => $filterUnitFacilityId,
            'filterAdults' => $filterAdults,
            'filterChildren' => $filterChildren,
            'agreement_color'   => $agreement_color,
            'booking_color'   => $booking_color,
            'proposal_color'   => $proposal_color,
            'enquiry_color'   => $enquiry_color,
        ];
        return view('admin-views.sales.book', $data);
    }



    // services 


    public function empty_unit_from_enquiry_unit_search($id)
    {
        $enquiry_unit = SalesEnquiryUnitSearchDetails::select('id', 'unit_management_id')->where('id', $id)->first();
        if ($enquiry_unit->unit_management_id) {
            $unit_management = UnitManagement::where('id', $enquiry_unit->unit_management_id)->first();
            $unit_management->update([
                'sales_status' => 'empty',
            ]);
        }
        $enquiry_unit->delete();
        return redirect()->back()->with('success', __('general.deleted_successfully'));
    }
    public function get_customer($id)
    {
        $customer = PropertyCustomer::find($id);
        return json_encode($customer);
    }
}
