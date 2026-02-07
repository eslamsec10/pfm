<?php

namespace App\Http\Controllers\property_transactions;

use Throwable;
use Carbon\Carbon;
use App\Models\View;
use App\Models\Agent;
use App\Models\Tenant;
use App\Models\Enquiry;
use App\Models\Employee;
use App\Models\LiveWith;
use App\Models\Proposal;
use App\Models\UnitType;
use Illuminate\Support\Str;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use App\Models\CountryMaster;
use App\Models\EnquiryStatus;
use App\Models\ProposalUnits;
use App\Models\ServiceMaster;
use App\Models\UnitCondition;
use App\Models\EnquiryDetails;
use App\Models\UnitManagement;
use App\Models\BlockManagement;
use App\Models\BusinessSetting;
use App\Models\FloorManagement;
use App\Models\ProposalDetails;
use App\Models\UnitDescription;
use App\Models\BusinessActivity;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\EnquiryRequestStatus;
use App\Http\Requests\EnquiryRequest;
use Illuminate\Support\Facades\Cache;
use App\Models\EnquiryUnitSearchDetails;

class EnquiryController extends Controller
{

    public function index(Request $request)
    {
        // $this->authorize('unit_management');
        $ids     = $request->bulk_ids;
        $lastRun = Cache::get('last_enquiry_expiry_run');
        if (! $lastRun || now()->diffInHours($lastRun) >= 24) {
            $enquiry_settings = get_business_settings('enquiry')->where('type', 'enquiry_expire_date')->first();
            $expiry_days      = $enquiry_settings ? (int) $enquiry_settings->value : 0;

            if ($expiry_days > 0) {
                $this->expire_unit($expiry_days);
                Cache::put('last_enquiry_expiry_run', now(), now()->addDay());
            }
        }

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $enquiries = (new Enquiry())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('enquiry_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->whereHas('enquiry_details', function ($qu) {
                $qu->where('enquiry_request_status_id', 1);
            })
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param);

        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = (new Enquiry())->setConnection('tenant')->query();

            if ($request->booking_status && $request->booking_status != -1) {
                $report_query->where('booking_status', $request->booking_status);
            }

            if ($request->enquiry_request_status && $request->enquiry_request_status != -1) {
                $report_query->whereHas('enquiry_details.enquiry_request_status', function ($query) use ($request) {
                    $query->where('enquiry_request_status_id', $request->enquiry_request_status);
                });
            }

            if ($request->enquiry_status && $request->enquiry_status != -1) {
                $report_query->whereHas('enquiry_details.enquiry_status', function ($query) use ($request) {
                    $query->where('enquiry_status_id', $request->enquiry_status);
                });
            }

            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
                $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
                $report_query->whereBetween('created_at', [$startDate, $endDate]);
            }

            $enquiries = $report_query->orderBy('created_at', 'desc')->paginate();
        }

        $enquiry_request_status = (new EnquiryRequestStatus())->setConnection('tenant')->get();
        $enquiry_status         = (new EnquiryStatus())->setConnection('tenant')->get();

        $data = [
            'enquiries'              => $enquiries,
            'enquiry_status'         => $enquiry_status,
            'enquiry_request_status' => $enquiry_request_status,
            'search'                 => $search,
        ];

        return view("admin-views.property_transactions.enquiries.enquiry_list", $data);
    }

    public function create_with_select_unit(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($ids == null) {
            return redirect()->back()->with('error', 'Please Select Unit');
        }

        $allTenants = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')
            ->with('country_master')->orderBy('created_at', 'desc')
            ->paginate(100);

        $agents                   = (new Agent())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')->lazy(5000);
        $enquiry_statuses         = (new EnquiryStatus())->setConnection('tenant')->select('id', 'name')->lazy();
        $enquiry_request_statuses = (new EnquiryRequestStatus())->setConnection('tenant')->select('id', 'name')->lazy();
        $employees                = (new Employee())->setConnection('tenant')->select('id', 'name')->lazy();
        $country_master           = (new CountryMaster())->setConnection('tenant')->select('id', 'country_id')->with('country')->lazy();
        $all_units                = (new UnitManagement())->setConnection('tenant')->select('id', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id')->whereIn('id', $ids)
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
        $live_withs          = (new LiveWith())->setConnection('tenant')->select('id', 'name')->lazy();
        $business_activities = (new BusinessActivity())->setConnection('tenant')->select('id', 'name')->lazy();
        $buildings           = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name')->lazy();
        $unit_descriptions   = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_conditions     = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_types          = (new UnitType())->setConnection('tenant')->select('id', 'name')->lazy();
        $views               = (new View())->setConnection('tenant')->select('id', 'name')->lazy();
        $property_types      = (new PropertyType())->setConnection('tenant')->select('id', 'name')->lazy();
        $data                = [
            'all_units'                => $all_units,
            'unit_types'               => $unit_types,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'enquiry_request_statuses' => $enquiry_request_statuses,
            'enquiry_statuses'         => $enquiry_statuses,
            'employees'                => $employees,
            'agents'                   => $agents,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'tenants'                  => $allTenants,
        ];
        return view('admin-views.property_transactions.enquiries.create_with_select_unit', $data);
    }
    public function create()
    {
        $tenants                  = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')->paginate(5000);
        $agents                   = DB::connection('tenant')->table('agents')->get();
        $enquiry_statuses         = DB::connection('tenant')->table('enquiry_statuses')->get();
        $enquiry_request_statuses = DB::connection('tenant')->table('enquiry_request_statuses')->get();
        $employees                = DB::connection('tenant')->table('employees')->get();
        $country_master           = CountryMaster::get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $data                     = [
            'unit_types'               => $unit_types,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'enquiry_request_statuses' => $enquiry_request_statuses,
            'enquiry_statuses'         => $enquiry_statuses,
            'employees'                => $employees,
            'agents'                   => $agents,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'tenants'                  => $tenants,
        ];
        return view('admin-views.property_transactions.enquiries.create', $data);
    }

    public function edit($id)
    {
        $enquiry              = (new Enquiry())->setConnection('tenant')->findOrFail($id);
        $enquiry_details      = (new EnquiryDetails())->setConnection('tenant')->where('enquiry_id', $id)->first();
        $enquiry_unit_details = (new EnquiryUnitSearchDetails())->setConnection('tenant')->where('enquiry_id', $id)->get();
        $tenants              = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')->paginate(5000);
        $unit_management_all  = (new UnitManagement())->setConnection('tenant')->select('id', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id')
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
        $agents                   = DB::connection('tenant')->table('agents')->get();
        $enquiry_statuses         = DB::connection('tenant')->table('enquiry_statuses')->get();
        $enquiry_request_statuses = DB::connection('tenant')->table('enquiry_request_statuses')->get();
        $employees                = DB::connection('tenant')->table('employees')->get();
        $country_master           = (new CountryMaster())->setConnection('tenant')->get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $data                     = [
            'enquiry_details'          => $enquiry_details,
            'enquiry'                  => $enquiry,
            'unit_types'               => $unit_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'enquiry_request_statuses' => $enquiry_request_statuses,
            'enquiry_statuses'         => $enquiry_statuses,
            'employees'                => $employees,
            'agents'                   => $agents,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'tenants'                  => $tenants,
            'property_types'           => $property_types,
            'enquiry_unit_details'     => $enquiry_unit_details,
            'unit_management_all'      => $unit_management_all,
        ];
        return view('admin-views.property_transactions.enquiries.edit', $data);
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $enquiry                     = (new Enquiry())->setConnection('tenant')->findOrFail($id);
        $enquiry_details             = (new EnquiryDetails())->setConnection('tenant')->where('enquiry_id', $id)->first();
        $enquiry_unit_search_details = (new EnquiryUnitSearchDetails())->setConnection('tenant')->where('enquiry_id', $id)->get();

        DB::beginTransaction();
        try {
            $enquiry_unit_search_details->each(function ($enquiry_unit_search_details_item) {
                $enquiry_unit_search_details_item->delete();
            });
            if ($request->enquiry_date && Carbon::hasFormat($request->enquiry_date, 'd/m/Y')) {
                $enquiry_date = Carbon::createFromFormat('d/m/Y', $request->enquiry_date)->format('Y-m-d');
            }
            if ($request->period_from && Carbon::hasFormat($request->period_from, 'd/m/Y')) {
                $period_from = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d');
            }
            if ($request->period_to && Carbon::hasFormat($request->period_to, 'd/m/Y')) {
                $period_to = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d');
            }
            if ($request->time_frame_for_relocation) {
                $time_frame_for_relocation = Carbon::createFromFormat('d/m/Y', $request->time_frame_for_relocation)->format('Y-m-d');
            }
            if ($request->relocation_date) {
                $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d');
            }
            $enquiry->update([
                'enquiry_no'           => $request->enquiry_no ?? $enquiry->enquiry_no,
                'enquiry_date'         => $enquiry_date ?? $enquiry->enquiry_date,
                'tenant_id'            => $request->tenant_id ?? $enquiry->tenant_id,
                'name'                 => $request->name ?? $enquiry->name,
                'gender'               => $request->gender ?? $enquiry->gender,
                'id_number'            => $request->id_number ?? $enquiry->id_number,
                'registration_no'      => $request->registration_no ?? $enquiry->registration_no,
                'nick_name'            => $request->nick_name ?? $enquiry->nick_name,
                'group_company_name'   => $request->group_company_name ?? $enquiry->group_company_name,
                'contact_person'       => $request->contact_person ?? $enquiry->contact_person,
                'designation'          => $request->designation ?? $enquiry->designation,
                'contact_no'           => $request->contact_no ?? $enquiry->contact_no,
                'whatsapp_no'          => $request->whatsapp_no ?? $enquiry->whatsapp_no,
                'company_name'         => $request->company_name ?? $enquiry->company_name,
                'fax_no'               => $request->fax_no ?? $enquiry->fax_no,
                'telephone_no'         => $request->telephone_no ?? $enquiry->telephone_no,
                'other_contact_no'     => $request->other_contact_no ?? $enquiry->other_contact_no,
                'address1'             => $request->address1 ?? $enquiry->address1,
                'address2'             => $request->address2 ?? $enquiry->address2,
                'address3'             => $request->address3 ?? $enquiry->address3,
                'state'                => $request->state ?? $enquiry->state,
                'city'                 => $enquiry->city,
                'country_id'           => $request->country_id ?? $enquiry->country_id,
                'nationality_id'       => $request->nationality_id ?? $enquiry->nationality_id,
                'passport_no'          => $request->passport_no ?? $enquiry->passport_no,
                'email1'               => $request->email1 ?? $enquiry->email1,
                'email2'               => $request->email2 ?? $enquiry->email2,
                'live_with_id'         => $request->live_with_id ?? (($enquiry->live_with_id) ? $enquiry->live_with_id : null),
                'business_activity_id' => $request->business_activity_id ?? (($enquiry->business_activity_id) ? $enquiry->business_activity_id : null),
                'booking_status'       => 'enquiry',
            ]);
            if ($enquiry) {
                $enquiry_details->update([
                    'enquiry_id'                  => $enquiry->id,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : $enquiry_details->employee_id,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : $enquiry_details->agent_id,
                    'enquiry_status_id'           => $request->enquiry_status_id ?? $enquiry_details->enquiry_status_id,
                    'enquiry_request_status_id'   => $request->enquiry_request_status_id ?? $enquiry_details->enquiry_request_status_id,
                    'decision_maker'              => $request->decision_maker ?? $enquiry_details->decision_maker,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? $enquiry_details->decision_maker_designation,
                    'current_office_location'     => $request->current_office_location ?? $enquiry_details->current_office_location,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? $enquiry_details->reason_of_relocation,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? $enquiry_details->budget_for_relocation_start,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? $enquiry_details->budget_for_relocation_end,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? $enquiry_details->no_of_emp_staff_strength,
                    // 'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? $enquiry_details->no_of_emp_staff_strength,
                    'time_frame_for_relocation'   => $time_frame_for_relocation ?? $enquiry_details->time_frame_for_relocation,
                    'relocation_date'             => $relocation_date ?? $enquiry_details->relocation_date,
                    'period_from'                 => $period_from ?? $enquiry_details->period_from,
                    'period_to'                   => $period_to ?? $enquiry_details->period_to,
                ]);
                if (isset($request->property_id)) {
                    foreach ($request->property_id as $key => $value_unit) {

                        $unit_description_id = $request->unit_description_id[$key];
                        // $rent_amount         = $request->input('rent_amount-' . $unit_description_id);
                        if ($request->period_from_unit_desc[$key]) {
                            $enquiry_from_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_from_unit_desc[$key])->format('Y-m-d');
                        }
                        if ($request->period_to_unit_desc[$key]) {
                            $enquiry_to_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_to_unit_desc[$key])->format('Y-m-d');
                        }
                        DB::connection('tenant')->table('enquiry_unit_search_details')->insert([
                            'enquiry_id'             => $enquiry->id,
                            'property_management_id' => $request->property_id[$key],
                            'unit_description_id'    => $unit_description_id,
                            'unit_type_id'           => $request->unit_type_id[$key],
                            'unit_condition_id'      => $request->unit_condition_id[$key],
                            'view_id'                => $request->view_id[$key],
                            'property_type'          => $request->property_type[$key],
                            'total_area_required'    => $request->total_area_required[$key],
                            'area_measurement'       => $request->area_measurement[$key],
                            'city'                   => $request->city_unit_desc[$key],
                            'comment'                => $request->comment[$key],
                            'rent_amount'            => $request->rent_amount[$key],
                            'date_from'              => $enquiry_from_unit_date,
                            'date_to'                => $enquiry_to_unit_date,
                            'period_to'              => $enquiry_to_unit_date,
                            'period_from'            => $enquiry_from_unit_date,
                        ]);
                    }
                }
                if (isset($request->property_id_old)) {
                    foreach ($request->property_id_old as $key_old => $value_unit_old) {

                        $unit_description_id_old = $request->unit_description_id_old[$key_old];
                        // $rent_amount_old         = $request->input('rent_amount-' . $unit_description_id_old);
                        if ($request->period_from_old[$key_old]) {
                            $enquiry_from_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_from_old[$key_old])->format('Y-m-d');
                        }
                        if ($request->period_to_old[$key_old]) {
                            $enquiry_to_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_to_old[$key_old])->format('Y-m-d');
                        }
                        DB::connection('tenant')->table('enquiry_unit_search_details')->insert([
                            'enquiry_id'             => $enquiry->id,
                            'property_management_id' => $request->property_id_old[$key_old],
                            'unit_management_id'     => isset($request->unit_management_id_old[$key_old]) ? $request->unit_management_id_old[$key_old] : null,
                            'unit_description_id'    => $unit_description_id_old,
                            'unit_type_id'           => $request->unit_type_id_old[$key_old],
                            'unit_condition_id'      => $request->unit_condition_id_old[$key_old],
                            'view_id'                => $request->view_id_old[$key_old],
                            'property_type'          => $request->property_type_old[$key_old],
                            'total_area_required'    => $request->total_area_required_old[$key_old],
                            'area_measurement'       => $request->area_measurement_old[$key_old],
                            'city'                   => $request->city_old[$key_old],
                            'comment'                => $request->comment_old[$key_old],
                            'rent_amount'            => $request->rent_amount[$key_old],
                            'period_to'              => $enquiry_to_unit_date,
                            'period_from'            => $enquiry_from_unit_date,
                        ]);
                    }
                }
            }
            DB::commit();
            return to_route('enquiry.index')->with('success', ui_change('updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function store(EnquiryRequest $request)
    {
        // dd($request->all());
        // $rules = [];
        // foreach ($request->all() as $key => $value) {
        //     if (Str::startsWith($key, 'no_of_unit-')) {
        //         $rules[$key] = 'required|numeric|min:1';
        //     }
        // }
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
        if ($request->period_from) {
            $period_from = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d');
        }
        if ($request->period_to) {
            $period_to = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d');
        }
        if ($request->time_frame_for_relocation) {
            $time_frame_for_relocation = Carbon::createFromFormat('d/m/Y', $request->time_frame_for_relocation)->format('Y-m-d');
        }
        if ($request->relocation_date) {
            $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d');
        }
        DB::beginTransaction();
        try {
            $enquiry = DB::connection('tenant')->table('enquiries')->insertGetId([
                'enquiry_no'           => $request->enquiry_no,
                'enquiry_date'         => $enquiry_date,
                'tenant_id'            => $request->tenant_id,
                'name'                 => $request->name ?? null,
                'gender'               => $request->gender ?? null,
                'id_number'            => $request->id_number ?? null,
                'registration_no'      => $request->registration_no ?? null,
                'nick_name'            => $request->nick_name ?? null,
                'group_company_name'   => $request->group_company_name ?? null,
                'contact_person'       => $request->contact_person ?? null,
                'designation'          => $request->designation ?? null,
                'contact_no'           => $request->contact_no ?? null,
                'whatsapp_no'          => $request->whatsapp_no ?? null,
                'company_name'         => $request->company_name ?? null,
                'fax_no'               => $request->fax_no ?? null,
                'telephone_no'         => $request->telephone_no ?? null,
                'other_contact_no'     => $request->other_contact_no ?? null,
                'address1'             => $request->address1 ?? null,
                'address2'             => $request->address2 ?? null,
                'address3'             => $request->address3 ?? null,
                'state'                => $request->state ?? null,
                'city'                 => $request->city ?? null,
                'country_id'           => $request->country_id ?? null,
                'nationality_id'       => $request->nationality_id ?? null,
                'passport_no'          => $request->passport_no ?? null,
                'email1'               => $request->email1 ?? null,
                'email2'               => $request->email2 ?? null,
                'live_with_id'         => $request->live_with_id ?? null,
                'business_activity_id' => $request->business_activity_id ?? null,
                'booking_status'       => 'enquiry',
                'created_at'           => Carbon::now(),
            ]);
            if ($enquiry) {
                $enquiry_details = DB::connection('tenant')->table('enquiry_details')->insert([
                    'enquiry_id'                  => $enquiry,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
                    'enquiry_status_id'           => $request->enquiry_status_id ?? null,
                    'enquiry_request_status_id'   => $request->enquiry_request_status_id ?? null,
                    'decision_maker'              => $request->decision_maker ?? null,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? null,
                    'current_office_location'     => $request->current_office_location ?? null,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? null,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? null,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? null,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? null,
                    'time_frame_for_relocation'   => $time_frame_for_relocation ?? null,
                    'relocation_date'             => $relocation_date ?? null,
                    'period_from'                 => $period_from ?? null,
                    'period_to'                   => $period_to ?? null,
                    'created_at'                  => Carbon::now(),
                ]);
                if ($request->view_id) {
                    foreach ($request->view_id as $key => $value_unit) {

                        $unit_description_id = (isset($request->unit_description_id[$key]) ? $request->unit_description_id[$key] : null);
                        $rent_amount         = $request->input('rent_amount-' . $unit_description_id);
                        if ($request->period_from_unit_desc[$key]) {
                            $enquiry_from_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_from_unit_desc[$key])->format('Y-m-d');
                        }
                        if ($request->period_to_unit_desc[$key]) {
                            $enquiry_to_unit_date = Carbon::createFromFormat('d/m/Y', $request->period_to_unit_desc[$key])->format('Y-m-d');
                        }
                        EnquiryUnitSearchDetails::create([
                            'enquiry_id'             => $enquiry,
                            'property_management_id' => $request->property_id[$key],
                            'unit_management_id'     => (isset($request->unit_management_id[$key])) ? $request->unit_management_id[$key] : null,
                            'unit_description_id'    => $unit_description_id ?? null,
                            'unit_type_id'           => $request->unit_type_id[$key] ?? null,
                            'unit_condition_id'      => $request->unit_condition_id[$key] ?? null,
                            'view_id'                => (isset($request->view_id[$key]) && ($request->view_id[$key] != -1)) ? $request->view_id[$key] : null,
                            'property_type'          => $request->property_type[$key] ?? null,
                            'total_area_required'    => $request->total_area_required[$key] ?? null,
                            'area_measurement'       => $request->area_measurement[$key] ?? null,
                            'city'                   => $request->city_unit_desc[$key] ?? null,
                            'comment'                => $request->comment[$key] ?? null,
                            'rent_amount'            => (isset($request->rent_amount[$key])) ? $request->rent_amount[$key] : $rent_amount,
                            'date_from'              => $enquiry_from_unit_date ?? null,
                            'date_to'                => $enquiry_to_unit_date ?? null,
                            'period_to'              => $enquiry_to_unit_date ?? null,
                            'period_from'            => $enquiry_from_unit_date ?? null,
                        ]);
                        if (isset($request->unit_management_id[$key])) {
                            // Log::info($request->unit_management_id[$key]);
                            $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $request->unit_management_id[$key])->first();
                            $unit_management->update(['booking_status' => 'enquiry', 'tenant_id' => $request->tenant_id]);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
        return to_route('enquiry.index')->with('success', __('property_master.added_successfully'));
    }

    public function add_to_proposal($id)
    {
        $enquiry                  = (new Enquiry())->setConnection('tenant')->with('enquiry_details', 'enquiry_unit_search', 'tenant')->findOrFail($id);
        $tenants                  = DB::connection('tenant')->table('tenants')->get();
        $agents                   = DB::connection('tenant')->table('agents')->get();
        $enquiry_statuses         = DB::connection('tenant')->table('enquiry_statuses')->get();
        $enquiry_request_statuses = DB::connection('tenant')->table('enquiry_request_statuses')->get();
        $employees                = DB::connection('tenant')->table('employees')->get();
        $country_master           = (new CountryMaster())->setConnection('tenant')->get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $services_master          = (new ServiceMaster())->setConnection('tenant')->select('id', 'name', 'vat')->get();

        $data = [
            'unit_types'               => $unit_types,
            'services_master'          => $services_master,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'enquiry_request_statuses' => $enquiry_request_statuses,
            'enquiry_statuses'         => $enquiry_statuses,
            'employees'                => $employees,
            'agents'                   => $agents,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'tenants'                  => $tenants,
            'enquiry'                  => $enquiry,

        ];
        // $data = [
        //     'enquiry'           => $enquiry,
        // ];
        return view('admin-views.property_transactions.enquiries.add_to_proposal', $data);
    }

    public function store_to_proposal(Request $request)
    {
        $rules = [];

        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'property_id-')) {
                $rules[$key] = 'required';
            }
            if (Str::startsWith($key, 'unit-')) {
                $rules[$key] = 'required';
            }
            // if (Str::startsWith($key, 'payment_mode-')) {
            //     $rules[$key] = 'required';
            // }
        }

        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        try {

            $proposalDate = $request->proposal_date;
            if (Carbon::hasFormat($proposalDate, 'd/m/Y')) {
                $formattedDate = Carbon::createFromFormat('d/m/Y', $proposalDate)->format('Y-m-d');
            } else {
                $formattedDate = Carbon::now()->format('Y-m-d');
            }
            $enquiry             = (new Enquiry())->setConnection('tenant')->with('enquiry_details', 'enquiry_unit_search')->withCount('enquiry_unit_search')->findOrFail($request->enquiry_id);
            $enquiry_unit_search = $enquiry->enquiry_unit_search;
            $unit_count          = $enquiry->enquiry_unit_search->count();
            $proposal            = (new Proposal())->setConnection('tenant')->create([
                'proposal_no'                => $request->proposal_no,
                'proposal_date'              => $formattedDate,
                'tenant_id'                  => $request->tenant_id,
                'total_no_of_required_units' => $request->total_no_of_required_units ?? $unit_count,
                'name'                       => $request->name ?? $enquiry->name,
                'gender'                     => $request->gender ?? $enquiry->gender,
                'id_number'                  => $request->id_number ?? $enquiry->id_number,
                'registration_no'            => $request->registration_no ?? $enquiry->registration_no,
                'nick_name'                  => $request->nick_name ?? $enquiry->nick_name,
                'group_company_name'         => $request->group_company_name ?? $enquiry->group_company_name,
                'contact_person'             => $request->contact_person ?? $enquiry->contact_person,
                'designation'                => $request->designation ?? $enquiry->designation,
                'contact_no'                 => $request->contact_no ?? $enquiry->contact_no,
                'whatsapp_no'                => $request->whatsapp_no ?? $enquiry->whatsapp_no,
                'company_name'               => $request->company_name ?? $enquiry->company_name,
                'fax_no'                     => $request->fax_no ?? $enquiry->fax_no,
                'telephone_no'               => $request->telephone_no ?? $enquiry->telephone_no,
                'other_contact_no'           => $request->other_contact_no ?? $enquiry->other_contact_no,
                'address1'                   => $request->address1 ?? $enquiry->address1,
                'address2'                   => $request->address2 ?? $enquiry->address2,
                'address3'                   => $request->address3 ?? $enquiry->address3,
                'state'                      => $request->state ?? $enquiry->state,
                'city'                       => $request->city ?? $enquiry->city,
                'country_id'                 => $request->country_id ?? $enquiry->country_id,
                'nationality_id'             => $request->nationality_id ?? $enquiry->nationality_id,
                'passport_no'                => $request->passport_no ?? $enquiry->passport_no,
                'email1'                     => $request->email1 ?? $enquiry->email1,
                'email2'                     => $request->email2 ?? $enquiry->email2,
                'live_with_id'               => $request->live_with_id ?? null,
                'business_activity_id'       => $request->business_activity_id ?? null,
                'status'                     => 'pending',
                'booking_status'             => 'proposal',
            ]);
            $enquiry_details_for_update = (new EnquiryDetails())->setConnection('tenant')->where('enquiry_id', $request->enquiry_id)->first();
            $enquiry_details_for_update->update([
                'enquiry_request_status_id' => 2,
            ]);
            $enquiry->update([
                'booking_status' => 'proposal',
            ]);
            // if ($proposal) {
            ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
            ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
            ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
            $proposal_details                                      = (new ProposalDetails())->setConnection('tenant')->create([
                'proposal_id'                 => $proposal->id ?? null,
                'employee_id'                 => $request->employee_id ?? null,
                'agent_id'                    => $request->agent_id ?? null,
                'proposal_status_id'          => $request->enquiry_status_id ?? null,
                'proposal_request_status_id'  => $request->enquiry_request_status_id ?? null,
                'decision_maker'              => $request->decision_maker ?? null,
                'decision_maker_designation'  => $request->decision_maker_designation ?? null,
                'current_office_location'     => $request->current_office_location ?? null,
                'reason_of_relocation'        => $request->reason_of_relocation ?? null,
                'budget_for_relocation_start' => $request->budget_for_relocation_start ?? null,
                'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? null,
                'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? null,
                'time_frame_for_relocation'   => $request->time_frame_for_relocation ?? null,
                'relocation_date'             => $relocation_date,
                'period_from'                 => $period_from ?? null,
                'period_to'                   => $period_to ?? null,
            ]);

            foreach ($enquiry_unit_search as $key => $enquiry_unit_search_item) {

                $propertyId        = $request->input("property_id-$enquiry_unit_search_item->id");
                $unitDescriptionId = $request->input("unit_description_id-$enquiry_unit_search_item->id");
                $unitTypeId        = $request->input("unit_type_id-$enquiry_unit_search_item->id");
                $unitConditionId   = $request->input("unit_condition_id-$enquiry_unit_search_item->id");
                $viewId            = $request->input("view_id-$enquiry_unit_search_item->id");
                $propertyType      = $request->input("property_type-$enquiry_unit_search_item->id");
                $periodFrom        = Carbon::createFromFormat('d/m/Y', $request->input("period_from-$enquiry_unit_search_item->id"))->format('Y-m-d');
                $periodTo          = Carbon::createFromFormat('d/m/Y', $request->input("period_to-$enquiry_unit_search_item->id"))->format('Y-m-d');
                $city              = $request->input("city-$enquiry_unit_search_item->id");
                $totalArea         = $request->input("total_area-$enquiry_unit_search_item->id");
                $areaMeasurement   = $request->input("area_measurement-$enquiry_unit_search_item->id");
                $notes             = $request->input("notes-$enquiry_unit_search_item->id");
                $unit              = $request->input("unit-$enquiry_unit_search_item->id");
                $pdc               = $request->input("pdc-$enquiry_unit_search_item->id");
                $totalAreaAmount   = $request->input("total_area_amount-$enquiry_unit_search_item->id");
                $amount            = $request->input("amount-$enquiry_unit_search_item->id");
                $paymentMode       = $request->input("payment_mode-$enquiry_unit_search_item->id");
                $rentMode          = $request->input("rent_mode-$enquiry_unit_search_item->id");
                $rentAmount        = $request->input("rent_amount-$enquiry_unit_search_item->id");
                $baseAmount  = (float) $request->input("rent_amount-$enquiry_unit_search_item->id");


                $rentalGl          = $request->input("rental_gl-$enquiry_unit_search_item->id");
                // $lease_break_date         = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$enquiry_unit_search_item->id"))->format('Y-m-d');
                if ($request->input("lease_break_date-$enquiry_unit_search_item->id")) {
                    $lease_break_date_format = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$enquiry_unit_search_item->id"))->format('Y-m-d');
                }
                $lease_break_date         = $lease_break_date_format ?? null;
                $lease_break_comment      = $request->input("lease_break_comment-$enquiry_unit_search_item->id");
                $total_net_rent_amount    = $request->input("total_net_rent_amount-$enquiry_unit_search_item->id") ?? 0;
                $vat_percentage           = $request->input("vat_percentage-$enquiry_unit_search_item->id");
                $vat_amount               = $request->input("vat_amount-$enquiry_unit_search_item->id");
                $security_deposit         = $request->input("security_deposit_months_rent-$enquiry_unit_search_item->id");
                $security_deposit_amount  = $request->input("security_deposit_amount-$enquiry_unit_search_item->id");
                $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$enquiry_unit_search_item->id");
                $ewa_limit_mode           = $request->input("ewa_limit_mode-$enquiry_unit_search_item->id");
                $ewa_limit                = $request->input("ewa_limit_monthly-$enquiry_unit_search_item->id");
                $notice_period            = $request->input("notice_period-$enquiry_unit_search_item->id");
                
                if ($rentMode === $paymentMode) {

                    $rentAmount = $baseAmount;
                } else {
                    $rentAmount = calc_rent_amount($rentMode, $paymentMode, $baseAmount, $rentAmount);
                    $total_net_rent_amount = ($rentAmount * ($vat_percentage / 100 )) + $rentAmount;
                    $security_deposit_amount = $rentAmount * $security_deposit;
                }
                $proposal_units           = (new ProposalUnits())->setConnection('tenant')->create([
                    'proposal_id'              => $proposal->id,
                    'property_id'              => $propertyId,
                    'commencement_date'        => $periodFrom,
                    'expiry_date'              => $periodTo,
                    'unit_id'                  => $unit,
                    'payment_mode'             => $paymentMode,
                    'total_area_amount'        => $totalAreaAmount,
                    'total_net_amount'         => $amount,
                    'rent_amount'              => $rentAmount,
                    'rent_mode'                => $rentMode,
                    'rental_gl'                => $rentalGl,
                    'vat_amount'               => $vat_amount,
                    'vat_percentage'           => $vat_percentage,
                    'total_net_rent_amount'    => $total_net_rent_amount,
                    'lease_break_comment'      => $lease_break_comment,
                    'lease_break_date'         => $lease_break_date,
                    'security_deposit'         => $security_deposit,
                    'security_deposit_amount'  => $security_deposit_amount,
                    'is_rent_inclusive_of_ewa' => $is_rent_inclusive_of_ewa,
                    'ewa_limit_mode'           => $ewa_limit_mode,
                    'ewa_limit'                => $ewa_limit,
                    'notice_period'            => $notice_period,
                    'total'                    => 0,
                ]);
                $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                $unit_management->update(['booking_status' => 'proposal']);
                if (isset($request->service_counter[$enquiry_unit_search_item->id])) {
                    for ($ind = 1, $inde = $request->service_counter[$enquiry_unit_search_item->id]; $ind <= $inde; $ind++) {

                        $chargeMode       = $request->input("charge_mode-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $chargeModeType   = $request->input("charge_mode_type-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $amountCharge     = $request->input("amount_charge-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $percentageCharge = $request->input("percentage_amount_charge-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $calculateAmount  = $request->input("calculate_amount-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $startDate        = $request->input("amount_charge-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $expiryDate       = $request->input("expiry_date-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $vatPercentage    = $request->input("vat_percentage-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $vatAmount        = $request->input("vat_amount-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        $totalAmount      = $request->input("total_amount-{$enquiry_unit_search_item->id}-{$ind}")[0] ?? null;
                        DB::connection('tenant')->table('proposal_units_services')->insert([
                            'proposal_unit_id'  => $proposal_units->id,
                            'charge_mode'       => $chargeModeType,
                            'other_charge_type' => $chargeMode,
                            'amount'            => $calculateAmount,
                            'vat'               => $vatAmount,
                            'total'             => $totalAmount,
                        ]);
                    }
                }
            }

            // }
            $enquiry->update([
                'booking_status' => 'proposal',
            ]);
            DB::commit();
            return to_route('proposal.index')->with('success', __('country.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function get_service_master($id)
    {
        $service = (new ServiceMaster())->setConnection('tenant')->find($id);
        return json_encode($service);
    }
    public function get_tenant($id)
    {
        $tenant = (new Tenant())->setConnection('tenant')->find($id);
        return json_encode($tenant);
    }
    public function delete(Request $request)
    {
        $enquiry = (new Enquiry())->setConnection('tenant')->findOrFail($request->id);
        (new EnquiryDetails())->setConnection('tenant')->where('enquiry_id', $request->id)->delete();
        $enquiry_units = (new EnquiryUnitSearchDetails())->setConnection('tenant')->select('id', 'unit_management_id')->where('enquiry_id', $request->id)->get();

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
        return redirect()->route('enquiry.index')->with("success", __('property_master.deleted_successfully'));
    }
    public function general_check_property(Request $request)
    {

        $units = UnitManagement::select(
            'id',
            'unit_id',
            'property_management_id',
            'block_management_id',
            'floor_management_id',
            'unit_description_id',
            'unit_condition_id',
            'unit_type_id',
            'unit_parking_id',
            'view_id',
            'status',
            'booking_status'
        )
            ->with([
                'property_unit_management:id,name',
                'block_unit_management:id,block_id',
                'block_unit_management.block:id,name',
                'floor_unit_management:id,floor_id',
                'floor_unit_management.floor_management_main:id,name',
                'unit_management_main:id,name',
                'unit_description:id,name',
                'unit_type:id,name',
                'unit_condition:id,name',
                'view:id,name',
                'latest_rent_schedule',
                'rent_schedules',
            ])
            ->paginate();
        $floors            = FloorManagement::select('id', 'floor_id')->with('floor_management_main')->get();
        $blocks            = BlockManagement::select('id', 'block_id')->with('block')->get();
        $buildings         = PropertyManagement::select('id', 'name')->forUser()->get();
        $unit_descriptions = UnitDescription::select('id', 'name')->get();
        $unit_conditions   = UnitCondition::select('id', 'name')->get();
        $unit_types        = UnitType::select('id', 'name')->get();
        $unit_views        = View::select('id', 'name')->get();
        $tenants           = Tenant::select('id', 'name', 'company_name')->orderBy('created_at', 'desc')->get();
        if ($request->bulk_action_btn === 'filter') {
            $report_query = UnitManagement::select(
                'id',
                'unit_id',
                'property_management_id',
                'block_management_id',
                'floor_management_id',
                'unit_description_id',
                'unit_condition_id',
                'unit_type_id',
                'unit_parking_id',
                'view_id',
                'status',
                'booking_status'
            )->with([
                'property_unit_management:id,name',
                'block_unit_management:id,block_id',
                'block_unit_management.block:id,name',
                'floor_unit_management:id,floor_id',
                'floor_unit_management.floor_management_main:id,name',
                'unit_management_main:id,name',
                'unit_description:id,name',
                'unit_type:id,name',
                'unit_condition:id,name',
                'view:id,name',
                'latest_rent_schedule',
                'rent_schedules',
            ]);

            if ($request->report_status && ! in_array('-1', $request->report_status)) {
                $report_query->whereIn('booking_status', $request->report_status);
            }
            if ($request->has('report_tenant') && $request->report_tenant && (! empty($request->report_status) && ! in_array('empty', $request->report_status)) &&  $request->report_tenant != -1) {
                $report_query->where('tenant_id', $request->report_tenant);
            }
            if ($request->report_building && $request->report_building != -1) {
                $report_query->whereHas('property_unit_management', function ($query) use ($request) {
                    $query->where('id', $request->report_building);
                });
            }
            if ($request->report_floor && $request->report_floor != -1) {
                $report_query->whereHas('floor_unit_management', function ($query) use ($request) {
                    $query->where('id', $request->report_floor);
                });
            }
            if ($request->report_block && $request->report_block != -1) {
                $report_query->whereHas('block_unit_management', function ($query) use ($request) {
                    $query->where('id', $request->report_block);
                });
            }
            if ($request->report_unit_description && $request->report_unit_description != -1) {
                $report_query->whereHas('unit_description', function ($query) use ($request) {
                    $query->where('id', $request->report_unit_description);
                });
            }

            if ($request->report_unit_condition && $request->report_unit_condition != -1) {
                $report_query->whereHas('unit_condition', function ($query) use ($request) {
                    $query->where('id', $request->report_unit_condition);
                });
            }

            if ($request->report_unit_types && $request->report_unit_types != -1) {
                $report_query->whereHas('unit_type', function ($query) use ($request) {
                    $query->where('id', $request->report_unit_types);
                });
            }

            if ($request->report_unit_view && $request->report_unit_view != -1) {
                $report_query->whereHas('view', function ($query) use ($request) {
                    $query->where('id', $request->report_unit_view);
                });
            }

            $units = $report_query->orderBy('created_at', 'desc')->paginate(20);
        }

        $data = [
            'floors'            => $floors,
            'blocks'            => $blocks,
            'buildings'         => $buildings,
            'units'             => $units,
            'unit_descriptions' => $unit_descriptions,
            'unit_conditions'   => $unit_conditions,
            'unit_types'        => $unit_types,
            'unit_views'        => $unit_views,
            'tenants'           => $tenants,
        ];
        return view('admin-views.property_transactions.enquiries.general_check_property', $data);
    }
    public function check_property($id)
    {
        $enquiry  = (new Enquiry())->setConnection('tenant')->findOrFail($id);
        $unit_ids = (new EnquiryUnitSearchDetails())->setConnection('tenant')->where('enquiry_id', $id)
            ->pluck('unit_description_id')
            ->toArray();
        $units = (new UnitManagement())->setConnection('tenant')->whereIn('unit_description_id', $unit_ids)->get();
        $property_ids = $units->pluck('property_management_id')->toArray();
        $property     = (new PropertyManagement())->setConnection('tenant')->whereIn('id', $property_ids)->get();
        if ($property->isEmpty()) {
            $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        }
        $data = [
            'properties' => $property,
        ];
        return view('admin-views.property_transactions.enquiries.check_property', $data);
    }
    public function view_image($id, $enquiry_id)
    {
        // $enquiry_unit = EnquiryUnitSearchDetails::where('enquiry_id' , $enquiry_id)->whereNotNull('unit_management_id')->get( );//->toArray();
        $enquiry_unit = EnquiryUnitSearchDetails::where('enquiry_id', $enquiry_id)->whereNotNull('unit_management_id')->pluck('id', 'unit_management_id')->toArray();
        $property     = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        // $property = PropertyManagement::findOrFail($id);
        // dd($enquiry_unit);
        $data = [
            'property_item' => $property,
            'enquiry_units' => $enquiry_unit,
        ];
        return view('admin-views.property_transactions.enquiries.view_image', $data);
    }
    public function list_view($id, $enquiry_id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        $enquiry_unit = EnquiryUnitSearchDetails::where('enquiry_id', $enquiry_id)->whereNotNull('unit_management_id')->pluck('id', 'unit_management_id')->toArray();

        $data = [
            'property_item' => $property,
            'enquiry_units' => $enquiry_unit,
        ];
        return view('admin-views.property_transactions.enquiries.list_view', $data);
    }
    public function empty_unit_from_enquiry_unit_search($id)
    {
        $enquiry_unit = (new EnquiryUnitSearchDetails())->setConnection('tenant')->select('id', 'unit_management_id')->where('id', $id)->first();
        if ($enquiry_unit->unit_management_id) {
            $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $enquiry_unit->unit_management_id)->first();
            $unit_management->update([
                'booking_status' => 'empty',
            ]);
        }
        $enquiry_unit->delete();
        return redirect()->back()->with('success', __('general.deleted_successfully'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'unit_description_id' => 'required|array',
            'property_id'         => 'nullable|array',
            'unit_type_id'        => 'nullable|array',
            'unit_condition_id'   => 'nullable|array',
            'view_id'             => 'nullable|array',
        ]);

        $count = count($request->unit_description_id);

        $query = UnitManagement::select(
            'id',
            'unit_description_id',
            'property_management_id',
            'status',
            'booking_status',
            'unit_type_id',
            'unit_condition_id',
            'view_id',
            'unit_id',
            'block_management_id',
            'property_management_id',
            'floor_management_id'
        ) //->emptyUnit()
            ->with(
                'property_unit_management:id,name',
                'block_unit_management:id,block_id',
                'floor_unit_management:id,floor_id',
                'block_unit_management.block:id,name',
                'floor_unit_management.floor_management_main:id,name'
            );
        $query->where(function ($q) use ($request, $count) {
            for ($i = 0; $i < $count; $i++) {
                $q->orWhere(function ($subQuery) use ($request, $i) {
                    $subQuery->where('unit_description_id', $request->unit_description_id[$i]);

                    if (! empty($request->property_id[$i]) && $request->property_id[$i] != "-1") {
                        $subQuery->where('property_management_id', $request->property_id[$i]);
                    }
                    if (! empty($request->unit_type_id[$i]) && $request->unit_type_id[$i] != "-1") {
                        $subQuery->where('unit_type_id', $request->unit_type_id[$i]);
                    }

                    if (! empty($request->unit_condition_id[$i])) {
                        $subQuery->where('unit_condition_id', $request->unit_condition_id[$i]);
                    }

                    if (! empty($request->view_id[$i])) {
                        $subQuery->where('view_id', $request->view_id[$i]);
                    }
                });
            }
        });

        $all_units_filter  = $query->paginate(20);
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->get();
        $unit_conditions   = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->get();
        $unit_types        = (new UnitType())->setConnection('tenant')->select('id', 'name')->get();
        $unit_views        = (new View())->setConnection('tenant')->select('id', 'name')->get();
        $data              = [
            'units'             => $all_units_filter,
            'unit_descriptions' => $unit_descriptions,
            'unit_conditions'   => $unit_conditions,
            'unit_types'        => $unit_types,
            'unit_views'        => $unit_views,
        ];
        return view('admin-views.property_transactions.enquiries.general_check_property', $data);
    }

    protected function expire_unit($expiry_days)
    {
        DB::transaction(function () use ($expiry_days) {
            // Get expired enquiry details IDs first
            $expiredEnquiries = (new EnquiryDetails())->setConnection('tenant')->where('enquiry_request_status_id', 1)
                ->whereRaw("DATEDIFF(NOW(), created_at) > ?", [$expiry_days])
                ->pluck('enquiry_id');

            if ($expiredEnquiries->isNotEmpty()) {
                // Update enquiry details
                (new EnquiryDetails())->setConnection('tenant')->whereIn('enquiry_id', $expiredEnquiries)
                    ->where('enquiry_request_status_id', 1)
                    ->update(['enquiry_request_status_id' => 3]);

                // Get unit management IDs in one query
                $unitManagementIds = (new EnquiryUnitSearchDetails())->setConnection('tenant')->whereIn('enquiry_id', $expiredEnquiries)
                    ->pluck('unit_management_id');

                // Update unit management in bulk
                if ($unitManagementIds->isNotEmpty()) {
                    (new UnitManagement())->setConnection('tenant')->whereIn('id', $unitManagementIds)
                        ->update(['booking_status' => 'empty']);
                }
            }
        });
    }

    public function get_unit_type_by_unit_descrption_id($id)
    {
        $unit_types = (new UnitType())->setConnection('tenant')->where('unit_description_id', $id)->select('id', 'name')->get();

        if ($unit_types) {
            return response()->json([
                'status'     => 200,
                "unit_types" => $unit_types,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Unit Types Not Found",
            ]);
        }
    }

    public function search_master(Request $request)
    {
        if ($request->building) {
            $blocks = BlockManagement::with('block:id,name')->select('id', 'block_id', 'property_management_id')->where('property_management_id', $request->building)->get();
            $floors = FloorManagement::with('floor_management_main:id,name')->select('id', 'floor_id', 'property_management_id')->where('property_management_id', $request->building)->get();
        }
        return response()->json([
            'status'  => 200,
            'success' => true,
            'blocks'  => $blocks,
        ]);
    }

    public function empty_unit_from_enquiry_unit($id)
    {
        $enquiry_unit = (new EnquiryUnitSearchDetails())->setConnection('tenant')->select('id', 'unit_management_id')->where('id', $id)->first();
        if ($enquiry_unit->unit_management_id) {
            $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $enquiry_unit->unit_management_id)->first();
            $unit_management->update([
                'booking_status' => 'empty',
            ]);
        }

        $enquiry_unit->delete();
        return redirect()->back()->with('success', __('general.deleted_successfully'));

        // return response()->json([
        //     'status'  => 200,
        //     'success' => true,
        // ]);
    }

    public function general_view_image()
    {
        $property = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->forUser()->get();
       $settings = BusinessSetting::whereIn('type', ['enquiry_color','booking_color','proposal_color','agreement_color'])->select('type','value')->get()->keyBy('type');
 
        $data = [
            'property_items' => $property,
            'settings'=>$settings,
        ];
        return view('admin-views.property_transactions.enquiries.general_view_image', $data);
    }
    public function general_list_view()
    {
        $property = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->forUser()->get();

       $settings = BusinessSetting::whereIn('type', ['enquiry_color','booking_color','proposal_color','agreement_color'])->select('type','value')->get()->keyBy('type');
 
        $data = [
            'property_items' => $property,
            'settings'=>$settings,
        ];
        return view('admin-views.property_transactions.enquiries.general_list_view', $data);
    }
   
}
// App\Models\Admin::create([
//     'name'                  => 'Eslam',
//     'user_name'             => 'admin',
//     'password'              => Hash::make('12345'),
//     'role_id'               => 2,
//     'role_name'             => 'admin',
// ]);
