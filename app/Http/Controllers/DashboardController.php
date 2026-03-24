<?php
namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\BlockManagement;
use App\Models\Company;
use App\Models\FloorManagement;
use App\Models\PropertyManagement;
use App\Models\Tenant;
use App\Models\UnitCondition;
use App\Models\UnitDescription;
use App\Models\UnitManagement;
use App\Models\UnitType;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {     
        // dd(DB::getDatabaseName());
        $units   = UnitManagement::select('id' , 'booking_status')->get();
        $tenants = Tenant::
        //->with('agreements')
        select('id' , 'name' ,'company_name')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get(); 
        $enquiries         = DB::table('enquiries')->count();
        $enquiries_pending = DB::table('enquiry_details')
            ->join('enquiry_request_statuses', 'enquiry_details.enquiry_request_status_id', '=', 'enquiry_request_statuses.id')
            ->where('enquiry_request_statuses.name', 'pending')
            ->count();
        $enquiries_confirmed = DB::table('enquiry_details')
            ->join('enquiry_request_statuses', 'enquiry_details.enquiry_request_status_id', '=', 'enquiry_request_statuses.id')
            ->where('enquiry_request_statuses.name', 'confirmed')
            ->count();
        $enquiries_canceled = DB::table('enquiry_details')
            ->join('enquiry_request_statuses', 'enquiry_details.enquiry_request_status_id', '=', 'enquiry_request_statuses.id')
            ->where('enquiry_request_statuses.name', 'canceled')
            ->count();
        
        $proposals         = DB::table('proposals')->count();
        $proposals_pending = DB::table('proposals')
            ->where('status', 'pending')
            ->count();
        $proposals_confirmed = DB::table('proposals')
            ->where('status', 'completed')
            ->count();
        $proposals_canceled = DB::table('proposals')
            ->where('status', 'canceled')
            ->count();

        $bookings         = DB::table('bookings')->count();
        $bookings_pending = DB::table('bookings')
            ->where('status', 'pending')
            ->count();
        $bookings_confirmed = DB::table('bookings')
            ->where('status', 'completed')
            ->count();
        $bookings_canceled = DB::table('bookings')
            ->where('status', 'canceled')
            ->count();

        $agreements         = DB::table('agreements')->count();
        $agreements_pending = DB::table('agreements')
            ->where('status', 'pending')
            ->count();
        $agreements_confirmed = DB::table('agreements')
            ->where('status', 'completed')
            ->count();
        $agreements_canceled = DB::table('agreements')
            ->where('status', 'canceled')
            ->count();
        $agreements_expired = Agreement::whereHas('agreement_details' , function($q){
            $q->where('period_to' , '>' , today());
        })
            ->count();
        // dd($agreements_expired);
        $buildings         = PropertyManagement::forUser()->select('name' , 'id' , 'code' )->get();
        $unit_descriptions = DB::table('unit_descriptions')->select('id' ,'name' , 'code')->get();
        $unit_conditions   = DB::table('unit_conditions')->select('id' ,'name' , 'code')->get();
        $unit_types        = DB::table('unit_types')->select('id' ,'name' , 'code')->get();
        $views             = DB::table('views')->select('id' ,'name' , 'code')->get();
        $property_types    = DB::table('property_types')->select('id' ,'name' , 'code')->get();
        $data              = [
            'unit_types'           => $unit_types,
            'property_types'       => $property_types,
            'views'                => $views,
            'unit_conditions'      => $unit_conditions,
            'unit_descriptions'    => $unit_descriptions,
            'buildings'            => $buildings,
            'units'                => $units,
            'tenants'              => $tenants,
            'proposals'            => $proposals,
            'proposals_pending'    => $proposals_pending,
            'proposals_confirmed'  => $proposals_confirmed,
            'proposals_canceled'   => $proposals_canceled,
            'enquiries'            => $enquiries,
            'enquiries_pending'    => $enquiries_pending,
            'enquiries_confirmed'  => $enquiries_confirmed,
            'enquiries_canceled'   => $enquiries_canceled,
            'bookings'             => $bookings,
            'bookings_pending'     => $bookings_pending,
            'bookings_confirmed'   => $bookings_confirmed,
            'bookings_canceled'    => $bookings_canceled,
            'agreements'           => $agreements,
            'agreements_pending'   => $agreements_pending,
            'agreements_confirmed' => $agreements_confirmed,
            'agreements_canceled'  => $agreements_canceled,
            'agreements_expired'   => $agreements_expired,
        ];
        return view("dashboard", $data);
    }

    public function general_search_units_in_dashboard(Request $request)
    {
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

    ->when($request->property_id && $request->property_id != -1, function ($q) use ($request) {
        $q->where('property_management_id', $request->property_id);
    })

    ->when($request->unit_description_id && $request->unit_description_id != -1, function ($q) use ($request) {
        $q->where('unit_description_id', $request->unit_description_id);
    })

    ->when($request->unit_condition_id && $request->unit_condition_id != -1, function ($q) use ($request) {
        $q->where('unit_condition_id', $request->unit_condition_id);
    })

    ->when($request->unit_type_id && $request->unit_type_id != -1, function ($q) use ($request) {
        $q->where('unit_type_id', $request->unit_type_id);
    })

    ->when($request->view_id && $request->view_id != -1, function ($q) use ($request) {
        $q->where('view_id', $request->view_id);
    });

$units = $report_query
    // ->orderBy('created_at', 'desc')
    ->paginate(20);

            // $units = $report_query->orderBy('created_at', 'desc')->paginate(20);
        // $all_units_filter = $query->get();
        // dd($units);
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->select('id', 'name')->get();
        $unit_conditions   = (new UnitCondition())->setConnection('tenant')->select('id', 'name')->get();
        $unit_types        = (new UnitType())->setConnection('tenant')->select('id', 'name')->get();
        $unit_views        = (new View())->setConnection('tenant')->select('id', 'name')->get();
        $tenants           = Tenant::select('id', 'name', 'company_name')->orderBy('created_at', 'desc')->get();
                
        $floors            = FloorManagement::select('id', 'floor_id')->with('floor_management_main')->get();
        $blocks            = BlockManagement::select('id', 'block_id')->with('block')->get();
        $buildings         = PropertyManagement::select('id', 'name')->forUser()->get();
        
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
                // return view('admin-views.property_transactions.enquiries.general_check_property', $data);

    }
    public function get_unit_details(Request $request, $id)
    {
        $query = (new UnitManagement())->setConnection('tenant')->with('unit_type', 'unit_condition', 'unit_description', 'view')
            ->where('property_management_id', $id);
        if ($request->filled('unit_description_id')) {
            $query->where('unit_description_id', $request->unit_description_id);
        }
        if ($request->filled('unit_type_id')) {
            $query->where('unit_type_id', $request->unit_type_id);
        }
        if ($request->filled('unit_condition_id')) {
            $query->where('unit_condition_id', $request->unit_condition_id);
        }
        if ($request->filled('view_id')) {
            $query->where('view_id', $request->view_id);
        }
        
    
        $units = $query
            ->select('id', 'unit_description_id', 'unit_condition_id', 'unit_type_id', 'view_id')
            ->get();
    
        $unitDescriptions = $units->pluck('unit_description_id')->unique()->values();
        $unitCondition = $units->pluck('unit_condition_id')->unique()->values();
        $unitType = $units->pluck('unit_type_id')->unique()->values();
        $unitView = $units->pluck('view_id')->unique()->values();
    
        $unit_descriptions = (new UnitDescription())->setConnection('tenant')->whereIn('id', $unitDescriptions)->select('id', 'name')->get();
        $unit_conditions = (new UnitCondition())->setConnection('tenant')->whereIn('id', $unitCondition)->select('id', 'name')->get();
        $unit_types = (new UnitType())->setConnection('tenant')->whereIn('id', $unitType)->select('id', 'name')->get();
        $unit_view = (new View())->setConnection('tenant')->whereIn('id', $unitView)->select('id', 'name')->get();
        return response()->json([
            'success' => true,
            'unit_descriptions' => $unit_descriptions,
            'unit_conditions'   => $unit_conditions,
            'unit_types'        => $unit_types,
            'unit_view'         => $unit_view,
        ]);
    }
    public function search_unit_side(){
        $buildings = PropertyManagement::forUser()->select('id' , 'name')->get();
        $unit_descriptions =  DB::connection('tenant')->table('unit_descriptions')->select('id' , 'name')->get();
        $unit_conditions =  DB::connection('tenant')->table('unit_conditions')->select('id' , 'name')->get();
        $unit_types =   DB::connection('tenant')->table('unit_types')->select('id' , 'name')->get();
        $views =   DB::connection('tenant')->table('views')->select('id' , 'name')->get();
        $property_types =   DB::connection('tenant')->table('property_types')->select('id' , 'name')->get(); 
        $data = [
            'property_types'                    => $property_types,
            'views'                             => $views,
            'unit_types'                        => $unit_types,
            'unit_conditions'                   => $unit_conditions,
            'unit_descriptions'                 => $unit_descriptions,
            'buildings'                         => $buildings,
        ];
        return view('dashboard.search_side' , $data);
    }
    public function get_unit_by_id($id){
        $unit = (new UnitManagement())->setConnection('tenant')->with('rent_schedules' , 'latest_rent_schedule')->where('id' , $id)->first();
         
        return response()->json($unit);
    }   
    // public function get_unit_details($id)
    // {
    //     //    `unit_description_id`, `unit_condition_id`, `unit_type_id`, `unit_parking_id`, `view_id`, `status`, `booking_status`,
    //     $units = UnitManagement::with('unit_type', 'unit_condition', 'unit_description', 'view')
    //     ->where('property_management_id', $id)
    //     ->whereNotNull('unit_description_id')
    //     ->select('id', 'unit_description_id', 'unit_condition_id', 'unit_type_id', 'view_id')
    //     ->get(); 
    //     $unitDescriptions = $units->pluck('unit_description_id')->unique()->values(); 
    //     $unitCondition = $units->pluck('unit_condition_id')->unique()->values(); 
    //     $unitType = $units->pluck('unit_type_id')->unique()->values(); 
    //     $unitView = $units->pluck('view_id')->unique()->values(); 
    //     $unit_descriptions = UnitDescription::whereIn('id', $unitDescriptions)->select('id', 'name')->get();
    //     $unit_conditions   = UnitCondition::whereIn('id', $unitCondition)->select('id', 'name')->get();
    //     $unit_types = UnitType::whereIn('id', $unitType)->select('id', 'name')->get();
    //     $unit_view = View::whereIn('id', $unitView)->select('id', 'name')->get();
    
    //     return response()->json([
    //         'success'           => true,
    //         'unit_descriptions' => $unit_descriptions,  
    //         'unit_conditions'   => $unit_conditions,  
    //         'unit_types'        => $unit_types,  
    //         'unit_view'         => $unit_view,  
    //     ]);
        

        // $units = UnitManagement::with('unit_type' , 'unit_condition' , 'unit_description' , 'view')->where('property_management_id' , $id)->select('id' , 'unit_description_id' , 'unit_condition_id' , 'unit_type_id' , 'view_id')->get();

        // return response()->json([ 
        //     'success' => true,
        //     'units' => $units
        // ]);

    // }

  

    public function get_units_by_booking_status($status = 'empty'){
        $unit_management = (new UnitManagement())->setConnection('tenant')->where('booking_status' , $status)->with([
                'property_unit_management:id,name',
                'block_unit_management:id,block_id',
                'block_unit_management.block:id,name',
                'floor_unit_management:id,floor_id',
                'floor_unit_management.floor_management_main:id,name',
                'unit_management_main:id,name',
                'unit_description',
                'unit_type',
                'unit_condition',
                'view',
            ])->orderBy('created_at', 'desc')->paginate(20); 
            // dd($unit_management);
             $data = [ 
            'units'             => $unit_management, 
        ];
        return view('dashboard.units_list_by_booking_status', $data);
    }

}
