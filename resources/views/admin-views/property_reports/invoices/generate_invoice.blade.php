@extends('layouts.back-end.app')

@section('title', ui_change('print', 'property_report'))
@php
    $company =
        (new App\Models\Company())
            ->setConnection('tenant')
            ->where('id', auth()->user()?->company_id)
            ->first() ?? (new App\Models\Company())->setConnection('tenant')->first();
    $lang = session()->get('locale');
    $dir = session()->get('direction');
    $signature_mode = App\Models\CompanySettings::where('type', 'signature_mode')->first();
    $width = App\Models\CompanySettings::where('type', 'width')->first();
    $height = App\Models\CompanySettings::where('type', 'height')->first(); 
@endphp
@push('css_or_js')
    <style>
        .section_two {
            margin-right: 50px;
            margin-left: 50px;
        }

        .company-info {
            max-width: 50%;
        }

        .company-info h2 {
            margin: 0;
            font-size: 18px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 14px;
        }

        .logo {
            width: 150px;

        }

        .partner-logos img {
            height: 50px;
            margin: 0 5px;

        }

        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .invoice-container-two {
            margin: auto;
            border: 1px solid #000;
            padding: 10px;
        }

        .title-two {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .invoice-header-two {
            display: flex;
            justify-content: space-between;
            border: 1px solid #000;
            background: #ddd;
            padding: 8px;
            font-weight: bold;
        }

        .invoice-body-two {
            display: flex;
            justify-content: space-between;
            border: 1px solid #000;
            padding: 10px;
        }

        .invoice-section-two {
            width: 48%;
        }

        .bold-two {
            font-weight: bold;
        }

        .invoice {
            margin-right: 50px;
            margin-top: 20px;

            margin-left: 50px;
            border-collapse: collapse;
        }

        .invoice th,
        .invoice td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .invoice th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
        }

        .section {
            margin-top: 15px;
        }

        .amount-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
        }
            .bank-details p {
        margin: 2px 0; 
        line-height: 1.3;
    }

    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <button class="btn btn--primary mt-3" onclick="printInvoice()">{{ ui_change('print', 'property_report') }}</button>
        {{-- <a class="btn btn--primary mt-3" target="_blank" href="{{ route('invoice.pdf' , $invoice->id) }}">{{ ui_change('print', 'property_report') }}</a> --}}


        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card " id="printableArea"> 
                    @if (isset($invoice_settings))
                        @if ($invoice_settings->invoice_with_logo == 'yes')
                            @if ($invoice_settings->invoice_logo_position == 'right')
                                @include('admin-views.property_reports.invoices.includes.logo_right', [
                                    'invoice_settings' => $invoice_settings,
                                ])
                            @elseif($invoice_settings->invoice_logo_position == 'left')
                                @include('admin-views.property_reports.invoices.includes.logo_left', [
                                    'invoice_settings' => $invoice_settings,
                                ])
                            @elseif($invoice_settings->invoice_logo_position == 'middle')
                                @include('admin-views.property_reports.invoices.includes.logo_middle', [
                                    'invoice_settings' => $invoice_settings,
                                ])
                            @endif
                        @else
                        @endif
                    @else
                        @include('admin-views.property_reports.invoices.includes.logo_right')

                    @endif

                    <div class="section_two mt-5">
                        {{-- <div class="invoice-container-two"> --}}
                        <div class="title-two">
                            @if (isset($invoice_settings))
                                {{ $invoice_settings->invoice_name }}
                            @else
                                {{ 'TAX INVOICE' }}
                            @endif
                        </div>

                        <div class="invoice-header-two">
                            <span>TO:</span>
                            <span></span>
                        </div>

                        <div class="invoice-body-two">
                            <div class="invoice-section-two"> 
                                <p class="bold-two">
    @if($tenant)
        {{ $tenant->type == 'individual' 
            ? ($tenant->name ?: ui_change('not_available', 'property_report')) 
            : ($tenant->company_name ?: ui_change('not_available', 'property_report')) }}
    @else
        {{ ui_change('not_available', 'property_report') }}
    @endif
