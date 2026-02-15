@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title',  ui_change('list_view' , 'property_transaction') )
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
            color: #6ab0c9;
        }


        .unit.proposed {
            color: #ffeb3b;
        }

        .unit.booked {
            color: #d500f9;
            /* color: #fff; */
        }

        .unit.agreement {
            color: #f44336;
            /* color: #fff; */
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

        .unit.empty {
            color: black;
        }

        .unit.enquiry {
            color: #372be2;
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
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                {{  ui_change('property' , 'property_transaction')  }}
            </h2>
        </div>




        <div class="row  @if ($lang == 'ar') rtl text-start @else ltr @endif">
            <div class="d-flex flex-wrap mb-3">
                <span class="badge bg-primary me-2 m-2 p-2" style="color:black">{{ ui_change('Property,_Block_and_Floors_Listing' , 'property_transaction') }}</span>
                <span class="badge bg-success me-2 m-2 p-2" style="color:black">{{ ui_change('Floor_Wise_Unit_Count' , 'property_transaction') }}</span>
                <span class="badge bg-warning me-2 m-2 p-2" style="color:black">{{ ui_change('Total_Unit' , 'property_transaction') }}</span>
                <span class="badge  me-2 m-2 p-2" style="color:black;background-color: #ffeb3b;">{{ ui_change('Proposed_Unit' , 'property_transaction') }}</span>
                <span class="badge   me-2 m-2 p-2" style="color:black;background-color: #d500f9;">{{ ui_change('Booked_Unit' , 'property_transaction') }}</span>
                <span class="badge   me-2 m-2 p-2" style="color:black;background-color: #f44336;">{{ ui_change('Agreement_Unit' , 'property_transaction') }}</span>
                <span class="badge  me-2 m-2 p-2" style="background-color: #372be2;color:white"> {{ ui_change('Proposal_Pending' , 'property_transaction') }}</span>

            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr class="header">
                            <th>{{  ui_change('Property-Block-Floor' , 'property_transaction')  }}</th>
                            <th>{{  ui_change('Count_Of_Units' , 'property_transaction')  }}</th>
                            <th>{{  ui_change('Total_Units' , 'property_transaction')  }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($property_item->blocks_management_child as $block_item)
                            @foreach ($block_item->floors_management_child as $floor_item)
                                <tr class="striped-row">


                                    <td>{{ $property_item->code . '-' . $block_item->block->name . '-' . $floor_item->floor_management_main->name }}
                                    </td>
                                    <td class="count-units">{{ $floor_item->unit_management_child->count() }}</td>
                                    <td class="total-units">
                                        @foreach ($floor_item->unit_management_child as $unit)
                                            @if ($unit->booking_status == 'enquiry')
                                                <div style="background-color: #372be2;color:white"
                                                    class="unit  hover-info   p-1 border border-gray-300">
                                                    {{ $unit->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->company_name }}
                                                        <br>
                                                        {{ optional(optional($unit->enquiry)->main_enquiry)->enquiry_no }}
                                                        <br>
                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->main_enquiry)->rent_amount }}
                                                        <br>
                                                        {{ optional(optional(optional($unit->enquiry)->main_enquiry)->tenant)->contact_no }}

                                                    </div>
                                                </div>
                                            @elseif($unit->booking_status == 'proposal')
                                                <div class="unit  hover-info bg-danger  p-1 border border-gray-300">
                                                    {{ $unit->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit->proposal_main)->proposal)->tenant)->company_name }}
                                                        <br>
                                                        {{ optional(optional($unit->proposal_main)->proposal)->proposal_no }}
                                                        <br>
                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->proposal_main)->rent_amount }}
                                                        <br>
                                                        {{ optional(optional(optional($unit->proposal_main)->proposal)->tenant)->contact_no }}
                                                    </div>
                                                </div>
                                            @elseif($unit->booking_status == 'booking')
                                                <div class="unit  hover-info bg-success  p-1 border border-gray-300">
                                                    {{ $unit->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit->booking_main)->booking)->tenant)->company_name }}
                                                        <br>
                                                        {{ optional(optional($unit->booking_main)->booking)->booking_no }}
                                                        <br>
                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->booking_main)->rent_amount }}
                                                        <br>
                                                        {{ optional(optional(optional($unit->booking_main)->booking)->tenant)->contact_no }}
                                                    </div>
                                                </div>
                                            @elseif($unit->booking_status == 'agreement')
                                                <div class="unit  hover-info bg-secondary p-1 border border-gray-300">
                                                    {{ $unit->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit->agreement_main)->agreement)->tenant)->company_name }}
                                                        <br>
                                                        {{ optional(optional($unit->agreement_main)->agreement)->agreement_no }}
                                                        <br>
                                                        {{ optional($unit->rent_schedules->first())->rent_amount ?? optional($unit->agreement_main)->rent_amount }}
                                                        <br>
                                                        {{ optional(optional(optional($unit->agreement_main)->agreement)->tenant)->contact_no }}
                                                    </div>
                                                </div>
                                            @elseif($unit->booking_status == 'empty')
                                                <div style="background-color:#fff;"
                                                    class="unit  hover-info empty  p-1 border border-gray-300">
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
                        {{-- @foreach ($property_item->blocks_management_child as $block_item)
                        @foreach ($block_item->floors_management_child as $floor_item)
                        <tr class="striped-row">


                            <td>{{ $property_item->code .'-'.$block_item->block->name . '-' . $floor_item->floor_management_main->name }}</td>
                            <td class="count-units">{{ $floor_item->unit_management_child->count() }}</td>
                            <td class="total-units">
                            @foreach ($floor_item->unit_management_child as $unit)
                                <div class="unit @if ($unit->booking_status == 'empty') empty
                                    @elseif( $unit->booking_status == 'proposal') proposed
                                    @elseif( $unit->booking_status == 'booking') booked
                                    @elseif($unit->booking_status == 'agreement') agreement @endif" >{{ $unit->unit_management_main->name }},</div>

                            @endforeach
                            @endforeach
                        </td>
                        </tr>
                        @endforeach --}}
                        {{-- <tr class="striped-row">
                            <td>abc-Block A-FLR002</td>
                            <td class="count-units">0</td>
                            <td class="total-units"></td>
                        </tr>
                        <tr class="striped-row">
                            <td>abc-Block A-FLR003</td>
                            <td class="count-units">0</td>
                            <td class="total-units"></td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
