@extends('layouts.back-end.app')

@section('title', ui_change('create_booking', 'property_transaction'))

@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $lang = Session::get('locale');
    @endphp
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-product-list.png') }}" alt=""> --}}
                {{ ui_change('create_booking', 'property_transaction') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.sales.inline-menu')


        <form id="productForm" class="product-form text-start" action="{{ route('sales.booking.store') }}" method="POST"
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
                                    value="{{ SalesProposalNo() }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_date', 'property_transaction') }} <span
                                        class="text-danger"> *</span></label></label>
                                <input type="text" class="form-control" id="booking_date" name="booking_date"
                                    class="form-control"  >
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('customer', 'property_transaction') }}
                                    <span class="text-danger"> *</span></label>
                                <button type="button" data-target="#add_customer" data-add_customer="" data-toggle="modal"
                                    class="btn btn--primary btn-sm">
                                    <i class="fa fa-plus-square"></i>
                                </button>
                                </label>
                                <select class="js-select2-custom form-control" id="customer_id" name="customer_id" required>
                                    <option value="" selected>{{ ui_change('select', 'property_transaction') }}
                                    </option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->name ?? $customer->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('customer_type', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="customer_type" readonly
                                    class="form-control">
                            </div>
                            @error('customer_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('total_no_of_required_units', 'property_transaction') }}
                                    <span class="text-danger"> *</span></label></label>
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
                        <h4 class="mb-0">{{ ui_change('customer_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 customer_form d-none company-form" id="company-form">

                            @include('admin-views.sales.enquiries.company_form')

                        </div>
                        <div class="col-md-12 customer_form d-none personal-form" id="personal-form">
                            @include('admin-views.sales.enquiries.personal_form')
                        </div>
                    </div>

                </div>

            </div>



            <div id="main-content"></div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset"
                    class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                <button type="submit" class="btn btn--primary px-5"
                    onclick="setFormAction('{{ route('sales.booking.store') }}')">{{ ui_change('submit', 'property_transaction') }}</button>
                {{-- <button type="submit" class="btn btn-warning px-5"
                    onclick="setFormAction('{{ route('sales.booking.search') }}')"><i
                        class="fa fa-search"></i>{{ ui_change('search', 'property_transaction') }}</button> --}}

            </div>
        </form>



    </div>
    <div class="modal fade" id="add_customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ ui_change('create_customer', 'property_transaction') }}</h5>
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
                        <div class="col-md-12 customer_form_create personal-form_create" id="personal-form_create">
                            <form id="customerForm_personal" action="{{ route('sales.customer.store_for_anything') }}"
                                method="post" class="customerForm">
                                @csrf
                                @method('post')
                                @include('admin-views.sales.customer.personal_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="savecustomerPersonal"
                                        class="btn btn--primary px-5 savecustomer">{{ ui_change('submit', 'property_transaction') }}</button>
                                </div>

                            </form>
                        </div>
                        <div class="col-md-12 customer_form_create d-none company-form_create" id="company-form_create">
                            <form id="customerForm_company" action="{{ route('sales.customer.store_for_anything') }}"
                                method="post" class="customerForm">
                                @csrf
                                @method('post')

                                @include('admin-views.sales.customer.company_form')
                                <div class="row justify-content-end gap-3 mt-3 mx-1">
                                    <button type="reset"
                                        class="btn btn-secondary px-5">{{ ui_change('reset', 'property_transaction') }}</button>
                                    <button type="submit" id="savecustomerCompany"
                                        class="btn btn--primary px-5 savecustomer">{{ ui_change('submit', 'property_transaction') }}</button>
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
         flatpickr("#booking_date", {
                dateFormat: "d/m/Y",
                minDate: "today",
                defaultDate: "today"
            });

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
            $(".customer_form_create").addClass('d-none');
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
        function unitFunc(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('sales.booking.get_units') }}",
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
                            if (value.sales_status === 'agreement') {
                                isBooked = 'style="background-color:red;color:white"';
                            } else if (value.sales_status === 'booking') {
                                isBooked =
                                    'style="background-color:#d500f9;color:white"';
                            } else if (value.sales_status === 'booking') {
                                isBooked =
                                    'style="background-color:#ffeb3b;color:black"';
                            } else if (value.sales_status === 'enquiry') {
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
    </script>
    <script>
        document.getElementById('total-no-units').addEventListener('input', function() {
            const totalUnits = parseInt(this.value) || 0;
            const container = document.getElementById('main-content');
            var decimals = parseFloat($('#decimals').val()) || 0;
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
                <label for="building">{{ ui_change('property', 'property_transaction') }}</label>
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
       

        
        <div class="col-md-6 col-lg-4 col-xl-6">
            <div class="form-group">
                <label for="notes">{{ ui_change('notes_comments', 'property_transaction') }}</label>
                <textarea id="notes" name="notes-${i}" class="form-control" rows="2"> </textarea>
            </div>
        </div>
    </div>

    <hr>
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-6">

            <div class="form-group">
                <label for="area-measurement">{{ ui_change('unit', 'property_transaction') }}</label>
                <select id="area-measurement" name="unit-${i}"  
                    class="js-select2-custom form-control">
                    <option>{{ ui_change('select_unit', 'property_transaction') }}</option>
                </select>
            </div>
        </div>
       
          <div class="col-md-6 col-lg-4 col-xl-3  " id="price-${i}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('price', 'property_transaction') }}</label>
                                            <input type="number" name="price-${i}" class="form-control"
                                                value="" step="0.001"
                                                placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3  "
                                        id="advance_percentage-${i}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('advance_percentage', 'property_transaction') }}</label>
                                            <input type="number" name="advance_percentage-${i}"
                                                class="form-control" value="" step="0.001"
                                                placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3  " id="advance_amount-${i}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('advance_amount', 'property_transaction') }}</label>
                                            <input type="number" readonly name="advance_amount-${i}"
                                                class="form-control text-white" value="" step="0.001"
                                                placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
      


                                                          <div class="col-md-6 col-lg-4 col-xl-3  "
                                        id="number_of_installments-${i}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('number_of_installments', 'property_transaction') }}</label>
                                            <input type="number" name="number_of_installments-${i}"
                                                class="form-control" value="" step="0.001"
                                                placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label>{{ ui_change('Payment Plan') }}</label>
                                            <select name="payment_plan-${i}" class="form-control">
                                                <option value="1">{{ ui_change('Monthly') }}</option>
                                                <option value="2">{{ ui_change('Bimonthly') }}</option>
                                                <option value="3">{{ ui_change('Quarterly') }}</option>
                                                <option value="4">{{ ui_change('Semi_Anuual') }}</option>
                                                <option value="5">{{ ui_change('Annual') }}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="">{{ ui_change('start_date', 'property_transaction') }}</label>
                                            <input type="text" class="form-control start_date text-white"
                                                name="start_date-${i}">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div id="installments_table_${i}"></div>
                                    </div> 

                </div>
            </div>
        `;
                // container.innerHTML += bladeContent;
                let tempDiv = document.createElement('div');
                tempDiv.innerHTML = bladeContent;
                container.appendChild(tempDiv);

                attachUnitEvents(i);
            }

            function attachUnitEvents(itemId) {

                let $price = $(`[name="price-${itemId}"]`);
                let $percentage = $(`[name="advance_percentage-${itemId}"]`);
                let $advance = $(`[name="advance_amount-${itemId}"]`);
                let $installments = $(`[name="number_of_installments-${itemId}"]`);
                let $plan = $(`[name="payment_plan-${itemId}"]`);
                let $start = $(`[name="start_date-${itemId}"]`);
                let $table = $(`#installments_table_${itemId}`);

                function calculateAdvance() {

                    let price = parseFloat($price.val()) || 0;
                    let percentage = parseFloat($percentage.val()) || 0;

                    let advanceAmount = (price * percentage) / 100;

                    $advance.val(advanceAmount.toFixed(decimals));

                    generateInstallments();
                }

                function generateInstallments() {

                    let price = parseFloat($price.val()) || 0;
                    let advance = parseFloat($advance.val()) || 0;
                    let installments = parseInt($installments.val()) || 0;
                    let planValue = parseInt($plan.val()) || 1;
                    let startVal = $start.val();

                    if (installments <= 0 || !startVal) {
                        $table.html('');
                        return;
                    }

                    let monthStep = 1;
                    if (planValue == 2) monthStep = 2;
                    if (planValue == 3) monthStep = 3;
                    if (planValue == 4) monthStep = 6;
                    if (planValue == 5) monthStep = 12;

                    let remaining = price - advance;
                    let installmentAmount = remaining / installments;

                    let parts = startVal.split('/');
                    let startDate = new Date(parts[2], parts[1] - 1, parts[0]);
                    let originalDay = startDate.getDate();

                    let html = `
        <table class="table table-bordered mt-3 text-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
        `;

                    for (let x = 0; x < installments; x++) {

                        let installmentDate = new Date(startDate);
                        installmentDate.setMonth(startDate.getMonth() + (x * monthStep));

                        if (installmentDate.getDate() !== originalDay) {
                            installmentDate.setDate(0);
                        }

                        let day = ("0" + installmentDate.getDate()).slice(-2);
                        let month = ("0" + (installmentDate.getMonth() + 1)).slice(-2);
                        let year = installmentDate.getFullYear();

                        let formattedDate = `${day}/${month}/${year}`;

                        html += `
            <tr>
                <td>${x + 1}</td>
                <td>
                    <input type="text" 
                        name="installment_date_${itemId}[]" 
                        class="form-control main_date text-white"
                        value="${formattedDate}">
                </td>
                <td>
                    <input type="number" 
                        name="installment_amount_${itemId}[]" 
                        class="form-control"
                        value="${installmentAmount.toFixed(decimals)}"
                        step="0.001">
                </td>
            </tr>
            `;
                    }

                    html += `</tbody></table>`;
                    $table.html(html);

                    flatpickr(`#installments_table_${itemId} .main_date`, {
                        dateFormat: "d/m/Y"
                    });
                }

                $price.on('input', calculateAdvance);
                $percentage.on('input', calculateAdvance);
                $installments.on('input', generateInstallments);
                $plan.on('change', generateInstallments);
                $start.on('change', generateInstallments);
            }


            flatpickr(".start_date", {
                dateFormat: "d/m/Y",
                minDate: "today",
                defaultDate: "today"
            });
        });
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
                                <label for="period-from-${i}"> ui_change('Period_From-_To' , 'property_transaction') </label>
                                <div style="display: flex; gap: 10px;">
                                    <input type="date" name="period_from-${i}" id="period-from-${i}" class="form-control">
                                    <input type="date" name="period_to-${i}" id="period-to-${i}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property-type-${i}"> ui_change('Property_Type' , 'property_transaction') </label>
                                <select id="property-type-${i}" name="property_type-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('any', 'property_transaction') }}</option>
                                    @foreach ($property_types as $property_type)
                                        <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${i}"> ui_change('City' , 'property_transaction') </label>
                                <input type="text" id="city-${i}" name="city-${i}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="total-area-${i}"> ui_change('Total_Area_Required' , 'property_transaction') </label>
                                <input type="number" id="total-area-${i}" name="total_area-${i}" class="form-control" step="0.001" value="0.000">
                            </div>
                            <div class="form-group">
                                <label for="area-measurement-${i}"> ui_change('Area_Measurement' , 'property_transaction') </label>
                                <select id="area-measurement-${i}" name="area_measurement-${i}" class="js-select2-custom form-control">
                                    <option> ui_change('Select_Area_Measurement' , 'property_transaction') </option>
                                    <option> ui_change('Sq._Mtr.' , 'property_transaction') </option>
                                    <option> ui_change('Sq._Ft.' , 'property_transaction') </option>
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
        $('select[name=customer_id]').on('change', function() {
            var customer_id = $(this).val();
            if (customer_id) {
                $.ajax({
                    url: "{{ URL::to('sales/enquiry/get_customer') }}/" + customer_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $('input[name="customer_type"]').empty();
                            $('input[name="customer_type"]').val(data.type);
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
                            // $('input[name="customer_type"]').empty();
                            // $('input[name="customer_type"]').val(data.customer_type);
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
