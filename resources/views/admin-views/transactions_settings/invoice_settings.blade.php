@extends('layouts.back-end.app')
@php
    $currentUrl = url()->current();
    $segments = explode('/', $currentUrl);
    $end = end($segments);
    $lang = Session::get('locale');

@endphp
@section('title', ui_change('invoice_settings'))

@push('css_or_js')
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/custom.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->

        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ ui_change('invoice_settings') }}
            </h2>

        </div>
        <!-- End Page Title -->
        @include('admin-views.transactions_settings.business-setup-inline-menu')

        <!-- Inlile Menu -->
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">

                                {{ ui_change('invoice_settings') }}
                            </h2>
                            <button class="btn btn--primary mr-2" data-add_new_ledger="" data-toggle="modal"
                                data-target="#add_new_ledger">{{ ui_change('add_new_settings') }}</button>
                        </div>
                    </div>
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                <h5 class="mb-0 d-flex align-items-center gap-2">
                                    {{ ui_change('invoice_settings') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12"> </span>
                                </h5>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ ui_change('search_by_settings_name') }}"
                                            aria-label="Search" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn--primary">{{ ui_change('search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{ ui_change('sl') }}</th>
                                        <th class="text-center">{{ ui_change('name') }} </th>
                                        <th class="text-center">{{ ui_change('applicable_from') }} </th>
                                        <th class="text-center">{{ ui_change('total_digit') }}</th>
                                        <th class="text-center">{{ ui_change('start_number') }}</th>
                                        <th class="text-center">{{ ui_change('prefix') }}</th>
                                        <th class="text-center">{{ ui_change('sufix') }}</th>
                                        {{-- <th class="text-center">{{ ui_change('invoice_type') }}</th> --}}
                                        <th class="text-center">{{ ui_change('ledger') }}</th>
                                        <th class="text-center">{{ ui_change('invoice_with_logo') }}</th>
                                        <th class="text-center">{{ ui_change('invoice_logo_position') }}</th>
                                        <th class="text-center">{{ ui_change('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice_settings as $key => $value)
                                        <tr>
                                            <td>{{ $invoice_settings->firstItem() + $key }}</td>
                                            <td class="text-center">{{ $value->invoice_name }}</td>
                                            <td class="text-center">{{ $value->invoice_date }} </td>
                                            <td class="text-center">{{ $value->invoice_width }} </td>
                                            <td class="text-center">{{ $value->invoice_start_number }} </td>
                                            <td class="text-center">{{ $value->invoice_prefix }} </td>
                                            <td class="text-center">{{ $value->invoice_suffix }} </td>
                                            {{-- <td class="text-center">{{ $value->invoice_type }} </td> --}}
                                            <td class="text-center">
                                                {{ $value->ledger->name ?? ui_change('not_applicable') }} </td>
                                            <td class="text-center">{{ $value->invoice_with_logo }} </td>
                                            <td class="text-center">
                                                {{ $value->invoice_with_logo == 'yes' ? $value->invoice_logo_position : ui_change('not_available') }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a id="edit_receipt_settings_item"
                                                        class="btn btn-outline-info btn-sm square-btn"
                                                        title="{{ ui_change('edit') }}"
                                                        data-receipt_settings_id="{{ $value->id }}"
                                                        data-target="#edit_receipt_settings">
                                                        <i class="tio-edit"></i>
                                                    </a>
                                                    <a class="btn btn-outline-danger btn-sm delete square-btn"
                                                        title="{{ ui_change('delete') }}" id="{{ $value['id'] }}">
                                                        <i class="tio-delete"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {!! $invoice_settings->links() !!}
                        </div>
                    </div>

                    @if (count($invoice_settings) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="add_new_ledger" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('add_new_settings') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('invoice_settings.store') }}" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="row">
                              <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_name') }}</label>
                                    <input type="text" class="form-control" name="invoice_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token" class="title-color">{{ ui_change('prefix') }}</label>
                                    <input type="text" class="form-control" name="invoice_prefix">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('sufix') }}</label>
                                    <input type="text" class="form-control" name="invoice_suffix"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token" class="title-color">{{ ui_change('total_digit') }}</label>
                                    <input type="number" class="form-control" name="invoice_width">
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('start_number') }}</label>
                                    <input type="number" class="form-control" name="invoice_start_number"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('start_date') }}</label>
                                    <input type="text" class="form-control" id="invoice_start_date"
                                        name="invoice_date" class="form-control">
                                </div>
                            </div>
                          
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('defualt_bank_account') }}</label>
                                    <select name="ledger_id" class="js-select2-custom form-control">
                                        <option value="0">{{ ui_change('not_applicable') }}</option>
                                        @foreach ($ledgers as $ledger_item)
                                            <option value="{{ $ledger_item->id }}">
                                                {{ $ledger_item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token"
                                        class="title-color">{{ ui_change('invoice_type') }}</label>
                                    <input type="text" class="form-control" name="invoice_type">
                                </div>
                            </div> --}}
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_with_logo') }}</label>
                                    <select name="invoice_with_logo" class="js-select2-custom form-control">
                                        <option value="yes">
                                            {{ ui_change('yes') }}
                                        </option>
                                        <option value="no">
                                            {{ ui_change('no') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_logo_position') }}</label>
                                    <select name="invoice_logo_position" class="js-select2-custom form-control">
                                        <option value="right">
                                            {{ ui_change('right') }}
                                        </option>
                                        <option value="left">
                                            {{ ui_change('left') }}
                                        </option>
                                        <option value="middle">
                                            {{ ui_change('middle') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('width') }}</label>
                                    <input type="number" class="form-control" name="width" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('height') }}</label>
                                    <input type="number" class="form-control" name="height" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('format') }}</label>
                                    <select name="invoice_format" class="js-select2-custom form-control">
                                        <option value="format-1">
                                            {{ ui_change('format') . ' 1' }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div class="row"> 
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('format_color') }}</label>
                                    <input type="color" class="form-control" name="format_color" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('background_color') }}</label>
                                    <input type="color" class="form-control" name="background_color"  class="form-control">
                                </div>
                            </div>


                            @php
                                $allFields = [
                                    'company_email' => 'Display Company Email',
                                    'company_phone' => 'Display Company Phone',
                                    'company_fax' => 'Display Company Fax',
                                    'company_address' => 'Display Company Address',
                                    'company_vat_no' => 'Display Company VAT No.',

                                    'tenant_email' => 'Display Customer Email',
                                    'tenant_phone' => 'Display Customer Phone',
                                    'tenant_fax' => 'Display Customer Fax',
                                    'tenant_address' => 'Display Customer Address',
                                    'tenant_vat_no' => 'Display Customer VAT No.',
                                ];
                                $half_size = ceil(count($allFields) / 2);
                                $firstHalf = array_slice($allFields, 0, $half_size, true);
                                $secondHalf = array_slice($allFields, $half_size, null, true);
                            @endphp

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <h3>{{ ui_change('Company Details') }}</h3>
                                @foreach ($firstHalf as $name => $label)
                                    <div style="margin-bottom: 10px;"> 
                                        <input type="checkbox" name="{{ $name }}" value="1"
                                              @if (isset($settings) && $settings->{$name} == 1) checked @endif>
                                        <label for="{{ $name }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <h3>{{ ui_change('Customer Details') }}</h3>
                                @foreach ($secondHalf as $name => $label)
                                    <div style="margin-bottom: 10px;"> 
                                        <input type="checkbox" name="{{ $name }}" value="1"
                                              @if (isset($settings) && $settings->{$name} == 1) checked @endif>
                                        <label for="{{ $name }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('qr_code_width') }}</label>
                                    <input type="number" class="form-control" name="qr_code_width"    class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('qr_code_height') }}</label>
                                    <input type="number" class="form-control" name="qr_code_height"  class="form-control">
                                </div>
                            </div>






                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ ui_change('cancel') }}</button>
                        <button type="submit" class="btn btn--primary">{{ ui_change('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="modal fade" id="edit_receipt_settings" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('edit_settings') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('invoice_settings.update') }}" method="post">
                    @csrf
                    @method('patch')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_name') }}</label>
                                    <input type="text" class="form-control" name="invoice_name"
                                        id="edit_invoice_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token" class="title-color">{{ ui_change('prefix') }}</label>
                                    <input type="text" class="form-control" name="invoice_prefix" id="edit_prefix">
                                    <input type="hidden" class="form-control" name="edit_invoice_settings_id"
                                        id="edit_invoice_settings_id">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('sufix') }}</label>
                                    <input type="text" class="form-control" name="invoice_suffix" id="edit_suffix"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token" class="title-color">{{ ui_change('total_digit') }}</label>
                                    <input type="number" class="form-control" name="invoice_width" id="edit_width">
                                </div>
                            </div>
                            {{-- <div class="col-md-6 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="token"
                                        class="title-color">{{ ui_change('invoice_type') }}</label>
                                    <input type="text" class="form-control" name="invoice_type"
                                        id="edit_invoice_type">
                                </div>
                            </div> --}}
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('start_number') }}</label>
                                    <input type="number" class="form-control" name="invoice_start_number"
                                        id="edit_start_number" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('start_date') }}</label>
                                    <input type="text" class="form-control" id="edit_invoice_start_date"
                                        name="invoice_date" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_with_logo') }}</label>
                                    <select name="invoice_with_logo" id="edit_invoice_with_logo"
                                        class="js-select2-custom form-control">
                                        <option value="yes">
                                            {{ ui_change('yes') }}
                                        </option>
                                        <option value="no">
                                            {{ ui_change('no') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('invoice_logo_position') }}</label>
                                    <select name="invoice_logo_position" id="edit_invoice_logo_position"
                                        class="js-select2-custom form-control">
                                        <option value="right">
                                            {{ ui_change('right') }}
                                        </option>
                                        <option value="left">
                                            {{ ui_change('left') }}
                                        </option>
                                        <option value="middle">
                                            {{ ui_change('middle') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('width') }}</label>
                                    <input type="number" class="form-control" name="width" id="edit_logo_width"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('height') }}</label>
                                    <input type="number" class="form-control" name="height" id="edit_height"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('format') }}</label>
                                    <select name="invoice_format" class="js-select2-custom form-control"
                                        id="edit_format">
                                        <option value="format-1">
                                            {{ ui_change('format') . ' 1' }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <label for="">{{ ui_change('defualt_bank_account') }}</label>
                                    <select name="ledger_id" id="edit_ledger_id" class="js-select2-custom form-control">
                                        <option value="0">{{ ui_change('not_applicable') }}</option>

                                        @foreach ($ledgers as $ledger_item)
                                            <option value="{{ $ledger_item->id }}">
                                                {{ $ledger_item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('format_color') }}</label>
                                    <input type="color" class="form-control" name="format_color" id="format_color"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('background_color') }}</label>
                                    <input type="color" class="form-control" name="background_color"
                                        id="background_color" class="form-control">
                                </div>
                            </div>


                            @php
                                $allFields = [
                                    'company_email' => 'Display Company Email',
                                    'company_phone' => 'Display Company Phone',
                                    'company_fax' => 'Display Company Fax',
                                    'company_address' => 'Display Company Address',
                                    'company_vat_no' => 'Display Company VAT No.',

                                    'tenant_email' => 'Display Customer Email',
                                    'tenant_phone' => 'Display Customer Phone',
                                    'tenant_fax' => 'Display Customer Fax',
                                    'tenant_address' => 'Display Customer Address',
                                    'tenant_vat_no' => 'Display Customer VAT No.',
                                ];
                                $half_size = ceil(count($allFields) / 2);
                                $firstHalf = array_slice($allFields, 0, $half_size, true);
                                $secondHalf = array_slice($allFields, $half_size, null, true);
                            @endphp

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <h3>{{ ui_change('Company Details') }}</h3>
                                @foreach ($firstHalf as $name => $label)
                                    <div style="margin-bottom: 10px;">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input type="checkbox" name="{{ $name }}" value="1"
                                            id="{{ $name }}" @if (isset($settings) && $settings->{$name} == 1) checked @endif>
                                        <label for="{{ $name }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <h3>{{ ui_change('Customer Details') }}</h3>
                                @foreach ($secondHalf as $name => $label)
                                    <div style="margin-bottom: 10px;">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input type="checkbox" name="{{ $name }}" value="1"
                                            id="{{ $name }}" @if (isset($settings) && $settings->{$name} == 1) checked @endif>
                                        <label for="{{ $name }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('qr_code_width') }}</label>
                                    <input type="number" class="form-control" name="qr_code_width"  id="qr_code_width" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('qr_code_height') }}</label>
                                    <input type="number" class="form-control" name="qr_code_height"  id="qr_code_height" class="form-control">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ ui_change('cancel') }}</button>
                        <button type="submit" class="btn btn--primary">{{ ui_change('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>


@endsection
@push('script')
    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{ __('general.are_you_sure_delete_this') }}",
                text: "{{ __('general.you_will_not_be_able_to_revert_this') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('general.yes_delete_it') }}!",
                cancelButtonText: "{{ __('general.cancel') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('invoice_settings.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success("{{ ui_change('deleted_successfully') }}");
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        flatpickr("#edit_invoice_start_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
        });

        flatpickr("#invoice_start_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
        });
    </script>
    <script>
        $(document).on('click', '#edit_receipt_settings_item', function(e) {
            e.preventDefault();
            var sect_id = $(this).data('receipt_settings_id');
            $('#edit_receipt_settings').modal('show');
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: "{{ route('invoice_settings.edit', ':id') }}".replace(':id', sect_id),
                success: function(response) {
                    if (response.status == 404) {
                        $('#success_message').html("");
                        $('#success_message').addClass("alert alert-danger");
                        $('#success_message').text(response.message);
                    } else {
                        let main_date = response.invoice_settings.applicable_date;
                        if (main_date) {
                            let formattedDate = moment(main_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                            $('#edit_invoice_start_date').val(formattedDate)
                        }
                        $('#edit_invoice_settings_id').val(sect_id)
                        $('#edit_invoice_name').val(response.invoice_settings.invoice_name)
                        $('#edit_prefix').val(response.invoice_settings.invoice_prefix)
                        $('#edit_suffix').val(response.invoice_settings.invoice_suffix)
                        $('#edit_start_number').val(response.invoice_settings.invoice_start_number)
                        $('#edit_width').val(response.invoice_settings.invoice_width)
                        // $('#edit_invoice_type').val(response.invoice_settings.invoice_type)
                        $('#edit_height').val(response.invoice_settings.height)
                        $('#edit_logo_width').val(response.invoice_settings.width)
                        $('#qr_code_height').val(response.invoice_settings.qr_code_height)
                        $('#qr_code_width').val(response.invoice_settings.qr_code_width)
                        // $('#edit_invoice_with_logo').val(response.invoice_settings.invoice_with_logo)
                        // $('#edit_invoice_logo_position').val(response.invoice_settings.invoice_logo_position)
                        let ledgerId = response.invoice_settings.ledger_id ?? 0;
                        $('#edit_ledger_id').val(ledgerId).trigger('change');

                        $('#edit_invoice_logo_position').val(response.invoice_settings
                            .invoice_logo_position).trigger('change');
                        $('#edit_invoice_with_logo').val(response.invoice_settings.invoice_with_logo)
                            .trigger('change');

                        $('#edit_format').val(response.invoice_settings.invoice_format)
                            .trigger('change');
                        $('#format_color').val(response.invoice_settings.format_color);
                        $('#background_color').val(response.invoice_settings.background_color);
 
                        let settings = response.invoice_settings;
 
                        let displayFields = [
                            'company_email', 'company_phone', 'company_fax', 'company_address',
                            'company_vat_no',
                            'tenant_email', 'tenant_phone', 'tenant_fax', 'tenant_address',
                            'tenant_vat_no',
                        ];
                        displayFields.forEach(function(fieldName) {
                            let value = settings[fieldName];
                            let checkbox = $('#' + fieldName);

                            // Set the checkbox property
                            if (value == 1) {
                                checkbox.prop('checked', true);
                            } else {
                                checkbox.prop('checked', false);
                            }
                        });
                        // let selectedLedgers = response.main_ledgers.map(l => l.id);
                        // // $('.js-select2-custom').val(selectedLedgers).trigger('change');
                        // let selectedLedgers = response.main_ledgers.map(l => l.main_ledger_id);
                        // $('.js-select2-custom').val(selectedLedgers).trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + error);
                }
            });

        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            $('input[name="sufix"]').on('keyup', function() {
                let sufix = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let prefix = $('input[name="prefix"]').val();
                let total_digit = $('input[name="total_digit"]').val();
                let start_number = $('input[name="start_number"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);
                }
            });
            $('input[name="prefix"]').on('keyup', function() {
                let prefix = $(this).val();
                let sufix = $('input[name="sufix"]').val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let total_digit = $('input[name="total_digit"]').val();
                let start_number = $('input[name="start_number"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);
                }
            });
            $('input[name="start_number"]').on('keyup', function() {
                let start_number = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let total_digit = $('input[name="total_digit"]').val();
                let prefix = $('input[name="prefix"]').val();
                let sufix = $('input[name="sufix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);
                }
            });
            $('input[name="total_digit"]').on('keyup', function() {
                let total_digit = $(this).val();
                let prefix_with_zero = $('select[name="prefix_with_zero"]').val();
                let start_number = $('input[name="start_number"]').val();
                let prefix = $('input[name="prefix"]').val();
                let sufix = $('input[name="sufix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);
                }
            });
            $('select[name="prefix_with_zero"]').on('change', function() {
                let prefix_with_zero = $(this).val();
                let total_digit = $('input[name="total_digit"]').val();
                let start_number = $('input[name="start_number"]').val();
                let prefix = $('input[name="prefix"]').val();
                let sufix = $('input[name="sufix"]').val();
                if (prefix_with_zero == 'yes') {
                    let paddedNumber = start_number.toString().padStart(total_digit, '0');

                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);

                } else {
                    let paddedNumber = start_number.toString().padStart(0, '0');
                    let result = $('input[name="result"]').val(prefix + paddedNumber + sufix);
                }
            });


        });
    </script> --}}
    <script>
        flatpickr("#applicable_from", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
        });
    </script>
@endpush
