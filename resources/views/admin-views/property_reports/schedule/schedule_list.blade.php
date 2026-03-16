@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
    $firstDay = Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
    $lastDay = Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
@endphp
@section('title', ui_change('pre_bill_checking', 'property_report'))

@push('css_or_js') 
    <link href="{{ asset(main_path() . 'date/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset(main_path() . 'date/date.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"> --}}
    
@endpush
@php
    $company = App\Models\User::first();
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt=""> --}}
                {{ ui_change('pre_bill_checking', 'property_report') }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $schedules->count() }}</span>
            </h2>
        </div>
        @include('admin-views.inline_menu.property_reports.inline-menu')
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">

                            <div class="col-lg-2 mt-3 mt-lg-0 ">
                                {{-- d-flex flex-wrap gap-3 justify-content-lg-end --}}
                                <button class="btn   btn-outline-primary " data-generate_invoice="" data-toggle="modal"
                                    data-target="#generate_invoice">{{ ui_change('Generate_Invoice', 'property_report') }}</button>
                            </div>
                            <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                class="btn btn--primary btn-sm">
                                <i class="fas fa-filter"></i>
                            </button>
                            {{-- <div class="col-lg-1  mt-3 mt-lg-0 ">
                                <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                    class="btn btn--primary btn-sm">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div> --}}
                            {{-- <div class="col-lg-2  mt-3 mt-lg-0 "> 
                                <button class="btn   btn-outline-primary " data-filter="" data-toggle="modal"
                                    data-target="#filter">{{ ui_change('filter','property_report')  }}</button>
                            </div> --}}

                        </div>

                        <div class="modal fade" id="generate_invoice" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ ui_change('Generate_Invoice', 'property_report') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('invoice_generate.store') }}" method="post">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <?php
                                                use Carbon\Carbon;
                                                $months = [];
                                                $currentMonth = Carbon::now()->subMonths(2);
                                                
                                                for ($i = 0; $i < 16; $i++) {
                                                    $months[$currentMonth->format('m-Y')] = $currentMonth->format('F Y');
                                                    $currentMonth->addMonth();
                                                }
                                                ?>

                                                <label for="">{{ ui_change('select', 'property_report') }}</label>
                                                <select name="tenant_id" id="" class="form-control">
                                                    <option value="0">{{ ui_change('all_tenants', 'property_report') }}
                                                    </option>
                                                    @foreach ($tenants as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->name ?? $item->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="">{{ ui_change('Invoice_Month', 'property_report') }}</label>
                                                <input type="month" name="invoice_month" id="invoice_month"
                                                    class="form-control">

                                                {{-- <select name="invoice_month" class="form-control" id="">
                                                    @foreach ($months as $key => $month)
                                                        <option value="{{ $key }}">{{ $month }}</option>
                                                    @endforeach
                                                </select> --}}
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="">{{ ui_change('building', 'property_report') }}</label>
                                                <select name="building_id" class="form-control" id="">
                                                    <option value="0">{{ ui_change('all') }}</option>
                                                    @foreach ($all_building as $building_item)
                                                        <option value="{{ $building_item->id }}">
                                                            {{ $building_item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    for="">{{ ui_change('Invoice_Date', 'property_report') }}</label>
                                                <input type="text" id="invoice_datee" name="invoice_date"
                                                    class="form-control date_picker"> </select>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{ ui_change('Cancel', 'property_report') }}</button>
                                            <button type="submit"
                                                class="btn btn--primary">{{ ui_change('Generate', 'property_report') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade " id="filter" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ ui_change('Generate_Invoice', 'property_report') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ url()->current() }}" method="get">
                                        @csrf
                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-6 col-lg-4 col-xl-6">
                                                    @php
                                                        $from = date('d/m/Y', strtotime(date('Y-m-d') . '-29 Days'));
                                                        $to = date('d/m/Y');
                                                    @endphp
                                                    <label class="{{ $lang == 'ar' ? 'mr-2' : 'ml-2' }} ">
                                                        {{ ui_change('Date_Form_-_To_:', 'property_report') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <input
                                                            class="form-control float-right date_picker {{ $lang == 'ar' ? 'mr-2' : 'ml-2' }}"
                                                            type="text"   name="start_date"
                                                            value="{{ $firstDay }}">
                                                       
                                                        <input
                                                            class="form-control date_picker float-right {{ $lang == 'ar' ? 'mr-2' : 'ml-2' }}"
                                                            type="text"   name="end_date"
                                                            value="{{ $lastDay }}">

                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 col-lg-4 col-xl-6">
                                                    <label for="">
                                                        {{ ui_change('building', 'property_report') }}
                                                    </label>
                                                    <select name="report_building" id="report_building"
                                                        class="form-control remv_focus" onchange="filterUnits()">
                                                        <option value="-1" selected>
                                                            {{ ui_change('All_Buildings', 'property_report') }}</option>
                                                        @foreach ($all_building as $building_filter)
                                                            <option value="{{ $building_filter->id }}">
                                                                {{ $building_filter->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-12 col-lg-4 col-xl-6">
                                                    <label for="">
                                                        {{ ui_change('unit', 'property_report') }}
                                                    </label>
                                                    <select name="report_unit_management" id="report_unit_management"
                                                        class="form-control remv_focus" disabled>
                                                        <option value="-1">
                                                            {{ ui_change('All_Units', 'property_report') }}</option>
                                                        @foreach ($unit_management as $unit_management_filter)
                                                            <option value="{{ $unit_management_filter->id }}"
                                                                data-building="{{ $unit_management_filter->property_management_id }}">
                                                                {{ $unit_management_filter->property_unit_management->code .
                                                                    '-' .
                                                                    $unit_management_filter->block_unit_management->block->code .
                                                                    '-' .
                                                                    $unit_management_filter->floor_unit_management->floor_management_main->name .
                                                                    '-' .
                                                                    $unit_management_filter->unit_management_main->name .
                                                                    '-' .
                                                                    ($unit_management_filter->unit_description->code ?? '') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-lg-4 col-xl-6">
                                                    <label for="">
                                                        {{ ui_change('tenants', 'property_report') }}
                                                    </label>
                                                    <select name="report_tenant" class="form-control select2">
                                                        <option value="-1">
                                                            {{ ui_change('all_tenants', 'property_report') }}</option>
                                                        @foreach ($tenants as $tenant_filter)
                                                            <option value="{{ $tenant_filter->id }}">
                                                                {{ $tenant_filter->name ?? $tenant_filter->company_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-6 col-lg-4 col-xl-6">
                                                    <div class="form-group">
                                                        <label for="">
                                                            {{ ui_change('invoice_status', 'property_report') }}
                                                        </label>
                                                        <select name="invoice_status" class="form-control select2">
                                                            <option value="-1">
                                                                {{ ui_change('all', 'property_report') }}</option>
                                                            <option value="pending">
                                                                {{ ui_change('Pending', 'property_report') }}</option>
                                                            <option value="invoiced">
                                                                {{ ui_change('Invoiced', 'property_report') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{ ui_change('Cancel', 'property_report') }}</button>
                                            <button type="submit" name="bulk_action_btn" class="btn btn--primary"
                                                value="filter">{{ ui_change('filter', 'property_report') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- {{ dd($firstDay) }} --}}
                    <form action="" method="get">


                        @php
                            $rent_mode_months = [
                                1 => 'Daily',
                                2 => 'Monthly',
                                3 => 'Bi-Monthly',
                                4 => 'Quarterly',
                                5 => 'Half-Yearly',
                                6 => 'Yearly',
                            ];
                        @endphp
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th><input id="bulk_check_all" class="bulk_check_all" type="checkbox" />
                                            {{ ui_change('sl', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('tenant', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('agreement_no', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('property', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('unit_name', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('services', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('billing_month_year', 'property_report') }}
                                        </th>
                                        <th class="text-center">{{ ui_change('rent_mode', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('net_total_amount', 'property_report') }}
                                        </th>
                                        <th class="text-center">{{ ui_change('currency_name', 'property_report') }}</th>
                                        <th class="text-center">{{ ui_change('invoice_status', 'property_report') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $k => $schedules_item)
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $schedules_item->id }}" />
                                                {{ $schedules->firstItem() + $k }}</th>

                                            <td class="text-center">
                                                {{ $schedules_item->tenant->type == 'individual' ? $schedules_item->tenant->name ?? ui_change('not_available', 'property_report') : $schedules_item->tenant->company_name ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->agreement->agreement_no ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->main_unit->property_unit_management->name ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($schedules_item->category == 'rent')
                                                    {{ $schedules_item->main_unit->property_unit_management->name .
                                                        '-' .
                                                        $schedules_item->main_unit->block_unit_management->block->name .
                                                        '-' .
                                                        $schedules_item->main_unit->floor_unit_management->floor_management_main->name .
                                                        '-' .
                                                        $schedules_item->main_unit->unit_management_main->name }}
                                                @else
                                                    {{ App\Models\ServiceMaster::where('id', $schedules_item->service_id)->first()->name }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($schedules_item->service == 'yes')
                                                    <a id="show_service_info" class="btn   "
                                                        title="{{ ui_change('show', 'property_report') }}"
                                                        data-receipt_settings_id="{{ $schedules_item->service_id }}"
                                                        data-target="#show_service_info">
                                                        {{ $schedules_item->service ?? ui_change('not_available', 'property_report') }}
                                                    </a>
                                                @else
                                                    {{ $schedules_item->service ?? ui_change('not_available', 'property_report') }}
                                                @endif

                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->billing_month_year ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $rent_mode_months[$schedules_item->rent_mode] ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            {{-- <td class="text-center">
                                                @php
                                                    $formatted_amount = number_format(
                                                        $schedules_item->total_amount,
                                                        $company->decimals ?? 3,
                                                        '.',
                                                        '',
                                                    );
                                                @endphp

                                                {{ $formatted_amount ?? ui_change('not_available', 'property_report') }}
                                            </td> --}}

                                            <td class="text-center">
                                                @php
                                                    $formatted_amount = number_format(
                                                        $schedules_item->rent_amount,
                                                        $company->decimals ?? 3,
                                                        '.',
                                                        '',
                                                    );
                                                @endphp
                                                {{ $formatted_amount ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->currency ?? ui_change('not_available', 'property_report') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->invoice_status ?? ui_change('not_available', 'property_report') }}
                                            </td>



                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {{ $schedules->links() }}
                        </div>
                    </div>

                    @if (count($schedules) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-160"
                                src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show', 'property_report') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_service_info_model" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('service_details', 'property_report') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="card-body">
                        <div style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                            <div class="table-responsive">
                                <table id="datatable"
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th class="text-center">{{ ui_change('services_name', 'property_report') }}
                                            </th>
                                            <th class="text-center">{{ ui_change('services_code', 'property_report') }}
                                            </th>
                                            <th class="text-center">{{ ui_change('vat_amount', 'property_report') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="service_master_item">
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>

                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">

                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ ui_change('cancel', 'property_report') }}</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset(main_path() . 'back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        $(document).on('click', '#show_service_info', function(e) {
            e.preventDefault();
            var sect_id = $(this).data('receipt_settings_id');
            $('#show_service_info_model').modal('show');
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "GET",
                url: "{{ route('agreement.get_unit_service', ':id') }}".replace(':id', sect_id),
                success: function(response) {
                    if (response.status == 404) {
                        $('#success_message').html("");
                        $('#success_message').addClass("alert alert-danger");
                        $('#success_message').text(response.message);
                        // $('#edit_name').val(response.get_service.name)
                    } else {

                        let serviceRow = `< 
                <td class="text-center">${response.get_service.name}</td>
                <td class="text-center">${response.get_service.code}</td>
                <td class="text-center">${response.get_service.vat}</td> `;

                        $("#service_master_item").empty().append(serviceRow);

                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + error);
                }
            });

        });
    </script>
    <script>
        flatpickr("#invoice_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
        });
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        $('.subscription_status_form').on('submit', function(event) {
            event.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('agreement.status-update') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success == true) {
                        toastr.success('{{ ui_change('updated_successfully', 'property_report') }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.', 'property_report') }} {{ ui_change('Product_must_be_approved', 'property_report') }}'
                        );
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });

        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: "{{ ui_change('are_you_sure_delete_this', 'property_report') }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this', 'property_report') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it', 'property_report') }}!",
                cancelButtonText: "{{ ui_change('cancel', 'property_report') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('agreement.delete') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                "{{ ui_change('deleted_successfully', 'property_report') }}"
                            );
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
    <script>
        function filterUnits() {
            const buildingSelect = document.getElementById('report_building');
            const unitSelect = document.getElementById('report_unit_management');
            const selectedBuildingId = buildingSelect.value;

            if (selectedBuildingId != -1) {
                unitSelect.disabled = false;

                Array.from(unitSelect.options).forEach(option => {
                    if (option.value !== "-1") {
                        option.style.display = option.getAttribute('data-building') == selectedBuildingId ?
                            'block' : 'none';
                    }
                });
            } else {
                unitSelect.disabled = true;
                Array.from(unitSelect.options).forEach(option => {
                    option.style.display = 'block';
                });
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        
        $('.date_picker').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    todayHighlight: true
});
    </script>
    
@endpush
