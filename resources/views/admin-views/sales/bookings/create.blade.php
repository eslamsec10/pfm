@extends('layouts.back-end.app')

@section('title', ui_change('create_booking', 'property_transaction'))
@php
    $lang = Session::get('locale');
@endphp
@push('css_or_js')
    <link href="{{ asset('public/assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('public/assets/back-end/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('create_booking', 'property_transaction') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')

        <div>
            {{-- d-flex align-items-center --}}
            <a href="{{ route('general_image_view') }}"
                class="btn btn--primary btn-sm  text-end">{{ ui_change('view_image', 'property_transaction') }}</a>
            <a href="{{ route('general_list_view') }}"
                class="btn btn--primary btn-sm  text-end">{{ ui_change('list_view', 'property_transaction') }}</a>
        </div>
        <!-- Form -->
        <form id="productForm" class="product-form text-start" action="{{ route('booking.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <!-- general setup -->


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('general_info', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_no', 'property_transaction') }}</label>
                                <input readonly type="text" name="booking_no" class="form-control"
                                    value="{{ bookingNo() }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_date', 'property_transaction') }}<span
                                        class="text-danger"> *</span></label>
                                <input type="text" class="form-control" id="booking_date" name="booking_date"
                                    class="form-control">
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('tenant', 'property_transaction') }}<span
                                        class="text-danger"> *</span>
                                    <button type="button" data-target="#add_tenant" data-add_tenant="" data-toggle="modal"
                                        class="btn btn--primary btn-sm">
                                        <i class="fa fa-plus-square"></i>
                                    </button>
                                </label>
                                <select class="js-select2-custom form-control" id="tenant_id" name="tenant_id" required>
                                    <option selected>{{ ui_change('select', 'property_transaction') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">
                                            {{ $tenant->name ?? $tenant->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('tenant_type', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="tenant_type" readonly class="form-control">
                            </div>
                            @error('tenant_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label
                                    for="">{{ ui_change('total_no_of_required_units', 'property_transaction') }}<span
                                        class="text-danger"> *</span></label>
                                <input type="number" id="total-no-units" class="form-control"
                                    name="total_no_of_required_units">
                            </div>
                            @error('total_no_of_required_units')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>


                </div>

            </div>
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img width="20px" src="{{ asset('/public/assets/back-end/img/seller-information.png') }}"
                            class="mb-1" alt=""> --}}
                        <h4 class="mb-0">{{ ui_change('tenant_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 tenant_form d-none company-form" id="company-form">

                            @include('admin-views.property_transactions.bookings.company_form')

                        </div>
                        <div class="col-md-12 tenant_form d-none personal-form" id="personal-form">
                            @include('admin-views.property_transactions.bookings.personal_form')
                        </div>
                    </div>

                </div>

            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img width="40px" src="{{ asset('/public/assets/back-end/img/booking.jpg') }}" class="mb-1"
                            alt=""> --}}
                        <h4 class="mb-0">{{ ui_change('booking_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                @include('includes.property_transactions.main_info')
            </div>

            <div id="main-content"></div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset"
                    class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                <button type="submit" class="btn btn--primary px-5"
                    onclick="setFormAction('{{ route('booking.store') }}')">{{ ui_change('submit', 'property_transaction') }}</button>
                <button type="submit" class="btn btn-warning px-5"
                    onclick="setFormAction('{{ route('booking.search') }}')"><i
                        class="fa fa-search"></i>{{ ui_change('search', 'property_transaction') }}</button>
            </div>
        </form>



    </div>
    <div class="modal fade" id="add_tenant" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ ui_change('create_tenant', 'property_transaction') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            <li class="nav-item">
                                <a class="nav-link type_link_create active" href="#"
                                    id="personal-link_create">{{ ui_change('personal', 'property_transaction') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link type_link_create " href="#"
                                    id="company-link_create">{{ ui_change('company', 'property_transaction') }}</a>
                            </li>
                        </ul>
                        <div class="col-md-12 tenant_form_create personal-form_create" id="personal-form_create">
                            <form id="tenantForm_personal" action="{{ route('tenant.store_for_anything') }}"
                                method="post" class="tenantForm">
                                @csrf
                                @method('post')
                                @include('admin-views.property_transactions.tenants.personal_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="saveTenantPersonal"
                                        class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
                                </div>

                            </form>
                        </div>
                        <div class="col-md-12 tenant_form_create d-none company-form_create" id="company-form_create">
                            <form id="tenantForm_company" action="{{ route('tenant.store_for_anything') }}"
                                method="post" class="tenantForm">
                                @csrf
                                @method('post')

                                @include('admin-views.property_transactions.tenants.company_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="saveTenantCompany"
                                        class="btn btn--primary px-5 saveTenant">{{ ui_change('submit', 'property_transaction') }}</button>
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
    <script>
        function setFormAction(actionUrl) {
            document.getElementById('productForm').action = actionUrl;
        }

        function calculation_method(i) {
            calculation_method_val = $('select[name="calculation_method-' + i + '"]').val();
            console.log(calculation_method_val)
            if (calculation_method_val == 2) {
                $('#area_measurement-' + i).removeClass('d-none');
                $('#amount-' + i).removeClass('d-none');
                $('#total_area_amount-' + i).removeClass('d-none');
            } else {
                $('#area_measurement-' + i).addClass('d-none');
                $('#amount-' + i).addClass('d-none');
                $('#total_area_amount-' + i).addClass('d-none');
            }
        }
        $(".type_link_create").click(function(e) {
            e.preventDefault();
            $(".type_link_create").removeClass('active');
            $(".tenant_form_create").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            if (form_id === 'personal-link_create') {
                $("#personal-form_create").removeClass('d-none').addClass('active');
                $("#company-form_create").removeClass('active').addClass('d-none');
            } else if (form_id === 'company-link_create') {
                $("#company-form_create").removeClass('d-none').addClass('active');
                $("#personal-form_create").removeClass('active').addClass('d-none');
            }

        });
    </script>
    <script>
        flatpickr("#booking_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            minDate: "today"
        });
        flatpickr(".main_date", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
        flatpickr(".relocation_date", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
        flatpickr(".period_from", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
        flatpickr(".period_to", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
        flatpickr(".main_data", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });

        function proposal_unit_date_clc(i) {
            var form_unit_date = $(`input[name=period_from-${i}]`).val();
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to-${i}]`).val(formattedDate);


                }
            }
        }

        function proposal_period_date_clc() {
            var form_unit_date = $(`input[name=period_from]`).val();
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to]`).val(formattedDate);


                }
            }
        }

        function service_date(i, key) {

            var form_unit_date = $(`input[name='start_date-${i}-${key}[]']`).val();

            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name='expiry_date-${i}-${key}[]']`).val(formattedDate);


                }
            }
        }

        function deposite(id) {
            rent_amount = $('input[name="rent_amount-' + id + '"]').val();
            deposite_month = $('input[name="security_deposit_months_rent-' + id + '"]').val();
            deposite_all = $('input[name="security_deposit_amount-' + id + '"]').val((rent_amount * deposite_month));
        }

        function unitFunc(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('booking.get_units') }}",
                type: "GET",
                data: {
                    property_id: property_id,
                    unit_description_id: unit_description_id,
                    unit_type_id: unit_type_id,
                    unit_condition_id: unit_condition_id,
                    view_id: view_id,
                    property_type: property_type
                },
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        $('select[name="unit-' + i + '"]').empty();
                        $.each(data, function(key, value) {
                            let isBooked = '';
                            if (value.booking_status === 'agreement') {
                                isBooked = 'style="background-color:red;color:white"';
                            } else if (value.booking_status === 'booking') {
                                isBooked =
                                    'style="background-color:#d500f9;color:white"';
                            } else if (value.booking_status === 'proposal') {
                                isBooked =
                                    'style="background-color:#ffeb3b;color:black"';
                            } else if (value.booking_status === 'enquiry') {
                                isBooked =
                                    'style="background-color:#372be2;color:white"';
                            }
                            $('select[name="unit-' + i + '"]').append(
                                '<option ' + isBooked + ' value="' + value.id + '">' +
                                value.property_unit_management.name + '-' + value
                                .block_unit_management.block.name + '-' +
                                value.floor_unit_management.floor_management_main.name + '-' + value
                                .unit_management_main
                                .name + '</option>'
                            );
                        });
                    } else if (data.length == 0) {
                        $('select[name="unit-' + i + '"]').empty();
                        $('select[name="unit-' + i + '"]').append(
                            '<option value="0">Not Found</option>'
                        );
                    } else {
                        ('No data found.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error occurred:', error);
                }
            });
        }

        function unit_change_main_date() {
            var form_unit_date = $(`input[name=period_from]`).val();
            if (form_unit_date) {
                $(`.general_period_date`).val(form_unit_date)
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;

                    $(`.general_period_date_to`).val(formattedDate)

                }
            }
        }

        function payment_mode_func(i) {
            var payment_mode_id = $('select[name="payment_mode-' + i + '"]').val();
            $('select[name="rent_mode-' + i + '"]').val(payment_mode_id);
            $('select[name="area_measurement-' + i + '"]').removeAttr('disabled');
            // $('input[name="amount-' + i + '"]').removeAttr('disabled');
            $('input[name="total_area_amount-' + i + '"]').removeAttr('disabled');
        }

        function disabled_false(i) {
            $('input[name="amount-' + i + '"]').removeAttr('disabled');
        }

        function rent_mode_amount(i) {
            var amount = parseFloat($('input[name="amount-' + i + '"]').val());
            var total_area_amount = parseFloat($('input[name="total_area_amount-' + i + '"]').val()) || 0;

            if (amount != 0 && total_area_amount != 0) {
                $('input[name="rent_amount-' + i + '"]').empty().val(0)
                $('input[name="vat_percentage-' + i + '"]').removeAttr('disabled');

                var rent_amount = amount * total_area_amount;
                $('input[name="rent_amount-' + i + '"]').val(rent_amount.toFixed(2));
            } else if ((amount == 0 && amount != null) || total_area_amount == 0) {
                $('input[name="rent_amount-' + i + '"]').empty().val()
            }
            vat_amount_func(i)
        }

        function vat_amount_func(i) {
            var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;
            var rental_gl = parseFloat($('select[name="rental_gl-' + i + '"]').val()) || 0;
            var vat_percentage = $('input[name="vat_percentage-' + i + '"]').empty();

            $('input[name="vat_amount-' + i + '"]').val(0);
            $('input[name="vat_percentage-' + i + '"]').val(0);
            $('input[name="total_net_rent_amount-' + i + '"]').val(0);

            var vat_amount = (rent_amount * rental_gl) / 100;
            var total_net_rent_amount = parseFloat(vat_amount) + parseFloat(rent_amount);
            $('input[name="vat_amount-' + i + '"]').val(vat_amount.toFixed(2));
            $('input[name="vat_percentage-' + i + '"]').val(rental_gl.toFixed(2));
            $('input[name="total_net_rent_amount-' + i + '"]').val(total_net_rent_amount.toFixed(2));
        }


        const counters = {};

        function add_service(i) {
            const container = document.getElementById('main_service_content-' + i);
            var form_unit_date = $(`input[name=period_from-${i}]`).val();
            var to_unit_date = $(`input[name=period_to-${i}]`).val();

            if (!counters[i]) {
                counters[i] = 0;
            }
            counters[i]++;

            let counterInput = document.getElementById('service_counter-' + i);
            if (!counterInput) {

                const formContainer = document.createElement('div');
                formContainer.innerHTML = `
                    <input type="hidden" id="service_counter-${i}" name="service_counter[${i}]" value="${counters[i]}">
                    `;
                container.appendChild(formContainer);
            } else {

                counterInput.value = counters[i];
            }

            const bladeContent = `
                <div class="row mt-1 bg-warning  border rounded p-2 position-relative" id="service-${i}-${counters[i]}">
                                <button type="button" class="btn btn-danger btn-sm position-absolute " 
                                    style="top: 5px; right: 5px; z-index: 10;" 
                                    onclick="removeService(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="charge_mode-${i}-${counters[i]}" class="form-control-label">{{ ui_change('Charge_Mode', 'property_transaction') }}</label> <span class="starColor">*</span>
                            <select name="charge_mode-${i}-${counters[i]}[]" class="form-control"
                                onchange="charge_type(${i},${counters[i]}) , amount_charge_func(${i},${counters[i]}) , percentage_amount_charge_func(${i},${counters[i]})">
                                <option value="0">{{ ui_change('Select_Other_Charge_Type', 'property_transaction') }}</option>
                                @foreach ($services_master as $service_master_item)
                                    <option value="{{ $service_master_item->id }}">{{ $service_master_item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="charge_mode_type-${i}-${counters[i]}" class="form-control-label">{{ ui_change('Charge_Mode', 'property_transaction') }}</label> <span
                                class="starColor">*</span>
                            <select name="charge_mode_type-${i}-${counters[i]}[]" class="form-control"
                                onchange="service_value_calc(${i},${counters[i]})">
                                <option value="amount">{{ ui_change('amount', 'property_transaction') }}</option>
                                <option value="percentage">{{ ui_change('percentage', 'property_transaction') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3" id="amount_charge-${i}-${counters[i]}">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('amount', 'property_transaction') }}</label>
                            <input type="number" onkeyup="amount_charge_func(${i},${counters[i]})"
                                name="amount_charge-${i}-${counters[i]}[]" class="form-control" step="0.001" placeholder="0.000">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="percentage_amount_charge-${i}-${counters[i]}">
                        <div class="form-group ">
                            <label for="total-area">{{ ui_change('percentage', 'property_transaction') }}</label>
                            <input type="number" onkeyup="percentage_amount_charge_func(${i},${counters[i]})"
                                name="percentage_amount_charge-${i}-${counters[i]}[]" class="form-control" step="0.001"
                                placeholder="0.000">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('calculate_amount', 'property_transaction') }}</label>
                            <input type="number" readonly id="total-area" name="calculate_amount-${i}-${counters[i]}[]"
                                class="form-control  bg-white" step="0.001" placeholder="0.000">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('start_date', 'property_transaction') }}</label>
                            <input type="text"   name="start_date-${i}-${counters[i]}[]" class="main_unit_data text-white form-control"
                            value="${form_unit_date}"  onchange="service_date(${i}, ${counters[i]})" >
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('expaired_date', 'property_transaction') }}</label>
                            <input type="text" name="expiry_date-${i}-${counters[i]}[]" class="main_unit_data text-white form-control"
                            value="${to_unit_date}">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('vat_percentage', 'property_transaction') }}</label>
                            <input type="number" readonly name="vat_percentage-${i}-${counters[i]}[]" class="form-control  bg-white"
                                step="0.001" placeholder="0.000">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="total-area">{{ ui_change('vat_amount', 'property_transaction') }}</label>
                            <input type="number" readonly name="vat_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
                                step="0.001" placeholder="0.000">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-xl-12">
                        <div class="form-group">   
                            <label for="total-area">{{ ui_change('total_amount', 'property_transaction') }}</label>
                            <input type="number" readonly id="total-area" name="total_amount-${i}-${counters[i]}[]"
                                class="form-control  bg-white" step="0.001" placeholder="0.000">
                        </div>
                    </div>
                </div>
                `;
            // container.innerHTML += bladeContent;   
            container.insertAdjacentHTML('beforeend', bladeContent);
            flatpickr(".main_unit_data", {
                dateFormat: "d/m/Y",
                minDate: "today"
            });

        }

        function removeService(button) {
            button.closest('.row').remove();
        }

        function charge_type(i, j) {
            var charge_type_amount = $('select[name="charge_mode-' + i + '-' + j + '[]"]').val();
            $.ajax({
                url: "{{ route('booking.get_unit_service', ':id') }}".replace(':id', charge_type_amount),
                type: "GET",
                dataType: "json",
                success: function(data) {
                    var vat_percentage = $('input[name="vat_percentage-' + i + '-' + j + '[]"]').val(data
                        .get_service.vat);
                },
                error: function(xhr, status, error) {
                    console.error('Error occurred:', error);
                }
            });
        }

        function service_value_calc(i, j) {
            var service_type = $('select[name="charge_mode_type-' + i + '-' + j + '[]"]').val();
            $('input[name="total_amount-' + i + '-' + j + '[]"]').empty().val(0)
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').empty().val(0)
            $('input[name="vat_amount-' + i + '-' + j + '[]"]').empty().val(0)
            if (service_type == 'percentage') {
                $('input[name="amount_charge-' + i + '-' + j + '[]"]').empty().val(0)
                $('#percentage_amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#amount_charge-' + i + '-' + j + '').addClass('d-none');
                var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;

            } else if (service_type == 'amount') {
                $('input[name="percentage_amount_charge-' + i + '-' + j + '[]"]').empty().val(0)
                $('#amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#percentage_amount_charge-' + i + '-' + j + '').addClass('d-none');
            }
        }

        function rent_amount_from_unit(i) {
            var unit_id = $('select[name="unit-' + i + '"]').val();
            $.ajax({
                url: "{{ route('general.get_unit_by_id', ':id') }}".replace(':id', unit_id),
                type: "GET",

                dataType: "json",
                success: function(data) {
                    $('input[name="rent_amount-' + i + '"]').empty();
                    $('input[name="rent_amount-' + i + '"]').val(data.latest_rent_schedule.rent_amount);
                },
                error: function(xhr, status, error) {
                    console.error('Error occurred:', error);
                }
            });
        }

        function amount_charge_func(i, j) {
            var amount_item = $('input[name="amount_charge-' + i + '-' + j + '[]"]').first();
            var amount_val = parseFloat(amount_item.val()) || 0;
            var vat_percentage_item = $('input[name="vat_percentage-' + i + '-' + j + '[]"]').first();
            var vat_percentage_val = parseFloat(vat_percentage_item.val()) || 0;
            var total_vat_service = amount_val * (vat_percentage_val / 100);
            var total_amount_val = amount_val + total_vat_service;

            let vat_amount_item = $('input[name="vat_amount-' + i + '-' + j + '[]"]').first();
            vat_amount_item.val(total_vat_service.toFixed(2))
            var calculate_amount = $('input[name="calculate_amount-' + i + '-' + j + '[]"]').first();
            calculate_amount.val(amount_val.toFixed(2));
            var total_amount = $('input[name="total_amount-' + i + '-' + j + '[]"]').first();
            // total_amount.empty() ;
            total_amount.val(total_amount_val.toFixed(2));
        }


        function percentage_amount_charge_func(i, j) {
            var percentage_amount_charge_item = $('input[name="percentage_amount_charge-' + i + '-' + j + '[]"]');
            var percentage_amount_charge_val = parseFloat(percentage_amount_charge_item.val()) || 0;
            var rent_amount_item = ($('input[name="rent_amount-' + i + '"]'));
            var rent_amount_val = parseFloat(rent_amount_item.val()) || 0;
            var percentage_amount = rent_amount_val * (percentage_amount_charge_val / 100);
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').val(percentage_amount.toFixed(2));
            var vat_percentage_val = parseFloat($('input[name="vat_percentage-' + i + '-' + j + '[]"]').val()) || 0;
            var total_vat_service = percentage_amount * (vat_percentage_val / 100);
            var total_amount_val = percentage_amount + total_vat_service;
            let mean_vat = $('input[name="vat_amount-' + i + '-' + j + '[]"]');
            mean_vat.val(total_vat_service.toFixed(3))

            $('input[name="total_amount-' + i + '-' + j + '[]"]').val(total_amount_val.toFixed(3));

        }
    </script>
    <script>
        document.getElementById('total-no-units').addEventListener('input', function() {
            const totalUnits = parseInt(this.value) || 0;
            const container = document.getElementById('main-content');

            container.innerHTML = '';

            for (let i = 1; i <= totalUnits; i++) {
                const bladeContent = `
                        <div class="card mt-3 rest-part" id="main_content" style="background-color: #2b368f;color:white">
                <div class="card-header">
                    <div class="d-flex gap-2">

                        <h4 class="mb-0">{{ ui_change('unit_search_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body mt-3">


                    <div class="form-container mt-3">
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="building">{{ ui_change('property', 'property_transaction') }} <span class="starColor " style="font-size: 18px; "> *</span></label>
                <select id="building" name="property_id-${i}"  onchange="unitFunc(${i})" class="js-select2-custom form-control">
                    <option value="0">{{ ui_change('select', 'property_transaction') }}</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="unit-description">{{ ui_change('unit_description', 'property_transaction') }}</label>
                <select id="unit-description" name="unit_description_id-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="0">{{ ui_change('any', 'property_transaction') }}</option>
                    @foreach ($unit_descriptions as $unit_description)
                        <option value="{{ $unit_description->id }}" >
                            {{ $unit_description->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="unit-type">{{ ui_change('unit_type', 'property_transaction') }}</label>
                <select id="unit-type" name="unit_type_id-${i}" class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="0">{{ ui_change('any', 'property_transaction') }}</option>
                    @foreach ($unit_types as $unit_type)
                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="unit-condition">{{ ui_change('unit_condition', 'property_transaction') }}</label>
                <select id="unit-condition" name="unit_condition_id-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                    @foreach ($unit_conditions as $unit_condition)
                        <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="preferred-view">{{ ui_change('preferred_view', 'property_transaction') }}</label>
                <select id="preferred-view" name="view_id-${i}"  onchange="unitFunc(${i})" class="js-select2-custom form-control">
                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                    @foreach ($views as $view)
                        <option value="{{ $view->id }}">{{ $view->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="property-type">{{ ui_change('property_type', 'property_transaction') }}</label>
                <select id="property-type" name="property_type-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                    @foreach ($property_types as $property_type)
                        <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="period-from">{{ ui_change('period_from_to', 'property_transaction') }}</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="period_from-${i}"   id="period-from" class="form-control main_date text-white general_period_date" onchange="(booking_unit_date_clc(${i}))" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="price" class="title-color"> </label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="period_to-${i}" id="period-to" class="form-control mt-2 main_date text-white general_period_date_to"  value="{{ \Carbon\Carbon::now()->subDay()->addYear()->format('d/m/Y') }}">
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="city">{{ ui_change('city', 'property_transaction') }}</label>
                <input type="text" id="city" name="city-${i}" class="form-control">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="total-area">{{ ui_change('total_area_required', 'property_transaction') }}</label>
                <input type="number" id="total-area" name="total_area-${i}" class="form-control"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="area-measurement">{{ ui_change('area_measurement', 'property_transaction') }}</label>
                <select id="area-measurement" name="area_measurement-${i}"
                    class="js-select2-custom form-control">
                    <option>{{ ui_change('select_area_measurement', 'property_transaction') }}</option>
                    <option>Sq. Mtr.</option>
                    <option>Sq. Ft.</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <label for="notes">{{ ui_change('notes_&_comments', 'property_transaction') }}</label>
                <textarea id="notes" name="notes-${i}" class="form-control" rows="2"> </textarea>
            </div>
        </div>
    </div>

    <hr>
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-6">

            <div class="form-group">
                <label for="area-measurement">{{ ui_change('unit', 'property_transaction') }} <span class="starColor " style="font-size: 18px; "> *</span></label>
                <select id="area-measurement" name="unit-${i}"
                    class="js-select2-custom form-control" onchange="(rent_amount_from_unit(${i}))">
                    <option>{{ ui_change('select_unit', 'property_transaction') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="area-measurement">{{ ui_change('payment_mode', 'property_transaction') }} <span class="starColor " style="font-size: 18px; "> *</span></label>
                <select id="area-measurement" name="payment_mode-${i}" required onchange="payment_mode_func(${i})"
                    class="js-select2-custom form-control">
                    <option value="0">{{ ui_change('select_payment_mode', 'property_transaction') }}</option>
                    <option value="1">{{ ui_change('daily', 'property_transaction') }}</option>
                    <option value="2">{{ ui_change('monthly', 'property_transaction') }}</option>
                    <option value="3">{{ ui_change('bi_monthly', 'property_transaction') }}</option>
                    <option value="4">{{ ui_change('quarterly', 'property_transaction') }}</option>
                    <option value="5">{{ ui_change('half_yearly', 'property_transaction') }}</option>
                    <option value="6">{{ ui_change('yearly', 'property_transaction') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ ui_change('pdc', 'property_transaction') }}</label>
                <select id="area-measurement" name="pdc-${i}" class="js-select2-custom form-control">
                    <option>{{ ui_change('yes', 'property_transaction') }}</option>
                    <option>{{ ui_change('no', 'property_transaction') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ ui_change('Calculation_Method', 'property_transaction') }}</label>
                <select id="area-measurement"
                    onchange="calculation_method(${i})"
                    name="calculation_method-${i}"
                    class="js-select2-custom form-control">
                    <option value="1" selected>{{ ui_change('Fixed', 'property_transaction') }}  </option>
                    <option value="2">{{ ui_change('Based_on_area', 'property_transaction') }}  </option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="area_measurement-${i}">
            <div class="form-group">
                <label
                    for="area-measurement">{{ ui_change('area_measurement', 'property_transaction') }}</label>
                <select id="area-measurement" disabled name="area_measurement-${i}"
                    class="js-select2-custom form-control">
                    <option>{{ ui_change('Sq._Mtr.', 'property_transaction') }}</option>
                    <option>{{ ui_change('Sq._Ft.', 'property_transaction') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="total_area_amount-${i}">
            <div class="form-group">
                <label for="total-area">{{ ui_change('total_area', 'property_transaction') }}</label>
                <input type="number" disabled  name="total_area_amount-${i}" onkeyup="disabled_false(${i}),rent_mode_amount(${i})" class="form-control" placeholder="0.000"  >
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="amount-${i}">
            <div class="form-group">
                <label for="total-area">{{ ui_change('amount', 'property_transaction') }}</label>
                <input type="number" disabled  name="amount-${i}" class="form-control" onkeyup="rent_mode_amount(${i})"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('rent_amount', 'property_transaction') }} <span class="starColor " style="font-size: 18px; "> *</span></label>
                <input type="number"   name="rent_amount-${i}" class="form-control" required onkeyup="rent_mode_amount(${i}), vat_amount_func(${i}) , deposite(${i})"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ ui_change('rent_mode', 'property_transaction') }}</label>
                <select id="area-measurement" name="rent_mode-${i}"
                    class="js-select2-custom form-control" required>
                    <option value="0">{{ ui_change('select_rent_mode', 'property_transaction') }}</option>
                    <option value="1">{{ ui_change('daily', 'property_transaction') }}</option>
                    <option value="2">{{ ui_change('monthly', 'property_transaction') }}</option>
                    <option value="3">{{ ui_change('bi_monthly', 'property_transaction') }}</option>
                    <option value="4">{{ ui_change('quarterly', 'property_transaction') }}</option>
                    <option value="5">{{ ui_change('half_yearly', 'property_transaction') }}</option>
                    <option value="6">{{ ui_change('yearly', 'property_transaction') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ ui_change('rental_gl', 'property_transaction') }}</label>
                <select id="area-measurement" name="rental_gl-${i}"
                    class="js-select2-custom form-control"  onchange="vat_amount_func(${i})">
                    <option value="0">{{ ui_change('select_rental_gl', 'property_transaction') }}</option>
                    <option value="0">{{ ui_change('Rental_Income_0%', 'property_transaction') }}</option>
                    <option value="10">{{ ui_change('Rental_Income_10%', 'property_transaction') }}</option>
                    <option value="20">{{ ui_change('Rental_Income_20%', 'property_transaction') }}</option>
                    <option value="30">{{ ui_change('Rental_Income_30%', 'property_transaction') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('vat_percentage', 'property_transaction') }}</label>
                <input type="number" readonly name="vat_percentage-${i}" class="form-control text-white"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ ui_change('vat_amount', 'property_transaction') }}</label>
                <input type="number" readonly name="vat_amount-${i}" class="form-control text-white" step="0.001"
                    placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <label
                    for="total-area">{{ ui_change('total_net_rent_amount', 'property_transaction') }}</label>
                <input type="number" readonly name="total_net_rent_amount-${i}" class="form-control text-white"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <button type="button" class="btn btn--primary form-control" onclick="add_service(${i})">{{ ui_change('add_other_services', 'property_transaction') }}</button>
            </div>
        </div>
        <div class="card-body" id="main_service_content-${i}">

        </div>
        </div>
        <div class="card-body">
            <div  class="row">
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label
                            for="total-area">{{ ui_change('security_deposit_months_rent', 'property_transaction') }}</label>
                        <input type="number"   onkeyup="deposite(${i})" name="security_deposit_months_rent-${i}"
                            class="form-control" step="0.001" placeholder="0.000">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label
                            for="total-area">{{ ui_change('security_deposit_amount', 'property_transaction') }}</label>
                        <input type="number"  name="security_deposit_amount-${i}" class="form-control"
                            step="0.001" placeholder="0.000">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label
                            for="area-measurement">{{ ui_change('is_rent_inclusive_of_ewa', 'property_transaction') }}</label>
                        <select name="is_rent_inclusive_of_ewa-${i}" class="js-select2-custom form-control">
                            <option>{{ ui_change('yes', 'property_transaction') }}</option>
                            <option>{{ ui_change('no', 'property_transaction') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="area-measurement">{{ ui_change('ewa_limit_mode', 'property_transaction') }}</label>
                        <select name="ewa_limit_mode-${i}" class="js-select2-custom form-control">
                            <option>{{ ui_change('monthly', 'property_transaction') }}</option>
                            <option>{{ ui_change('yearly', 'property_transaction') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="total-area">{{ ui_change('ewa_limit_monthly', 'property_transaction') }}</label>
                        <input type="number"  name="ewa_limit_monthly-${i}" class="form-control"
                            step="0.001" value="0.000">
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="">{{ ui_change('lease_break_date', 'property_transaction') }}</label>
                        <input type="text" class="form-control main_date text-white" name="lease_break_date-${i}"  value="{{ \Carbon\Carbon::now()->addMonth()->format('d/m/Y') }}">
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="area-measurement">{{ ui_change('notice_period', 'property_transaction') }}</label>
                        <select name="notice_period-${i}" class="js-select2-custom form-control">
                            <option>{{ ui_change('one_month', 'property_transaction') }}</option>
                            <option>{{ ui_change('two_month', 'property_transaction') }}</option>
                            <option>{{ ui_change('three_month', 'property_transaction') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-6">
                    <div class="form-group">
                        <label for="total-area">{{ ui_change('lease_break_comments', 'property_transaction') }}</label>
                        <input type="text" name="lease_break_comments-${i}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

                </div>
            </div>
        `;
                container.innerHTML += bladeContent;
            }
            flatpickr(".main_date", {
                dateFormat: "d/m/Y",
                minDate: "today"
            });

        });
    </script>
    <script>
        function booking_unit_date_clc(i) {
            var form_unit_date = $(`input[name=period_from-${i}]`).val();
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to-${i}]`).val(formattedDate);


                }
            }
        }

        function proposal_unit_date_clc(i) {
            var form_unit_date = $(`input[name=period_from-${i}]`).val();
            if (form_unit_date) {
                var parts = form_unit_date.split('/');
                if (parts.length === 3) {
                    var day = parseInt(parts[0]) - 1;
                    var month = parseInt(parts[1]) - 1;
                    var year = parseInt(parts[2]) + 1;

                    var dateObj = new Date(year, month, day);
                    var formattedDate =
                        `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;
                    $(`input[name=period_to-${i}]`).val(formattedDate);


                }
            }
        }
    </script>
    <script>
        function collectUnitValues() {
            let totalValue = 0;
            var input_names = {};
            document.querySelectorAll('.no-of-units').forEach(function(input) {
                const value = parseInt(input.value) || 0;
                totalValue += value;
                var unitId = $(input).data('id');
                input_names[unitId] = parseInt(input.value);

            });
            return {
                totalValue: totalValue,
                input_names: input_names
            };
        }

        document.querySelectorAll('.no-of-units').forEach(function(input) {
            document.getElementById('main_content').classList.remove('d-none');
            input.addEventListener('input', function() {
                const totalValue_and_names = collectUnitValues();
                const totalValue = totalValue_and_names.totalValue
                const totalnames = totalValue_and_names.input_names;
                (totalValue, totalnames);

                const container = document.getElementById('units-container');
                container.innerHTML = '';
                let currentIndex = 0;
                let currentDescriptionId = null;
                let remainingUnits = 0;
                const descriptions = Object.entries(totalnames);

                for (let i = 0; i < totalValue; i++) {
                    if (remainingUnits === 0 && currentIndex < descriptions.length) {
                        currentDescriptionId = descriptions[currentIndex][0];
                        remainingUnits = descriptions[currentIndex][1];
                        currentIndex++;
                    }

                    const unitHtml = `
        <div class="form-container mt-3">
            <div class="form-row">
                <div class="form-group">
                    <label for="building-${i}">{{ ui_change('Building', 'property_transaction') }}</label>
                    <select id="building-${i}" name="property_id-${i}" class="js-select2-custom form-control">
                        <option value="">{{ ui_change('Select_building', 'property_transaction') }}</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="unit-description-${i}">{{ ui_change('Unit_Description', 'property_transaction') }}</label>
                    <select id="unit-description-${i}" name="unit_description_id-${i}" class="js-select2-custom form-control">
                        <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                        @foreach ($unit_descriptions as $unit_description)
                            <option value="{{ $unit_description->id }}"
                                ${currentDescriptionId == '{{ $unit_description->id }}' ? 'selected' : ''}>
                                {{ $unit_description->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                            <div class="form-group">
                                <label for="unit-type-${i}">{{ ui_change('Unit_Type', 'property_transaction') }}</label>
                                <select id="unit-type-${i}" name="unit_type_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                                    @foreach ($unit_types as $unit_type)
                                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-condition-${i}">{{ ui_change('Unit_Condition', 'property_transaction') }}</label>
                                <select id="unit-condition-${i}" name="unit_condition_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                                    @foreach ($unit_conditions as $unit_condition)
                                        <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferred-view-${i}">{{ ui_change('Preferred_View', 'property_transaction') }}</label>
                                <select id="preferred-view-${i}" name="view_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                                    @foreach ($views as $view)
                                        <option value="{{ $view->id }}">{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="period-from-${i}">{{ ui_change('Period_From-_To', 'property_transaction') }}</label>
                                <div style="display: flex; gap: 10px;">  
                                    <input type="text" name="period_from-${i}" id="period-from-${i}" class="form-control text-white main_date" onchange="(booking_unit_date_clc(${i}))" value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                                    <input type="text" name="period_to-${i}" id="period-to-${i}" class="form-control text-white"  value="{{ \Carbon\Carbon::now()->subDay()->addYear()->format('d/m/Y') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property-type-${i}">{{ ui_change('Property_Type', 'property_transaction') }}</label>
                                <select id="property-type-${i}" name="property_type-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                                    @foreach ($property_types as $property_type)
                                        <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${i}">{{ ui_change('City', 'property_transaction') }}</label>
                                <input type="text" id="city-${i}" name="city-${i}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="total-area-${i}">{{ ui_change('Total_Area_Required', 'property_transaction') }}</label>
                                <input type="number" id="total-area-${i}" name="total_area-${i}" class="form-control" step="0.001" value="0.000">
                            </div>
                            <div class="form-group">
                                <label for="area-measurement-${i}">Area Measurement</label>
                                <select id="area-measurement-${i}" name="area_measurement-${i}" class="js-select2-custom form-control">
                                    <option>{{ ui_change('Select_Area_Measurement', 'property_transaction') }}</option>
                                    <option>{{ ui_change('Sq._Mtr.', 'property_transaction') }}</option>
                                    <option>{{ ui_change('Sq._Ft.', 'property_transaction') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes-${i}">{{ ui_change('Notes_/_Comments', 'property_transaction') }}</label>
                                <textarea id="notes-${i}" name="notes-${i}" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                    container.insertAdjacentHTML('beforeend', unitHtml);
                    remainingUnits--;


                    if (remainingUnits === 0 && currentIndex < descriptions.length - 1) {
                        currentIndex++;
                        currentDescriptionId = descriptions[currentIndex][0];
                        remainingUnits = descriptions[currentIndex][1];
                    }
                }
            });
        });
    </script>

    <script>
        $('select[name=tenant_id]').on('change', function() {
            var tenant_id = $(this).val();
            if (tenant_id) {
                $.ajax({
                    url: "{{ URL::to('enquiry/get_tenant') }}/" + tenant_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $('input[name="tenant_type"]').empty();
                            $('input[name="tenant_type"]').val(data.type);
                            if (data.type == 'company') {
                                $("#company-form").removeClass('d-none').addClass('active');
                                $("#personal-form").removeClass('active').addClass('d-none');
                                $("#docs_company_id").addClass('d-none');
                            } else {
                                $("#personal-form").removeClass('d-none').addClass('active');
                                $("#company-form").removeClass('active').addClass('d-none');
                                $("#docs_personal_id").addClass('d-none');
                            }
                            $('input[name="name"]').empty();
                            // $('input[name="tenant_type"]').empty();
                            // $('input[name="tenant_type"]').val(data.tenant_type);
                            $('input[name="name"]').empty();
                            $('input[name="name"]').val(data.name);
                            $('input[name="id_number"]').empty();
                            $('input[name="id_number"]').val(data.id_number);
                            $('input[name="nick_name"]').empty();
                            $('input[name="nick_name"]').val(data.nick_name);
                            $('input[name="contact_person"]').empty();
                            $('input[name="contact_person"]').val(data.contact_person);
                            $('input[name="designation"]').empty();
                            $('input[name="designation"]').val(data.designation);
                            $('input[name="contact_no"]').empty();
                            $('input[name="contact_no"]').val(data.contact_no);
                            $('input[name="whatsapp_no"]').empty();
                            $('input[name="whatsapp_no"]').val(data.whatsapp_no);
                            $('input[name="company_name"]').empty();
                            $('input[name="company_name"]').val(data.company_name);
                            $('input[name="fax_no"]').empty();
                            $('input[name="fax_no"]').val(data.fax_no);
                            $('input[name="telephone_no"]').empty();
                            $('input[name="telephone_no"]').val(data.telephone_no);
                            $('input[name="other_contact_no"]').empty();
                            $('input[name="other_contact_no"]').val(data.other_contact_no);
                            $('input[name="address1"]').empty();
                            $('input[name="address1"]').val(data.address1);
                            $('input[name="address2"]').empty();
                            $('input[name="address2"]').val(data.address2);
                            $('input[name="address3"]').empty();
                            $('input[name="address3"]').val(data.address3);
                            $('input[name="city"]').empty();
                            $('input[name="city"]').val(data.city);
                            $('input[name="state"]').empty();
                            $('input[name="state"]').val(data.state);
                            $('input[name="passport_no"]').empty();
                            $('input[name="passport_no"]').val(data.passport_no);
                            $('input[name="email1"]').empty();
                            $('input[name="email1"]').val(data.email1);
                            $('input[name="email2"]').empty();
                            $('input[name="email2"]').val(data.email2);
                            $('input[name="company_name"]').empty();
                            $('input[name="company_name"]').val(data.company_name);
                            $('input[name="registration_no"]').empty();
                            $('input[name="registration_no"]').val(data.registration_no);
                            $('input[name="group_company_name"]').empty();
                            $('input[name="group_company_name"]').val(data.group_company_name);

                            var genderValue = data.gender;
                            var live_with_id_value = data.live_with_id;
                            var nationality_id_value = data.nationality_id;
                            var country_id_value = data.country_id;
                            var business_activity_id_value = data.business_activity_id;
                            if ($('select[name="gender"] option[value="' + genderValue + '"]')
                                .length === 0) {
                                $('select[name="gender"]').append('<option value="' + genderValue +
                                    '">' + genderValue + '</option>');
                            }
                            $('select[name="gender"]').val(genderValue).change();
                            if ($('select[name="live_with_id"] option[value="' +
                                    live_with_id_value + '"]')
                                .length === 0) {
                                $('select[name="live_with_id"]').append('<option value="' +
                                    live_with_id_value +
                                    '">' + live_with_id_value + '</option>');
                            }
                            $('select[name="live_with_id"]').val(live_with_id_value).change();

                            if ($('select[name="country_id"] option[value="' +
                                    country_id_value + '"]')
                                .length === 0) {
                                $('select[name="country_id"]').append('<option value="' +
                                    country_id_value +
                                    '">' + country_id_value + '</option>');
                            }
                            $('select[name="country_id"]').val(country_id_value).change();

                            if ($('select[name="nationality_id"] option[value="' +
                                    nationality_id_value + '"]')
                                .length === 0) {
                                $('select[name="nationality_id"]').append('<option value="' +
                                    nationality_id_value +
                                    '">' + nationality_id_value + '</option>');
                            }
                            $('select[name="nationality_id"]').val(nationality_id_value).change();

                            if ($('select[name="business_activity_id"] option[value="' +
                                    business_activity_id_value + '"]')
                                .length === 0) {
                                $('select[name="business_activity_id"]').append('<option value="' +
                                    business_activity_id_value +
                                    '">' + business_activity_id_value + '</option>');
                            }
                            $('select[name="business_activity_id"]').val(business_activity_id_value)
                                .change();
                            // $('input[name="region"]').empty();
                            // $('input[name="region"]').val(data.region.name);
                            // $('input[name="currency"]').empty();
                            // $('input[name="currency"]').val(data.currency_name);
                            // $('input[name="symbol"]').empty();
                            // $('input[name="symbol"]').val(data.currency_symbol);
                            // $('input[name="international_currency_code"]').empty();
                            // $('input[name="international_currency_code"]').val(data
                            //     .international_currency_code);
                            // $('input[name="denomination"]').empty();
                            // $('input[name="denomination"]').val(data.denomination_name);
                            // $('input[name="decimals"]').empty();
                            // $('input[name="decimals"]').val(data.no_of_decimals);


                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all inputs for number of units
            const unitInputs = document.querySelectorAll('.no-of-units');

            unitInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const unitId = this.dataset.id; // Get the unit ID from the data attribute
                    const dateFromInput = document.getElementById(`date-from-${unitId}`);
                    const dateToInput = document.getElementById(`date-to-${unitId}`);

                    // Get today's date
                    const today = new Date();
                    const nextYear = new Date(today);
                    nextYear.setFullYear(today.getFullYear() + 1);
                    nextYear.setDate(nextYear.getDate() - 1);

                    const formatDate = (date) => {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        return `${day}/${month}/${year}`;
                    };

                    dateFromInput.value = formatDate(today);
                    dateToInput.value = formatDate(nextYear);
                });
            });
        });
    </script>
@endpush
