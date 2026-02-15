@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
    $company = (new App\Models\Company())->setConnection('tenant')->select('decimals')->first();
@endphp
@section('title', ui_change('view_image', 'property_transaction'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <style>
        .legend {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .legend div {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend span {
            width: 20px;
            height: 20px;
            display: inline-block;
            border: 1px solid #000;
        }

        .grid-container {
            margin-bottom: 30px;
        }

        .grid-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(5, 100px);
            gap: 5px;
        }

        .grid .floor {
            background-color: teal;
            color: #fff;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            border: 1px solid #000;
        }

        .grid .unit {
            background-color: #fff;
            text-align: center;
            line-height: 40px;
            border: 1px solid #000;
        }

        .unit.empty {
            background-color: #fff;
        }

        .unit.proposed {
            background-color: #ffeb3b;
        }

        .unit.booked {
            background-color: #d500f9;
            color: #fff;
        }

        .unit.agreement {
            background-color: #f44336;
            color: #fff;
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

        .unit.enquiry {
            background-color: #372be2;
            color: #fff;
        }

        /* new edition */
        .legend {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1000;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }


        .btn-click {
            display: inline-block;
            background-color: white;
            color: blue;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            margin-right: 5px;
        }

        .btn-click:hover {
            background-color: #ddd;
            color: darkblue;
        }

        .click-buttons {
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                {{ ui_change('view_image', 'property_transaction') }}
            </h2>

        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row  @if ($lang == 'ar') rtl text-start @else ltr @endif">

            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="legend">
                            <div><span style="background-color: teal;"></span>
                                {{ ui_change('Floors', 'property_transaction') }}</div>
                            <div><span style="background-color: #fff;"></span>
                                {{ ui_change('Empty_Units', 'property_transaction') }}</div>
                            <div><span style="background-color: #ffeb3b;"></span>
                                {{ ui_change('Proposed_Units', 'property_transaction') }}</div>
                            <div><span style="background-color: #d500f9;"></span>
                                {{ ui_change('Booked_Units', 'property_transaction') }}</div>
                            <div><span style="background-color: #f44336;"></span>
                                {{ ui_change('Agreement_Units', 'property_transaction') }}</div>
                            <div><span style="background-color: #372be2;"></span>
                                {{ ui_change('Proposal_Pending', 'property_transaction') }}</div>

                        </div>
                        {{-- @forelse ($properties as $property_item) --}}
                        <div class="grid-title">
                            <h3 style="color: var(--primary)">{{ $property_item->name }}</h3>
                        </div>
                        <div class="grid-container">
                            @foreach ($property_item->blocks_management_child as $block_item)
                                <div class="grid-title">{{ $block_item->block->name }}</div>
                                @foreach ($block_item->floors_management_child as $floor_item)
                                    <div class="grid">
                                        <div class="floor">{{ $floor_item->floor_management_main->name }}</div>
                                        @foreach ($floor_item->unit_management_child as $unit_item)
                                            @if ($unit_item->booking_status == 'enquiry')
                                                <div style="background-color: #372be2;color:white"
                                                    class="unit  hover-info clickable-unit" onclick="toggleButtons(this)">
                                                    {{ $unit_item->unit_management_main->name }}


                                                    <div class="info-box">
                                                         {{ ui_change('Enquiry_Number', 'property_transaction') }} :
                                                        {{ optional(optional($unit_item->enquiry)->main_enquiry)->enquiry_no }}
                                                        <br>
                                                        {{ ui_change('rent_amount', 'property_transaction') }} :
                                                        {{ number_format(optional($unit_item->enquiry)->rent_amount ?? 0, $company->decimals) ?? optional($unit_item->rent_schedules->first())->rent_amount }}
                                                        <br>
                                                        {{ ui_change('tenant_name', 'property_transaction') }} :
                                                        {{ optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->company_name }}
                                                        <br>
                                                        {{ ui_change('Period', 'property_transaction') }} :
                                                        {{ optional($unit_item->enquiry)->period_from . ' to ' . optional($unit_item->enquiry)->period_to }}
                                                        <br>
                                                    </div>
                                                    @if (isset($unit_item->enquiry->main_enquiry))
                                                        <div class="click-buttons" style="display: none;">
                                                            <a href="{{ route('enquiry.add_to_proposal', optional(optional($unit_item->enquiry)->main_enquiry)->id) }}"
                                                                class="btn-click">{{ ui_change('proposal', 'property_transaction') }}</a>

                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($unit_item->booking_status == 'proposal')
                                                <div style="background-color:#ffeb3b;"
                                                    class="unit  hover-info clickable-unit" onclick="toggleButtons(this)">
                                                    {{ $unit_item->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ ui_change('Proposal_Number', 'property_transaction') }} :
                                                        {{ optional(optional($unit_item->proposal_main)->proposal)->proposal_no }}
                                                        <br>
                                                        {{ ui_change('rent_amount', 'property_transaction') }} :
                                                        {{ number_format(optional($unit_item->proposal_main)->rent_amount ?? 0, $company->decimals) ?? optional($unit_item->rent_schedules->first())->rent_amount }}
                                                        <br>
                                                        {{ ui_change('tenant_name', 'property_transaction') }} :
                                                        {{ optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->company_name }}
                                                        <br>
                                                        {{ ui_change('Period', 'property_transaction') }} :
                                                        {{ optional($unit_item->proposal_main)->commencement_date . ' to ' . optional($unit_item->proposal_main)->expiry_date }}
                                                        <br>
                                                    </div>
                                                    @if (isset($unit_item->proposal_main->proposal))
                                                        <div class="click-buttons" style="display: none;">
                                                            <a href="{{ route('proposal.add_to_booking', optional(optional($unit_item->proposal_main)->proposal)->id) }}"
                                                                class="btn-click">{{ ui_change('booking', 'property_transaction') }}</a>

                                                            <a href="{{ route('proposal.add_to_agreement', optional(optional($unit_item->proposal_main)->proposal)->id) }}"
                                                                class="btn-click">{{ ui_change('agreement', 'property_transaction') }}</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($unit_item->booking_status == 'booking')
                                                <div style="background-color:#d500f9;"
                                                    class="unit  hover-info clickable-unit" onclick="toggleButtons(this)">
                                                    {{ $unit_item->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ ui_change('booking_Number', 'property_transaction') }} :
                                                        {{ optional(optional($unit_item->booking_main)->booking)->booking_no }}
                                                        <br>
                                                        {{ ui_change('rent_amount', 'property_transaction') }} :
                                                        {{ optional($unit_item->booking_main)->rent_amount ?? optional($unit_item->rent_schedules->first())->rent_amount }}
                                                        <br>
                                                        {{ ui_change('tenant_name', 'property_transaction') }} :
                                                        {{ optional(optional(optional($unit_item->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit_item->booking_main)->booking)->tenant)->company_name }}
                                                        <br>
                                                        {{ ui_change('Period', 'property_transaction') }} :
                                                        {{ optional($unit_item->booking_main)->commencement_date . ' to ' . optional($unit_item->booking_main)->expiry_date }}
                                                       
                                                        <br>
                                                    </div>
                                                    @if (isset($unit_item->booking_main->booking))
                                                        <div class="click-buttons" style="display: none;">
                                                            <a href="{{ route('booking.add_to_agreement', optional(optional($unit_item->booking_main)->booking)->id) }}"
                                                                class="btn-click">{{ ui_change('agreement', 'property_transaction') }}</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($unit_item->booking_status == 'agreement')
                                                <div style="background-color:#f44336;" class="unit  hover-info  ">
                                                    {{ $unit_item->unit_management_main->name }}


                                                    <div class="info-box">
                                                        {{ ui_change('agreement_Number', 'property_transaction') }} :
                                                        {{ optional(optional($unit_item->agreement_main)->agreement)->agreement_no }}
                                                        <br>
                                                        {{ ui_change('rent_amount', 'property_transaction') }} :
                                                        {{ optional($unit_item->agreement_main)->rent_amount ?? optional($unit_item->rent_schedules->first())->rent_amount }}
                                                        <br>
                                                        {{ ui_change('tenant_name', 'property_transaction') }} :
                                                        {{ optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->company_name }}
                                                        <br>
                                                        {{ ui_change('Period', 'property_transaction') }} :
                                                        {{ optional($unit_item->agreement_main)->commencement_date . ' to ' . optional($unit_item->agreement_main)->expiry_date }}
                                                        <br>
                                                    </div>
                                                </div>
                                            @elseif($unit_item->booking_status == 'empty')
                                                <div style="background-color:#fff;" class="unit  hover-info empty ">
                                                    {{ $unit_item->unit_management_main->name }}
                                                    <div class="info-box">

                                                        {{ optional($unit_item->rent_schedules->first())->rent_amount }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        {{-- <div class="unit booked">UNIT015</div>
                                <div class="unit empty">UNIT016</div>
                                <div class="unit empty">UNIT017</div>
                                <div class="unit empty">UNIT018</div> --}}
                                    </div>
                                    <hr>
                                @endforeach
                                {{-- <div class="grid">
                                <div class="floor">FLR004</div>
                                <div class="unit booked">UNIT015</div>
                                <div class="unit empty">UNIT016</div>
                                <div class="unit empty">UNIT017</div>
                                <div class="unit empty">UNIT018</div>

                                <div class="floor">FLR003</div>
                                <div class="unit empty">UNIT010</div>
                                <div class="unit empty">UNIT011</div>
                                <div class="unit proposed">UNIT012</div>
                                <div class="unit booked">UNIT013</div>
                                <div class="unit booked">UNIT014</div>

                                <div class="floor">FLR002</div>
                                <div class="unit empty">UNIT006</div>
                                <div class="unit empty">UNIT007</div>
                                <div class="unit empty">UNIT008</div>
                                <div class="unit empty">UNIT009</div>

                                <div class="floor">FLR001</div>
                                <div class="unit empty">UNIT001</div>
                                <div class="unit empty">UNIT002</div>
                                <div class="unit empty">UNIT003</div>
                                <div class="unit empty">UNIT004</div>
                                <div class="unit empty">UNIT005</div>
                            </div> --}}
                            @endforeach

                        </div>
                        <hr>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function toggleButtons(element) {
            const allButtons = document.querySelectorAll('.click-buttons');

            // Hide all others
            allButtons.forEach(btn => {
                if (!element.contains(btn)) {
                    btn.style.display = 'none';
                }
            });

            // Toggle current
            const btns = element.querySelector('.click-buttons');
            if (btns.style.display === 'none' || btns.style.display === '') {
                btns.style.display = 'block';
            } else {
                btns.style.display = 'none';
            }
        }

        // Hide if clicked outside
        document.addEventListener('click', function(event) {
            const isUnit = event.target.closest('.clickable-unit');
            if (!isUnit) {
                document.querySelectorAll('.click-buttons').forEach(btn => btn.style.display = 'none');
            }
        });
    </script>
@endpush
