<?php
namespace App\Http\Controllers\property_reports;

use App\Http\Controllers\Controller;
use App\Models\collections\InvoiceSettings;
use App\Models\PropertyManagement;
use App\Models\Schedule;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    // public function index(Request $request)
    // {
    //     // تحديث الحالة الجماعية
    //     $ids = $request->bulk_ids;
    //     if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
    //         (new Schedule())->setConnection('tenant')->whereIn('id', $ids)->update(['status' => 1]);
    //         return back()->with('success', __('general.updated_successfully'));
    //     }

    //     $search      = $request->input('search');
    //     $query_param = ! empty($search) ? ['search' => $search] : [];

    //     if (! empty($search)) {
    //         $schedules = (new Schedule())->setConnection('tenant')->where(function ($query) use ($search) {
    //             $keywords = explode(' ', $search);
    //             foreach ($keywords as $keyword) {
    //                 $query->orWhere('agreement_no', 'like', "%{$keyword}%")
    //                     ->orWhere('id', $keyword);
    //             }
    //         })
    //             ->with(['tenant', 'agreement', 'main_unit'])
    //             ->where('billing_month_year', Carbon::now()->format('Y-m'))
    //             ->latest()
    //             ->paginate()
    //             ->appends($query_param);
    //     } else {
    //         // استعلام التقرير
    //         $report_query = (new Schedule())->setConnection('tenant')->query()->with([
    //             'main_unit.property_unit_management',
    //             'main_unit.block_unit_management.block',
    //             'main_unit.block_unit_management',
    //             'main_unit.floor_unit_management.floor_management_main',
    //             'main_unit.floor_unit_management',
    //             'main_unit.unit_management_main',
    //             'main_unit.unit_description',
    //             'main_unit.unit_type',
    //             'main_unit.unit_condition',
    //         ]);

    //         // // تصفية حسب التواريخ
    //         // if ($request->filled(['start_date', 'end_date'])) {
    //         //     $report_query->whereBetween('from', [
    //         //         Carbon::parse($request->start_date)->format('Y-m-d'),
    //         //         Carbon::parse($request->end_date)->format('Y-m-d')
    //         //     ]);
    //         // }

    //         // تصفية حسب المستأجر
    //         if ($request->filled('report_tenant') && $request->report_tenant != -1) {
    //             $report_query->where('tenant_id', $request->report_tenant);
    //         }

    //         // تصفية حسب المبنى
    //         // if ($request->filled('report_building') && $request->report_building != -1) {
    //         //     $report_query->whereHas('building', function ($query) use ($request) {
    //         //         $query->where('building_id', $request->report_building);
    //         //     });
    //         // }

    //         // تصفية حسب الوحدة
    //         // if ($request->filled('report_unit_management') && $request->report_unit_management != -1) {
    //         //     $report_query->whereHas('unit_management', function ($query) use ($request) {
    //         //         $query->where('unit_id', $request->report_unit_management);
    //         //     });
    //         // }

    //         // إذا لم يتم تحديد أي فلاتر، يتم تصفية البيانات حسب الشهر الحالي
    //         if (! $request->filled(['report_unit_management', 'report_building', 'report_tenant', 'start_date', 'end_date'])) {
    //             $report_query->whereMonth('billing_month_year', Carbon::now()->month);
    //         }

    //         $schedules = $report_query->get();
    //         // $schedules = $report_query->orderBy('from', 'asc')->get();
    //     }

    //     $tenants = DB::table('tenants')->where('status', 'active')->get();
    //     dd($schedules);
    //     return view("admin-views.property_reports.schedule.schedule_list", [
    //         'schedules' => $schedules,
    //         'tenants'   => $tenants,
    //         'search'    => $search,
    //     ]);
    // }

    public function index(Request $request)
    {
        // $this->authorize('agreement');
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            (new Schedule())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        // if($query_param != ''){

        $schedules = (new Schedule())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('agreement_no', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })->where('category', 'rent')
            ->with('tenant', 'agreement', 'main_unit')->where('billing_month_year', Carbon::now()->format('Y-m'))->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        // }else{
        if ($request->bulk_action_btn === 'filter') {
            $report_query = (new Schedule())->setConnection('tenant')->query()
                ->with(['main_unit', 'main_unit.property_unit_management', 'main_unit.block_unit_management' , 'main_unit.block_unit_management.block',
                'main_unit.floor_unit_management', 'main_unit.floor_unit_management.floor_management_main', 'main_unit.unit_description',
                'main_unit.unit_type', 'main_unit.unit_condition']);
                if ($request->start_date && $request->end_date) {
                    $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m');  
                    $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m');
                
                    $report_query->whereBetween('billing_month_year', [$startDate, $endDate]);
                }
                
                
            if ($request->report_tenant && $request->report_tenant != -1) {
                $report_query->where('tenant_id', $request->report_tenant);
            }
            if ($request->invoice_status && $request->invoice_status != -1) {
                $report_query->where('invoice_status', $request->invoice_status);
                // dd("ERF");
            }
            if ($request->report_building && $request->report_building != -1) {
                $report_query->whereHas('main_unit.property_unit_management', function ($query) use ($request) {
                    $query->where('property_management_id', $request->report_building);
                });
            }
            if ($request->report_unit_management && $request->report_unit_management != -1) {
                $report_query->whereHas('main_unit', function ($query) use ($request) {
                    $query->where('id', $request->report_unit_management);
                });
            }
            if (! $request->report_unit_management && ! $request->report_building && ! $request->report_tenant && ! $request->start_date && ! $request->end_date) {
                $report_query->whereMonth('billing_month_year', Carbon::now()->month);
            }
            $schedules = $report_query->where('category', 'rent')->orderBy('created_at', 'asc')->paginate();
        }
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'block_unit_management.block',
         'floor_unit_management', 'floor_unit_management.floor_management_main', 'unit_management_main', 'unit_description'])->get();
        $tenants         = DB::connection('tenant')->table('tenants')->get();
        $invoice_types = (new InvoiceSettings())->setConnection('tenant')->get();
        $data = [
            'schedules' => $schedules,
            'all_building' => $all_building,
            'unit_management' => $unit_management,
            'tenants'   => $tenants,
            'search'    => $search,
            'invoice_types'    => $invoice_types,

        ];
        // dd($schedules);
        return view("admin-views.property_reports.schedule.schedule_list", $data);
    }
}
