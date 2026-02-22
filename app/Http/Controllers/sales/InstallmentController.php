<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SalesAgreementInstallment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InstallmentController extends Controller
{
    public function index(Request $request)
    {

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $installments = SalesAgreementInstallment::whereBetween('due_date', [
            $startOfMonth,
            $endOfMonth
        ])->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data = [
            'installments'  => $installments,
            'company'       => $company,

        ];
        return view("admin-views.sales.installment.index", $data);
    }
    public function list($id)
    {
        $installments   = SalesAgreementInstallment::where('agreement_id', $id)->get();
        $company = (new Company())->setConnection('tenant')->where('id', auth()->user()->company_id)->first() ?? (new Company())->setConnection('tenant')->first();

        $data = [
            'installments'  => $installments,
            'company'       => $company,

        ];
        return view("admin-views.sales.installment.list", $data);
    }
}
