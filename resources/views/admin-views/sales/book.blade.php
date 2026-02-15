 @extends('layouts.back-end.app')

 @php
     if (auth()->check()) {
         $company = App\Models\Company::where('id', auth()->user()->company_id)->first() ?? App\Models\User::first();
     } else {
         $company = App\Models\User::first();
     }
     $lang = session()->get('locale');
 @endphp
 @section('title', ui_change('room_reservation', 'room_reservation'))

 @push('css_or_js')
     <style>
         .unit-box {
             width: 70px;
             height: 50px;
             border: 1px solid #333;
             display: flex;
             align-items: center;
             justify-content: center;
             font-weight: 600;
             cursor: pointer;
             user-select: none;
         }

         .unit-box input {
             display: none;
         }

         .unit-box.selected {
             border: 3px solid #0d6efd;
         }

         .block-card {
             border: 1px solid #ccc;
             padding: 1rem;
             margin-bottom: 1.5rem;
             border-radius: 5px;
             background-color: #f9f9f9;
         }

         .floor-section {
             margin-bottom: 1rem;
         }

         .floor-label {
             font-weight: 600;
             margin-bottom: 0.5rem;
         }

         .units-container {
             display: flex;
             flex-wrap: wrap;
             gap: 0.5rem;
         }

         .context-menu {
             position: absolute;
             background: #fff;
             border: 1px solid #ccc;
             border-radius: 6px;
             box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
             display: none;
             z-index: 9999;
             min-width: 140px;
         }

         .context-menu ul {
             list-style: none;
             margin: 0;
             padding: 6px 0;
         }

         .context-menu li {
             padding: 8px 12px;
             cursor: pointer;
             font-size: 14px;
         }

         .context-menu li:hover {
             background-color: #0d6efd;
             color: #fff;
         }



         .block-card {
             border: 1px solid #ccc;
             padding: 10px;
             margin-bottom: 20px;
         }

         .floor-section {
             display: flex;
             align-items: flex-start;
             border: 1px dashed #888;
             padding: 10px;
             margin-bottom: 10px;
             gap: 10px;
         }

         .floor-label {
             font-weight: bold;
             min-width: 100px;
         }

         .units-container {
             display: flex;
             flex-wrap: wrap;
             gap: 5px;
         }

         .unit-box {
             display: flex;
             align-items: center;
             padding: 5px 8px;
             border: 1px solid #007bff;
             border-radius: 4px;
             cursor: pointer;
             position: relative;
         }

         .unit-box input[type="checkbox"] {
             display: none;
         }

         .unit-box.selected {
             outline: 2px solid #007bff;
         }

         .unit-dots {
             display: flex;
             gap: 3px;
             margin-left: auto;
         }

         .dot {
             width: 8px;
             height: 8px;
             border-radius: 50%;
             background-color: gray;
         }

         .dot-booking {
             background-color: #d500f9;
         }

         .dot-checkin {
             background-color: #ffeb3b;
         }

         .dot-info {
             background-color: #f44336;
         }

         .unit-box {
             width: 70px;
             height: 55px;
             border: 1px solid #333;
             display: flex;
             flex-direction: column;
             align-items: center;
             justify-content: space-between;
             font-weight: 600;
             cursor: pointer;
             user-select: none;
             padding: 4px;
             position: relative;
         }

         .unit-name {
             font-size: 13px;
         }

         .unit-dots {
             display: flex;
             gap: 4px;
             margin-bottom: 2px;
         }

         .dot {
             width: 6px;
             height: 6px;
             border-radius: 50%;
             background-color: #bbb;
             cursor: pointer;
         }

         .dot-booking {
             background-color: #0d6efd;
         }

         .dot-checkin {
             background-color: #198754;
         }

         .dot-info {
             background-color: #6c757d;
         }

         .dot::after {
             content: attr(data-tooltip);
             position: absolute;
             bottom: 60px;
             background: #000;
             color: #fff;
             font-size: 11px;
             padding: 4px 6px;
             border-radius: 4px;
             white-space: nowrap;
             opacity: 0;
             transform: translateY(5px);
             pointer-events: none;
             transition: .2s;
         }

         .dot:hover::after {
             opacity: 1;
             transform: translateY(0);
         }

         .unit-dots {
             position: relative;
         }

         .unit-dots {
             position: relative;
         }

         .unit-hover-box.dark {
             position: absolute;
             bottom: 120%;
             left: 50%;
             transform: translateX(-50%) translateY(6px);

             width: 230px;
             background: #0b0f14;
             color: #fff;
             border-radius: 6px;
             padding: 12px;
             font-size: 13px;

             box-shadow: 0 10px 25px rgba(0, 0, 0, .4);

             opacity: 0;
             visibility: hidden;
             transition: .25s ease;
             z-index: 9999;
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

         .dot-info:hover~.unit-hover-box {
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

         .options-title {
             margin-top: 10px;
             font-weight: 600;
             border-bottom: 1px solid #2a2f35;
             padding-bottom: 4px;
         }

         .options {
             list-style: none;
             padding: 0;
             margin: 6px 0 0;
         }

         .options li {
             display: flex;
             align-items: center;
             gap: 6px;
             margin-bottom: 5px;
         }

         .options li.yes::before {
             content: "✔";
             color: #00e676;
             font-weight: bold;
         }

         .options li.no::before {
             content: "✖";
             color: #ff5252;
             font-weight: bold;
         }

         .unit-status-empty {
             background: #e8f7ee;
             border: 1px solid #4caf50;
         }

         .unit-status-booked {
             background: #fff3cd;
             border: 1px solid #ffc107;
             cursor: not-allowed;
             opacity: 0.7;
         }

         .unit-status-checkin {
             background: #f8d7da;
             border: 1px solid #dc3545;
             cursor: not-allowed;
             opacity: 0.7;
         }

         .unit-status-maintenance {
             background: #e2e3e5;
             border: 1px solid #6c757d;
             cursor: not-allowed;
             opacity: 0.7;
         }

         .unit-box.selected {
             outline: 2px solid #007bff;
         }

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
     </style>
 @endpush

 @section('content')
     <div class="container list-container">
         <div class="row mt-5 @if ($lang == 'ar') rtl text-start @else ltr @endif">
             <div id="unitContextMenu" class="context-menu">
                 <ul>
                     <li data-action="enquiry">{{ ui_change('enquiry') }}</li>
                     <li data-action="proposal">{{ ui_change('proposal') }}</li>
                     <li data-action="booking">{{ ui_change('booking') }}</li>
                     <li data-action="agreement">{{ ui_change('agreement') }}</li>

                 </ul>
             </div>
             <div class="col-md-12">
                 <div class="card">
                     <div class="px-3 py-4">

                         <div class="legend">
                             <div><span style="background-color: #fff;"></span>
                                 {{ ui_change('Empty_Units', 'property_transaction') }}</div>
                             <div><span style="background-color: {{ $proposal_color ?? '#ffeb3b' }}"></span>
                                 {{ ui_change('Proposed_Units', 'property_transaction') }}</div>
                             <div><span style="background-color: {{ $booking_color ?? '#d500f9' }}"></span>
                                 {{ ui_change('Booked_Units', 'property_transaction') }}</div>
                             <div><span style="background-color: {{ $agreement_color ?? '#f44336' }}"></span>
                                 {{ ui_change('Agreement_Units', 'property_transaction') }}</div>
                             <div><span style="background-color: {{ $enquiry_color ?? '#372be2' }}"></span>
                                 {{ ui_change('Proposal_Pending', 'property_transaction') }}</div>

                         </div>
                         <form id="productForm" method="get" class="d-flex flex-wrap gap-2">
                             <button type="submit"
                                 onclick="setFormAction('{{ route('sales.enquiry.create_with_select_unit') }}')"
                                 class="btn btn--primary createButton">
                                 <i class="tio-add"></i>
                                 <span class="text">{{ ui_change('create_enquiry', 'property_transaction') }}</span>
                             </button>

                             <button type="submit"
                                 onclick="setFormAction('{{ route('proposal.create_with_select_unit') }}')"
                                 class="btn btn--primary createButton">
                                 <i class="tio-add"></i>
                                 <span class="text">{{ ui_change('create_proposal', 'property_transaction') }}</span>
                             </button>

                             <button type="submit"
                                 onclick="setFormAction('{{ route('booking.create_with_select_unit') }}')"
                                 class="btn btn--primary createButton">
                                 <i class="tio-add"></i>
                                 <span class="text">{{ ui_change('create_booking', 'property_transaction') }}</span>
                             </button>

                             <button type="submit"
                                 onclick="setFormAction('{{ route('agreement.create_with_select_unit') }}')"
                                 class="btn btn--primary createButton">
                                 <i class="tio-add"></i>
                                 <span class="text">{{ ui_change('create_agreement', 'property_transaction') }}</span>
                             </button>

                             <button type="button" data-target="#filter" data-filter="" data-toggle="modal"
                                 class="btn btn--primary btn-sm">
                                 <i class="fas fa-filter"></i>
                             </button>
                             {{-- <button type="button" data-check_in="" data-toggle="modal" data-target="#check_in"
                                class="btn btn--primary createButton">
                                <i class="tio-add"></i>
                                <span class="text">{{ ui_change('checkIn', 'property_transaction') }}</span>
                            </button>  --}}

                     </div>
                 </div>
             </div>
         </div>
         <div class="row mt-5 @if ($lang == 'ar') rtl text-start @else ltr @endif">

             <div class="col-md-12">
                 <div class="card">
                     <div class="px-3 py-4">
                         <div class="container list-container">
                             @foreach ($property_items as $property)
                                 <h3 class="mt-3">{{ $property->name }}</h3>

                                 @foreach ($property->blocks_management_child as $block_item)
                                     @if ($block_item->floors_management_child?->count() > 0)
                                         <div class="block-card">
                                             <div class="block-name mb-2">{{ ui_change('block') }}:
                                                 {{ $block_item->block->name }}
                                             </div>

                                             @foreach ($block_item->floors_management_child as $floor_item)
                                                 @if ($floor_item->unit_management_child?->count() > 0)
                                                     <div class="floor-section">
                                                         <div class="floor-label">{{ ui_change('floor') }}:
                                                             {{ $floor_item->floor_management_main->name }}</div>
                                                         <div class="units-container">
                                                             @foreach ($floor_item->unit_management_child as $unit)
                                                                 @php
                                                                     $bgColor = null;

                                                                     if ($unit->sales_status === 'agreement') {
                                                                         $bgColor = $agreement_color;
                                                                     } elseif ($unit->sales_status === 'booked') {
                                                                         $bgColor = $booking_color;
                                                                     } elseif ($unit->sales_status === 'proposal') {
                                                                         $bgColor = $proposal_color;
                                                                     } elseif ($unit->sales_status === 'enquiry') {
                                                                         $bgColor = $enquiry_color;
                                                                     }
                                                                 @endphp
                                                                 <label
                                                                     class="unit-box   {{ $unit->sales_status !== 'empty' || $unit->sales_status != null ? 'not-available' : '' }}"
                                                                     data-unit-id="{{ $unit->id }}"
                                                                     data-status="{{ $unit->sales_status }}"
                                                                     style="{{ $bgColor ? 'background-color:' . $bgColor . '; color:#fff;' : '' }}">
                                                                     <input type="checkbox" class="bulk-checkbox"
                                                                         name="bulk_ids[]" value="{{ $unit->id }}"
                                                                         @if ($unit->sales_status != 'empty' && $unit->sales_status != null) disabled @endif>
                                                                     {{ $unit->unit_management_main->name }}
                                                                     <div class="unit-dots">
                                                                         <span class="dot dot-booking"></span>
                                                                         <span class="dot dot-checkin"></span>
                                                                         <span class="dot dot-info"></span>

                                                                         <div class="unit-hover-box dark">
                                                                             <div class="title">
                                                                                 {{ ui_change('Room_Info') }}</div>

                                                                             <div class="info">
                                                                                 <div>{{ ui_change('room_description') }} :
                                                                                     <span>{{ $unit->unit_description?->name }}</span>
                                                                                 </div>
                                                                                 <div>{{ ui_change('room_type') }} :
                                                                                     <span>{{ $unit->unit_type?->name }}</span>
                                                                                 </div>
                                                                                 <div>{{ ui_change('room_condition') }} :
                                                                                     <span>{{ $unit->unit_condition?->name }}</span>
                                                                                 </div>
                                                                                 <div>{{ ui_change('room_parking') }} :
                                                                                     <span>{{ $unit->unit_parking?->name }}</span>
                                                                                 </div>
                                                                                 <div>{{ ui_change('room_view') }} :
                                                                                     <span>{{ $unit->unit_view?->name }}</span>
                                                                                 </div>

                                                                             </div>

                                                                             <div class="options-title">
                                                                                 {{ ui_change('room_facilities') }}</div>

                                                                             <ul class="options">
                                                                                 @forelse ($unit->facilities as $facility_item)
                                                                                     <li class="yes">
                                                                                         {{ $facility_item->name }}</li>
                                                                                 @empty
                                                                                     <li class="no">
                                                                                         {{ ui_change('no_facilities') }}
                                                                                     </li>
                                                                                 @endforelse

                                                                             </ul>
                                                                         </div>
                                                                     </div>


                                                                 </label>
                                                             @endforeach
                                                         </div>
                                                     </div>
                                                 @endif
                                             @endforeach
                                         </div>
                                     @endif
                                 @endforeach
                             @endforeach
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         </form>
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
                             <form action="{{ url()->current() }}" method="GET">
                                 <div class="px-3 py-4">
                                     <div class="row align-items-center">
                                         <div class="col-md-12 col-lg-4 col-xl-3">
                                             <label for="">
                                                 {{ ui_change('booking_status', 'property_transaction') }}
                                             </label>
                                             <select name="unit_description_id" class="form-control select2">
                                                 <option value="-1">{{ ui_change('any', 'property_transaction') }}
                                                 </option>
                                                 @foreach ($unit_descriptions as $unit_description_item)
                                                     <option value="{{ $unit_description_item->id }}"
                                                         @if (isset($filterUnitDescriptionId) && $filterUnitDescriptionId == $unit_description_item->id) selected @endif>
                                                         {{ $unit_description_item->name }}
                                                     </option>
                                                 @endforeach
                                             </select>

                                         </div>

                                         <!-- Unit Type -->
                                         <div class="col-md-12 col-lg-3">
                                             <label>{{ ui_change('unit_type') }}</label>
                                             <select name="unit_type_id" class="form-control select2">
                                                 <option value="-1">{{ ui_change('any') }}</option>
                                                 @foreach ($unit_types as $item)
                                                     <option value="{{ $item->id }}"
                                                         @if ($filterUnitTypeId == $item->id) selected @endif>
                                                         {{ $item->name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>

                                         <!-- Unit Condition -->
                                         <div class="col-md-12 col-lg-3">
                                             <label>{{ ui_change('unit_condition') }}</label>
                                             <select name="unit_condition_id" class="form-control select2">
                                                 <option value="-1">{{ ui_change('any') }}</option>
                                                 @foreach ($unit_conditions as $item)
                                                     <option value="{{ $item->id }}"
                                                         @if ($filterUnitConditionId == $item->id) selected @endif>
                                                         {{ $item->name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>

                                         <!-- Unit Facility -->
                                         <div class="col-md-12 col-lg-3">
                                             <label>{{ ui_change('unit_facility') }}</label>
                                             <select name="unit_facility_id" class="form-control select2">
                                                 <option value="-1">{{ ui_change('any') }}</option>
                                                 @foreach ($unit_facilities as $item)
                                                     <option value="{{ $item->id }}"
                                                         @if ($filterUnitFacilityId == $item->id) selected @endif>
                                                         {{ $item->name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>



                                     </div>
                                 </div>
                                 <div class="d-flex gap-3 justify-content-end mt-4">
                                     <button type="submit" class="btn btn--primary px-4 m-2" name="bulk_action_btn"
                                         value="filter"> {{ ui_change('filter', 'property_transaction') }}</button>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

     </div>
 @endsection

 @push('script')
     <script>
         flatpickr(".date", {
             dateFormat: "d/m/Y",
             defaultDate: "today",
         });
         flatpickr(".date_after_month", {
             dateFormat: "d/m/Y",
             defaultDate: "{{ \Carbon\Carbon::now()->addMonth()->format('d/m/Y') }}",
         });
     </script>
     <script>
         function setFormAction(actionUrl) {
             document.getElementById('productForm').action = actionUrl;
         }
         const bookingCreateRoute = "{{ route('booking.create_with_select_unit') }}";
         const enquiryCreateRoute = "{{ route('sales.enquiry.create_with_select_unit') }}";
         const proposalCreateRoute = "{{ route('proposal.create_with_select_unit') }}";
         const agreementCreateRoute = "{{ route('agreement.create_with_select_unit') }}";
         const BookNowCreateRoute = "{{ route('booking_room.create') }}";
         const CheckInRoute = "{{ route('booking_room.check_in_page') }}";
     </script>

     <script>
         document.querySelectorAll('.unit-box').forEach(box => {
             const checkbox = box.querySelector('input');

             box.addEventListener('click', function() {
                 checkbox.checked = !checkbox.checked;
                 box.classList.toggle('selected', checkbox.checked);
             });
         });
     </script>

     <script>
         let currentUnitId = null;
         const menu = document.getElementById('unitContextMenu');

         document.querySelectorAll('.unit-box').forEach(box => {
             const checkbox = box.querySelector('input');

             box.addEventListener('click', function() {
                 checkbox.checked = !checkbox.checked;
                 box.classList.toggle('selected', checkbox.checked);
             });

             box.addEventListener('contextmenu', function(e) {
                 e.preventDefault();

                 currentUnitId = this.dataset.unitId;

                 menu.style.display = 'block';
                 menu.style.top = `${e.pageY}px`;
                 menu.style.left = `${e.pageX}px`;
             });
         });

         menu.querySelectorAll('li').forEach(item => {
             item.addEventListener('click', function() {
                 const action = this.dataset.action;
                 if (action === 'enquiry') {
                     window.location.href = `${enquiryCreateRoute}?bulk_ids[]=${currentUnitId}`;
                 }
                 if (action === 'proposal') {
                     window.location.href = `${proposalCreateRoute}?bulk_ids[]=${currentUnitId}`;
                 }
                 if (action === 'booking') {
                     window.location.href =
                         `${bookingCreateRoute}?bulk_ids[]=${currentUnitId}`;
                 }
                 if (action === 'agreement') {
                     window.location.href = `${agreementCreateRoute}?bulk_ids[]=${currentUnitId}`;
                 }
                 if (action === 'book_now') {
                     window.location.href = `${BookNowCreateRoute}?bulk_ids[]=${currentUnitId}`;
                 }



                 if (action === 'check_in') {
                     window.location.href = `${CheckInRoute}?bulk_ids[]=${currentUnitId}`;
                 }

                 menu.style.display = 'none';
             });
         });

         document.addEventListener('click', () => {
             menu.style.display = 'none';
         });
     </script>
     <script>
         document.addEventListener('submit', function(e) {
             if (!e.target.matches('#checkin-form')) return;

             let container = document.getElementById('bulk-hidden-inputs');
             container.innerHTML = '';

             document.querySelectorAll('.bulk-checkbox:checked').forEach(function(checkbox) {
                 let input = document.createElement('input');
                 input.type = 'hidden';
                 input.name = 'bulk_ids[]';
                 input.value = checkbox.value;
                 container.appendChild(input);
             });
         });
     </script>
 @endpush
