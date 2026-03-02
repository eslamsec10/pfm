@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
    $company_settings = App\Models\CompanySettings::where('type', 'signature_mode')->first();

    $isSaudi = $company->countryid == 2;
@endphp

@section('title')
    {{ ui_change('companies', 'hierarchy') }}
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
                {{-- <img src="{{ asset('/assets/back-end/img/inhouse-product-list.png') }}" alt=""> --}}
                {{ ui_change('edit_company', 'hierarchy') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Form -->
        <form class="product-form text-start" id="signature-form" action="{{ route('companies.update', $company->id) }}"
            method="POST" enctype="multipart/form-data" id="product_form">
            @csrf
            @method('patch')

            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('edit_company', 'hierarchy') . ' ' . $company->name }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" required class="title-color">
                                    {{ ui_change('name', 'hierarchy') }}
                                </label>
                                <input type="text" id="name" class="form-control" name="name"
                                    value="{{ old('name', $company->name) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="lang_name" class="title-color">
                                    {{ ui_change('Translated_Name', 'hierarchy') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="lang_name" class="form-control" name="lang_name"
                                    value="{{ old('lang_name', $company->lang_name) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="opening_time" class="title-color">
                                    {{ ui_change('opening_time', 'hierarchy') }}
                                </label>
                                <input type="time" class="form-control" name="opening_time"
                                    value="{{ old('opening_time', $company->opening_time) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="closing_time" class="title-color">
                                    {{ ui_change('closing_time', 'hierarchy') }}
                                </label>
                                <input type="time" class="form-control" name="closing_time"
                                    value="{{ old('closing_time', $company->closing_time) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-2">
                            <div class="form-group">
                                <label for="phone_dail_code" class="title-color">
                                    {{ ui_change('dail_code', 'hierarchy') }}
                                </label>
                                <select class="js-select2-custom form-control" name="phone_dail_code">
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ old('phone_dail_code', $company->phone_dail_code) == '+' . $item_dail_code->dial_code ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="phone" class="title-color">
                                    {{ ui_change('phone', 'hierarchy') }}
                                </label>
                                <input type="number" name="phone" class="form-control"
                                    value="{{ old('phone', $company->phone) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-2">
                            <div class="form-group">
                                <label for="fax_dail_code" class="title-color">
                                    {{ ui_change('dail_code', 'hierarchy') }}
                                </label>
                                <select class="js-select2-custom form-control" name="fax_dail_code">
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ old('fax_dail_code', $company->fax_dail_code) == '+' . $item_dail_code->dial_code ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-2">
                            <div class="form-group">
                                <label for="fax" class="title-color">
                                    {{ ui_change('fax', 'hierarchy') }}
                                </label>
                                <input type="number" name="fax" class="form-control"
                                    value="{{ old('fax', $company->fax) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="financial_year_start_edit" class="title-color">
                                    {{ ui_change('financial_year_start_ith', 'hierarchy') }}
                                </label>
                                <input type="text" name="financial_year" id="financial_year_start_edit"
                                    class="form-control" value="{{ old('financial_year', $company->financial_year) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="booking_beging_with_edit" class="title-color">
                                    {{ ui_change('book_begining_with', 'hierarchy') }}
                                </label>
                                <input type="text" name="book_begining" id="booking_beging_with_edit"
                                    class="form-control" value="{{ old('book_begining', $company->book_begining) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="user_name" class="title-color">
                                    {{ ui_change('user_name', 'hierarchy') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="user_name" class="form-control"
                                    value="{{ old('user_name', $user->user_name) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <label for="password" class="title-color">
                                {{ ui_change('password', 'hierarchy') }}
                            </label>
                            <div class="form-group input-group input-group-merge">
                                <input type="password" class="js-toggle-password form-control" name="password"
                                    id="signupSrPassword" value="{{ old('password', $user->my_name) }}"
                                    placeholder="{{ ui_change('8+_characters_required', 'hierarchy') }}"
                                    aria-label="8+ characters required" required
                                    data-msg="Your password is invalid. Please try again."
                                    data-hs-toggle-password-options='{
                        "target": "#changePassTarget",
                        "defaultClass": "tio-hidden-outlined",
                        "showClass": "tio-visible-outlined",
                        "classChangeTarget": "#changePassIcon"
                    }'>
                                <div id="changePassTarget" class="input-group-append">
                                    <a class="input-group-text" href="javascript:">
                                        <i id="changePassIcon" class="tio-visible-outlined"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                        {{ ui_change('address_logo', 'hierarchy') }}
                    </h5>

                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            {{-- Address 1 --}}
                            <div class="form-group">
                                <label for="address1" class="title-color d-flex gap-1 align-items-center">
                                    @if ($isSaudi)
                                        {{ ui_change('National_Address', 'hierarchy') }}
                                    @else
                                        {{ ui_change('address1', 'hierarchy') }}
                                    @endif
                                </label>
                                <input type="text" id="address1" class="form-control form-control-user"
                                    name="address1" value="{{ old('address1', $company->address1) }}">
                                @error('address1')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Translated Address 1 --}}
                            <div class="form-group">
                                <label for="lang_address1" class="title-color d-flex gap-1 align-items-center">
                                    @if ($isSaudi)
                                        {{ ui_change('Translated_National_Address', 'hierarchy') }}
                                    @else
                                        {{ ui_change('Translated_Address_1', 'hierarchy') }}
                                    @endif
                                </label>
                                <input type="text" id="lang_address1" class="form-control form-control-user"
                                    name="lang_address1" value="{{ old('lang_address1', $company->lang_address1) }}">
                                @error('lang_address1')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (!$isSaudi)
                                {{-- Address 2 --}}
                                <div class="form-group">
                                    <label for="address2" class="title-color d-flex gap-1 align-items-center">
                                        {{ ui_change('address2', 'hierarchy') }}
                                    </label>
                                    <input type="text" id="address2" class="form-control form-control-user"
                                        name="address2" value="{{ old('address2', $company->address2) }}">
                                    @error('address2')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Translated Address 2 --}}
                                <div class="form-group">
                                    <label for="lang_address2" class="title-color d-flex gap-1 align-items-center">
                                        {{ ui_change('Translated_Address_2', 'hierarchy') }}
                                    </label>
                                    <input type="text" id="lang_address2" class="form-control form-control-user"
                                        name="lang_address2" value="{{ old('lang_address2', $company->lang_address2) }}">
                                    @error('lang_address2')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Address 3 --}}
                                <div class="form-group">
                                    <label for="address3" class="title-color d-flex gap-1 align-items-center">
                                        {{ ui_change('address3', 'hierarchy') }}
                                    </label>
                                    <input type="text" id="address3" class="form-control form-control-user"
                                        name="address3" value="{{ old('address3', $company->address3) }}">
                                    @error('address3')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Translated Address 3 --}}
                                <div class="form-group">
                                    <label for="lang_address3" class="title-color d-flex gap-1 align-items-center">
                                        {{ ui_change('Translated_Address_3', 'hierarchy') }}
                                    </label>
                                    <input type="text" id="lang_address3" class="form-control form-control-user"
                                        name="lang_address3" value="{{ old('lang_address3', $company->lang_address3) }}">
                                    @error('lang_address3')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        {{-- Logo Section --}}
                        <div class="col-lg-6">
                            <div class="form-group text-center">
                                <img class="upload-img-view" id="viewer"
                                    src="{{ asset(main_path() . $company->logo_image) }}" alt="banner image"
                                    onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'" />
                            </div>

                            <div class="form-group">
                                <div class="title-color mb-2 d-flex gap-1 align-items-center">
                                    {{ ui_change('logo', 'hierarchy') }}
                                </div>
                                <div class="custom-file text-left">
                                    <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label"
                                        for="customFileUpload">{{ ui_change('upload_image', 'hierarchy') }}</label>
                                </div>
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                        {{ ui_change('general_info', 'hierarchy') }}
                    </h5>

                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="countryid" class="title-color">
                                    {{ ui_change('country', 'hierarchy') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="js-select2-custom form-control" name="countryid" required>
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    @foreach ($country as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('countryid', $company->countryid) == $c->id ? 'selected' : '' }}>
                                            {{ $c->country?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('country_code', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" name="countryCode" readonly
                                    value="{{ old('countryCode', $company->countryCode) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('region', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" readonly
                                    value="{{ old('region', $company->region) }}" name="region" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('currency_name', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" readonly
                                    value="{{ old('currency', $company->currency) }}" name="currency" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('symbol', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" readonly
                                    value="{{ old('symbol', $company->symbol) }}" name="symbol" required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label
                                    class="title-color">{{ ui_change('international_currency_code', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" readonly
                                    value="{{ old('currency_code', $company->currency_code) }}" name="currency_code"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('denomination_name', 'hierarchy') }}</label>
                                <input type="text" class="form-control form-control-user" readonly
                                    value="{{ old('denomination', $company->denomination) }}" name="denomination"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('no_of_decimals', 'hierarchy') }}</label>
                                <input readonly type="text" name="decimals" class="form-control"
                                    value="{{ old('decimals', $company->decimals) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('email', 'hierarchy') }}</label>
                                <input type="text" name="email" class="form-control"
                                    value="{{ old('email', $company->email) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('state', 'hierarchy') }}</label>
                                <input type="text" name="state" class="form-control"
                                    value="{{ old('state', $company->state) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('city', 'hierarchy') }}</label>
                                <input type="text" name="city" class="form-control"
                                    value="{{ old('city', $company->city) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('location', 'hierarchy') }}</label>
                                <input type="text" name="location" class="form-control"
                                    value="{{ old('location', $company->location) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('pin', 'hierarchy') }}</label>
                                <input type="text" name="pin" class="form-control"
                                    value="{{ old('pin', $company->pin) }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-2">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('dail_code', 'hierarchy') }}</label>
                                <select class="js-select2-custom form-control" name="mobile_dail_code">
                                    <option value="" selected>{{ ui_change('select', 'hierarchy') }}</option>
                                    @foreach ($dail_code_main as $item_dail_code)
                                        <option value="{{ '+' . $item_dail_code->dial_code }}"
                                            {{ old('mobile_dail_code', $company->mobile_dail_code) == '+' . $item_dail_code->dial_code ? 'selected' : '' }}>
                                            {{ '+' . $item_dail_code->dial_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-2">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('mobile', 'hierarchy') }}</label>
                                <input type="number" name="mobile" class="form-control"
                                    value="{{ old('mobile', $company->mobile) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                        {{ ui_change('tax_info', 'hierarchy') }}
                    </h5>

                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="form-group">
                                <label for="name"
                                    class="title-color">{{ ui_change('taxability', 'hierarchy') }}</label>
                                <select class="js-select2-custom form-control" name="tax_type" required>
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    <option value="1"
                                        {{ old('tax_type', $company->tax_type) == 1 ? 'selected' : '' }}>
                                        {{ ui_change('taxable', 'hierarchy') }}</option>
                                    <option value="2"
                                        {{ old('tax_type', $company->tax_type) == 2 ? 'selected' : '' }}>
                                        {{ ui_change('zero_rated', 'hierarchy') }}</option>
                                    <option value="3"
                                        {{ old('tax_type', $company->tax_type) == 3 ? 'selected' : '' }}>
                                        {{ ui_change('exempted', 'hierarchy') }}</option>
                                    <option value="4"
                                        {{ old('tax_type', $company->tax_type) == 4 ? 'selected' : '' }}>
                                        {{ ui_change('non_taxable', 'hierarchy') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="tax_status_html tax_status_saudi_html">
                                <label class="title-color">{{ ui_change('vat_no', 'hierarchy') }}</label>
                                <input type="text" name="vat_no" class="form-control"
                                    value="{{ old('vat_no', $company->vat_no) }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="tax_status_html tax_status_saudi_html">
                                <label class="title-color">{{ ui_change('group_vat_no', 'hierarchy') }}</label>
                                <input type="text" name="group_vat_no" class="form-control"
                                    value="{{ old('group_vat_no', $company->group_vat_no) }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="tax_status_html tax_status_saudi_html">
                                <label class="title-color">{{ ui_change('tax_registration_date', 'hierarchy') }}</label>
                                <input type="date" name="tax_reg_date" id="tax_registration_date_edit"
                                    class="form-control" value="{{ old('tax_reg_date', $company->tax_reg_date) }}">
                            </div>
                        </div>
                      
                        <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="tax_status_html" style="{{ old('tax_type', $company->tax_type) == 1 }}">
                                <label class="title-color">{{ ui_change('tax_rate', 'hierarchy') }}</label>
                                <input type="number" name="tax_rate" class="form-control"
                                    value="{{ old('tax_rate', $company->tax_rate) }}">
                            </div>
                        </div>
                          <div class="col-md-6 col-lg-4 col-xl-3 ">
                            <div class="tax_status_html tax_status_saudi_html">
                                <div>
                                    <input type="radio" name="status" value="active"
                                        {{ old('status', $company->status ?? 'active') == 'active' ? 'checked' : '' }}>
                                    <label class="title-color">{{ ui_change('active', 'hierarchy') }}</label>

                                    <input type="radio" name="status" value="inactive"
                                        {{ old('status', $company->status) == 'inactive' ? 'checked' : '' }}>
                                    <label class="title-color">{{ ui_change('inactive', 'hierarchy') }}</label>
                                </div>
                            </div>
                        </div>
                        {{-- 
            <div class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 ? '' : 'd-none' }} tax_status_html tax_status_saudi_html">
                <div class="form-group">
                    <label class="title-color">{{ ui_change('vat_no', 'hierarchy') }}</label>
                    <input type="text" name="vat_no" class="form-control" value="{{ old('vat_no', $company->vat_no) }}">
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 ? '' : 'd-none' }} tax_status_html tax_status_saudi_html">
                <div class="form-group">
                    <label class="title-color">{{ ui_change('group_vat_no', 'hierarchy') }}</label>
                    <input type="text" name="group_vat_no" class="form-control" value="{{ old('group_vat_no', $company->group_vat_no) }}">
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 ? '' : 'd-none' }} tax_status_html tax_status_saudi_html">
                <div class="form-group">
                    <label class="title-color">{{ ui_change('tax_registration_date', 'hierarchy') }}</label>
                    <input type="date" name="tax_reg_date" id="tax_registration_date_edit" class="form-control" value="{{ old('tax_reg_date', $company->tax_reg_date) }}">
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi != 2 ? '' : 'd-none' }} tax_status_html">
                <div class="form-group">
                    <label class="title-color">{{ ui_change('tax_rate', 'hierarchy') }}</label>
                    <input type="text" name="tax_rate" class="form-control" value="{{ old('tax_rate', $company->tax_rate) }}">
                </div>
            </div>

            <div class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi != 2 ? '' : 'd-none' }} mt-5 tax_status_html">
                <div class="form-group d-flex align-items-center gap-3">
                    <div>
                        <input type="radio" name="status" value="active" {{ old('status', $company->status ?? 'active') == 'active' ? 'checked' : '' }}>
                        <label class="title-color">{{ ui_change('active', 'hierarchy') }}</label>
                    </div>
                    <div>
                        <input type="radio" name="status" value="inactive" {{ old('status', $company->status) == 'inactive' ? 'checked' : '' }}>
                        <label class="title-color">{{ ui_change('inactive', 'hierarchy') }}</label>
                    </div>
                </div>
            </div> --}}

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('organization_unit_name', 'hierarchy') }}</label>
                                <input type="text" name="organization_unit_name" id="organization_unit_name_edit"
                                    class="form-control"
                                    value="{{ old('organization_unit_name', $company->organization_unit_name) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label
                                    class="title-color">{{ ui_change('commercial_registration_number', 'hierarchy') }}</label>
                                <input type="number" name="commercial_registration_number"
                                    id="commercial_registration_number_edit" class="form-control"
                                    value="{{ old('commercial_registration_number', $company->commercial_registration_number) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('invoice_type', 'hierarchy') }}</label>
                                <select class="js-select2-custom form-control" name="invoice_type">
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    <option value="1100"
                                        {{ old('invoice_type', $company->invoice_type) == 1100 ? 'selected' : '' }}>
                                        {{ ui_change('both', 'hierarchy') }}</option>
                                    <option value="0100"
                                        {{ old('invoice_type', $company->invoice_type) == 0100 ? 'selected' : '' }}>
                                        {{ ui_change('simplified_invoice', 'hierarchy') }}</option>
                                    <option value="1000"
                                        {{ old('invoice_type', $company->invoice_type) == 1000 ? 'selected' : '' }}>
                                        {{ ui_change('standard_invoice', 'hierarchy') }}</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('environment', 'hierarchy') }}</label>
                                <select class="js-select2-custom form-control" name="environment">
                                    <option value="">{{ ui_change('select', 'hierarchy') }}</option>
                                    <option value="developer-portal"
                                        {{ old('environment', $company->environment) == 'developer-portal' ? 'selected' : '' }}>
                                        {{ ui_change('test', 'hierarchy') }}</option>
                                    <option value="simulation"
                                        {{ old('environment', $company->environment) == 'simulation' ? 'selected' : '' }}>
                                        {{ ui_change('simulation', 'hierarchy') }}</option>
                                    <option value="core"
                                        {{ old('environment', $company->environment) == 'core' ? 'selected' : '' }}>
                                        {{ ui_change('core', 'hierarchy') }}</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('short_address', 'hierarchy') }}</label>
                                <input type="text" name="short_address" id="short_address_edit" class="form-control"
                                    value="{{ old('short_address', $company->short_address) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('Zip_Code', 'hierarchy') }}</label>
                                <input type="number" name="zip_code" id="zip_code_edit" class="form-control"
                                    value="{{ old('zip_code', $company->zip_code) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('OTP', 'hierarchy') }}</label>
                                <input type="number" name="otp" id="otp_edit" class="form-control"
                                    value="{{ old('otp', $company->otp) }}">
                            </div>
                        </div>

                        <div
                            class="col-md-6 col-lg-4 col-xl-3 {{ old('tax_type', $company->tax_type) == 1 && $isSaudi == 2 ? '' : 'd-none' }} tax_status_saudi_html">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('company_category', 'hierarchy') }}</label>
                                <input type="text" name="company_category" id="company_category_edit"
                                    class="form-control"
                                    value="{{ old('company_category', $company->company_category) }}">
                            </div>
                        </div>
                        <!-- Saudi Tax Info -->

                    </div>
                    <div class="row align-items-end">
                        <!-- Checkbox -->
                        <div class="col-3">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('Govt.Levy') }}</label>
                                <input type="checkbox" id="apply_levy_checkbox" name="apply_levy"
                                    @if ($company->levy_id != null) checked @endif>
                                <label for="apply_levy_checkbox"> </label>
                            </div>
                        </div>

                        <!-- Levy Select -->
                        <div class="col-3" id="levy_select_col"
                            style="@if ($company->levy_id != null) display:block; @else display:none; @endif">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('select_levy') }}</label>
                                <select name="levy_id" id="levy_select" class="form-control">
                                    <option value="">{{ ui_change('-- Choose Levy --') }}</option>
                                    @foreach ($levies as $levy)
                                        <option value="{{ $levy->id }}" data-percentage="{{ $levy->percentage }}"
                                            @if ($company->levy_id == $levy->id) selected @endif>
                                            {{ $levy->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Levy Percentage -->
                        <div class="col-3" id="levy_percentage_col"
                            style="@if ($company->levy_id != null) display:block; @else display:none; @endif">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('Levy_Percentage') }}</label>
                                <input type="text" name="levy_percentage" id="levy_percentage" class="form-control"
                                    value="{{ $company->levy_id ? $company->levy_percentage : '' }}" readonly>
                            </div>
                        </div>

                        <!-- Levy Applicable Date -->
                        <div class="col-3" id="levy_date_col"
                            style="@if ($company->levy_id != null) display:block; @else display:none; @endif">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('Levy_Applicable_Date') }}</label>
                                <input type="date" name="levy_applicable_date"
                                    class="form-control levy_applicable_date" value="{{ $company->levy_date }}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- #region -->

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2 border-bottom pb-3 mb-4 pl-4">
                        {{-- <img src="{{ asset('/assets/back-end/img/seller-information.png') }}" class="mb-1"
                            alt=""> --}}
                        {{ ui_change('signature_seal', 'hierarchy') }}
                    </h5>
                    <div class="row align-items-center">
                        @if (isset($company_settings) && $company_settings->value == 'digital')
                            <div class="col-lg-6 mb-4 mb-lg-0">


                                <div class="form-group">
                                    <a id="add-signature-pad"
                                        class="btn btn--primary mb-2">{{ ui_change('add_signature', 'hierarchy') }}</a>
                                    <div id="signature-pads-container">
                                        @isset($company->signature)
                                            <img width="150px" height="150px" id="signature_img"
                                                src="{{ $company->signature }}" alt="Signature">
                                        @endisset

                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- {{ dd( asset(main_path().'seal/'.$company->name.'/' . $company->seal)) }} --}}
                            {{-- {{ dd(asset('seal/' . $company->name . '/' . $company->seal)) }} --}}
                            <div class="col-lg-6" id="seal_image">
                                <div class="form-group">
                                    <center>
                                        <img class="upload-img-view" id="viewerSeal"
                                            src="{{ asset('seal/' . $company->name . '/' . $company->seal) }} "
                                            alt="banner image"
                                            onerror="this.src='{{ asset('assets/back-end/img/400x400/img2.jpg') }}'" />
                                    </center>
                                </div>

                                <div class="form-group">
                                    <div class="title-color mb-2 d-flex gap-1 align-items-center">
                                        {{ ui_change('seal', 'hierarchy') }}

                                    </div>
                                    <div class="custom-file text-left">
                                        <input type="file" name="seal" id="customFileUploadSeal"
                                            class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                            for="customFileUploadSeal">{{ ui_change('upload_image', 'hierarchy') }}</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset', 'hierarchy') }}</button>
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit', 'hierarchy') }}</button>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>

    <script>
        $(document).ready(function() {

            // Toggle show/hide fields when checkbox changed
            $('#apply_levy_checkbox').change(function() {
                if ($(this).is(':checked')) {
                    $('#levy_select_col, #levy_percentage_col, #levy_date_col').slideDown();
                } else {
                    $('#levy_select_col, #levy_percentage_col, #levy_date_col').slideUp();
                    $('#levy_select').val('');
                    $('#levy_percentage').val('');
                    $('input[name="levy_applicable_date"]').val('');
                }
            });

            // Update percentage when levy selected
            $('#levy_select').change(function() {
                var percentage = $(this).find(':selected').data('percentage') || '';
                $('#levy_percentage').val(percentage);
            });

        });
    </script>


    <script>
        $(document).on('change', '[name="tax_type"], [name="countryid"]', function() {
            let taxType = $('[name="tax_type"]').val();
            let country = $('[name="countryid"]').val();
            let saudiID = 2;
            if (taxType == 1 && country == saudiID) {
                $('.tax_status_html').addClass('d-none');
                $('.tax_status_saudi_html').removeClass('d-none');
            } else {
                $('.tax_status_saudi_html').addClass('d-none');
                $('.tax_status_html').removeClass('d-none');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('select[name="tax_type"]').on('change', function() {
                let status = $(this).val();
                if (status == 1 || status == 2 || status == 3 || status == 4) {
                    $(".tax_status_html").removeClass('d-none');
                } else {
                    $(".tax_status_html").addClass('d-none');
                }
                var tax_type = $(this).val();
                var $tax_rate_input = $('input[name="tax_rate"]');

                if (tax_type == '3' || tax_type == '1') {
                    $tax_rate_input.removeAttr('disabled').val('0');
                } else if (tax_type == '2' || tax_type == '4') {
                    $tax_rate_input.attr('disabled', 'disabled').val('0');
                }
            });
        });
    </script>
    <script>
        flatpickr("#financial_year_start_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $company->financial_year ? Carbon\Carbon::createFromFormat('Y-m-d', $company->financial_year)->format('d/m/Y') : '' }}",
        });
        flatpickr("#booking_beging_with_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ isset($company->book_begining) ? \Carbon\Carbon::createFromFormat('Y-m-d', $company->book_begining)->format('d/m/Y') : '' }}"
        });

        flatpickr("#tax_registration_date_edit", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ $company->tax_reg_date ? \Carbon\Carbon::createFromFormat('Y-m-d', $company->tax_reg_date)->format('d/m/Y') : '' }}",
        });
        flatpickr(".levy_applicable_date", {
            dateFormat: "d/m/Y",
            defaultDate: "{{ \Carbon\Carbon::now()->format('d/m/Y') }}",
        });
        $(function() {
            $('#color_switcher').click(function() {
                var checkBoxes = $("#color_switcher");
                if ($('#color_switcher').prop('checked')) {
                    $('.color_image_column').removeClass('d-none');
                    $('.additional_image_column').removeClass('col-md-9');
                    $('.additional_image_column').addClass('col-md-12');
                    $('#color_wise_image').show();
                    $('#additional_Image_Section .col-md-4').addClass('col-lg-2');
                } else {
                    $('.color_image_column').addClass('d-none');
                    $('.additional_image_column').addClass('col-md-9');
                    $('.additional_image_column').removeClass('col-md-12');
                    $('#color_wise_image').hide();
                    $('#additional_Image_Section .col-md-4').removeClass('col-lg-2');
                }
            });

            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 15,
                rowHeight: 'auto',
                groupClassName: 'col-6 col-md-4 col-lg-3 col-xl-2',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        '{{ ui_change('please_only_input_png_or_jpg_type_file', 'hierarchy') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ ui_change('file_size_too_big', 'hierarchy') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLSeal(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewerSeal').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });
        $("#customFileUploadSeal").change(function() {
            readURLSeal(this);
        });


        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF SHOW PASSWORD
            // =======================================================
            $('.js-toggle-password').each(function() {
                new HSTogglePassword(this).init()
            });

            // INITIALIZATION OF FORM VALIDATION
            // =======================================================
            $('.js-validate').each(function() {
                $.HSCore.components.HSValidation.init($(this));
            });
        });
    </script>
    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append(
                '<div class="col-md-6"><div class="form-group"><input type="hidden" name="choice_no[]" value="' + i +
                '"><label class="title-color">' + n + '</label><input type="text" name="choice[]" value="' + n +
                '" hidden><div class=""><input type="text" class="form-control" name="choice_options_' + i +
                '[]" placeholder="{{ ui_change('enter_choice_values', 'hierarchy') }}" data-role="tagsinput" onchange="update_sku()"></div></div></div>'
            );

            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        $('#colors-selector').on('change', function() {
            update_sku();

            if ($('#color_switcher').prop('checked')) {
                color_wise_image($('#colors-selector'));
            }

            $('.remove_button').on('click', function() {
                alert('ok');
                $(this).parents('.upload_images')
                    .find('.color_image')
                    .attr('src', '{{ asset('assets/back-end/img/400x400/img2.jpg') }}');
            });
        });



        function color_wise_image(t) {
            let colors = t.val();
            $('#color_wise_image').html('')
            $.each(colors, function(key, value) {
                let value_id = value.replace('#', '');
                let color = "color_image_" + value_id;

                html = `<div class="col-sm-12 col-md-4">
                            <div class="custom_upload_input position-relative border-dashed-2">
                                <input type="file" name="` + color +
                    `" class="custom-upload-input-file" id="color-img-upload-` + value_id + `" data-index="1" data-imgpreview="additional_Image_${value_id}"
                                    accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required onchange="uploadColorImage(this)">

                                <div class="position-absolute right-0 top-0 d-flex gap-2">
                                    <label for="color-img-upload-` + value_id + `" class="delete_file_input_css btn btn-outline-danger btn-sm square-btn position-relative" style="background: ${value};border-color: ${value};color:#fff">
                                        <i class="tio-edit"></i>
                                    </label>

                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn position-relative" style="display: none">
                                        <i class="tio-delete"></i>
                                    </span>
                                </div>

                                <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                    <img id="additional_Image_${value_id}" class="h-auto aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')">
                                </div>
                                <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <img src="{{ asset('assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-50">
                                        <h3 class="text-muted">{{ ui_change('Upload_Image', 'hierarchy') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                $('#color_wise_image').append(html);

                $('.delete_file_input').click(function() {
                    let $parentDiv = $(this).parent().parent();
                    $parentDiv.find('input[type="file"]').val('');
                    $parentDiv.find('.img_area_with_preview img').attr("src", " ");
                    $(this).hide();
                });

                $('.custom-upload-input-file').on('change', function() {
                    if (parseFloat($(this).prop('files').length) != 0) {
                        let $parentDiv = $(this).closest('div');
                        $parentDiv.find('.delete_file_input').fadeIn();
                    }
                });

                uploadColorImage();
            });
        }

        function uploadColorImage(thisData = null) {
            if (thisData) {
                document.getElementById(thisData.dataset.imgpreview).setAttribute("src", window.URL.createObjectURL(thisData
                    .files[0]));
                document.getElementById(thisData.dataset.imgpreview).classList.remove('d-none');
            }
        }




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

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('select[name="countryid"]').on('change', function() {
                var country_master_id = $(this).val();
                if (country_master_id) {
                    $.ajax({
                        url: "{{ URL::to('get_country_master') }}/" + country_master_id,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data) {
                                console.log(data)
                                $('input[name="countryCode"]').empty();
                                $('input[name="countryCode"]').val(data.country_code);
                                $('input[name="region"]').empty();
                                $('input[name="region"]').val(data.region.name);
                                $('input[name="currency"]').empty();
                                $('input[name="currency"]').val(data.currency_name);
                                $('input[name="symbol"]').empty();
                                $('input[name="symbol"]').val(data.currency_symbol);
                                $('input[name="currency_code"]').empty();
                                $('input[name="currency_code"]').val(data
                                    .international_currency_code);
                                $('input[name="denomination"]').empty();
                                $('input[name="denomination"]').val(data.denomination_name);
                                $('input[name="decimals"]').empty();
                                $('input[name="decimals"]').val(data.no_of_decimals);


                            } else {}
                        },
                        error: function(xhr, status, error) {
                            console.error('Error occurred:', error);
                        }
                    });
                }

            });
            $('select[name="tax_type"]').on('change', function() {
                var tax_type = $(this).val();
                var $tax_rate_input = $('input[name="tax_rate"]');

                if (tax_type === 'exempted' || tax_type === 'taxable') {
                    $tax_rate_input.removeAttr('disabled').val('0');
                } else if (tax_type === 'zero_rated' || tax_type === 'non_taxable') {
                    $tax_rate_input.attr('disabled', 'disabled').val('0');
                }
            });

        });
    </script>

    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>

    {{-- ck editor --}}

    <script>
        $('.delete_file_input').click(function() {
            let $parentDiv = $(this).closest('div');
            $parentDiv.find('input[type="file"]').val('');
            $parentDiv.find('.img_area_with_preview img').attr("src", " ");
            $(this).hide();
        });

        $('.custom-upload-input-file').on('change', function() {
            if (parseFloat($(this).prop('files').length) != 0) {
                let $parentDiv = $(this).closest('div');
                $parentDiv.find('.delete_file_input').fadeIn();
            }
        })
    </script>

    <script>
        function addMoreImage(thisData, targetSection) {

            let $fileInputs = $(targetSection + " input[type='file']");
            let nonEmptyCount = 0;

            $fileInputs.each(function() {
                if (parseFloat($(this).prop('files').length) == 0) {
                    nonEmptyCount++;
                }
            });

            // let input_id = thisData.id;
            document.getElementById(thisData.dataset.imgpreview).setAttribute("src", window.URL.createObjectURL(thisData
                .files[0]));
            document.getElementById(thisData.dataset.imgpreview).classList.remove('d-none');

            if (nonEmptyCount == 0) {

                let dataset_index = thisData.dataset.index + 1;

                let newHtmlData = `<div class="col-sm-12 col-md-4">
                        <div class="custom_upload_input position-relative border-dashed-2">
                            <input type="file" name="${thisData.name}" class="custom-upload-input-file" data-index="${dataset_index}" data-imgpreview="additional_Image_${dataset_index}"
                                accept=".jpg, .webp, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" onchange="addMoreImage(this, '${targetSection}')">

                            <span class="delete_file_input delete_file_input_section btn btn-outline-danger btn-sm square-btn" style="display: none">
                                <i class="tio-delete"></i>
                            </span>

                            <div class="img_area_with_preview position-absolute z-index-2 border-0">
                                <img id="additional_Image_${dataset_index}" class="h-auto aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')">
                            </div>
                            <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <img src="{{ asset('assets/back-end/img/icons/product-upload-icon.svg') }}" class="w-50">
                                    <h3 class="text-muted">{{ ui_change('Upload_Image', 'hierarchy') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $(targetSection).append(newHtmlData);
            }


            $('.custom-upload-input-file').on('change', function() {
                if (parseFloat($(this).prop('files').length) != 0) {
                    let $parentDiv = $(this).closest('div');
                    $parentDiv.find('.delete_file_input').fadeIn();
                }
            })

            $('.delete_file_input_section').click(function() {
                let $parentDiv = $(this).closest('div').parent().remove();
                // var filledInputs = $(targetSection +' input[type="file"]').length;
            });

            if ($('#color_switcher').prop('checked')) {
                $('#additional_Image_Section .col-md-4').addClass('col-lg-2');
            } else {
                $('#additional_Image_Section .col-md-4').removeClass('col-lg-2');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        document.getElementById('add-signature-pad').addEventListener('click', () => {
            let signature_img = document.getElementById('signature_img');
            let seal_image = document.getElementById('seal_image');
            if (signature_img) {
                signature_img.classList.add('d-none');
                seal_image.classList.add('d-none');
            }
            if (seal_image) {
                seal_image.classList.add('d-none');
            }
            createSignaturePad();
        });

        function createSignaturePad() {

            const padContainer = document.createElement('div');
            let add_btn = document.getElementById('add-signature-pad');
            add_btn.setAttribute('hidden', '');
            padContainer.classList.add('signature-pad-container');

            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 200;
            canvas.style.border = '1px solid #000';

            const clearButton = document.createElement('a');
            clearButton.textContent = "{{ ui_change('Clear', 'hierarchy') }}";
            clearButton.classList.add('btn', 'btn-danger', 'm-1');
            clearButton.addEventListener('click', () => {
                signaturePad.clear();
                inputElement.value = '';
            });
            // signature_img
            const deleteButton = document.createElement('a');
            deleteButton.textContent = "{{ ui_change('Delete', 'hierarchy') }}";
            deleteButton.classList.add('btn', 'btn-warning');
            deleteButton.addEventListener('click', () => {
                padContainer.remove();
                add_btn.removeAttribute('hidden');
                let seal_image = document.getElementById('seal_image');
                if (seal_image) {
                    seal_image.classList.remove('d-none');
                }
            });

            padContainer.appendChild(canvas);
            padContainer.appendChild(clearButton);
            padContainer.appendChild(deleteButton);
            document.getElementById('signature-pads-container').appendChild(padContainer);

            const signaturePad = new SignaturePad(canvas);

            const inputElement = document.createElement('input');
            inputElement.type = 'hidden';
            inputElement.name = 'signature';

            document.getElementById('signature-form').appendChild(inputElement);

            signaturePad.onEnd = () => {
                inputElement.value = signaturePad.toDataURL('image/png');
            };
        }
    </script>


    <script>
        jQuery('body').on('change', '#financial_year', function() {
            var date = $('#financial_year').val();
            $('#book_begining').val(date);
        });
    </script>

    <script>
        function setupAutoTranslate(sourceId, targetId) {
            const source = document.getElementById(sourceId);
            const target = document.getElementById(targetId);

            source.addEventListener('input', async function() {
                const text = this.value.trim();
                if (!text) {
                    target.value = '';
                    return;
                }

                try {
                    const response = await fetch(
                        'https://api.mymemory.translated.net/get?q=' + encodeURIComponent(text) +
                        '&langpair=en|ar'
                    );
                    const data = await response.json();
                    target.value = data.responseData.translatedText;
                } catch (error) {
                    console.error('Translation API error:', error);
                }
            });
        }

        setupAutoTranslate('name', 'lang_name');
        setupAutoTranslate('address1', 'lang_address1');
        setupAutoTranslate('address2', 'lang_address2');
        setupAutoTranslate('address3', 'lang_address3');
    </script>
@endpush
