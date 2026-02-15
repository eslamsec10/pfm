 @extends('layouts.back-end.app')

 @php
   
     $lang = session()->get('locale');
 @endphp
 @section('title', ui_change('property_Config', 'property_config'))

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

         .accordion-button {
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
             <div class="col-md-6">
                 <div class="accordion" id="accordionExample">
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('property_management.index') }}">{{ ui_change('building_master', 'property_config') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('block_management.index') }}">{{ ui_change('block_master', 'property_config') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('floor_management.index') }}">{{ ui_change('floor_master', 'property_config') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('unit_management.index') }}">{{ ui_change('unit_master', 'property_config') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('rent_price.index') }}">{{ ui_change('rent_price_list', 'property_config') }}</a>
                              
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('sales_price.index') }}">{{ ui_change('sales_price_list', 'property_config') }}</a>
                              
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('daily_price.index') }}">{{ ui_change('daily_price_list', 'property_config') }}</a>
                              
                         </p>
                     </div>


                 </div>

             </div>
             {{-- <div class="col-md-6">
                 <a href="#" class="list-group-item">Store Master <span class="arrow">&rsaquo;</span></a>
                 <a href="#" class="list-group-item">Zone Master <span class="arrow">&rsaquo;</span></a>
                 <a href="#" class="list-group-item">Rack Master <span class="arrow">&rsaquo;</span></a>
                 <a href="#" class="list-group-item">Bin Master <span class="arrow">&rsaquo;</span></a>
             </div> --}}
         </div>
     </div>

 @endsection

 @push('script')
     <!-- Bootstrap JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 @endpush
