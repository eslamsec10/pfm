@extends('layouts.back-end.app')

@section('title', ui_change('create_unit_management', 'property_config'))
@php
    $lang = Session::get('locale');
@endphp
@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dedede;
            border: 1px solid #dedede;
            border-radius: 2px;
            color: #222;
            display: flex;
            gap: 4px;
            align-items: center;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .unit-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .unit-item {
            display: flex;
            align-items: center;
            background-color: #f5f5dc;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .unit-item input[type="checkbox"] {
            margin-right: 10px;
        }

        .unit-item:hover {
            background-color: #e0e0d1;
        }

        .unit-title {
            font-size: 18px;
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{-- <img src="{{ asset(main_path() . 'back-end/img/inhouse-product-list.png') }}" alt=""> --}}
                {{ ui_change('create_unit_management', 'property_config') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_config.inline-menu')

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('unit_management.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('create_unit_management', 'property_config') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('property', 'property_config') }}
                                </label>
                                <select class="js-select2-custom form-control" name="property" id="property" required>
                                    <option selected disabled>{{ ui_change('select', 'property_config') }}
                                    </option>
                                    @foreach ($property as $property_item)
                                        <option value="{{ $property_item->id }}">
                                            {{ $property_item->name . ' - ' . $property_item->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('blocks', 'property_config') }}
                                </label>
                                <select class="js-select2-custom form-control" name="block" id="block" required
                                    disabled>
                                    <option selected>{{ ui_change('select', 'property_config') }}
                                    </option>
                                    @foreach ($blocks as $block_item)
                                        <option value="{{ $block_item->id }}">
                                            {{ $block_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('floors', 'property_config') }}
                                </label>
                                <select class="js-select2-custom form-control" name="floor" id="floor" required
                                    disabled>
                                    <option selected>{{ ui_change('select', 'property_config') }}
                                    </option>
                                    @foreach ($floors as $floor_item)
                                        <option value="{{ $floor_item->id }}">
                                            {{ $floor_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('description', 'property_config') }}</label>
                                <select id="general_unit_description" class="form-control">
                                    {!! '<option value="">' . ui_change('not_applicable', 'property_config') . '</option>' !!}
                                    @foreach ($unit_descriptions as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('type', 'property_config') }}</label>
                                <select id="general_unit_type" class="form-control">
                                    {!! '<option value="">' . ui_change('not_applicable', 'property_config') . '</option>' !!}
                                    @foreach ($unit_types as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('condition', 'property_config') }}</label>
                                <select id="general_unit_condition" class="form-control">
                                    {!! '<option value="">' . ui_change('not_applicable', 'property_config') . '</option>' !!}
                                    @foreach ($unit_conditions as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('parking', 'property_config') }}</label>
                                <select id="general_unit_parking" class="form-control">
                                    {!! '<option value="">' . ui_change('not_applicable', 'property_config') . '</option>' !!}
                                    @foreach ($unit_parkings as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('view', 'property_config') }}</label>
                                <select id="general_unit_view" class="form-control">
                                    {!! '<option value="">' . ui_change('not_applicable', 'property_config') . '</option>' !!}
                                    @foreach ($views as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <label>{{ ui_change('facilities', 'property_config') }}</label>
                            <select id="general_unit_facilities" class="js-select2-custom form-control" multiple>
                                @foreach ($facilities as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('adults', 'property_config') }}</label>
                                <input type="number" id="general_adults" class="form-control" value="1"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-3  col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Children', 'property_config') }}</label>
                                <input type="number" id="general_children" class="form-control" value="0"
                                    min="0">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>Area{{ ui_change('', 'property_transaction') }}</label>
                                <input type="number" step="0.01" name="area" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>Area Unit{{ ui_change('', 'property_transaction') }}</label>
                                <select name="area_unit" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Select_Unit_Type', 'property_transaction') }}
                                    </option>
                                    <option value="1">{{ ui_change('Sq. Mtr.', 'property_transaction') }}</option>
                                    <option value="2">{{ ui_change('Sq. Ft.', 'property_transaction') }}</option>
                                </select>
                            </div>
                        </div>
                        {{-- Area Inside --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>Area Inside{{ ui_change('', 'property_transaction') }}</label>
                                <input type="number" step="0.01" name="area_inside" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Area Inside Unit', 'property_transaction') }}</label>
                                <select name="area_inside_unit" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Select_Unit_Type', 'property_transaction') }}
                                    </option>
                                    <option value="1">{{ ui_change('Sq. Mtr.', 'property_transaction') }}</option>
                                    <option value="2">{{ ui_change('Sq. Ft.', 'property_transaction') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Area Terrace --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Area Terrace', 'property_transaction') }}</label>
                                <input type="number" step="0.01" name="area_terrace" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Area Terrace Unit', 'property_transaction') }}</label>
                                <select name="area_terrace_unit" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Select_Unit_Type', 'property_transaction') }}
                                    </option>
                                    <option value="1">{{ ui_change('Sq. Mtr.', 'property_transaction') }}</option>
                                    <option value="2">{{ ui_change('Sq. Ft.', 'property_transaction') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Rate --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Rate', 'property_transaction') }}</label>
                                <input type="number" step="0.01" name="rate" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Rate Unit', 'property_transaction') }}</label>
                                <select name="rate_unit" class="js-select2-custom form-control">
                                    <option value="">{{ ui_change('Select_Unit_Type', 'property_transaction') }}
                                    </option>
                                    <option value="1">{{ ui_change('Sq. Mtr.', 'property_transaction') }}</option>
                                    <option value="2">{{ ui_change('Sq. Ft.', 'property_transaction') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Security Deposit --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Security Deposit Amount', 'property_transaction') }}</label>
                                <input type="number" step="0.01" name="security_deposit_amount"
                                    class="form-control">
                            </div>
                        </div>

                        {{-- Municipality Nos --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Municipality Nos', 'property_transaction') }}</label>
                                <input type="text" name="municipality_nos" class="form-control">
                            </div>
                        </div>

                        {{-- Installation Date --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Installation Date', 'property_transaction') }}</label>
                                <input type="date" name="installation_date" class="form-control">
                            </div>
                        </div>

                        {{-- Electricity Meter No --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Electricity Meter No', 'property_transaction') }}</label>
                                <input type="text" name="electricity_meter_no" class="form-control">
                            </div>
                        </div>

                        {{-- Installation Date 2 --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Installation Date 2', 'property_transaction') }}</label>
                                <input type="date" name="installation_date_1" class="form-control">
                            </div>
                        </div>

                        {{-- Water Meter No --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Water Meter No', 'property_transaction') }}</label>
                                <input type="text" name="water_meter_no" class="form-control">
                            </div>
                        </div>

                        {{-- Electricity AC No --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Electricity A/C No', 'property_transaction') }}</label>
                                <input type="text" name="electricity_ac_no" class="form-control">
                            </div>
                        </div>

                        {{-- Rent Applicable Date --}}
                        <div class="col-md-3 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label>{{ ui_change('Rent Applicable Date', 'property_transaction') }}</label>
                                <input type="date" name="rent_applicable_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12 col-xl-12 units mt-3">
                        <div class="unit-container" id="unit-container">

                        </div>
                    </div>
                </div>
            </div>


            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('units_info', 'property_config') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered" id="unitsTable">
                                <thead>
                                    <tr>
                                        <th>{{ ui_change('unit', 'property_config') }}</th>
                                        <th>{{ ui_change('Adults_&_childrens', 'property_config') }}</th>
                                        <th>{{ ui_change('description', 'property_config') }}</th>
                                        <th>{{ ui_change('type', 'property_config') }}</th>
                                        <th>{{ ui_change('condition', 'property_config') }}</th>
                                        <th>{{ ui_change('parking', 'property_config') }}</th>
                                        <th>{{ ui_change('view', 'property_config') }}</th>
                                        <th>{{ ui_change('facilities', 'property_config') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset"
                    class="btn btn-secondary px-5">{{ ui_change('reset', 'property_config') }}</button>
                <button type="submit"
                    class="btn btn--primary px-5">{{ ui_change('submit', 'property_config') }}</button>
            </div>
        </form>



    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('select[name="property"]').on('change', function() {
                var property = $(this).val();
                if (property) {
                    $.ajax({
                        url: "{{ URL::to('unit_management/get_blocks_by_property_id') }}/" +
                            property,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="block"]').removeAttr('disabled');

                                // $('select[name="block"]').empty();
                                $('select[name="block"]').empty().append(
                                    '<option value=""  selected>{{ ui_change('select', 'property_config') }}</option>'
                                );
                                $.each(data, function(key, value) {
                                    $('select[name="block"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .block.name + ' - ' + value.block.code +
                                        '</option>'
                                    )
                                })

                            } else {
                                // $('input[name="token"]').removeAttr('disabled')
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                            // $('input[name="token"]').removeAttr('disabled')
                            //
                        }
                    });
                }

            });
            $('select[name="block"]').on('change', function() {
                var block = $(this).val();
                if (block) {
                    $.ajax({
                        url: "{{ URL::to('unit_management/get_floors_by_block_id') }}/" + block,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="floor"]').removeAttr('disabled');

                                $('select[name="floor"]').empty().append(
                                    '<option value=""  selected>{{ ui_change('select', 'property_config') }}</option>'
                                );
                                $.each(data, function(key, value) {
                                    $('select[name="floor"]').append(
                                        '<option value="' + value.id +
                                        '">' + value
                                        .floor_management_main.name + ' - ' + value
                                        .floor_management_main.code + '</option>'
                                    )
                                })

                            } else {
                                // $('input[name="token"]').removeAttr('disabled')
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                            // $('input[name="token"]').removeAttr('disabled')
                            //
                        }
                    });
                }

            });

            $('select[name="floor"]').on('change', function() {
                var floor = $(this).val();
                var block = $('select[name="block"]').val();
                var property = $('select[name="property"]').val();
                if (floor) {
                    $.ajax({
                        url: "{{ URL::to('unit_management/get_units_by_floor_id') }}/" + floor +
                            "/" + block + "/" + property,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="start_up_unit"]').removeAttr('disabled');

                                $('select[name="start_up_unit"]').empty().append(
                                    '<option value=""  selected>{{ ui_change('select', 'property_config') }}</option>'
                                );
                                var html_cont = '';

                                $.each(data, function(key, value) {
                                    html_cont += `
        <label class="unit-item d-block">
            <input type="checkbox"
                   class="unit-checkbox"
                   data-name="${value.name}"
                   value="${value.id}">
            ${value.name}
        </label>
    `;
                                });

                                $('#unit-container').html(html_cont);



                            } else {
                                // $('input[name="token"]').removeAttr('disabled')
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);

                        }
                    });
                }

            });




        });

        const unitTypes = @json($unit_types);
        const unitConditions = @json($unit_conditions);
        const unitDescriptions = @json($unit_descriptions);
        const unitParkings = @json($unit_parkings);
        const viewsData = @json($views);
        const facilitiesData = @json($facilities);

        function buildOptions(data) {
            let options = `<option value="">{{ ui_change('not_applicable', 'property_config') }}</option>`;
            data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            return options;
        }
    </script>

    <script>
        $(document).on('change', '.unit-checkbox', function() {

            let unitId = $(this).val();
            let unitName = $(this).data('name');

            if ($(this).is(':checked')) {
                let generalAdults = $('#general_adults').val() || 1;
                let generalChildren = $('#general_children').val() || 0;
                let generalDescription = $('#general_unit_description').val();
                let generalType = $('#general_unit_type').val();
                let generalCondition = $('#general_unit_condition').val();
                let generalParking = $('#general_unit_parking').val();
                let generalView = $('#general_unit_view').val() ?? '';
                let generalFacilities = $('#general_unit_facilities').val() || [];
                let rowHtml = `
        <tr id="unit-row-${unitId}">
            <td>
                ${unitName}
                <input type="hidden" name="units[]" value="${unitId}">
            </td>

                       <td>
                <div class="d-flex align-items-center mb-2">
                    <label class="me-2" style="min-width:70px;">{{ ui_change('adults', 'property_config') }}</label>
                    <button type="button" class="btn btn-sm btn-light qty-btn" data-action="minus">−</button>
                    <input type="number" name="adults[${unitId}]" class="form-control text-center mx-1 qty-input" value="${generalAdults}" min="0" style="width:70px;">
                    <button type="button" class="btn btn-sm btn-light qty-btn" data-action="plus">+</button>
                </div>

                <div class="d-flex align-items-center">
                    <label class="me-2" style="min-width:70px;">{{ ui_change('children', 'property_config') }}</label>
                    <button type="button" class="btn btn-sm btn-light qty-btn" data-action="minus">−</button>
                    <input type="number" name="children[${unitId}]" class="form-control text-center mx-1 qty-input" value="${generalChildren}" min="0" style="width:70px;">
                    <button type="button" class="btn btn-sm btn-light qty-btn" data-action="plus">+</button>
                </div>
            </td>

            <td>
                <select name="unit_description[${unitId}]" class="form-control">
                    ${buildOptions(unitDescriptions)}
                </select>
            </td>

            <td>
                <select name="unit_type[${unitId}]" class="form-control">
                    ${buildOptions(unitTypes)}
                </select>
            </td>

            <td>
                <select name="unit_condition[${unitId}]" class="form-control">
                    ${buildOptions(unitConditions)}
                </select>
            </td>

            <td>
                <select name="unit_parking[${unitId}]" class="form-control">
                    ${buildOptions(unitParkings)}
                </select>
            </td>

            <td>
                <select name="unit_view[${unitId}]" class="form-control">
                    ${buildOptions(viewsData)}
                </select>
            </td>
             <td>
        <select name="unit_facilities[${unitId}][]" class="js-select2-custom form-control" multiple>
            ${facilitiesData.map(f => `<option value="${f.id}" ${generalFacilities.includes(f.id.toString()) ? 'selected' : ''}>${f.name}</option>`).join('')}
        </select>
    </td>
        </tr>
        `;

                let $row = $(rowHtml);

                $row.find(`select[name="unit_description[${unitId}]"]`).val(generalDescription);
                $row.find(`select[name="unit_type[${unitId}]"]`).val(generalType);
                $row.find(`select[name="unit_condition[${unitId}]"]`).val(generalCondition);
                $row.find(`select[name="unit_parking[${unitId}]"]`).val(generalParking);
                $row.find(`select[name="unit_view[${unitId}]"]`).val(generalView);

                $('#unitsTable tbody').append($row);
                $row.find('.js-select2-custom').select2({
                    placeholder: '{{ ui_change('select_facilities', 'property_config') }}',
                    width: '100%'
                });
            } else {
                $('#unit-row-' + unitId).remove();
            }
        });



        $(document).on('click', '.qty-btn', function() {
            let action = $(this).data('action');
            let input = $(this).siblings('input.qty-input');
            let value = parseInt(input.val()) || 0;

            if (action === 'plus') {
                input.val(value + 1);
            } else if (action === 'minus' && value > 0) {
                input.val(value - 1);
            }
        });
    </script>
@endpush
