@extends('layouts.back-end.app')

@section('title', ui_change('booking_page', 'room_reservation'))

@push('css_or_js')
    <style>
        .info-row p {
            margin-bottom: 6px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .upload-btn {
            background-color: #17a2b8;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-inline-start: 8px;
        }

        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 34px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #ccc;
            background: #f8f9fa;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            border-radius: 4px;
        }

        .qty-btn:hover {
            background: #e9ecef;
        }
    </style>
@endpush
@php

    $adults = 0;
    $children = 0;
    foreach ($unit_managements as $unit) {
        $adults += $unit->adults;
        $children += $unit->children;
    }
@endphp
@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">Check-In</h1>
        </div>

        <form method="POST" action="{{ route('booking.checkin_dir.submit') }}" enctype="multipart/form-data">
            @csrf

            <!-- Booking Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row info-row">

                        {{-- Customer --}}
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Customer :</label>
                                <select name="tenant_id" class="js-select2-custom form-control" required>
                                    <option value="">{{ ui_change('select_tenant') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" data-mobile="{{ $tenant->contact_no ?? '' }}"
                                            data-passport="{{ $tenant->id_number ?? '' }}">
                                            {{ $tenant->name ?? $tenant->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Customer :</label>
                                <select name="tenant_id" class="js-select2-custom form-control" required>
                                    <option value="">{{ ui_change('select_tenant') }}</option>
                                    @foreach ($tenants as $tenants_item)
                                        <option value="{{ $tenants_item->id }}">
                                            {{ $tenants_item->name ?? $tenants_item->company_name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('rental_type', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <select name="rental_type_id" class="js-select2-custom form-control" required>
                                        @foreach ($rental_types as $rental_types_item)
                                            <option value="{{ $rental_types_item->id }}">
                                                {{ $rental_types_item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('check_in_date', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled name="check_in_date" class="form-control date"
                                        value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                                </div>
                            </div>


                        </div>

                        {{-- Dates --}}
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('Booking_Date', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" disabled name="booking_date" class="form-control date"
                                        value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('Booking_from', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" name="booking_from" class="form-control date"
                                        value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('Booking_to', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" name="booking_to" class="form-control date_to"
                                        value="{{ \Carbon\Carbon::now()->addDays(1)->format('d/m/Y') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-sm-4 col-form-label">{{ ui_change('Check-In_Time', 'room_reservation') }}</label>
                                <div class="col-sm-8">
                                    <input type="time" name="checkin_time" class="form-control w-auto"
                                        value="{{ now()->format('H:i') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-unstyled">
                                <li>
                                    <strong>{{ ui_change('Adults', 'room_reservation') }}:</strong>

                                    <div class="qty-control">
                                        <button type="button" class="qty-btn minus" data-target="adults">−</button>

                                        <input type="number" name="adults" id="adults" class="qty-input"
                                            value="{{ $adults }}" min="1" readonly>

                                        <button type="button" class="qty-btn plus" data-target="adults">+</button>
                                    </div>
                                </li>

                                <li class="mt-1">
                                    <strong>{{ ui_change('Children', 'room_reservation') }}:</strong>

                                    <div class="qty-control">
                                        <button type="button" class="qty-btn minus" data-target="children">−</button>

                                        <input type="number" name="children" id="children" class="qty-input"
                                            value="{{ $children }}" min="0" readonly>

                                        <button type="button" class="qty-btn plus" data-target="children">+</button>
                                    </div>
                                </li>
                            </ul>
                        </div>



                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <h5 class="mb-3">Guests</h5>

                    <table class="table table-bordered align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Room</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Age</th>
                                <th>ID Type</th>
                                <th>ID</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="table-secondary">
                                <td colspan="8"><strong>Adults</strong></td>
                            </tr>
                        <tbody id="adults-tbody"></tbody>

                        <tr class="table-secondary">
                            <td colspan="8"><strong>Children</strong></td>
                        </tr>
                        <tbody id="children-tbody"></tbody>
                        </tbody>

                    </table>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="tio-checkmark-circle"></i> Check-In
                        </button>


                    </div>

                </div>
            </div>


        </form>

    </div>
@endsection


@push('script')
    <script>
     $(document).ready(function() {
    $('.js-select2-custom').select2({
        placeholder: "{{ ui_change('select_tenant') }}",
        allowClear: true,
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;

            var term = params.term.toLowerCase();
 
            var text = data.text ? data.text.toLowerCase() : '';
 
            var mobile = data.element ? $(data.element).data('mobile') : '';
            var passport = data.element ? $(data.element).data('passport') : '';

            mobile = mobile ? mobile.toString() : '';
            passport = passport ? passport.toString() : '';

            if (text.indexOf(term) > -1 || mobile.indexOf(term) > -1 || passport.indexOf(term) > -1) {
                return data;
            }

            return null;
        }
    });
});

    </script>
    <script>
        $(document).ready(function() {
            function updateGuests(type) {
                let qty = parseInt($('#' + type).val());
                let tbody = $('#' + type + '-tbody');

                tbody.empty();

                for (let i = 0; i < qty; i++) {
                    let row = `
            <tr>
                <td>${i + 1}</td>
                <td><input type="text" name="guests[${type}][${i}][name]" class="form-control"></td>
                <td>
                    <select name="guests[${type}][${i}][room_id]" class="form-control">
                        @foreach ($unit_managements as $room)
                            <option value="{{ $room->room_id }}">{{ $room->unit_management_main?->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="guests[${type}][${i}][gender]" class="form-control">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </td>
                <td><input type="text" name="guests[${type}][${i}][dob]" class="form-control dob-input date"></td>
                <td><input type="text" name="guests[${type}][${i}][age]" class="form-control age-input"></td>
                <td>
                    <select name="guests[${type}][${i}][id_type]" class="form-control">
                        <option value="">Select</option>
                        <option value="passport">Passport</option>
                        <option value="id_card">ID Card</option>
                        <option value="driving_license">Driving License</option>
                    </select>
                </td>
                <td><input type="file" name="guests[${type}][${i}][id_file]" class="form-control"></td>
            </tr>
        `;
                    tbody.append(row);
                }
                flatpickr(".date", {
                    dateFormat: "d/m/Y",
                });
            }

            $('.qty-btn').click(function() {
                let target = $(this).data('target');
                let input = $('#' + target);
                let value = parseInt(input.val());

                if ($(this).hasClass('plus')) {
                    input.val(value + 1);
                } else {
                    if (value > parseInt(input.attr('min'))) {
                        input.val(value - 1);
                    }
                }

                updateGuests(target);
            });

            updateGuests('adults');
            updateGuests('children');
        });
    </script>
    <script>
        flatpickr(".date", {
            dateFormat: "d/m/Y",
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const fromInput = document.querySelector('input[name="booking_from"]');
            const toInput = document.querySelector('input[name="booking_to"]');

            const fromPicker = flatpickr(fromInput, {
                dateFormat: "d/m/Y",
                defaultDate: "today",
                minDate: "today",
                onChange: function(selectedDates) {

                    if (!selectedDates.length) return;

                    const fromDate = selectedDates[0];

                    const minToDate = new Date(fromDate);
                    minToDate.setDate(minToDate.getDate() + 1);

                    toPicker.set('minDate', minToDate);

                    if (!toPicker.selectedDates.length || toPicker.selectedDates[0] <= fromDate) {
                        toPicker.setDate(minToDate, true);
                    }

                    calculateDays();
                }
            });

            const toPicker = flatpickr(toInput, {
                dateFormat: "d/m/Y",
                minDate: new Date().fp_incr(1),
                onChange: function() {
                    calculateDays();
                }
            });

            function parseDate(dateStr) {
                const parts = dateStr.split('/');
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }

            function calculateDays() {
                const fromDate = parseDate(fromInput.value);
                const toDate = parseDate(toInput.value);

                if (!fromDate || !toDate) return;

                let diffTime = toDate - fromDate;
                let days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                if (days < 1) days = 1;

                document.querySelectorAll('.days-input').forEach(input => {
                    input.value = days;
                    input.dispatchEvent(new Event('input'));
                });

                calculateTotals();
            }

        });
    </script>
@endpush
