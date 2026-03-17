 @extends('layouts.back-end.app')

 @section('title', ui_change('property_reports', 'property_report'))
 @php
     $lang = session()->get('locale');
 @endphp
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
             <div class="col-md-6">
                 <div class="accordion" id="accordionExample">
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('schedules.index') }}">{{ ui_change('pre_bill_checking', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('contract_details') }}">{{ ui_change('contract_details', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('tenant_ledger_report') }}">{{ ui_change('tenant_ledger_report', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('tenant_schedule') }}">{{ ui_change('tenant_schedule', 'property_report') }}</a>
                             
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('reports.accrued_income_report.list') }}">{{ ui_change('accrued_income_report', 'property_report') }}</a> 
                         </h2>
                     </div>
                     {{-- <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('invoices.all_invoices') }}">{{ ui_change('invoice_register', 'property_report') }}</a>
                             
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('invoices_return.all_invoices') }}">{{ ui_change('invoices_return_register', 'property_report') }}</a>
                           
                         </h2>
                     </div> --}}
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('tenant_contact_details') }}">{{ ui_change('tenant_contact_details', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('occupancy_details') }}">{{ ui_change('occupancy_details', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('leased_expired_details') }}">{{ ui_change('leased_expired_details', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>
                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('tenant_age_analysis') }}">{{ ui_change('tenant_age_analysis', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>

                     <div class="accordion-item ">
                         <h2 class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('tenant_financial_summary') }}">{{ ui_change('tenant_financial_summary', 'property_report') }}</a>
                             {{-- <span class="arrow">&rsaquo;</span> --}}
                         </h2>
                     </div>



                 </div>

             </div>
         </div>
     </div>

 @endsection

 @push('script')
     <!-- Bootstrap JavaScript -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 @endpush
