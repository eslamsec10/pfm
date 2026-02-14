@extends('layouts.back-end.app')

@section('title', ui_change('edit_sale'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                {{ ui_change('edit_sale') }} 
            </h2>
        </div>
        @include('admin-views.inline_menu.property_config.inline-menu')


        <form class="product-form text-start" action="{{ route('sales_price.update' , $unit_sale->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('patch')
        <div class="card mt-3 rest-part">
            <div class="card-header">
                <div class="d-flex gap-2"> 
                    <h4 class="mb-0">{{ ui_change('edit_sale') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="name" class="title-color">{{ ui_change('property') }}<span class="text-danger"> *</span>
                            </label>
                            <select class="js-select2-custom form-control" name="property" required>
                                <option selected  value="">{{ ui_change('select') }}
                                </option>
                                @foreach ($property_managements as $property_item)
                                    <option value="{{ $property_item->id }}" {{ ($property_item->id == $unit_sale->property_id) ? 'selected' : '' }}>
                                        {{ $property_item->name . ' - '.$property_item->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="form-group">
                            <label for="name" class="title-color">{{ ui_change('blocks') }}<span class="text-danger"> *</span>
                            </label>
                            <select class="js-select2-custom form-control" name="block" required  >
                            <option  value="{{ $unit_sale->block_management_id }}">{{ $unit_sale->block_management?->block?->name }} </option>  
                            </select>
                        </div>
                    </div>
                 

                    <div class="col-md-6 col-lg-4 col-xl-3 ">
                        <div class="form-group">
                        <label class="floor-title">{{ ui_change('floors') }}<span class="text-danger"> *</span></label>
                            <select class="js-select2-custom form-control" name="floor" required  >
                                <option  value="{{ $unit_sale->floor_management_id }}">{{ $unit_sale->floor_management?->floor_management_main?->name }} </option>  

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3  ">
                        <div class="form-group">
                        <label class="floor-title">{{ ui_change('sale_price') }}<span class="text-danger"> *</span></label>
                        <input type="number" class="form-control" name="sale_price" value="{{ $unit_sale->price }}" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3  ">
                        <div class="form-group">
                        <label class="floor-title">{{ ui_change('units') }}<span class="text-danger"> *</span></label>
                            <select class="js-select2-custom form-control  " name="units" required  >
                                <option  value="{{ $unit_sale->unit_management_id }}">{{ $unit_sale->unit_management?->unit_management_main?->name }} </option>  

                            </select>
                        </div>
                    </div>
                   
                    <div class="col-md-6 col-lg-4 col-xl-3   ">
                        <div class="form-group">
                        <label class="floor-title">{{ ui_change('applicable_from') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control applicable_date" value="{{ \Carbon\Carbon::parse($unit_sale->applicable_date)->format('d/m/Y') }}" name="applicable_date" required>
                        </div>
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
        flatpickr(".applicable_date", {
            dateFormat: "d/m/Y",  
        });
        $(document).ready(function() {
            $('select[name="property"]').on('change', function() {
                var property = $(this).val();
                if (property) {
                    $.ajax({
                        url: "{{ route('sales_price.get_blocks_by_property_id_for_sales' , ':id') }}".replace(':id', property) , 
                        // url: "{{ URL::to('floor_management/get_blocks_by_property_id') }}/" + property, 
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="block"]').removeAttr('disabled'); 
                                $('select[name="block"]').empty();
                                $('select[name="floor"]').empty();  
                                $('select[name="units"]').empty();  
                                $('select[name="block"]').append(
                                        '<option value="">{{ ui_change('select') }}</option>'
                                    );
                                $('select[name="floor"]').append(
                                        '<option value="">{{ ui_change('select') }}</option>'
                                    );
                                $('select[name="units"]').append(
                                        '<option value="">{{ ui_change('select') }}</option>'
                                    );
                                $.each(data, function(key, value) {
                                    $('select[name="block"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .block.name +' - ' + value.block.code+ '</option>'
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
                        url: "{{ route('sales_price.get_floors_by_block_id_for_sales' , ':id') }}".replace(':id', block) , 
                        // url: "{{ URL::to('floor_management/get_blocks_by_block_id') }}/" + block, 
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="floor"]').removeAttr('disabled'); 
                                $('select[name="floor"]').empty();
                                $('select[name="units"]').empty();  
                                $('select[name="floor"]').append(
                                        '<option value="">{{ ui_change('select') }}</option>'
                                    );
                                $('select[name="units"]').append(
                                        '<option value="">{{ ui_change('select') }}</option>'
                                    );
                                $.each(data, function(key, value) {
                                    $('select[name="floor"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .floor_management_main.name +' - ' + value.floor_management_main.code+ '</option>'
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
            $('select[name="floor"]').on('change', function() {
                var floor = $(this).val();
                if (floor) {
                    $.ajax({
                        url: "{{ route('sales_price.get_units_by_floor_id_for_sales' , ':id') }}".replace(':id', floor) ,   
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                $('select[name="units"]').removeAttr('disabled'); 
                                $('select[name="units"]').empty();
                                $.each(data, function(key, value) {
                                    $('select[name="units"]').append(
                                        '<option value="' + value.id + '">' + value
                                        .unit_management_main.name +' - ' + value.unit_management_main.code+ '</option>'
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

          

        });
    </script>
@endpush
