@extends('layouts.back-end.app')

@section('title', ui_change('settings'))

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
                {{ ui_change('settings') }}
            </h2>

        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        @include('admin-views.settings.business-setup-inline-menu')



        <!-- Form -->
        <form class="product-form text-start" action="{{ route('notifications_settings.store') }}" method="POST"
            enctype="multipart/form-data" id="product_form">
            @csrf
            @method('patch')

            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <h4 class="mb-0">{{ ui_change('settings') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- @php($signature_mode = \App\Models\CompanySettings::where('type', 'signature_mode')->first())
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="name" class="title-color">{{ __('companies.signature_mode') }}</label>
                                <select class="js-select2-custom form-control" name="signature_mode" required>
                                    <option selected disabled>{{ __('general.select') }}</option>
                                    <option value="digital" @if (isset($signature_mode) && $signature_mode->value == 'digital') selected @endif>
                                        {{ __('companies.digital') }}</option>
                                    <option value="normal" @if (isset($signature_mode) && $signature_mode->value == 'normal') selected @endif>
                                        {{ __('companies.normal') }}</option>
                                </select>
                            </div>
                        </div> --}}

                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="email" class="title-color">{{ ui_change('email') }}</label>
                                <input class="form-control" type="email" name="email" value=" ">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="appkey" class="title-color">{{ ui_change('app_key') }}</label>
                                <input class="form-control" type="text" name="appkey" value=" ">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label for="authkey" class="title-color">{{ ui_change('auth_key') }}</label>
                                <input class="form-control" type="text" name="authkey" value=" ">
                            </div>
                        </div>
                        {{-- @php($height = \App\Models\CompanySettings::where('type', 'height')->first())
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="height" class="title-color">{{ __('collections.height') }} (px)</label>
                                <input class="form-control" type="text" name="height"
                                    value="{{ $height->value ?? '' }}">
                            </div>
                        </div> --}}

                    </div>
                </div>


            </div>


            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit') }}</button>
            </div>
        </form>
    </div>

@endsection
