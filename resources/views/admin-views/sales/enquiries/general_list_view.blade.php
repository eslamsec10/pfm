@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title', ui_change('list_view', 'property_transaction'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <style>
        .custom-table {
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table th,
        .custom-table td {
            border: 2px solid black;
            padding: 10px;
            text-align: left;
        }

        .header {
            background-color: #2c7da0;
            color: white;
            font-weight: bold;
        }

        .count-units {
            background-color: #4caf50;
            color: white;
        }

        .total-units {
            background-color: #d4af37;
            color: black;
        }

        .striped-row:nth-child(even) {
            background-color: #6ab0c9;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }

            100% {
                opacity: 1;
            }
        }

        .empty {
            animation: blink 3s infinite;
            font-weight: bold;
        }

        .hover-info {
            position: relative;
            display: inline-block;
        }

        .hover-info .info-box {
            display: none;
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
            white-space: nowrap;
            z-index: 999;
        }

        .hover-info:hover .info-box {
            display: block;
        }

        .unit-hover-box {
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: #0b0f14;
            color: #fff;
            padding: 12px 14px;
            border-radius: 8px;
            min-width: 220px;
            font-size: 13px;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 99999 !important;

        }

        .unit-hover-box.dark {
            background: #0b0f14;
        }

        .unit-hover-box.dark::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 8px 8px 0;
            border-style: solid;
            border-color: #0b0f14 transparent transparent;
        }

        .hover-info:hover .unit-hover-box {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .unit-hover-box .title {
            font-weight: 700;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #2a2f35;
        }

        .unit-hover-box .info div {
            margin: 4px 0;
        }

        .unit-hover-box .info span {
            color: #4fc3f7;
        }

        .count-units.count-column {
            width: 60px;
            text-align: center;
            white-space: nowrap;
            font-size: 12px;
            padding: 0 !important;
            margin: 0 !important;
        }

        .unit.selected {
            border: 2px solid #007bff !important;
            background-color: rgba(0, 123, 255, 0.2) !important;
        }

        .unit.selected .checkmark {
            display: block !important;
        }

        .table-responsive {
            overflow: visible !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                {{-- <img width="60" src="{{ asset('/assets/back-end/img/property.jpg') }}" alt=""> --}}
                {{ ui_change('property', 'property_transaction') }}
            </h2>
        </div>
        @include('admin-views.inline_menu.property_transaction.inline-menu')


        <form id="productForm" method="get" class="d-flex flex-wrap gap-2">
            <button type="submit" onclick="setFormAction('{{ route('enquiry.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i>
                <span class="text">{{ ui_change('create_enquiry', 'property_transaction') }}</span>
            </button>

            <button type="submit" onclick="setFormAction('{{ route('proposal.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i>
                <span class="text">{{ ui_change('create_proposal', 'property_transaction') }}</span>
            </button>

            <button type="submit" onclick="setFormAction('{{ route('booking.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i>
                <span class="text">{{ ui_change('create_booking', 'property_transaction') }}</span>
            </button>

            <button type="submit" onclick="setFormAction('{{ route('agreement.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i>
                <span class="text">{{ ui_change('create_agreement', 'property_transaction') }}</span>
            </button>
    </div>
    <div class="content container-fluid">

        <div class="row  @if ($lang == 'ar') rtl text-start @else ltr @endif">
            <div class="d-flex flex-wrap mb-3">
                <span class="badge bg-primary me-2 m-2 p-2"
                    style="color:black">{{ ui_change('Property,_Block_and_Floors_Listing', 'property_transaction') }}</span>
                <span class="badge bg-success me-2 m-2 p-2"
                    style="color:black">{{ ui_change('Floor_Wise_Unit_Count', 'property_transaction') }}</span>
                <span class="badge bg-warning me-2 m-2 p-2"
                    style="color:black">{{ ui_change('Total_Unit', 'property_transaction') }}</span>
                <span class="badge   me-2 m-2 p-2"
                    style="background-color: {{ $settings['proposal_color']->value ?? '#ffeb3b' }};color:white"
                    style="color:black">{{ ui_change('Proposed_Unit', 'property_transaction') }}</span>
                <span class="badge  me-2 m-2 p-2"
                    style="background-color: {{ $settings['booking_color']->value ?? '#d500f9' }};color:white"
                    style="color:black">{{ ui_change('Booked_Unit', 'property_transaction') }}</span>
                <span class="badge me-2 m-2 p-2"
                    style="background-color: {{ $settings['agreement_color']->value ?? '#f44336' }};color:white"
                    style="color:black">{{ ui_change('Agreement_Unit', 'property_transaction') }}</span>
                <span class="badge  me-2 m-2 p-2"
                    style="background-color: {{ $settings['enquiry_color']->value ?? '#372be2' }};color:white">
                    {{ ui_change('Proposal_Pending', 'property_transaction') }}</span>


            </div>
            @foreach ($property_items as $property_item)
                <div class="table-responsive mt-3">
                    <table class="custom-table">
                        <thead>
                            <tr class="header">
                                <th>{{ ui_change('Property-Block-Floor', 'property_transaction') }}</th>
                                <th class="count-column">{{ ui_change('Count_Of_Units', 'property_transaction') }}</th>
                                <th>{{ ui_change('Total_Units', 'property_transaction') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($property_item->blocks_management_child as $block_item)
                                @foreach ($block_item->floors_management_child as $floor_item)
                                    <tr class="striped-row">
                                        <td>{{ $property_item->code . '-' . $block_item->block->name . '-' . $floor_item->floor_management_main->name }}
                                        </td>
                                        <td class="count-units count-column text-center ">
                                            {{ $floor_item->unit_management_child->count() }}</td>
                                        <td class="total-units">
                                            @foreach ($floor_item->unit_management_child->sortBy('position') as $unit)
                                                @if ($unit->booking_status == 'enquiry')
                                                    @php
                                                        $periodFrom = optional($unit->enquiry)->period_from;
                                                        $enquiry_selected = false;
                                                    @endphp
                                                    @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                        <input type="checkbox" name="bulk_ids[]"
                                                            value="{{ $unit->id }}" style="display:none;"
                                                            class="unit-checkbox check_bulk_item check_bulk_item">
                                                        @php
                                                            $enquiry_selected = true;
                                                        @endphp
                                                    @endif
                                                    <div style="background-color: {{ $settings['enquiry_color']->value ?? '#372be2' }};color:white"
                                                        class="unit  hover-info   p-1 border border-gray-300"
                                                        @if ($enquiry_selected) style="background-color:#fff; position:relative; display:inline-block; margin:2px; cursor:pointer; min-width:80px; text-align:center;"
                                                        data-unit-id="{{ $unit->id }}" @endif>
                                                        {{ $unit->unit_management_main?->name }}
                                                        <div class="unit-hover-box dark">
                                                            <div class="title">
                                                                {{ ui_change('Unit_Info') }}
                                                            </div>

                                                            <div class="info">
                                                                <div>
                                                                    {{ ui_change('tenant') }} :
                                                                    <span>{{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->name ??
                                                                        optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->company_name }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Enquiry_No') }} : <span>
                                                                        {{ optional(optional($unit->enquiry)->main_enquiry)->enquiry_no }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Rent_Amount') }} : <span>
                                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->main_enquiry)->rent_amount }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Contact_No') }} : <span>
                                                                        {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->contact_no }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    {{ ui_change('Period_From') }} :
                                                                    <span>
                                                                        {{ optional($unit->enquiry)->period_from
                                                                            ? \Carbon\Carbon::parse($unit->enquiry->period_from)->format('d/m/Y')
                                                                            : '' }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Period_To') }} :
                                                                    <span>
                                                                        {{ optional($unit->enquiry)->period_to ? \Carbon\Carbon::parse($unit->enquiry->period_to)->format('d/m/Y') : '' }}
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>


                                                        {{-- <div class="info-box">
                                                        {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->company_name }}
                                                        <br>
                                                        {{ optional(optional($unit->enquiry)->main_enquiry)->enquiry_no }}
                                                        <br>
                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->main_enquiry)->rent_amount }}
                                                        <br>
                                                        {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->contact_no }}

                                                    </div> --}}
                                                    </div>
                                                @elseif($unit->booking_status == 'proposal')
                                                    @php
                                                        $periodFrom = optional($unit->proposal_main)->commencement_date;
                                                        $proposal_selected = false;
                                                    @endphp
                                                    @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                        <input type="checkbox" name="bulk_ids[]"
                                                            class="unit-checkbox check_bulk_item check_bulk_item"
                                                            value="{{ $unit->id }}" style="display:none;">
                                                        @php
                                                            $proposal_selected = true;
                                                        @endphp
                                                    @endif
                                                    <div class="unit  hover-info    p-1 border border-gray-300"
                                                        style="background-color: {{ $settings['proposal_color']->value ?? '#ffeb3b' }};color:white"
                                                        @if ($proposal_selected) style="background-color:#fff; position:relative; display:inline-block; margin:2px; cursor:pointer; min-width:80px; text-align:center;"
                                                        data-unit-id="{{ $unit->id }}" @endif>
                                                        {{ $unit->unit_management_main->name }}
                                                        <div class="unit-hover-box dark">
                                                            <div class="title">
                                                                {{ ui_change('Unit_Info') }}
                                                            </div>
                                                            <div class="info">
                                                                <div>
                                                                    {{ ui_change('tenant') }} :
                                                                    <span>{{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->name ??
                                                                        optional(optional(optional($unit->proposal_main)->proposal)->tenant)->company_name }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Booking_No') }} : <span>
                                                                        {{ optional(optional($unit->proposal_main)->proposal)->proposal_no }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Rent_Amount') }} : <span>
                                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->proposal_main)->rent_amount }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Contact_No') }} : <span>
                                                                        {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->contact_no }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    {{ ui_change('Period_From') }} :
                                                                    <span>
                                                                        {{ optional($unit->proposal_main)->commencement_date
                                                                            ? \Carbon\Carbon::parse($unit->proposal_main->commencement_date)->format('d/m/Y')
                                                                            : '' }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Period_To') }} :
                                                                    <span>
                                                                        {{ optional($unit->proposal_main)->expiry_date ? \Carbon\Carbon::parse($unit->proposal_main->expiry_date)->format('d/m/Y') : '' }}
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>
{{-- 
                                                        <div class="info-box">
                                                            {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit->proposal_main)->proposal)->tenant)->company_name }}
                                                            <br>
                                                            {{ optional(optional($unit->proposal_main)->proposal)->proposal_no }}
                                                            <br>
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->proposal_main)->rent_amount }}
                                                            <br>
                                                            {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->contact_no }}
                                                        </div> --}}
                                                    </div>
                                                @elseif($unit->booking_status == 'booking')
                                                    @php
                                                        $periodFrom = optional($unit->booking_main)->commencement_date;
                                                        $booking_selected = false;
                                                    @endphp
                                                    @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                        <input type="checkbox" name="bulk_ids[]"
                                                            value="{{ $unit->id }}" style="display:none;"
                                                            class="unit-checkbox check_bulk_item check_bulk_item">
                                                        @php
                                                            $booking_selected = true;
                                                        @endphp
                                                    @endif
                                                    <div class="unit  hover-info  p-1 border border-gray-300"
                                                        style="background-color: {{ $settings['booking_color']->value ?? '#d500f9' }};color:white"
                                                        @if ($booking_selected) style="background-color:#fff; position:relative; display:inline-block; margin:2px; cursor:pointer; min-width:80px; text-align:center;"
                                                        data-unit-id="{{ $unit->id }}" @endif>

                                                        {{ $unit->unit_management_main->name }}
                                                        <div class="unit-hover-box dark">
                                                            <div class="title">
                                                                {{ ui_change('Unit_Info') }}
                                                            </div>
                                                            <div class="info">
                                                                <div>
                                                                    {{ ui_change('tenant') }} :
                                                                    <span>{{ optional(optional(optional($unit->booking_main)->booking)->tenant)->name ??
                                                                        optional(optional(optional($unit->booking_main)->booking)->tenant)->company_name }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Booking_No') }} : <span>
                                                                        {{ optional(optional($unit->booking_main)->booking)->booking_no }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Rent_Amount') }} : <span>
                                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->booking_main)->rent_amount }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Contact_No') }} : <span>
                                                                        {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->contact_no }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    {{ ui_change('Period_From') }} :
                                                                    <span>
                                                                        {{ optional($unit->booking_main)->commencement_date
                                                                            ? \Carbon\Carbon::parse($unit->booking_main->commencement_date)->format('d/m/Y')
                                                                            : '' }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Period_To') }} :
                                                                    <span>
                                                                        {{ optional($unit->booking_main)->expiry_date ? \Carbon\Carbon::parse($unit->booking_main->expiry_date)->format('d/m/Y') : '' }}
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        {{-- <div class="info-box">
                                                            {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit->booking_main)->booking)->tenant)->company_name }}
                                                            <br>
                                                            {{ optional(optional($unit->booking_main)->booking)->booking_no }}
                                                            <br>
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->booking_main)->rent_amount }}
                                                            <br>
                                                            {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->contact_no }}
                                                        </div> --}}
                                                    </div>
                                                @elseif($unit->booking_status == 'agreement')
                                                    @php
                                                        $periodFrom = optional($unit->agreement_main)
                                                            ->commencement_date;
                                                        $agreement_selected = false;
                                                    @endphp
                                                    @if ($periodFrom && Carbon\Carbon::parse($periodFrom)->gt(Carbon\Carbon::today()))
                                                        <input type="checkbox" name="bulk_ids[]"
                                                            value="{{ $unit->id }}" style="display:none;"
                                                            class="unit-checkbox check_bulk_item check_bulk_item">
                                                        @php
                                                            $agreement_selected = true;
                                                        @endphp
                                                    @endif
                                                    <div class="unit  hover-info  p-1 border border-gray-300"
                                                        style="background-color: {{ $settings['agreement_color']->value ?? '#f44336' }};color:white"
                                                        @if ($agreement_selected) style="background-color:#fff; position:relative; display:inline-block; margin:2px; cursor:pointer; min-width:80px; text-align:center;"
                                                        data-unit-id="{{ $unit->id }}" @endif>
                                                        {{ $unit->unit_management_main->name }}
                                                        <div class="unit-hover-box dark">
                                                            <div class="title">
                                                                {{ ui_change('Unit_Info') }}
                                                            </div>
                                                            <div class="info">
                                                                <div>
                                                                    {{ ui_change('tenant') }} :
                                                                    <span>{{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->name ??
                                                                        optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Booking_No') }} : <span>
                                                                        {{ optional(optional($unit->agreement_main)->agreement)->agreement_no }}</span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Rent_Amount') }} : <span>
                                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->agreement_main)->rent_amount }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Contact_No') }} : <span>
                                                                        {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->contact_no }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    {{ ui_change('Period_From') }} :
                                                                    <span>
                                                                        {{ optional($unit->agreement_main)->commencement_date
                                                                            ? \Carbon\Carbon::parse($unit->agreement_main->commencement_date)->format('d/m/Y')
                                                                            : '' }}
                                                                    </span>
                                                                </div>

                                                                <div>
                                                                    {{ ui_change('Period_To') }} :
                                                                    <span>
                                                                        {{ optional($unit->agreement_main)->expiry_date ? \Carbon\Carbon::parse($unit->agreement_main->expiry_date)->format('d/m/Y') : '' }}
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        {{-- <div class="info-box">
                                                            {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}
                                                            <br>
                                                            {{ optional(optional($unit->agreement_main)->agreement)->agreement_no }}
                                                            <br>
                                                            {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->agreement_main)->rent_amount }}
                                                            <br>
                                                            {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->contact_no }}
                                                        </div> --}}
                                                    </div>
                                                @elseif($unit->booking_status == 'empty')
                                                    <div class="unit hover-info empty p-1 border border-gray-300"
                                                        style="background-color:#fff; position:relative; display:inline-block; margin:2px; cursor:pointer; min-width:80px; text-align:center;"
                                                        data-unit-id="{{ $unit->id }}">
                                                        <input type="checkbox" name="bulk_ids[]"
                                                            value="{{ $unit->id }}" style="display:none;"
                                                            class="unit-checkbox check_bulk_item check_bulk_item">
                                                        {{ $unit->unit_management_main->name }}
                                                        <div class="info-box">
                                                            {{ optional($unit->rent_schedules->first())->rent_amount }}
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                @endforeach
                                </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            @endforeach

        </div>
    </div>
