@extends('layouts.back-end.app')

@section('title', __('roles.create_booking'))
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
                <img src="{{ asset(main_path() . 'back-end/img/inhouse-product-list.png') }}" alt="">
                {{ __('roles.create_booking') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('booking.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <!-- general setup -->


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img width="20px" src="{{ asset('/public/assets/back-end/img/property.jpg') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ __('general.general_info') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ __('property_transactions.booking_no') }}</label>
                                <input readonly type="text" name="booking_no" class="form-control"
                                    value="{{ $booking->booking_no ?? bookingNo() }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ __('property_transactions.booking_date') }}</label>
                                <input type="text" class="form-control" id="booking_date" name="booking_date"
                                    class="form-control"  value="{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}">
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ __('property_transactions.tenant') }}
                                </label>
                                <select class="js-select2-custom form-control" id="tenant_id" name="tenant_id" required>
                                    <option >{{ __('general.select') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ ($tenant->id == $booking->tenant_id) ? 'selected' : ''  }}>
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
                                <label for="">{{ __('property_transactions.tenant_type') }}</label>
                                <input type="text" class="form-control" name="tenant_type" readonly class="form-control" value="{{ $booking->tenant->type }}">
                            </div>
                            @error('tenant_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ __('property_transactions.total_no_of_required_units') }}</label>
                                <input type="number" id="total-no-units" class="form-control"
                                    name="total_no_of_required_units" value="{{ $booking->total_no_of_required_units }}">
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
                        <img width="20px" src="{{ asset('/public/assets/back-end/img/seller-information.png') }}"
                            class="mb-1" alt="">
                        <h4 class="mb-0">{{ __('property_transactions.tenant_details') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-12 tenant_form d-none company-form" id="company-form">

                            @include('admin-views.property_transactions.enquiries.company_form')

                        </div>
                        <div class="col-md-12 tenant_form d-none personal-form" id="personal-form">
                            @include('admin-views.property_transactions.enquiries.personal_form')
                        </div>
                    </div>

                </div>

            </div>

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img width="40px" src="{{ asset('/public/assets/back-end/img/booking.jpg') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ __('property_transactions.booking_details') }}</h4>
                    </div>
                </div>
                @include('includes.property_transactions.main_info')
            </div>
            {{-- <div class="card mt-3 rest-part" id="main_content">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img width="40px" src="{{ asset('/public/assets/back-end/img/booking.jpg') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ __('property_transactions.unit_search_details') }}</h4>
                    </div>
                </div>
                <div class="card-body mt-3">
                    @include('includes.property_transactions.main_info')
                </div>
            </div> --}}

            <div id="main-content">

            </div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ __('general.reset') }}</button>
                <button type="submit" class="btn btn--primary px-5">{{ __('general.submit') }}</button>
            </div>
        </form>



    </div>
@endsection

@push('script')
    <script>
        flatpickr("#booking_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            // minDate: "today"
        });
        </script>
        @endpush
{{--
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
                            $('select[name="unit-' + i + '"]').append(
                                '<option value="' + value.id + '">' + value.unit_management_main
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

        function payment_mode_func(i) {
            var payment_mode_id = $('select[name="payment_mode-' + i + '"]').val();
            $('select[name="rent_mode-' + i + '"]').val(payment_mode_id);
            $('select[name="area_measurement-' + i + '"]').removeAttr('disabled');
            $('input[name="amount-' + i + '"]').removeAttr('disabled');
            $('input[name="total_area_amount-' + i + '"]').removeAttr('disabled');
        }

        function rent_mode_amount(i) {
            var amount = parseFloat($('input[name="amount-' + i + '"]').val()) || 0;
            var total_area_amount = parseFloat($('input[name="total_area_amount-' + i + '"]').val()) || 0;

            if (amount != 0 && total_area_amount != 0) {
                $('input[name="vat_percentage-' + i + '"]').removeAttr('disabled');

                var rent_amount = amount * total_area_amount;
                $('input[name="rent_amount-' + i + '"]').val(rent_amount.toFixed(2));
                vat_amount_func(i)
            }
        }

        function vat_amount_func(i) {
            var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;
            var rental_gl = parseFloat($('select[name="rental_gl-' + i + '"]').val()) || 0;
            var vat_percentage = $('input[name="vat_percentage-' + i + '"]').empty();

            var vat_amount = (rent_amount * rental_gl) / 100;
            var total_net_rent_amount = parseFloat(vat_amount) + parseFloat(rent_amount);
            $('input[name="vat_amount-' + i + '"]').val(vat_amount.toFixed(2));
            $('input[name="vat_percentage-' + i + '"]').val(rental_gl.toFixed(2));
            $('input[name="total_net_rent_amount-' + i + '"]').val(total_net_rent_amount.toFixed(2));
        }


        const counters = {};

        function add_service(i) {
            const container = document.getElementById('main_service_content-' + i);
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
<div class="row mt-1 bg-warning  border rounded p-2 " id="service-${i}-${counters[i]}"  >

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="charge_mode-${i}-${counters[i]}" class="form-control-label">Charge Mode</label> <span class="starColor">*</span>
            <select name="charge_mode-${i}-${counters[i]}[]" class="form-control" onchange="charge_type(${i},${counters[i]}) , amount_charge_func(${i},${counters[i]}) , percentage_amount_charge_func(${i},${counters[i]})">
                <option value="0">Select Other Charge Type</option>
                <option value="10">Service</option>
                <option value="0">Maintenance</option>
                <option value="10">House Keeping</option>
                <option value="0">Watch Man</option>
                <option value="10">Internet</option>
                <option value="0">Rental Income 0%</option>
            </select>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="charge_mode_type-${i}-${counters[i]}" class="form-control-label">Charge Mode</label> <span class="starColor">*</span>
            <select name="charge_mode_type-${i}-${counters[i]}[]" class="form-control" onchange="service_value_calc(${i},${counters[i]})">
                <option value="amount">{{ __('property_transactions.amount') }}</option>
                <option value="percentage">{{ __('property_transactions.percentage') }}</option>
            </select>
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3" id="amount_charge-${i}-${counters[i]}">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.amount') }}</label>
            <input type="number" onkeyup="amount_charge_func(${i},${counters[i]})" name="amount_charge-${i}-${counters[i]}[]" class="form-control"
                step="0.001" placeholder="0.000">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="percentage_amount_charge-${i}-${counters[i]}">
        <div class="form-group ">
            <label for="total-area">{{ __('property_transactions.percentage') }}</label>
            <input type="number" onkeyup="percentage_amount_charge_func(${i},${counters[i]})" name="percentage_amount_charge-${i}-${counters[i]}[]" class="form-control"
                step="0.001" placeholder="0.000">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.calculate_amount') }}</label>
            <input type="number" readonly id="total-area" name="calculate_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
                step="0.001" placeholder="0.000">
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.start_date') }}</label>
            <input type="date" name="amount_charge-${i}-${counters[i]}[]" class="form-control" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}" >
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.expaired_date') }}</label>
            <input type="date" name="expiry_date-${i}-${counters[i]}[]" class="form-control" value="{{ Carbon\Carbon::today()->addYear()->subDay()->format('Y-m-d') }}" >
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.vat_percentage') }}</label>
            <input type="number" readonly  name="vat_percentage-${i}-${counters[i]}[]" class="form-control  bg-white"
                step="0.001" placeholder="0.000">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.vat_amount') }}</label>
            <input type="number" readonly  name="vat_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
                step="0.001" placeholder="0.000">
        </div>
    </div>

    <div class="col-md-6 col-lg-4 col-xl-12">
        <div class="form-group">
            <label for="total-area">{{ __('property_transactions.total_amount') }}</label>
            <input type="number" readonly id="total-area" name="total_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
                step="0.001" placeholder="0.000">
        </div>
    </div>
</div>
`;
            container.innerHTML += bladeContent;
        }
        //     function add_service(i) {

        //         if (!counters[i]) {
        //             counters[i] = 0;
        //         }
        //         counters[i]++;
        //         const container = document.getElementById('main_service_content-' + i);
        //         const bladeContent = `
    //     <div class="row mt-1 bg-warning  border rounded p-2 " id="service-${i}-${counters[i]}"  >

    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="charge_mode-${i}-${counters[i]}" class="form-control-label">Charge Mode</label> <span class="starColor">*</span>
    //                 <select name="charge_mode-${i}-${counters[i]}[]" class="form-control" onchange="charge_type(${i},${counters[i]}) , amount_charge_func(${i},${counters[i]}) , percentage_amount_charge_func(${i},${counters[i]})">
    //                     <option value="0">Select Other Charge Type</option>
    //                     <option value="10">Service</option>
    //                     <option value="0">Maintenance</option>
    //                     <option value="10">House Keeping</option>
    //                     <option value="0">Watch Man</option>
    //                     <option value="10">Internet</option>
    //                     <option value="0">Rental Income 0%</option>
    //                 </select>
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="charge_mode_type-${i}-${counters[i]}" class="form-control-label">Charge Mode</label> <span class="starColor">*</span>
    //                 <select name="charge_mode_type-${i}-${counters[i]}[]" class="form-control" onchange="service_value_calc(${i},${counters[i]})">
    //                     <option value="amount">{{ __('property_transactions.amount') }}</option>
    //                     <option value="percentage">{{ __('property_transactions.percentage') }}</option>
    //                 </select>
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3" id="amount_charge-${i}-${counters[i]}">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.amount') }}</label>
    //                 <input type="number" onkeyup="amount_charge_func(${i},${counters[i]})" name="amount_charge-${i}-${counters[i]}[]" class="form-control"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3 d-none" id="percentage_amount_charge-${i}-${counters[i]}">
    //             <div class="form-group ">
    //                 <label for="total-area">{{ __('property_transactions.percentage') }}</label>
    //                 <input type="number" onkeyup="percentage_amount_charge_func(${i},${counters[i]})" name="percentage_amount_charge-${i}-${counters[i]}[]" class="form-control"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.calculate_amount') }}</label>
    //                 <input type="number" readonly id="total-area" name="calculate_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>
    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.start_date') }}</label>
    //                 <input type="date" name="amount_charge-${i}-${counters[i]}[]" class="form-control" value="{{ Carbon\Carbon::today()->format('Y-m-d') }}" >
    //             </div>
    //         </div>
    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.expaired_date') }}</label>
    //                 <input type="date" name="expiry_date-${i}-${counters[i]}[]" class="form-control" value="{{ Carbon\Carbon::today()->addYear()->subDay()->format('Y-m-d') }}" >
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.vat_percentage') }}</label>
    //                 <input type="number" readonly  name="vat_percentage-${i}-${counters[i]}[]" class="form-control  bg-white"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-3">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.vat_amount') }}</label>
    //                 <input type="number" readonly  name="vat_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>

    //         <div class="col-md-6 col-lg-4 col-xl-12">
    //             <div class="form-group">
    //                 <label for="total-area">{{ __('property_transactions.total_amount') }}</label>
    //                 <input type="number" readonly id="total-area" name="total_amount-${i}-${counters[i]}[]" class="form-control  bg-white"
    //                     step="0.001" placeholder="0.000">
    //             </div>
    //         </div>
    //     </div>
    // `;
        //         container.innerHTML += bladeContent;
        //     }

        function charge_type(i, j) {
            var charge_type_amount = $('select[name="charge_mode-' + i + '-' + j + '[]"]').val();
            var vat_percentage = $('input[name="vat_percentage-' + i + '-' + j + '[]"]').val(charge_type_amount);
        }

        function service_value_calc(i, j) {
            var service_type = $('select[name="charge_mode_type-' + i + '-' + j + '[]"]').val();
            if (service_type == 'percentage') {
                $('#percentage_amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#amount_charge-' + i + '-' + j + '').addClass('d-none');
                var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;

            } else if (service_type == 'amount') {
                $('#amount_charge-' + i + '-' + j + '').removeClass('d-none');
                $('#percentage_amount_charge-' + i + '-' + j + '').addClass('d-none');
            }
        }

        function amount_charge_func(i, j) {
            var amount_val = parseFloat($('input[name="amount_charge-' + i + '-' + j + '[]"]').val()) || 0;
            var vat_percentage_val = parseFloat($('input[name="vat_percentage-' + i + '-' + j + '[]"]').val()) || 0;
            var total_vat_service = amount_val * (vat_percentage_val / 100);
            var total_amount_val = amount_val + total_vat_service;
            $('input[name="vat_amount-' + i + '-' + j + '[]"]').val(total_vat_service.toFixed(2));
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').val(amount_val.toFixed(2));
            $('input[name="total_amount-' + i + '-' + j + '[]"]').empty().val(total_amount_val.toFixed(2));
        }

        function percentage_amount_charge_func(i, j) {
            var percentage_amount_charge_val = parseFloat($('input[name="percentage_amount_charge-' + i + '-' + j + '[]"]')
                .val()) || 0;
            var rent_amount = parseFloat($('input[name="rent_amount-' + i + '"]').val()) || 0;
            var percentage_amount = rent_amount * (percentage_amount_charge_val / 100);
            $('input[name="calculate_amount-' + i + '-' + j + '[]"]').val(percentage_amount.toFixed(2));
            var vat_percentage_val = parseFloat($('input[name="vat_percentage-' + i + '-' + j + '[]"]').val()) || 0;
            var total_vat_service = percentage_amount * (vat_percentage_val / 100);
            var total_amount_val = percentage_amount + total_vat_service;
            $('input[name="vat_amount-' + i + '-' + j + '[]"]').val(total_vat_service.toFixed(2));
            $('input[name="total_amount-' + i + '-' + j + '[]"]').val(total_amount_val.toFixed(2));
            // $('input[name="grand_total_amount-' + i + '-' + j + '[]"]').val(total_amount_val.toFixed(2));
        }
    </script>
    <script>
        document.getElementById('total-no-units').addEventListener('input', function() {
            const totalUnits = parseInt(this.value) || 0;
            const container = document.getElementById('main-content');

            container.innerHTML = '';

            for (let i = 1; i <= totalUnits; i++) {
                const bladeContent = `
                        <div class="card mt-3 rest-part" id="main_content">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img width="40px" src="{{ asset('/public/assets/back-end/img/booking.jpg') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ __('property_transactions.unit_search_details') }}</h4>
                    </div>
                </div>
                <div class="card-body mt-3">


                    <div class="form-container mt-3">
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="building">{{ __('property_management.property') }}</label>
                <select id="building" name="property_id-${i}"  onchange="unitFunc(${i})" class="js-select2-custom form-control">
                    <option value="0">{{ __('general.select') }}</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="unit-description">{{ __('property_master.unit_description') }}</label>
                <select id="unit-description" name="unit_description_id-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="0">{{ __('property_transactions.any') }}</option>
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
                <label for="unit-type">{{ __('property_transactions.unit_type') }}</label>
                <select id="unit-type" name="unit_type_id-${i}" class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="0">{{ __('property_transactions.any') }}</option>
                    @foreach ($unit_types as $unit_type)
                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="unit-condition">{{ __('property_transactions.unit_condition') }}</label>
                <select id="unit-condition" name="unit_condition_id-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="">{{ __('property_transactions.any') }}</option>
                    @foreach ($unit_conditions as $unit_condition)
                        <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="preferred-view">{{ __('property_transactions.preferred_view') }}</label>
                <select id="preferred-view" name="view_id-${i}"  onchange="unitFunc(${i})" class="js-select2-custom form-control">
                    <option value="">{{ __('property_transactions.any') }}</option>
                    @foreach ($views as $view)
                        <option value="{{ $view->id }}">{{ $view->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="property-type">{{ __('property_master.property_type') }}</label>
                <select id="property-type" name="property_type-${i}"
                    class="js-select2-custom form-control"  onchange="unitFunc(${i})">
                    <option value="">{{ __('property_transactions.any') }}</option>
                    @foreach ($property_types as $property_type)
                        <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="period-from">{{ __('property_transactions.period_from_to') }}</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="period_from-${i}" id="period-from" class="form-control main_data">
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="price" class="title-color"> </label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="period_to-${i}" id="period-to" class="form-control mt-2 main_data">
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="city">{{ __('property_transactions.city') }}</label>
                <input type="text" id="city" name="city-${i}" class="form-control">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.total_area_required') }}</label>
                <input type="number" id="total-area" name="total_area-${i}" class="form-control"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="area-measurement">{{ __('property_transactions.area_measurement') }}</label>
                <select id="area-measurement" name="area_measurement-${i}"
                    class="js-select2-custom form-control">
                    <option>{{ __('property_transactions.select_area_measurement') }}</option>
                    <option>Sq. Mtr.</option>
                    <option>Sq. Ft.</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <label for="notes">{{ __('property_transactions.notes_comments') }}</label>
                <textarea id="notes" name="notes-${i}" class="form-control" rows="2"> </textarea>
            </div>
        </div>
    </div>

    <hr>
    <div class="form-row">
        <div class="col-md-6 col-lg-4 col-xl-6">

            <div class="form-group">
                <label for="area-measurement">{{ __('property_master.unit') }}</label>
                <select id="area-measurement" name="unit-${i}"
                    class="js-select2-custom form-control">
                    <option>{{ __('property_transactions.select_unit') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">

            <div class="form-group">
                <label for="area-measurement">{{ __('property_transactions.payment_mode') }}</label>
                <select id="area-measurement" name="payment_mode-${i}" onchange="payment_mode_func(${i})"
                    class="js-select2-custom form-control">
                    <option value="0">{{ __('property_transactions.select_payment_mode') }}</option>
                    <option value="1">{{ __('property_transactions.daily') }}</option>
                    <option value="2">{{ __('property_transactions.monthly') }}</option>
                    <option value="3">{{ __('property_transactions.bi_monthly') }}</option>
                    <option value="4">{{ __('property_transactions.quarterly') }}</option>
                    <option value="5">{{ __('property_transactions.half_yearly') }}</option>
                    <option value="6">{{ __('property_transactions.yearly') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ __('property_transactions.pdc') }}</label>
                <select id="area-measurement" name="pdc-${i}" class="js-select2-custom form-control">
                    <option>{{ __('property_transactions.yes') }}</option>
                    <option>{{ __('property_transactions.no') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label
                    for="area-measurement">{{ __('property_transactions.area_measurement') }}</label>
                <select id="area-measurement" disabled name="area_measurement-${i}"
                    class="js-select2-custom form-control">
                    <option>Sq. Mtr.</option>
                    <option>Sq. Ft.</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.total_area') }}</label>
                <input type="number" disabled  name="total_area_amount-${i}" class="form-control" placeholder="0.000"  >
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.amount') }}</label>
                <input type="number" disabled  name="amount-${i}" class="form-control" onkeyup="rent_mode_amount(${i})"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.rent_amount') }}</label>
                <input type="number"   name="rent_amount-${i}" class="form-control" onkeyup="rent_mode_amount(${i})"
                    step="0.001" placeholder="0.000">
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ __('property_transactions.rent_mode') }}</label>
                <select id="area-measurement" name="rent_mode-${i}"
                    class="js-select2-custom form-control">
                    <option value="0">{{ __('property_transactions.select_rent_mode') }}</option>
                    <option value="1">{{ __('property_transactions.daily') }}</option>
                    <option value="2">{{ __('property_transactions.monthly') }}</option>
                    <option value="3">{{ __('property_transactions.bi_monthly') }}</option>
                    <option value="4">{{ __('property_transactions.quarterly') }}</option>
                    <option value="5">{{ __('property_transactions.half_yearly') }}</option>
                    <option value="6">{{ __('property_transactions.yearly') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="area-measurement">{{ __('property_transactions.rental_gl') }}</label>
                <select id="area-measurement" name="rental_gl-${i}"
                    class="js-select2-custom form-control"  onchange="vat_amount_func(${i})">
                    <option value="0">{{ __('property_transactions.select_rental_gl') }}</option>
                    <option value="0">Rental Income 0%</option>
                    <option value="10">Rental Income 10%</option>
                    <option value="20">Rental Income 20%</option>
                    <option value="30">Rental Income 30%</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.vat_percentage') }}</label>
                <input type="number" readonly name="vat_percentage-${i}" class="form-control"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="form-group">
                <label for="total-area">{{ __('property_transactions.vat_amount') }}</label>
                <input type="number" readonly name="vat_amount-${i}" class="form-control" step="0.001"
                    placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <label
                    for="total-area">{{ __('property_transactions.total_net_rent_amount') }}</label>
                <input type="number" readonly name="total_net_rent_amount-${i}" class="form-control"
                    step="0.001" placeholder="0.000">
            </div>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-12">
            <div class="form-group">
                <button type="button" class="btn btn--primary form-control" onclick="add_service(${i})">{{ __('property_transactions.add_other_services') }}</button>
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
                            for="total-area">{{ __('property_transactions.security_deposit_months_rent') }}</label>
                        <input type="number"  name="security_deposit_months_rent-${i}"
                            class="form-control" step="0.001" placeholder="0.000">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label
                            for="total-area">{{ __('property_transactions.security_deposit_amount') }}</label>
                        <input type="number"  name="security_deposit_amount-${i}" class="form-control"
                            step="0.001" placeholder="0.000">
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label
                            for="area-measurement">{{ __('property_transactions.is_rent_inclusive_of_ewa') }}</label>
                        <select name="is_rent_inclusive_of_ewa-${i}" class="js-select2-custom form-control">
                            <option>{{ __('general.yes') }}</option>
                            <option>{{ __('general.no') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="area-measurement">{{ __('property_transactions.ewa_limit_mode') }}</label>
                        <select name="ewa_limit_mode-${i}" class="js-select2-custom form-control">
                            <option>{{ __('property_transactions.monthly') }}</option>
                            <option>{{ __('property_transactions.yearly') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="total-area">{{ __('property_transactions.ewa_limit_monthly') }}</label>
                        <input type="number"  name="ewa_limit_monthly-${i}" class="form-control"
                            step="0.001" value="0.000">
                    </div>
                </div>
                <div class="col-md-12 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="">{{ __('property_transactions.lease_break_date') }}</label>
                        <input type="text" class="form-control main_date" name="lease_break_date-${i}">
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="form-group">
                        <label for="area-measurement">{{ __('property_transactions.notice_period') }}</label>
                        <select name="notice_period-${i}" class="js-select2-custom form-control">
                            <option>{{ __('property_transactions.one_month') }}</option>
                            <option>{{ __('property_transactions.two_month') }}</option>
                            <option>{{ __('property_transactions.three_month') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-6">
                    <div class="form-group">
                        <label for="total-area">{{ __('property_transactions.lease_break_comments') }}</label>
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
        });
        // <div class="col-md-6 col-lg-4 col-xl-3">
        //     <div class="form-group">
        //         <label for="total-area">{{ __('property_transactions.grand_total_amount') }}</label>
        //         <input type="number" disabled name="grand_total_amount-${i}" class="form-control"
        //             step="0.001" placeholder="0.000">
        //     </div>
        // </div>
        // <div class="col-md-6 col-lg-4 col-xl-3">
        //     <div class="form-group">
        //         <label for="total-area">{{ __('property_transactions.vat_total_amount') }}</label>
        //         <input type="number" disabled name="vat_total_amount-${i}" class="form-control"
        //             step="0.001" placeholder="0.000">
        //     </div>
        // </div>
        // <div class="col-md-6 col-lg-4 col-xl-3">
        //     <div class="form-group">
        //         <label for="total-area">{{ __('property_transactions.net_total_amount') }}</label>
        //         <input type="number" disabled name="net_total_amount-${i}" class="form-control"
        //             step="0.001" placeholder="0.000">
        //     </div>
        // </div>
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
                    <label for="building-${i}">Building</label>
                    <select id="building-${i}" name="property_id-${i}" class="js-select2-custom form-control">
                        <option value="">Select building</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="unit-description-${i}">Unit Description</label>
                    <select id="unit-description-${i}" name="unit_description_id-${i}" class="js-select2-custom form-control">
                        <option value="">{{ __('property_transactions.any') }}</option>
                        @foreach ($unit_descriptions as $unit_description)
                            <option value="{{ $unit_description->id }}"
                                ${currentDescriptionId == '{{ $unit_description->id }}' ? 'selected' : ''}>
                                {{ $unit_description->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                            <div class="form-group">
                                <label for="unit-type-${i}">Unit Type</label>
                                <select id="unit-type-${i}" name="unit_type_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ __('property_transactions.any') }}</option>
                                    @foreach ($unit_types as $unit_type)
                                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="unit-condition-${i}">Unit Condition</label>
                                <select id="unit-condition-${i}" name="unit_condition_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ __('property_transactions.any') }}</option>
                                    @foreach ($unit_conditions as $unit_condition)
                                        <option value="{{ $unit_condition->id }}">{{ $unit_condition->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="preferred-view-${i}">Preferred View</label>
                                <select id="preferred-view-${i}" name="view_id-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ __('property_transactions.any') }}</option>
                                    @foreach ($views as $view)
                                        <option value="{{ $view->id }}">{{ $view->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="period-from-${i}">Period From-To</label>
                                <div style="display: flex; gap: 10px;">
                                    <input type="date" name="period_from-${i}" id="period-from-${i}" class="form-control">
                                    <input type="date" name="period_to-${i}" id="period-to-${i}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property-type-${i}">Property Type</label>
                                <select id="property-type-${i}" name="property_type-${i}" class="js-select2-custom form-control">
                                    <option value="">{{ __('property_transactions.any') }}</option>
                                    @foreach ($property_types as $property_type)
                                        <option value="{{ $property_type->id }}">{{ $property_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="city-${i}">City</label>
                                <input type="text" id="city-${i}" name="city-${i}" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="total-area-${i}">Total Area Required</label>
                                <input type="number" id="total-area-${i}" name="total_area-${i}" class="form-control" step="0.001" value="0.000">
                            </div>
                            <div class="form-group">
                                <label for="area-measurement-${i}">Area Measurement</label>
                                <select id="area-measurement-${i}" name="area_measurement-${i}" class="js-select2-custom form-control">
                                    <option>Select Area Measurement</option>
                                    <option>Sq. Mtr.</option>
                                    <option>Sq. Ft.</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notes-${i}">Notes / Comments</label>
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
 --}}
