<?php

namespace App\Http\Controllers\collections;

use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use App\Models\CompanySettings;
use Illuminate\Support\Facades\DB;
use App\Models\collections\Receipt;
use App\Http\Controllers\Controller;
use App\Models\hierarchy\MainLedger;
use App\Models\collections\ReceiptItems;
use App\Models\collections\ReceiptSettings;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('agreement');
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && is_array($ids) && count($ids)) {
            $data = ['status' => 1];
            (new Receipt())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('general.updated_successfully'));
        }
        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $receipts    = (new Receipt())->setConnection('tenant')->when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('receipt_ref', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->orderBy('created_at', 'asc')->paginate()->appends($query_param);
        if ($request->bulk_action_btn === 'filter') {
            $report_query = (new Receipt())->setConnection('tenant')->query();
            if ($request->voucher_type && $request->voucher_type != -1) {
                $report_query->where('voucher_type', $request->voucher_type);
            }
            if ($request->from && $request->to) {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay();
                $endDate   = Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay();
                $report_query->whereBetween('created_at', [$startDate, $endDate]);
            }
            $receipts = $report_query->orderBy('created_at', 'desc')->paginate();
        }
        $tenants          = (new Tenant())->setConnection('tenant')->get();
        $receipt_settings = (new ReceiptSettings())->setConnection('tenant')->get();
        $data             = [
            'tenants'          => $tenants,
            'receipts'         => $receipts,
            'search'           => $search,
            'receipt_settings' => $receipt_settings,

        ];
        return view("admin-views.collections.receipts.receipts_list", $data);
    }
    public function create(Request $request)
    {
        $tenant   = (new Tenant())->setConnection('tenant')->findOrFail($request->tenant_id);
        $invoices = (new Invoice())->setConnection('tenant')->with('items')->where('tenant_id', $request->tenant_id)->get();
        $total    = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($request) {
            $query->where('tenant_id', $request->tenant_id);
        })->sum('total');
        $total_paid = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($request) {
            $query->where('tenant_id', $request->tenant_id);
        })->sum('paid_amount');
        $receipt_settings       = (new ReceiptSettings())->setConnection('tenant')->get();
        $receipt_settings_first = (new ReceiptSettings())->setConnection('tenant')->first();
        $main_ledgers           = $receipt_settings_first->main_ledgers;
        $total_debit            = ($total - $total_paid);

        $data = [
            'tenant'           => $tenant,
            'invoices'         => $invoices,
            'receipt_settings' => $receipt_settings,
            'main_ledgers'     => $main_ledgers,
            'total_debit'      => $total_debit,
        ];
        return view('admin-views.collections.receipts.create', $data);
    }
    public function edit($id)
    {
        $receipt      = (new Receipt())->setConnection('tenant')->with('receipt_items', 'payment_methods')->findOrFail($id);
        $receipt_item = (new ReceiptItems())->setConnection('tenant')->where('receipt_id', $id)->get();
        // $receipt_item       = ReceiptItems::where('receipt_id' , $id)->pluck('invoice_item_id');
        $tenant   = (new Tenant())->setConnection('tenant')->where('id', $receipt->tenant_id)->first();
        $invoices = (new Invoice())->setConnection('tenant')->with('items')->where('tenant_id', $receipt->tenant_id)->get();
        // $invoices_items         = InvoiceItems::whereIn('id', $receipt_item )->get();
        $receipt_settings = (new ReceiptSettings())->setConnection('tenant')->get();
        $payment_methods  = DB::connection('tenant')->table('receipts_payment_method')->where('receipt_id', $id)->get();
        $main_ledgers     = (new MainLedger())->setConnection('tenant')->whereIn('group_id', [44, 45, 46])->get();
        $data             = [
            'receipt'          => $receipt,
            'receipt_item'     => $receipt_item,
            // 'invoices_items'           => $invoices_items,
            'tenant'           => $tenant,
            'invoices'         => $invoices,
            'receipt_settings' => $receipt_settings,
            'main_ledgers'     => $main_ledgers,
            'payment_methods'  => $payment_methods,
        ];
        return view('admin-views.collections.receipts.edit', $data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'tenant_id'        => 'required',
            'balance_due'      => 'required',
            'receipt_amount'   => 'required',
            'receipt_date'     => 'required',
            'payment_method'   => ['required', 'array', 'min:1'],
            'payment_method.*' => ['required', 'exists:main_ledgers,id'],
            'payment_amount.*' => ['required', 'numeric', 'min:0.01'],
        ]);
        DB::beginTransaction();
        try {
            $receipt_date = $request->receipt_date
                ? Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d')
                : null;
            $receipt = (new Receipt())->setConnection('tenant')->create([
                'tenant_id'      => $request->tenant_id,
                'balance_due'    => str_replace(',', '', $request->balance_due),
                'voucher_type'   => $request->voucher_type,
                'receipt_ref'    => $request->receipt_ref,
                'receipt_date'   => $receipt_date,
                'receipt_amount' => $request->receipt_amount,
                'is_advance'     => $request->has('isAdvance') ? 1 : 0,
                'advance_ref'    => $request->advance_ref ?? null,
            ]);
            if (! isset($request->isAdvance)) {
                foreach ($request->receipt_item_id as $receipt_item) {
                    $invoice_item = (new InvoiceItems())->setConnection('tenant')->where('id', $receipt_item)->with(
                        'unit_management',
                        'invoice',
                        'building',
                        'unit_management.block_unit_management',
                        'unit_management.property_unit_management',
                        'unit_management.block_unit_management.block',
                        'unit_management.floor_unit_management',
                        'unit_management.floor_unit_management.floor_management_main',
                        'unit_management.unit_management_main'
                    )->first();
                    $invoice = (new Invoice())->setConnection('tenant')->where('id', $invoice_item->invoice_id)->first();
                    if (isset($request->pay_amount[$receipt_item])) {
                        $paid_amount = $request->pay_amount[$receipt_item];
                    }
                    $invoice_item->update([
                        'paid_amount' => ($paid_amount != 0) ? ($paid_amount + $invoice_item->paid_amount) : $invoice_item->paid_amount,
                    ]);
                    if ($request->pay_amount[$receipt_item] != null) {
                        $receipt_items = (new ReceiptItems())->setConnection('tenant')->create([
                            'date'            => $receipt_date,
                            'receipt_id'      => $receipt->id,
                            'ref'             => $invoice->invoice_number,
                            'invoice_item_id' => $invoice_item->id,
                            'paid_amount'     => $request->pay_amount[$receipt_item] ?? 0,
                            'type'            => $invoice_item->category,
                            'unit_name'       => $invoice_item->unit_management->property_unit_management->name .
                                '-' .
                                $invoice_item->unit_management->unit_management_main->name .
                                '-' .
                                $invoice_item->unit_management->block_unit_management->block->name .
                                '-' .
                                $invoice_item->unit_management->floor_unit_management->floor_management_main->name,

                        ]);
                    }
                }
            }
            foreach ($request->payment_method as $payment_method_item) {
                $cheque_date = (isset($request->cheque_date[$payment_method_item])) ? Carbon::createFromFormat('d/m/Y', $request->cheque_date[$payment_method_item])->format('Y-m-d') : null;
                DB::connection('tenant')->table('receipts_payment_method')->insert([
                    'receipt_id'     => $receipt->id,
                    'main_ledger_id' => $payment_method_item,
                    'amount'         => $request->payment_amount[$payment_method_item],
                    'bank_name'      => $request->bank_name[$payment_method_item] ?? null,
                    'cheque_no'      => $request->cheque_no[$payment_method_item] ?? null,
                    'cheque_date'    => $cheque_date ?? null,
                ]);
            }
            DB::commit();
            return redirect()->route("receipts.list")->with("success", __('general.updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with("error", $th->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'tenant_id'      => 'required',
            'balance_due'    => 'required',
            'receipt_amount' => 'required',
            'receipt_date'   => 'required',
        ]);

        DB::beginTransaction();
        try {
            $receipt_date = $request->receipt_date
                ? Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d')
                : null;

            $receipt = (new Receipt())->setConnection('tenant')->findOrFail($id);
            $receipt->update([
                'tenant_id'      => $request->tenant_id,
                'balance_due'    => $request->balance_due,
                'voucher_type'   => $request->voucher_type,
                'receipt_ref'    => $request->receipt_ref,
                'receipt_date'   => $receipt_date,
                'receipt_amount' => $request->receipt_amount,
                'is_advance'     => $request->has('isAdvance') ? 1 : 0,
                'advance_ref'    => $request->advance_ref ?? null,
            ]);

            if (! isset($request->isAdvance)) {
                (new ReceiptItems())->setConnection('tenant')->where('receipt_id', $receipt->id)->delete();

                foreach ($request->receipt_item_id as $receipt_item) {
                    $invoice_item = (new InvoiceItems())->setConnection('tenant')->where('id', $receipt_item)->with(
                        'unit_management',
                        'invoice',
                        'building',
                        'unit_management.block_unit_management',
                        'unit_management.property_unit_management',
                        'unit_management.block_unit_management.block',
                        'unit_management.floor_unit_management',
                        'unit_management.floor_unit_management.floor_management_main',
                        'unit_management.unit_management_main'
                    )->first();
                    $invoice = (new Invoice())->setConnection('tenant')->where('id', $invoice_item->invoice_id)->first();
                    // $invoice_item->update([
                    //     'paid_amount'       => $request->pay_amount[$receipt_item],
                    // ]);
                    if ($request->pay_amount[$receipt_item] != null) {
                        (new ReceiptItems())->setConnection('tenant')->create([
                            'date'            => $receipt_date,
                            'receipt_id'      => $receipt->id,
                            'ref'             => $invoice->invoice_number,
                            'invoice_item_id' => $invoice_item->id,
                            'type'            => $invoice_item->category,
                            'paid_amount'     => $request->pay_amount[$receipt_item] ?? 0,
                            'unit_name'       => $invoice_item->unit_management->property_unit_management->name .
                                '-' .
                                $invoice_item->unit_management->unit_management_main->name .
                                '-' .
                                $invoice_item->unit_management->block_unit_management->block->name .
                                '-' .
                                $invoice_item->unit_management->floor_unit_management->floor_management_main->name,
                        ]);
                    }
                }
            }

            DB::connection('tenant')->table('receipts_payment_method')->where('receipt_id', $receipt->id)->delete();

            foreach ($request->payment_method as $payment_method_item) {
                DB::connection('tenant')->table('receipts_payment_method')->insert([
                    'receipt_id'     => $receipt->id,
                    'main_ledger_id' => $payment_method_item,
                    'amount'         => $request->payment_amount[$payment_method_item],
                ]);
            }

            DB::commit();
            return redirect()->route("receipts.list")->with("success", __('general.updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->with("error", $th->getMessage());
        }
    }

    public function get_receipt_type_id($id)
    {
        $receipt_settings = (new ReceiptSettings())->setConnection('tenant')->where('id', $id)->with('main_ledgers')->first();
        if ($receipt_settings) {
            return response()->json([
                'status'           => 200,
                "receipt_settings" => $receipt_settings,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Group Not Found",
            ]);
        }
    }
    public function delete(Request $request)
    {
        $receipts = (new Receipt())->setConnection('tenant')->findOrFail($request->id);
        $receipts->delete();
        return redirect()->route("receupts.index")->with("success", __('general.deleted_successfully'));
    }

    public function print_receipt($id)
    {
        $receipt          = (new Receipt())->setConnection('tenant')->findOrFail($id);
        $receipt_items    = (new ReceiptItems())->setConnection('tenant')->where('receipt_id', $receipt->id)->get();
        $invoice_item_ids = $receipt_items->pluck('invoice_item_id')->toArray();
        $invoice_items    = (new InvoiceItems())->setConnection('tenant')->whereIn('id', $invoice_item_ids)->with(
            'unit_management',
            'invoice',
            'building',
            'unit_management.block_unit_management',
            'unit_management.property_unit_management',
            'unit_management.block_unit_management.block',
            'unit_management.floor_unit_management',
            'unit_management.floor_unit_management.floor_management_main',
            'unit_management.unit_management_main'
        )->get();
        $receipts_payment_method = DB::connection('tenant')->table('receipts_payment_method')->where('receipt_id', $receipt->id)->get();
        $tenant                  = (new Tenant())->setConnection('tenant')->where('id', $receipt->tenant_id)->first();
        $total                   = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->sum('total');
        $total_paid = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->sum('paid_amount');
        $total_debit      = ($total - $total_paid);
        $receipt_settings = (new ReceiptSettings())->setConnection('tenant')->first();
        $data             = [
            'receipt_settings'        => $receipt_settings,
            'receipt'                 => $receipt,
            'total_debit'             => $total_debit,
            'receipt_items'           => $receipt_items,
            'invoice_item_ids'        => $invoice_item_ids,
            'tenant'                  => $tenant,
            'invoice_items'           => $invoice_items,
            'receipts_payment_method' => $receipts_payment_method,
        ];
        return view('admin-views.collections.receipts.print', $data);
    }

    public function add_receipt($id)
    {
        $invoice = Invoice::findOrFail($id);
        $tenant  = (new Tenant())->setConnection('tenant')->findOrFail($invoice->tenant_id);
        $total   = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($invoice) {
            $query->where('tenant_id', $invoice->tenant_id);
        })->sum('total');
        $total_paid = (new InvoiceItems())->setConnection('tenant')->whereHas('invoice', function ($query) use ($invoice) {
            $query->where('tenant_id', $invoice->tenant_id);
        })->sum('paid_amount');
        $receipt_settings       = (new ReceiptSettings())->setConnection('tenant')->get();
        $receipt_settings_first = (new ReceiptSettings())->setConnection('tenant')->first();
        $main_ledgers           = $receipt_settings_first->main_ledgers;
        $total_debit            = ($total - $total_paid);

        $data = [
            'tenant'           => $tenant,
            'invoice'          => $invoice,
            'receipt_settings' => $receipt_settings,
            'main_ledgers'     => $main_ledgers,
            'total_debit'      => $total_debit,
        ];
        return view('admin-views.collections.receipts.add_receipt_for_invoice', $data);
    }
    public function store_receipt(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tenant_id'        => 'required',
            'balance_due'      => 'required',
            'receipt_amount'   => 'required',
            'receipt_date'     => 'required',
            'payment_method'   => ['required', 'array'],
            // 'payment_method.*' => ['required', 'exists:main_ledgers,id'],
            'payment_amount.*' => ['required', 'numeric', 'min:0.01'],
        ]);
        // DB::beginTransaction();
        // try {
        $receipt_date = $request->receipt_date
            ? Carbon::createFromFormat('d/m/Y', $request->receipt_date)->format('Y-m-d')
            : null;
        $receipt = (new Receipt())->setConnection('tenant')->create([
            'tenant_id'      => $request->tenant_id,
            'invoice_id'      => $request->invoice_id,
            'balance_due'    => str_replace(',', '', $request->balance_due),
            'voucher_type'   => $request->voucher_type,
            'receipt_ref'    => $request->receipt_ref,
            'receipt_date'   => $receipt_date,
            'receipt_amount' => $request->receipt_amount,
            'is_advance'     => $request->has('isAdvance') ? 1 : 0,
            'advance_ref'    => $request->advance_ref ?? null,
        ]);
        $total_paid_amount = 0;
        if (! isset($request->isAdvance)) {
            foreach ($request->receipt_item_id as $receipt_item) {
                $invoice_item = (new InvoiceItems())->setConnection('tenant')->where('id', $receipt_item)->with(
                    'unit_management',
                    'invoice',
                    'building',
                    'unit_management.block_unit_management',
                    'unit_management.property_unit_management',
                    'unit_management.block_unit_management.block',
                    'unit_management.floor_unit_management',
                    'unit_management.floor_unit_management.floor_management_main',
                    'unit_management.unit_management_main'
                )->first();
                $invoice = (new Invoice())->setConnection('tenant')->where('id', $invoice_item->invoice_id)->first();
                $paid_amount = 0;
                if (isset($request->pay_amount[$receipt_item])) {
                    $paid_amount = $request->pay_amount[$receipt_item];
                }
                $invoice_item->update([
                    'paid_amount' => ($paid_amount != 0) ? ($paid_amount + $invoice_item->paid_amount) : $invoice_item->paid_amount,
                ]);
                $total_paid_amount += $paid_amount;
                if ($request->pay_amount[$receipt_item] != null) {
                    $receipt_items = (new ReceiptItems())->setConnection('tenant')->create([
                        'date'            => $receipt_date,
                        'receipt_id'      => $receipt->id,
                        'ref'             => $invoice->invoice_number,
                        'invoice_item_id' => $invoice_item->id,
                        'paid_amount'     => $request->pay_amount[$receipt_item] ?? 0,
                        'type'            => $invoice_item->category,
                        'unit_name'       => $invoice_item->unit_management->property_unit_management->name .
                            '-' .
                            $invoice_item->unit_management->unit_management_main->name .
                            '-' .
                            $invoice_item->unit_management->block_unit_management->block->name .
                            '-' .
                            $invoice_item->unit_management->floor_unit_management->floor_management_main->name,

                    ]);
                }
            }
        }
        foreach ($request->payment_method as $payment_method_item) {
            $cheque_date = (isset($request->cheque_date[$payment_method_item])) ? Carbon::createFromFormat('d/m/Y', $request->cheque_date[$payment_method_item])->format('Y-m-d') : null;
            DB::connection('tenant')->table('receipts_payment_method')->insert([
                'receipt_id'     => $receipt->id,
                'main_ledger_id' => $payment_method_item,
                'amount'         => $request->payment_amount[$payment_method_item],
                'bank_name'      => $request->bank_name[$payment_method_item] ?? null,
                'cheque_no'      => $request->cheque_no[$payment_method_item] ?? null,
                'cheque_date'    => $cheque_date ?? null,
            ]);
        }
        DB::commit();
        return redirect()->route("receipts.list")->with("success", __('general.updated_successfully'));
        // } catch (\Throwable $th) {
        //     DB::rollback();
        //     return redirect()->back()->with("error", $th->getMessage());
        // }
    }

    public function generate_receipt($id)
    {
        $receipt          = Receipt::findOrFail($id);
        $receipt_items    = ReceiptItems::where('receipt_id', $receipt->id)->get();
        $invoice_item_ids = $receipt_items->pluck('invoice_item_id')->toArray();
        $invoice_items    = InvoiceItems::whereIn('id', $invoice_item_ids)->with(
            'unit_management',
            'invoice',
            'building',
            'unit_management.block_unit_management',
            'unit_management.property_unit_management',
            'unit_management.block_unit_management.block',
            'unit_management.floor_unit_management',
            'unit_management.floor_unit_management.floor_management_main',
            'unit_management.unit_management_main'
        )->get();
        $receipts_payment_method = DB::connection('tenant')->table('receipts_payment_method')->where('receipt_id', $receipt->id)->get();
        $tenant                  = Tenant::where('id', $receipt->tenant_id)->first();
        $total                   = InvoiceItems::whereHas('invoice', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->sum('total');
        $total_paid = InvoiceItems::whereHas('invoice', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->sum('paid_amount');
        $total_debit      = ($total - $total_paid);
        $receipt_settings = ReceiptSettings::first();
        $company_settings    = CompanySettings::first();
        $company             = Company::first();
        $data             = [
            'receipt_settings'        => $receipt_settings,
            'receipt'                 => $receipt,
            'total_debit'             => $total_debit,
            'receipt_items'           => $receipt_items,
            'invoice_item_ids'        => $invoice_item_ids,
            'tenant'                  => $tenant,
            'invoice_items'           => $invoice_items,
            'receipts_payment_method' => $receipts_payment_method,
            'company_settings'        => $company_settings,
            'company'                 => $company,
        ];

        $html = view('receipts.format-1', $data)->render();

        $mpdf = new Mpdf([
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'default_font' => 'dejavusans'
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output("{$receipt->receipt_ref}.pdf", 'I');
    }
}
