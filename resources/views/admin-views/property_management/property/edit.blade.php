@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
@endphp

@section('title')
    {{ ui_change('edit_property_management', 'property_config') }}
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
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                {{-- <img width="20px" src="{{ asset('/assets/back-end/img/property.jpg') }}" alt=""> --}}
                {{ ui_change('edit_property_management', 'property_config') }}
            </h2>
        </div>
        <!-- End Page Title -->
        @include('admin-views.inline_menu.property_config.inline-menu')

        <!-- Form -->
        <form class="product-form text-start" action="{{ route('property_management.update', $property_management->id) }}"
            method="POST" enctype="multipart/form-data" id="product_form">
            @csrf
            @method('patch')


            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        {{-- <img width="20px" src="{{ asset('/assets/back-end/img/property.jpg') }}" class="mb-1"
                            alt=""> --}}
                        <h4 class="mb-0">{{ ui_change('general_info', 'property_config') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('property_name', 'property_config') }}<span
                                        class="text-danger"> *</span>
                                </label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ $property_management->name }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="code"
                                    class="title-color">{{ ui_change('property_code', 'property_config') }}<span
                                        class="text-danger"> *</span>
                                </label>
                                <input type="text" class="form-control" name="code"
                                    value="{{ $property_management->code }}">
                            </div>
                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('ownership', 'property_config') }}<span
                                        class="text-danger"> *</span>

                                </label>
                                <select class="js-select2-custom form-control" name="ownership_id" required>
                                    <option value="">{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($owner_ship as $ownership)
                                        <option value="{{ $ownership->id }}"
                                            {{ $property_management->ownership_id == $ownership->id ? 'selected' : '' }}>
                                            {{ $ownership->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ownership_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                             <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ ui_change('tax_rate' , 'property_config') }}<span
                                        class="text-danger"> *</span>

                                </label>
                                <input class="form-control" type="number" value="{{  $property_management->tax_rate ?? $company->tax_rate }}" name="tax_rate" required/>
                                @error('tax_rate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('property_type', 'property_config') }}<span
                                        class="text-danger"> *</span>

                                </label>
                                <select class="js-select2-custom form-control" name="property_type_id[]" multiple required
                                    multiple="multiple">
                                    @foreach ($property_type as $propertyType)
                                        <option value="{{ $propertyType->id }}"
                                            {{ $property_management->property_types && $property_management->property_types->contains($propertyType->id) ? 'selected' : '' }}>
                                            {{ $propertyType->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('property_type_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('building_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="building_no"
                                    value="{{ $property_management->building_no }}">
                                @error('building_no')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('block_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="block_no"
                                    value="{{ $property_management->block_no }}">
                                @error('block_no')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('road', 'property_config') }}</label>
                                <input type="text" class="form-control" name="road"
                                    value="{{ $property_management->road }}">
                                @error('road')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('location', 'property_config') }}</label>
                                <input type="text" class="form-control" name="location"
                                    value="{{ $property_management->location }}">
                                @error('location')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('city', 'property_config') }}</label>
                                <input type="text" class="form-control" name="city"
                                    value="{{ $property_management->city }}">
                                @error('city')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('description', 'property_config') }}</label>
                                <textarea name="description" class="form-control">{{ $property_management->description }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('country', 'property_config') }}<span
                                        class="text-danger"> *</span>

                                </label>
                                <select class="js-select2-custom form-control" name="country_master_id" required>
                                    <option value="">{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($country_master as $country)
                                        <option value="{{ $country->id }}"
                                            {{ $property_management->country_master_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_master_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <input type="hidden" name="status" value="approved">
                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4"> 
                        {{ ui_change('personal_info', 'property_config') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('bank_name', 'property_config') }}</label>
                                <input type="text" class="form-control" name="bank_name"
                                    value="{{ $property_management->bank_name }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('bank_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="bank_no"
                                    value="{{ $property_management->bank_no }}">
                                @error('bank_no')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('contact_person', 'property_config') }}</label>
                                <input type="text" class="form-control" name="contact_person"
                                    value="{{ $property_management->contact_person }}">
                            </div>
                            @error('contact_person')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="email"
                                    class="title-color">{{ ui_change('email', 'property_config') }}</label>
                                <input type="text" class="form-control" name="email"
                                    value="{{ $property_management->email }}">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-1">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('dail_code', 'property_config') }}</label>
                                {{-- <input type="text" class="form-control" name="dail_code_telephone" value="{{ $property_management->dail_code_telephone }}"
                                    placeholder="+845"> --}}
                                <select class="js-select2-custom form-control" name="dail_code_telephone">
                                    <option>{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ $item_dail_code->dial_code == $property_management->dail_code_telephone ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('dail_code_telephone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('telephone', 'property_config') }}</label>
                                <input type="text" class="form-control" name="telephone"
                                    value="{{ $property_management->telephone }}">
                            </div>
                            @error('telephone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-1">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('dail_code', 'property_config') }}</label>
                                {{-- <input type="text" class="form-control" name="dail_code_mobile" value="{{ $property_management->dail_code_mobile }}" placeholder="+845"> --}}
                                <select class="js-select2-custom form-control" name="dail_code_mobile">
                                    <option>{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ $item_dail_code->dial_code == $property_management->dail_code_mobile ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('dail_code_mobile')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('mobile_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="mobile"
                                    value="{{ $property_management->mobile }}">
                            </div>
                            @error('mobile')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="col-md-6 col-lg-4 col-xl-1">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('dail_code', 'property_config') }}</label>
                                {{-- <input type="text" class="form-control" name="dail_code_fax" value="{{ $property_management->dail_code_fax }}" placeholder="+845"> --}}
                                <select class="js-select2-custom form-control" name="dail_code_fax">
                                    <option>{{ ui_change('select', 'property_config') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ $item_dail_code->dial_code == $property_management->dail_code_fax ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('dail_code_fax')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="fax"
                                    class="title-color">{{ ui_change('fax_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="fax"
                                    value="{{ $property_management->fax }}">
                            </div>
                            @error('fax')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="total_area"
                                    class="title-color">{{ ui_change('total_area', 'property_config') }}</label>
                                <input type="text" class="form-control" name="total_area"
                                    value="{{ $property_management->total_area }}">
                            </div>
                            @error('total_area')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_provider"
                                    class="title-color">{{ ui_change('insurance_provider', 'property_config') }}</label>
                                {{-- <input type="text" class="form-control" name="insurance_provider" value="{{ $property_management->insurance_provider }}"> --}}
                                <select class="js-select2-custom form-control" name="insurance_provider">
                                    <option>{{ ui_change('select', 'insurance_provider') }}</option>
                                    @foreach ($suppliers as $supplier_item)
                                        <option value="{{ $supplier_item->id }}"
                                            {{ $supplier_item->id == $property_management->insurance_provider ? 'selected' : '' }}>
                                            {{ $supplier_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('insurance_provider')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_period_from"
                                    class="title-color">{{ ui_change('insurance_period_from', 'property_config') }}</label>
                                <input type="text" class="form-control" name="insurance_period_from"
                                    id="insurance_period_from_edit">
                            </div>
                            @error('insurance_period_from')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_period_to"
                                    class="title-color">{{ ui_change('insurance_period_to', 'property_config') }}</label>
                                <input type="text" class="form-control" name="insurance_period_to"
                                    id="insurance_period_to_edit">
                            </div>
                            @error('insurance_period_to')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_type"
                                    class="title-color">{{ ui_change('insurance_type', 'property_config') }}</label>
                                <input type="text" class="form-control" name="insurance_type"
                                    value="{{ $property_management->insurance_type }}">
                            </div>
                            @error('insurance_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_policy_no"
                                    class="title-color">{{ ui_change('insurance_policy_no', 'property_config') }}</label>
                                <input type="text" class="form-control" name="insurance_policy_no"
                                    value="{{ $property_management->insurance_policy_no }}">
                            </div>
                            @error('insurance_policy_no')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="insurance_holder"
                                    class="title-color">{{ ui_change('insurance_holder', 'property_config') }}</label>
                                <input type="text" class="form-control" name="insurance_holder"
                                    value="{{ $property_management->insurance_holder }}">
                            </div>
                            @error('insurance_holder')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="premium_amount"
                                    class="title-color">{{ ui_change('premium_amount', 'property_config') }}</label>
                                <input type="text" class="form-control" name="premium_amount"
                                    value="{{ $property_management->premium_amount }}">
                            </div>
                            @error('premium_amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <label class="title-color" for="status">
                                {{ ui_change('status', 'property_config') }}
                            </label>
                            <div class="input-group">
                                <input type="radio" name="status" class="mr-3 ml-3"
                                    {{ $property_management->status == 'active' ? 'checked' : '' }} value="active">
                                <label class="title-color" for="status">
                                    {{ ui_change('active', 'property_config') }}
                                </label>
                                <input type="radio" name="status" class="mr-3 ml-3"
                                    {{ $property_management->status == 'inactive' ? 'checked' : '' }} value="inactive">
                                <label class="title-color" for="status">
                                    {{ ui_change('inactive', 'property_config') }}
                                </label>

                            </div>
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
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>

    <script>
        flatpickr("#insurance_period_from_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $property_management->insurance_period_from ? Carbon\Carbon::createFromFormat('Y-m-d', $property_management->insurance_period_from)->format('d/m/Y') : '' }}",
        });
        flatpickr("#insurance_period_to_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $property_management->insurance_period_to ? Carbon\Carbon::createFromFormat('Y-m-d', $property_management->insurance_period_to)->format('d/m/Y') : '' }}",
        });
        flatpickr("#registration_on_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $property_management->registration_on ? Carbon\Carbon::createFromFormat('Y-m-d', $property_management->registration_on)->format('d/m/Y') : '' }}",
        });
        flatpickr("#established_on_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $property_management->established_on ? Carbon\Carbon::createFromFormat('Y-m-d', $property_management->established_on)->format('d/m/Y') : '' }}",
        });
    </script>
    <script>
        $(document).ready(function() {
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function(m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                    .text;
            }
        });
    </script>
@endpush
