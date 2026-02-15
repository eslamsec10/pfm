<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('customer') ?'active':'' }}"><a href="{{ route('sales.customer.index') }}">{{ui_change('customers' , 'property_master')}}</a></li> 
    </ul>
</div>
