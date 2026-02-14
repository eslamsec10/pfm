<div class="inline-page-menu my-4">
    <ul class="list-unstyled"> 
        <li class="{{ Request::is('property_management*') ?'active':'' }}"><a href="{{ route('property_management.index') }}">{{ui_change('building_master' , 'property_config')}}</a></li>
        <li class="{{ Request::is('block_management') ?'active':'' }}"><a href="{{ route('block_management.index') }}">{{ui_change('block_master' , 'property_config')}}</a></li>
        <li class="{{ Request::is('floor_management') ?'active':'' }}"><a href="{{ route('floor_management.index') }}">{{ui_change('floor_master' , 'property_config')}}</a></li>
        <li class="{{ (Request::is('unit_management') ||
        Request::is('unit_management/create') || Request::is('unit_management/edit*') )?'active':'' }}"><a href="{{ route('unit_management.index') }}">{{ui_change('unit_master' , 'property_config')}}</a></li>
        <li class="{{ (Request::is('rent_price_list') || 
        Request::is('rent_price_list/create') || Request::is('rent_price_list/edit*')
        ) ?'active':'' }}"><a href="{{ route('rent_price.index') }}">{{ui_change('rent_price_list' , 'property_config')}}</a></li> 
    
        <li class="{{ (Request::is('sales_price_list') || 
        Request::is('sales_price_list/create') || Request::is('sales_price_list/edit*')
        ) ?'active':'' }}"><a href="{{ route('sales_price.index') }}">{{ui_change('sales_price_list' , 'property_config')}}</a></li> 
    
        <li class="{{ (Request::is('daily_price_list') || 
        Request::is('daily_price_list/create') || Request::is('daily_price_list/edit*')
        ) ?'active':'' }}"><a href="{{ route('daily_price.index') }}">{{ui_change('daily_price_list' , 'property_config')}}</a></li> 
    </ul>
 
