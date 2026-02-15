@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
    $firstDay = Carbon\Carbon::now()->startOfMonth()->format('d/m/Y');
    $lastDay = Carbon\Carbon::now()->endOfMonth()->format('d/m/Y');
@endphp
@section('title', ui_change('pre_bill_checking' , 'property_transaction') )

@push('css_or_js')
@endpush
@php
    $company = App\Models\User::first();
@endphp
@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2"> 
                {{ ui_change('pre_bill_checking' , 'property_transaction') }}
                <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $schedules->count() }}</span>
            </h2>
        </div>

        @include('admin-views.inline_menu.property_transaction.inline-menu')
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">



                    </div>
                    <form action="{{ url()->current() }}" method="get">


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
                        <div class="col-md-12 ">
                            <div class="input-group mb-3 d-flex justify-content-end">
                                <div class="remv_control mr-2 d-flex align-items-start flex-column">
                                    <label for="">{{ ui_change('rent_amount' , 'property_transaction')  }}</label>
                                    <input name="rent_amount" type="number" class="mr-3 form-control remv_focus" />
                                </div>
                                <div class=" ">
                                    <button type="submit" name="bulk_action_btn" value="update_status"
                                        class="btn btn-primary mr-2" style="margin-top: 26px;">
                                        <i class="la la-refresh"></i> {{ ui_change('update' , 'property_transaction')  }}
                                    </button>
                                </div>
                            </div>
                        </div> 
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th><input id="bulk_check_all" class="bulk_check_all" type="checkbox" />
                                            {{ ui_change('sl' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('tenant' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('agreement_no' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('property' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('unit_name' , 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('services' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('rent_mode' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('net_total_amount' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('billing_month_year' , 'property_transaction')  }}</th>
                                        <th class="text-center">{{ ui_change('currency_name' , 'property_transaction') }}</th>
                                        <th class="text-center">{{ ui_change('invoice_status' , 'property_transaction') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $k => $schedules_item)
                                        <tr>
                                            <th scope="row"><input class="check_bulk_item" name="bulk_ids[]"
                                                    type="checkbox" value="{{ $schedules_item->id }}" />
                                                {{ $schedules->firstItem() + $k }}</th>

                                            <td class="text-center">
                                                {{ $schedules_item->tenant->type == 'individual' ? $schedules_item->tenant->name ?? ui_change('not_available' , 'property_transaction')  : $schedules_item->tenant->company_name ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->agreement->agreement_no ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->main_unit->property_unit_management->name ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center"> 
                                                {{ $schedules_item->main_unit->property_unit_management->name .
                                                    '-' .
                                                    $schedules_item->main_unit->block_unit_management->block->name .
                                                    '-' .
                                                    $schedules_item->main_unit->floor_unit_management->floor_management_main->name .
                                                    '-' .
                                                    $schedules_item->main_unit->unit_management_main->name }}
                                                 
                                            </td>
                                            <td class="text-center">
                                                @if ($schedules_item->service == 'yes')
                                                    <a id="show_service_info" class="btn   "
                                                        title="{{ ui_change('show' , 'property_transaction')  }}"
                                                        data-receipt_settings_id="{{ $schedules_item->service_id }}"
                                                        data-target="#show_service_info">
                                                        {{ $schedules_item->service ?? ui_change('not_available' , 'property_transaction') }}
                                                    </a>
                                                @else
                                                    {{ $schedules_item->service ?? ui_change('not_available' , 'property_transaction')  }}
                                                @endif

                                            </td>
                                            <td class="text-center">
                                                {{ $rent_mode_months[$schedules_item->rent_mode] ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $formatted_amount = number_format(
                                                        $schedules_item->rent_amount,
                                                        $company->decimals,
                                                        '.',
                                                        '',
                                                    );
                                                @endphp
                                                {{ $formatted_amount ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->billing_month_year ?? ui_change('not_available' , 'property_transaction') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $schedules_item->currency ?? ui_change('not_available' , 'property_transaction')  }}
                                            </td>

                                            <td class="text-center">
                                                {{ $schedules_item->invoice_status ?? ui_change('not_available' , 'property_transaction') }}
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
                            <img class="mb-3 w-160" src="{{ asset(main_path() . 'back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description">
                            <p class="mb-0">{{ ui_change('no_data_to_show' , 'property_transaction') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_service_info_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('service_details' , 'property_transaction') }}</h5>
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
                                            <th class="text-center">{{ ui_change('services_name' , 'property_transaction') }} </th>
                                            <th class="text-center">{{ ui_change('services_code' , 'property_transaction') }} </th>
                                            <th class="text-center">{{ ui_change('vat_amount' , 'property_transaction') }} </th>
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
                        data-dismiss="modal">{{ ui_change('cancel' , 'property_transaction') }}</button>
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
                        toastr.success('{{ ui_change('updated_successfully' , 'property_transaction') }}');
                    } else if (data.success == false) {
                        toastr.error(
                            '{{ ui_change('Status_updated_failed.' , 'property_transaction') }} {{ ui_change('Product_must_be_approved' , 'property_transaction') }}'
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
                title: "{{ ui_change('are_you_sure_delete_this' , 'property_transaction')  }}",
                text: "{{ ui_change('you_will_not_be_able_to_revert_this' , 'property_transaction') }}!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ ui_change('yes_delete_it' , 'property_transaction')  }}!",
                cancelButtonText: "{{ ui_change('cancel' , 'property_transaction')  }}",
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
                            toastr.success("{{ ui_change('deleted_successfully' , 'property_transaction') }}");
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
    <script>
        flatpickr("#end_date", {
            dateFormat: "d/m/Y",
        });
        flatpickr("#start_date", {
            dateFormat: "d/m/Y",
        });
    </script>
@endpush
