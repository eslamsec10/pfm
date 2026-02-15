@extends('layouts.back-end.app')

@section('title', ui_change('add_new'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('add_new') }}
            </h2>
        </div>
        @include('admin-views.inline_menu.property_config.inline-menu')
      


        <form class="product-form text-start" action="{{ route('daily_price.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img src="{{ asset(main_path() . 'back-end/img/shop-information.png') }}" class="mb-1"
                        alt=""> --}}
                        <h4 class="mb-0">{{ ui_change('create_daily_price') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('property') }}<span
                                        class="text-danger"> *</span>
                                </label>
                                <select id="propertySelect" class="js-select2-custom form-control" name="property" required>
                                    <option selected disabled>{{ ui_change('select') }} </option>
                                    <option value="-1">{{ ui_change('all') }} </option>
                                    @foreach ($property_managements as $property_item)
                                        <option value="{{ $property_item->id }}">
                                            {{ $property_item->name . ' - ' . $property_item->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('blocks') }}<span
                                        class="text-danger"> *</span>
                                </label>
                                <select id="blockSelect" class="js-select2-custom form-control" name="block"  
                                    disabled>
                                    <option selected>{{ ui_change('select') }}
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="form-group">
                                <label class="floor-title">{{ ui_change('floors') }}<span class="text-danger">
                                        *</span></label>
                                <select id="floorSelect" class="js-select2-custom form-control" name="floor"  
                                    disabled>
                                    <option selected>{{ ui_change('select') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3  ">
                            <div class="form-group">
                                <label class="floor-title">{{ ui_change('daily_price') }}<span class="text-danger">
                                        *</span></label>
                                <input type="number" class="form-control" id="mainRentAmount" name="daily_price" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3   ">
                            <div class="form-group">
                                <label class="floor-title">{{ ui_change('applicable_from') }}<span class="text-danger">
                                        *</span></label>
                                <input type="text" class="form-control applicable_date" name="applicable_date" required>
                            </div>
                        </div>


                        <div class="col-12 mt-3">
                            <table class="table table-bordered" id="unitsTable">
                                <thead>
                                    <tr>
                                        <th>{{ ui_change('unit') }}</th>
                                        <th>{{ ui_change('daily_price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>


            </div>

            <div class="row justify-content-end gap-3 mt-3 mx-1"> 
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit') }}</button>
            </div>
        </form>




    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#mainRentAmount').on('input', function() {
                var value = $(this).val();
                $('#unitsTable tbody input[name^="daily_price"]').val(value);
            });

            function loadUnits(propertyId, blockId = null, floorId = null) {
                $.ajax({
                    url: '{{ route('daily_price.get_units_filtered') }}',
                    type: 'GET',
                    data: {
                        property_id: propertyId,
                        block_id: blockId,
                        floor_id: floorId
                    },
                    dataType: 'json',
                   success: function(data) {
    var tbody = $('#unitsTable tbody');
    tbody.empty();

    $.each(data.units, function(index, unit) {

        const unitName = [
            unit.property_unit_management?.name,
            unit.block_unit_management?.block?.name,
            unit.floor_unit_management?.floor_management_main?.name,
            unit.unit_management_main?.name,
            unit.unit_description?.name,
            unit.unit_condition?.name,
            unit.unit_type?.name,
            unit.view?.name
        ].filter(Boolean).join(' - ');

        var row = `
            <tr>
                <input type="hidden" name="units[]" value="${unit.id}">
                <td>${unitName}</td>
                <td>
                    <input type="number"
                           name="daily_price[${unit.id}]"
                           class="form-control"
                           placeholder="Enter daily price"
                           value="${$('#mainRentAmount').val()}">
                </td>
            </tr>
        `;

        tbody.append(row);
    });
}

                });
            }

            $('#propertySelect').change(function() {
                var propertyId = $(this).val();

                $.ajax({
                    url: '/daily_price_list/get-blocks/' + propertyId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var blockSelect = $('#blockSelect');
                        blockSelect.prop('disabled', false).empty().append(
                            '<option selected disabled>Select Block</option>');
                        $.each(data.blocks, function(index, block) {
                            blockSelect.append(
                                `<option value="${block.id}">${block.name}</option>`
                            );
                        });
                    }
                });

                $.ajax({
                    url: '/daily_price_list/get-floors/' + propertyId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var floorSelect = $('#floorSelect');
                        floorSelect.prop('disabled', false).empty().append(
                            '<option selected disabled>Select Floor</option>');
                        $.each(data.floors, function(index, floor) {
                            floorSelect.append(
                                `<option value="${floor.id}">${floor.name}</option>`
                            );
                        });
                    }
                });
                loadUnits(propertyId);
            });
            $('#blockSelect, #floorSelect').change(function() {
                var propertyId = $('#propertySelect').val();
                var blockId = $('#blockSelect').val();
                var floorId = $('#floorSelect').val();

                loadUnits(propertyId, blockId, floorId);
            });
        });
    </script>
   

    <script>
        flatpickr(".applicable_date", {
            dateFormat: "d/m/Y",
            minDate: "today",
            defaultDate: "today",
        });
        $(document).ready(function() {
            $('select[name="property"]').on('change', function() {
                var property = $(this).val();
                if (property) {
                    $.ajax({
                        url: "{{ route('daily_price.get_blocks_by_property_id_for_daily', ':id') }}"
                            .replace(':id', property),
                      
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="block"]').removeAttr('disabled');
                                $('select[name="block"]').empty();
                                $('select[name="floor"]').empty();
                                $('select[name="units[]"]').empty();
                                $('select[name="block"]').append(
                                    '<option value="">{{ ui_change('select') }}</option>'
                                );
                                $('select[name="floor"]').append(
                                    '<option value="">{{ ui_change('select') }}</option>'
                                );
                                $('select[name="units[]"]').append(
                                    '<option value="">{{ ui_change('select') }}</option>'
                                );
                                $.each(data, function(key, value) {
                                    $('select[name="block"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .block.name + ' - ' + value.block.code +
                                        '</option>'
                                    )
                                })

                            } else {

                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                        }
                    });
                }

            });
            $('select[name="block"]').on('change', function() {
                var block = $(this).val();
                if (block) {
                    $.ajax({
                        url: "{{ route('daily_price.get_floors_by_block_id_for_daily', ':id') }}"
                            .replace(':id', block), 
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="floor"]').removeAttr('disabled');
                                $('select[name="floor"]').empty();
                                $('select[name="units[]"]').empty();
                                $('select[name="floor"]').append(
                                    '<option value="">{{ ui_change('select') }}</option>'
                                );
                                $('select[name="units[]"]').append(
                                    '<option value="">{{ ui_change('select') }}</option>'
                                );
                                $.each(data, function(key, value) {
                                    $('select[name="floor"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .floor_management_main.name + ' - ' + value
                                        .floor_management_main.code + '</option>'
                                    )
                                })

                            } else {}
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                        }
                    });
                }

            });
            $('select[name="floor"]').on('change', function() {
                var floor = $(this).val();
                if (floor) {
                    $.ajax({
                        url: "{{ route('daily_price.get_units_by_floor_id_for_daily', ':id') }}"
                            .replace(':id', floor),
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="units[]"]').removeAttr('disabled');
                                $('select[name="units[]"]').empty();
                                $.each(data, function(key, value) {
                                    $('select[name="units[]"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .unit_management_main.name + ' - ' + value
                                        .unit_management_main.code + '</option>'
                                    )
                                })

                            } else {}
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                        }
                    });
                }

            });



        });
    </script>
@endpush
