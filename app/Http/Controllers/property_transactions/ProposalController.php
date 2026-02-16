<?php

namespace App\Http\Controllers\property_transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionsRequest;
use App\Models\Agent;
use App\Models\Agreement;
use App\Models\AgreementDetails;
use App\Models\AgreementUnits;
use App\Models\Booking;
use App\Models\BookingDetails;
use App\Models\BookingUnits;
use App\Models\BusinessActivity;
use App\Models\CountryMaster;
use App\Models\Employee;
use App\Models\EnquiryRequestStatus;
use App\Models\EnquiryStatus;
use App\Models\LiveWith;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
use App\Models\Proposal;
use App\Models\ProposalDetails;
use App\Models\ProposalUnits;
use App\Models\ProposalUnitsService;
use App\Models\ServiceMaster;
use App\Models\Tenant;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\UnitType;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('proposal');
        $ids     = $request->bulk_ids;
        $lastRun = Cache::get('last_proposal_expiry_run');
        if (! $lastRun || now()->diffInHours($lastRun) >= 24) {
            $proposal_settings = get_business_settings('proposal')->where('type', 'proposal_expire_date')->first();
            $expiry_days       = $proposal_settings ? (int) $proposal_settings->value : 0;
            if ($expiry_days > 0) {
                expire_unit($expiry_days, 'Proposal', 'ProposalUnits');
                Cache::put('last_proposal_expiry_run', now(), now()->addDay());
            }
        }
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            (new Proposal())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $proposals   = (new Proposal())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('proposal_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->where('status', 'pending')->orWhere('status', 'proposal')
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = (new Proposal())->setConnection('tenant')->query();
            if ($request->booking_status && $request->booking_status != -1) {
                $report_query->where('booking_status', $request->booking_status);
            }
            if ($request->status && $request->status != -1) {
                $report_query->where('status', $request->status);
            }
            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
                $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
                $report_query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $proposals = $report_query->orderBy('created_at', 'desc')->paginate();
        }
        $data = [
            'proposals' => $proposals,
            'search'    => $search,

        ];
        return view("admin-views.property_transactions.proposals.proposal_list", $data);
    }

    public function create()
    {
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
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
            'dail_code_main'           => $dail_code_main,
        ];
        return view('admin-views.property_transactions.proposals.create', $data);
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
    public function get_units(Request $request)
    {
        $property_id         = $request->input('property_id');
        $unit_description_id = $request->input('unit_description_id');
        $unit_type_id        = $request->input('unit_type_id');
        $unit_condition_id   = $request->input('unit_condition_id');
        $view_id             = $request->input('view_id');
        $property_type       = $request->input('property_type');
        $units               = (new UnitManagement())->setConnection('tenant')
            // ->where('booking_status', 'empty')
            ->with('unit_management_main:id,name')
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
        }
        if (count($unitValues) !== count(array_unique($unitValues))) {
            return back()->withErrors(['unit' => __('property_transactions.unit_duplicate')])->withInput();
        }
        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->proposal_date)->format('Y-m-d');
            $unit_count    = 0;
            $proposal      = (new Proposal())->setConnection('tenant')->create([
                'proposal_no'                => $request->proposal_no,
                'proposal_date'              => $formattedDate,
                'tenant_id'                  => $request->tenant_id,
                'total_no_of_required_units' => $request->total_no_of_required_units ?? $unit_count,
                'name'                       => $request->name ?? null,
                'gender'                     => $request->gender ?? null,
                'id_number'                  => $request->id_number ?? null,
                'registration_no'            => $request->registration_no ?? null,
                'nick_name'                  => $request->nick_name ?? null,
                'group_company_name'         => $request->group_company_name ?? null,
                'contact_person'             => $request->contact_person ?? null,
                'designation'                => $request->designation ?? null,
                'contact_no'                 => $request->contact_no ?? null,
                'whatsapp_no'                => $request->whatsapp_no ?? null,
                'company_name'               => $request->company_name ?? null,
                'fax_no'                     => $request->fax_no ?? null,
                'telephone_no'               => $request->telephone_no ?? null,
                'other_contact_no'           => $request->other_contact_no ?? null,
                'address1'                   => $request->address1 ?? null,
                'address2'                   => $request->address2 ?? null,
                'address3'                   => $request->address3 ?? null,
                'state'                      => $request->state ?? null,
                'city'                       => $request->city ?? null,
                'country_id'                 => $request->country_id ?? null,
                'nationality_id'             => $request->nationality_id ?? null,
                'passport_no'                => $request->passport_no ?? null,
                'email1'                     => $request->email1 ?? null,
                'email2'                     => $request->email2 ?? null,
                'live_with_id'               => $request->live_with_id ?? null,
                'business_activity_id'       => $request->business_activity_id ?? null,
                'status'                     => 'pending',
                'booking_status'             => 'proposal',
            ]);

            if ($proposal) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $proposal_details                                      = (new ProposalDetails())->setConnection('tenant')->create([
                    'proposal_id'                 => $proposal->id ?? null,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
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
                    $city                     = $request->input("city-$i");
                    $totalArea                = $request->input("total_area-$i");
                    $areaMeasurement          = $request->input("area_measurement-$i");
                    $notes                    = $request->input("notes-$i");
                    $unit                     = $request->input("unit-$i");
                    $paymentMode              = $request->input("payment_mode-$i");
                    $pdc                      = $request->input("pdc-$i");
                    $totalAreaAmount          = $request->input("total_area_amount-$i");
                    $amount                   = $request->input("amount-$i");
                    $rentAmount               = $request->input("rent_amount-$i");
                    $rentMode                 = $request->input("rent_mode-$i");
                    $rentalGl                 = $request->input("rental_gl-$i");
                    if ($request->input("lease_break_date-$i")) {
                        $lease_break_date_format = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$i"))->format('Y-m-d');
                    }
                    $lease_break_date         = $lease_break_date_format ?? null;
                    $lease_break_comment      = $request->input("lease_break_comments-$i");
                    $total_net_rent_amount    = $request->input("total_net_rent_amount-$i") ?? 0;
                    $vat_percentage           = $request->input("vat_percentage-$i");
                    $vat_amount               = $request->input("vat_amount-$i");
                    $security_deposit         = $request->input("security_deposit_months_rent-$i");
                    $security_deposit_amount  = $request->input("security_deposit_amount-$i");
                    $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$i");
                    $ewa_limit_mode           = $request->input("ewa_limit_mode-$i");
                    $ewa_limit                = $request->input("ewa_limit-$i");
                    $notice_period            = $request->input("notice_period-$i");
                    $baseAmount  = (float)$request->input("rent_amount-$i");

                    if ($rentMode === $paymentMode) {

                        $rentAmount = $baseAmount;
                    } else {
                        $rentAmount = calc_rent_amount($rentMode, $paymentMode, $baseAmount, $rentAmount);
                        $total_net_rent_amount = ($rentAmount * ($vat_percentage / 100)) + $rentAmount;
                        $security_deposit_amount = $rentAmount * $security_deposit;
                    }
                    $proposal_units           = (new ProposalUnits())->setConnection('tenant')->create([
                        'proposal_id'              => $proposal->id,
                        'property_id'              => $propertyId,
                        'commencement_date'        => $periodFrom ?? null,
                        'expiry_date'              => $periodTo ?? null,
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
                    $unit_management->update(['booking_status' => 'proposal', 'tenant_id' => $request->tenant_id]);
                    if (isset($request->service_counter[$i])) {
                        for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {
                            $chargeMode       = isset($request->input("charge_mode-{$i}-{$ind}")[0]) ? $request->input("charge_mode-{$i}-{$ind}")[0] :  null;
                            $chargeModeType   = isset($request->input("charge_mode_type-{$i}-{$ind}")[0]) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] :  null;
                            $amountCharge     = isset($request->input("amount_charge-{$i}-{$ind}")[0]) ? $request->input("amount_charge-{$i}-{$ind}")[0] :  null;
                            $percentageCharge = isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] :  null;
                            $calculateAmount  = isset($request->input("calculate_amount-{$i}-{$ind}")[0]) ? $request->input("calculate_amount-{$i}-{$ind}")[0] :  null;
                            $vatPercentage    = isset($request->input("vat_percentage-{$i}-{$ind}")[0]) ?  $request->input("vat_percentage-{$i}-{$ind}")[0] :  null;
                            $vatAmount        = isset($request->input("vat_amount-{$i}-{$ind}")[0]) ? $request->input("vat_amount-{$i}-{$ind}")[0]  :  null;
                            $totalAmount      = isset($request->input("total_amount-{$i}-{$ind}")[0]) ? $request->input("total_amount-{$i}-{$ind}")[0] :  null;
                            if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate     = $start ?? null;
                            $expiryDate    = $expiry ?? null;
                            if ($chargeModeType) {

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
                }
            }
            DB::commit();
            return to_route('proposal.index')->with('success', __('country.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function edit($id)
    {

        $proposal         = (new Proposal())->setConnection('tenant')->findOrFail($id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $id)->first();
        $proposal_units   = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

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
            'proposal'                 => $proposal,
            'proposal_details'         => $proposal_details,
            'proposal_units'           => $proposal_units,
            'dail_code_main'           => $dail_code_main,
        ];
        return view('admin-views.property_transactions.proposals.edit', $data);
    }
    public function show($id)
    {

        $proposal         = (new Proposal())->setConnection('tenant')->with('tenant', 'proposal_details', 'proposal_unit')->findOrFail($id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $id)->first();
        $proposal_units   = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)->get();

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
            'proposal'                 => $proposal,
            'proposal_details'         => $proposal_details,
            'proposal_units'           => $proposal_units,
        ];
        return view('admin-views.property_transactions.proposals.view', $data);
    }

    public function update(Request $request, $id)
    {
        $proposal         = (new Proposal())->setConnection('tenant')->findOrFail($id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $id)->first();
        $proposal_units   = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)->get();

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
        }
        if (count($unitValues) !== count(array_unique($unitValues))) {
            return back()->withErrors(['unit' => __('property_transactions.unit_duplicate')])->withInput();
        }
        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->proposal_date)->format('Y-m-d');
            $unit_count    = 0;
            $proposal->update([
                'proposal_no'                => $request->proposal_no ?? $proposal->proposal_no,
                'proposal_date'              => $formattedDate ?? $proposal->proposal_date,
                'tenant_id'                  => $request->tenant_id ?? $proposal->tenant_id,
                'total_no_of_required_units' => $request->total_no_of_required_units ?? $unit_count,
                'name'                       => $request->name ?? $proposal->name,
                'gender'                     => $request->gender ?? $proposal->gender,
                'id_number'                  => $request->id_number ?? $proposal->id_number,
                'registration_no'            => $request->registration_no ?? $proposal->registration_no,
                'nick_name'                  => $request->nick_name ?? $proposal->nick_name,
                'group_company_name'         => $request->group_company_name ?? $proposal->group_company_name,
                'contact_person'             => $request->contact_person ?? $proposal->contact_person,
                'designation'                => $request->designation ?? $proposal->designation,
                'contact_no'                 => $request->contact_no ?? $proposal->contact_no,
                'whatsapp_no'                => $request->whatsapp_no ?? $proposal->whatsapp_no,
                'company_name'               => $request->company_name ?? $proposal->company_name,
                'fax_no'                     => $request->fax_no ?? $proposal->fax_no,
                'telephone_no'               => $request->telephone_no ?? $proposal->telephone_no,
                'other_contact_no'           => $request->other_contact_no ?? $proposal->other_contact_no,
                'address1'                   => $request->address1 ?? $proposal->address1,
                'address2'                   => $request->address2 ?? $proposal->address2,
                'address3'                   => $request->address3 ?? $proposal->address3,
                'state'                      => $request->state ?? $proposal->state,
                'city'                       => $request->city ?? $proposal->city,
                'country_id'                 => $request->country_id ?? $proposal->country_id,
                'nationality_id'             => $request->nationality_id ?? $proposal->nationality_id,
                'passport_no'                => $request->passport_no ?? $proposal->passport_no,
                'email1'                     => $request->email1 ?? $proposal->email1,
                'email2'                     => $request->email2 ?? $proposal->email2,
                'live_with_id'               => $request->live_with_id ?? (($proposal->live_with_id) ? $proposal->live_with_id : null),
                'business_activity_id'       => $request->business_activity_id ?? (($proposal->business_activity_id) ? $proposal->business_activity_id : null),
                'status'                     => 'pending',
            ]);
            $proposal_units->each(function ($proposal_units_item) {
                $proposal_units_item->delete();
            });
            if ($proposal) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $proposal_details->update([
                    'proposal_id'                 => $proposal->id,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
                    'proposal_status_id'          => $request->enquiry_status_id ?? $proposal_details->enquiry_status_id,
                    'proposal_request_status_id'  => $request->enquiry_request_status_id ?? $proposal_details->enquiry_request_status_id,
                    'decision_maker'              => $request->decision_maker ?? $proposal_details->decision_maker,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? $proposal_details->decision_maker_designation,
                    'current_office_location'     => $request->current_office_location ?? $proposal_details->current_office_location,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? $proposal_details->reason_of_relocation,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? $proposal_details->budget_for_relocation_start,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? $proposal_details->budget_for_relocation_end,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? $proposal_details->no_of_emp_staff_strength,
                    'time_frame_for_relocation'   => $request->time_frame_for_relocation ?? $proposal_details->time_frame_for_relocation,
                    'relocation_date'             => $relocation_date ?? $proposal_details->relocation_date,
                    'period_from'                 => $period_from ?? $proposal_details->period_from,
                    'period_to'                   => $period_to ?? $proposal_details->period_to,
                ]);
                foreach ($request->city_id as $key => $city_value) {
                    $propertyId        = $request->input("property_id-$key");
                    $unitDescriptionId = $request->input("unit_description_id-$key");
                    $unitTypeId        = $request->input("unit_type_id-$key");
                    $unitConditionId   = $request->input("unit_condition_id-$key");
                    $viewId            = $request->input("view_id-$key");
                    $propertyType      = $request->input("property_type-$key");
                    if ($request->input("period_from-$key")) {
                        $periodFrom = Carbon::createFromFormat('d/m/Y', $request->input("period_from-$key"))->format('Y-m-d');
                    }
                    if ($request->input("period_to-$key")) {
                        $periodTo = Carbon::createFromFormat('d/m/Y', $request->input("period_to-$key"))->format('Y-m-d');
                    }
                    $totalArea                = $request->input("total_area-$key");
                    $areaMeasurement          = $request->input("area_measurement-$key");
                    $notes                    = $request->input("notes-$key");
                    $unit                     = $request->input("unit-$key");
                    $paymentMode              = $request->input("payment_mode-$key");
                    $pdc                      = $request->input("pdc-$key");
                    $totalAreaAmount          = $request->input("total_area_amount-$key");
                    $amount                   = $request->input("amount-$key");
                    $rentAmount               = $request->input("rent_amount-$key");
                    $rentMode                 = $request->input("rent_mode-$key");
                    $rentalGl                 = $request->input("rental_gl-$key");
                    if ($request->input("lease_break_date-$key")) {
                        $lease_break_date_format = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$key"))->format('Y-m-d');
                    }
                    $lease_break_date         = $lease_break_date_format ?? null;                    // $lease_break_date         = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$key"))->format('Y-m-d');
                    $lease_break_comment      = $request->input("lease_break_comments-$key");
                    $total_net_rent_amount    = $request->input("total_net_rent_amount-$key") ?? 0;
                    $vat_percentage           = $request->input("vat_percentage-$key");
                    $vat_amount               = $request->input("vat_amount-$key");
                    $security_deposit         = $request->input("security_deposit_months_rent-$key");
                    $security_deposit_amount  = $request->input("security_deposit_amount-$key");
                    $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$key");
                    $ewa_limit_mode           = $request->input("ewa_limit_mode-$key");
                    $ewa_limit                = $request->input("ewa_limit-$key");
                    $notice_period            = $request->input("notice_period-$key");
                    $proposal_units           = (new ProposalUnits())->setConnection('tenant')->create([
                        'proposal_id'              => $proposal->id,
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
                        'lease_break_date'         => $lease_break_date,
                        'security_deposit'         => $security_deposit,
                        'security_deposit_amount'  => $security_deposit_amount,
                        'is_rent_inclusive_of_ewa' => $is_rent_inclusive_of_ewa,
                        'ewa_limit_mode'           => $ewa_limit_mode,
                        'ewa_limit'                => $ewa_limit,
                        'notice_period'                => $notice_period,
                        'total'                    => 0,
                    ]);
                    $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                    $unit_management->update(['booking_status' => 'proposal']);
                    if (isset($request->service_counter[$key])) {
                        for ($ind = 1, $inde = $request->service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = (isset($request->input("charge_mode-{$key}-{$ind}")[0])) ? $request->input("charge_mode-{$key}-{$ind}")[0] : null;
                            $chargeModeType   = (isset($request->input("charge_mode_type-{$key}-{$ind}")[0])) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] : null;
                            $amountCharge     = (isset($request->input("amount_charge-{$key}-{$ind}")[0])) ? $request->input("amount_charge-{$key}-{$ind}")[0] : null;
                            $percentageCharge = (isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] : null;
                            $calculateAmount  = (isset($request->input("calculate_amount-{$key}-{$ind}")[0])) ? $request->input("calculate_amount-{$key}-{$ind}")[0] : null;
                            $vatPercentage    = (isset($request->input("vat_percentage-{$key}-{$ind}")[0])) ? $request->input("vat_percentage-{$key}-{$ind}")[0] : null;
                            $vatAmount        = (isset($request->input("vat_amount-{$key}-{$ind}")[0])) ? $request->input("vat_amount-{$key}-{$ind}")[0] : null;
                            $totalAmount      = (isset($request->input("total_amount-{$key}-{$ind}")[0])) ?  $request->input("total_amount-{$key}-{$ind}")[0] : null;

                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate     = $start ?? null;
                            $expiryDate    = $expiry ?? null;
                            if ($chargeModeType) {
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
                    if (! isset($request->service_counter[$key]) && isset($request->old_service_counter[$key])) {
                        for ($ind = 1, $inde = $request->old_service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = isset($request->input("charge_mode-{$key}-{$ind}")[0]) ? $request->input("charge_mode-{$key}-{$ind}")[0] :  null;
                            $chargeModeType   = isset($request->input("charge_mode_type-{$key}-{$ind}")[0]) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] :  null;
                            $amountCharge     = isset($request->input("amount_charge-{$key}-{$ind}")[0]) ? $request->input("amount_charge-{$key}-{$ind}")[0] :  null;
                            $percentageCharge = isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] :  null;
                            $calculateAmount  = isset($request->input("calculate_amount-{$key}-{$ind}")[0]) ? $request->input("calculate_amount-{$key}-{$ind}")[0] :  null;
                            $vatPercentage    = isset($request->input("vat_percentage-{$key}-{$ind}")[0]) ? $request->input("vat_percentage-{$key}-{$ind}")[0] :  null;
                            $vatAmount        = isset($request->input("vat_amount-{$key}-{$ind}")[0]) ? $request->input("vat_amount-{$key}-{$ind}")[0] :  null;
                            $totalAmount      = isset($request->input("total_amount-{$key}-{$ind}")[0]) ? $request->input("total_amount-{$key}-{$ind}")[0] :  null;

                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate     = $start ?? null;
                            $expiryDate    = $expiry ?? null;
                            if ($chargeModeType) {

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
                }
            }
            DB::commit();
            return to_route('proposal.index')->with('success', __('general.updated_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function add_to_booking($id)
    {

        $proposal         = (new Proposal())->setConnection('tenant')->findOrFail($id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $id)->first();
        $proposal_units   = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)->get();
        
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
            'booking'                  => $proposal,
            'booking_details'          => $proposal_details,
            'booking_units'            => $proposal_units, 
        ];
        return view('admin-views.property_transactions.proposals.add_to_booking', $data);
    }

    public function add_to_agreement($id)
    {

        $proposal         = (new Proposal())->setConnection('tenant')->findOrFail($id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $id)->first();
        $proposal_units   = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)->get();

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
            'agreement'                => $proposal,
            'agreement_details'        => $proposal_details,
            'agreement_units'          => $proposal_units,
        ];
        return view('admin-views.property_transactions.proposals.add_to_agreement', $data);
    }

    public function delete(Request $request)
    {
        $proposal         = (new Proposal())->setConnection('tenant')->findOrFail($request->id);
        $proposal_details = (new ProposalDetails())->setConnection('tenant')->where('proposal_id', $request->id)->first();
        if ($proposal_details) {
            $proposal_details->delete();
        }
        $proposal_units    = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $request->id)->get();
        $unitManagementIds = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $request->id)
            ->pluck('unit_id')
            ->toArray();
        if (! empty($proposal_units)) {
            (new UnitManagement())->setConnection('tenant')->whereIn('id', $unitManagementIds)
                ->update(['booking_status' => 'empty']);
        }
        foreach ($proposal_units as $unit) {
            (new ProposalUnitsService())->setConnection('tenant')->where('proposal_unit_id', $unit->id)->delete();
            $unit->delete();
        }
        $proposal->delete();

        return to_route('proposal.index')->with('success', __('general.deleted_successfully'));
    }

    public function check_property($id = 0)
    {
        $proposal = (new Proposal())->setConnection('tenant')->findOrFail($id);
        // $proposal = 
        $unit_ids = (new ProposalUnits())->setConnection('tenant')->where('proposal_id', $id)
            ->pluck('unit_id')
            ->toArray();
        $units        = (new UnitManagement())->setConnection('tenant')->whereIn('id', $unit_ids)->get();
        $property_ids = $units->pluck('property_management_id')->toArray();
        $property     = (new PropertyManagement())->setConnection('tenant')->whereIn('id', $property_ids)->get();
        if ($property->isEmpty()) {
            $property = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        }
        $data = [
            'properties' => $property,
        ];
        return view('admin-views.property_transactions.proposals.check_property', $data);
    }
    public function view_image($id, $proposal_id)
    {
        $proposal_unit = ProposalUnits::where('proposal_id', $proposal_id)->pluck('id', 'unit_id')->toArray();
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
            'proposal_unit' => $proposal_unit,
        ];
        return view('admin-views.property_transactions.proposals.view_image', $data);
    }
    public function list_view($id, $proposal_id)
    {
        $proposal_unit = ProposalUnits::where('proposal_id', $proposal_id)->pluck('id', 'unit_id')->toArray();

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
            'proposal_unit' => $proposal_unit,
        ];
        return view('admin-views.property_transactions.proposals.list_view', $data);
    }

    public function store_to_booking(TransactionsRequest $request)
    {
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d');
            $tenant        = (new Tenant())->setConnection('tenant')->find($request->tenant_id);
            $proposal      = (new Proposal())->setConnection('tenant')->find($request->proposal_id);
            $booking       = (new Booking())->setConnection('tenant')->create([
                'booking_no'                 => $request->booking_no,
                'booking_date'               => $formattedDate ?? Carbon::now(),
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
                'booking_status'             => 'booking',
            ]);
            $proposal->update([
                'status'         => 'completed',
                'booking_status' => 'booking',
            ]);
            if ($booking) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $booking_details                                       = (new BookingDetails())->setConnection('tenant')->create([
                    'booking_id'                  => $booking->id ?? null,
                    'employee_id'                 => (! empty($request->employee_id)) ? $request->employee_id : null,
                    'agent_id'                    => (! empty($request->agent_id)) ? $request->agent_id : null,
                    'booking_status_id'           => $request->enquiry_status_id ?? null,
                    'booking_request_status_id'   => $request->enquiry_request_status_id ?? null,
                    'decision_maker'              => $request->decision_maker ?? null,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? null,
                    'current_office_location'     => $request->current_office_location ?? null,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? null,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? null,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? null,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? null,
                    'time_frame_for_relocation'   => $request->time_frame_for_relocation ? Carbon::createFromFormat('d/m/Y', $request->time_frame_for_relocation)->format('Y-m-d') : null,
                    'relocation_date'             => $relocation_date,
                    'period_from'                 => $period_from ?? null,
                    'period_to'                   => $period_to ?? null,
                ]);
                if (isset($request->unit_ids)) {
                    foreach ($request->unit_ids as $i) {
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
                        $city                     = $request->input("city-$i");
                        $totalArea                = $request->input("total_area-$i");
                        $areaMeasurement          = $request->input("area_measurement-$i");
                        $notes                    = $request->input("notes-$i");
                        $unit                     = $request->input("unit-$i");
                        $paymentMode              = $request->input("payment_mode-$i");
                        $pdc                      = $request->input("pdc-$i");
                        $totalAreaAmount          = $request->input("total_area_amount-$i");
                        $amount                   = $request->input("amount-$i");
                        $rentAmount               = $request->input("rent_amount-$i");
                        $rentMode                 = $request->input("rent_mode-$i");
                        $rentalGl                 = $request->input("rental_gl-$i");
                        if ($request->input("lease_break_date-$i")) {
                            $lease_break_date_format = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$i"))->format('Y-m-d');
                        }
                        $lease_break_date         = $lease_break_date_format ?? null;
                        // $lease_break_date         = $request->input("lease_break_date-$i") ? Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$i"))->format('Y-m-d') : null;
                        $lease_break_comment      = $request->input("lease_break_comment-$i");
                        $total_net_rent_amount    = $request->input("total_net_rent_amount-$i");
                        $vat_percentage           = $request->input("vat_percentage-$i");
                        $vat_amount               = $request->input("vat_amount-$i");
                        $security_deposit         = $request->input("security_deposit_months_rent-$i");
                        $security_deposit_amount  = $request->input("security_deposit_amount-$i");
                        $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$i");
                        $ewa_limit_mode           = $request->input("ewa_limit_mode-$i");
                        $ewa_limit                = $request->input("ewa_limit-$i");
                        $notice_period            = $request->input("notice_period-$i");
                        $baseAmount  = (float)$request->input("rent_amount-$i");

                        // if ($rentMode === $paymentMode) {

                        //     $rentAmount = $baseAmount;
                        // } else {
                        //     $rentAmount = calc_rent_amount($rentMode, $paymentMode, $baseAmount, $rentAmount);
                        //     $total_net_rent_amount = ($rentAmount * ($vat_percentage / 100 )) + $rentAmount;
                        //     $security_deposit_amount = $rentAmount * $security_deposit;
                        // }
                        $booking_units            = (new BookingUnits())->setConnection('tenant')->create([
                            'booking_id'               => $booking->id,
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
                            'notice_period'             => $notice_period,
                            'total'                    => 0,
                        ]);
                        $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                        $unit_management->update(['booking_status' => 'booking',  'tenant_id' => $request->tenant_id]);
                        if (isset($request->service_counter[$i])) {
                            for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {

                                $chargeMode       = (isset($request->input("charge_mode-{$i}-{$ind}")[0]))  ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                                $chargeModeType   = (isset($request->input("charge_mode_type-{$i}-{$ind}")[0]))  ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                                $amountCharge     = (isset($request->input("amount_charge-{$i}-{$ind}")[0]))  ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                                $percentageCharge = (isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0]))  ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                                $calculateAmount  = (isset($request->input("calculate_amount-{$i}-{$ind}")[0]))  ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate     = $start ?? null;
                                $expiryDate    = $expiry ?? null;
                                $vatPercentage = (isset($request->input("vat_percentage-{$i}-{$ind}")[0]))  ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                                $vatAmount     = (isset($request->input("vat_amount-{$i}-{$ind}")[0]))  ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                                $totalAmount   = (isset($request->input("total_amount-{$i}-{$ind}")[0]))  ? $request->input("total_amount-{$i}-{$ind}")[0] : null;
                                if ($chargeModeType) {
                                    DB::connection('tenant')->table('booking_units_services')->insert([
                                        'booking_unit_id'   => $booking_units->id,
                                        'charge_mode'       => $chargeModeType,
                                        'other_charge_type' => $chargeMode,
                                        'amount'            => $calculateAmount,
                                        'vat'               => $vatAmount,
                                        'total'             => $totalAmount,
                                    ]);
                                }
                            }
                        } elseif (! isset($request->service_counter[$i]) && isset($request->old_service_counter[$i])) {
                            for ($ind = 1, $inde = $request->old_service_counter[$i]; $ind <= $inde; $ind++) {
                                $chargeMode       = (isset($request->input("charge_mode-{$i}-{$ind}")[0])) ? $request->input("charge_mode-{$i}-{$ind}")[0] :  null;
                                $chargeModeType   = (isset($request->input("charge_mode_type-{$i}-{$ind}")[0])) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] :  null;
                                $amountCharge     = (isset($request->input("amount_charge-{$i}-{$ind}")[0])) ? $request->input("amount_charge-{$i}-{$ind}")[0] :  null;
                                $percentageCharge = (isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0]  :  null;
                                $calculateAmount  = (isset($request->input("calculate_amount-{$i}-{$ind}")[0])) ? $request->input("calculate_amount-{$i}-{$ind}")[0] :  null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate     = $start ?? null;
                                $expiryDate    = $expiry ?? null;
                                $vatPercentage = (isset($request->input("vat_percentage-{$i}-{$ind}")[0])) ? $request->input("vat_percentage-{$i}-{$ind}")[0] :  null;
                                $vatAmount     = (isset($request->input("vat_amount-{$i}-{$ind}")[0])) ? $request->input("vat_amount-{$i}-{$ind}")[0] :  null;
                                $totalAmount   = (isset($request->input("total_amount-{$i}-{$ind}")[0])) ? $request->input("total_amount-{$i}-{$ind}")[0] :  null;
                                if ($chargeModeType) {
                                    DB::connection('tenant')->table('booking_units_services')->insert([
                                        'booking_unit_id'   => $booking_units->id,
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
            }
            DB::commit();
            return to_route('booking.index')->with('success', __('country.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function store_to_agreement(TransactionsRequest $request)
    {
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->agreement_date)->format('Y-m-d');
            $tenant        = (new Tenant())->setConnection('tenant')->find($request->tenant_id);
            $proposal      = (new Proposal())->setConnection('tenant')->find($request->proposal_id);
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
            $proposal->update([
                'status'         => 'completed',
                'booking_status' => 'agreement',
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
                if (isset($request->unit_ids)) {
                    foreach ($request->unit_ids as $i) {
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
                        $baseAmount      = $request->input("rent_amount-$i");
                        $rentMode        = $request->input("rent_mode-$i");
                        $rentalGl        = $request->input("rental_gl-$i");
                        if ($request->input("lease_break_date-$i")) {
                            $lease_break_date = Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$i"))->format('Y-m-d');
                        }
                        $lease_break_comment      = $request->input("lease_break_comment-$i");
                        $total_net_rent_amount    = $request->input("total_net_rent_amount-$i");
                        $vat_percentage           = $request->input("vat_percentage-$i");
                        $vat_amount               = $request->input("vat_amount-$i");
                        $security_deposit         = $request->input("security_deposit_months_rent-$i");
                        $security_deposit_amount  = $request->input("security_deposit_amount-$i");
                        $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$i");
                        $ewa_limit_mode           = $request->input("ewa_limit_mode-$i");
                        $ewa_limit                = $request->input("ewa_limit_monthly-$i");
                        $notice_period            = $request->input("notice_period-$i");

                        // if ($rentMode === $paymentMode) {

                        //     $rentAmount = $baseAmount;
                        // } else {
                        //     $rentAmount = calc_rent_amount($rentMode, $paymentMode, $baseAmount, $rentAmount);
                        //     $total_net_rent_amount = ($rentAmount * ($vat_percentage / 100)) + $rentAmount;
                        //     $security_deposit_amount = $rentAmount * $security_deposit;
                        // }
                        $agreement_units          = (new AgreementUnits())->setConnection('tenant')->create([
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
                            'notice_period'             => $notice_period,
                            'total'                    => 0,
                        ]);
                        $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $unit)->first();
                        $unit_management->update(['booking_status' => 'agreement',  'tenant_id' => $request->tenant_id]);
                        if (isset($request->service_counter[$i])) {
                            for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {
                                $chargeMode       = (isset($request->input("charge_mode-{$i}-{$ind}")[0])) ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                                $chargeModeType   = (isset($request->input("charge_mode_type-{$i}-{$ind}")[0])) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                                $amountCharge     = (isset($request->input("amount_charge-{$i}-{$ind}")[0])) ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                                $percentageCharge = (isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                                $calculateAmount  = (isset($request->input("calculate_amount-{$i}-{$ind}")[0])) ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate     = $start ?? null;
                                $expiryDate    = $expiry ?? null;
                                $vatPercentage = (isset($request->input("vat_percentage-{$i}-{$ind}")[0])) ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                                $vatAmount     = (isset($request->input("vat_amount-{$i}-{$ind}")[0])) ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                                $totalAmount   = (isset($request->input("total_amount-{$i}-{$ind}")[0])) ? $request->input("total_amount-{$i}-{$ind}")[0] : null;
                                if ($chargeModeType) {
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
                        } elseif (! isset($request->service_counter[$i]) && isset($request->old_service_counter[$i])) {
                            for ($ind = 1, $inde = $request->old_service_counter[$i]; $ind <= $inde; $ind++) {
                                $chargeMode       = (isset($request->input("charge_mode-{$i}-{$ind}")[0])) ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                                $chargeModeType   = (isset($request->input("charge_mode_type-{$i}-{$ind}")[0])) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                                $amountCharge     = (isset($request->input("amount_charge-{$i}-{$ind}")[0])) ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                                $percentageCharge = (isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0])) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                                $calculateAmount  = (isset($request->input("calculate_amount-{$i}-{$ind}")[0])) ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate     = $start ?? null;
                                $expiryDate    = $expiry ?? null;
                                $vatPercentage = (isset($request->input("vat_percentage-{$i}-{$ind}")[0])) ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                                $vatAmount     = (isset($request->input("vat_amount-{$i}-{$ind}")[0])) ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                                $totalAmount   = (isset($request->input("total_amount-{$i}-{$ind}")[0])) ? $request->input("total_amount-{$i}-{$ind}")[0] : null;
                                if ($chargeModeType) {
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
            }
            DB::commit();
            return to_route('agreement.index')->with('success', __('general.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function empty_unit_from_proposal_unit($id)
    {
        $proposal_unit   = (new ProposalUnits())->setConnection('tenant')->select('id', 'unit_id')->where('id', $id)->first();
        $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $proposal_unit->unit_id)->first();
        $unit_management->update([
            'booking_status' => 'empty',
        ]);
        $proposal_unit->delete();
        return response()->json([
            'status'  => 200,
            'success' => true,
        ]);
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
        $all_units                = (new UnitManagement())->setConnection('tenant')->select('id', 'property_management_id', 'booking_status', 'view_id', 'unit_type_id', 'unit_condition_id', 'unit_description_id', 'unit_id', 'block_management_id', 'floor_management_id')->whereIn('id', $ids)
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
        $live_withs          = (new LiveWith())->setConnection('tenant')->select('id', 'name')->lazy();
        $business_activities = (new BusinessActivity())->setConnection('tenant')->select('id', 'name')->lazy();
        $buildings           = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name')->lazy();
        $unit_descriptions   = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_conditions     = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->lazy();
        $unit_types          = (new UnitType())->setConnection('tenant')->select('id', 'name')->lazy();
        $views               = (new View())->setConnection('tenant')->select('id', 'name')->lazy();
        $property_types      = (new PropertyType())->setConnection('tenant')->select('id', 'name')->lazy();
        $services_master      =  (new ServiceMaster())->setConnection('tenant')->select('id', 'name')->lazy();
             $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'dail_code_main'            => $dail_code_main,
            'services_master'                => $services_master,
            'tenant_id'                => $tenant_id,
            'tenant'                   => $tenant,
            'proposal_units'           => $all_units,
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
        return view('admin-views.property_transactions.proposals.create_with_select_unit', $data);
    }
    public function empty_unit_from_service_proposal($id)
    {
        $deleted = DB::connection('tenant')->table('proposal_units_services')->where('id', $id)->delete();

        return response()->json([
            'status'  => 200,
            'success' => $deleted > 0,
        ]);
    }
}
