<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('settings/company') ?'active':'' }}"><a href="{{ route('company_settings') }}">{{ui_change('settings')}}</a></li>
        <li class="{{ Request::is('settings/enquiry') ?'active':'' }}"><a href="{{ route('enquiry_settings') }}">{{ui_change('enquiry_settings')}}</a></li>
        <li class="{{ Request::is('settings/proposal') ?'active':'' }}"><a href="{{ route('proposal_settings') }}">{{ui_change('proposal_settings')}}</a></li>
        <li class="{{ Request::is('settings/booking') ?'active':'' }}"><a href="{{ route('booking_settings') }}">{{ui_change('booking_settings')}}</a></li>
        <li class="{{ Request::is('settings/agreement') ?'active':'' }}"><a href="{{ route('agreement_settings') }}">{{ui_change('agreement_settings')}}</a></li>
        <li class="{{ Request::is('settings/investment') ?'active':'' }}"><a href="{{ route('investment_settings') }}">{{ui_change('investment_settings' , 'investment')}}</a></li>
        <li class="{{ Request::is('settings/complaint') ?'active':'' }}"><a href="{{ route('complaint_settings') }}">{{ui_change('complaint_settings')}}</a></li>
        <li class="{{ Request::is('settings/receipt_settings') ?'active':'' }}"><a href="{{ route('receipt_settings') }}">{{ui_change('receipt_settings')}}</a></li>
        <li class="{{ Request::is('settings/investment_settings') ?'active':'' }}"><a href="{{ route('investment_settings') }}">{{ ui_change('investment_settings' , 'investment')}}</a></li>
        <li class="{{ Request::is('settings/notifications') ?'active':'' }}"><a href="{{ route('notifications_settings') }}">{{ ui_change('notifications' , 'investment')}}</a></li>

    </ul>
</div>
