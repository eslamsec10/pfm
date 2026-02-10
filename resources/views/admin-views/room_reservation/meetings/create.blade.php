@extends('layouts.back-end.app')

@section('title', ui_change('create_unit_management', 'property_config'))
@php
    $lang = Session::get('locale');
@endphp
@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <style>
        .unit-type-container {
            width: 80%;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        select {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }

        .unit-type-rows {
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .unit-type-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            align-items: center;
        }

        .unit-type-row>div {
            flex: 1;
        }


        .unit-description-container {
            width: 80%;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .unit-description-rows {
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .unit-description-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            align-items: center;
        }

        .unit-description-row>div {
            flex: 1;
        }

        .unit-condition-container {
            width: 80%;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .unit-condition-rows {
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .unit-condition-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            align-items: center;
        }

        .unit-condition-row>div {
            flex: 1;
        }

        .unit-parking-container {
            width: 80%;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .unit-parking-rows {
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .unit-parking-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            align-items: center;
        }

        .unit-parking-row>div {
            flex: 1;
        }

        .view-container {
            width: 80%;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .view-rows {
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        .view-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            align-items: center;
        }

        .view-row>div {
            flex: 1;
        }



        button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        button:hover {
            background-color: #218838;
        }

        .remove-row {
            background-color: #dc3545;
        }

        .remove-row:hover {
            background-color: #c82333;
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
        @include('admin-views.inline_menu.room_reservation.master.inline-menu')

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('meeting_room.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img src="{{ asset(main_path() . 'back-end/img/shop-information.png') }}" class="mb-1"
                            alt=""> --}}
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
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('meeting_room_name', 'room_reservation') }}</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('capacity', 'room_reservation') }}</label>
                                <input type="number" name="capacity" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('location', 'room_reservation') }}</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('rent_amount', 'room_reservation') }}</label>
                                <input type="number" step="0.001" name="rent_amount" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('description', 'room_reservation') }}</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div> 

                    </div>
                </div>
            </div>



            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit', 'property_config') }}</button>
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
                                $.each(data, function(key, value) {
                                    $('select[name="start_up_unit"]').append(
                                        '<option value="' + value.id +
                                        '">' + value.name + '</option>'
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



        });
    </script>
@endpush
