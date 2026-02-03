<?php
namespace App\Http\Controllers\property_reports;

use App\Http\Controllers\Controller;
use App\Models\collections\InvoiceSettings;
use App\Models\CompanySettings;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\PropertyManagement;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\ServiceMaster;
use App\Models\Tenant;
use App\Models\UnitManagement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceReturnController extends Controller
{

    public function index(Request $request)
    {

        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && $request->status && is_array($ids) && count($ids)) {
            $data = ['status' => $request->status];

            $invoiceUpdated = SalesReturn::whereIn('id', $ids)->update($data);
            return back()->with('success', __('updated successfully'));
        }
        $invoices_query = SalesReturn::query();
        if ($request->invoice_status && $request->invoice_status != -1) {
            $invoices_query = $invoices_query->whereStatus($request->invoice_status);
        }
        if ($request->invoice_tenant && $request->invoice_tenant != -1) {
            $invoices_query = $invoices_query->where('tenant_id', $request->invoice_tenant);
        }
        if ($request->invoice_building && $request->invoice_building != -1) {
            $invoices_query = $invoices_query->whereHas('items', function ($query) use ($request) {
                $query->where('building_id', $request->invoice_building);
            });
        }
        if ($request->invoice_unit_management && $request->invoice_unit_management != -1) {
            $invoices_query = $invoices_query->whereHas('items', function ($query) use ($request) {
                $query->where('unit_id', $request->invoice_unit_management);
            });
        }
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
            $endDate   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
            $invoices_query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $invoices = $invoices_query->paginate();
        // dd($invoices);
        $all_building    = PropertyManagement::forUser()->get();
        $tenants         = Tenant::get();
        $unit_management = UnitManagement::with(['property_unit_management', 'block_unit_management', 'floor_unit_management', 'unit_management_main', 'unit_description'])->get();
        return view('admin-views.property_reports.sales_return.invoices', compact('invoices', 'unit_management', 'all_building', 'tenants'));
    }
    public function print($id)
    {
        $invoice       = SalesReturn::findOrFail($id);
        $invoice_items = SalesReturnItem::where('invoice_return_id', $id)->get();
        ///dd($invoice);
        $invoice_settings    = InvoiceSettings::where('invoice_type', 'LIKE', "%{$invoice->invoice_type}%")->first();
        $company_settings    = CompanySettings::first();
        $company             = auth()->user();
        ($invoice) ? $tenant = Tenant::where('id', $invoice->tenant_id)->first() : $tenant = null;
        return view('admin-views.property_reports.sales_return.generate_invoice', compact('invoice', 'tenant', 'company_settings', 'company', 'invoice_settings', 'invoice_items'));
    }

    public function invoice_return_create(Request $request)
    {
        $invoice        = Invoice::findOrFail($request->invoice_id);
        $tenant         = Tenant::findOrFail($request->invoice_return_tenant);
        $invoices_items = InvoiceItems::where('invoice_id', $request->invoice_id)->whereNull('sales_return_status')->get();
        // dd($invoices_items  );
        return view('admin-views.property_reports.sales_return.create', compact('invoice', 'tenant', 'invoices_items'));
    }

    public function sales_return_store(Request $request)
    {
        $request->validate([
            'bulk_ids' => 'required',
        ]);
        $invoice               = Invoice::findOrFail($request->invoice_id);
        $tenant                = Tenant::findOrFail($request->tenant_id);
        $lastSalesReturnNumber = SalesReturn::orderBy('id', 'desc')->value('invoice_number');
        $nextNumber            = $lastSalesReturnNumber
            ? 'SR-' . sprintf('%05d', (int) str_replace('SR-', '', $lastSalesReturnNumber) + 1)
            : 'SR-00001';
        $sales_return = SalesReturn::create([
            'invoice_id'     => $invoice->id,
            'invoice_number' => $nextNumber,
            'tenant_id'      => $request->tenant_id,
            'invoice_date'   => today(),
        ]);
        $sales_return_total = 0;
        foreach ($request->bulk_ids as $item_id) {
            $invoice_item = InvoiceItems::where('id', $item_id)->first();
            SalesReturnItem::create([
                'invoice_return_id' => $sales_return->id, 
                'agreement_id'      => $invoice_item->agreement_id,
                'unit_id'           => $invoice_item->unit_id,
                'tenant_id'         => $invoice_item->tenant_id,
                'building_id'       => $invoice_item->building_id,
                'rent_amount'       => $invoice_item->rent_amount,
                'service'           => $invoice_item->service,
                'vat'               => $invoice_item->vat,
                'total'             => $invoice_item->total,
                'service_id'        => $invoice_item->service_id,
                'branch_id'         => $invoice_item->branch_id,
                'category'          => $invoice_item->category,
                'paid_amount'       => $invoice_item->paid_amount,
                'balance_due'       => $invoice_item->balance_due,

            ]);
            $invoice_item->update([
                'sales_return_status' => 1,
            ]);

            if ($invoice_item->service_id != null) {

                $service = ServiceMaster::where('id', $invoice_item->service_id)->first();
                $service->service_ledger->update([
                    'debit' => $invoice_item->total,
                ]);
            } else { 
                $unit = UnitManagement::where('id', $invoice_item->unit_id)->first();
                $unit->unit_ledger->update([
                    'debit' => $invoice_item->total,
                ]);
            }
            $sales_return_total += $invoice_item->total;
        }

        $sales_return->update([
            'total' => $sales_return_total,
        ]);
        $tenant->tenant_ledger->update([
            'credit'        => $sales_return_total,
        ]);

        return redirect()->route('invoices_return.all_invoices');
    }

    public function get_tenant_invoices(Request $request)
    {
        if ($request->ajax() && $request->has('tenant_id')) {
            $tenantId = $request->tenant_id;

            $invoices = Invoice::where('tenant_id', $tenantId)
                ->get(['id', 'invoice_number']);
            return response()->json([
                'success'  => true,
                'invoices' => $invoices,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'طلب غير صالح.'], 400);
    }
}
