@extends('layouts.back-end.app')
@php
    $currentUrl = url()->current();
    $segments = explode('/', $currentUrl);
    $end = end($segments);
@endphp
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
                {{ ui_change('agreement_settings') }}
            </h2>

        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        @include('admin-views.settings.business-setup-inline-menu')



        <!-- Form -->
        <form class="product-form text-start"
            action="{{ route('room_reservation.settings.room_reservation_settings.store') }}" method="POST"
            enctype="multipart/form-data" id="product_form">
            @csrf
            @method('patch')

            <!-- general setup -->
            <div class="card mt-3 rest-part">
                <div class="card-header">
                    <div class="d-flex gap-2">
                        <img src="{{ asset('/public/assets/back-end/img/seller-information.png') }}" class="mb-1"
                            alt="">
                        <h4 class="mb-0">{{ ui_change('room_reservation_settings') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="token"
                                    class="title-color">{{ ui_change('renewal_reminder_by_(days)') }}</label>
                                <input type="number" class="form-control" name="renewal_reminder"
                                    value="{{ isset($renewal_reminder) ? $renewal_reminder : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label for="booked_color" class="title-color">{{ ui_change('booked_color') }}</label>
                                <input type="color" class="form-control" name="booked_color"
                                    value="{{ isset($booked_color) ? $booked_color : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="form-group">
                                <label class="title-color d-block">
                                    {{ ui_change('show_agreement') }}
                                </label>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_agreement"
                                        id="show_agreement" value="1"
                                        {{ isset($show_agreement) && $show_agreement == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_agreement">
                                        Yes
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>


            <div class="row justify-content-end gap-3 mt-3 mx-1">
                <button type="reset" class="btn btn-secondary px-5">{{ ui_change('reset') }}</button>
                <button type="submit" class="btn btn--primary px-5">{{ ui_change('submit') }}</button>
            </div>
        </form>
    @endsection
    @push('script')
        <script>
            flatpickr("#agreement_start_date", {
                dateFormat: "d/m/Y",

            });
        </script>
    @endpush
