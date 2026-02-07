<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\AgreementUnits;
use App\Models\UnitManagement;
use App\Observers\TenantObserver;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Observers\EnquiryUnitsObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\EnquiryUnitSearchDetails;
use App\Observers\AgreementUnitsObserver;
use App\Observers\UnitManagementObserver;
use App\Observers\PropertyManagementObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
        PropertyManagement::observe(PropertyManagementObserver::class);
        UnitManagement::observe(UnitManagementObserver::class);
        Tenant::observe(TenantObserver::class);

        // AgreementUnits::observe(AgreementUnitsObserver::class);
        EnquiryUnitSearchDetails::observe(EnquiryUnitsObserver::class);

    //php artisan make:observer BookingUnitsObserver --model=BookingUnits
    //php artisan make:observer ProposalUnitsObserver --model=ProposalUnits
    //php artisan make:observer EnquiryUnitsObserver --model=EnquiryUnitSearchDetails


    }
}
