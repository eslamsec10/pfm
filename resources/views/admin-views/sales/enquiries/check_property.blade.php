@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
    $currentUrl = url()->current();
    $segments = explode('/', $currentUrl);
    $id = end($segments);
    $enquiry_id = end($segments);
@endphp

@section('title')
    {{ ui_change('check_property' , 'property_transaction') }}
@endsection
@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
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
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="inline-page-menu my-4">
            <ul class="list-unstyled">
                <li class="{{ Request::is('enquiry/check_property*') ?'active':'' }}"><a href="{{ route('enquiry.check_propoerty' , $id) }}">{{ ui_change('check_property' , 'property_transaction') }}</a></li>
                {{-- <li class="{{ Request::is('enquiry/general_check_property*') ?'active':'' }}"><a href="{{ route('enquiry.general_check_property') }}">{{ui_change('general_check_property' , 'property_transaction')}}</a></li> --}}
               
        
            </ul>
        </div>
        {{-- <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2"> 
                {{ ui_change('general.check_property' , 'property_transaction') }}
            </h2>
        </div> --}}
        {{-- <div class="row gx-2 gy-3" id="printableArea"> --}}
        <div class="col-lg-8 col-xl-12">
            <!-- Card -->
            @foreach ($properties as $property_item)
                <div class="card h-100 m-2">
                    <!-- Body -->
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('property_name' , 'property_transaction') }} :
                                    <strong>{{ $property_item->name ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('property_code' , 'property_transaction') }} :
                                    <strong>{{ $property_item->code ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('type_of_ownership' , 'property_transaction') }} :
                                    <strong>{{ $property_item->type_of_ownership ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('property_type' , 'property_transaction') }} :
                                    <strong>
                                        {{-- {{ dd($property_item->property_types) }} --}}
                                        @foreach ($property_item->property_types as $property_type_item)
                                            {{ $property_type_item->name  ?? ui_change('not_available' , 'property_transaction') }},
                                        @endforeach
                                    </strong></span>
                            </div>
                        </div>



                        <div class="row mt-2">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('building_no' , 'property_transaction') }} :
                                    <strong>{{ $property_item->building_no ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('block_no' , 'property_transaction') }} :
                                    <strong>{{ $property_item->block_no ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('road' , 'property_transaction') }} :
                                    <strong>{{ $property_item->road ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('location' , 'property_transaction') }} :
                                    <strong>
                                        {{ $property_item->location  ?? ui_change('not_available' , 'property_transaction') }},
                                    </strong>
                                </span>
                            </div>
                        </div>



                        <div class="row mt-2">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('city' , 'property_transaction') }} :
                                    <strong>{{ $property_item->city ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('established_on' , 'property_transaction') }} :
                                    <strong>{{ $property_item->established_on ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('registration_on' , 'property_transaction') }} :
                                    <strong>{{ $property_item->registration_on ?? ui_change('not_available' , 'property_transaction') }}</strong></span>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <span class="title-color break-all"> {{ ui_change('tax_no' , 'property_transaction') }} :
                                    <strong>
                                        {{ $property_item->tax_no  ?? ui_change('not_available' , 'property_transaction') }},
                                    </strong>
                                </span>
                            </div>
                        </div>



                        <!-- End Row -->
                    </div>
                    <!-- End Body -->

                <div class="row justify-content-end gap-3 mt-3 mb-2 mx-1">
                    <a href="{{ route('enquiry.image_view' , [$property_item->id , $enquiry_id]) }}"   class="btn btn-secondary px-5">{{ ui_change('view_image' , 'property_transaction') }}</a>
                    <a href="{{ route('enquiry.list_view' , [$property_item->id , $enquiry_id]) }}"  class="btn btn--primary px-5">{{ ui_change('list_view' , 'property_transaction') }}</a>
                </div>
            </div>
            <hr>
            @endforeach
            <!-- End Card -->
        </div>
        {{-- </div> --}}
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
@endpush
