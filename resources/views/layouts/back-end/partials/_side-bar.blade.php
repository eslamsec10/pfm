<style>
    /* .nav-item.active {
    background-color: #007bff !important;
    color: white !important;
}*/
</style>
<div id="sidebarMain" class="d-none">
    <?php $lang = session()->get('locale'); ?>
    <aside style="text-align: {{ $lang == 'ar' ? 'right' : 'left' }};"
        class="bg-white js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    {{-- @php(=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value) --}}
                    <a class="navbar-brand" href="{{ route('main_dashboard') }}" aria-label="Front">
                        <img onerror="this.src='{{ asset('assets/back-end/img/900x400/img1.jpg') }}'"
                            class="navbar-brand-logo-mini for-web-logo max-h-30"
                            src="{{ asset('assets/finexerp_logo.png') }}" alt="Logo">
                    </a>
                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                        class="d-none js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                    <button type="button" id="toggle-sidebar"
                        class="btn btn-sm btn--primary js-navbar-vertical-aside-toggle-invoker">
                        <i class="fas fa-angle-double-left navbar-vertical-aside-toggle-short-align"
                            data-toggle="tooltip" data-placement="right"></i>
                        <i class="fas fa-angle-double-right navbar-vertical-aside-toggle-full-align d-none"
                            data-toggle="tooltip" data-placement="right"></i>
                    </button>


                </div>

                <!-- Content -->
                <div class="navbar-vertical-content sidebar_main">
                    <!-- Search Form -->
                    <div class="sidebar--search-form pb-3 pt-4">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control"
                                id="search-bar-input" placeholder="{{ ui_change('search_menu') }}...">
                        </div>
                    </div>
                    <!-- End Search Form -->
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        {{-- {{ dd(\App\Helpers\Helpers::module_permission_check('dashboard')) }} --}}
                        @if (\App\Helpers\Helpers::module_permission_check('dashboard'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('main_dashboard*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ auth()->check() ? route('main_dashboard') : route('employee_dashboard') }}"
                                    title="{{ ui_change('dashboard') }}">
                                    <i class="fas fa-home  "></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('dashboard') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('search_unit'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('search_unit-side*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('search_unit_side') }}" title="{{ ui_change('search_unit') }}">
                                    <i class="fas fa-search "></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('search_unit') }}
                                    </span>
                                </a>
                            </li>
                            <!-- End Dashboards -->
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('hierarchy'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('hierarchy*') || Request::is('companies*') || Request::is('countries*') || Request::is('region*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link " href="{{ route('hierarchy') }}"
                                    title="{{ ui_change('hierarchy') }}">
                                    <i class="fas fa-sitemap"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('hierarchy') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('accounts_master'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('accounts_master*') ||
                            Request::is('groups*') ||
                            Request::is('ledgers*') ||
                            Request::is('chart_of_account*') ||
                            Request::is('category-cost-center*') ||
                            Request::is('cost_center*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('property_master') }}" title="{{ ui_change('Master_Settings') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('Master_Settings') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        {{-- @if (\App\Helpers\Helpers::module_permission_check('accounts_master'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('accounts_master*') ||
                            Request::is('groups*') ||
                            Request::is('ledgers*') ||
                            Request::is('chart_of_account*') ||
                            Request::is('category-cost-center*') ||
                            Request::is('cost_center*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('accounts_master') }}" title="{{ ui_change('accounts_master') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('accounts_master') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('transactions_master'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('transactions*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('transactions_master') }}"
                                    title="{{ ui_change('transactions_master') }}">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('transactions_master') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('property_master'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('property_master*') ||
                            Request::is('tenant*') ||
                            Request::is('agent*') ||
                            Request::is('ownership*') ||
                            Request::is('property_type*') ||
                            Request::is('services*') ||
                            Request::is('block') ||
                            Request::is('floors*') ||
                            Request::is('units*') ||
                            Request::is('unit_description*') ||
                            Request::is('unit_condition*') ||
                            Request::is('unit_type*') ||
                            Request::is('unit_parking*') ||
                            Request::is('view*') ||
                            Request::is('live_with*') ||
                            Request::is('business_activity*') ||
                            Request::is('enquiry_status*') ||
                            Request::is('enquiry_request_status*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('property_master') }}"
                                    title="{{ ui_change('property_master') }}">
                                    <i class="fas fa-building"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('property_master') }}
                                    </span>
                                </a>
                            </li>
                        @endif --}}
                        @if (\App\Helpers\Helpers::module_permission_check('property_config'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('property_management_side*') ||
                            Request::is('property_management*') ||
                            Request::is('unit_management*') ||
                            Request::is('floor_management*') ||
                            Request::is('block_management*') ||
                            Request::is('rent_price_list*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('property_management_side') }}"
                                    title="{{ ui_change('property_master') }}">
                                    <i class="fas fa-layer-group"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('property_master') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('property_transactions'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('property_transactions-side*') ||
                            Request::is('general_check_property*') ||
                            Request::is('enquiry*') ||
                            Request::is('proposal*') ||
                            Request::is('agreement*') ||
                            Request::is('booking*') ||
                            Request::is('termination*') ||
                            Request::is('renewal*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('property_transactions_side') }}"
                                    title="{{ ui_change('property_transactions') }}">
                                    <i class="fas fa-handshake"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('property_transactions') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('property_reports'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('property_reports-side*') || Request::is('property_reports*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('property_reports_side') }}"
                                    title="{{ ui_change('property_reports') }}">
                                    <i class="fas fa-chart-bar"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('property_reports') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('billing_&_collection'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('billing_collection-side*') || Request::is('receipt*')  || Request::is('sales-return*') || Request::is('invoice*') || Request::is('schedules*') || Request::is('billing_collection-side*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('billing_&_collection_side') }}"
                                    title="{{ ui_change('Billing_&_Collection') }}">
                                    <i class="fas fa-hand-holding-usd"></i>

                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('Billing_&_Collection') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        {{-- @if (\App\Helpers\Helpers::module_permission_check('collections'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('collections-side*') || Request::is('receipts*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('collections_side') }}" title="{{ ui_change('transactions') }}">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('transactions') }}
                                    </span>
                                </a>
                            </li>
                        @endif --}}
                        @if (\App\Helpers\Helpers::module_permission_check('facility_masters'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('facility_masters-side*') ||
                            Request::is('department*') ||
                            Request::is('complaint_category*') ||
                            Request::is('main_complaint*') ||
                            Request::is('supplier*') ||
                            Request::is('amc_providers*') ||
                            Request::is('work_status*') ||
                            Request::is('employee_type*') ||
                            Request::is('employee*') ||
                            Request::is('asset_group*') ||
                            Request::is('asset*') ||
                            Request::is('freezing*') ||
                            Request::is('priority*')
                                ? 'active'
                                : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('facility_masters_side') }}"
                                    title="{{ ui_change('facility_masters') }}">
                                    <i class="fas fa-warehouse"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('facility_masters') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('facility_transactions'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('facility_transactions-side*') || Request::is('facility_transactions*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('facility_transactions_side') }}"
                                    title="{{ ui_change('facility_transactions') }}">
                                    <i class="fas fa-calendar-check"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('facility_transactions') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('investments'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('investments-side*') || Request::is('investments*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('investments_side') }}" title="{{ ui_change('investments', 'investment') }}">
                                    <i class="fas fa-money-bill-trend-up"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('investments', 'investment') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('room_reservation'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('room_reservation-side*') || Request::is('room_reservation*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('room_reservation_side') }}" title="{{ ui_change('room_reservation', 'room_reservation') }}">
                                    <i class="fas fa-hotel"></i>

                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('room_reservation', 'room_reservation') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('sales'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('sales-side*') || Request::is('sales*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('sales_side') }}" title="{{ ui_change('sales', 'sales') }}">
                                    <i class="fas fa-hotel"></i>

                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('sales', 'sales') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('facility_reports'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('facility_reports-side*') || Request::is('facility_reports*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('facility_reports_side') }}"
                                    title="{{ ui_change('facility_reports') }}">
                                    <i class="fas fa-database"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('facility_reports') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('general_management'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('general_management-side*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('general_management_side') }}"
                                    title="{{ ui_change('general_management') }}">
                                    <i class="fas fa-cogs"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('general_management') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('import_excel'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('import_excel-side*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('import_excel_side') }}" title="{{ ui_change('import_excel') }}">
                                    <i class="fas fa-cog"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('import_excel') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('settings'))
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('settings-side*') ? 'active' : '' }}"
                                style="font-size: 1rem">
                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                    href="{{ route('settings_side') }}" title="{{ ui_change('settings') }}">
                                    <i class="fas fa-cog"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"
                                        style="{{ $lang == 'ar' ? 'margin-right: 8px;' : 'margin-left: 8px;' }}">
                                        {{ ui_change('settings') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if (\App\Helpers\Helpers::module_permission_check('search_unit'))
                            <li class="nav-item pt-5">
                            </li>
                        @endif
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

@push('script_2')
    <script>
        $(window).on('load', function() {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        //Sidebar Menu Search
        var $rows = $('.navbar-vertical-content .navbar-nav > li');
        $('#search-bar-input').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
    </script>
@endpush
