 {{-- width="250px" height="107px" --}}
 <div class="invoice-header "
     style="display: flex;
flex-direction: column;
align-items: center;  
border-bottom: 2px solid black;
padding-bottom: 10px;
margin: 50px 50px 30px 50px;">

     <div class="partner-logos">
         <img style="margin-bottom: 20px;" src="{{ asset('assets/finexerp_logo.png') }}?v={{ time() }}"
             style="height: {{ $invoice_settings->height . 'px' ?? '107px' }};"
             width="{{ $invoice_settings->width ?? '250px' }}" alt="eBird ERP">

     </div>
     <div class="company-info "
         style=" text-align: center;  
    display: flex;
    flex-direction: column;
    align-items: center;  ">
         <h2>{{ $company->name }}</h2>
         @if (isset($invoice_settings) && $invoice_settings->company_address != 0)
             <p>{{ $company->address1 }}</p>
             <p>{{ $company->address2 }}</p>
             <p>{{ $company->address3 }}</p>
         @endif

         @if (isset($invoice_settings) && $invoice_settings->company_phone != 0)
             <p>{{ __('general.mobile') }} :
                 {{ (isset($company->mobile_dail_code) ? '(' . $company->mobile_dail_code . ')' : '') . $company->mobile }}
             </p>
         @endif
         @if (isset($invoice_settings) && $invoice_settings->company_fax != 0)
             <p>{{ __('companies.fax') }} :
                 {{ (isset($company->fax_dail_code) ? '(' . $company->fax_dail_code . ')' : '') . $company->fax }}
             </p>
         @endif
         @if (isset($invoice_settings) && $invoice_settings->company_phone != 0)
             <p>{{ __('general.phone') }} :
                 {{ (isset($company->phone_dail_code) ? '(' . $company->phone_dail_code . ')' : '') . $company->phone }}
             </p>
         @endif
         @if (isset($invoice_settings) && $invoice_settings->company_email != 0)
             <p>{{ __('roles.email') }} : {{ $company->email }}</p>
         @endif
         @if (isset($invoice_settings) && $invoice_settings->company_vat_no != 0)
             <p>{{ ui_change('VAT_no') }} : {{ $company->vat_no }}</p>
         @endif
     </div>


 </div>
 
