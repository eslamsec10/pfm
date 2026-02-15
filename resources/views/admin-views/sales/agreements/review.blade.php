@extends('layouts.back-end.app')

@section('title', ui_change('review_Rent' , 'property_transaction') )
<?php 
if (! function_exists('get_last_rent_inschedule')) {
   function get_last_rent_inschedule($id){
    $rent_amount =  App\Models\Schedule::where('unit_id' , $id)->where('service','no')->orderBy('created_at','desc')->first()?->rent_amount;

    return $rent_amount;
}
}
?>
@php
    $lang = Session::get('locale');
    $company = (new App\Models\Company())->setConnection('tenant')->select('decimals')->first();

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
                {{ ui_change('review_Rent' , 'property_transaction') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_transaction.inline-menu')

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('agreement.update_review') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('patch')
            <!-- general setup -->

            @foreach ($agreement_units as $agreement_unit)
                <div class="card mt-3 rest-part">
                    <div class="card-header">
                        <div class="d-flex gap-2">
                            <h4 class="mb-0">{{ ui_change('general_info' , 'property_transaction') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('units' , 'property_transaction')  }}</label>
                                    <input readonly type="text" name="units[]" class="form-control"
                                        value="{{ $agreement_unit->agreement_unit_main->property_unit_management->name .
                                            '-' .
                                            $agreement_unit->agreement_unit_main->block_unit_management->block->name .
                                            '-' .
                                            $agreement_unit->agreement_unit_main->floor_unit_management->floor_management_main->name .
                                            '-' .
                                            $agreement_unit->agreement_unit_main->unit_management_main->name }}">
                                </div>
                            </div>
                            <input type="hidden" value="{{ $agreement_unit->unit_id }}" name="unit_id[]">
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('applicable_Date' , 'property_transaction') }}</label>
                                    <input type="text" class="form-control" id="agreement_date" name="applicable_date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-4 col-xl-6">
                                <div class="form-group">
                                    <label for="">{{ ui_change('rent_Amount' , 'property_transaction') }}</label>
                                    <input type="number" class="form-control"
                                        name="rent_amount-{{ $agreement_unit->unit_id }}" placeholder="e.g. :  500.000"
                                        value="{{ number_format(get_last_rent_inschedule($agreement_unit->unit_id), $company->decimals) }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach


            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset' , 'property_transaction') }}</button>
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit' , 'property_transaction') }}</button>
            </div>
        </form>



    </div>
@endsection

@push('script')
    <script>
        flatpickr("#agreement_date", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            // minDate: "today"
        });
        flatpickr("#main_data", {
            dateFormat: "d/m/Y",
        });
        flatpickr(".main_date", {
            dateFormat: "d/m/Y",
        });

        flatpickr(".period_from_date", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ isset($agreement->agreement_details->period_from) ? \Carbon\Carbon::createFromFormat('Y-m-d', $agreement->agreement_details->period_from)->format('d/m/Y') : '' }}",
            // minDate: "today"
        });
        flatpickr(".period_to_date", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ isset($agreement->agreement_details->period_to) ? \Carbon\Carbon::createFromFormat('Y-m-d', $agreement->agreement_details->period_to)->format('d/m/Y') : '' }}",
            // minDate: "today"
        });
    </script>
@endpush
