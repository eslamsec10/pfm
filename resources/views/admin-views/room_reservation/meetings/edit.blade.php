@extends('layouts.back-end.app')

@section('title', ui_change('create_unit_management', 'property_config'))
@php
    $lang = Session::get('locale');
@endphp
@push('css_or_js')
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
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

        <form class="product-form text-start" action="{{ route('meeting_room.update', $room->id) }}" method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <h4 class="mb-0">{{ ui_change('edit_meeting_room', 'room_reservation') }}</h4>
                </div>

                <div class="card-body">
                    <div class="row">

                        {{-- PROPERTY --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('property', 'property_config') }}</label>
                                <select class="js-select2-custom form-control" name="property" required>
                                    <option disabled>{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($property as $property_item)
                                        <option value="{{ $property_item->id }}"
                                            {{ $room->property_management_id == $property_item->id ? 'selected' : '' }}>
                                            {{ $property_item->name }} - {{ $property_item->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- BLOCK --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('blocks', 'property_config') }}</label>
                                <select class="js-select2-custom form-control" name="block">
                                    @foreach ($blocks as $block_item)
                                        <option value="{{ $block_item->id }}"
                                            {{ $room->block_management_id == $block_item->id ? 'selected' : '' }}>
                                            {{ $block_item->block?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- FLOOR --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('floors', 'property_config') }}</label>
                                <select class="js-select2-custom form-control" name="floor">
                                    @foreach ($floors as $floor_item)
                                        <option value="{{ $floor_item->id }}"
                                            {{ $room->floor_management_id == $floor_item->id ? 'selected' : '' }}>
                                            {{ $floor_item->floor_management_main?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- NAME --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('meeting_room_name', 'room_reservation') }}</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $room->name) }}" required>
                            </div>
                        </div>

                        {{-- CAPACITY --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('capacity', 'room_reservation') }}</label>
                                <input type="number" name="capacity" class="form-control"
                                    value="{{ old('capacity', $room->capacity) }}" min="1" required>
                            </div>
                        </div>

                        {{-- LOCATION --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('location', 'room_reservation') }}</label>
                                <input type="text" name="location" class="form-control"
                                    value="{{ old('location', $room->location) }}">
                            </div>
                        </div>

                        {{-- RENT --}}
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('rent_amount', 'room_reservation') }}</label>
                                <input type="number" step="0.001" name="rent_amount" class="form-control"
                                    value="{{ old('rent_amount', $room->rent_amount) }}">
                            </div>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('description', 'room_reservation') }}</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $room->description) }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="submit" class="btn btn--primary px-5">
                    {{ ui_change('update', 'property_config') }}
                </button>
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
