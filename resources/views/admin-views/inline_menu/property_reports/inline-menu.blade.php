<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('property_reports/schedules*') ?'active':'' }}"><a href="{{ route('schedules.index') }}">{{ ui_change('pre_bill_checking','property_report')  }}</a></li>
        <li class="{{ Request::is('property_reports/contract_details*') ?'active':'' }}"><a href="{{ route('contract_details') }}">{{ ui_change('contract_details','property_report')  }}</a></li>
        <li class="{{ Request::is('property_reports/invoice*') ?'active':'' }}"><a href="{{ route('invoices.all_invoices') }}">{{ ui_change('invoice_register','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/sales-return*') ?'active':'' }}"><a href="{{ route('invoices_return.all_invoices') }}">{{ ui_change('invoices_return_register','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/tenant_contact_details*') ?'active':'' }}"><a href="{{ route('tenant_contact_details') }}">{{ ui_change('tenant_contact_details','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/occupancy_details*') ?'active':'' }}"><a href="{{ route('occupancy_details') }}">{{ ui_change('occupancy_details','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/leased_expired_details*') ?'active':'' }}"><a href="{{ route('leased_expired_details') }}">{{ ui_change('leased_expired_details','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/tenant_age_analysis*') ?'active':'' }}"><a href="{{ route('tenant_age_analysis') }}">{{ ui_change('tenant_age_analysis','property_report')  }}</a></li> 
        <li class="{{ Request::is('property_reports/tenant_financial_summary*') ?'active':'' }}"><a href="{{ route('tenant_financial_summary') }}">{{ ui_change('tenant_financial_summary','property_report')  }}</a></li> 
    </ul>
</div>
