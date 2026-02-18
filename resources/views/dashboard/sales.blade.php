 @extends('layouts.back-end.app')

 @php
     
     $lang = session()->get('locale');
 @endphp
 @section('title', ui_change('sales', 'sales'))

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
             padding: 5px 5px;
             border: none;
             border-bottom: 1px dashed #ddd;
             font-size: 16px;
             color: black;
             text-decoration: none;
         }

         .list-group-item:hover {
             background-color: #f1f1f1;
         }
         .accordion-button{
            color: black;
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
                 <h4 class="mb-3">{{ ui_change('Masters', 'sales') }}</h4>

                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.book_now') }}">{{ ui_change('quick_search', 'sales') }}</a>
                     </p>
                 </div> 
                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.customer.index') }}">{{ ui_change('customer', 'sales') }}</a>
                     </p>
                 </div> 
                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.enquiry.index') }}">{{ ui_change('enquiries', 'sales') }}</a>
                     </p>
                 </div> 
                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.proposal.index') }}">{{ ui_change('proposals', 'sales') }}</a>
                     </p>
                 </div> 
                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.booking.index') }}">{{ ui_change('bookings', 'sales') }}</a>
                     </p>
                 </div> 
                 <div class="accordion-item ">
                     <p class="accordion-header list-group-item">
                         <a class="accordion-button"
                             href="{{ route('sales.agreement.index') }}">{{ ui_change('agreements', 'sales') }}</a>
                     </p>
                 </div> 
             </div>
        
          
         </div> 
     </div>
 @endsection

 @push('script')
     <!-- Bootstrap JavaScript -->
     <script src="{{ asset(main_path().'js/bootstrap.bundle.min.js') }}"></script>
 @endpush