</p>

                                @if (isset($tenant->address1))
                                    <p>{{ $tenant->address1 }}</p>
                                @endif
                                @if (isset($tenant->address2))
                                    <p>{{ $tenant->address2 }}</p>
                                @endif
                                @if (isset($tenant->country_id))
                                    <p>{{ ui_change('country', 'property_report') }} :
                                        {{ $tenant->country_master->country->name }}</p>
                                @endif
                                @if (isset($tenant->contact_person))
                                    <p>{{ ui_change('contact_person', 'property_report') }} : {{ $tenant->contact_person }}
                                    </p>
                                @endif
                                @if (isset($tenant->email1))
                                    <p>{{ ui_change('email', 'property_report') }} : {{ $tenant->email1 }}</p>
                                @endif
                                @if (isset($tenant->telephone_no))
                                    <p>{{ ui_change('phone', 'property_report') }} : {{ $tenant->telephone_no }}</p>
                                @endif
                            </div>

                            <div class="invoice-section-two">
                                <p><span class="bold-two">{{ ui_change('invoice_number', 'property_report') }}:</span> <span
                                        class="bold-two">{{ $invoice->invoice_number }}</span></p>
                                <p><span class="bold-two">{{ ui_change('invoice_date', 'property_report') }}:</span>
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('j-M-Y') }} </p>
                                <p><span class="bold-two">{{ ui_change('invoice_for_period', 'property_report') }}:</span>
                                    {{ \Carbon\Carbon::parse($period->commencement_date)->format('j-M-Y') }} to {{ \Carbon\Carbon::parse($start_date)->format('j-M-Y') }}</p>
                                <p><span class="bold-two">{{ ui_change('agreement_no', 'property_report') }}:</span>
                                    {{ $agreement->agreement_no }}</p>
                                <p><span class="bold-two">{{ ui_change('buildings', 'property_report') }}:</span>
                                    {{ implode(', ', $buildings) }}</p>
                                {{-- <p><span class="bold-two">{{ ui_change('','property_report')('collections.invoice_type') }}:</span>
                                    {{ $invoice->invoice_type }}</p> --}}
                                {{-- <p><span class="bold-two">Payment Terms:</span></p>
                                <p><span class="bold-two">SO/PI Ref & Date:</span> End of List</p>
                                <p><span class="bold-two">DN Ref & Date:</span> End of List</p> --}}
                            </div>
                        </div>
                    </div>


                    <table class="invoice">
                        <tr>
                            <th>{{ ui_change('sl', 'property_report') }}</th>
                            {{-- <th>{{ ui_change('agreement_no', 'property_report') }}</th> --}}
                            <th>{{ ui_change('category', 'property_report') }}</th>
                            <th>{{ ui_change('unit_description', 'property_report') }}</th>
                            <th>{{ ui_change('amount_Exl. VAT', 'property_report') . ' ( ' . $company->currency_code . ' )' }}
                            </th>
                            <th>{{ ui_change('VAT', 'property_report') }} %</th>
                            <th>{{ ui_change('VAT_amount', 'property_report') }}</th>
                            <th>{{ ui_change('net_total_amount', 'property_report') }}</th>
                        </tr>
                        @php
                            $sub_total = 0;
                            $total_vat = 0;
                        @endphp
                        @foreach ($invoice_items as $k => $invoice_item_main)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                {{-- <td>{{ $invoice_item_main->agreement->agreement_no }}</td> --}}
                                <td>{{ ucfirst($invoice_item_main->category) }}</td>
                                <td>
                                    {{-- @if ($invoice_item_main->category == 'rent') --}}
                                    {{   $invoice_item_main->unit_management->block_unit_management->block->name .
                                        '-' .
                                        $invoice_item_main->unit_management->floor_unit_management->floor_management_main->name .
                                        '- ' .
                                        $invoice_item_main->unit_management->unit_management_main->name }}
                                    {{-- {{ $invoice_item_main->building->name .
                                        '-' .
                                        $invoice_item_main->unit_management->block_unit_management->block->name .
                                        '-' .
                                        $invoice_item_main->unit_management->floor_unit_management->floor_management_main->name .
                                        '-' .
                                        $invoice_item_main->unit_management->unit_management_main->name }} --}}
                                    {{-- @else
                                        {{ optional((new App\Models\ServiceMaster())->setConnection('tenant')->where('id', $invoice_item_main->service_id)->first())->name }}
                                    @endif --}}
                                </td>
                                <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($invoice_item_main->rent_amount, $company->decimals ?? 2) }}</td>
                                <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($invoice_item_main->vat_percentage ?? 0) }}
                                </td> 
                                <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($invoice_item_main->vat , $company->decimals ?? 2) }}</td>
                                <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($invoice_item_main->total, $company->decimals ?? 2) }}</td>
                                @php
                                    $sub_total += $invoice_item_main->rent_amount;
                                    $total_vat += $invoice_item_main->vat;
                                @endphp
                            </tr>
                        @endforeach
                    </table>
                    <table class="invoice">
                        <tr>
                            <td class="total">{{ ui_change('Sub_Total', 'property_report') }}
                                {{ ' ( ' . $company->currency_code . ' )' }} : {{ amount_in_words($sub_total  , $company->decimals) }}</td>
                            <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($sub_total, $company->decimals ?? 2) }} </td>
                        </tr>
                        <tr>
                            <td class="total">{{ ui_change('VAT_amount', 'property_report') }}
                                {{ ' ( ' . $company->currency_code . ' )' }} : {{ amount_in_words(  $total_vat, $company->decimals) }}</td>
                            <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($total_vat, $company->decimals ?? 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total">{{ ui_change('Grand_Total', 'property_report') }}
                                {{ ' ( ' . $company->currency_code . ' )' }}  {{ amount_in_words($sub_total + $total_vat, $company->decimals) }}</td>
                            <td  @if($dir == 'rtl') style="text-align: left;" @else  style="text-align: right;"  @endif>{{ number_format($sub_total + $total_vat, $company->decimals ?? 2) }}</td>
                        </tr>
                    </table>
                    {{-- <div class="invoice">
                        <div class="amount-box">
                            <span>{{ ui_change('Sub_Total_Amount_in_words', 'property_report') }} ({{ $company->currency_code }})
                                {{ amount_in_words($sub_total  , $company->decimals) }}</span>
                        </div>
                        <div class="amount-box">
                            <span>{{ ui_change('VAT_Amount_in_words', 'property_report') }} ({{ $company->currency_code }})
                                {{ amount_in_words(  $total_vat, $company->decimals) }}</span>
                        </div>
                        <div class="amount-box">
                            <span>{{ ui_change('Amount_in_words', 'property_report') }} ({{ $company->currency_code }})
                                {{ amount_in_words($sub_total + $total_vat, $company->decimals) }}</span>
                        </div>
                    </div> --}}
                    <div class="row d-flex align-items-start m-5">

                        @if (isset($invoice_settings->ledger->account_name))
                            <div class="bank-details col-6  ">
                                <p><strong>Bank Details :-</strong></p>
                                <p>{{ ui_change('account_name', 'property_report') }}:
                                    <strong>{{ $invoice_settings->ledger?->account_name }}</strong>
                                </p>
                                <p>{{ ui_change('bank_name', 'property_report') }}:
                                    <strong>{{ $invoice_settings->ledger?->bank_name }}</strong>
                                    &nbsp; {{ ui_change('branch', 'property_report') }}: <strong>
                                        {{ $invoice_settings->ledger?->branch }}
                                    </strong>
                                </p>
                                <p>{{ ui_change('account_no', 'property_report') }}:
                                    <strong>{{ $invoice_settings->ledger?->account_no }}</strong>
                                </p>
                                <p>{{ ui_change('swift_code', 'property_report') }}:
                                    <strong>{{ $invoice_settings->ledger?->swift_code }}</strong>
                                </p>
                                <p>{{ ui_change('iban_no', 'property_report') }}:
                                    <strong>{{ $invoice_settings->ledger?->iban_no }}</strong>
                                </p>
                            </div>
                        @endif
                        @if (isset($company->signature) || isset($company->seal))


                            <div class="col-6 d-flex justify-content-end">
                                <div id="signature-pads-container">
                                    @if ($signature_mode->value == 'digital')
                                        <img width="{{ $width->value . 'px' ?? '300px' }}"
                                            height="{{ $height->value . 'px' ?? '150px' }}" id="signature_img"
                                            src="{{ $company->signature }}" alt="Signature">
                                    @else
                                        <img width="{{ $width->value . 'px' ?? '300px' }}"
                                            height="{{ $height->value . 'px' ?? '150px' }}" id="seal"
                                            src="{{ asset('seal/' . $company->name . '/' . $company->seal) }}"
                                            alt="seal">
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- <div class="col-md-12">
                        <hr class="custom-class">
                        <div class="pull-right mt-5 {{ Session::get('locale') == 'en' ? 'text-right' : 'text-left' }}">
                            <address>

                                <p class="m-t-30"><b>{{ ui_change('','property_report')('general.created_at') }}</b> <i class="fa fa-calendar"></i>
                                    {{ $invoice->created_at->shortAbsoluteDiffForHumans() }}</p>
                            </address>
                        </div>
                    </div> --}}


                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        function printDiv() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
    <script>
        function printInvoice() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endpush

{{-- <div class="col-md-12 printableArea">
    {!! clean_html($invoice->content) !!}
    @if (isset($invoice->signature))
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;margin-top:30px">
            @foreach ($invoice->signature as $signature)
                <div style="text-align: center; width: 48%;">
                    <h4>{{ App\Models\User::where('id', $signature->user_id)->first()->name }}</h4>
                    <img width="100px" height="100px" src="{{ $signature->image }}"
                        alt="Signature">
                </div>
            @endforeach
        </div>
    @endif --}}



{{-- @if ($invoice->signature->isNotEmpty())
                    @foreach ($invoice->signature as $signature)
                        <h4>{{ App\Models\User::where('id' , $signature->user_id)->first()->name }}</h4>
                        <img width="100px" height="100px" src="{{ $signature->image }}" alt="Signature">
                    @endforeach
                @endif --}}

{{-- </div> --}}
