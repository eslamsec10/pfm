@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
    $firstDay = Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
    $lastDay = Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
@endphp
@section('title', ui_change('invoice_register','property_report') )

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-subscription-list.png') }}" alt=""> --}}
                {{ ui_change('invoice_register','property_report')  }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $invoices->total() }}</span>
            </h2>
        </div>
        <!-- End Page Title -->

        @include('admin-views.inline_menu.property_reports.inline-menu')

        <form action="" method="get" class="mt-3">
            <div class="row mt-20">
                <div class="col-md-12">
                    <div class="card ">
                        <div class="px-3 py-4 ">
                            <div class="row align-items-center mb-2">
                                <div class="col-md-12 col-lg-4 col-xl-3">
                                    <select name="status" class="form-control remv_focus">
                                        <option value="">{{ ui_change('Set_Status','property_report')  }}</option>
                                        <option value="paid">{{ ui_change('Paid','property_report')  }}</option>
                                        <option value="unpaid">{{ ui_change('Unpaid','property_report')  }}</option>
                                        <option value="partially_paid">{{ ui_change('partially_paid','property_report')  }}</option>
                                    </select>
                                </div>
                                <button type="submit" name="bulk_action_btn" value="update_status"
                                    class="btn btn--primary">
                                    <i class="la la-refresh"></i> {{ ui_change('update','property_report')  }}
                                </button>

                                <button type="button" data-target="#filter_invoices" data-filter_invoices=""
                                    data-toggle="modal" class="btn btn--primary btn-sm m-1">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>


                            <div style="text-align: {{ $lang === 'ar' ? 'right' : 'left' }};">
                                <div class="table-responsive">
                                    <table id="datatable"
                                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th width="40"><input class="bulk_check_all" type="checkbox" /></th>
                                                <th width="100">{{ ui_change('Invoice_No.' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Invoice_Date' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Invoice_Total' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Billing_Month/_Year' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Customer_Name' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Status' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Created_At' , 'property_report') }}</th>
                                                <th width="100">{{ ui_change('Action' , 'property_report') }}</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($invoices as $invoice)
                                                <tr>
                                                    <td>
                                                        <label>
                                                            <input class="check_bulk_item" name="bulk_ids[]" type="checkbox"
                                                                value="{{ $invoice->id }}" />
                                                            <span class="text-muted">#{{ $invoice->id }}</span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        @if ($invoice->invoice_number)
                                                            {{ $invoice->invoice_number }}
                                                        @else
                                                            <span class="text-red">{{ ui_change('Not_Available','property_report') }}</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ($invoice->invoice_date)
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d',$invoice->invoice_date)->format('d-m-Y') }}
                                                        @else
                                                            <span class="text-red">{{ ui_change('Not_Available','property_report') }}</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ($invoice->total)
                                                            {{ number_format($invoice->total , $company->decimals ?? 3) }}
                                                        @else
                                                            <span class="text-red">{{ ui_change('Not_Available','property_report') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($invoice->invoice_month_year)
                                                            {{ $invoice->invoice_month_year }}
                                                        @else
                                                            <span class="text-red">{{ ui_change('Not_Available','property_report') }}</span>
                                                        @endif
                                                    </td>



                                                    <td>
                                                        <?php $customer = App\Models\Tenant::where('id', $invoice->tenant_id)->first(); ?>
                                                        {{-- {{ dd($customer->name) }} --}}
                                                        @if ($customer)
                                                            {{ $customer->name ?? $customer->company_name }}
                                                        @else
                                                            <span class="text-red">{{ ui_change('All_Tenant','property_report') }}</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <span
                                                            class="badge badge-pill
                                    @if ($invoice->status == 'paid') badge-success
                                    @elseif($invoice->status == 'unpaid')
                                        badge-warning
                                    @else
                                        badge-secondary @endif">
                                                            @if ($invoice->status == 'paid')
                                                                {{ ui_change('Paid','property_report') }} 
                                                            @elseif($invoice->status == 'unpaid')
                                                                {{ ui_change('Unpaid','property_report') }} 
                                                            @else
                                                                {{   ui_change('Partially_Paid','property_report') }}  
                                                            @endif
                                                        </span>
                                                    </td>

                                                    <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
                                                    {{-- <td>{{ $invoice->created_at->shortAbsoluteDiffForHumans() }}</td> --}}
                                                    <td>
                                                        <a href="{{ route('invoices.print_pdf', $invoice->id) }}"
                                                            class="btn btn--primary">{{ ui_change('print','property_report') }}</a>
                                                        <a href="{{ route('receipts.add_receipt_for_invoice', $invoice->id) }}"
                                                            class="btn btn--primary">{{ ui_change('add_Receipt','property_report') }}</a>
                                                    </td>
                                                    {{-- <td><a href="{{ route('invoice_generate.print', $invoice->id) }}"
                                        class="btn btn-primary">Print</a></td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>

    <div class="modal fade" id="filter_invoices" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('Filter','property_report')  }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="col-md-12  ">
                            <form action="{{ url()->current() }}" method="get">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-12 col-lg-4 col-xl-4">


                                        <input class="form-control float-right {{ $lang == 'ar' ? 'mr-2' : 'ml-2' }}"
                                            type="text" id="start_date" name="start_date" value="{{ $firstDay }}">
                                    </div>
                                    <div class="col-md-12 col-lg-4 col-xl-4">
                                        <input class="form-control float-right {{ $lang == 'ar' ? 'mr-2' : 'ml-2' }}"
                                            type="text" id="end_date" name="end_date" value="{{ $lastDay }}">

                                    </div>

                                    <div class="col-md-12 col-lg-4 col-xl-4">
                                        <select name="invoice_tenant" class="form-control remv_focus">
                                            <option value="-1">{{ ui_change('All_Tenants','property_report')  }}</option>
                                            @foreach ($tenants as $tenant_filter)
                                                <option value="{{ $tenant_filter->id }}">
                                                    {{ $tenant_filter->name ?? $tenant_filter->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row align-items-center mt-1">
                                    <div class="col-md-12 col-lg-4 col-xl-4">
                                        <select name="invoice_building" id="invoice_building"
                                            class="form-control remv_focus" onchange="filterUnits()">
                                            <option value="-1" selected>{{ ui_change('All_Buildings','property_report') }}</option>
                                            @foreach ($all_building as $building_filter)
                                                <option value="{{ $building_filter->id }}">{{ $building_filter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 col-lg-4 col-xl-4">
                                        <select name="invoice_unit_management" id="invoice_unit_management"
                                            class="form-control remv_focus" disabled>
                                            <option value="-1">{{ ui_change('All_Units','property_report')  }}</option>
                                            @foreach ($unit_management as $unit_management_filter)
                                                <option value="{{ $unit_management_filter->id }}"
                                                    data-building="{{ $unit_management_filter->property_management_id }}">
                                                    {{ $unit_management_filter->property_unit_management->name .
                                                        '-' .
                                                        $unit_management_filter->block_unit_management->block->name .
                                                        '-' .
                                                        $unit_management_filter->floor_unit_management->floor_management_main->name .
                                                        '-' .
                                                        $unit_management_filter->unit_management_main->name .
                                                        '-' .
                                                        ($unit_management_filter->unit_description->name ?? '') }}

                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-12 col-lg-4 col-xl-3">
                                        <button type="submit" class="btn btn--primary px-4 m-2" name="bulk_action_btn"
                                            value="filter"> {{ ui_change('','property_report')('general.filter') }}</button>
                                    </div> --}}
                                </div>

                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="submit" class="btn btn--primary px-5 saveTenant"
                                        name="bulk_action_btn" value="filter">{{ ui_change('filter','property_report')  }}
                                    </button>
                                </div>
                            </form>
                        </div>

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
    <!-- Page level custom scripts -->
    <script>
        flatpickr("#end_date", {
            dateFormat: "d/m/Y",
        });
        flatpickr("#start_date", {
            dateFormat: "d/m/Y",
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
                        toastr.success('{{ ui_change('updated_successfully','property_report') }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.','property_report')  }} {{ ui_change('Product_must_be_approved','property_report')  }}'
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
                title: "{{ ui_change('are_you_sure_delete_this','property_report')  }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this','property_report')  }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it','property_report')  }}!",
                cancelButtonText: "{{ ui_change('cancel','property_report') }}",
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
                            toastr.success("{{ ui_change('deleted_successfully','property_report') }}");
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>

    <script>
        function filterUnits() {
            const buildingSelect = document.getElementById('invoice_building');
            const unitSelect = document.getElementById('invoice_unit_management');
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
@endpush
