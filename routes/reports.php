<?php

use App\Http\Controllers\reports\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'property_reports', 'middleware' => 'auth:web'], function () {
    Route::get('/contract_details', [ReportController::class, 'contract_details'])->name('contract_details');
    Route::get('/tenant_contact_details', [ReportController::class, 'tenant_report'])->name('tenant_contact_details');
    Route::get('/occupancy_details', [ReportController::class, 'occupancy_details'])->name('occupancy_details');
    Route::get('/leased_expired_details', [ReportController::class, 'leased_expired_details'])->name('leased_expired_details');
    Route::get('/tenant_age_analysis', [ReportController::class, 'tenant_age_analysis'])->name('tenant_age_analysis');
    Route::get('/tenant_financial_summary', [ReportController::class, 'tenant_financial_summary'])->name('tenant_financial_summary');
    Route::get('/tenant_ledger', [ReportController::class, 'tenant_ledger_report'])->name('tenant_ledger_report');
    Route::get('/schedule', [ReportController::class, 'schedule'])->name('tenant_schedule');
});


Route::group(['prefix' => 'reports', 'as' => 'reports.', 'middleware' => 'auth'],
    function () {

        Route::group(['prefix' => 'accrued-income-report', 'as' => 'accrued_income_report.'], function () {
            Route::get('list', [ReportController::class, 'Accrued_Income'])->name('list');
        });
    }
);
