<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('sales/book-now*') ?'active':'' }}"><a href="{{ route('sales.book_now') }}">{{ui_change('quick_search' , 'property_master')}}</a></li> 
        <li class="{{ Request::is('sales/property_customer*') ?'active':'' }}"><a href="{{ route('sales.customer.index') }}">{{ui_change('customers' , 'property_master')}}</a></li> 
        <li class="{{ Request::is('sales/enquiry*') ? 'active':'' }}"><a href="{{ route('sales.enquiry.index') }}">{{ui_change('enquiries' , 'property_master')}}</a></li> 
        <li class="{{ Request::is('sales/proposal*') ? 'active':'' }}"><a href="{{ route('sales.proposal.index') }}">{{ui_change('proposals' , 'property_master')}}</a></li> 
    </ul>
</div>
