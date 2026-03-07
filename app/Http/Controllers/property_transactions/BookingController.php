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
use App\Models\BookingUnitsService;
use App\Models\BusinessActivity;
use App\Models\CountryMaster;
use App\Models\Employee;
use App\Models\EnquiryRequestStatus;
use App\Models\EnquiryStatus;
use App\Models\LiveWith;
use App\Models\PropertyManagement;
use App\Models\PropertyType;
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

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('booking');
        $ids     = $request->bulk_ids;
        // $lastRun = Cache::get('last_booking_expiry_run');
        // if (! $lastRun || now()->diffInHours($lastRun) >= 24) {
        $booking_settings = get_business_settings('booking')->where('type', 'booking_expire_date')->first();
        $expiry_days      = $booking_settings ? (int) $booking_settings->value : 0;
        if ($expiry_days > 0) {
            expire_unit($expiry_days, 'Booking', 'BookingUnits');
            // Cache::put('last_booking_expiry_run', now(), now()->addDay());
        }
        // }

        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids) && isset($request->status)) {
            $data = ['status' => $request->status];

            $this->booking_update_status($data, $ids);
            return back()->with('success', ui_change('updated_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $bookings    = (new Booking())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('booking_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->where('status', 'pending')
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = (new Booking())->setConnection('tenant')->query();
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
            $bookings = $report_query->orderBy('created_at', 'desc')->paginate();
        }

        $data = [
            'bookings' => $bookings,
            'search'   => $search,

        ];
        return view("admin-views.property_transactions.bookings.booking_list", $data);
    }

    public function create()
    {
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
        return view('admin-views.property_transactions.bookings.create', $data);
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
    public function store(TransactionsRequest $request)
    {
        // dd($request->all());
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
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d');
            $tenant        = (new Tenant())->setConnection('tenant')->find($request->tenant_id);
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

            if ($booking) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $booking_details                                       = (new BookingDetails())->setConnection('tenant')->create([
                    'booking_id'                  => $booking->id ?? null,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
                    'booking_status_id'           => $request->enquiry_status_id ?? null,
                    'booking_request_status_id'   => $request->enquiry_request_status_id ?? null,
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
                    // $lease_break_date         = $request->input("lease_break_date-$i");
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
                    $booking_units = (new BookingUnits())->setConnection('tenant')->create([
                        'booking_id'               => $booking->id,
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
                    $unit_management->update(['booking_status' => 'booking',  'tenant_id' => $request->tenant_id]);
                    if (isset($request->service_counter[$i])) {
                        for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {
                            $chargeMode       = isset($request->input("charge_mode-{$i}-{$ind}")[0]) ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                            $chargeModeType   = isset($request->input("charge_mode_type-{$i}-{$ind}")[0]) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                            $amountCharge     = isset($request->input("amount_charge-{$i}-{$ind}")[0]) ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                            $percentageCharge = isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                            $calculateAmount  = isset($request->input("calculate_amount-{$i}-{$ind}")[0]) ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                            $vatPercentage    = isset($request->input("vat_percentage-{$i}-{$ind}")[0]) ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                            $vatAmount        = isset($request->input("vat_amount-{$i}-{$ind}")[0]) ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                            $totalAmount      = isset($request->input("total_amount-{$i}-{$ind}")[0]) ? $request->input("total_amount-{$i}-{$ind}")[0] : null;

                            if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate  = $start ?? null;
                            $expiryDate = $expiry ?? null;
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

            // }
            DB::commit();
            return to_route('booking.index')->with('success', __('country.added_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $booking         = (new Booking())->setConnection('tenant')->findOrFail($id);
        $booking_details = (new BookingDetails())->setConnection('tenant')->where('booking_id', $id)->first();
        $booking_units   = (new BookingUnits())->setConnection('tenant')->where('booking_id', $id)->get();

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
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d');
            $unit_count    = 0;
            $booking->update([
                'booking_no'                 => $request->booking_no ?? $booking->booking_no,
                'booking_date'               => $formattedDate ?? $booking->booking_date,
                'tenant_id'                  => $request->tenant_id ?? $booking->tenant_id,
                'total_no_of_required_units' => $request->total_no_of_required_units ?? $unit_count,
                'name'                       => $request->name ?? $booking->name,
                'gender'                     => $request->gender ?? $booking->gender,
                'id_number'                  => $request->id_number ?? $booking->id_number,
                'registration_no'            => $request->registration_no ?? $booking->registration_no,
                'nick_name'                  => $request->nick_name ?? $booking->nick_name,
                'group_company_name'         => $request->group_company_name ?? $booking->group_company_name,
                'contact_person'             => $request->contact_person ?? $booking->contact_person,
                'designation'                => $request->designation ?? $booking->designation,
                'contact_no'                 => $request->contact_no ?? $booking->contact_no,
                'whatsapp_no'                => $request->whatsapp_no ?? $booking->whatsapp_no,
                'company_name'               => $request->company_name ?? $booking->company_name,
                'fax_no'                     => $request->fax_no ?? $booking->fax_no,
                'telephone_no'               => $request->telephone_no ?? $booking->telephone_no,
                'other_contact_no'           => $request->other_contact_no ?? $booking->other_contact_no,
                'address1'                   => $request->address1 ?? $booking->address1,
                'address2'                   => $request->address2 ?? $booking->address2,
                'address3'                   => $request->address3 ?? $booking->address3,
                'state'                      => $request->state ?? $booking->state,
                'city'                       => $request->city ?? $booking->city,
                'country_id'                 => $request->country_id ?? $booking->country_id,
                'nationality_id'             => $request->nationality_id ?? $booking->nationality_id,
                'passport_no'                => $request->passport_no ?? $booking->passport_no,
                'email1'                     => $request->email1 ?? $booking->email1,
                'email2'                     => $request->email2 ?? $booking->email2,
                'live_with_id'               => $request->live_with_id ?? (($booking->live_with_id) ? $booking->live_with_id : null),
                'business_activity_id'       => $request->business_activity_id ?? (($booking->business_activity_id) ? $booking->business_activity_id : null),
                'status'                     => 'pending',
            ]);
            // $services_count = $booking_units->sum(function ($booking_units_item) {
            //     return DB::connection('tenant')
            //         ->table('booking_units_services')
            //         ->where('unit_id', $booking_units_item->id)
            //         ->count();
            // });
            // dd();
            $booking_units->each(function ($booking_units_item) {
                $booking_units_item->delete();
            });
            if ($booking) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $booking_details->update([
                    'booking_id'                  => $booking->id,
                    'employee_id'                 => (! empty($request->employee_id)) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
                    'booking_status_id'           => $request->enquiry_status_id ?? $booking_details->enquiry_status_id,
                    'booking_request_status_id'   => $request->enquiry_request_status_id ?? $booking_details->enquiry_request_status_id,
                    'decision_maker'              => $request->decision_maker ?? $booking_details->decision_maker,
                    'decision_maker_designation'  => $request->decision_maker_designation ?? $booking_details->decision_maker_designation,
                    'current_office_location'     => $request->current_office_location ?? $booking_details->current_office_location,
                    'reason_of_relocation'        => $request->reason_of_relocation ?? $booking_details->reason_of_relocation,
                    'budget_for_relocation_start' => $request->budget_for_relocation_start ?? $booking_details->budget_for_relocation_start,
                    'budget_for_relocation_end'   => $request->budget_for_relocation_end ?? $booking_details->budget_for_relocation_end,
                    'no_of_emp_staff_strength'    => $request->no_of_emp_staff_strength ?? $booking_details->no_of_emp_staff_strength,
                    'time_frame_for_relocation'   => $request->time_frame_for_relocation ?? $booking_details->time_frame_for_relocation,
                    'relocation_date'             => $relocation_date ?? $booking_details->relocation_date,
                    'period_from'                 => $period_from ?? $booking_details->period_from,
                    'period_to'                   => $period_to ?? $booking_details->period_to,
                ]);
                foreach ($request->city_id as $key => $city_value) {
                    $propertyId               = $request->input("property_id-$key");
                    $unitDescriptionId        = $request->input("unit_description_id-$key");
                    $unitTypeId               = $request->input("unit_type_id-$key");
                    $unitConditionId          = $request->input("unit_condition_id-$key");
                    $viewId                   = $request->input("view_id-$key");
                    $propertyType             = $request->input("property_type-$key");
                    $periodFrom               = Carbon::createFromFormat('d/m/Y', $request->input("period_from-$key"))->format('Y-m-d') ?? null;
                    $periodTo                 = Carbon::createFromFormat('d/m/Y', $request->input("period_to-$key"))->format('Y-m-d') ?? null;
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
                    $lease_break_date         = ($request->input("lease_break_date-$key")) ? Carbon::createFromFormat('d/m/Y', $request->input("lease_break_date-$key"))->format('Y-m-d') : null;
                    $lease_break_comment      = $request->input("lease_break_comment-$key");
                    $total_net_rent_amount    = $request->input("total_net_rent_amount-$key") ?? 0;
                    $vat_percentage           = $request->input("vat_percentage-$key");
                    $vat_amount               = $request->input("vat_amount-$key");
                    $security_deposit         = $request->input("security_deposit_months_rent-$key");
                    $security_deposit_amount  = $request->input("security_deposit_amount-$key");
                    $is_rent_inclusive_of_ewa = $request->input("is_rent_inclusive_of_ewa-$key");
                    $ewa_limit_mode           = $request->input("ewa_limit_mode-$key");
                    $ewa_limit                = $request->input("ewa_limit-$key");
                    $notice_period            = $request->input("notice_period-$key");
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
                    $unit_management->update(['booking_status' => 'booking']);
                    // if($services_count < $request->service_counter[$key] || $services_count < $request->old_service_counter[$key]){
                    //     $service_counter =
                    // }
                    if (isset($request->service_counter[$key])) {
                        for ($ind = 1, $inde = $request->service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = isset($request->input("charge_mode-{$key}-{$ind}")[0]) ? $request->input("charge_mode-{$key}-{$ind}")[0] : null;
                            $chargeModeType   = isset($request->input("charge_mode_type-{$key}-{$ind}")[0]) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] : null;
                            $amountCharge     = isset($request->input("amount_charge-{$key}-{$ind}")[0]) ? $request->input("amount_charge-{$key}-{$ind}")[0] : null;
                            $percentageCharge = isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] : null;
                            $calculateAmount  = isset($request->input("calculate_amount-{$key}-{$ind}")[0]) ? $request->input("calculate_amount-{$key}-{$ind}")[0] : null;
                            $vatPercentage    = isset($request->input("vat_percentage-{$key}-{$ind}")[0]) ? $request->input("vat_percentage-{$key}-{$ind}")[0] : null;
                            $vatAmount        = isset($request->input("vat_amount-{$key}-{$ind}")[0]) ? $request->input("vat_amount-{$key}-{$ind}")[0] : null;
                            $totalAmount      = isset($request->input("total_amount-{$key}-{$ind}")[0]) ? $request->input("total_amount-{$key}-{$ind}")[0] : null;

                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate  = $start ?? null;
                            $expiryDate = $expiry ?? null;
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
                    if (! isset($request->service_counter[$key]) && isset($request->old_service_counter[$key])) {
                        for ($ind = 1, $inde = $request->old_service_counter[$key]; $ind <= $inde; $ind++) {
                            $chargeMode       = isset($request->input("charge_mode-{$key}-{$ind}")[0]) ? $request->input("charge_mode-{$key}-{$ind}")[0] : null;
                            $chargeModeType   = isset($request->input("charge_mode_type-{$key}-{$ind}")[0]) ? $request->input("charge_mode_type-{$key}-{$ind}")[0] : null;
                            $amountCharge     = isset($request->input("amount_charge-{$key}-{$ind}")[0]) ? $request->input("amount_charge-{$key}-{$ind}")[0] : null;
                            $percentageCharge = isset($request->input("percentage_amount_charge-{$key}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$key}-{$ind}")[0] : null;
                            $calculateAmount  = isset($request->input("calculate_amount-{$key}-{$ind}")[0]) ? $request->input("calculate_amount-{$key}-{$ind}")[0] : null;

                            $vatPercentage = isset($request->input("vat_percentage-{$key}-{$ind}")[0]) ? $request->input("vat_percentage-{$key}-{$ind}")[0] : null;
                            $vatAmount     = isset($request->input("vat_amount-{$key}-{$ind}")[0]) ? $request->input("vat_amount-{$key}-{$ind}")[0] : null;
                            $totalAmount   = isset($request->input("total_amount-{$key}-{$ind}")[0]) ? $request->input("total_amount-{$key}-{$ind}")[0] : null;
                            if (isset($request->input("start_date-{$key}-{$ind}")[0])) {
                                $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            if (isset($request->input("expiry_date-{$key}-{$ind}")[0])) {
                                $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$key}-{$ind}")[0])->format('Y-m-d');
                            }
                            $startDate  = $start ?? null;
                            $expiryDate = $expiry ?? null;
                            if ($chargeModeType != null) {
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
            DB::commit();
            return to_route('booking.index')->with('success', __('general.updated_successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function edit($id)
    {

        $booking                  = (new Booking())->setConnection('tenant')->findOrFail($id);
        $booking_details          = (new BookingDetails())->setConnection('tenant')->where('booking_id', $id)->first();
        $booking_units            = (new BookingUnits())->setConnection('tenant')->where('booking_id', $id)->get();
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
            'booking'                  => $booking,
            'booking_details'          => $booking_details,
            'booking_units'            => $booking_units,
        ];
        return view('admin-views.property_transactions.bookings.edit', $data);
    }

    public function delete(Request $request)
    {
        $booking         = (new Booking())->setConnection('tenant')->findOrFail($request->id);
        $booking_details = (new BookingDetails())->setConnection('tenant')->where('booking_id', $request->id)->first();
        if ($booking_details) {
            $booking_details->delete();
        }
        $booking_units     = (new BookingUnits())->setConnection('tenant')->where('booking_id', $request->id)->get();
        $unitManagementIds = (new BookingUnits())->setConnection('tenant')->where('booking_id', $request->id)
            ->pluck('unit_id')
            ->toArray();
        if (! empty($booking_units)) {
            (new UnitManagement())->setConnection('tenant')->whereIn('id', $unitManagementIds)
                ->update(['booking_status' => 'empty']);
        }

        foreach ($booking_units as $unit) {
            BookingUnitsService::where('booking_unit_id', $unit->id)->delete();
            $unit->delete();
        }
        $booking->delete();

        return to_route('booking.index')->with('success', __('general.deleted_successfully'));
    }

    public function add_to_agreement($id)
    {

        $booking         = (new Booking())->setConnection('tenant')->findOrFail($id);
        $booking_details = (new BookingDetails())->setConnection('tenant')->where('booking_id', $id)->first();
        $booking_units   = (new BookingUnits())->setConnection('tenant')->where('booking_id', $id)->get();

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
            'agreement'                => $booking,
            'agreement_details'        => $booking_details,
            'agreement_units'          => $booking_units,
        ]; // add_to_agreement.blade
        return view('admin-views.property_transactions.bookings.add_to_agreement', $data);
    }
    public function check_property($id = 0)
    {
        $booking  = (new Booking())->setConnection('tenant')->findOrFail($id);
        $unit_ids = (new BookingUnits())->setConnection('tenant')->where('booking_id', $id)
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
        return view('admin-views.property_transactions.bookings.check_property', $data);
    }
    public function view_image($id, $booking_id)
    {
        $booking_unit = BookingUnits::where('booking_id', $booking_id)->pluck('id', 'unit_id')->toArray();

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
            'booking_unit'  => $booking_unit,
        ];
        return view('admin-views.property_transactions.bookings.view_image', $data);
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
        // $property = PropertyManagement::findOrFail($id);
        $data = [
            'property_item' => $property,
        ];
        return view('admin-views.property_transactions.bookings.list_view', $data);
    }

    public function store_to_agreement(TransactionsRequest $request)
    {
        DB::beginTransaction();
        try {
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->agreement_date)->format('Y-m-d');
            $tenant        = (new Tenant())->setConnection('tenant')->find($request->tenant_id);
            $booking       = (new Booking())->setConnection('tenant')->find($request->booking_id);
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
            $booking->update([
                'status'         => 'completed',
                'booking_status' => 'agreement',
            ]);
            if ($agreement) {
                ($request->relocation_date != null) ? $relocation_date = Carbon::createFromFormat('d/m/Y', $request->relocation_date)->format('Y-m-d') : $relocation_date = null;
                ($request->period_from != null) ? $period_from         = Carbon::createFromFormat('d/m/Y', $request->period_from)->format('Y-m-d') : $period_from         = null;
                ($request->period_to != null) ? $period_to             = Carbon::createFromFormat('d/m/Y', $request->period_to)->format('Y-m-d') : $period_to             = null;
                $agreement_details                                     = (new AgreementDetails())->setConnection('tenant')->create([
                    'agreement_id'                => $agreement->id ?? null,
                    'employee_id'                 => ($request->employee_id != -1) ? $request->employee_id : null,
                    'agent_id'                    => ($request->agent_id != -1) ? $request->agent_id : null,
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
                        $unit_management->update(['booking_status' => 'agreement',  'tenant_id' => $request->tenant_id]);
                        if (isset($request->service_counter[$i])) {
                            for ($ind = 1, $inde = $request->service_counter[$i]; $ind <= $inde; $ind++) {
                                $chargeMode       = isset($request->input("charge_mode-{$i}-{$ind}")[0]) ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                                $chargeModeType   = isset($request->input("charge_mode_type-{$i}-{$ind}")[0]) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                                $amountCharge     = isset($request->input("amount_charge-{$i}-{$ind}")[0]) ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                                $percentageCharge = isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                                $calculateAmount  = isset($request->input("calculate_amount-{$i}-{$ind}")[0]) ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                                $vatPercentage    = isset($request->input("vat_percentage-{$i}-{$ind}")[0]) ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                                $vatAmount        = isset($request->input("vat_amount-{$i}-{$ind}")[0]) ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                                $totalAmount      = isset($request->input("total_amount-{$i}-{$ind}")[0]) ? $request->input("total_amount-{$i}-{$ind}")[0] : null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate  = $start ?? null;
                                $expiryDate = $expiry ?? null;

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
                        if (! isset($request->service_counter[$i]) && isset($request->old_service_counter[$i])) {
                            for ($ind = 1, $inde = $request->old_service_counter[$i]; $ind <= $inde; $ind++) {
                                $chargeMode       = isset($request->input("charge_mode-{$i}-{$ind}")[0]) ? $request->input("charge_mode-{$i}-{$ind}")[0] : null;
                                $chargeModeType   = isset($request->input("charge_mode_type-{$i}-{$ind}")[0]) ? $request->input("charge_mode_type-{$i}-{$ind}")[0] : null;
                                $amountCharge     = isset($request->input("amount_charge-{$i}-{$ind}")[0]) ? $request->input("amount_charge-{$i}-{$ind}")[0] : null;
                                $percentageCharge = isset($request->input("percentage_amount_charge-{$i}-{$ind}")[0]) ? $request->input("percentage_amount_charge-{$i}-{$ind}")[0] : null;
                                $calculateAmount  = isset($request->input("calculate_amount-{$i}-{$ind}")[0]) ? $request->input("calculate_amount-{$i}-{$ind}")[0] : null;
                                if (isset($request->input("start_date-{$i}-{$ind}")[0])) {
                                    $start = Carbon::createFromFormat('d/m/Y', $request->input("start_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                if (isset($request->input("expiry_date-{$i}-{$ind}")[0])) {
                                    $expiry = Carbon::createFromFormat('d/m/Y', $request->input("expiry_date-{$i}-{$ind}")[0])->format('Y-m-d');
                                }
                                $startDate     = $start ?? null;
                                $expiryDate    = $expiry ?? null;
                                $vatPercentage = isset($request->input("vat_percentage-{$i}-{$ind}")[0]) ? $request->input("vat_percentage-{$i}-{$ind}")[0] : null;
                                $vatAmount     = isset($request->input("vat_amount-{$i}-{$ind}")[0]) ? $request->input("vat_amount-{$i}-{$ind}")[0] : null;
                                $totalAmount   = isset($request->input("total_amount-{$i}-{$ind}")[0]) ? $request->input("total_amount-{$i}-{$ind}")[0] : null;
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

    public function empty_unit_from_booking_unit($id)
    {
        $booking_unit    = (new BookingUnits())->setConnection('tenant')->select('id', 'unit_id')->where('id', $id)->first();
        $unit_management = (new UnitManagement())->setConnection('tenant')->where('id', $booking_unit->unit_id)->first();
        $unit_management->update([
            'booking_status' => 'empty',
        ]);
        $booking_unit->delete();
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
        $services_master     = (new ServiceMaster())->setConnection('tenant')->select('id', 'name')->lazy();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();

        $data = [
            'dail_code_main'            => $dail_code_main,
            'services_master'          => $services_master,
            'tenant_id'                => $tenant_id,
            'tenant'                   => $tenant,
            'booking_units'            => $all_units,
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
        return view('admin-views.property_transactions.bookings.create_with_select_unit', $data);
    }

    public function empty_unit_from_service_booking($id)
    {
        $deleted = DB::connection('tenant')->table('booking_units_services')->where('id', $id)->delete();

        return response()->json([
            'status'  => 200,
            'success' => $deleted > 0,
        ]);
    }

    public function booking_update_status($data, $ids)
    {
        if ($data['status'] == 'canceled') {
            $units = BookingUnits::whereIn('booking_id', $ids)->pluck('unit_id');

            UnitManagement::whereIn('id', $units)->update([
                'booking_status' => 'empty'
            ]);
            Booking::whereIn('id', $ids)->update($data);
        }
    }
}
