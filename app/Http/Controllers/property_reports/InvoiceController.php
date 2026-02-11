<?php

namespace App\Http\Controllers\property_reports;

use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Schedule;
use App\Models\Agreement;
use Illuminate\Support\Str;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use App\Models\ServiceMaster;
use App\Models\AgreementUnits;
use App\Models\UnitManagement;
use App\Models\CompanySettings;
use App\Models\AgreementDetails;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\hierarchy\MainLedger;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\collections\InvoiceSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {

        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && $request->status && is_array($ids) && count($ids)) {
            $data = ['status' => $request->status];

            $invoiceUpdated = (new Invoice())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('updated successfully'));
        }
        $invoices_query = (new Invoice())->setConnection('tenant')->query();
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
        $all_building    = (new PropertyManagement())->setConnection('tenant')->forUser()->get();
        $tenants         = (new Tenant())->setConnection('tenant')->get();
        $unit_management = (new UnitManagement())->setConnection('tenant')->with(['property_unit_management', 'block_unit_management', 'floor_unit_management', 'unit_management_main', 'unit_description'])->get();
        return view('admin-views.property_reports.invoices.invoices', compact('invoices', 'unit_management', 'all_building', 'tenants'));
    }
    public function print($id)
    {
        $invoice       = (new Invoice())->setConnection('tenant')->findOrFail($id);
        $invoice_items = (new InvoiceItems())->setConnection('tenant')->where('invoice_id', $id)->get();
        $invoice_per_item = $invoice_items->first();
        $agreement = (new Agreement())->setConnection('tenant')->select('id', 'agreement_no')->where('id', $invoice_per_item->agreement_id)->first();
        $period = (new AgreementUnits())->setConnection('tenant')->select('commencement_date', 'payment_mode', 'expiry_date', 'id', 'agreement_id')->where('agreement_id', $invoice_per_item->agreement_id)->first();
        $start_date = Carbon::parse($period->commencement_date);
        if ($period->payment_mode == 2) {
            $start_date->addMonth();
        } elseif ($period->payment_mode == 3) {
            $start_date->addMonths(2);
        } elseif ($period->payment_mode == 4) {
            $start_date->addMonths(3);
        } elseif ($period->payment_mode == 5) {
            $start_date->addMonths(6);
        } elseif ($period->payment_mode == 6) {
            $start_date->addMonths(12);
        }
        $buildings = [];
        foreach ($invoice_items as $item) {
            $buildings[] = (new UnitManagement())->setConnection('tenant')->find($item->unit_id)->property_unit_management?->name;
        }
        $invoice_settings    = (new InvoiceSettings())->setConnection('tenant')->where('invoice_type', 'LIKE', "%{$invoice->invoice_type}%")->first();
        $company_settings    = (new CompanySettings())->setConnection('tenant')->first();
        $company             = auth()->user();
        ($invoice) ? $tenant = (new Tenant())->setConnection('tenant')->where('id', $invoice->tenant_id)->first() : $tenant = null;
        return view('admin-views.property_reports.invoices.generate_invoice', compact('buildings', 'agreement', 'start_date', 'period', 'invoice', 'tenant', 'company_settings', 'company', 'invoice_settings', 'invoice_items'));
    }
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'tenant_id'     => "required",
            'invoice_date'  => "required",
            'invoice_month' => "required",
        ]);
        DB::beginTransaction();

        try {
        $formattedDate = Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d');
        $date          = explode('-', $request->invoice_month);
        $month         = $date[0];
        $year          = $date[1];
        $tenant        = $request->tenant_id != 0 ? Tenant::find($request->tenant_id) : null;

        if ($request->tenant_id == 0) {
            $all_tenant          = Tenant::get();
            $all_master_invoices = [];
            foreach ($all_tenant as $tenant_invoice) {
                // get schedules
                $schedulesQuery = Schedule::where('billing_month_year', $request->invoice_month);
                $schedulesQuery->where('tenant_id', $tenant_invoice->id);
                $schedules = $schedulesQuery->where(function ($query) {
                    $query->where('invoice_status', 'pending')
                        ->orWhereNull('invoice_status');
                })->get();
                if (! $schedules->isEmpty()) {

                    $invoice = Invoice::create([
                        'invoice_number'     => InvoiceNumber(),
                        // 'invoice_number'     => invoiceNo($request->invoice_type) ?? $invoice_number,
                        'invoice_type'       => $request->invoice_type,
                        'tenant_id'          => $tenant_invoice->id,
                        'invoice_date'       => $formattedDate,
                        'invoice_month_year' => $request->invoice_month,
                        'status'             => "unpaid",
                        'total'              => 0,
                    ]);
                    $all_master_invoices[] = $invoice;
                    $grand_total           = 0;
                    $tenant_debit = 0;
                    $tenant_credit = 0;
                    if (isset($invoice)) {
                        $options['to'] = $tenant_invoice->whatsapp_no ?? "+201150099801";
                        // $options['to'] = $tenant->whatsapp_no ?? $tenant->contact_no;
                        $options['message'] = "Hello Dear " . ($tenant_invoice->name ?? $tenant_invoice->company_name) . " this your invoice";
                        $pdf = Pdf::loadView('admin-views.invoices-formats.format-1', [
                            'invoice' => $invoice->load('items', 'tenant')
                        ]);

                        $fileName = 'invoice_' . $invoice->id . '.pdf';

                        $path = public_path('invoices/' . $fileName);

                        if (!file_exists(public_path('invoices'))) {
                            mkdir(public_path('invoices'), 0777, true);
                        }
                        file_put_contents($path, $pdf->output());

                        $pdfUrl = url('invoices/' . $fileName);

                        $options['file'] = $pdfUrl;

                        sendWhatsApp($options);
                        foreach ($schedules as $schedule) {

                            $invoice_item = InvoiceItems::create([
                                'invoice_id'     => $invoice->id,
                                'agreement_id'   => $schedule->agreement_id,
                                'vat'            => $schedule->vat_amount,
                                'unit_id'        => $schedule->unit_id,
                                'building_id'    => $schedule->building_id ?? $schedule->main_unit->property_management_id,
                                'tenant_id'      => $schedule->tenant_id,
                                'rent_amount'    => ($schedule->rent_amount),
                                'service'        => $schedule->category,
                                'vat_percentage' => $schedule->vat,
                                'total'          => ($schedule->rent_amount + ((isset($schedule->vat_amount)) ? $schedule->vat_amount : 0)),
                                // 'building_id'  => $schedule->main_unit->property_management_id,
                                'service_id'     => $schedule->service_id,
                                'category'       => $schedule->category ?? 'rent',
                                'balance_due'    => ($schedule->rent_amount + ((isset($schedule->vat_amount)) ? $schedule->vat_amount : 0)),

                            ]);
                            if ($schedule->category == 'service') {
                                $service = ServiceMaster::find($schedule->service_id);
                                $service->service_ledger?->update([
                                    'credit'         => $invoice_item->total,
                                ]);
                            } elseif ($schedule->category == 'rent') {
                                $unit_management = UnitManagement::find($schedule->unit_id);
                                $unit_management->unit_ledger?->update([
                                    'credit'         => $invoice_item->total,
                                ]);
                            }

                            $grand_total += $invoice_item->total;
                            $tenant_credit += $invoice_item->total;
                            $schedule->update([
                                'invoice_status' => 'invoiced',
                            ]);
                        }
                    }
                    $ledger = MainLedger::where('name', 'LIKE', '%Tenants%')->where('main_id', $tenant_invoice->id)->first();
                    if ($ledger) {
                        $ledger->update([
                            'debit' => $tenant_debit,
                        ]);
                    }
                    $invoice->update([
                        'total' => $grand_total,
                    ]);
                }
            }
            if (count($all_master_invoices) == 0) {
                Toastr::error('you_dont_have_invoices_in_this_month');
                return back()->with('error', ui_change('you_dont_have_invoices_in_this_month'));
            }
            return redirect()->route('invoices.all_invoices')->with('success', 'Invoice All Added Successfully');
        } else {
            $tenant = $request->tenant_id != 0 ? (new Tenant())->setConnection('tenant')->find($request->tenant_id) : null;

            $invoiceMonth   = date('Y-m', strtotime($request->invoice_month . '-01'));
            $schedulesQuery = (new Schedule())->setConnection('tenant')->where('billing_month_year', $invoiceMonth);
            if ($tenant) {
                $schedulesQuery->where('tenant_id', $request->tenant_id);
            }
            $schedules = $schedulesQuery->where(function ($query) {
                $query->where('invoice_status', 'pending')
                    ->orWhereNull('invoice_status');
            })->get();
            $lastInvoiceNumber = (new Invoice())->setConnection('tenant')->orderBy('invoice_number', 'desc')->first();
            $invoice_number    = $lastInvoiceNumber
                ? 'INV-' . sprintf('%05d', (int) str_replace('INV-', '', $lastInvoiceNumber->invoice_number) + 1)
                : 'INV-00001';
            if ($schedules) {
                $invoice = (new Invoice())->setConnection('tenant')->create([
                    'invoice_number'     => InvoiceNumber(),
                    'tenant_id'          => $request->tenant_id ?? 0,
                    'invoice_type'       => $request->invoice_type,
                    'invoice_date'       => $formattedDate,
                    'invoice_month_year' => $request->invoice_month,
                    'status'             => "unpaid",
                    'total'              => 0,
                ]);

                // create invoice items if there is tenant
                $grand_total = 0;
                if (isset($invoice)) {
                    $options['to'] = $tenant->whatsapp_no ?? "+201150099801";
                    // $options['to'] = $tenant->whatsapp_no ?? $tenant->contact_no;
                    $options['message'] = "Hello Dear " . ($tenant->name ?? $tenant->company_name) . " this your invoice";
                    $pdf = Pdf::loadView('admin-views.invoices-formats.format-1', [
                        'invoice' => $invoice->load('items', 'tenant')
                    ]);

                    $fileName = 'invoice_' . $invoice->id . '.pdf';

                    $path = public_path('invoices/' . $fileName);

                    if (!file_exists(public_path('invoices'))) {
                        mkdir(public_path('invoices'), 0777, true);
                    }
                    file_put_contents($path, $pdf->output());

                    $pdfUrl = url('invoices/' . $fileName);

                    $options['file'] = $pdfUrl;

                    sendWhatsApp($options);

                    foreach ($schedules as $schedule) {
                        $schedule->update([
                            'invoice_status' => 'invoiced',
                        ]);
                        $rent = (float) ($schedule->rent_amount ?? 0);
                        $vat  = (float) ($schedule->vat_amount ?? 0); 


                        $invoice_item = (new InvoiceItems())->setConnection('tenant')->create([
                            'invoice_id'     => $invoice->id,
                            'agreement_id'   => $schedule->agreement_id,
                            'vat'            => $schedule->vat_amount,
                            'unit_id'        => $schedule->unit_id,
                            'building_id'    => $schedule->building_id ?? $schedule->main_unit->property_management_id,
                            'tenant_id'      => $schedule->tenant_id,
                            'rent_amount'    => ($schedule->rent_amount),
                            'service'        => $schedule->total_service_amount,
                            'vat_percentage' => $schedule->vat,
                            // 'total'          => number_format($schedule->rent_amount ?? 0) + number_format($schedule->vat_amount ?? 0),
                            // 'building_id'  => $schedule->main_unit->property_management_id,
                            'total' => $rent + $vat,
                            'category'       => $schedule->category ?? 'rent',
                            'balance_due'    => ($schedule->rent_amount + ((isset($schedule->vat_amount)) ? $schedule->vat_amount : 0)),

                        ]);
                        $grand_total += $invoice_item->total;
                    }
                } else {
                    Toastr::error('you_dont_have_invoices_in_this_month');
                    return back()->with('error', ui_change('you_dont_have_invoices_in_this_month'));
                }
                $invoice->update([
                    'total' => $grand_total,
                ]);
                // $tenant->update([
                //     'debit' => $grand_total,
                // ]);
            } else {
                Toastr::error('you_dont_have_invoices_in_this_month');
                return back()->with('error', ui_change('you_dont_have_invoices_in_this_month'));
            }
            return redirect()->route('invoices.all_invoices')->with('success', 'Invoice Added Successfully');
        }
        DB::commit();

        return redirect()->route('invoices.all_invoices')
            ->with('success', 'Invoice Added Successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            return back()->with('error', 'Something went wrong');
        }
    }
    public function paid(Request $request)
    {
        $ids = $request->bulk_ids;
        if ($request->bulk_action_btn === 'update_status' && $request->status && is_array($ids) && count($ids)) {
            $data = ['status' => $request->status];

            $invoiceUpdated = (new Invoice())->setConnection('tenant')->whereIn('id', $ids)->update($data);
            return back()->with('success', __('updated successfully'));
        }
        $invoices = (new Invoice())->setConnection('tenant')->where('status', 'paid')->get();
        return view('invoices.paid', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'    => "required",
            'invoice_date' => "required",
            'prefix'       => "required",
        ]);
        if ($request->tenant_id != 0) {

            $agreements = (new AgreementDetails())->setConnection('tenant')->where('tenantId', $request->tenant_id)->get();
        }
        foreach ($agreements as $item) {

            $OriginalAgreementDetail = (new AgreementDetails())->setConnection('tenant')->where('originalAgreementId', $item->id)->first();
            $ProposalUnitsDetial     = (new AgreementUnits())->setConnection('tenant')->where('proposalId', $item->proposalId)->first();
            $rent                    = $ProposalUnitsDetial->RentMode;
            $invoices_later          = (new Invoice())->setConnection('tenant')->where('proposalId', $item->proposalId)->first();
            if ($rent == 1 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                for ($i = 0, $ii = 12; $i < $ii; $i++) {
                    $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "$i" . '-' . uniqid();
                    $itemArray['due']            = Carbon::now()->addMonths($i);
                    (new Invoice())->setConnection('tenant')->create($itemArray);
                }
            } elseif ($rent == 2 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                for ($i = 0, $ii = 12; $i < $ii; $i++) {
                    $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "$i" . '-' . uniqid();
                    $itemArray['due']            = Carbon::now()->addMonths($i);
                    (new Invoice())->setConnection('tenant')->create($itemArray);
                }
            } elseif ($rent == 3 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                for ($i = 0, $ii = 6; $i < 6; $i++) {
                    $months                      = [0, 2, 4, 6, 8, 10, 12];
                    $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "$i" . '-' . uniqid();
                    $itemArray['due']            = Carbon::now()->addMonths($months[$i]);
                    (new Invoice())->setConnection('tenant')->create($itemArray);
                }
            } elseif ($rent == 4 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                for ($i = 0, $ii = 4; $i < $ii; $i++) {
                    $months                      = [0, 3, 6, 9, 12];
                    $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "$i" . '-' . uniqid();
                    $itemArray['due']            = Carbon::now()->addMonths($months[$i]);
                    (new Invoice())->setConnection('tenant')->create($itemArray);
                }
            } elseif ($rent == 5 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                for ($i = 0, $ii = 2; $i < $ii; $i++) {
                    $months                      = [0, 6, 12];
                    $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "$i" . '-' . uniqid();
                    $itemArray['due']            = Carbon::now()->addMonths($months[$i]);
                    (new Invoice())->setConnection('tenant')->create($itemArray);
                }
            } elseif ($rent == 6 && ! isset($invoices_later)) {
                $itemArray['invoice_date']         = Carbon::now();
                $itemArray['tenantId']             = $item->tenantId;
                $itemArray['tenant_type']          = $item->tenant_type;
                $itemArray['required_units_no']    = $item->required_units_no;
                $itemArray['enquiryId']            = $item->enquiryId;
                $itemArray['enquiry_no']           = $item->enquiry_no;
                $itemArray['enquiry_date']         = $item->enquiry_date;
                $itemArray['proposalId']           = $item->proposalId;
                $itemArray['proposal_no']          = $item->proposal_no;
                $itemArray['proposal_date']        = $item->proposal_date;
                $itemArray['bookingId']            = $item->bookingId;
                $itemArray['booking_no']           = $item->booking_no;
                $itemArray['booking_date']         = $item->booking_date;
                $itemArray['agreement_no']         = $item->agreement_no;
                $itemArray['agreement_date']       = $item->agreement_date;
                $itemArray['BusinessActivity']     = $item->BusinessActivity;
                $itemArray['ContactPerson']        = $item->ContactPerson;
                $itemArray['WhatsappNumber']       = $item->WhatsappNumber;
                $itemArray['ContactNumber']        = $item->ContactNumber;
                $itemArray['work_in']              = $item->work_in;
                $itemArray['FaxNo']                = $item->FaxNo;
                $itemArray['TelephoneNo']          = $item->TelephoneNo;
                $itemArray['Email']                = $item->Email;
                $itemArray['AddressLine1']         = $item->AddressLine1;
                $itemArray['employee']             = $item->employee;
                $itemArray['BudgetPerSq']          = $item->BudgetPerSq;
                $itemArray['BudgetPerMonth']       = $item->BudgetPerMonth;
                $itemArray['StaffStrength']        = $item->StaffStrength;
                $itemArray['TimeFrame']            = $item->TimeFrame;
                $itemArray['RelocationDate']       = $item->RelocationDate;
                $itemArray['status']               = $item->status;
                $itemArray['status_to_terminated'] = $item->status_to_terminated;
                $itemArray['status_to_expired']    = $item->status_to_expired;
                $itemArray['cancellation_reason']  = $item->cancellation_reason;
                $itemArray['companyId']            = $item->companyId;
                $itemArray['order_by']             = $item->order_by;
                $itemArray['billing_status']       = $item->billing_status;
                $itemArray['invoice_mode']         = $item->invoice_mode;
                $itemArray['billing_property_id']  = $item->billing_property_id;
                $itemArray['billing_unit_id']      = $item->billing_unit_id;

                // for ($i = 0, $ii = 2; $i < $ii; $i++) {
                $itemArray['invoice_number'] = $request->prefix . Str::slug($item->enquiry_no, '-') . "1" . '-' . uniqid();
                $itemArray['due']            = Carbon::now();
                (new Invoice())->setConnection('tenant')->create($itemArray);
                // }
            }
        }

        return redirect()->route('invoices.all_invoices')->with('success', 'Generated successfully');
    }

    public function create(Request $request)
    {
        $request->validate([
            'tenant_id'     => "required",
            'invoice_date'  => "required",
            'invoice_month' => "required",
        ]);
        // dd($request->all());
        $date = explode('-', $request->invoice_month);
        // dd( $request->invoice_month);
        $month         = $date[0];
        $year          = $date[1];
        $invoice_month = $date[0];
        if ($request->tenant_id == 0) {
            $schedules = (new Schedule())->setConnection('tenant')->whereMonth('billing_month_year', $month)
                ->whereYear('billing_month_year', $year)
                ->get();
            $tenant = null;
        } else {
            $schedules = (new Schedule())->setConnection('tenant')->whereMonth('billing_month_year', $month)
                ->whereYear('billing_month_year', $year)
                ->where('tenant_id', $request->tenant_id)->get();
            $tenant = (new Tenant())->setConnection('tenant')->where('id', $request->tenant_id)->first();
        }
        // dd($schedules);
        // $original = OriginalAgreementTransaction::where('originalAgreementId',$schedules[0]->agreement_number)->first();
        // $original2 = OriginalAgreementTransactionServiceCharge::where('original_agreement_transaction_id',$original->id)->first();
        // dd($original->id);
        // dd($original2);
        return view('invoices.generate_invoice', compact('schedules', 'tenant', 'invoice_month'));
    }

    public function generate_invoice($id)
    {
        $invoice = Invoice::with('tenant', 'items')->findOrFail($id);
        // $invoice_settings = InvoiceSettings::
        $invoice_settings    = InvoiceSettings::where('invoice_type', 'LIKE', "%{$invoice->invoice_type}%")->first();
        $company_settings    = CompanySettings::first();
        $company             = Company::first();

        $html = view('invoices.format-1', compact('invoice', 'company', 'company_settings', 'invoice_settings'))->render();

        $mpdf = new Mpdf([
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'default_font' => 'dejavusans',
            // 'img_dpi' => 96,
        ]);
        $mpdf->shrink_tables_to_fit = 1;

        $mpdf->WriteHTML($html);

        return $mpdf->Output("invoice-{$invoice->id}.pdf", 'I');
    }
}
