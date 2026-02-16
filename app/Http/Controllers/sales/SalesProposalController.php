<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\SalesProposal;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