@endsection

@push('script')
    <script>
        function setFormAction(actionUrl) {
            document.getElementById('productForm').action = actionUrl;
        }
        document.addEventListener("DOMContentLoaded", function() {

            const form = document.querySelector("form");
            const checkboxes = document.querySelectorAll(".unit-checkbox");
            const bulkCheckAll = document.querySelector(".bulk_check_all");
            const createEnquiryButton = document.querySelector(".createButton");

            form.addEventListener("submit", function(event) {
                if (event.submitter === createEnquiryButton) {

                    const checkedItems = document.querySelectorAll(".unit-checkbox:checked").length;

                    if (checkedItems === 0) {
                        event.preventDefault();
                        alert("Please Select Unit");
                    }
                }
            });

            if (bulkCheckAll) {
                bulkCheckAll.addEventListener("change", function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    if (bulkCheckAll && !this.checked) {
                        bulkCheckAll.checked = false;
                    }
                });
            });

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const unitDivs = document.querySelectorAll(".unit");

            unitDivs.forEach(div => {
                const checkbox = div.querySelector("input[type='checkbox']");

                if (!checkbox) {
                    div.style.cursor = "not-allowed";
                    return;
                }

                div.addEventListener("click", function() {
                    div.classList.toggle("selected");
                    checkbox.checked = div.classList.contains("selected");
                });
            });
        });
    </script>
@endpush
