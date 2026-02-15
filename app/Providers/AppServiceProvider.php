<?php

namespace App\Providers;

use App\Models\AgreementUnits;
use App\Models\EnquiryUnitSearchDetails;
use App\Models\PropertyCustomer;
use App\Models\PropertyManagement; 
use App\Models\Tenant; 
use App\Models\UnitManagement;
use App\Observers\AgreementUnitsObserver;
use App\Observers\EnquiryUnitsObserver;
use App\Observers\PropertyCustomerObserver;
use App\Observers\PropertyManagementObserver;
use App\Observers\TenantObserver;
use App\Observers\UnitManagementObserver;
use Illuminate\Pagination\Paginator; 
use Illuminate\Support\ServiceProvider;

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
        PropertyCustomer::observe(PropertyCustomerObserver::class);

        // AgreementUnits::observe(AgreementUnitsObserver::class);
        EnquiryUnitSearchDetails::observe(EnquiryUnitsObserver::class);

    //php artisan make:observer BookingUnitsObserver --model=BookingUnits
    //php artisan make:observer ProposalUnitsObserver --model=ProposalUnits
    //php artisan make:observer EnquiryUnitsObserver --model=EnquiryUnitSearchDetails


    }
}
