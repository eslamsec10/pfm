<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('*customer*') ?'active':'' }}"><a href="{{ route('customer.list') }}">{{ui_change('customers','investment') }}</a></li>
        <li class="{{ Request::is('*room-type*') ?'active':'' }}"><a href="{{ route('room_type.list') }}">{{ui_change('room_type','investment') }}</a></li>
        <li class="{{ Request::is('*room-facilities*') ?'active':'' }}"><a href="{{ route('room_facility.list') }}">{{ui_change('room_facility','investment') }}</a></li>
        <li class="{{ Request::is('*room-options*') ?'active':'' }}"><a href="{{ route('room_option.list') }}">{{ui_change('room_option','investment') }}</a></li>
        <li class="{{ Request::is('*room-status*') ?'active':'' }}"><a href="{{ route('room_status.list') }}">{{ui_change('room_status','investment')  }}</a></li> 
    </ul>
</div>
