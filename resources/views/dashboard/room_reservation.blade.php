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
         body {
             background-color: #f8f9fa;
         }

         .list-container {
             max-width: 800px;
             margin: 10px 10px;
         }

         .list-group-item {
             display: flex;
             justify-content: space-between;
             align-items: center;
             padding: 12px 20px;
             border: none;
             border-bottom: 1px dashed #ddd;
             font-size: 16px;
             color: #007bff;
             text-decoration: none;
         }

         .list-group-item:hover {
             background-color: #f1f1f1;
         }

         .arrow {
             font-size: 14px;
             color: #bbb;
         }
     </style>
 @endpush

 @section('content')
     <div class="container list-container  ">
         <div class="row">

             <div class="col-md-4">
                 <h4 class="mb-3">{{ ui_change('Masters', 'room_reservation') }}</h4>

                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('customer.list') }}">{{ ui_change('customer', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('rental_type.list') }}">{{ ui_change('rental_type', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_type.list') }}">{{ ui_change('room_types', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_facility.list') }}">{{ ui_change('room_facilities', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_option.list') }}">{{ ui_change('room_options', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_status.list') }}">{{ ui_change('room_status', 'room_reservation') }}</a>
                     </h2>
                 </div>

             </div>
             {{-- <div class="col-md-4">
                 <h4 class="mb-3">{{ ui_change('Room_Management', 'room_reservation') }}</h4>

                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_building.list') }}">{{ ui_change('buildings', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_block.list') }}">{{ ui_change('blocks', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_floor.list') }}">{{ ui_change('floors', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_unit.list') }}">{{ ui_change('rooms', 'room_reservation') }}</a>
                     </h2>
                 </div>

             </div> --}}
             <div class="col-md-4">
                 <h4 class="mb-3">{{ ui_change('Transactions', 'room_reservation') }}</h4>

                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('booking_room.book_now') }}">{{ ui_change('book_now', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('booking_room.list') }}">{{ ui_change('booking_list', 'room_reservation') }}</a>
                     </h2>
                 </div>
                 <div class="accordion-item ">
                     <h2 class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('room_reservation.settings.room_reservation_settings') }}">{{ ui_change('room_reservation_settings', 'room_reservation') }}</a>
                     </h2>
                 </div>
                   
             </div>

         </div> 
     </div>
 @endsection

 @push('script')
     <!-- Bootstrap JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 @endpush
