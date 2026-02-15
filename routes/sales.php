<?php

use App\Http\Controllers\property_management\DailyRentListController;
use App\Http\Controllers\property_management\SalesPriceListController; 
use App\Http\Controllers\sales\PropertyCustomerController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'daily_price_list', 'middleware' => 'auth:web'], function () {
    Route::get('/', [DailyRentListController::class, 'index'])->name('daily_price.index');
    Route::get('/create', [DailyRentListController::class, 'create'])->name('daily_price.create');
    Route::get('/edit/{id}', [DailyRentListController::class, 'edit'])->name('daily_price.edit');
    Route::patch('/update/{id}', [DailyRentListController::class, 'update'])->name('daily_price.update');
    Route::post('/store', [DailyRentListController::class, 'store'])->name('daily_price.store');
    Route::get('/get_blocks_by_property_id_for_daily/{id}', [DailyRentListController::class, 'get_blocks_by_property_id_for_daily'])->name('daily_price.get_blocks_by_property_id_for_daily');
    Route::get('/get_floors_by_block_id_for_daily/{id}', [DailyRentListController::class, 'get_floors_by_block_id_for_daily'])->name('daily_price.get_floors_by_block_id_for_daily');
    Route::get('/get_units_by_floor_id_for_daily/{id}', [DailyRentListController::class, 'get_units_by_floor_id_for_daily'])->name('daily_price.get_units_by_floor_id_for_daily');
    Route::get('delete', [DailyRentListController::class, 'delete'])->name('daily_price.delete');
    Route::get('/get-units/{property_id}', [DailyRentListController::class, 'getUnits'])->name('daily_price.get_units'); 
    Route::get('/get-blocks/{property}', [DailyRentListController::class, 'getBlocks'])->name('daily_price.get_blocks');
    Route::get('/get-floors/{property}/{block}', [DailyRentListController::class, 'getFloors'])->name('daily_price.get_floors');
    Route::get('/get-units', [DailyRentListController::class, 'getUnitsFiltered'])->name('daily_price.get_units_filtered');
});


Route::group(['prefix' => 'sales_price_list', 'middleware' => 'auth:web'], function () {
    Route::get('/', [SalesPriceListController::class, 'index'])->name('sales_price.index');
    Route::get('/create', [SalesPriceListController::class, 'create'])->name('sales_price.create');
    Route::get('/edit/{id}', [SalesPriceListController::class, 'edit'])->name('sales_price.edit');
    Route::patch('/update/{id}', [SalesPriceListController::class, 'update'])->name('sales_price.update');
    Route::post('/store', [SalesPriceListController::class, 'store'])->name('sales_price.store');
    Route::get('/get_blocks_by_property_id_for_sales/{id}', [SalesPriceListController::class, 'get_blocks_by_property_id_for_sales'])->name('sales_price.get_blocks_by_property_id_for_sales');
    Route::get('/get_floors_by_block_id_for_sales/{id}', [SalesPriceListController::class, 'get_floors_by_block_id_for_sales'])->name('sales_price.get_floors_by_block_id_for_sales');
    Route::get('/get_units_by_floor_id_for_sales/{id}', [SalesPriceListController::class, 'get_units_by_floor_id_for_sales'])->name('sales_price.get_units_by_floor_id_for_sales');
    Route::get('delete', [SalesPriceListController::class, 'delete'])->name('sales_price.delete');
    Route::get('/get-units/{property_id}', [SalesPriceListController::class, 'getUnits'])->name('sales_price.get_units'); 
    Route::get('/get-blocks/{property}', [SalesPriceListController::class, 'getBlocks'])->name('sales_price.get_blocks');
    Route::get('/get-floors/{property}/{block}', [SalesPriceListController::class, 'getFloors'])->name('sales_price.get_floors');
    Route::get('/get-units', [SalesPriceListController::class, 'getUnitsFiltered'])->name('sales_price.get_units_filtered');
});


// Customers Management
Route::group(['prefix' => 'sales/property_customer','as'=>'sales.', 'middleware' => 'auth:web'], function () {
    Route::get('/', [PropertyCustomerController::class, 'index'])->name('customer.index');
    Route::get('/create', [PropertyCustomerController::class, 'create'])->name('customer.create');
    Route::post('store', [PropertyCustomerController::class, 'store'])->name('customer.store');
    Route::post('store_for_anything', [PropertyCustomerController::class, 'store_for_anything'])->name('customer.store_for_anything');
    Route::get('/edit/{id}', [PropertyCustomerController::class, 'edit'])->name('customer.edit');
    Route::get('/show/{id}', [PropertyCustomerController::class, 'show'])->name('customer.show');
    Route::patch('/update/{id}', [PropertyCustomerController::class, 'update'])->name('customer.update');
    Route::get('delete', [PropertyCustomerController::class, 'delete'])->name('customer.delete');
    Route::post('status-update', [PropertyCustomerController::class, 'statusUpdate'])->name('customer.status-update');
    Route::get('exportCustomers', [PropertyCustomerController::class, 'exportCustomers'])->name('customer.exportCustomers');
});