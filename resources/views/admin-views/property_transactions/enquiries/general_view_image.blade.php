@extends('layouts.back-end.app')
@php
    $lang = Session::get('locale');
@endphp
@section('title', ui_change('view_image', 'property_transaction'))

@push('css_or_js')
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
            grid-template-columns: 100px auto; 
            gap: 5px;
            margin-bottom: 10px;
        } 
        .grid .floor {
            background-color: teal;
            color: #fff;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            line-height: 40px;
        }
 
        .grid .unit-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, 100px); 
            gap: 5px;
        }

        .grid .unit {
            width: 100px; 
            height: 40px; 
            background-color: #fff;
            text-align: center;
            line-height: 40px;
            border: 1px solid #000;
            cursor: pointer;
            position: relative;
        }


        .unit.empty {
            animation: blink 3s infinite;
            font-weight: bold;
        }

        .unit.proposed {
            background-color: {{ $settings['proposal_color']->value ?? '#ffeb3b' }};
        }

        .unit.booked {
            background-color: {{ $settings['booking_color']->value ?? '#d500f9' }};
            color: #fff;
        }

        .unit.agreement {
            background-color: {{ $settings['agreement_color']->value ?? '#f44336' }};
            color: #fff;
        }

        .unit.enquiry {
            background-color: {{ $settings['enquiry_color']->value ?? '#372be2' }};
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

        .unit-checkbox {
            display: none;
        }

        .unit.selected {
            outline: 3px solid #007bff;
            outline-offset: -3px;
        }

        .unit.dragging {
            opacity: 0.5;
            border: 2px dashed #007bff;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h1 mb-0 d-flex gap-2 align-items-center">
                {{ ui_change('property', 'property_transaction') }}
            </h2>
        </div>

        @include('admin-views.inline_menu.property_transaction.inline-menu')

        <form id="productForm" method="get" class="d-flex flex-wrap gap-2">
            <button type="submit" onclick="setFormAction('{{ route('enquiry.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i> {{ ui_change('create_enquiry', 'property_transaction') }}
            </button>
            <button type="submit" onclick="setFormAction('{{ route('proposal.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i> {{ ui_change('create_proposal', 'property_transaction') }}
            </button>
            <button type="submit" onclick="setFormAction('{{ route('booking.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i> {{ ui_change('create_booking', 'property_transaction') }}
            </button>
            <button type="submit" onclick="setFormAction('{{ route('agreement.create_with_select_unit') }}')"
                class="btn btn--primary createButton">
                <i class="tio-add"></i> {{ ui_change('create_agreement', 'property_transaction') }}
            </button>
        </form>

        @foreach ($property_items as $property_item)
            <div class="row mt-5 @if ($lang == 'ar') rtl text-start @else ltr @endif">
                <div class="col-md-12">
                    <div class="card">
                        <div class="px-3 py-4">
                            <div class="legend">
                                <div><span style="background-color: teal;"></span>
                                    {{ ui_change('Floors', 'property_transaction') }}</div>
                                <div><span style="background-color: #fff;"></span>
                                    {{ ui_change('Empty_Units', 'property_transaction') }}</div>
                                <div><span
                                        style="background-color: {{ $settings['proposal_color']->value ?? '#ffeb3b' }}"></span>
                                    {{ ui_change('Proposed_Units', 'property_transaction') }}</div>
                                <div><span
                                        style="background-color: {{ $settings['booking_color']->value ?? '#d500f9' }}"></span>
                                    {{ ui_change('Booked_Units', 'property_transaction') }}</div>
                                <div><span
                                        style="background-color: {{ $settings['agreement_color']->value ?? '#f44336' }}"></span>
                                    {{ ui_change('Agreement_Units', 'property_transaction') }}</div>
                                <div><span
                                        style="background-color: {{ $settings['enquiry_color']->value ?? '#372be2' }}"></span>
                                    {{ ui_change('Proposal_Pending', 'property_transaction') }}</div>
                            </div>

                            <div class="grid-title">
                                <h3 style="color: var(--primary)">{{ $property_item->name }}</h3>
                            </div>
                            <div class="grid-container">
                                @foreach ($property_item->blocks_management_child as $block_item)
                                    <div class="grid-title">{{ $block_item->block->name }}</div>

                                    @foreach ($block_item->floors_management_child as $floor_item)
                                        <div class="grid">
                                            <div class="floor">{{ $floor_item->floor_management_main?->name }}</div>
                                            <div class="unit-container">
                                                @foreach ($floor_item->unit_management_child->sortBy('position') as $unit_item)
                                                    <div class="unit hover-info {{ $unit_item->booking_status }}" data-id="{{ $unit_item->id }}">
                                                        {{ $unit_item->unit_management_main?->name.'-'. $unit_item->unit_management_main?->unit_description?->name }}
                                                        <div class="info-box">
                                                            @if ($unit_item->booking_status == 'enquiry')
                                                                {{ optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->company_name }}
                                                            @elseif($unit_item->booking_status == 'proposal')
                                                                {{ optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->company_name }}
                                                            @elseif($unit_item->booking_status == 'booking')
                                                                {{ optional(optional(optional($unit_item->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit_item->booking_main)->booking)->tenant)->company_name }}
                                                            @elseif($unit_item->booking_status == 'agreement')
                                                                {{ optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->company_name }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- <div class="grid">
                                            <!-- Floor column -->
                                            <div class="floor">{{ $floor_item->floor_management_main->name }}</div>

                                            <!-- Units -->
                                            @foreach ($floor_item->unit_management_child as $unit_item)
                                                <div class="unit hover-info {{ $unit_item->booking_status }}">
                                                    {{ $unit_item->unit_management_main->name }}
                                                    <div class="info-box">
                                                        @if ($unit_item->booking_status == 'enquiry')
                                                            {{ optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->name ?? optional(optional(optional($unit_item->enquiry)->main_enquiry)->tenant)->company_name }}
                                                        @elseif($unit_item->booking_status == 'proposal')
                                                            {{ optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->name ?? optional(optional(optional($unit_item->proposal_main)->proposal)->tenant)->company_name }}
                                                        @elseif($unit_item->booking_status == 'booking')
                                                            {{ optional(optional(optional($unit_item->booking_main)->booking)->tenant)->name ?? optional(optional(optional($unit_item->booking_main)->booking)->tenant)->company_name }}
                                                        @elseif($unit_item->booking_status == 'agreement')
                                                            {{ optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->name ?? optional(optional(optional($unit_item->agreement_main)->agreement)->tenant)->company_name }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div> --}}
                                        <hr>
                                    @endforeach
                                @endforeach
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('script') 
{{-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>  --}}

<script src="{{ asset(main_path()."js/Sortable.min.js") }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.unit-container').forEach(container => {
        new Sortable(container, {
            animation: 150,
            ghostClass: 'dragging',
            onEnd: function(evt) {
                let order = [];
                container.querySelectorAll('.unit').forEach((el, index) => {
                    order.push({
                        id: el.getAttribute('data-id'), 
                        position: index + 1
                    });
                });
 
                fetch("{{ route('units.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success'){
                        console.log('Order saved successfully');
                    }
                })
                .catch(err => console.error(err));
            }
        });
    });

});
</script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.unit').forEach(unit => {
                unit.addEventListener('click', function(e) {
                    if (!unit.classList.contains('empty')) return;
                    unit.classList.toggle('selected');
                    const checkbox = unit.querySelector('.unit-checkbox');
                    if (checkbox) {
                        checkbox.checked = unit.classList.contains('selected');
                        checkbox.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    }
                });
            });
        });

        function setFormAction(actionUrl) {
            document.getElementById('productForm').action = actionUrl;
        }
    </script>
@endpush
