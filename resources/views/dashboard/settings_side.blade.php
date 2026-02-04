 @extends('layouts.back-end.app')

 @section('title', ui_change('settings'))
 @php
     if (auth()->check()) {
         $company = App\Models\Company::where('id', auth()->user()->company_id)->first() ?? App\Models\User::first();
     } else {
         $company = App\Models\User::first();
     }     $lang = session()->get('locale');
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
             <div class="col-md-6">
                 <div class="accordion" id="accordionExample">
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('company_settings') }}">{{ ui_change('settings') }}</a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('enquiry_settings') }}">{{ ui_change('enquiry_settings') }}</a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('proposal_settings') }}">{{ ui_change('proposal_settings') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('booking_settings') }}">{{ ui_change('booking_settings') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('agreement_settings') }}">{{ ui_change('agreement_settings') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('investment_settings') }}">{{ ui_change('investment_settings' , 'investment') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('complaint_settings') }}">{{ ui_change('complaint_settings') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('admin.business-settings.language.index') }}">{{ ui_change('language') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('admin.settings.ui_settings.index') }}">{{ ui_change('ui_settings') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('admin.currency.view') }}">{{ ui_change('currency') }}
                             </a>
                         </p>
                     </div>
                     <div class="accordion-item ">
                         <p class="accordion-header list-group-item">
                             <a class="accordion-button"
                                 href="{{ route('notifications_settings') }}">{{ ui_change('notifications') }}
                             </a>
                         </p>
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
