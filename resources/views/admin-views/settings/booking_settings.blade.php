@extends('layouts.back-end.app')
@php
    $currentUrl = url()->current();
    $segments = explode('/', $currentUrl);
    $end = end($segments);
@endphp
@section('title', __('general.settings'))

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
                {{ $end == 'booking' ? __('property_transactions.booking_settings') : __('property_transactions.booking_settings') }}
            </h2>

        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        @include('admin-views.settings.business-setup-inline-menu')



        <!-- Form -->
        <form class="product-form text-start"
            action="{{ $end == 'booking' ? route('booking_settings.store') : route('booking_settings.store') }}"
            method="POST" enctype="multipart/form-data" id="product_form">
            @csrf
            @method('patch')

            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img src="{{ asset('/public/assets/back-end/img/seller-information.png') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ __('property_transactions.booking_settings') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="token" class="title-color">{{ __('property_transactions.prefix') }}</label>
                                <input type="text" class="form-control" name="booking_prefix"
                                    value="{{ $booking_prefix }}">
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="token"
                                    class="title-color">{{ __('property_transactions.booking_digits') }}</label>
                                <input type="text" class="form-control" name="booking_digits"
                                    value="{{ $booking_digits }}">
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ __('property_transactions.start_date') }}</label>
                                <input type="text" class="form-control" id="booking_start_date" name="booking_date"
                                    value="{{ isset($booking_date) ? \Carbon\Carbon::parse($booking_date)->format('d-m-Y') : \Carbon\Carbon::now()->format('d-m-Y') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ __('property_transactions.booking_expire_date') }}</label>
                                <input type="number" class="form-control" name="booking_expire_date" class="form-control"
                                    value="{{ isset($booking_expire_date) ? $booking_expire_date : '' }}">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="">{{ ui_change('booking_color') }}</label>
                                <input type="color" class="form-control" name="booking_color" class="form-control"
                                    value="{{ isset($booking_color) ? $booking_color : '' }}">
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-6 col-xl-6">
                            <div class="form-group">
                                <label class="title-color">{{ ui_change('Notification_Types') }}</label>

                                <div class="d-flex flex-wrap gap-3 mt-2">

                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="notifications[]" value="system"
                                            {{ in_array('system', $notifications ?? []) ? 'checked' : '' }}>
                                        {{ ui_change('system') }}
                                    </label>

                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="notifications[]" value="email"
                                            {{ in_array('email', $notifications ?? []) ? 'checked' : '' }}>
                                        {{ ui_change('email') }}
                                    </label>

                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="notifications[]" value="whatsapp"
                                            {{ in_array('whatsapp', $notifications ?? []) ? 'checked' : '' }}>
                                        {{ ui_change('whatsapp') }}
                                    </label>

                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" id="select_all_notifications">
                                        {{ ui_change('select_all') }}
                                    </label>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>


            <div class="row justify-content-end gap-3 mt-3 mx-1"> 
                <button type="submit" class="btn btn--primary px-5">{{ __('submit') }}</button>
            </div>
        </form>
    </div>
@endsection
@push('script')
    <script>
        flatpickr("#booking_start_date", {
            dateFormat: "d/m/Y",

        });
    </script>


    <script>
        document.getElementById('select_all_notifications').addEventListener('change', function() {

            let checkboxes = document.querySelectorAll('input[name="notifications[]"]');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = event.target.checked;
            });

        });
    </script>
@endpush
