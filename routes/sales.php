<?php

use App\Http\Controllers\property_management\DailyRentListController;
use App\Http\Controllers\property_management\SalesPriceListController;
use App\Http\Controllers\sales\PropertyCustomerController;
use App\Http\Controllers\sales\SalesEnquiryController;
use App\Http\Controllers\sales\SalesProposalController;
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
Route::group(['prefix' => 'sales/property_customer', 'as' => 'sales.', 'middleware' => 'auth:web'], function () {
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


Route::group(['prefix' => 'sales/enquiry', 'as' => 'sales.', 'middleware' => 'auth:web'], function () {
    Route::get('/', [SalesEnquiryController::class, 'index'])->name('enquiry.index');
    Route::get('/create', [SalesEnquiryController::class, 'create'])->name('enquiry.create');
    Route::get('/create_with_select_unit', [SalesEnquiryController::class, 'create_with_select_unit'])->name('enquiry.create_with_select_unit');
    Route::post('store', [SalesEnquiryController::class, 'store'])->name('enquiry.store');
    Route::post('search', [SalesEnquiryController::class, 'search'])->name('enquiry.search');
    Route::get('/edit/{id}', [SalesEnquiryController::class, 'edit'])->name('enquiry.edit');
    Route::get('/add_to_proposal/{id}', [SalesEnquiryController::class, 'add_to_proposal'])->name('enquiry.add_to_proposal');
    Route::post('/store_to_proposal', [SalesEnquiryController::class, 'store_to_proposal'])->name('enquiry.store_to_proposal');
    Route::get('/show/{id}', [SalesEnquiryController::class, 'show'])->name('enquiry.show_enquiry');
    Route::get('/view_image/{id}', [SalesEnquiryController::class, 'view_image'])->name('enquiry.show');
    Route::patch('/update/{id}', [SalesEnquiryController::class, 'update'])->name('enquiry.update');
    Route::get('delete', [SalesEnquiryController::class, 'delete'])->name('enquiry.delete');
    Route::post('status-update', [SalesEnquiryController::class, 'statusUpdate'])->name('enquiry.status-update');
    Route::get('/check_property/{id}', [SalesEnquiryController::class, 'check_property'])->name('enquiry.check_propoerty');
    Route::get('/view_image/{id}/{enquiry_id}', [SalesEnquiryController::class, 'view_image'])->name('enquiry.image_view');
    Route::get('/list_view/{id}/{enquiry_id}', [SalesEnquiryController::class, 'list_view'])->name('enquiry.list_view');
    Route::get('/empty_unit_from_enquiry_unit_search/{id}', [SalesEnquiryController::class, 'empty_unit_from_enquiry_unit_search'])->name('enquiry.empty_unit_from_enquiry_unit_search');
    Route::get('/empty_unit_from_enquiry_unit/{id}', [SalesEnquiryController::class, 'empty_unit_from_enquiry_unit'])->name('enquiry.empty_unit_from_enquiry_unit');

    Route::get('get_customer/{id}', [SalesEnquiryController::class, 'get_customer'])->name('enquiry.get_customer');
    Route::get('get_service_master/{id}', [SalesEnquiryController::class, 'get_service_master'])->name('enquiry.get_service_master');
    // Route::get('get_floors_by_block_id/{id}', [EnquiryController::class, 'get_floors_by_block_id'])->name('enquiry.get_floors_by_block_id');
    // Route::get('get_units_by_floor_id/{floor_id}/{block_id}/{property_id}', [EnquiryController::class, 'get_units_by_floor_id'])->name('enquiry.get_units_by_floor_id');

});

Route::get('sales/book-now', [SalesEnquiryController::class, 'book_now'])->name('sales.book_now');



Route::group(['prefix' => 'sales/proposal','as' => 'sales.', 'middleware' => 'auth:web'], function () {
    Route::get('/', [SalesProposalController::class, 'index'])->name('proposal.index');
    Route::get('/create', [SalesProposalController::class, 'create'])->name('proposal.create');
    Route::post('store', [SalesProposalController::class, 'store'])->name('proposal.store');
    Route::get('/edit/{id}', [SalesProposalController::class, 'edit'])->name('proposal.edit');
    Route::get('/show/{id}', [SalesProposalController::class, 'show'])->name('proposal.show');
    Route::patch('/update/{id}', [SalesProposalController::class, 'update'])->name('proposal.update');
    Route::get('delete', [SalesProposalController::class, 'delete'])->name('proposal.delete');
    Route::post('status-update', [SalesProposalController::class, 'statusUpdate'])->name('proposal.status-update');
    Route::get('/check_property/{id}', [SalesProposalController::class, 'check_property'])->name('proposal.check_property');
    Route::get('/view_image/{id}/{proposal_id}', [SalesProposalController::class, 'view_image'])->name('proposal.image_view');
    Route::get('/list_view/{id}/{proposal_id}', [SalesProposalController::class, 'list_view'])->name('proposal.list_view');
    Route::get('get_tenant/{id}', [SalesProposalController::class, 'get_tenant'])->name('proposal.get_tenant');
    Route::get('get_units', [SalesProposalController::class, 'get_units'])->name('proposal.get_units');
    Route::get('get_unit_service/{id}', [SalesProposalController::class, 'get_unit_service'])->name('proposal.get_unit_service');
    Route::get('add_to_booking/{id}', [SalesProposalController::class, 'add_to_booking'])->name('proposal.add_to_booking');
    Route::get('add_to_agreement/{id}', [SalesProposalController::class, 'add_to_agreement'])->name('proposal.add_to_agreement');
    Route::post('/store_to_booking', [SalesProposalController::class, 'store_to_booking'])->name('proposal.store_to_booking');
    Route::post('/store_to_agreement', [SalesProposalController::class, 'store_to_agreement'])->name('proposal.store_to_agreement');
    Route::get('/empty_unit_from_proposal_unit/{id}', [SalesProposalController::class, 'empty_unit_from_proposal_unit'])->name('proposal.empty_unit_from_proposal_unit');
    Route::get('/empty_unit_from_service_proposal/{id}', [SalesProposalController::class, 'empty_unit_from_service_proposal'])->name('proposal.empty_unit_from_service_proposal');
    Route::post('search', [SalesProposalController::class, 'search'])->name('proposal.search');
    Route::get('/create_with_select_unit', [SalesProposalController::class, 'create_with_select_unit'])->name('proposal.create_with_select_unit');
});
