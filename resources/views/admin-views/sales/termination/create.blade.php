@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
@endphp
@section('title', ui_change('create_termination' , 'property_transaction') )

@push('css_or_js')
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/back-end/css/custom.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{ asset('/public/assets/back-end/img/business-setup.png') }}" alt="">
                {{ ui_change('create_termination' , 'property_transaction')  }}
            </h2>

        </div>




        <!-- Form -->
        <form class="product-form text-start" action="{{ route('termination.store') }}" method="POST"
            enctype="multipart/form-data" id="product_form">
            @csrf

            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('create_termination' , 'property_transaction') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="agreement_id"
                                    class="title-color">{{ ui_change('agreement_no' , 'property_transaction')  }}</label>
                                <input type="text" class="form-control" name="agreement_no" readonly
                                    value="{{ $agreement->agreement_no }}">
                                <input type="hidden" value="{{ $agreement->id }}" name="agreement_id">
                            </div>
                            @error('agreement_no')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="tenant_name"
                                    class="title-color">{{ ui_change('tenant' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="tenant_name" readonly
                                    value="{{ $agreement->tenant->type == 'individual' ? $agreement->tenant->name ?? ui_change('not_available' , 'property_transaction')  : $agreement->tenant->company_name ?? ui_change('not_available' , 'property_transaction')  }}">
                                <input type="hidden" value="{{ $agreement->tenant->id }}" name="tenant_id">
                            </div>
                            @error('tenant_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="applicant"
                                    class="title-color">{{ ui_change('applicant' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" name="applicant">
                            </div>
                            @error('applicant')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('termination_date' , 'property_transaction') }}</label>
                                <input type="text" class="form-control" id="termination_start_date"
                                    name="termination_date" class="form-control">
                            </div>
                            @error('termination_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="notes">{{ ui_change('notes_&_comments' , 'property_transaction') }}</label>
                                <textarea name="comment" class="form-control" rows="1"></textarea>

                            </div>
                            @error('termination_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-6">
                            <div class="form-group">
                                <label for="">{{ ui_change('units' , 'property_transaction') }}</label>
                                <select name="units[]" class="js-select2-custom form-control" multiple="multiple">
                                    <option value="-1">
                                        {{ ui_change('all' , 'property_transaction') }}
                                    </option>
                                    @foreach ($agreement_units as $agreement_units_item)
                                        <option value="{{ $agreement_units_item->id }}">
                                            {{ $agreement_units_item->agreement_units->property_unit_management->code .
                                                '-' .
                                                $agreement_units_item->agreement_units->block_unit_management->block->code .
                                                '-' .
                                                $agreement_units_item->agreement_units->floor_unit_management->floor_management_main->name .
                                                '-' .
                                                $agreement_units_item->agreement_units->unit_management_main->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


            </div>


            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset' , 'property_transaction')  }}</button>
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit' , 'property_transaction')  }}</button>
            </div>
        </form>
    </div>
@endsection
@push('script')
    <script>
        flatpickr("#termination_start_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            minDate: "today"
        });
    </script>
@endpush
