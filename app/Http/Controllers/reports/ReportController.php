<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Invoice;
use App\Models\PropertyManagement;
use App\Models\Schedule;
use App\Models\Tenant;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function tenant_report(Request $request)
    {
        $ids = $request->bulk_ids;

        // if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
        //     $data = ['status' => 1];
        //     (new Tenant())->setConnection('tenant')->whereIn('id', $ids)->update($data);
        //     return back()->with('success', __('general.updated_successfully'));
        // }
        // $search      = $request['search'];
        // $query_param = $search ? ['search' => $request['search']] : '';

        // if ($request->bulk_action_btn === 'filter') {
        //     $report_query = (new Tenant())->setConnection('tenant')->query();
        //     if ($request->booking_status && $request->booking_status != -1) {
        //         $report_query->where('booking_status', $request->booking_status);
        //     }
        //     if ($request->filter_tenant && $request->filter_tenant != -1) {
        //         $report_query->where('id', $request->filter_tenant);
        //     }
        //     if ($request->from && $request->to) {
        //         $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
        //         $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
        //         $report_query->whereBetween('created_at', [$startDate, $endDate]);
        //     }
        //     $tenants = $report_query->orderBy('created_at', 'desc')->paginate();
        // }

        // $currentMonth = Carbon::now()->format('Y-m');
        // $tenants      = (new Tenant())->setConnection('tenant')->whereHas('schedules', function ($query) use ($currentMonth) {
        //     $query->where('billing_month_year', $currentMonth);
        // })
        //     ->with([
        //         'schedules' => function ($query) use ($currentMonth) {
        //             $query->select('id', 'tenant_id', 'unit_id', 'billing_month_year')
        //                 ->where('billing_month_year', $currentMonth);
        //         },
        //         'schedules.main_unit:id,unit_id,property_management_id,block_management_id,floor_management_id',
        //         'schedules.main_unit.unit_management_main:id,name',
        //         'schedules.main_unit.property_unit_management:id,name',
        //         'schedules.main_unit.block_unit_management:id,block_id',
        //         'schedules.main_unit.block_unit_management.block:id,name',
        //         'schedules.main_unit.floor_unit_management:id,floor_id',
        //         'schedules.main_unit.floor_unit_management.floor_management_main:id,name',
        //     ])->when($request['search'], function ($q) use ($request) {
        //     $key = explode(' ', $request['search']);
        //     foreach ($key as $value) {
        //         $q->Where('name', 'like', "%{$value}%")
        //             ->orWhere('id', $value);
        //     }
        // })
        //     ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        // $tenants = (new Tenant())
        // ->setConnection('tenant')
        // ->whereHas('schedules', function ($query) use ($currentMonth) {
        //     $query->where('billing_month_year', $currentMonth);
        // })
        // ->with([
        //     'schedules' => function ($query) use ($currentMonth) {
        //         $query->select('id', 'tenant_id', 'unit_id', 'billing_month_year')
        //               ->where('billing_month_year', $currentMonth);
        //     },
        //     'schedules.main_unit:id,unit_id,property_management_id,block_management_id,floor_management_id',
        //     'schedules.main_unit.unit_management_main:id,name',
        //     'schedules.main_unit.property_unit_management:id,name',
        //     'schedules.main_unit.block_unit_management:id,block_id',
        //     'schedules.main_unit.block_unit_management.block:id,name',
        //     'schedules.main_unit.floor_unit_management:id,floor_id',
        //     'schedules.main_unit.floor_unit_management.floor_management_main:id,name'
        // ])
        // ->get();
        $search      = $request['search'];
        $query_param = $search ? ['search' => $search] : [];

        $currentMonth = Carbon::now()->format('Y-m');

        if ($request->bulk_action_btn === 'filter') {
            $report_query = (new Tenant())->setConnection('tenant')->query();
            $report_query->whereHas('schedules', function ($query) use ($currentMonth) {
                $query->where('billing_month_year', $currentMonth);
            })
                ->with([
                    'schedules' => function ($query) use ($currentMonth) {
                        $query->select('id', 'tenant_id', 'unit_id', 'billing_month_year')
                            ->where('billing_month_year', $currentMonth);
                    },
                    'schedules.main_unit:id,unit_id,property_management_id,block_management_id,floor_management_id',
                    'schedules.main_unit.unit_management_main:id,name',
                    'schedules.main_unit.property_unit_management:id,name',
                    'schedules.main_unit.block_unit_management:id,block_id',
                    'schedules.main_unit.block_unit_management.block:id,name',
                    'schedules.main_unit.floor_unit_management:id,floor_id',
                    'schedules.main_unit.floor_unit_management.floor_management_main:id,name',
                ]);
            if ($request->filter_building && $request->filter_building != -1) {
                $report_query->whereHas('schedules.main_unit.property_unit_management', function ($q) use ($request) {
                    $q->where('id', $request->filter_building);
                });
            }
            if ($request->filter_unit_management && $request->filter_unit_management != -1) {
                $report_query->whereHas('schedules.main_unit', function ($q) use ($request) {
                    $q->where('id', $request->filter_unit_management);
                });
            }
            if ($request->filter_tenant && $request->filter_tenant != -1) {
                $report_query->where('id', $request->filter_tenant);
            }

            // if ($request->start_date && $request->end_date) {
            //     $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay();
            //     $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay();
            //     $report_query->whereBetween('created_at', [$startDate, $endDate]);
            // }

            if ($search) {
                $keywords = explode(' ', $search);
                $report_query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('name', 'like', "%{$word}%")
                            ->orWhere('id', $word);
                    }
                });
            }

            $tenants = $report_query->orderBy('created_at', 'desc')->paginate()->appends($query_param);
        } else {
            $tenants = (new Tenant())->setConnection('tenant')
                ->whereHas('schedules', function ($query) use ($currentMonth) {
                    $query->where('billing_month_year', $currentMonth);
                })
                ->with([
                    'schedules' => function ($query) use ($currentMonth) {
                        $query->select('id', 'tenant_id', 'unit_id', 'billing_month_year')
                            ->where('billing_month_year', $currentMonth);
                    },
                    'schedules.main_unit:id,unit_id,property_management_id,block_management_id,floor_management_id',
                    'schedules.main_unit.unit_management_main:id,name',
                    'schedules.main_unit.property_unit_management:id,name',
                    'schedules.main_unit.block_unit_management:id,block_id',
                    'schedules.main_unit.block_unit_management.block:id,name',
                    'schedules.main_unit.floor_unit_management:id,floor_id',
                    'schedules.main_unit.floor_unit_management.floor_management_main:id,name',
                ])
                ->when($search, function ($q) use ($search) {
                    $keywords = explode(' ', $search);
                    $q->where(function ($q2) use ($keywords) {
                        foreach ($keywords as $word) {
                            $q2->orWhere('name', 'like', "%{$word}%")
                                ->orWhere('id', $word);
                        }
                    });
                })
                ->orderBy('created_at', 'asc')
                ->paginate()
                ->appends($query_param);
        }
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name')->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'floor_unit_management', 'unit_management_main', 'unit_description'])->get();
        $data            = [
            'all_building'    => $all_building,
            'tenants'         => $tenants,
            'unit_management' => $unit_management,
        ];
        return view('admin-views.reports.tenant_contact', $data);
    }
    public function occupancy_details(Request $request)
    {
        $ids             = $request->bulk_ids;
        $search          = $request['search'];
        $query_param     = $search ? ['search' => $request['search']] : '';
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->select('id', 'name')->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'floor_unit_management', 'unit_management_main', 'unit_description'])->get();

        $currentMonth = Carbon::now()->format('Y-m');

        $units = (new UnitManagement())->setConnection('tenant')
            ->with([
                'schedules' => function ($q) use ($currentMonth) {
                    $q->where('billing_month_year', $currentMonth);
                },
                'schedules.tenant:id,name,company_name',
            ])
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param);

        if ($request->bulk_action_btn === 'filter') {
            $report_query = (new UnitManagement())->setConnection('tenant')
                ->with([
                    'schedules' => function ($q) use ($currentMonth) {
                        $q->where('billing_month_year', $currentMonth);
                    },
                    'schedules.tenant:id,name,company_name',
                ]);
            if ($request->filter_building && $request->filter_building != -1) {
                $report_query->where('property_management_id', $request->filter_building);
            }

            $units = $report_query->latest()
                ->orderBy('created_at', 'desc')
                ->paginate()
                ->appends($query_param);
        }

        $data = [
            'units'           => $units,
            'unit_management' => $unit_management,
            'all_building'    => $all_building,
        ];
        return view('admin-views.reports.occupancy_details', $data);
    }
    public function leased_expired_details(Request $request)
    {
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $currentDate     = Carbon::now();
        $thirtyDaysLater = Carbon::now()->addDays(30);
        $agreements      = (new Agreement())->setConnection('tenant')
            ->whereHas('agreement_details', function ($q) use ($currentDate, $thirtyDaysLater) {
                $q->whereBetween('period_to', [$currentDate, $thirtyDaysLater]);
            })
            ->with([
                'agreement_details',
                'tenant:id,name,company_name',
                'agreement_units.agreement_unit_main:id,property_management_id,block_management_id,floor_management_id,unit_id',
                'agreement_units.agreement_unit_main.unit_management_main:id,name',
                'agreement_units.agreement_unit_main.property_unit_management:id,name',
                'agreement_units.agreement_unit_main.block_unit_management:id,block_id',
                'agreement_units.agreement_unit_main.block_unit_management.block:id,name',
                'agreement_units.agreement_unit_main.floor_unit_management:id,floor_id',
                'agreement_units.agreement_unit_main.floor_unit_management.floor_management_main:id,name',
            ])
            ->latest()->orderBy('created_at', 'desc')->paginate()->appends($query_param);

        $data = [
            'agreements' => $agreements,
        ];
        return view('admin-views.reports.leased_expired_details', $data);
    }
    public function tenant_age_analysis(Request $request)
    {
        $ids         = $request->bulk_ids;
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';

        $invoices = (new Invoice())
            ->setConnection('tenant')
            ->where('status', 'unpaid')
            ->with('tenant')
            ->latest()
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->appends($query_param);

        $groupedSummaries = $invoices->getCollection()
            ->groupBy('tenant_id')
            ->map(function ($group) {
                $tenant = $group->first()->tenant;

                $totals = [
                    'total'    => 0,
                    '30_day'   => 0,
                    '30_to_60' => 0,
                    '60_to_90' => 0,
                ];

                foreach ($group as $invoice) {
                    $ageInDays = Carbon::parse($invoice->created_at)->diffInDays(Carbon::now());

                    $totals['total'] += $invoice->total;

                    if ($ageInDays <= 30) {
                        $totals['30_day'] += $invoice->total;
                    } elseif ($ageInDays <= 60) {
                        $totals['30_to_60'] += $invoice->total;
                    } elseif ($ageInDays <= 90) {
                        $totals['60_to_90'] += $invoice->total;
                    }
                }

                return [
                    'tenant' => $tenant,
                    'totals' => $totals,
                ];
            });

        $data = [
            'invoices'         => $invoices,
            'groupedSummaries' => $groupedSummaries,
        ];
        return view('admin-views.reports.tenant_age_analysis', $data);
    }

    public function tenant_financial_summary(Request $request)
    {
        $tenants = (new Tenant())->setConnection('tenant')->select('id', 'name', 'company_name')->whereHas('invoices')
            ->with('invoices', 'receipts', 'invoices.items', 'receipts.receipt_items', 'receipts.payment_methods')->paginate();
        $data = [
            'tenants' => $tenants,
            // 'groupedSummaries' => $groupedSummaries,
        ];
        return view('admin-views.reports.tenant_financial_summary', $data);
    }

    public function contract_details(Request $request)
    {
        $ids     = $request->bulk_ids;

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
        $agreements  = (new Agreement())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('agreement_no', 'like', "%{$value}%")

                    ->orWhereHas('tenant', function ($query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")->orWhere('company_name', 'like', "%{$value}%");
                    });
            }
        }) //->where('status', 'pending')
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
            $report_query = (new Agreement())->setConnection('tenant')->query();
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
        return view("admin-views.reports.contract_details", $data);
    }


    public function tenant_ledger_report(Request $request)
    {
        $tenants = Tenant::select('ledger_id', 'name', 'id', 'company_name')->with('ledger:id,name,code,debit,credit,credit_vat,debit_vat')->paginate();
        // dd($tenants);
        return view("admin-views.reports.tenant_ledger_report", compact('tenants'));
    }

    public function schedule(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['rent_amount' => $request->rent_amount];
            Schedule::whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $schedules = Schedule::paginate();
        // $agreement = Agreement::where('id', $id)->first();
        $data      = [
            'schedules' => $schedules,
            // 'agreement' => $agreement,
        ];
        return view('admin-views.property_transactions.tenants.schedule_list', $data);
    }
}
