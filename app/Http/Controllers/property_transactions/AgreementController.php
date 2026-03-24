<?php

namespace App\Http\Controllers\property_transactions;

use App\Http\Controllers\Controller;
use App\Models\AccruedIncome;
use App\Models\Agent;
use App\Models\Agreement;
use App\Models\AgreementDetails;
use App\Models\AgreementUnits;
use App\Models\AgreementUnitsService;
use App\Models\BusinessActivity;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\Employee;
use App\Models\EnquiryRequestStatus;
use App\Models\EnquiryStatus;
use App\Models\hierarchy\MainLedger;
use App\Models\LiveWith;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\Schedule;
use App\Models\ServiceMaster;
use App\Models\Tenant;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\UnitType;
use App\Models\User;
use App\Models\View;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AgreementController extends Controller
{
    public function __construct()
    {
        $this->middleware('switch.database');
    }
    public function index(Request $request)
    {
        // $this->authorize('agreement'); 
        $ids     = $request->bulk_ids;
        $company = (new Company())
            ->setConnection('tenant')
            ->select('id', 'financial_year', 'book_begining')
            ->first();
        $companyStartMonth = null;
        // $lastRun = Cache::get('last_agreement_expiry_run');
        // if (! $lastRun || now()->diffInHours($lastRun) >= 24) {
        $agreement_settings = get_business_settings('agreement')->where('type', 'agreement_expire_date')->first();
        $expiry_days        = $agreement_settings ? (int) $agreement_settings->value : 0;
        if ($expiry_days > 0) {
            expire_unit($expiry_days, 'Agreement', 'AgreementUnits');
            // Cache::put('last_agreement_expiry_run', now(), now()->addDay());
        }
        // }
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids) && isset($request->status)) {
            $data = ['status' => $request->status];

            $this->agreement_update_status($data, $ids);
            return back()->with('success', ui_change('updated_successfully'));
        }
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            (new Agreement())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', ui_change('updated_successfully'));
        }
        if ($request->bulk_action_btn === 'sign' && is_array($ids) && count($ids)) {
            foreach ($ids as $id) {
                $agreement = (new Agreement())->setConnection('tenant')->findOrFail($id);
                if ($agreement->booking_status == 'signed') {
                    continue;
                } else {

                    $this->signed($id);
                }
            }
            return back()->with('success', ui_change('signed_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $agreements  = (new Agreement())->setConnection('tenant')
            // ->where('agreement_date' , '>=' ,$company->book_begining)
            ->when($request['search'], function ($q) use ($request) {
                $key = explode(' ', $request['search']);
                foreach ($key as $value) {
                    $q->Where('agreement_no', 'like', "%{$value}%")

                        ->orWhereHas('tenant', function ($query) use ($value) {
                            $query->where('name', 'like', "%{$value}%")->orWhere('company_name', 'like', "%{$value}%");
                        });
                }
            })->whereHas('agreement_details', function ($query) use ($company) {
                if ($company->book_begining) {

                    $query->where('period_to', '>=',  $company->book_begining);
                }
            })
            //->where('status', 'pending')
            ->with(
                'agreement_units:id,agreement_id,property_id,unit_id,commencement_date,expiry_date',
                'agreement_units.agreement_unit_main:id,property_management_id,block_management_id,floor_management_id,unit_id',
                'agreement_units.agreement_unit_main.property_unit_management:id,name',
                'agreement_units.agreement_unit_main.block_unit_management:id,block_id',
                'agreement_units.agreement_unit_main.floor_unit_management:id,floor_id',
                'agreement_units.agreement_unit_main.floor_unit_management.floor_management_main:id,name',
                'agreement_units.agreement_unit_main.block_unit_management.block:id,name'
            )
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = (new Agreement())->setConnection('tenant')
                // ->where('agreement_date' , '>=' ,$company->book_begining)
                ->query();
            if ($request->booking_status && $request->booking_status != -1 && ($request->booking_status == 'signed')) {
                $report_query->where('booking_status', $request->booking_status);
            }
            if ($request->status && $request->status != -1) {
                $report_query->where('status', $request->status);
            }
            if ($request->booking_status && $request->booking_status != -1 && ($request->booking_status == 'unsigned')) {
                $report_query->where('booking_status', '!=', 'signed');
            }
            if ($request->sign_status && $request->sign_status != -1 && ($request->sign_status == 'unsigned')) {
                $report_query->where('booking_status', '!=', 'signed');
            }
            // if ($request->tenant_id && $request->tenant_id != -1) {
            //     $report_query->whereRelation('tenant', 'id', $request->tenant_id);
            // }

            if ($request->tenant_id && $request->tenant_id != -1) {
                $report_query->where('tenant_id', $request->tenant_id);
            }
            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
                $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
                $report_query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $agreements = $report_query->orderBy('created_at', 'desc')
                ->with(
                    'agreement_units:id,agreement_id,property_id,unit_id,commencement_date,expiry_date',
                    'agreement_units.agreement_unit_main:id,property_management_id,block_management_id,floor_management_id,unit_id',
                    'agreement_units.agreement_unit_main.property_unit_management:id,name',
                    'agreement_units.agreement_unit_main.block_unit_management:id,block_id',
                    'agreement_units.agreement_unit_main.floor_unit_management:id,floor_id',
                    'agreement_units.agreement_unit_main.floor_unit_management.floor_management_main:id,name',
                    'agreement_units.agreement_unit_main.block_unit_management.block:id,name'
                )->paginate();
        }
        $tenants = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name')->get();
        $data    = [
            'agreements' => $agreements,
            'search'     => $search,
            'tenants'    => $tenants,

        ];
        return view("admin-views.property_transactions.agreements.agreement_list", $data);
    }

    public function create()
    {
        $tenants                  = DB::connection('tenant')->table('tenants')->get();
        $agents                   = DB::connection('tenant')->table('agents')->get();
        $enquiry_statuses         = DB::connection('tenant')->table('enquiry_statuses')->get();
        $enquiry_request_statuses = DB::connection('tenant')->table('enquiry_request_statuses')->get();
        $employees                = DB::connection('tenant')->table('employees')->get();
        $country_master           = (new CountryMaster())->setConnection('tenant')->get();
        $services_master          = (new ServiceMaster())->setConnection('tenant')->get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $services_master          = (new ServiceMaster())->setConnection('tenant')->select('id', 'name')->get();

        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'dail_code_main'            => $dail_code_main,
            'services_master'          => $services_master,
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
        return view('admin-views.property_transactions.agreements.create', $data);
    }

    public function get_units(Request $request)
    {
        $property_id         = $request->input('property_id');
        $unit_description_id = $request->input('unit_description_id');
        $unit_type_id        = $request->input('unit_type_id');
        $unit_condition_id   = $request->input('unit_condition_id');
        $view_id             = $request->input('view_id');
        $property_type       = $request->input('property_type');

        $units = (new UnitManagement())->setConnection('tenant')->with('unit_management_main:id,name')
            // $units = (new UnitManagement())->setConnection('tenant')->where('booking_status', 'empty')->with('unit_management_main:id,name')
            ->when($property_id, function ($query, $property_id) {
                return $query->where('property_management_id', $property_id);
            })
            ->when($unit_description_id, function ($query, $unit_description_id) {
                return $query->where('unit_description_id', $unit_description_id);
            })
            ->when($unit_type_id, function ($query, $unit_type_id) {
                return $query->where('unit_type_id', $unit_type_id);
            })
            ->when($unit_condition_id, function ($query, $unit_condition_id) {
                return $query->where('unit_condition_id', $unit_condition_id);
            })
            ->when($view_id, function ($query, $view_id) {
                return $query->where('view_id', $view_id);
            })->with(
                'property_unit_management',
                'block_unit_management',
                'block_unit_management.block',
                'floor_unit_management.floor_management_main',
                'floor_unit_management',
                'unit_management_main'
            )

            ->get();
        return response()->json($units);
    }

    public function store(Request $request)
    {

        $rules = [
            'tenant_id'                  => 'required',
            'total_no_of_required_units' => 'required',
        ];
        $unitValues = [];
        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'property_id-')) {
                $rules[$key] = 'required';
            }

            if (Str::startsWith($key, 'unit-')) {
                $rules[$key]  = 'required';
                $unitValues[] = $value;
            }
            if (Str::startsWith($key, 'rent_amount-')) {
                $rules[$key] = 'required';
                // $unitValues[] = $value;
            }
        }
        if (count($unitValues) !== count(array_unique($unitValues))) {
            return back()->withErrors(['unit' => __('property_transactions.unit_duplicate')])->withInput();
        }
        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->agreement_date)->format('Y-m-d');
            $tenant        = (new Tenant())->setConnection('tenant')->find($request->tenant_id);
            $agreement     = (new Agreement())->setConnection('tenant')->create([
                'agreement_no'               => $request->agreement_no,
                'agreement_date'             => $formattedDate ?? Carbon::now(),
                'tenant_id'                  => $request->tenant_id ?? $tenant->id,
                'total_no_of_required_units' => $request->total_no_of_required_units,
                'name'                       => $request->name ?? $tenant->name,
                'gender'                     => $request->gender ?? $tenant->gender,
                'id_number'                  => $request->id_number ?? $tenant->id_number,
                'registration_no'            => $request->registration_no ?? $tenant->registration_no,
                'nick_name'                  => $request->nick_name ?? $tenant->nick_name,
                'group_company_name'         => $request->group_company_name ?? $tenant->group_company_name,
                'contact_person'             => $request->contact_person ?? $tenant->contact_person,
                'designation'                => $request->designation ?? $tenant->designation,
                'contact_no'                 => $request->contact_no ?? $tenant->contact_no,
                'whatsapp_no'                => $request->whatsapp_no ?? $tenant->whatsapp_no,
                'company_name'               => $request->company_name ?? $tenant->company_name,
                'fax_no'                     => $request->fax_no ?? $tenant->fax_no,
                'telephone_no'               => $request->telephone_no ?? $tenant->telephone_no,
                'other_contact_no'           => $request->other_contact_no ?? $tenant->other_contact_no,
                'address1'                   => $request->address1 ?? $tenant->address1,
                'address2'                   => $request->address2 ?? $tenant->address2,
                'address3'                   => $request->address3 ?? $tenant->address3,
                'state'                      => $request->state ?? $tenant->state,
                'city'                       => $request->city ?? $tenant->city,
                'country_id'                 => $request->country_id ?? $tenant->country_id,
                'nationality_id'             => $request->nationality_id ?? $tenant->nationality_id,
                'passport_no'                => $request->passport_no ?? $tenant->passport_no,
                'email1'                     => $request->email1 ?? $tenant->email1,
                'email2'                     => $request->email2 ?? $tenant->email2,
                'live_with_id'               => null,
                'business_activity_id'       => null,
                'status'                     => 'pending',
                'booking_status'             => 'agreement',
            ]);

            if ($agreement) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $agreement_details                                     = (new AgreementDetails())->setConnection('tenant')->create([
                    'agreement_id'                => $agreement->id ?? null,
                    'employee_id'                 => (! empty($request->employee_id)) ? $request->employee_id : null,
                    'agent_id'                    => (! empty($request->agent_id)) ? $request->agent_id : null,
                    'agreement_status_id'         => $request->enquiry_status_id ?? null,
                    'agreement_request_status_id' => $request->enquiry_request_status_id ?? null,
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

                for ($i = 1, $ii = $request->total_no_of_required_units; $i <= $ii; $i++) {
                    $propertyId        = $request->input("property_id-$i");
                    $unitDescriptionId = $request->input("unit_description_id-$i");
                    $unitTypeId        = $request->input("unit_type_id-$i");
                    $unitConditionId   = $request->input("unit_condition_id-$i");
                    $viewId            = $request->input("view_id-$i");
                    $propertyType      = $request->input("property_type-$i");
                    if ($request->input("period_from-$i")) {
                        $periodFrom = Carbon::createFromFormat('d/m/Y', $request->input("period_from-$i"))->format('Y-m-d');
                    }
                    if ($request->input("period_to-$i")) {
                        $periodTo = Carbon::createFromFormat('d/m/Y', $request->input("period_to-$i"))->format('Y-m-d');
                    }
                    $city            = $request->input("city-$i");
                    $totalArea       = $request->input("total_area-$i");
                    $areaMeasurement = $request->input("area_measurement-$i");
                    $notes           = $request->input("notes-$i");
                    $unit            = $request->input("unit-$i");
                    $paymentMode     = $request->input("payment_mode-$i");
                    $pdc             = $request->input("pdc-$i");
                    $totalAreaAmount = $request->input("total_area_amount-$i");
                    $amount          = $request->input("amount-$i");
                    $rentAmount      = $request->input("rent_amount-$i");
                    $rentMode        = $request->input("rent_mode-$i");
                    $rentalGl        = $request->input("rental_gl-$i");
                    if ($request->input("lease_break_date-$i")) {
                        $lease_break_date = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$i"))->format('Y-m-d');
                    }
                    $lease_break_comment      = $request->input("lease_break_comment-$i");
                    $total_net_rent_amount    = $request->input("total_net_rent_amount-$i");
                    $vat_percentage           = $request->input("vat_percentage-$i");
                    $vat_amount               = $request->input("vat_amount-$i");
                    $security_deposit         = $request->input("security_deposit-$i");
                    $security_deposit_amount  = $request->input("security_deposit_amount-$i");
                    $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$i");
                    $ewa_limit_mode           = $request->input("ewa_limit_mode-$i");
                    $ewa_limit                = $request->input("ewa_limit-$i");
                    $notice_period            = $request->input("notice_period-$i");
                    // $total =
                    $baseAmount  = (float)$request->input("rent_amount-$i");

                    if ($rentMode === $paymentMode) {

                        $rentAmount = $baseAmount;
                    } else {
                        $rentAmount = calc_rent_amount($rentMode, $paymentMode, $baseAmount, $rentAmount);
                        $total_net_rent_amount = ($rentAmount * ($vat_percentage / 100)) + $rentAmount;
                        $security_deposit_amount = $rentAmount * $security_deposit;
                    }
                    $agreement_units = (new AgreementUnits())->setConnection('tenant')->create([
                        'agreement_id'             => $agreement->id,
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
                        'lease_break_date'         => $lease_break_date ?? null,
                        'security_deposit'         => $security_deposit,
                        'security_deposit_amount'  => $security_deposit_amount,
                        'is_rent_inclusive_of_ewa' => $is_rent_inclusive_of_ewa,
                        'ewa_limit_mode'           => $ewa_limit_mode,
                        'ewa_limit'                => $ewa_limit,
                        'notice_period'            => $notice_period,
                        'total'                    => 0,
                    ]);
                    $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                    $unit_management->update(['booking_status' => 'agreement', 'tenant_id' => $request->tenant_id]);
                    if (isset($request->service_counter[$i])) {
                        for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {
                            $chargeMode       = $request->input("charge_mode-{$i}-{$ind}")[0] ?? null;
                            $chargeModeType   = $request->input("charge_mode_type-{$i}-{$ind}")[0] ?? null;
                            $amountCharge     = $request->input("amount_charge-{$i}-{$ind}")[0] ?? null;
                            $percentageCharge = $request->input("percentage_amount_charge-{$i}-{$ind}")[0] ?? null;
                            $calculateAmount  = $request->input("calculate_amount-{$i}-{$ind}")[0] ?? null;
                            $startDate        = $request->input("amount_charge-{$i}-{$ind}")[0] ?? null;
                            $expiryDate       = $request->input("expiry_date-{$i}-{$ind}")[0] ?? null;
                            $vatPercentage    = $request->input("vat_percentage-{$i}-{$ind}")[0] ?? null;
                            $vatAmount        = $request->input("vat_amount-{$i}-{$ind}")[0] ?? null;
                            $totalAmount      = $request->input("total_amount-{$i}-{$ind}")[0] ?? null;

                            DB::connection('tenant')->table('agreement_units_services')->insert([
                                'agreement_unit_id' => $agreement_units->id,
                                'charge_mode'       => $chargeModeType,
                                'other_charge_type' => $chargeMode,
                                'amount'            => $calculateAmount,
                                'vat'               => $vatAmount,
                                'total'             => $totalAmount,
                            ]);
                        }
                    }
                }
            }

            // }
            DB::commit();
            return to_route('agreement.index')->with('success', __('general.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function edit($id)
    {

        $agreement         = (new Agreement())->setConnection('tenant')->findOrFail($id);
        $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $id)->first();
        $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)->get();
        $services_master   = (new ServiceMaster())->setConnection('tenant')->get();

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
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'dail_code_main'            => $dail_code_main,
            'services_master'          => $services_master,
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
            'agreement'                => $agreement,
            'agreement_details'        => $agreement_details,
            'agreement_units'          => $agreement_units,
        ];
        return view('admin-views.property_transactions.agreements.edit', $data);
    }
    public function get_unit_service($id)
    {
        $service = (new ServiceMaster())->setConnection('tenant')->find($id);
        if ($service) {
            return response()->json([
                'status'      => 200,
                "get_service" => $service,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Group Not Found",
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $agreement         = (new Agreement())->setConnection('tenant')->findOrFail($id);
        $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $id)->first();
        $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)->get();

        $rules      = [];
        $unitValues = [];
        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'property_id-')) {
                $rules[$key] = 'required';
            }

            if (Str::startsWith($key, 'unit-')) {
                $rules[$key]  = 'required';
                $unitValues[] = $value;
            }
            if (Str::startsWith($key, 'rent_amount-')) {
                $rules[$key] = 'required';
                // $unitValues[] = $value;
            }
        }
        if (count($unitValues) !== count(array_unique($unitValues))) {
            return back()->withErrors(['unit' => __('property_transactions.unit_duplicate')])->withInput();
        }
        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->agreement_date)->format('Y-m-d');
            $unit_count    = 0;
            $agreement->update([
                'agreement_no'               => $request->agreement_no ?? $agreement->agreement_no,
                'agreement_date'             => $formattedDate ?? $agreement->agreement_date,
                'tenant_id'                  => $request->tenant_id ?? $agreement->tenant_id,
                'total_no_of_required_units' => $request->total_no_of_required_units ?? $unit_count,
                'name'                       => $request->name ?? $agreement->name,
                'gender'                     => $request->gender ?? $agreement->gender,
                'id_number'                  => $request->id_number ?? $agreement->id_number,
                'registration_no'            => $request->registration_no ?? $agreement->registration_no,
                'nick_name'                  => $request->nick_name ?? $agreement->nick_name,
                'group_company_name'         => $request->group_company_name ?? $agreement->group_company_name,
                'contact_person'             => $request->contact_person ?? $agreement->contact_person,
                'designation'                => $request->designation ?? $agreement->designation,
                'contact_no'                 => $request->contact_no ?? $agreement->contact_no,
                'whatsapp_no'                => $request->whatsapp_no ?? $agreement->whatsapp_no,
                'company_name'               => $request->company_name ?? $agreement->company_name,
                'fax_no'                     => $request->fax_no ?? $agreement->fax_no,
                'telephone_no'               => $request->telephone_no ?? $agreement->telephone_no,
                'other_contact_no'           => $request->other_contact_no ?? $agreement->other_contact_no,
                'address1'                   => $request->address1 ?? $agreement->address1,
                'address2'                   => $request->address2 ?? $agreement->address2,
                'address3'                   => $request->address3 ?? $agreement->address3,
                'state'                      => $request->state ?? $agreement->state,
                'city'                       => $request->city ?? $agreement->city,
                'country_id'                 => $request->country_id ?? $agreement->country_id,
                'nationality_id'             => $request->nationality_id ?? $agreement->nationality_id,
                'passport_no'                => $request->passport_no ?? $agreement->passport_no,
                'email1'                     => $request->email1 ?? $agreement->email1,
                'email2'                     => $request->email2 ?? $agreement->email2,
                'live_with_id'               => $request->live_with_id ?? (($agreement->live_with_id) ? $agreement->live_with_id : null),
                'business_activity_id'       => $request->business_activity_id ?? (($agreement->business_activity_id) ? $agreement->business_activity_id : null),
            ]);
            $agreement_units->each(function ($agreement_units_item) {
                $agreement_units_item->delete();
            });
            if ($agreement) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $agreement_details->update([
                    'agreement_id'                => $agreement->id,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
                    'agreement_status_id'         => $request->enquiry_status_id ?? $agreement_details->enquiry_status_id,
                    'agreement_request_status_id' => $request->enquiry_request_status_id ?? $agreement_details->enquiry_request_status_id,
                    'decision_maker'              => $request->decision_maker ?? $agreement_details->decision_maker,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? $agreement_details->decision_maker_designation,
                    'current_office_location'     => $request->current_office_location ?? $agreement_details->current_office_location,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? $agreement_details->reason_of_relocation,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? $agreement_details->budget_for_relocation_start,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? $agreement_details->budget_for_relocation_end,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? $agreement_details->no_of_emp_staff_strength,
                    'time_frame_for_relocation'   => $request->time_frame_for_relocation ?? $agreement_details->time_frame_for_relocation,
                    'relocation_date'             => $relocation_date ?? $agreement_details->relocation_date,
                    'period_from'                 => $period_from ?? $agreement_details->period_from,
                    'period_to'                   => $period_to ?? $agreement_details->period_to,
                ]);
                foreach ($request->city_id as $key => $city_value) {
                    $propertyId        = $request->input("property_id-$key");
                    $unitDescriptionId = $request->input("unit_description_id-$key");
                    $unitTypeId        = $request->input("unit_type_id-$key");
                    $unitConditionId   = $request->input("unit_condition_id-$key");
                    $viewId            = $request->input("view_id-$key");
                    $propertyType      = $request->input("property_type-$key");
                    $periodFrom        = Carbon::createFromFormat('d/m/Y', $request->input("period_from-$key"))->format('Y-m-d') ?? null;
                    $periodTo          = Carbon::createFromFormat('d/m/Y', $request->input("period_to-$key"))->format('Y-m-d') ?? null;
                    // $city                     = $request->input("city_id[$key]");
                    $totalArea       = $request->input("total_area-$key");
                    $areaMeasurement = $request->input("area_measurement-$key");
                    $notes           = $request->input("notes-$key");
                    $unit            = $request->input("unit-$key");
                    $paymentMode     = $request->input("payment_mode-$key");
                    $pdc             = $request->input("pdc-$key");
                    $totalAreaAmount = $request->input("total_area_amount-$key");
                    $amount          = $request->input("amount-$key");
                    $rentAmount      = $request->input("rent_amount-$key");
                    $rentMode        = $request->input("rent_mode-$key");
                    $rentalGl        = $request->input("rental_gl-$key");
                    if ($request->input("lease_break_date-$key")) {
                        $lease_break_date = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$key"))->format('Y-m-d');
                    }
                    $lease_break_comment      = $request->input("lease_break_comment-$key");
                    $total_net_rent_amount    = $request->input("total_net_rent_amount-$key") ?? 0;
                    $vat_percentage           = $request->input("vat_percentage-$key");
                    $vat_amount               = $request->input("vat_amount-$key");
                    $security_deposit         = $request->input("security_deposit-$key");
                    $security_deposit_amount  = $request->input("security_deposit_amount-$key");
                    $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$key");
                    $ewa_limit_mode           = $request->input("ewa_limit_mode-$key");
                    $ewa_limit                = $request->input("ewa_limit-$key");
                    $notice_period            = $request->input("notice_period-$key");
                    $agreement_units          = (new AgreementUnits())->setConnection('tenant')->create([
                        'agreement_id'             => $agreement->id,
                        'property_id'              => $propertyId,
                        'commencement_date'        => $periodFrom ?? $period_from,
                        'expiry_date'              => $periodTo ?? $period_to,
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
                        'lease_break_date'         => $lease_break_date ?? null,
                        'security_deposit'         => $security_deposit,
                        'security_deposit_amount'  => $security_deposit_amount,
                        'is_rent_inclusive_of_ewa' => $is_rent_inclusive_of_ewa,
                        'ewa_limit_mode'           => $ewa_limit_mode,
                        'ewa_limit'                => $ewa_limit,
                        'notice_period'            => $notice_period,
                        'total'                    => 0,
                    ]);
                    $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                    $unit_management->update(['booking_status' => 'agreement']);

                    if (isset($request->service_counter[$key])) {
                        for ($ind = 1, $inde = $request->service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = (isset($request->input("charge_mode-{$key}-{$ind}")[0])) ? $request->input("charge_mode-{$key}-{$ind}")[0] : null;
                            $chargeModeType   = (isset($request->input("charge_mode_type-{$key}-{$ind}")[0])) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] : null;
                            $amountCharge     = (isset($request->input("amount_charge-{$key}-{$ind}")[0])) ? $request->input("amount_charge-{$key}-{$ind}")[0] : null;
                            $percentageCharge = (isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] : null;
                            $calculateAmount  = (isset($request->input("calculate_amount-{$key}-{$ind}")[0])) ? $request->input("calculate_amount-{$key}-{$ind}")[0] : null;
                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate     = $start ?? null;
                            $expiryDate    = $expiry ?? null;
                            $vatPercentage = (isset($request->input("vat_percentage-{$key}-{$ind}")[0])) ? $request->input("vat_percentage-{$key}-{$ind}")[0] : null;
                            $vatAmount     = (isset($request->input("vat_amount-{$key}-{$ind}")[0])) ? $request->input("vat_amount-{$key}-{$ind}")[0] : null;
                            $totalAmount   = (isset($request->input("total_amount-{$key}-{$ind}")[0])) ? $request->input("total_amount-{$key}-{$ind}")[0] : null;
                            if ($chargeModeType != null) {
                                DB::connection('tenant')->table('agreement_units_services')->insert([
                                    'agreement_unit_id' => $agreement_units->id,
                                    'charge_mode'       => $chargeModeType,
                                    'other_charge_type' => $chargeMode,
                                    'amount'            => $calculateAmount,
                                    'vat'               => $vatAmount,
                                    'total'             => $totalAmount,
                                ]);
                            }
                        }
                    }
                    if (! isset($request->service_counter[$key]) && isset($request->old_service_counter[$key])) {
                        for ($ind = 1, $inde = $request->old_service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = (isset($request->input("charge_mode-{$key}-{$ind}")[0])) ? $request->input("charge_mode-{$key}-{$ind}")[0] : null;
                            $chargeModeType   = (isset($request->input("charge_mode_type-{$key}-{$ind}")[0])) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] : null;
                            $amountCharge     = (isset($request->input("amount_charge-{$key}-{$ind}")[0])) ? $request->input("amount_charge-{$key}-{$ind}")[0] : null;
                            $percentageCharge = (isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] : null;
                            $calculateAmount  = (isset($request->input("calculate_amount-{$key}-{$ind}")[0])) ? $request->input("calculate_amount-{$key}-{$ind}")[0] : null;
                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate     = $start ?? null;
                            $expiryDate    = $expiry ?? null;
                            $vatPercentage = (isset($request->input("vat_percentage-{$key}-{$ind}")[0])) ? $request->input("vat_percentage-{$key}-{$ind}")[0] : null;
                            $vatAmount     = (isset($request->input("vat_amount-{$key}-{$ind}")[0])) ? $request->input("vat_amount-{$key}-{$ind}")[0] : null;
                            $totalAmount   = (isset($request->input("total_amount-{$key}-{$ind}")[0])) ? $request->input("total_amount-{$key}-{$ind}")[0] : null;
                            if ($chargeModeType != null) {
                                DB::connection('tenant')->table('agreement_units_services')->insert([
                                    'agreement_unit_id' => $agreement_units->id,
                                    'charge_mode'       => $chargeModeType,
                                    'other_charge_type' => $chargeMode,
                                    'amount'            => $calculateAmount,
                                    'vat'               => $vatAmount,
                                    'total'             => $totalAmount,
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            return to_route('agreement.index')->with('success', __('general.updated_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $agreement         = (new Agreement())->setConnection('tenant')->findOrFail($request->id);
        $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $request->id)->first();
        if ($agreement_details) {
            $agreement_details->delete();
        }
        $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $request->id)->get();
        $unitManagementIds = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $request->id)
            ->pluck('unit_id')
            ->toArray();
        if (! empty($agreement_units)) {
            (new UnitManagement())->setConnection('tenant')->whereIn('id', $unitManagementIds)
                ->update(['booking_status' => 'empty']);
        }
        foreach ($agreement_units as $unit) {
            (new AgreementUnitsService())->setConnection('tenant')->where('agreement_unit_id', $unit->id)->delete();
            $unit->delete();
        }
        $agreement->delete();

        return to_route('agreement.index')->with('success', __('general.deleted_successfully'));
    }

    public function show($id)
    {

        $agreement         = (new Agreement())->setConnection('tenant')->with('tenant', 'agreement_details', 'agreement_units')->findOrFail($id);
        $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $id)->first();
        $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)->get();

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
        $services_master          = (new ServiceMaster())->setConnection('tenant')->get();
        $data                     = [
            'services_master'          => $services_master,
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
            'agreement'                => $agreement,
            'agreement_details'        => $agreement_details,
            'agreement_units'          => $agreement_units,
        ];
        return view('admin-views.property_transactions.agreements.view', $data);
    }
    public function check_property($id = 0)
    {
        $agreement = (new Agreement())->setConnection('tenant')->findOrFail($id);
        $unit_ids  = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)
            ->pluck('unit_id')
            ->toArray();
        $units        = (new UnitManagement())->setConnection('tenant')->whereIn('id', $unit_ids)->get();
        $property_ids = $units->pluck('property_management_id')->toArray();
        $property     = (new PropertyManagement())->setConnection('tenant')->forUser()->whereIn('id', $property_ids)->get();
        if ($property->isEmpty()) {
            $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        }
        $data = [
            'properties' => $property,
        ];
        return view('admin-views.property_transactions.agreements.check_property', $data);
    }
    public function view_image($id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        // $property = PropertyManagement::findOrFail($id);
        $data = [
            'property_item' => $property,
        ];
        return view('admin-views.property_transactions.agreements.view_image', $data);
    }
    public function list_view($id)
    {
        $property = (new PropertyManagement())->setConnection('tenant')->with(
            'blocks_management_child',
            'blocks_management_child.block',
            'blocks_management_child.floors_management_child',
            'blocks_management_child.floors_management_child.floor_management_main',
            'blocks_management_child.floors_management_child.unit_management_child',
            'blocks_management_child.floors_management_child.unit_management_child.unit_management_main'
        )->findOrFail($id);
        $data = [
            'property_item' => $property,
        ];
        return view('admin-views.property_transactions.agreements.list_view', $data);
    }

    public function signed($id)
    {
        // dd(Schema::connection('tenant')->getColumnListing('schedules'));

        $agreement            = Agreement::findOrFail($id);
        $units                = AgreementUnits::where('agreement_id', $id)->get();
        $rent_intervals       = [];
        $total_service_amount = 0;
        try {
            foreach ($units as $unit) {
                // advanced_group_id
                $main_unit = UnitManagement::where('id' , $unit->unit_id)->select('advanced_group_id' , 'ledger_id')->first(); 
                $start_date = Carbon::parse($unit->commencement_date);
                $end_date   = Carbon::parse($unit->expiry_date);
                $unit_ledger = MainLedger::where('id', $main_unit->ledger_id)->first();
                $advanced_unit_ledger = MainLedger::where('id', $main_unit->advanced_group_id)->first();
                $company              = auth()->user() ?? User::first();
                $total_service_amount = AgreementUnitsService::where('agreement_unit_id', $unit->id ?? 0)->sum('amount') ?? 0;
                $total_services       = AgreementUnitsService::where('agreement_unit_id', $unit->id)->with('service_master:id,vat')->get();
                // dd($advanced_unit_ledger);
                $original_start_date = $start_date->copy();
                if (isset($total_services)) {
                    if ($unit->rent_mode == null) {
                        $unit->rent_mode = 2;
                    }

                    foreach ($total_services as $total_service_item) {
                        $start_date = $original_start_date->copy();

                        if ($unit->rent_mode == 2) {
                            while ($start_date <= $end_date) {
                                $rent_intervals[] = [
                                    'rent_amount'          => $total_service_item->amount,
                                    'rent_mode'            => $unit->rent_mode,
                                    'total_service_amount' => $total_service_amount ?? 0,
                                    'unit_id'              => $unit->unit_id,
                                    'agreement_id'         => $agreement->id,
                                    'vat_amount'           => $total_service_item->vat ?? 0,
                                    'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                    'tenant_id'            => $agreement->tenant_id,
                                    'currency'             => $company->currency_code ?? 'BHD',
                                    'billing_month_year'   => $start_date->format('Y-m'),
                                    'service'              => 'yes',
                                    'category'             => 'service',
                                    'service_id'           => $total_service_item->other_charge_type,
                                    'invoice_status'       => null,
                                    'branch_id'            =>  1,
                                    'created_at'           => now(),
                                ];
                                $start_date->addMonth();
                            }
                        } elseif ($unit->rent_mode == 3) {
                            while ($start_date <= $end_date) {
                                $rent_intervals[] = [
                                    'rent_amount'          => $total_service_item->amount,
                                    'rent_mode'            => $unit->rent_mode,
                                    'total_service_amount' => $total_service_amount ?? 0,
                                    'unit_id'              => $unit->unit_id,
                                    'agreement_id'         => $agreement->id,
                                    'vat_amount'           => $total_service_item->vat ?? 0,
                                    'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                    'tenant_id'            => $agreement->tenant_id,
                                    'currency'             => $company->currency_code ?? 'BHD',
                                    'billing_month_year'   => $start_date->format('Y-m'),
                                    'service'              => 'yes',
                                    'category'             => 'service',
                                    'invoice_status'       => null,
                                    'branch_id'            =>  1,
                                    'service_id'           => $total_service_item->other_charge_type,
                                    'created_at'           => now(),

                                ];
                                $start_date->addMonths(2);
                            }
                        } elseif ($unit->rent_mode == 4) {
                            while ($start_date <= $end_date) {
                                $rent_intervals[] = [
                                    'rent_amount'          => $total_service_item->amount,
                                    'rent_mode'            => $unit->rent_mode,
                                    'total_service_amount' => $total_service_amount ?? 0,
                                    'unit_id'              => $unit->unit_id,
                                    'agreement_id'         => $agreement->id,
                                    'vat_amount'           => $total_service_item->vat ?? 0,
                                    'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                    'tenant_id'            => $agreement->tenant_id,
                                    'currency'             => $company->currency_code ?? 'BHD',
                                    'billing_month_year'   => $start_date->format('Y-m'),
                                    'service'              => 'yes',
                                    'category'             => 'service',
                                    'invoice_status'       => null,
                                    'branch_id'            =>  1,
                                    'service_id'           => $total_service_item->other_charge_type,
                                    'created_at'           => now(),

                                ];
                                $start_date->addMonths(3);
                            }
                        } elseif ($unit->rent_mode == 5) {
                            while ($start_date <= $end_date) {
                                $rent_intervals[] = [
                                    'rent_amount'          => $total_service_item->amount,
                                    'rent_mode'            => $unit->rent_mode,
                                    'total_service_amount' => $total_service_amount ?? 0,
                                    'unit_id'              => $unit->unit_id,
                                    'agreement_id'         => $agreement->id,
                                    'vat_amount'           => $total_service_item->vat ?? 0,
                                    'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                    'tenant_id'            => $agreement->tenant_id,
                                    'currency'             => $company->currency_code ?? 'BHD',
                                    'billing_month_year'   => $start_date->format('Y-m'),
                                    'service'              => 'yes',
                                    'category'             => 'service',
                                    'service_id'           => $total_service_item->other_charge_type,
                                    'invoice_status'       => null,
                                    'branch_id'            =>  1,
                                    'created_at'           => now(),

                                ];
                                $start_date->addMonths(6);
                            }
                        } elseif ($unit->rent_mode == 6) {
                            while ($start_date <= $end_date) {
                                $rent_intervals[] = [
                                    'rent_amount'          => $total_service_item->amount,
                                    'rent_mode'            => $unit->rent_mode,
                                    'total_service_amount' => $total_service_amount ?? 0,
                                    'unit_id'              => $unit->unit_id,
                                    'agreement_id'         => $agreement->id,
                                    'vat_amount'           => $total_service_item->vat ?? 0,
                                    'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                    'tenant_id'            => $agreement->tenant_id,
                                    'currency'             => $company->currency_code ?? 'BHD',
                                    'billing_month_year'   => $start_date->format('Y-m'),
                                    'service'              => 'yes',
                                    'category'             => 'service',
                                    'service_id'           => $total_service_item->other_charge_type,
                                    'created_at'           => now(),

                                ];
                                $start_date->addMonths(12);
                            }
                        }

                        if ($start_date->diffInDays($end_date) > 0) {
                            $remaining_days      = $start_date->diffInDays($end_date);
                            $daily_rent          = $unit->rent_amount / 30;
                            $partial_rent_amount = $remaining_days * $daily_rent;
                            $rent_intervals[]    = [
                                'rent_amount'          => $partial_rent_amount,
                                'rent_mode'            => $unit->rent_mode,
                                'total_service_amount' => $total_service_amount ?? 0,
                                'unit_id'              => $unit->unit_id,
                                'agreement_id'         => $agreement->id,
                                'vat_amount'           => $total_service_item->vat ?? 0,
                                'vat'                  => $total_service_item->service_master?->vat ?? 0,
                                'tenant_id'            => $agreement->tenant_id,
                                'currency'             => $company->currency_code ?? 'BHD',
                                'billing_month_year'   => $start_date->format('Y-m'),
                                'service'              => 'yes',
                                'category'             => 'service',
                                'invoice_status'       => null,
                                'branch_id'            =>  1,
                                'service_id'           => $total_service_item->other_charge_type,
                                'created_at'           => now(),

                            ];
                        }
                    }
                }
                $start_date = $original_start_date->copy();

                if ($unit->rent_mode == 2) {
                    $monthlyAmount = convertToMonthly($unit->rent_amount, $unit->rent_mode);
                    while ($start_date <= $end_date) {
                        $rent_intervals[] = [
                            'rent_amount'          => $unit->rent_amount,
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $start_date->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            =>  1,
                            'created_at'           => now(),

                        ];
                        $start_date->addMonth();
                    }
                } elseif ($unit->rent_mode == 3) {
                    while ($start_date <= $end_date) {
                        $rent_intervals[] = [
                            'rent_amount'          => $unit->rent_amount,
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $start_date->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            =>  1,
                            'created_at'           => now(),

                        ];
                        $start_date->addMonths(2);
                    }
                } elseif ($unit->rent_mode == 4) {
                    while ($start_date <= $end_date) {
                        $rent_intervals[] = [
                            'rent_amount'          => $unit->rent_amount,
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $start_date->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            =>  1,
                            'created_at'           => now(),

                        ];
                        $start_date->addMonths(3);
                    }
                } elseif ($unit->rent_mode == 5) {
                    while ($start_date <= $end_date) {
                        $rent_intervals[] = [
                            'rent_amount'          => $unit->rent_amount,
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $start_date->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            =>  1,
                            'created_at'           => now(),

                        ];

                        $start_date->addMonths(6);
                    }
                } elseif ($unit->rent_mode == 6) {
                    while ($start_date <= $end_date) {
                        $rent_intervals[] = [
                            'rent_amount'          => $unit->rent_amount,
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $start_date->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            =>  1,
                            'created_at'           => now(),

                        ];

                        $start_date->addMonths(12);
                    }
                }

                if ($start_date->diffInDays($end_date) > 0) {
                    $remaining_days      = $start_date->diffInDays($end_date);
                    $daily_rent          = $unit->rent_amount / 30;
                    $partial_rent_amount = $remaining_days * $daily_rent;
                    $rent_intervals[]    = [
                        'rent_amount'          => $partial_rent_amount,
                        'rent_mode'            => $unit->rent_mode,
                        'total_service_amount' => $total_service_amount ?? 0,
                        'unit_id'              => $unit->unit_id,
                        'agreement_id'         => $agreement->id,
                        'vat_amount'           => $unit->vat_amount ?? 0,
                        'vat'                  => $unit->vat_percentage ?? 0,
                        'tenant_id'            => $agreement->tenant_id,
                        'currency'             => $company->currency_code ?? 'BHD',
                        'billing_month_year'   => $start_date->format('Y-m'),
                        'service'              => 'no',
                        'category'             => 'rent',
                        'service_id'           => null,
                        'invoice_status'       => null,
                        'branch_id'            =>  1,
                        'created_at'           => now(),

                    ];
                } 
                if ($advanced_unit_ledger) {
                    // Log::info($advanced_unit_ledger);
                    $start_date = Carbon::parse($unit->commencement_date);
                    $end_date   = Carbon::parse($unit->expiry_date);
                    $monthlyAmount = convertToMonthly($unit->rent_amount, $unit->rent_mode);
                    // dd( $monthlyAmount);
                    $current = $start_date->copy();

                    while ($current <= $end_date) {
 
                        $daysInMonth = $current->daysInMonth;
 
                        $periodStart = $current->copy()->startOfMonth();
                        if ($current->isSameMonth($start_date)) {
                            $periodStart = $start_date->copy();
                        }
 
                        $periodEnd = $current->copy()->endOfMonth();
                        if ($current->isSameMonth($end_date)) {
                            $periodEnd = $end_date->copy();
                        }
 
                        $daysUsed = $periodStart->diffInDays($periodEnd) + 1;
 
                        $proRatedAmount = ($monthlyAmount / $daysInMonth) * $daysUsed;

                        $rent_intervals_for_accrud[] = [
                            'rent_amount'          => round($proRatedAmount, 5),
                            'rent_mode'            => $unit->rent_mode,
                            'total_service_amount' => $total_service_amount ?? 0,
                            'unit_id'              => $unit->unit_id,
                            'agreement_id'         => $agreement->id,
                            'vat_amount'           => $unit->vat_amount ?? 0,
                            'vat'                  => $unit->vat_percentage ?? 0,
                            'tenant_id'            => $agreement->tenant_id,
                            'currency'             => $company->currency_code ?? 'BHD',
                            'billing_month_year'   => $current->format('Y-m'),
                            'service'              => 'no',
                            'category'             => 'rent',
                            'service_id'           => null,
                            'invoice_status'       => null,
                            'branch_id'            => 1,
                            'created_at'           => now(),
                        ];

                        $current->addMonth();
                    }
                    //  Log::info($rent_intervals_for_accrud);
                    foreach ($rent_intervals_for_accrud as $interval) {

                        $billingDate = Carbon::parse($interval['billing_month_year'] . '-01');

                        $accruedAmount = $interval['rent_amount'] + $interval['total_service_amount'];
                        AccruedIncome::create([
                            'ledger_id'         => $advanced_unit_ledger->id,
                            'income_ledger_id'  => $unit_ledger->id,

                            'status'            => 'unpaid',
                            'voucher_date'      =>  now(),
                            'applicable_date'   => $billingDate,
                            'receivable_upto'   => $billingDate->copy()->endOfMonth(),
                            // 'billing_month'     => $billingDate,
                            'accrued_amount'    => $accruedAmount,
                            'received_amount'   => 0,
                            'balance_amount'    => $accruedAmount,
                            'balance_for'       => '1 Month',
                        ]);
                        // DB::table('accrued_incomes')->insert([
                        //     'ledger_id'         => $unit_ledger->id,
                        //     'income_ledger_id'  => $unit_ledger->id,

                        //     'status'            => 'unpaid',
                        //     'voucher_date'      => now(),
                        //     'applicable_date'   => $billingDate,
                        //     'receivable_upto'   => $billingDate->copy()->endOfMonth(),
                        //     'billing_month'     => $billingDate,
                        //     'accrued_amount'    => $accruedAmount,
                        //     'received_amount'   => 0,
                        //     'balance_amount'    => $accruedAmount,
                        //     'balance_for'       => '1 Month',
                        //     'created_at'        => now(),
                        //     'updated_at'        => now(),
                        // ]);
                    }
                }
            }
            DB::connection('tenant')->table('schedules')->insert($rent_intervals);
            $agreement->update([
                'booking_status' => 'signed',
                'status'         => 'completed',
            ]);
            return to_route('agreement.index')->with('success', __('general.signed_success'));
        } catch (Throwable $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'total_no_of_required_units' => 'required',
        ]);
        $all_units_filter  = general_search_unit($request);
        $tenant_id         = $request->tenant_id;
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->get();
        $unit_conditions   = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->get();
        $unit_types        = (new UnitType())->setConnection('tenant')->select('id', 'name')->get();
        $unit_views        = (new View())->setConnection('tenant')->select('id', 'name')->get();

        return view('admin-views.property_transactions.enquiries.general_check_property', [
            'units'             => $all_units_filter,
            'unit_descriptions' => $unit_descriptions,
            'unit_conditions'   => $unit_conditions,
            'unit_types'        => $unit_types,
            'unit_views'        => $unit_views,
            'tenant_id'         => $tenant_id,

        ]);
    }
    public function create_with_select_unit(Request $request)
    {
        // dd($request->all());
        $ids = $request->bulk_ids;
        if ($ids == null) {
            return redirect()->back()->with('error', 'Please Select Unit');
        }
        $tenant_id = $request->tenant_id;
        $tenant    = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')
            ->with('country_master')->where('id', $tenant_id)->first();
        $allTenants = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')
            ->with('country_master')
            ->paginate(5000);

        $agents                   = (new Agent())->setConnection('tenant')->select('id', 'name', 'company_name', 'type')->lazy(5000);
        $enquiry_statuses         = (new EnquiryStatus())->setConnection('tenant')->select('id', 'name')->lazy();
        $enquiry_request_statuses = (new EnquiryRequestStatus())->setConnection('tenant')->select('id', 'name')->lazy();
        $employees                = (new Employee())->setConnection('tenant')->select('id', 'name')->lazy();
        $country_master           = (new CountryMaster())->setConnection('tenant')->select('id', 'country_id')->with('country')->lazy();
        $all_units                = (new UnitManagement())->setConnection('tenant')->select('id', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id', 'ledger_id')->whereIn('id', $ids)
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
                'unit_condition',
                'unit_ledger'
            )->lazy();
        $live_withs          = (new LiveWith())->setConnection('tenant')->select('id', 'name')->lazy();
        $business_activities = (new BusinessActivity())->setConnection('tenant')->select('id', 'name')->lazy();
        $buildings           = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name')->lazy();
        $unit_descriptions   = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_conditions     = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_types          = (new UnitType())->setConnection('tenant')->select('id', 'name')->lazy();
        $views               = (new View())->setConnection('tenant')->select('id', 'name')->lazy();
        $property_types      = (new PropertyType())->setConnection('tenant')->select('id', 'name')->lazy();
        $services_master     = (new ServiceMaster())->setConnection('tenant')->select('id', 'name')->lazy();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'dail_code_main'            => $dail_code_main,
            'services_master'          => $services_master,
            'tenant_id'                => $tenant_id,
            'tenant'                   => $tenant,
            'agreement_units'          => $all_units,
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
        return view('admin-views.property_transactions.agreements.create_with_select_unit', $data);
    }
    public function empty_unit_from_service_agreement($id)
    {
        $deleted = DB::connection('tenant')->table('agreement_units_services')->where('id', $id)->delete();

        return response()->json([
            'status'  => 200,
            'success' => $deleted > 0,
        ]);
    }
    public function schedule(Request $request, $id)
    {
        $ids = $request->bulk_ids;
        $company = (new Company())
            ->setConnection('tenant')
            ->select('id', 'financial_year', 'book_begining')
            ->first();
        $companyStartMonth = null;

        if ($company && $company->book_begining) {
            $companyStartMonth = Carbon::parse($company->book_begining)->format('Y-m');
        }
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['rent_amount' => $request->rent_amount];
            (new Schedule())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $schedules = Schedule::where('agreement_id', $id)->where('billing_month_year', '>=', $companyStartMonth)->paginate();
        $agreement = Agreement::where('id', $id)->first();
        $data      = [
            'schedules' => $schedules,
            'agreement' => $agreement,
        ];
        return view('admin-views.property_transactions.agreements.schedule_list', $data);
    }

    public function review($id)
    {

        $agreement         = (new Agreement())->setConnection('tenant')->findOrFail($id);
        $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $id)->first();
        $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)->with('agreement_unit_services')->get();
        $services_master   = (new ServiceMaster())->setConnection('tenant')->get();

        $data = [
            'agreement'         => $agreement,
            'agreement_details' => $agreement_details,
            'agreement_units'   => $agreement_units,
            'services_master'   => $services_master,
        ];
        return view('admin-views.property_transactions.agreements.review', $data);
    }
    public function update_review(Request $request)
    {

        // dd($request->all());

        $applicable_date = Carbon::createFromFormat('d/m/Y', $request->applicable_date)->format('Y-m');

        // dd($applicable_date );
        foreach ($request->unit_id as $unit_id) {
            $schedules = (new Schedule())
                ->setConnection('tenant')
                ->where('unit_id', $unit_id)
                ->where('service', 'no')
                ->where('invoice_status', 'pending')
                ->whereDate('billing_month_year', '>=', $applicable_date)
                ->get();

            foreach ($schedules as $schedule) {
                // dd($request->input('rent_amount-' .$unit_id) );

                $schedule->update([
                    'rent_amount' => $request->input('rent_amount-' . $unit_id),

                ]);
            }
        }
        Toastr::success(translate('rent_amount_updated_successfully!'));
        return to_route('agreement.index');
        // $agreement         = (new Agreement())->setConnection('tenant')->findOrFail($id);
        // $agreement_details = (new AgreementDetails())->setConnection('tenant')->where('agreement_id', $id)->first();
        // $agreement_units   = (new AgreementUnits())->setConnection('tenant')->where('agreement_id', $id)->with('agreement_unit_services')->get();
        // $services_master   = (new ServiceMaster())->setConnection('tenant')->get();

    }
    public function agreement_update_status($data, $ids)
    {
        if ($data['status'] == 'canceled') {

            $agreements = Agreement::whereIn('id', $ids)
                ->where('booking_status', 'agreement')
                ->pluck('id');
            Agreement::whereIn('id', $agreements)->update($data);
            $units = AgreementUnits::whereIn('agreement_id', $agreements)
                ->pluck('unit_id');
            UnitManagement::whereIn('id', $units)->update([
                'booking_status' => 'empty'
            ]);
        }
    }
    // public function agreement_update_status($data, $ids)
    // {
    //     if ($data['status'] == 'canceled') {
    //         Agreement::whereIn('id', $ids)->where('booking_status', 'agreement')->update($data);

    //         $units = AgreementUnits::whereIn('agreement_id', $ids)->pluck('unit_id'); 
    //         UnitManagement::whereIn('id', $units)->update([
    //             'booking_status' => 'empty'
    //         ]);
    //     }
    // }
}
