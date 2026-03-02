<?php
namespace App\Http\Controllers\collections;

use App\Http\Controllers\Controller;
use App\Models\collections\InvoiceSettings;
use App\Models\hierarchy\MainLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceSettingsController extends Controller
{
    public function InvoiceIndex(Request $request)
    {
        $ids              = $request->bulk_ids;
        $search           = $request['search'];
        $query_param      = $search ? ['search' => $request['search']] : '';
        $invoice_settings = InvoiceSettings::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);
        $ledgers = MainLedger::whereNotNull('account_no')->get();

        // $ledgers = MainLedger::where('group_id', $group->id)->get();
        // dd($group , $ledgers);
        $data = [
            "invoice_settings" => $invoice_settings,
            "search"           => $search,
            "ledgers"          => $ledgers,

        ];
        return view('admin-views.transactions_settings.invoice_settings', $data);
    }
    public function InvoiceStore(Request $request)
    {
        $request->validate([
            'invoice_width'        => 'required',
            'invoice_start_number' => 'required',
            'invoice_name'         => 'required|string|max:255',
            'ledger_id'            => 'required',
            // Rule::unique('tenant.invoice_settings', 'invoice_type'),

        ]);
        DB::beginTransaction();
        try {

            $invoice_settings = InvoiceSettings::create([
                'invoice_prefix'        => $request->invoice_prefix,
                'invoice_name'          => $request->invoice_name,
                'invoice_suffix'        => $request->invoice_suffix,
                'invoice_start_number'  => $request->invoice_start_number,
                'invoice_with_logo'     => $request->invoice_with_logo,
                'invoice_logo_position' => $request->invoice_logo_position,
                'invoice_width'         => $request->invoice_width,
                'ledger_id'             => ($request->ledger_id == 0) ? null : $request->ledger_id,
                // 'invoice_type'          => $request->invoice_type,
                'invoice_format'        => $request->invoice_format ?? null,
                'width'                 => $request->width ?? null,
                'height'                => $request->height ?? null,
                'invoice_date'          => Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') ?? null,

                'format_color'          => $request->format_color,
                'background_color'      => $request->background_color,

                'company_email'         => $request->company_email,
                'company_phone'         => $request->company_phone,
                'company_fax'           => $request->company_fax,
                'company_address'       => $request->company_address,
                'company_vat_no'        => $request->company_vat_no,

                'tenant_email'          => $request->tenant_email,
                'tenant_phone'          => $request->tenant_phone,
                'tenant_fax'            => $request->tenant_fax,
                'tenant_address'        => $request->tenant_address,
                'tenant_vat_no'         => $request->tenant_vat_no,

                'qr_code_width'         => $request->qr_code_width,
                'qr_code_height'        => $request->qr_code_height,
            ]);
            DB::commit();
            return redirect()->route("invoice_settings")->with("success", __('general.added_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with("error", $th->getMessage());

        }
    }
    public function InvoiceUpdate(Request $request)
    {
        $request->validate([
            // 'invoice_prefix'        => 'required|string|max:255',
            // 'invoice_suffix'        => 'required|string|max:255',
            'invoice_width'        => 'required',
            'invoice_start_number' => 'required',
            'invoice_name'         => 'required|string|max:255',
            'ledger_id'            => 'required',
            // 'invoice_type'         => 'required|unique:invoice_settings,invoice_type,' . $request->edit_invoice_settings_id,

        ]);

        DB::beginTransaction();
        try {
            $invoice_settings = InvoiceSettings::findOrFail($request->edit_invoice_settings_id);
            $invoice_settings->update([
                'invoice_prefix'        => $request->invoice_prefix,
                'invoice_name'          => $request->invoice_name,
                'invoice_suffix'        => $request->invoice_suffix,
                'invoice_start_number'  => $request->invoice_start_number,
                'invoice_with_logo'     => $request->invoice_with_logo,
                'invoice_logo_position' => $request->invoice_logo_position,
                'invoice_width'         => $request->invoice_width,
                'ledger_id'             => ($request->ledger_id == 0) ? null : $request->ledger_id,
                // 'invoice_type'          => $request->invoice_type,
                'invoice_format'        => $request->invoice_format ?? null,
                'width'                 => $request->width ?? null,
                'height'                => $request->height ?? null,
                'invoice_date'          => $request->invoice_date
                    ? Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d')
                    : null,

                'format_color'          => $request->format_color,
                'background_color'      => $request->background_color,

                'company_email'         => $request->company_email,
                'company_phone'         => $request->company_phone,
                'company_fax'           => $request->company_fax,
                'company_address'       => $request->company_address,
                'company_vat_no'        => $request->company_vat_no,

                'tenant_email'          => $request->tenant_email,
                'tenant_phone'          => $request->tenant_phone,
                'tenant_fax'            => $request->tenant_fax,
                'tenant_address'        => $request->tenant_address,
                'tenant_vat_no'         => $request->tenant_vat_no,

                'qr_code_width'         => $request->qr_code_width,
                'qr_code_height'        => $request->qr_code_height,
            ]);

            DB::commit();
            return redirect()->route("invoice_settings")->with("success", __('general.updated_successfully'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with("error", $th->getMessage());
        }
    }

    public function edit($id)
    {
        $invoice_settings = InvoiceSettings::findOrFail($id);
        // $main_ledgers          = DB::table('main_ledgers')->where('receipt_settings_id' ,$id )->get();
        // dd($main_ledgers);
        if ($invoice_settings) {
            return response()->json([
                'status'           => 200,
                "invoice_settings" => $invoice_settings,
                // "main_ledgers" => $main_ledgers,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Invoice Settings Not Found",
            ]);
        }
    }

    public function delete(Request $request)
    {
        $invoice_settings = InvoiceSettings::findOrFail($request->id);
        $invoice_settings->delete();
        return to_route('invoice_settings')->with('success', __('general.deleted_successfully'));
    }
}
