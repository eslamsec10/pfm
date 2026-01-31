<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\UnitManagement;
use App\Models\PropertyManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
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


    }
}
