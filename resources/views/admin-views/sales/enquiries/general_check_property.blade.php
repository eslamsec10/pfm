@extends('layouts.back-end.app')
@php
    $lang = session()->get('locale');
    $currentUrl = url()->current();
    $segments = explode('/', $currentUrl);
    $last = end($segments);
@endphp

@section('title')
    {{ ui_change('check_property', 'property_transaction') }}
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

        .unit-row {
            position: relative;
        }

        .unit-tooltip {
            display: none;
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            width: 300px;
            z-index: 999;
            text-align: right;
            font-size: 14px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .unit-row:hover .unit-tooltip {
            display: block;
        }

        .unit-row:hover {
            background-color: #888 !important;
            cursor: pointer;
        }

        .status-enquiry {
            background-color: #372be2;
            color: white;
        }

        .status-proposal {
            background-color: #ffeb3b;
            color: black;
        }

        .status-booking {
            background-color: #d500f9;
            color: white;
        }

        .status-agreement {
            background-color: #f44336;
            color: white;
        }
    </style>
@endpush
@php
    $paymentModes = [
        1 => ui_change('daily', 'property_transaction'),
        2 => ui_change('monthly', 'property_transaction'),
        3 => ui_change('bi_monthly', 'property_transaction'),
        4 => ui_change('quarterly', 'property_transaction'),
        5 => ui_change('half_yearly', 'property_transaction'),
        6 => ui_change('yearly', 'property_transaction'),
    ];
    $company = App\Models\Company::select('decimals', 'currency_code')->first();
@endphp

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        {{-- <div class="inline-page-menu my-4">
            <ul class="list-unstyled">
                <li class="{{ Request::is('enquiry/general_check_property*') ? 'active' : '' }}">
                    <a
                        href="{{ route('enquiry.general_check_property') }}">{{ ui_change('general_check_property', 'property_transaction') }}</a>
                </li>
            </ul>
        </div> --}}
        @include('admin-views.inline_menu.property_transaction.inline-menu')
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row align-items-center">
                            <div class="col-12 d-flex align-items-center flex-wrap gap-2">

                                @if ($last == 'general_check_property')
                                    <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                        class="btn btn--primary btn-sm me-2">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                @endif

                                <a href="{{ route('general_property_list') }}" class="btn btn-secondary px-5 me-2">
                                    {{ ui_change('check_property', 'property_transaction') }}
                                </a>
                                <a href="{{ route('general_list_view') }}" class="btn btn-secondary px-5 me-2">
                                    {{ ui_change('list_view', 'property_transaction') }}
                                </a>
                                <a href="{{ route('general_image_view') }}" class="btn btn-secondary px-5 me-2">
                                    {{ ui_change('image_view', 'property_transaction') }}
                                </a>

                                <form id="productForm" method="get" class="d-flex flex-wrap gap-2">
                                    <button type="submit"
                                        onclick="setFormAction('{{ route('enquiry.create_with_select_unit') }}')"
                                        class="btn btn--primary createButton">
                                        <i class="tio-add"></i>
                                        <span
                                            class="text">{{ ui_change('create_enquiry', 'property_transaction') }}</span>
                                    </button>

                                    <button type="submit"
                                        onclick="setFormAction('{{ route('proposal.create_with_select_unit') }}')"
                                        class="btn btn--primary createButton">
                                        <i class="tio-add"></i>
                                        <span
                                            class="text">{{ ui_change('create_proposal', 'property_transaction') }}</span>
                                    </button>

                                    <button type="submit"
                                        onclick="setFormAction('{{ route('booking.create_with_select_unit') }}')"
                                        class="btn btn--primary createButton">
                                        <i class="tio-add"></i>
                                        <span
                                            class="text">{{ ui_change('create_booking', 'property_transaction') }}</span>
                                    </button>

                                    <button type="submit"
                                        onclick="setFormAction('{{ route('agreement.create_with_select_unit') }}')"
                                        class="btn btn--primary createButton">
                                        <i class="tio-add"></i>
                                        <span
                                            class="text">{{ ui_change('create_agreement', 'property_transaction') }}</span>
                                    </button>


                            </div>
                        </div>

                        {{-- <div class="row align-items-center">
                            <div class="col-lg-1 d-flex justify-content-end">
                                @if ($last == 'general_check_property')
                                    <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                        class="btn btn--primary btn-sm">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                @endif
                            </div>
                            <a href="{{ route('general_property_list') }}"
                                class="btn btn-secondary px-5">{{ ui_change('check_property', 'property_transaction') }}</a>
                            <div class="col-lg-11 d-flex justify-content-end">

                                <form id="productForm" action="#" method="get">
                                    <form id="productForm" method="get">
                                        <button type="submit"
                                            onclick="setFormAction('{{ route('enquiry.create_with_select_unit') }}')"
                                            class="m-2 btn btn--primary createButton">
                                            <i class="tio-add"></i>
                                            <span
                                                class="text">{{ ui_change('create_enquiry', 'property_transaction') }}</span>
                                        </button>

                                        <button type="submit"
                                            onclick="setFormAction('{{ route('proposal.create_with_select_unit') }}')"
                                            class="m-2 btn btn--primary createButton">
                                            <i class="tio-add"></i>
                                            <span
                                                class="text">{{ ui_change('create_proposal', 'property_transaction') }}</span>
                                        </button>

                                        <button type="submit"
                                            onclick="setFormAction('{{ route('booking.create_with_select_unit') }}')"
                                            class="m-2 btn btn--primary createButton">
                                            <i class="tio-add"></i>
                                            <span
                                                class="text">{{ ui_change('create_booking', 'property_transaction') }}</span>
                                        </button>

                                        <button type="submit"
                                            onclick="setFormAction('{{ route('agreement.create_with_select_unit') }}')"
                                            class="m-2 btn btn--primary createButton">
                                            <i class="tio-add"></i>
                                            <span
                                                class="text">{{ ui_change('create_agreement', 'property_transaction') }}</span>
                                        </button>


                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="d-flex flex-wrap mb-3">
                        <span class="badge  me-2 m-2 p-2" style="background-color: #372be2;color:white">
                            {{ ui_change('Proposal_Pending', 'property_transaction') }}</span>
                        <span class="badge  me-2 m-2 p-2"
                            style="color:black;background-color: #ffeb3b;">{{ ui_change('Proposed_Unit', 'property_transaction') }}
                        </span>
                        <span class="badge   me-2 m-2 p-2"
                            style="color:black;background-color: #d500f9;">{{ ui_change('Booked_Unit', 'property_transaction') }}
                        </span>
                        <span class="badge   me-2 m-2 p-2"
                            style="color:black;background-color: #f44336;">{{ ui_change('Agreement_Unit', 'property_transaction') }}
                        </span>


                    </div>
                    <div class="px-3 py-4">
                        <div style="text-align: {{ Session::get('locale') === 'ar' ? 'right' : 'left' }};">
                            <div class="table-responsive">
                                <table id="datatable"
                                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th width="40"><input class="bulk_check_all" type="checkbox" /></th>
                                            <th width="50">{{ ui_change('Unit_No.', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Property', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Block', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Floor', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Unit_Description', 'property_transaction') }}
                                            </th>
                                            <th width="50">{{ ui_change('Unit_Condition', 'property_transaction') }}
                                            </th>
                                            <th width="50">{{ ui_change('Unit_Type', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Unit_View', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('rent_amount', 'property_transaction') }}
                                                ({{ $company->currency_code }})</th>
                                            <th width="50">{{ ui_change('Status', 'property_transaction') }}</th>
                                            <th width="50">{{ ui_change('Show_Info', 'property_transaction') }}</th>
                                        </tr>
                                    </thead>
                                    <input type="hidden" name="tenant_id"
                                        value="{{ isset($tenant_id) ? $tenant_id : '' }}">
                                    <tbody id="unit_list">
                                        @foreach ($units as $unit)
                                            <tr class="unit-row"
                                                @if ($unit->booking_status == 'enquiry') style="background-color: #372be2;color:white"
                                                 
                                            @elseif ($unit->booking_status == 'proposal')
                                            style="background-color: #ffeb3b;color:black"
                                             
                                            @elseif ($unit->booking_status == 'booking')
                                            style="background-color: #d500f9;color:white"
                                             
                                            @elseif ($unit->booking_status == 'agreement')
                                            style="background-color: #f44336;color:white" @endif>

                                                <td width="50" style="position: relative;">
                                                    <label>
                                                        @if ($unit->booking_status == 'empty')
                                                            <input class="check_bulk_item" name="bulk_ids[]" type="checkbox"
                                                                value="{{ $unit->id }}" />
                                                        @elseif($unit->booking_status == 'enquiry')
                                                            @php
                                                                $periodFrom = optional($unit->enquiry)->period_from;
                                                            @endphp
                                                            @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                                <input class="check_bulk_item" name="bulk_ids[]"
                                                                    type="checkbox" value="{{ $unit->id }}" />
                                                            @endif
                                                        @elseif ($unit->booking_status == 'proposal')
                                                            @php
                                                                $periodFrom = optional($unit->proposal_main)
                                                                    ->commencement_date;
                                                            @endphp
                                                            @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                                <input class="check_bulk_item" name="bulk_ids[]"
                                                                    type="checkbox" value="{{ $unit->id }}" />
                                                            @endif
                                                        @elseif ($unit->booking_status == 'booking')
                                                            @php
                                                                $periodFrom = optional($unit->booking_main)
                                                                    ->commencement_date;
                                                            @endphp
                                                            @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                                <input class="check_bulk_item" name="bulk_ids[]"
                                                                    type="checkbox" value="{{ $unit->id }}" />
                                                            @endif
                                                        @elseif ($unit->booking_status == 'agreement')
                                                            @php
                                                                $periodFrom = optional($unit->agreement_main)
                                                                    ->commencement_date;
                                                            @endphp
                                                            @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                                <input class="check_bulk_item" name="bulk_ids[]"
                                                                    type="checkbox" value="{{ $unit->id }}" />
                                                            @endif
                                                        @endif
                                                        <span class="text-muted">#{{ $loop->index + 1 }}</span>
                                                    </label>
                                                </td>

                                                <td width="50">
                                                    @if ($unit->booking_status == 'enquiry')
                                                        @php
                                                            $enquiry_setting = optional(
                                                                App\Models\BusinessSetting::whereType(
                                                                    'enquiry_expire_date',
                                                                )->first(),
                                                            )->value;
                                                            $enquiry_main = optional(
                                                                optional($unit->enquiry)->enquiry_main,
                                                            );
                                                        @endphp
                                                        <div class="unit-tooltip" style="text-align: left;">
                                                            <strong>{{ ui_change('Enquiry_Ref ', 'property_transaction') }}:
                                                                {{ optional(optional($unit->enquiry)->main_enquiry)->enquiry_no }}</strong><br>
                                                            {{ ui_change('Enquiry_Date', 'property_transaction') }} :
                                                            {{ optional(optional($unit->enquiry)->main_enquiry)->enquiry_date }}<br>
                                                            {{ ui_change('Rent_Amount', 'property_transaction') }} :
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->main_enquiry)->rent_amount }}
                                                            ({{ $company->currency_code }})
                                                            <br>
                                                            {{ ui_change('Contact_Number', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->contact_no }}<br>
                                                            {{ ui_change('Tenant', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->company_name }}
                                                           
                                                            @if ($enquiry_setting && $enquiry_main && isset($unit->enquiry))
                                                                  <br>{{ ui_change('Period_From', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($unit->enquiry?->period_from)->format('d-m-Y') }}
                                                                <br>{{ ui_change('Period_to', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($unit->enquiry?->period_to)->format('d-m-Y') }}
                                                            @endif
                                                            {{-- @if (isset($unit->enquiry->period_from) && isset($unit->enquiry->period_to))
                                                                <br>{{ ui_change('Period', 'property_transaction') }} :
                                                                {{ \Carbon\Carbon::parse($unit->enquiry->period_from)->format('Y-F-d') }}
                                                            @endif --}}
                                                            <br>
                                                            @php
                                                                $periodFrom = optional($unit->enquiry)->period_from;
                                                            @endphp


                                                        </div>
                                                    @elseif ($unit->booking_status == 'proposal')
                                                        <div class="unit-tooltip text-start" style="text-align: left;">
                                                            <strong>{{ ui_change('Proposal_Ref', 'property_transaction') }}
                                                                :
                                                                {{ optional(optional($unit->proposal_main)->proposal)->proposal_no }}</strong><br>
                                                            {{ ui_change('Proposal_Date', 'property_transaction') }} :
                                                            {{ optional(optional($unit->proposal_main)->proposal)->proposal_date }}<br>
                                                            {{ ui_change('Rent_Mode', 'property_transaction') }} :
                                                            @php
                                                                $rentModeProposal = optional($unit->proposal_main)
                                                                    ->rent_mode;
                                                                $proposal_setting = optional(
                                                                    App\Models\BusinessSetting::whereType(
                                                                        'proposal_expire_date',
                                                                    )->first(),
                                                                )->value;
                                                                $proposal_main = optional(
                                                                    optional($unit->proposal_main)->proposal,
                                                                );
                                                            @endphp

                                                            {{ $rentModeProposal && array_key_exists($rentModeProposal, $paymentModes) ? $paymentModes[$rentModeProposal] : '' }}

                                                            <br>
                                                            {{ ui_change('Rent_Amount', 'property_transaction') }} :
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->proposal_main)->rent_amount }}
                                                            (BHD)<br>
                                                            {{ ui_change('Contact_Number', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->contact_no }}<br>
                                                            {{ ui_change('Tenant', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}
                                                            @if ($proposal_setting && $proposal_main)
                                                                <br>{{ ui_change('Period_From', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($proposal_main->proposal_details->period_from)->format('d-m-Y') }}
                                                                <br>{{ ui_change('Period_to', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($proposal_main->proposal_details->period_to)->format('d-m-Y') }}
                                                            @endif
                                                        </div>
                                                    @elseif ($unit->booking_status == 'booking')
                                                        <div class="unit-tooltip" style="text-align: left;">
                                                            <strong>{{ ui_change('Booking_Ref', 'property_transaction') }}
                                                                :
                                                                {{ optional(optional($unit->booking_main)->booking)->booking_no }}</strong><br>
                                                            {{ ui_change('Booking_Date', 'property_transaction') }} :
                                                            {{ optional(optional($unit->booking_main)->booking)->booking_date }}<br>
                                                            {{ ui_change('Rent_Mode', 'property_transaction') }} :
                                                            @php
                                                                $rentModeBooking = optional($unit->booking_main)
                                                                    ->rent_mode;
                                                                $booking_setting = optional(
                                                                    App\Models\BusinessSetting::whereType(
                                                                        'booking_expire_date',
                                                                    )->first(),
                                                                )->value;
                                                                $booking_main = optional(
                                                                    optional($unit->booking_main)->booking,
                                                                );
                                                            @endphp
                                                            {{ $rentModeBooking && array_key_exists($rentModeBooking, $paymentModes) ? $paymentModes[$rentModeBooking] : '' }}
                                                            <br>
                                                            {{ ui_change('Rent_Amount', 'property_transaction') }} :
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->booking_main)->rent_amount }}
                                                            (BHD)<br>
                                                            {{ ui_change('Contact_Number', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->contact_no }}<br>
                                                            {{ ui_change('Tenant', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}
                                                            @if ($booking_setting && $booking_main)
                                                                <br>{{ ui_change('Period_From', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($booking_main->booking_details->period_from)->format('d-m-Y') }}
                                                                <br>{{ ui_change('Period_to', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($booking_main->booking_details->period_to)->format('d-m-Y') }}
                                                            @endif
                                                        </div>
                                                    @elseif ($unit->booking_status == 'agreement')
                                                        <div class="unit-tooltip" style="text-align: left;">
                                                            <strong>{{ ui_change('Agreement_Ref', 'property_transaction') }}
                                                                :
                                                                {{ optional(optional($unit->agreement_main)->agreement)->agreement_no }}</strong><br>
                                                            {{ ui_change('Agreement_Date', 'property_transaction') }} :
                                                            {{ optional(optional($unit->agreement_main)->agreement)->agreement_date }}<br>
                                                            {{ ui_change('Rent_Mode', 'property_transaction') }} :
                                                            @php
                                                                $rentMode = optional($unit->agreement_main)->rent_mode;
                                                                $agreement_setting = optional(
                                                                    App\Models\BusinessSetting::whereType(
                                                                        'agreement_expire_date',
                                                                    )->first(),
                                                                )->value;
                                                                $agreement_main = optional(
                                                                    optional($unit->agreement_main)->agreement,
                                                                );
                                                            @endphp

                                                            {{ $rentMode && array_key_exists($rentMode, $paymentModes) ? $paymentModes[$rentMode] : '' }}
                                                            <br>
                                                            {{ ui_change('Rent_Amount', 'property_transaction') }} :
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->agreement_main)->rent_amount }}
                                                            (BHD)<br>
                                                            {{ ui_change('Contact_Number', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->contact_no }}<br>
                                                            {{ ui_change('Tenant', 'property_transaction') }} :
                                                            {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}

                                                            @if ($agreement_setting && $agreement_main)
                                                                 <br>{{ ui_change('Period_From', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($agreement_main->agreement_details->period_from)->format('d-m-Y') }}
                                                                <br>{{ ui_change('Period_to', 'property_transaction') }}
                                                                :
                                                                {{ \Carbon\Carbon::parse($agreement_main->agreement_details->period_to)->format('d-m-Y') }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if ($unit->unit_management_main->name)
                                                        {{ $unit->unit_management_main->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>
                                                <td width="50">
                                                    @if ($unit->property_unit_management->name)
                                                        {{ $unit->property_unit_management->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>


                                                <td width="50">
                                                    @if ($unit->block_unit_management->block->name)
                                                        {{ $unit->block_unit_management->block->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>

                                                <td width="50">
                                                    @if ($unit->floor_unit_management->floor_management_main->name)
                                                        {{ $unit->floor_unit_management->floor_management_main->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif

                                                </td>
                                                <td width="50">
                                                    @if ($unit->unit_description)
                                                        {{ $unit->unit_description->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>
                                                <td width="50">
                                                    @if ($unit->unit_condition)
                                                        {{ $unit->unit_condition->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>
                                                <td width="50">
                                                    @if ($unit->unit_type)
                                                        {{ $unit->unit_type->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>
                                                <td width="50">
                                                    @if ($unit->view)
                                                        {{ $unit->view->name }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>
                                                <td width="50">
                                                    @if ($unit->latest_rent_schedule)
                                                        {{ number_format($unit->latest_rent_schedule->rent_amount, $company->decimals) }}
                                                    @else
                                                        <span
                                                            class="text-red">{{ ui_change('Not_Available', 'property_transaction') }}</span>
                                                    @endif
                                                </td>



                                                <td width="50">
                                                    {{ isset($unit->booking_status) && ($unit->booking_status == 'empty' || $unit->booking_status == null) ? ui_change('Vacant', 'property_transaction') : $unit->booking_status }}
                                                </td>
                                            
                                                <td width="50">
                                                    <a href="#"
                                                        class="btn btn--primary
                                                     show-booking-info"
                                                        title="{{ ui_change('show_info', 'property_report') }}">
                                                        {{ ui_change('Show Info', 'property_report') }}
                                                    </a>

                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="table-responsive mt-4">
                            <div class="px-4 d-flex justify-content-lg-end">
                                <!-- Pagination -->
                                {{ $units->links() }}
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ ui_change('filter', 'property_transaction') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        @if ($last == 'general_check_property')
                            <div class="col-lg-12">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label for="">Building</label>
                                            <select name="report_building" id="report_building"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($buildings as $buildings_filter)
                                                    <option value="{{ $buildings_filter->id }}">
                                                        {{ $buildings_filter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('block', 'property_transaction') }}</label>
                                            <select name="report_block" id="report_block"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($blocks as $block_filter)
                                                    <option value="{{ $block_filter->id }}">
                                                        {{ $block_filter->block->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('floor', 'property_transaction') }}</label>
                                            <select name="report_floor" id="report_floor"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($floors as $floors_filter)
                                                    <option value="{{ $floors_filter->id }}">
                                                        {{ $floors_filter->floor_management_main->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">

                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('unit_description', 'property_transaction') }}</label>
                                            <select name="report_unit_description" id="report_unit_description"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($unit_descriptions as $unit_descriptions_filter)
                                                    <option value="{{ $unit_descriptions_filter->id }}">
                                                        {{ $unit_descriptions_filter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('unit_condition', 'property_transaction') }}</label>
                                            <select name="report_unit_condition" id="report_unit_condition"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}
                                                </option>
                                                @foreach ($unit_conditions as $unit_conditions_filter)
                                                    <option value="{{ $unit_conditions_filter->id }}">
                                                        {{ $unit_conditions_filter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('unit_type', 'property_transaction') }}</label>
                                            <select name="report_unit_types" id="report_unit_types"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}</option>
                                                @foreach ($unit_types as $unit_types_filter)
                                                    <option value="{{ $unit_types_filter->id }}">
                                                        {{ $unit_types_filter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label for="">{{ ui_change('view', 'property_transaction') }}</label>
                                            <select name="report_unit_view" id="report_unit_view"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}</option>
                                                @foreach ($unit_views as $unit_views_filter)
                                                    <option value="{{ $unit_views_filter->id }}">
                                                        {{ $unit_views_filter->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('booked_by_tenant', 'property_transaction') }}</label>
                                            <select name="report_tenant" id="report_tenant"
                                                class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('any', 'property_transaction') }}</option>
                                                @foreach ($tenants as $tenant_filter)
                                                    <option value="{{ $tenant_filter->id }}">
                                                        {{ $tenant_filter->name ?? $tenant_filter->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('Booking Status', 'property_transaction') }}</label>
                                            <div class="d-flex flex-column p-2 border rounded">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="-1" id="status_all" checked>
                                                    <label class="form-check-label" for="status_all">
                                                        {{ ui_change('any', 'property_transaction') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="empty" id="status_empty">
                                                    <label class="form-check-label" for="status_empty">
                                                        {{ ui_change('Vacant', 'property_transaction') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="enquiry" id="status_enquiry">
                                                    <label class="form-check-label" for="status_enquiry">
                                                        {{ ui_change('Enquiry', 'property_transaction') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="proposal" id="status_proposal">
                                                    <label class="form-check-label" for="status_proposal">
                                                        {{ ui_change('Proposed', 'property_transaction') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="booking" id="status_booking">
                                                    <label class="form-check-label" for="status_booking">
                                                        {{ ui_change('Booked', 'property_transaction') }}
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="report_status[]" value="agreement" id="status_agreement">
                                                    <label class="form-check-label" for="status_agreement">
                                                        {{ ui_change('Agreement', 'property_transaction') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-6 col-lg-4 col-xl-4">
                                            <label
                                                for="">{{ ui_change('Booking Status', 'property_transaction') }}</label>
                                            <select name="report_status" class="form-control remv_focus">
                                                <option value="-1" selected>
                                                    {{ ui_change('All Status', 'property_transaction') }}</option>
                                                <option value="empty">{{ ui_change('Vacant', 'property_transaction') }}
                                                </option>
                                                <option value="enquiry">
                                                    {{ ui_change('Enquiry', 'property_transaction') }}</option>
                                                <option value="proposal">
                                                    {{ ui_change('Proposed', 'property_transaction') }}</option>
                                                <option value="booking">
                                                    {{ ui_change('Booked', 'property_transaction') }}</option>
                                                <option value="agreement">
                                                    {{ ui_change('Agreement', 'property_transaction') }}</option>

                                            </select>
                                        </div> --}}
                                        <div class="modal-footer">
                                            <button type="submit" name="bulk_action_btn" class="btn btn--primary"
                                                value="filter">
                                                {{ ui_change('filter', 'property_transaction') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- show booking information -->
    <div class="modal fade" id="show_booking_info_model" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0">{{ ui_change('Unit Information', 'property_report') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width: 40%; font-weight:600;">{{ ui_change('Field', 'property_report') }}
                                    </th>
                                    <th style="font-weight:600;">{{ ui_change('Value', 'property_report') }}</th>
                                </tr>
                            </thead>
                            <tbody id="service_master_item" class="text-center">
                       
                                <tr>
                                    <td>Unit Name</td>
                                    <td>Villa 12</td>
                                </tr>
                                -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                        {{ ui_change('Close', 'property_report') }}
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
    <script>
        function setFormAction(actionUrl) {
            document.getElementById('productForm').action = actionUrl;
        }
        $('select[name=report_building]').on('change', function() {
            var property_id = $(this).val();
            if (property_id) {
                $.ajax({ // blocks
                    url: "{{ route('search_master') }}",
                    type: "GET",
                    data: {
                        'building': property_id,
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data) {
                            $('select[name="report_block"]').empty();

                            $('select[name="report_block"]').append(
                                `<option value="">{{ ui_change('any', 'property_transaction') }}</option>`
                            );

                            data.blocks.forEach(function(block_main) {
                                $('select[name="report_block"]').append(
                                    `<option value="${block_main.id}">${block_main.block.name}</option>`
                                );
                            });


                        } else {}
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred:', error);
                    }
                });
            }
        })
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const checkboxes = document.querySelectorAll(".check_bulk_item");
            const bulkCheckAll = document.querySelector(".bulk_check_all");
            const createEnquiryButton = document.querySelector(".createButton");

            form.addEventListener("submit", function(event) {
                if (event.submitter === createEnquiryButton) {
                    const checkedItems = document.querySelectorAll(".check_bulk_item:checked").length;

                    if (checkedItems === 0) {
                        event.preventDefault();
                        alert("Please Select Unit");
                    }
                }
            });

            bulkCheckAll.addEventListener("change", function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    if (!this.checked) {
                        bulkCheckAll.checked = false;
                    }
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '#show_booking_info', function(e) {
            e.preventDefault();
            $('#show_booking_info_model').modal('show');
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.show-booking-info', function(e) {
                e.preventDefault();

                let bookingInfo = $(this).closest('tr').find('.unit-tooltip').html();

                if (bookingInfo && bookingInfo.trim() !== '') {
                    let rowsHtml = '';
                    bookingInfo.split('<br>').forEach(line => {
                        line = line.trim();
                        if (line) {
                            let parts = line.split(':');
                            let key = parts[0]?.trim() || '';
                            let value = parts[1]?.trim() || '';
                            if (key && value) {
                                rowsHtml += `
                            <tr>
                                <td style="width: 40%; font-weight: 600; text-align: left;">${key}</td>
                                <td style="text-align: center;">${value}</td>
                            </tr>
                        `;
                            }
                        }
                    });

                    if (!rowsHtml) {
                        rowsHtml = `
                    <tr>
                        <td colspan="2" class="text-center text-danger">
                            {{ ui_change('No valid information available', 'property_report') }}
                        </td>
                    </tr>`;
                    }

                    $('#service_master_item').html(rowsHtml);
                } else {
                    $('#service_master_item').html(`
                <tr>
                    <td colspan="2" class="text-center text-danger">
                        {{ ui_change('No information available', 'property_report') }}
                    </td>
                </tr>
            `);
                }

                $('#show_booking_info_model').modal('show');
            });
        });
    </script>

    {{-- <script>
        $(document).ready(function() { 
            $(document).on('click', '.show-booking-info', function(e) {
                e.preventDefault(); 
                let bookingInfo = $(this).closest('tr').find('.unit-tooltip').html(); 
                if (bookingInfo && bookingInfo.trim() !== '') { 
                    $('#service_master_item').html(`
                <td colspan="3">
                    <div style="text-align: center;">${bookingInfo}</div>
                </td>
            `);
                } else {
                    $('#service_master_item').html(`
                <td colspan="3" class="text-center text-danger">
                    {{ ui_change('No information available', 'property_report') }}
                </td>
            `);
                }

                $('#show_booking_info_model').modal('show');
            });
        });
    </script> --}}
@endpush
