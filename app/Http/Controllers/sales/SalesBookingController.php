<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\PropertyCustomer;
use App\Models\PropertyManagement;
use App\Models\SalesBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesBookingController extends Controller
{
    public function index(Request $request)
    {
        $ids     = $request->bulk_ids;


        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $bookings   = SalesBooking::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('booking_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->where('status', 'pending')->orWhere('status', 'booking')
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $data         = ['status' => 1];
            $report_query = SalesBooking::query();
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
            'search'    => $search,

        ];
        return view("admin-views.sales.bookings.booking_list", $data);
    }

    public function create()
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
        return view('admin-views.sales.bookings.create', $data);
    }
}
