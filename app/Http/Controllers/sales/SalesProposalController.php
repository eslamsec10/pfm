<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\PropertyCustomer;
use App\Models\PropertyManagement;
use App\Models\SalesAgreement;
use App\Models\SalesAgreementInstallment;
use App\Models\SalesAgreementUnit;
use App\Models\SalesBooking;
use App\Models\SalesBookingInstallment;
use App\Models\SalesBookingUnit;
use App\Models\SalesProposal;
use App\Models\SalesProposalInstallment;
use App\Models\SalesProposalUnit;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesProposalController extends Controller
{
    public function index(Request $request)
    {
        $ids     = $request->bulk_ids;


        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $proposals   = SalesProposal::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('proposal_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->where('status', 'pending')->orWhere('status', 'proposal')
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = SalesProposal::query();
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
        return view("admin-views.sales.proposals.proposal_list", $data);
    }

    public function create(Request $request)
    {
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $customers                  = PropertyCustomer::get();
        $country_master           = CountryMaster::get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data                     = [
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
            'dail_code_main'           => $dail_code_main,
            'company'                   => $company,
        ];
        return view('admin-views.sales.proposals.create', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $proposal = SalesProposal::create([
                'proposal_no'    => $request->proposal_no,
                'proposal_date'  => $request->proposal_date
                    ? Carbon::createFromFormat('d/m/Y', $request->proposal_date)->format('Y-m-d')
                    : null,
                'customer_id'    => $request->customer_id,
                'status'         => 'proposal',
                'booking_status' => 'proposal',
            ]);

            $totalUnits = $request->total_no_of_required_units;

            for ($suffix = 1; $suffix <= $totalUnits; $suffix++) {

                if (!$request->has("unit-$suffix")) {
                    continue;
                }

                $unit = SalesProposalUnit::create([
                    'proposal_id'            => $proposal->id,
                    'property_management_id' => $request["property_id-$suffix"],
                    'unit_description_id'    => $request["unit_description_id-$suffix"] ?: null,
                    'unit_type_id'           => $request["unit_type_id-$suffix"] ?: null,
                    'unit_condition_id'      => $request["unit_condition_id-$suffix"] ?: null,
                    'view_id'                => $request["view_id-$suffix"] ?: null,
                    'unit_management_id'     => $request["unit-$suffix"],
                    'property_type'          => $request["property_type-$suffix"],
                    'comment'                => $request["notes-$suffix"],
                    'price'                  => $request["price-$suffix"],
                    'advance_percentage'     => $request["advance_percentage-$suffix"],
                    'advance_amount'         => $request["advance_amount-$suffix"],
                    'number_of_installments' => $request["number_of_installments-$suffix"],
                    'payment_plan'           => $request["payment_plan-$suffix"],
                    'start_date'             => $request["start_date-$suffix"]
                        ? Carbon::createFromFormat('d/m/Y', $request["start_date-$suffix"])->format('Y-m-d')
                        : null,
                ]);

                $installmentDates   = $request["installment_date_$suffix"] ?? [];
                $installmentAmounts = $request["installment_amount_$suffix"] ?? [];

                foreach ($installmentDates as $key => $date) {

                    SalesProposalInstallment::create([
                        'proposal_id'            => $proposal->id,
                        'sales_proposal_unit_id' => $unit->id,
                        'unit_management_id'     => $request["unit-$suffix"],
                        'amount'                 => $installmentAmounts[$key] ?? 0,
                        'due_date'               => Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales.proposal.index')
                ->with('success', 'Proposal saved successfully');
        } catch (Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function add_to_booking($id)
    {
        $proposal                  = SalesProposal::with('proposal_units', 'customer')->findOrFail($id);
        $customers                  = DB::connection('tenant')->table('tenants')->get();
        $country_master           = CountryMaster::get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data = [
            'unit_types'                => $unit_types,
            'dail_code_main'            => $dail_code_main,
            'company'                   => $company,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'customers'                  => $customers,
            'proposal'                  => $proposal,

        ];
        return view('admin-views.sales.proposals.add_to_booking', $data);
    }

    public function store_to_booking(Request $request)
    {

        DB::beginTransaction();

        try {
            $booking = SalesBooking::create([
                'proposal_id'     => $request->proposal_id,
                'booking_no'    => $request->booking_no,
                'booking_date'  => ($request->booking_date) ? Carbon::createFromFormat('d-m-Y', $request->booking_date)->format('Y-m-d') : '',
                'customer_id'    => $request->customer_id,
                'status'         => 'booking',
                'booking_status' => 'booking',
            ]);
            $proposal_units = SalesProposalUnit::where('proposal_id', $request->proposal_id)
                ->pluck('id');

            foreach ($proposal_units as $suffix) {
                $unit = SalesBookingUnit::create([
                    'property_management_id' => $request["property_id-$suffix"],
                    'booking_id'             => $booking->id,
                    'unit_description_id'    => $request["unit_description_id-$suffix"] ?: null,
                    'unit_type_id'           => $request["unit_type_id-$suffix"] ?: null,
                    'unit_condition_id'      => $request["unit_condition_id-$suffix"],
                    'view_id'                => $request["view_id-$suffix"],
                    'unit_management_id'     => $request["unit-$suffix"],
                    'property_type'          => $request["property_type-$suffix"],
                    'comment'                => $request["notes-$suffix"],
                    'price'                  => $request["price-$suffix"],
                    'advance_percentage'     => $request["advance_percentage-$suffix"],
                    'advance_amount'         => $request["advance_amount-$suffix"],
                    'number_of_installments' => $request["number_of_installments-$suffix"],
                    'payment_plan'           => $request["payment_plan-$suffix"],
                    'start_date'             => ($request["start_date-$suffix"]) ? Carbon::createFromFormat('d/m/Y', $request["start_date-$suffix"])->format('Y-m-d') : '',
                ]);

                $installmentDates   = $request["installment_date_$suffix"] ?? [];
                $installmentAmounts = $request["installment_amount_$suffix"] ?? [];

                foreach ($installmentDates as $key => $date) {

                    SalesBookingInstallment::create([
                        'booking_id'                    => $booking->id,
                        'sales_booking_unit_id'         => $unit->id,
                        'unit_management_id'            => $request["unit-$suffix"],
                        'amount'                        => $installmentAmounts[$key] ?? 0,
                        'due_date'                      => Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
                    ]);
                }

                $proposal = SalesProposal::where('id', $request->proposal_id)->first();
                $proposal->update([
                    'booking_status'    => 'booking',
                    'status'            => 'completed',
                ]);
            }
            DB::commit();

            return redirect()->route('sales.booking.index')->with('success', 'Booking saved successfully');
        } catch (Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function add_to_agreement($id)
    {
        $proposal                  = SalesProposal::with('proposal_units', 'customer')->findOrFail($id);
        $customers                  = DB::connection('tenant')->table('tenants')->get();
        $country_master           = CountryMaster::get();
        $live_withs               = DB::connection('tenant')->table('live_withs')->get();
        $business_activities      = DB::connection('tenant')->table('business_activities')->get();
        $buildings                = PropertyManagement::forUser()->get();
        $unit_descriptions        = DB::connection('tenant')->table('unit_descriptions')->get();
        $unit_conditions          = DB::connection('tenant')->table('unit_conditions')->get();
        $unit_types               = DB::connection('tenant')->table('unit_types')->get();
        $views                    = DB::connection('tenant')->table('views')->get();
        $property_types           = DB::connection('tenant')->table('property_types')->get();
        $dail_code_main = DB::connection('tenant')->table('countries')->select('id', 'dial_code')->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data = [
            'unit_types'                => $unit_types,
            'dail_code_main'            => $dail_code_main,
            'company'                   => $company,
            'property_types'           => $property_types,
            'views'                    => $views,
            'unit_conditions'          => $unit_conditions,
            'unit_descriptions'        => $unit_descriptions,
            'buildings'                => $buildings,
            'country_master'           => $country_master,
            'live_withs'               => $live_withs,
            'business_activities'      => $business_activities,
            'customers'                  => $customers,
            'proposal'                  => $proposal,

        ];
        return view('admin-views.sales.proposals.add_to_agreement', $data);
    }

    public function store_to_agreement(Request $request)
    {

        DB::beginTransaction();

        try {
            $proposal = SalesProposal::where('id', $request->proposal_id)->first();

            $agreement = SalesAgreement::create([
                'proposal_no'     => $proposal->proposal_no,
                'agreement_no'    => $request->agreement_no,
                'agreement_date'  => ($request->agreement_date) ? Carbon::createFromFormat('d-m-Y', $request->agreement_date)->format('Y-m-d') : '',
                'customer_id'    => $request->customer_id,
                'status'         => 'agreement',
                'booking_status' => 'agreement',
            ]);
            $proposal_units = SalesProposalUnit::where('proposal_id', $request->proposal_id)
                ->pluck('id');

            foreach ($proposal_units as $suffix) {
                $unit = SalesAgreementUnit::create([
                    'property_management_id' => $request["property_id-$suffix"],
                    'agreement_id'             => $agreement->id,
                    'unit_description_id'    => $request["unit_description_id-$suffix"] ?: null,
                    'unit_type_id'           => $request["unit_type_id-$suffix"] ?: null,
                    'unit_condition_id'      => $request["unit_condition_id-$suffix"],
                    'view_id'                => $request["view_id-$suffix"],
                    'unit_management_id'     => $request["unit-$suffix"],
                    'property_type'          => $request["property_type-$suffix"],
                    'comment'                => $request["notes-$suffix"],
                    'price'                  => $request["price-$suffix"],
                    'advance_percentage'     => $request["advance_percentage-$suffix"],
                    'advance_amount'         => $request["advance_amount-$suffix"],
                    'number_of_installments' => $request["number_of_installments-$suffix"],
                    'payment_plan'           => $request["payment_plan-$suffix"],
                    'start_date'             => ($request["start_date-$suffix"]) ? Carbon::createFromFormat('d/m/Y', $request["start_date-$suffix"])->format('Y-m-d') : '',
                ]);

                $installmentDates   = $request["installment_date_$suffix"] ?? [];
                $installmentAmounts = $request["installment_amount_$suffix"] ?? [];

                foreach ($installmentDates as $key => $date) {

                    SalesAgreementInstallment::create([
                        'agreement_id'                    => $agreement->id,
                        'sales_agreement_unit_id'         => $unit->id,
                        'unit_management_id'            => $request["unit-$suffix"],
                        'amount'                        => $installmentAmounts[$key] ?? 0,
                        'due_date'                      => Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d'),
                    ]);
                }

                // $proposal = SalesProposal::where('id', $request->proposal_id)->first();
                $proposal->update([
                    'booking_status'    => 'agreement',
                    'status'            => 'completed',
                ]);
            }
            DB::commit();

            return redirect()->route('sales.agreement.index')->with('success', 'Agreement saved successfully');
        } catch (Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
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
        $units               =  UnitManagement::with('unit_management_main:id,name')
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
                'property_unit_management:id,name,code',
                'block_unit_management:id,block_id',
                'block_unit_management.block:id,name,code',
                'floor_unit_management.floor_management_main:id,name,code',
                'floor_unit_management:id,floor_id',
                'unit_management_main'
            )

            ->get();
        return response()->json($units);
    }
    public function get_customer($id)
    {
        $customer = PropertyCustomer::find($id);
        return json_encode($customer);
    }
}
