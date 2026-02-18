@extends('layouts.back-end.app')

@section('title', ui_change('create_booking', 'property_transaction'))
@php
    $lang = Session::get('locale');

@endphp
@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-product-list.png') }}" alt=""> --}}
                {{ ui_change('create_booking', 'property_transaction') }}
            </h2>
        </div>
        <input type="hidden" id="decimals" name="decimals" value="{{ $company->decimals }}">
        <!-- End Page Title -->
        @include('admin-views.inline_menu.sales.inline-menu')
        @include('admin-views.sales.enquiries.customer_model')

        <form class="product-form text-start" action="{{ route('sales.proposal.store_to_booking') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center" style="min-height: 40px;">
                        <h4 class="mb-0">{{ ui_change('general_info', 'property_transaction') }}</h4>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_no', 'property_transaction') }}</label>
                                <input readonly type="text" name="booking_no" class="form-control"
                                    value="{{ SalesBookingNo() }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_date', 'property_transaction') }}<span
                                        class="text-danger"> *</span></label></label>
                                <input type="text" class="form-control" name="booking_date" id="add_to_booking_date"
                                    class="form-control"
                                    value="{{ \Carbon\Carbon::parse($proposal->proposal_date)->format('d-m-Y') }}">
                                <input type="hidden" class="form-control" name="proposal_id" class="form-control"
                                    value="{{ $proposal->id }}">
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('customer', 'property_transaction') }} <span
                                        class="text-danger"> *</span></label>

                                <button type="button" data-target="#add_customer" data-add_customer="" data-toggle="modal"
                                    class="btn btn--primary btn-sm">
                                    <i class="fa fa-plus-square"></i>
                                </button>
                                </label>
                                <select class="js-select2-custom form-control" id="customer_id" name="customer_id" required>
                                    <option>{{ ui_change('select', 'property_transaction') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ $customer->id == $proposal->customer_id ? 'selected' : '' }}>
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


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('customer_type', 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="customer_type" readonly
                                    class="form-control" value="{{ $proposal->customer?->type }}">
                            </div>
                            @error('customer_type')
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
                        <img width="20px" src="{{ asset('assets/back-end/img/seller-information.png') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ ui_change('customer_details', 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 customer_form @if ($proposal->customer?->type == 'individual') d-none @endif company-form"
                            id="company-form">

                            @include('admin-views.sales.enquiries.company_form')

                        </div>
                        <div class="col-md-12 customer_form @if ($proposal->customer?->type == 'company') d-none @endif personal-form"
                            id="personal-form">
                            @include('admin-views.sales.enquiries.personal_form')
                        </div>
                    </div>

                </div>

            </div>



            <div id="main-content">
                @php
                    $unitCount = count($proposal->proposal_units);
                @endphp
                @foreach ($proposal->proposal_units as $item)
                    <div class="card mt-3 rest-part bg--primary" id="item-{{ $item->id }}"
                        style="background-color: #2b368f;color:white">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <h4 class="mb-0">{{ ui_change('unit_search_details', 'property_transaction') }}</h4>
                            </div>
                            @if ($unitCount > 1)
                                <a type="button" class="btn btn-danger btn-sm"
                                    href="{{ route('sales.proposal.empty_unit_from_proposal_unit', $item->id) }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endif
                        </div>
                        <div class="card-body mt-3">
                            <div class="form-container mt-3">
                                <div class="form-row">
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="building">{{ ui_change('property', 'property_transaction') }}</label>
                                            <select id="building" name="property_id-{{ $item->id }}"
                                                onchange="unitFuncByBuilding({{ $item->id }})"
                                                class="js-select2-custom form-control">
                                                <option value="">{{ ui_change('select', 'property_transaction') }}
                                                </option>
                                                @foreach ($buildings as $building)
                                                    <option value="{{ $building->id }}"
                                                        {{ isset($item->unit_management_id) && $building->id == $item->property_management_id ? 'selected' : '' }}>
                                                        {{ $building->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="unit-description">{{ ui_change('unit_description', 'property_transaction') }}</label>
                                            <select id="unit-description" name="unit_description_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFuncByUnitDesc({{ $item->id }})">
                                                <option value="0">{{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($unit_descriptions as $unit_description)
                                                    <option value="{{ $unit_description->id }}"
                                                        {{ $unit_description->id == $item->unit_description_id ? 'selected' : '' }}>
                                                        {{ $unit_description->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="unit-type">{{ ui_change('unit_type', 'property_transaction') }}</label>
                                            <select id="unit-type" name="unit_type_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFuncByUnitType({{ $item->id }})">
                                                <option value="0">{{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($unit_types as $unit_type)
                                                    <option value="{{ $unit_type->id }}"
                                                        {{ $unit_type->id == $item->unit_type_id ? 'selected' : '' }}>
                                                        {{ $unit_type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="unit-condition">{{ ui_change('unit_condition', 'property_transaction') }}</label>
                                            <select id="unit-condition" name="unit_condition_id-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFuncByUnitCond({{ $item->id }})">
                                                <option value="">{{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($unit_conditions as $unit_condition)
                                                    <option value="{{ $unit_condition->id }}"
                                                        {{ $unit_condition->id == $item->unit_condition_id ? 'selected' : '' }}>
                                                        {{ $unit_condition->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">

                                        <div class="form-group">
                                            <label
                                                for="preferred-view">{{ ui_change('preferred_view', 'property_transaction') }}</label>
                                            <select id="preferred-view" name="view_id-{{ $item->id }}"
                                                onchange="unitFunc({{ $item->id }})"
                                                class="js-select2-custom form-control">
                                                <option value="">{{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($views as $view)
                                                    <option value="{{ $view->id }}"
                                                        {{ $view->id == $item->view_id ? 'selected' : '' }}>
                                                        {{ $view->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="property-type">{{ ui_change('property_type', 'property_transaction') }}</label>
                                            <select id="property-type" name="property_type-{{ $item->id }}"
                                                class="js-select2-custom form-control"
                                                onchange="unitFunc({{ $item->id }})">
                                                <option value="">{{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($property_types as $property_type)
                                                    <option value="{{ $property_type->id }}">{{ $property_type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4 col-xl-6">
                                        <div class="form-group">
                                            <label
                                                for="notes">{{ ui_change('notes_comments', 'property_transaction') }}</label>
                                            <textarea id="notes" name="notes-{{ $item->id }}" class="form-control" rows="2"> </textarea>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6 col-lg-4 col-xl-6">

                                        <div class="form-group">
                                            <label
                                                for="area-measurement">{{ ui_change('unit', 'property_transaction') }}</label>
                                            <select name="unit-{{ $item->id }}"
                                                class="js-select2-custom form-control" required>
                                                @if (isset($item->unit_management_id))
                                                    <option value="{{ $item->unit_management?->id }}">
                                                        {{ $item->unit_management->property_unit_management->name .
                                                            '-' .
                                                            $item->unit_management->block_unit_management->block->name .
                                                            '-' .
                                                            $item->unit_management->floor_unit_management->floor_management_main->name .
                                                            '-' .
                                                            $item->unit_management->unit_management_main->name }}
                                                    </option>
                                                @endif

                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-6 col-lg-4 col-xl-3  " id="price-{{ $item->id }}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('price', 'property_transaction') }}</label>
                                            <input type="number" name="price-{{ $item->id }}" class="form-control"
                                                value="{{ $item->price }}" data-id="{{ $item->id }}"
                                                step="0.001" placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3  "
                                        id="advance_percentage-{{ $item->id }}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('advance_percentage', 'property_transaction') }}</label>
                                            <input type="number" name="advance_percentage-{{ $item->id }}"
                                                class="form-control" data-id="{{ $item->id }}" value="{{ $item->advance_percentage }}"
                                                step="0.001" placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3  " id="advance_amount-{{ $item->id }}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('advance_amount', 'property_transaction') }}</label>
                                            <input type="number" readonly name="advance_amount-{{ $item->id }}"
                                                class="form-control text-white" data-id="{{ $item->id }}"
                                                value="{{ $item->advance_amount }}" step="0.001"
                                                placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3  "
                                        id="number_of_installments-{{ $item->id }}">
                                        <div class="form-group">
                                            <label
                                                for="total-area">{{ ui_change('number_of_installments', 'property_transaction') }}</label>
                                            <input type="number" name="number_of_installments-{{ $item->id }}"
                                                class="form-control" data-id="{{ $item->id }}" 
                                                step="0.001" value="{{ $item->number_of_installments }}"  placeholder="{{ number_format(0, $company->decimals) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label>{{ ui_change('Payment Plan') }}</label>
                                            <select data-id="{{ $item->id }}"
                                                name="payment_plan-{{ $item->id }}" class="form-control">
                                                <option value="1" {{ ($item->payment_plan == 1 ) ? 'selected' : '' }}>{{ ui_change('Monthly') }}</option>
                                                <option value="2" {{ ($item->payment_plan == 2 ) ? 'selected' : '' }}>{{ ui_change('Bimonthly') }}</option>
                                                <option value="3" {{ ($item->payment_plan == 3 ) ? 'selected' : '' }}>{{ ui_change('Quarterly') }}</option>
                                                <option value="4" {{ ($item->payment_plan == 4 ) ? 'selected' : '' }}>{{ ui_change('Semi_Anuual') }}</option>
                                                <option value="5" {{ ($item->payment_plan == 5 ) ? 'selected' : '' }}>{{ ui_change('Annual') }}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-12 col-lg-4 col-xl-3">
                                        <div class="form-group">
                                            <label
                                                for="">{{ ui_change('start_date', 'property_transaction') }}</label>
                                            <input data-id="{{ $item->id }}" type="text"
                                                class="form-control start_date text-white"
                                                name="start_date-{{ $item->id }}" value="{{ isset($item->start_date) ? \Carbon\Carbon::createFromFormat('Y-m-d' , $item->start_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div id="installments_table_{{ $item->id }}">

                                            @if ($item->installments && $item->installments->count())
                                                <table class="table table-bordered mt-3 text-white">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ ui_change('Installment Amount') }}</th>
                                                            <th>{{ ui_change('Date') }}</th>
                                                            <th>{{ ui_change('Amount') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($item->installments as $index => $installment)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ number_format($installment->amount, 2) }}</td>
                                                                <td>
                                                                    <input type="text"
                                                                        name="installment_date_{{ $item->id }}[]"
                                                                        class="form-control main_date text-white"
                                                                        value="{{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="installment_amount_{{ $item->id }}[]"
                                                                        class="form-control installment-input"
                                                                        value="{{ $installment->amount }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="submit"
                    class="btn btn--primary px-5">{{ ui_change('submit', 'property_transaction') }}</button>
            </div>
        </form>
    </div>

@endsection

@push('script')
    <script>
        $(document).ready(function() {

            function calculateAdvance(itemId) {

                let $price = $(`[name="price-${itemId}"]`);
                let $percentage = $(`[name="advance_percentage-${itemId}"]`);
                let $advance = $(`[name="advance_amount-${itemId}"]`);

                let price = parseFloat($price.val()) || 0;
                let percentage = parseFloat($percentage.val()) || 0;

                let advanceAmount = (price * percentage) / 100;

                $advance.val(advanceAmount.toFixed(2));

                generateInstallments(itemId);
            }

            function generateInstallments(itemId) {

                let price = parseFloat($(`[name="price-${itemId}"]`).val()) || 0;
                let advance = parseFloat($(`[name="advance_amount-${itemId}"]`).val()) || 0;
                let installments = parseInt($(`[name="number_of_installments-${itemId}"]`).val()) || 0;
                let planValue = parseInt($(`[name="payment_plan-${itemId}"]`).val()) || 1;
                let startVal = $(`[name="start_date-${itemId}"]`).val();
                let $table = $(`#installments_table_${itemId}`);

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
                    <th>Installment Amount</th>
                    <th>Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
        `;

                for (let i = 0; i < installments; i++) {

                    let installmentDate = new Date(startDate);
                    installmentDate.setMonth(startDate.getMonth() + (i * monthStep));

                    if (installmentDate.getDate() !== originalDay) {
                        installmentDate.setDate(0);
                    }

                    let day = ("0" + installmentDate.getDate()).slice(-2);
                    let month = ("0" + (installmentDate.getMonth() + 1)).slice(-2);
                    let year = installmentDate.getFullYear();

                    let formattedDate = `${day}/${month}/${year}`;

                    html += `
            <tr>
                <td>${i + 1}</td>
                <td>${installmentAmount.toFixed(2)}</td>
                <td>
                    <input type="text" 
                           name="installment_date_${itemId}[]" 
                           class="form-control main_date text-white" 
                           value="${formattedDate}">
                </td>
                <td>
                    <input type="number" 
                           name="installment_amount_${itemId}[]" 
                           class="form-control installment-input" 
                           value="${installmentAmount.toFixed(2)}">
                </td>
            </tr>
            `;
                }

                html += `</tbody></table>`;
                $table.html(html);

                flatpickr($table.find(".main_date"), {
                    dateFormat: "d/m/Y"
                });
            }
            $(document).on('input', '[name^="price-"], [name^="advance_percentage-"]', function() {
                let itemId = $(this).attr('name').split('-')[1];
                calculateAdvance(itemId);
            });

            $(document).on('input', '[name^="number_of_installments-"]', function() {
                let itemId = $(this).attr('name').split('-')[1];
                generateInstallments(itemId);
            });

            $(document).on('change', '[name^="payment_plan-"], [name^="start_date-"]', function() {
                let itemId = $(this).attr('name').split('-')[1];
                generateInstallments(itemId);
            });

        });
    </script>
 


    <script>
        flatpickr(".main_date", {
            dateFormat: "d/m/Y",
            minDate: "today"
        });
        flatpickr(".start_date", {
            dateFormat: "d/m/Y",
            minDate: "today", 
        });


        function deleteItem(itemId) {
            $.ajax({
                url: "{{ route('proposal.empty_unit_from_proposal_unit', ':id') }}".replace(':id', itemId),
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        document.getElementById("item-" + itemId).remove();
                    }
                    swal("Success", "{{ ui_change('deleted_successfully', 'property_transaction') }}",
                        "success");

                },
                error: function(xhr, status, error) {
                    console.error("Error occurred:", error);

                }
            });
        }

        function unitFuncByBuilding(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('proposal.get_units') }}",
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.property_unit_management
                                .name + ' - ' +
                                value.block_unit_management.block.name + ' - ' +
                                value.floor_unit_management.floor_management_main.name + ' - ' +
                                value.unit_management_main
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

        function unitFuncByUnitDesc(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('proposal.get_units') }}",
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.property_unit_management
                                .name + ' - ' +
                                value.block_unit_management.block.name + ' - ' +
                                value.floor_unit_management.floor_management_main.name + ' - ' +
                                value.unit_management_main
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

        function unitFuncByUnitCond(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('proposal.get_units') }}",
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.property_unit_management
                                .name + ' - ' +
                                value.block_unit_management.block.name + ' - ' +
                                value.floor_unit_management.floor_management_main.name + ' - ' +
                                value.unit_management_main
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

        function unitFuncByUnitType(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('proposal.get_units') }}",
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.property_unit_management
                                .name + ' - ' +
                                value.block_unit_management.block.name + ' - ' +
                                value.floor_unit_management.floor_management_main.name + ' - ' +
                                value.unit_management_main
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

        function unitFunc(i) {
            var property_id = $('select[name="property_id-' + i + '"]').val();
            var unit_description_id = $('select[name="unit_description_id-' + i + '"]').val();
            var unit_type_id = $('select[name="unit_type_id-' + i + '"]').val();
            var unit_condition_id = $('select[name="unit_condition_id-' + i + '"]').val();
            var view_id = $('select[name="view_id-' + i + '"]').val();
            var property_type = $('select[name="property_type-' + i + '"]').val();
            (property_id, unit_description_id, unit_type_id, unit_condition_id, view_id, property_type);

            $.ajax({
                url: "{{ route('proposal.get_units') }}",
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.property_unit_management
                                .name + ' - ' +
                                value.block_unit_management.block.name + ' - ' +
                                value.floor_unit_management.floor_management_main.name + ' - ' +
                                value.unit_management_main
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
@endpush
