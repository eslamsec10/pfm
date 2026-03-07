<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UnitManagement;
use App\Models\PropertyManagement;
use App\Models\hierarchy\CostCenter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\LanguageController;
use App\Models\hierarchy\CostCenterCategory;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyListController;
use App\Http\Controllers\reports\ReportController;
use App\Http\Controllers\hierarchy\GroupController;
use App\Http\Controllers\hierarchy\LedgerController;
use App\Http\Controllers\hierarchy\RegionController;
use App\Http\Controllers\hierarchy\CountryController;
use App\Http\Controllers\auth\UserManagementController;
use App\Http\Controllers\collections\ReceiptController;
use App\Http\Controllers\Investment\InvestorController;
use App\Http\Controllers\hierarchy\CostCenterController;
use App\Http\Controllers\property_master\LevyController;
use App\Http\Controllers\property_master\UnitController;
use App\Http\Controllers\property_master\ViewController;
use App\Http\Controllers\facility_master\AgentController;
use App\Http\Controllers\facility_master\AssetController;
use App\Http\Controllers\Investment\InvestmentController;
use App\Http\Controllers\property_master\BlockController;
use App\Http\Controllers\property_master\FloorController;
use App\Http\Controllers\property_master\ServiceController;
use App\Http\Controllers\facility_master\EmployeeController;
use App\Http\Controllers\facility_master\FreezingController;
use App\Http\Controllers\facility_master\PriorityController;
use App\Http\Controllers\facility_master\SupplierController;
use App\Http\Controllers\property_master\LiveWithController;
use App\Http\Controllers\property_master\UnitTypeController;
use App\Http\Controllers\property_reports\InvoiceController;
use App\Http\Controllers\settings\CompanySettingsController;
use App\Http\Controllers\property_master\OwnershipController;
use App\Http\Controllers\property_reports\ScheduleController;
use App\Http\Controllers\facility_master\AssetGroupController;
use App\Http\Controllers\facility_master\DepartmentController;
use App\Http\Controllers\facility_master\WorkStatusController;
use App\Http\Controllers\settings\ComplaintSettingsController;
use App\Http\Controllers\collections\InvoiceSettingsController;
use App\Http\Controllers\collections\ReceiptSettingsController;
use App\Http\Controllers\facility_master\AMCProviderController;
use App\Http\Controllers\property_master\UnitParkingController;
use App\Http\Controllers\facility_master\EmployeeTypeController;
use App\Http\Controllers\hierarchy\CostCenterCategoryController;
use App\Http\Controllers\property_master\PropertyTypeController;
use App\Http\Controllers\property_transactions\TenantController;
use App\Http\Controllers\Room_Reservation\BookingRoomController;
use App\Http\Controllers\facility_master\MainComplaintController;
use App\Http\Controllers\property_master\EnquiryStatusController;
use App\Http\Controllers\property_master\UnitConditionController;
use App\Http\Controllers\property_transactions\BookingController;
use App\Http\Controllers\property_transactions\EnquiryController;
use App\Http\Controllers\property_reports\InvoiceReturnController;
use App\Http\Controllers\property_transactions\ProposalController;
use App\Http\Controllers\RolesAndCompanyManagement\RoleController;
use App\Http\Controllers\collections\SalesReturnSettingsController;
use App\Http\Controllers\property_master\UnitDescriptionController;
use App\Http\Controllers\property_transactions\AgreementController;
use App\Http\Controllers\property_master\BusinessActivityController;
use App\Http\Controllers\Room_Reservation\Management\RoomController;
use App\Http\Controllers\Room_Reservation\Master\CustomerController;
use App\Http\Controllers\Room_Reservation\Master\RoomTypeController;
use App\Http\Controllers\facility_master\ComplaintCategoryController;
use App\Http\Controllers\property_management\RentPriceListController;
use App\Http\Controllers\property_transactions\TerminationController;
use App\Http\Controllers\property_management\UnitManagementController;
use App\Http\Controllers\Room_Reservation\Master\RentalTypeController;
use App\Http\Controllers\Room_Reservation\Master\RoomOptionController;
use App\Http\Controllers\Room_Reservation\Master\RoomStatusController;
use App\Http\Controllers\property_management\BlockManagementController;
use App\Http\Controllers\property_management\FloorManagementController;
use App\Http\Controllers\property_master\EnquiryRequestStatusController;
use App\Http\Controllers\RolesAndCompanyManagement\CustomRoleController;
use App\Http\Controllers\Room_Reservation\Master\RoomFacilityController;
use App\Http\Controllers\Room_Reservation\Management\RoomBlockController;
use App\Http\Controllers\Room_Reservation\Management\RoomFloorController;
use App\Http\Controllers\Room_Reservation\Meetings\MeetingRoomController;
use App\Http\Controllers\property_management\PropertyManagementController;
use App\Http\Controllers\facility_transactions\ComplaintRegisterController;
use App\Http\Controllers\Room_Reservation\Management\RoomBuildingController;
use App\Http\Controllers\RolesAndCompanyManagement\CompanyManagementController;
use App\Http\Controllers\Room_Reservation\Meetings\MeetingRoomBookingController;
use App\Http\Controllers\property_transactions\PropertyTransactionSettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// // Translation
// Route::get('/login-page' ,function (){

//     return view('auth.login');
// })->name('login-page');
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang');
Route::group(['prefix' => 'custom-role', 'middleware' => 'auth:web'], function () {
    Route::get('create', [CustomRoleController::class, 'create'])->name('role_admin.create');
    Route::post('create', [CustomRoleController::class, 'store'])->name('role_admin.store');
    Route::get('update/{id}', [CustomRoleController::class, 'edit'])->name('role_admin.update');
    Route::post('update/{id}', [CustomRoleController::class, 'update'])->name('role_admin.master_update');
    Route::post('employee-role-status', [CustomRoleController::class, 'employee_role_status_update'])->name('role_admin.employee-role-status');
    Route::get('export', [CustomRoleController::class, 'export'])->name('role_admin.export');
    Route::post('delete', [CustomRoleController::class, 'delete'])->name('role_admin.delete');
});
// Route::middleware('auth')->get('/tenant/connect', [TenantConnectionController::class, 'connect'])->name('tenant.connect');
// Dashboard
// Route::group(['middleware' => 'database.set'], function () {
Route::group(["prefix" => "dashboard"], function () {
    // Route::get("/", [DashboardController::class, "index"])->name("dashboard")->middleware('auth');
    Route::get("/get_units_by_booking_status/{status}", [DashboardController::class, "get_units_by_booking_status"])->name("dashboard.get_units_by_booking_status");
});
// Route::get("/get_unit_details/{id}", [DashboardController::class, "get_unit_details"])->name("general.get_unit_details");
Route::get("/get_unit_by_id/{id}", [DashboardController::class, "get_unit_by_id"])->name("general.get_unit_by_id");

// Dashboard
Route::group(["prefix" => "main_dashboard", 'middleware' => 'auth:web'], function () {
    Route::get("/", [DashboardController::class, "index"])->name("main_dashboard");
    Route::get("/get_unit_details/{id}", [DashboardController::class, "get_unit_details"])->name("dashboard.get_unit_details");
});

Route::get('/hierarchy', function () {
    return view('dashboard.hierarchy');
})->name('hierarchy');
Route::get('/accounts_master', function () {
    return view('dashboard.accounts_master');
})->name('accounts_master');
Route::get('/transactions', function () {
    return view('dashboard.transactions');
})->name('transactions_master');
Route::get('/property_master', function () {
    return view('dashboard.transactions_master');
})->name('property_master');

Route::get('/property_management_side', function () {
    return view('dashboard.property_management');
})->name('property_management_side');

Route::get('/property_transactions-side', function () {
    return view('dashboard.property_transactions');
})->name('property_transactions_side');

Route::get('/property_reports-side', function () {
    return view('dashboard.property_reports');
})->name('property_reports_side');
Route::get('/billing_collection-side', function () {
    return view('dashboard.billing_&_collection');
})->name('billing_&_collection_side');

Route::get('/collections-side', function () {
    return view('dashboard.collections_side');
})->name('collections_side');

Route::get('/facility_masters-side', function () {
    return view('dashboard.facility_masters_side');
})->name('facility_masters_side');

Route::get('/facility_transactions-side', function () {
    return view('dashboard.facility_transactions_side');
})->name('facility_transactions_side');

Route::get('/investments-side', function () {
    return view('dashboard.investment');
})->name('investments_side');

Route::get('/room_reservation-side', function () {
    return view('dashboard.room_reservation');
})->name('room_reservation_side');
Route::get('/sales-side', function () {
    return view('dashboard.sales');
})->name('sales_side');

Route::get('/facility_reports-side', function () {
    return view('dashboard.facility_reports_side');
})->name('facility_reports_side');

Route::get('/general_management-side', function () {
    return view('dashboard.general_management_side');
})->name('general_management_side');

Route::get('/settings-side', function () {
    return view('dashboard.settings_side');
})->name('settings_side');

Route::get('/search_unit-side', [DashboardController::class, 'search_unit_side'])->name('search_unit_side');
// Route::get('/search_unit-side', function () {
//     return view('dashboard.search_side');
// })->name('search_unit_side');

// User managment general search units in dashboard
Route::get('/general_search_units_in_dashboard', [DashboardController::class, 'general_search_units_in_dashboard'])->name('general_search_units_in_dashboard');
Route::get('/get_unit_type_by_unit_descrption_id/{id}', [EnquiryController::class, 'get_unit_type_by_unit_descrption_id'])->name('get_unit_type_by_unit_descrption_id');

Route::group(['prefix' => 'companies', 'middleware' => 'auth:web'], function () {

    Route::get('/', [CompanyManagementController::class, 'index'])->name('companies');
    // Route::get('/create', [CompanyManagementController::class , 'create'])->name('companies.create');
    // Route::post('/store', [CompanyManagementController::class , 'store'])->name('companies.store');
    Route::get('/edit/{id}', [CompanyManagementController::class, 'edit'])->name('companies.edit');
    Route::get('/show/{id}', [CompanyManagementController::class, 'show'])->name('companies.show');
    Route::patch('/update/{id}', [CompanyManagementController::class, 'update'])->name('companies.update');
    // Route::get('/delete', [CompanyManagementController::class ,'delete'])->name('companies.delete');

});

// Roles
Route::group(['prefix' => 'roles', 'middleware' => 'auth:web'], function () {
    Route::get('/', [RoleController::class, 'index'])->name('roles');
    Route::get('/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/{id}/update', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/delete', [RoleController::class, 'destroy'])->name('roles.delete');
});
Route::group(['prefix' => 'general_property_list', 'middleware' => 'auth:web'], function () {
    Route::get('/', [PropertyListController::class, 'index'])->name('general_property_list');
    Route::get('/list_view/{id}', [PropertyListController::class, 'list_view'])->name('general_property_list_view');
    Route::get('/image_view/{id}', [PropertyListController::class, 'image_view'])->name('general_property_image_view');
});
// User Managment
Route::group(['prefix' => 'user_management', 'middleware' => 'auth:web'], function () {

    Route::get('/', [UserManagementController::class, 'index'])->name('user_management');
    Route::get('/create', [UserManagementController::class, 'create'])->name('user_management.create');
    Route::post('/create', [UserManagementController::class, 'store'])->name('user_management.store');
    Route::get('/view/{id}', [UserManagementController::class, 'view'])->name('user_management.view');
    Route::get('/edit/{id}', [UserManagementController::class, 'edit'])->name('user_management.edit');
    Route::patch('/update/{id}', [UserManagementController::class, 'update'])->name('user_management.update');
    Route::post('/update_status', [UserManagementController::class, 'update_status'])->name('user_management.update_status');
    Route::get('/delete', [UserManagementController::class, 'destroy'])->name('user_management.delete');
    Route::post('/bulk-user-delete', [UserManagementController::class, 'bulk_user_delete'])->name('bulk-user-delete');
});
// Route::group(['prefix' => 'settings'], function () {
//     Route::get('/company', [CompanySettingsController::class, 'index'])->name('company_settings');
//     Route::patch('/company/update', [CompanySettingsController::class, 'update'])->name('company_settings.store');
//     Route::get('/proposal', [PropertyTransactionSettingsController::class, 'proposalIndex'])->name('proposal_settings');
//     Route::patch('/proposal/update', [PropertyTransactionSettingsController::class, 'proposalUpdate'])->name('proposal_settings.store');
//     Route::get('/enquiry', [PropertyTransactionSettingsController::class, 'enquiryIndex'])->name('enquiry_settings');
//     Route::patch('/enquiry/update', [PropertyTransactionSettingsController::class, 'enquiryUpdate'])->name('enquiry_settings.store');
//     Route::get('/booking', [PropertyTransactionSettingsController::class, 'bookingIndex'])->name('booking_settings');
//     Route::patch('/booking/update', [PropertyTransactionSettingsController::class, 'bookingUpdate'])->name('booking_settings.store');
//     Route::get('/agreement', [PropertyTransactionSettingsController::class, 'agreementIndex'])->name('agreement_settings');
//     Route::patch('/agreement/update', [PropertyTransactionSettingsController::class, 'agreementUpdate'])->name('agreement_settings.store');
//     Route::get('/complaint', [ComplaintSettingsController::class, 'complaintIndex'])->name('complaint_settings');
//     Route::patch('/complaint/update', [ComplaintSettingsController::class, 'complaintUpdate'])->name('complaint_settings.store');

// });
Route::group(['prefix' => 'transactions_settings', 'middleware' => 'auth:web'], function () {
    Route::get('/receipt_settings', [ReceiptSettingsController::class, 'receiptIndex'])->name('receipt_settings');
    Route::patch('/receipt_settings/update', [ReceiptSettingsController::class, 'ReceiptUpdate'])->name('receipt_settings.update');
    Route::get('/receipt_settings/edit/{id}', [ReceiptSettingsController::class, 'edit'])->name('receipt_settings.edit');
    Route::post('/receipt_settings/create', [ReceiptSettingsController::class, 'ReceiptStore'])->name('receipt_settings.store');
    Route::get('/receipt_settings/delete', [ReceiptSettingsController::class, 'delete'])->name('receipt_settings.delete');
    Route::get('/invoice_settings', [InvoiceSettingsController::class, 'invoiceIndex'])->name('invoice_settings');
    Route::patch('/invoice_settings/update', [InvoiceSettingsController::class, 'invoiceUpdate'])->name('invoice_settings.update');
    Route::get('/invoice_settings/edit/{id}', [InvoiceSettingsController::class, 'edit'])->name('invoice_settings.edit');
    Route::post('/invoice_settings/create', [InvoiceSettingsController::class, 'invoiceStore'])->name('invoice_settings.store');
    Route::get('/invoice_settings/delete', [InvoiceSettingsController::class, 'delete'])->name('invoice_settings.delete');
    Route::get('/sales_return_settings', [SalesReturnSettingsController::class, 'sales_return_index'])->name('sales_return_settings');
    Route::patch('/sales_return_settings/update', [SalesReturnSettingsController::class, 'sales_return_update'])->name('sales_return_settings.update');
    Route::get('/sales_return_settings/edit/{id}', [SalesReturnSettingsController::class, 'edit'])->name('sales_return_settings.edit');
    Route::post('/sales_return_settings/create', [SalesReturnSettingsController::class, 'sales_return_store'])->name('sales_return_settings.store');
    Route::get('/sales_return_settings/delete', [SalesReturnSettingsController::class, 'delete'])->name('sales_return_settings.delete');
    // Route::get('/invoice_settings', [ReceiptSettingsController::class,'invoiceIndex'])->name('invoice_settings');
    // Route::patch('/invoice_settings/update', [ReceiptSettingsController::class,'invoiceUpdate'])->name('invoice_settings.store');

});
Route::group(['prefix' => 'region', 'middleware' => 'auth:web'], function () {
    Route::get('/', [RegionController::class, 'index'])->name('region');
    Route::post('/create', [RegionController::class, 'store'])->name('region.store');
    Route::get('/edit/{id}', [RegionController::class, 'edit'])->name('region.edit');
    Route::patch('/update/{id}', [RegionController::class, 'update'])->name('region.update');
    Route::get('/delete', [RegionController::class, 'delete'])->name('region.delete');
});

Route::group(['prefix' => 'countries', 'middleware' => 'auth:web'], function () {
    Route::get('/', [CountryController::class, 'index'])->name('country');
    Route::post('/create', [CountryController::class, 'store'])->name('country.store');
    Route::get('/edit/{id}', [CountryController::class, 'edit'])->name('country.edit');
    Route::patch('/update/{id}', [CountryController::class, 'update'])->name('country.update');
    Route::get('/delete', [CountryController::class, 'delete'])->name('country.delete');
});

###################################################### Hierarchy ###################################################
Route::group(['prefix' => 'ledgers', 'middleware' => 'auth:web'], function () {
    Route::get('/', [LedgerController::class, 'index'])->name('ledgers.index');
    Route::post('/', [LedgerController::class, 'store'])->name('ledgers.store');
    Route::get('/create', [LedgerController::class, 'create'])->name('ledgers.create');
    Route::get('/edit/{id}', [LedgerController::class, 'edit'])->name('ledgers.edit');
    Route::get('/show/{id}', [LedgerController::class, 'show'])->name('ledgers.show');
    Route::patch('/update/{id}', [LedgerController::class, 'update'])->name('ledgers.update');
    Route::get('/delete', [LedgerController::class, 'delete'])->name('ledgers.delete');
});
Route::group(['prefix' => 'groups', 'middleware' => 'auth:web'], function () {
    Route::get('/', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/edit/{id}', [GroupController::class, 'edit'])->name('groups.edit');
    Route::get('/show/{id}', [GroupController::class, 'show'])->name('groups.show');
    Route::patch('/update/{id}', [GroupController::class, 'update'])->name('groups.update');
    Route::get('/delete', [GroupController::class, 'delete'])->name('groups.delete');
    Route::get('/get_group_by_id/{id}', [GroupController::class, 'get_group_by_id'])->name('get_group_by_id');
});
Route::group(['prefix' => 'chart_of_account', 'middleware' => 'auth:web'], function () {
    Route::get('/', [GroupController::class, 'chart_of_account'])->name('chart_of_account');
});

// Cost Center Category
Route::group(['prefix' => 'category-cost-center', 'middleware' => 'auth:web'], function () {
    Route::get('/', [CostCenterCategoryController::class, 'index'])->name('cost_center_category.index');
    Route::post('store', [CostCenterCategoryController::class, 'store'])->name('cost_center_category.store');
    Route::get('/edit/{id}', [CostCenterCategoryController::class, 'edit'])->name('cost_center_category.edit');
    Route::patch('/update/{id}', [CostCenterCategoryController::class, 'update'])->name('cost_center_category.update');
    Route::get('delete', [CostCenterCategoryController::class, 'delete'])->name('cost_center_category.delete');
    Route::post('/status-update', [CostCenterCategoryController::class, 'statusUpdate'])->name('cost_center_category.status-update');
});

// Cost Center

Route::group(['prefix' => 'cost_center', 'middleware' => 'auth:web'], function () {
    Route::get('/', [CostCenterController::class, 'index'])->name('cost_center.index');
    Route::post('store', [CostCenterController::class, 'store'])->name('cost_center.store');
    Route::get('/edit/{id}', [CostCenterController::class, 'edit'])->name('cost_center.edit');
    Route::patch('/update/{id}', [CostCenterController::class, 'update'])->name('cost_center.update');
    Route::get('delete', [CostCenterController::class, 'delete'])->name('cost_center.delete');
    Route::post('/status-update', [CostCenterController::class, 'statusUpdate'])->name('cost_center.status-update');
});

################################################## Start Propert_master #############################################

// ownership
Route::group(['prefix' => 'ownership', 'middleware' => 'auth:web'], function () {
    Route::get('/', [OwnershipController::class, 'index'])->name('ownership.index');
    Route::post('store', [OwnershipController::class, 'store'])->name('ownership.store');
    Route::get('/edit/{id}', [OwnershipController::class, 'edit'])->name('ownership.edit');
    Route::patch('/update/{id}', [OwnershipController::class, 'update'])->name('ownership.update');
    Route::get('delete', [OwnershipController::class, 'delete'])->name('ownership.delete');
    Route::post('/status-update', [OwnershipController::class, 'statusUpdate'])->name('ownership.status-update');
});

// property type
Route::group(['prefix' => 'property_type', 'middleware' => 'auth:web'], function () {
    Route::get('/', [PropertyTypeController::class, 'index'])->name('property_type.index');
    Route::post('store', [PropertyTypeController::class, 'store'])->name('property_type.store');
    Route::get('/edit/{id}', [PropertyTypeController::class, 'edit'])->name('property_type.edit');
    Route::patch('/update/{id}', [PropertyTypeController::class, 'update'])->name('property_type.update');
    Route::get('delete', [PropertyTypeController::class, 'delete'])->name('property_type.delete');
    Route::post('/status-update', [PropertyTypeController::class, 'statusUpdate'])->name('property_type.status-update');
});

// services
Route::group(['prefix' => 'services', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::post('store', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/edit/{id}', [ServiceController::class, 'edit'])->name('services.edit');
    Route::patch('/update/{id}', [ServiceController::class, 'update'])->name('services.update');
    Route::get('delete', [ServiceController::class, 'delete'])->name('services.delete');
    Route::post('/status-update', [ServiceController::class, 'statusUpdate'])->name('services.status-update');
});

// Blocks
Route::group(['prefix' => 'block', 'middleware' => 'auth:web'], function () {
    Route::get('/', [BlockController::class, 'index'])->name('block.index');
    Route::post('store', [BlockController::class, 'store'])->name('block.store');
    Route::get('/edit/{id}', [BlockController::class, 'edit'])->name('block.edit');
    Route::patch('/update/{id}', [BlockController::class, 'update'])->name('block.update');
    Route::get('delete', [BlockController::class, 'delete'])->name('block.delete');
    Route::post('/status-update', [BlockController::class, 'statusUpdate'])->name('block.status-update');
});

// Unit Description
Route::group(['prefix' => 'unit_description', 'middleware' => 'auth:web'], function () {
    Route::get('/', [UnitDescriptionController::class, 'index'])->name('unit_description.index');
    Route::post('store', [UnitDescriptionController::class, 'store'])->name('unit_description.store');
    Route::get('/edit/{id}', [UnitDescriptionController::class, 'edit'])->name('unit_description.edit');
    Route::patch('/update/{id}', [UnitDescriptionController::class, 'update'])->name('unit_description.update');
    Route::get('delete', [UnitDescriptionController::class, 'delete'])->name('unit_description.delete');
    Route::post('/status-update', [UnitDescriptionController::class, 'statusUpdate'])->name('unit_description.status-update');
});

// Levy
Route::group(['prefix' => 'levy', 'middleware' => 'auth:web'], function () {
    Route::get('/', [LevyController::class, 'index'])->name('levy.index');
    Route::post('store', [LevyController::class, 'store'])->name('levy.store');
    Route::get('/edit/{id}', [LevyController::class, 'edit'])->name('levy.edit');
    Route::patch('/update/{id}', [LevyController::class, 'update'])->name('levy.update');
    Route::get('delete', [LevyController::class, 'delete'])->name('levy.delete');
    Route::post('/status-update', [LevyController::class, 'statusUpdate'])->name('levy.status-update');
});

// Unit Type
Route::group(['prefix' => 'unit_type', 'middleware' => 'auth:web'], function () {
    Route::get('/', [UnitTypeController::class, 'index'])->name('unit_type.index');
    Route::post('store', [UnitTypeController::class, 'store'])->name('unit_type.store');
    Route::get('/edit/{id}', [UnitTypeController::class, 'edit'])->name('unit_type.edit');
    Route::patch('/update/{id}', [UnitTypeController::class, 'update'])->name('unit_type.update');
    Route::get('delete', [UnitTypeController::class, 'delete'])->name('unit_type.delete');
    Route::post('/status-update', [UnitTypeController::class, 'statusUpdate'])->name('unit_type.status-update');
});

// Unit Condition
Route::group(['prefix' => 'unit_condition', 'middleware' => 'auth:web'], function () {
    Route::get('/', [UnitConditionController::class, 'index'])->name('unit_condition.index');
    Route::post('store', [UnitConditionController::class, 'store'])->name('unit_condition.store');
    Route::get('/edit/{id}', [UnitConditionController::class, 'edit'])->name('unit_condition.edit');
    Route::patch('/update/{id}', [UnitConditionController::class, 'update'])->name('unit_condition.update');
    Route::get('delete', [UnitConditionController::class, 'delete'])->name('unit_condition.delete');
    Route::post('/status-update', [UnitConditionController::class, 'statusUpdate'])->name('unit_condition.status-update');
});

// Unit Parking
Route::group(['prefix' => 'unit_parking', 'middleware' => 'auth:web'], function () {
    Route::get('/', [UnitParkingController::class, 'index'])->name('unit_parking.index');
    Route::post('store', [UnitParkingController::class, 'store'])->name('unit_parking.store');
    Route::get('/edit/{id}', [UnitParkingController::class, 'edit'])->name('unit_parking.edit');
    Route::patch('/update/{id}', [UnitParkingController::class, 'update'])->name('unit_parking.update');
    Route::get('delete', [UnitParkingController::class, 'delete'])->name('unit_parking.delete');
    Route::post('/status-update', [UnitParkingController::class, 'statusUpdate'])->name('unit_parking.status-update');
});

// View
Route::group(['prefix' => 'view', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ViewController::class, 'index'])->name('view.index');
    Route::post('store', [ViewController::class, 'store'])->name('view.store');
    Route::get('/edit/{id}', [ViewController::class, 'edit'])->name('view.edit');
    Route::patch('/update/{id}', [ViewController::class, 'update'])->name('view.update');
    Route::get('delete', [ViewController::class, 'delete'])->name('view.delete');
    Route::post('/status-update', [ViewController::class, 'statusUpdate'])->name('view.status-update');
});

// Business Activity
Route::group(['prefix' => 'business_activity', 'middleware' => 'auth:web'], function () {
    Route::get('/', [BusinessActivityController::class, 'index'])->name('business_activity.index');
    Route::post('store', [BusinessActivityController::class, 'store'])->name('business_activity.store');
    Route::get('/edit/{id}', [BusinessActivityController::class, 'edit'])->name('business_activity.edit');
    Route::patch('/update/{id}', [BusinessActivityController::class, 'update'])->name('business_activity.update');
    Route::get('delete', [BusinessActivityController::class, 'delete'])->name('business_activity.delete');
    Route::post('/status-update', [BusinessActivityController::class, 'statusUpdate'])->name('business_activity.status-update');
});

// Live With
Route::group(['prefix' => 'live_with', 'middleware' => 'auth:web'], function () {
    Route::get('/', [LiveWithController::class, 'index'])->name('live_with.index');
    Route::post('store', [LiveWithController::class, 'store'])->name('live_with.store');
    Route::get('/edit/{id}', [LiveWithController::class, 'edit'])->name('live_with.edit');
    Route::patch('/update/{id}', [LiveWithController::class, 'update'])->name('live_with.update');
    Route::get('delete', [LiveWithController::class, 'delete'])->name('live_with.delete');
    Route::post('/status-update', [LiveWithController::class, 'statusUpdate'])->name('live_with.status-update');
});

// Enquiry Status
Route::group(['prefix' => 'enquiry_status', 'middleware' => 'auth:web'], function () {
    Route::get('/', [EnquiryStatusController::class, 'index'])->name('enquiry_status.index');
    Route::post('store', [EnquiryStatusController::class, 'store'])->name('enquiry_status.store');
    Route::get('/edit/{id}', [EnquiryStatusController::class, 'edit'])->name('enquiry_status.edit');
    Route::patch('/update/{id}', [EnquiryStatusController::class, 'update'])->name('enquiry_status.update');
    Route::get('delete', [EnquiryStatusController::class, 'delete'])->name('enquiry_status.delete');
    Route::post('/status-update', [EnquiryStatusController::class, 'statusUpdate'])->name('enquiry_status.status-update');
});

// Enquiry Request Status
Route::group(['prefix' => 'enquiry_request_status', 'middleware' => 'auth:web'], function () {
    Route::get('/', [EnquiryRequestStatusController::class, 'index'])->name('enquiry_request_status.index');
    Route::post('store', [EnquiryRequestStatusController::class, 'store'])->name('enquiry_request_status.store');
    Route::get('/edit/{id}', [EnquiryRequestStatusController::class, 'edit'])->name('enquiry_request_status.edit');
    Route::patch('/update/{id}', [EnquiryRequestStatusController::class, 'update'])->name('enquiry_request_status.update');
    Route::get('delete', [EnquiryRequestStatusController::class, 'delete'])->name('enquiry_request_status.delete');
    Route::post('/status-update', [EnquiryRequestStatusController::class, 'statusUpdate'])->name('enquiry_request_status.status-update');
});

// Floors
Route::group(['prefix' => 'floors', 'middleware' => 'auth:web'], function () {
    Route::get('/', [FloorController::class, 'index'])->name('floor.index');
    Route::get('/create', [FloorController::class, 'create'])->name('floor.create');
    Route::post('/floor_single', [FloorController::class, 'floor_single'])->name('floor.floor_single');
    Route::patch('/floor_single_edit/{id}', [FloorController::class, 'floor_single_edit'])->name('floor.floor_single_edit');
    Route::get('/floor_multiple', [FloorController::class, 'floor_multiple'])->name('floor.floor_multiple');
    Route::patch('/floor_multiple_edit/{id}', [FloorController::class, 'floor_multiple_edit'])->name('floor.floor_multiple_edit');
    Route::post('/floor_multiple_store', [FloorController::class, 'floor_multiple_store'])->name('floor.floor_multiple_store');
    Route::get('/edit/{id}', [FloorController::class, 'edit'])->name('floor.edit');
    Route::get('delete', [FloorController::class, 'delete'])->name('floor.delete');
});

// Units
Route::group(['prefix' => 'units', 'middleware' => 'auth:web'], function () {
    Route::get('/', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/create', [UnitController::class, 'create'])->name('unit.create');
    Route::post('/unit_single', [UnitController::class, 'unit_single'])->name('unit.unit_single');
    Route::patch('/unit_single_edit/{id}', [UnitController::class, 'unit_single_edit'])->name('unit.unit_single_edit');
    Route::get('/unit_multiple', [UnitController::class, 'unit_multiple'])->name('unit.unit_multiple');
    Route::patch('/unit_multiple_edit/{id}', [UnitController::class, 'unit_multiple_edit'])->name('unit.unit_multiple_edit');
    Route::post('/unit_multiple_store', [UnitController::class, 'unit_multiple_store'])->name('unit.unit_multiple_store');
    Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('unit.edit');
    Route::get('delete', [UnitController::class, 'delete'])->name('unit.delete');
});

################################################## End Propert_master #############################################

################################################## Start Propert Management ###########################################

// Property Management
Route::group(['prefix' => 'property_management', 'middleware' => 'auth:web'], function () {
    Route::get('/', [PropertyManagementController::class, 'index'])->name('property_management.index');
    Route::get('/create', [PropertyManagementController::class, 'create'])->name('property_management.create');
    Route::post('store', [PropertyManagementController::class, 'store'])->name('property_management.store');
    Route::get('/edit/{id}', [PropertyManagementController::class, 'edit'])->name('property_management.edit');
    Route::get('/show/{id}', [PropertyManagementController::class, 'show'])->name('property_management.show');
    Route::get('/view_image/{id}', [PropertyManagementController::class, 'view_image'])->name('property_management.view_image');
    Route::patch('/update/{id}', [PropertyManagementController::class, 'update'])->name('property_management.update');
    Route::get('delete', [PropertyManagementController::class, 'delete'])->name('property_management.delete');
    Route::get('/list_view/{id}', [PropertyManagementController::class, 'list_view'])->name('property_management.list_view');
});

// Block Management
Route::group(['prefix' => 'block_management', 'middleware' => 'auth:web'], function () {
    Route::get('/', [BlockManagementController::class, 'index'])->name('block_management.index');
    Route::get('/create', [BlockManagementController::class, 'create'])->name('block_management.create');
    Route::post('store', [BlockManagementController::class, 'store'])->name('block_management.store');
    Route::get('/edit/{id}', [BlockManagementController::class, 'edit'])->name('block_management.edit');
    Route::get('/show/{id}', [BlockManagementController::class, 'show'])->name('block_management.show');
    Route::get('/view_image/{id}', [BlockManagementController::class, 'view_image'])->name('block_management.view_image');
    Route::get('/list_view/{id}', [BlockManagementController::class, 'list_view'])->name('block_management.list_view');
    Route::patch('/update/{id}', [BlockManagementController::class, 'update'])->name('block_management.update');
    Route::get('delete', [BlockManagementController::class, 'delete'])->name('block_management.delete');
});

// Floors Management
Route::get('/search_master', [EnquiryController::class, 'search_master'])->name('search_master');
Route::group(['prefix' => 'floor_management', 'middleware' => 'auth:web'], function () {
    Route::get('/', [FloorManagementController::class, 'index'])->name('floor_management.index');
    Route::get('/create', [FloorManagementController::class, 'create'])->name('floor_management.create');
    Route::post('store', [FloorManagementController::class, 'store'])->name('floor_management.store');
    Route::get('/edit/{id}', [FloorManagementController::class, 'edit'])->name('floor_management.edit');
    Route::get('/show/{id}', [FloorManagementController::class, 'show'])->name('floor_management.show_floor');
    Route::get('/view_image/{id}', [FloorManagementController::class, 'view_image'])->name('floor_management.show');
    Route::patch('/update/{id}', [FloorManagementController::class, 'update'])->name('floor_management.update');
    Route::get('delete', [FloorManagementController::class, 'delete'])->name('floor_management.delete');
    Route::get('get_blocks_by_property_id/{id}', [FloorManagementController::class, 'get_blocks_by_property_id'])->name('floor_management.get_blocks_by_property_id');
    Route::post('status-update', [FloorManagementController::class, 'statusUpdate'])->name('floor_management.status-update');
});

// Units Management
Route::group(['prefix' => 'unit_management', 'middleware' => 'auth:web'], function () {
    Route::post('/units/reorder', [UnitManagementController::class, 'reorder'])->name('units.reorder');

    Route::get('/', [UnitManagementController::class, 'index'])->name('unit_management.index');
    Route::get('/create-new', [UnitManagementController::class, 'create_new'])->name('unit_management.create_new');
    Route::get('/create', [UnitManagementController::class, 'create'])->name('unit_management.create');
    Route::post('store', [UnitManagementController::class, 'store'])->name('unit_management.store');
    Route::get('/edit/{id}', [UnitManagementController::class, 'edit'])->name('unit_management.edit');
    Route::get('/show/{id}', [UnitManagementController::class, 'show'])->name('unit_management.show_image');
    Route::get('/view_image/{id}', [UnitManagementController::class, 'view_image'])->name('unit_management.show');
    Route::patch('/update/{id}', [UnitManagementController::class, 'update'])->name('unit_management.update');
    Route::get('delete', [UnitManagementController::class, 'delete'])->name('unit_management.delete');
    Route::post('status-update', [UnitManagementController::class, 'statusUpdate'])->name('unit_management.status-update');

    Route::get('get_blocks_by_property_id/{id}', [UnitManagementController::class, 'get_blocks_by_property_id'])->name('unit_management.get_blocks_by_property_id');
    Route::get('get_floors_by_block_id/{id}', [UnitManagementController::class, 'get_floors_by_block_id'])->name('unit_management.get_floors_by_block_id');
    Route::get('get_units_by_floor_id/{floor_id}/{block_id}/{property_id}', [UnitManagementController::class, 'get_units_by_floor_id'])->name('unit_management.get_units_by_floor_id');
});
Route::group(['prefix' => 'rent_price_list', 'middleware' => 'auth:web'], function () {
    Route::get('/', [RentPriceListController::class, 'index'])->name('rent_price.index');
    Route::get('/create', [RentPriceListController::class, 'create'])->name('rent_price.create');
    Route::get('/edit/{id}', [RentPriceListController::class, 'edit'])->name('rent_price.edit');
    Route::patch('/update/{id}', [RentPriceListController::class, 'update'])->name('rent_price.update');
    Route::post('/store', [RentPriceListController::class, 'store'])->name('rent_price.store');
    Route::get('/get_blocks_by_property_id_for_rent/{id}', [RentPriceListController::class, 'get_blocks_by_property_id_for_rent'])->name('rent_price.get_blocks_by_property_id_for_rent');
    Route::get('/get_floors_by_block_id_for_rent/{id}', [RentPriceListController::class, 'get_floors_by_block_id_for_rent'])->name('rent_price.get_floors_by_block_id_for_rent');
    Route::get('/get_units_by_floor_id_for_rent/{id}', [RentPriceListController::class, 'get_units_by_floor_id_for_rent'])->name('rent_price.get_units_by_floor_id_for_rent');
    Route::get('delete', [RentPriceListController::class, 'delete'])->name('rent_price.delete');
    Route::get('/get-units/{property_id}', [RentPriceListController::class, 'getUnits'])->name('rent_price.get_units');
    // Route::get('/get-blocks/{property}', [RentPriceListController::class, 'getBlocks'])->name('rent_price.get_units_filtered');
    // Route::get('/get-floors/{property}/{block}', [RentPriceListController::class, 'getFloors'])->name('rent_price.get_units_filtered');
    // Route::get('/get-units-filtered', [RentPriceListController::class, 'getUnitsFiltered'])->name('rent_price.get_units_filtered');
    Route::get('/get-blocks/{property}', [RentPriceListController::class, 'getBlocks'])->name('rent_price.get_blocks');
    Route::get('/get-floors/{property}/{block}', [RentPriceListController::class, 'getFloors'])->name('rent_price.get_floors');
    Route::get('/get-units', [RentPriceListController::class, 'getUnitsFiltered'])->name('rent_price.get_units_filtered');
});

################################################## End Propert Management #############################################

################################################## Start Propert Transactions #############################################

// Transactions Management
// Route::group(['prefix'=> 'transactions'], function () {
//     Route::get('/view_image/{id}', [PropertyManagementController::class,'view_image'])->name('property_transactions.check_propoerty');

// });

// Tenants Management
Route::group(['prefix' => 'tenant', 'middleware' => 'auth:web'], function () {
    Route::get('/', [TenantController::class, 'index'])->name('tenant.index');
    Route::get('/create', [TenantController::class, 'create'])->name('tenant.create');
    Route::post('store', [TenantController::class, 'store'])->name('tenant.store');
    Route::post('store_for_anything', [TenantController::class, 'store_for_anything'])->name('tenant.store_for_anything');
    Route::get('/edit/{id}', [TenantController::class, 'edit'])->name('tenant.edit');
    Route::get('/schedule/{id}', [TenantController::class, 'schedule'])->name('tenant.schedule');
    Route::get('/show/{id}', [TenantController::class, 'show'])->name('tenant.show');
    Route::patch('/update/{id}', [TenantController::class, 'update'])->name('tenant.update');
    Route::get('delete', [TenantController::class, 'delete'])->name('tenant.delete');
    Route::post('status-update', [TenantController::class, 'statusUpdate'])->name('tenant.status-update');
    Route::get('exportTenants', [TenantController::class, 'exportTenants'])->name('tenant.exportTenants');
});
// Enquiry Management

Route::group(['prefix' => 'proposal', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ProposalController::class, 'index'])->name('proposal.index');
    Route::get('/create', [ProposalController::class, 'create'])->name('proposal.create');
    Route::post('store', [ProposalController::class, 'store'])->name('proposal.store');
    Route::get('/edit/{id}', [ProposalController::class, 'edit'])->name('proposal.edit');
    Route::get('/show/{id}', [ProposalController::class, 'show'])->name('proposal.show');
    Route::patch('/update/{id}', [ProposalController::class, 'update'])->name('proposal.update');
    Route::get('delete', [ProposalController::class, 'delete'])->name('proposal.delete');
    Route::post('status-update', [ProposalController::class, 'statusUpdate'])->name('proposal.status-update');
    Route::get('/check_property/{id}', [ProposalController::class, 'check_property'])->name('proposal.check_property');
    Route::get('/view_image/{id}/{proposal_id}', [ProposalController::class, 'view_image'])->name('proposal.image_view');
    Route::get('/list_view/{id}/{proposal_id}', [ProposalController::class, 'list_view'])->name('proposal.list_view');
    Route::get('get_tenant/{id}', [ProposalController::class, 'get_tenant'])->name('proposal.get_tenant');
    Route::get('get_units', [ProposalController::class, 'get_units'])->name('proposal.get_units');
    Route::get('get_unit_service/{id}', [ProposalController::class, 'get_unit_service'])->name('proposal.get_unit_service');
    Route::get('add_to_booking/{id}', [ProposalController::class, 'add_to_booking'])->name('proposal.add_to_booking');
    Route::get('add_to_agreement/{id}', [ProposalController::class, 'add_to_agreement'])->name('proposal.add_to_agreement');
    Route::post('/store_to_booking', [ProposalController::class, 'store_to_booking'])->name('proposal.store_to_booking');
    Route::post('/store_to_agreement', [ProposalController::class, 'store_to_agreement'])->name('proposal.store_to_agreement');
    Route::get('/empty_unit_from_proposal_unit/{id}', [ProposalController::class, 'empty_unit_from_proposal_unit'])->name('proposal.empty_unit_from_proposal_unit');
    Route::get('/empty_unit_from_service_proposal/{id}', [ProposalController::class, 'empty_unit_from_service_proposal'])->name('proposal.empty_unit_from_service_proposal');
    Route::post('search', [ProposalController::class, 'search'])->name('proposal.search');
    Route::get('/create_with_select_unit', [ProposalController::class, 'create_with_select_unit'])->name('proposal.create_with_select_unit');
});

// Booking Management
Route::group(['prefix' => 'booking', 'middleware' => 'auth:web'], function () {
    Route::get('/', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/create', [BookingController::class, 'create'])->name('booking.create');
    Route::post('store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/edit/{id}', [BookingController::class, 'edit'])->name('booking.edit');
    Route::get('/show/{id}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/view_image/{id}', [BookingController::class, 'view_image'])->name('booking.show');
    Route::patch('/update/{id}', [BookingController::class, 'update'])->name('booking.update');
    Route::get('delete', [BookingController::class, 'delete'])->name('booking.delete');
    Route::post('status-update', [BookingController::class, 'statusUpdate'])->name('booking.status-update');
    Route::get('/view_image/{id}', [BookingController::class, 'view_image'])->name('booking.check_propoerty');
    Route::get('add_to_agreement/{id}', [BookingController::class, 'add_to_agreement'])->name('booking.add_to_agreement');
    Route::post('/store_to_agreement', [BookingController::class, 'store_to_agreement'])->name('booking.store_to_agreement');
    Route::get('get_unit_service/{id}', [BookingController::class, 'get_unit_service'])->name('booking.get_unit_service');
    Route::post('search', [BookingController::class, 'search'])->name('booking.search');

    Route::get('/create_with_select_unit', [BookingController::class, 'create_with_select_unit'])->name('booking.create_with_select_unit');

    Route::get('/check_property/{id}', [BookingController::class, 'check_property'])->name('booking.check_property');
    Route::get('/view_image/{id}/{booking_id}', [BookingController::class, 'view_image'])->name('booking.image_view');
    Route::get('/list_view/{id}/{booking_id}', [BookingController::class, 'list_view'])->name('booking.list_view');
    Route::get('/empty_unit_from_booking_unit/{id}', [BookingController::class, 'empty_unit_from_booking_unit'])->name('booking.empty_unit_from_booking_unit');
    Route::get('/empty_unit_from_service_booking/{id}', [BookingController::class, 'empty_unit_from_service_booking'])->name('booking.empty_unit_from_service_booking');

    Route::get('get_tenant/{id}', [BookingController::class, 'get_tenant'])->name('booking.get_tenant');
    Route::get('get_units', [BookingController::class, 'get_units'])->name('booking.get_units');
    // Route::get('get_floors_by_block_id/{id}', [EnquiryController::class, 'get_floors_by_block_id'])->name('enquiry.get_floors_by_block_id');
    // Route::get('get_units_by_floor_id/{floor_id}/{block_id}/{property_id}', [EnquiryController::class, 'get_units_by_floor_id'])->name('enquiry.get_units_by_floor_id');

});
Route::group(['prefix' => 'termination', 'middleware' => 'auth:web'], function () {
    Route::get('/', [TerminationController::class, 'index'])->name('termination.index');
    Route::get('/add_request/{id}', [TerminationController::class, 'create'])->name('termination.add');
    Route::post('/add_request', [TerminationController::class, 'store'])->name('termination.store');
    Route::get('/edit_request/{id}', [TerminationController::class, 'edit'])->name('termination.edit');
    Route::get('/rejected/{id}', [TerminationController::class, 'rejected'])->name('termination.rejected');
    Route::get('/approved/{id}', [TerminationController::class, 'approved'])->name('termination.approved');
    Route::patch('/update_request/{id}', [TerminationController::class, 'update'])->name('termination.update');
    Route::get('/delete', [TerminationController::class, 'delete'])->name('termination.delete');
});
Route::group(['prefix' => 'renewal', 'middleware' => 'auth:web'], function () {
    Route::get('/{id}', [TerminationController::class, 'renewal'])->name('renewal.create');
    Route::post('/store/{id}', [TerminationController::class, 'renewal_update'])->name('renewal.update');
});

// Agreement Management
Route::group(['prefix' => 'agreement', 'middleware' => 'auth:web'], function () {
    Route::get('/', [AgreementController::class, 'index'])->name('agreement.index');
    Route::get('/create', [AgreementController::class, 'create'])->name('agreement.create');
    Route::post('store', [AgreementController::class, 'store'])->name('agreement.store');
    Route::get('/edit/{id}', [AgreementController::class, 'edit'])->name('agreement.edit');
    Route::get('/review/{id}', [AgreementController::class, 'review'])->name('agreement.review');
    Route::patch('/update_review', [AgreementController::class, 'update_review'])->name('agreement.update_review');
    Route::get('/show/{id}', [AgreementController::class, 'show'])->name('agreement.show_info');
    Route::get('/view_image/{id}', [AgreementController::class, 'view_image'])->name('agreement.show');
    Route::patch('/update/{id}', [AgreementController::class, 'update'])->name('agreement.update');
    Route::get('delete', [AgreementController::class, 'delete'])->name('agreement.delete');
    Route::post('status-update', [AgreementController::class, 'statusUpdate'])->name('agreement.status-update');
    Route::get('/signed/{id}', [AgreementController::class, 'signed'])->name('agreement.signed');
    Route::get('get_unit_service/{id}', [AgreementController::class, 'get_unit_service'])->name('agreement.get_unit_service');
    Route::get('/empty_unit_from_service_agreement/{id}', [AgreementController::class, 'empty_unit_from_service_agreement'])->name('agreement.empty_unit_from_service_agreement');
    // Route::get('/show/{id}', [AgreementController::class, 'show'])->name('agreement.show');

    Route::get('/create_with_select_unit', [AgreementController::class, 'create_with_select_unit'])->name('agreement.create_with_select_unit');

    Route::get('/check_property/{id}', [AgreementController::class, 'check_property'])->name('agreement.check_property');
    Route::get('/view_image/{id}', [AgreementController::class, 'view_image'])->name('agreement.image_view');
    Route::get('/list_view/{id}', [AgreementController::class, 'list_view'])->name('agreement.list_view');
    Route::post('search', [AgreementController::class, 'search'])->name('agreement.search');

    Route::get('get_tenant/{id}', [AgreementController::class, 'get_tenant'])->name('agreement.get_tenant');
    Route::get('get_units', [AgreementController::class, 'get_units'])->name('agreement.get_units');
    Route::get('schedule/{id}', [AgreementController::class, 'schedule'])->name('agreement.schedule');
    // Route::get('get_floors_by_block_id/{id}', [EnquiryController::class, 'get_floors_by_block_id'])->name('enquiry.get_floors_by_block_id');
    // Route::get('get_units_by_floor_id/{floor_id}/{block_id}/{property_id}', [EnquiryController::class, 'get_units_by_floor_id'])->name('enquiry.get_units_by_floor_id');

});
Route::get('/general_view_image', [EnquiryController::class, 'general_view_image'])->name('general_image_view');
Route::get('/general_list_view', [EnquiryController::class, 'general_list_view'])->name('general_list_view');
// Enquiry Management
Route::group(['prefix' => 'enquiry', 'middleware' => 'auth:web'], function () {
    Route::get('/', [EnquiryController::class, 'index'])->name('enquiry.index');
    Route::get('/create', [EnquiryController::class, 'create'])->name('enquiry.create');
    Route::get('/create_with_select_unit', [EnquiryController::class, 'create_with_select_unit'])->name('enquiry.create_with_select_unit');
    Route::post('store', [EnquiryController::class, 'store'])->name('enquiry.store');
    Route::post('search', [EnquiryController::class, 'search'])->name('enquiry.search');
    Route::get('/edit/{id}', [EnquiryController::class, 'edit'])->name('enquiry.edit');
    Route::get('/add_to_proposal/{id}', [EnquiryController::class, 'add_to_proposal'])->name('enquiry.add_to_proposal');
    Route::post('/store_to_proposal', [EnquiryController::class, 'store_to_proposal'])->name('enquiry.store_to_proposal');
    Route::get('/show/{id}', [EnquiryController::class, 'show'])->name('enquiry.show_enquiry');
    Route::get('/view_image/{id}', [EnquiryController::class, 'view_image'])->name('enquiry.show');
    Route::patch('/update/{id}', [EnquiryController::class, 'update'])->name('enquiry.update');
    Route::get('delete', [EnquiryController::class, 'delete'])->name('enquiry.delete');
    Route::post('status-update', [EnquiryController::class, 'statusUpdate'])->name('enquiry.status-update');
    Route::get('/check_property/{id}', [EnquiryController::class, 'check_property'])->name('enquiry.check_propoerty');
    Route::get('/view_image/{id}/{enquiry_id}', [EnquiryController::class, 'view_image'])->name('enquiry.image_view');
    Route::get('/list_view/{id}/{enquiry_id}', [EnquiryController::class, 'list_view'])->name('enquiry.list_view');
    Route::get('/empty_unit_from_enquiry_unit_search/{id}', [EnquiryController::class, 'empty_unit_from_enquiry_unit_search'])->name('enquiry.empty_unit_from_enquiry_unit_search');
    Route::get('/empty_unit_from_enquiry_unit/{id}', [EnquiryController::class, 'empty_unit_from_enquiry_unit'])->name('enquiry.empty_unit_from_enquiry_unit');

    Route::get('get_tenant/{id}', [EnquiryController::class, 'get_tenant'])->name('enquiry.get_tenant');
    Route::get('get_service_master/{id}', [EnquiryController::class, 'get_service_master'])->name('enquiry.get_service_master');
    // Route::get('get_floors_by_block_id/{id}', [EnquiryController::class, 'get_floors_by_block_id'])->name('enquiry.get_floors_by_block_id');
    // Route::get('get_units_by_floor_id/{floor_id}/{block_id}/{property_id}', [EnquiryController::class, 'get_units_by_floor_id'])->name('enquiry.get_units_by_floor_id');

});
Route::get('/general_check_property', [EnquiryController::class, 'general_check_property'])->name('enquiry.general_check_property');

################################################## End Propert Transactions #############################################

################################################## End Facility Master #############################################

// Departments
Route::group(['prefix' => 'department', 'middleware' => 'auth:web'], function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('store', [DepartmentController::class, 'store'])->name('department.store');
    Route::get('/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::patch('/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::get('delete', [DepartmentController::class, 'delete'])->name('department.delete');
    Route::post('/status-update', [DepartmentController::class, 'statusUpdate'])->name('department.status-update');
});

// Employee Type
Route::group(['prefix' => 'employee_type', 'middleware' => 'auth:web'], function () {
    Route::get('/', [EmployeeTypeController::class, 'index'])->name('employee_type.index');
    Route::post('store', [EmployeeTypeController::class, 'store'])->name('employee_type.store');
    Route::get('/edit/{id}', [EmployeeTypeController::class, 'edit'])->name('employee_type.edit');
    Route::patch('/update/{id}', [EmployeeTypeController::class, 'update'])->name('employee_type.update');
    Route::get('delete', [EmployeeTypeController::class, 'delete'])->name('employee_type.delete');
    Route::post('/status-update', [EmployeeTypeController::class, 'statusUpdate'])->name('employee_type.status-update');
});

// Employee
Route::group(['prefix' => 'employees', 'middleware' => 'auth:web'], function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('employee.index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('employee.create');
    Route::post('store', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
    Route::patch('/update/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::get('delete', [EmployeeController::class, 'delete'])->name('employee.delete');
    Route::post('/status-update', [EmployeeController::class, 'statusUpdate'])->name('employee.status-update');
});

// Agent
Route::group(['prefix' => 'agent', 'middleware' => 'auth:web'], function () {
    Route::get('/', [AgentController::class, 'index'])->name('agent.index');
    Route::get('/create', [AgentController::class, 'create'])->name('agent.create');
    Route::post('store', [AgentController::class, 'store'])->name('agent.store');
    Route::get('/edit/{id}', [AgentController::class, 'edit'])->name('agent.edit');
    Route::get('/show/{id}', [AgentController::class, 'show'])->name('agent.show');
    Route::patch('/update/{id}', [AgentController::class, 'update'])->name('agent.update');
    Route::get('delete', [AgentController::class, 'delete'])->name('agent.delete');
    Route::post('status-update', [AgentController::class, 'statusUpdate'])->name('agent.status-update');
});

// Complaint Cataegory
Route::group(['prefix' => 'complaint_category', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ComplaintCategoryController::class, 'index'])->name('complaint_category.index');
    Route::get('/create', [ComplaintCategoryController::class, 'create'])->name('complaint_category.create');
    Route::post('store', [ComplaintCategoryController::class, 'store'])->name('complaint_category.store');
    Route::get('/edit/{id}', [ComplaintCategoryController::class, 'edit'])->name('complaint_category.edit');
    Route::patch('/update/{id}', [ComplaintCategoryController::class, 'update'])->name('complaint_category.update');
    Route::get('delete', [ComplaintCategoryController::class, 'delete'])->name('complaint_category.delete');
    Route::post('/status-update', [ComplaintCategoryController::class, 'statusUpdate'])->name('complaint_category.status-update');
});

// Complaint
Route::group(['prefix' => 'main_complaint', 'middleware' => 'auth:web'], function () {
    Route::get('/', [MainComplaintController::class, 'index'])->name('main_complaint.index');
    Route::get('/create', [MainComplaintController::class, 'create'])->name('main_complaint.create');
    Route::post('store', [MainComplaintController::class, 'store'])->name('main_complaint.store');
    Route::get('/edit/{id}', [MainComplaintController::class, 'edit'])->name('main_complaint.edit');
    Route::patch('/update/{id}', [MainComplaintController::class, 'update'])->name('main_complaint.update');
    Route::get('delete', [MainComplaintController::class, 'delete'])->name('main_complaint.delete');
    Route::post('/status-update', [MainComplaintController::class, 'statusUpdate'])->name('main_complaint.status-update');
});

// Work Status
Route::group(['prefix' => 'work_status', 'middleware' => 'auth:web'], function () {
    Route::get('/', [WorkStatusController::class, 'index'])->name('work_status.index');
    Route::get('/create', [WorkStatusController::class, 'create'])->name('work_status.create');
    Route::post('store', [WorkStatusController::class, 'store'])->name('work_status.store');
    Route::get('/edit/{id}', [WorkStatusController::class, 'edit'])->name('work_status.edit');
    Route::patch('/update/{id}', [WorkStatusController::class, 'update'])->name('work_status.update');
    Route::get('delete', [WorkStatusController::class, 'delete'])->name('work_status.delete');
    Route::post('/status-update', [WorkStatusController::class, 'statusUpdate'])->name('work_status.status-update');
});

// Asset Group
Route::group(['prefix' => 'asset_group', 'middleware' => 'auth:web'], function () {
    Route::get('/', [AssetGroupController::class, 'index'])->name('asset_group.index');
    Route::get('/create', [AssetGroupController::class, 'create'])->name('asset_group.create');
    Route::post('store', [AssetGroupController::class, 'store'])->name('asset_group.store');
    Route::get('/edit/{id}', [AssetGroupController::class, 'edit'])->name('asset_group.edit');
    Route::patch('/update/{id}', [AssetGroupController::class, 'update'])->name('asset_group.update');
    Route::get('delete', [AssetGroupController::class, 'delete'])->name('asset_group.delete');
    Route::post('/status-update', [AssetGroupController::class, 'statusUpdate'])->name('asset_group.status-update');
});

// Priority
Route::group(['prefix' => 'priority', 'middleware' => 'auth:web'], function () {
    Route::get('/', [PriorityController::class, 'index'])->name('priority.index');
    Route::get('/create', [PriorityController::class, 'create'])->name('priority.create');
    Route::post('store', [PriorityController::class, 'store'])->name('priority.store');
    Route::get('/edit/{id}', [PriorityController::class, 'edit'])->name('priority.edit');
    Route::patch('/update/{id}', [PriorityController::class, 'update'])->name('priority.update');
    Route::get('delete', [PriorityController::class, 'delete'])->name('priority.delete');
    Route::post('/status-update', [PriorityController::class, 'statusUpdate'])->name('priority.status-update');
});

// Freezing
Route::group(['prefix' => 'freezing', 'middleware' => 'auth:web'], function () {
    Route::get('/', [FreezingController::class, 'index'])->name('freezing.index');
    Route::get('/create', [FreezingController::class, 'create'])->name('freezing.create');
    Route::post('store', [FreezingController::class, 'store'])->name('freezing.store');
    Route::get('/edit/{id}', [FreezingController::class, 'edit'])->name('freezing.edit');
    Route::patch('/update/{id}', [FreezingController::class, 'update'])->name('freezing.update');
    Route::get('delete', [FreezingController::class, 'delete'])->name('freezing.delete');
    Route::post('/status-update', [FreezingController::class, 'statusUpdate'])->name('freezing.status-update');
});

// Supplier
Route::group(['prefix' => 'supplier', 'middleware' => 'auth:web'], function () {
    Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::patch('/update/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::get('delete', [SupplierController::class, 'delete'])->name('supplier.delete');
    Route::post('/status-update', [SupplierController::class, 'statusUpdate'])->name('supplier.status-update');
});

// Amc Providers
Route::group(['prefix' => 'amc_providers', 'middleware' => 'auth:web'], function () {
    Route::get('/', [AMCProviderController::class, 'index'])->name('amc_provider.index');
    Route::get('/create', [AMCProviderController::class, 'create'])->name('amc_provider.create');
    Route::post('store', [AMCProviderController::class, 'store'])->name('amc_provider.store');
    Route::get('/edit/{id}', [AMCProviderController::class, 'edit'])->name('amc_provider.edit');
    Route::patch('/update/{id}', [AMCProviderController::class, 'update'])->name('amc_provider.update');
    Route::get('delete', [AMCProviderController::class, 'delete'])->name('amc_provider.delete');
    Route::post('/status-update', [AMCProviderController::class, 'statusUpdate'])->name('amc_provider.status-update');
});

// Asset
Route::group(['prefix' => 'asset', 'middleware' => 'auth:web'], function () {
    Route::get('/', [AssetController::class, 'index'])->name('asset.index');
    Route::get('/create', [AssetController::class, 'create'])->name('asset.create');
    Route::post('store', [AssetController::class, 'store'])->name('asset.store');
    Route::get('/edit/{id}', [AssetController::class, 'edit'])->name('asset.edit');
    Route::patch('/update/{id}', [AssetController::class, 'update'])->name('asset.update');
    Route::get('delete', [AssetController::class, 'delete'])->name('asset.delete');
    Route::post('/status-update', [AssetController::class, 'statusUpdate'])->name('asset.status-update');
    Route::get('schedule/{id}', [AssetController::class, 'schedule_list'])->name('asset.schedule');

    // Route::patch('/update-status-freezing/{id}' , [ComplaintRegisterController::class , 'freezedComplaint'])->name('complaint_registration.freezedComplaint');

});

################################################## End Facility Master #############################################

################################################## Start Property Reports #############################################

// Schedules
Route::group(['prefix' => 'schedules', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
    // Route::get('/create', [ScheduleController::class,'create'])->name('shedules.create');
    // Route::post('store', [ScheduleController::class,'store'])->name('shedules.store');
    // Route::get('/edit/{id}', [ScheduleController::class,'edit'])->name('shedules.edit');
    // Route::patch('/update/{id}', [ScheduleController::class,'update'])->name('shedules.update');
    // Route::get('delete', [ScheduleController::class, 'delete'])->name('shedules.delete');
    // Route::post('/status-update', [ScheduleController::class, 'statusUpdate'])->name('shedules.status-update');

});

// Invoice
Route::group(['prefix' => 'invoice', 'middleware' => 'auth:web'], function () {
    Route::post('/create', [InvoiceController::class, 'storeInvoice'])->name('invoice_generate.store');
    // Route::get('/', [ScheduleController::class,'create'])->name('invoice.index');
    Route::get('/all_invoices', [InvoiceController::class, 'index'])->name('invoices.all_invoices');
    Route::get('/print/{id}', [InvoiceController::class, 'print'])->name('invoices.print_pdf');
    Route::get('/invoice/{id}/pdf', [InvoiceController::class, 'generate_invoice'])->name('invoice.pdf');

    // Route::post('store', [ScheduleController::class,'store'])->name('shedules.store');
    // Route::get('/edit/{id}', [ScheduleController::class,'edit'])->name('shedules.edit');
    // Route::patch('/update/{id}', [ScheduleController::class,'update'])->name('shedules.update');
    // Route::get('delete', [ScheduleController::class, 'delete'])->name('shedules.delete');
    // Route::post('/status-update', [ScheduleController::class, 'statusUpdate'])->name('shedules.status-update');

});

// Sales Return 
Route::group(['prefix' => 'sales-return', 'middleware' => 'auth:web'], function () {
    Route::post('/create', [InvoiceReturnController::class, 'storeInvoice'])->name('invoice_return_generate.store');
    Route::get('/get-tenant-invoices', [InvoiceReturnController::class, 'get_tenant_invoices'])->name('get.tenant.invoices');
    Route::get('/all_invoices', [InvoiceReturnController::class, 'index'])->name('invoices_return.all_invoices');
    Route::get('/print/{id}', [InvoiceReturnController::class, 'print'])->name('invoices_return.print_pdf');
    Route::get('/invoice/{id}/pdf', [InvoiceReturnController::class, 'generate_invoice'])->name('invoice_return.pdf');
    Route::get('/invoice_return_create', [InvoiceReturnController::class, 'invoice_return_create'])->name('invoice_return_create');
    Route::post('/sales-return-store', [InvoiceReturnController::class, 'sales_return_store'])->name('sales_return.store');
});
// reports
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
// Complaint
Route::group(['prefix' => 'facility_transactions/complaint_registration', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ComplaintRegisterController::class, 'index'])->name('complaint_registration');
    // Route::get('/open' , [ComplaintRegisterController::class , 'OpenComplaint'])->name('opened.complaint_registration');
    // Route::get('/freezed' , [ComplaintRegisterController::class , 'FreezedComplaintIndex'])->name('freezed.complaint_registration');
    // Route::get('/closed' , [ComplaintRegisterController::class , 'ClosedComplaintIndex'])->name('closed.complaint_registration');
    Route::get('/create', [ComplaintRegisterController::class, 'create'])->name('complaint_registration.create');
    Route::post('/store', [ComplaintRegisterController::class, 'storeComplaint'])->name('complaint_registration.storeComplaint');
    Route::get('/show/{id}', [ComplaintRegisterController::class, 'showComplaint'])->name('complaint_registration.showComplaint');
    Route::get('/edit/{id}', [ComplaintRegisterController::class, 'editComplaint'])->name('complaint_registration.editComplaint');
    Route::patch('/update/{id}', [ComplaintRegisterController::class, 'updateComplaint'])->name('complaint_registration.updateComplaint');
    Route::get('/delete', [ComplaintRegisterController::class, 'deleteComplaint'])->name('complaint_registration.deleteComplaint');
    Route::patch('/update-status-freezing/{id}', [ComplaintRegisterController::class, 'freezedComplaint'])->name('complaint_registration.freezedComplaint');
    Route::patch('/closedComplaint/{id}', [ComplaintRegisterController::class, 'closedComplaint'])->name('complaint_registration.closedComplaint');
    Route::get('/external_files/view/{id}', [ComplaintRegisterController::class, 'viewPdf'])->name('complaint_registration.documents_view');
    Route::get('/show_logs/{id}', [ComplaintRegisterController::class, 'show_logs'])->name('complaint_registration.show_logs');
    Route::get('/get_employees_departments/{id}', [ComplaintRegisterController::class, 'get_employees_departments'])->name('get_employees_departments');
    Route::get('/get_employees_departments_complaint/{id}', [ComplaintRegisterController::class, 'get_employees_departments_complaint'])->name('get_employees_departments_complaint');
    Route::patch('/assign_to_employee', [ComplaintRegisterController::class, 'assign_to_employee'])->name('complaint_registration.assign_to_employee');
    Route::patch('/assign_to_department', [ComplaintRegisterController::class, 'assign_to_department'])->name('complaint_registration.assign_to_department');
});

// Complaint
Route::group(['prefix' => 'facility_reports', 'middleware' => 'auth:web'], function () {
    Route::get('/open', [ComplaintRegisterController::class, 'OpenComplaint'])->name('facility_reports.open');
    Route::get('/freezed', [ComplaintRegisterController::class, 'FreezedComplaintIndex'])->name('facility_reports.freezed');
    Route::get('/closed', [ComplaintRegisterController::class, 'ClosedComplaintIndex'])->name('facility_reports.closed');
});

################################################## Start Collections ######################################################
// Route::group(['prefix'=> 'cards'], function () {
//     Route::get('/', [CardsController::class,'index'])->name('cards.index');
// });
Route::group(['prefix' => 'receipts', 'middleware' => 'auth:web'], function () {
    Route::get('/', [ReceiptController::class, 'index'])->name('receipts.list');
    Route::get('/add_receipt/{id}', [ReceiptController::class, 'add_receipt'])->name('receipts.add_receipt_for_invoice');
    Route::get('/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::get('/edit/{id}', [ReceiptController::class, 'edit'])->name('receipts.edit');
    Route::post('/store', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::post('/store_receipt', [ReceiptController::class, 'store_receipt'])->name('receipts.store_receipt');
    Route::patch('/update/{id}', [ReceiptController::class, 'update'])->name('receipts.update');
    Route::get('/delete', [ReceiptController::class, 'delete'])->name('receipts.delete');
    Route::post('/status-update', [ReceiptController::class, 'statusUpdate'])->name('receipts.status-update');
    Route::get('/get_receipt_type_id/{id}', [ReceiptController::class, 'get_receipt_type_id'])->name('get_voucher_type_id');
    Route::get('/print_receipt/{id}', [ReceiptController::class, 'print_receipt'])->name('receipts.print_receipt');
    Route::get('/receipt/{id}/pdf', [ReceiptController::class, 'generate_receipt'])->name('receipt.pdf');
});
################################################## End Collections ######################################################
################################################## End Property Reports #############################################

// another routes

Route::get('get_country/{id}', [CountryController::class, 'get_country'])->name('get_country');
Route::get('get_country_master/{id}', [CompanyManagementController::class, 'get_country'])->name('get_country_master');
Route::get('get_blocks_by_property', [BlockManagementController::class, 'get_blocks_by_property'])->name('get_blocks_by_property');
Route::get('/get_units/{id}', [ComplaintRegisterController::class, 'getUnits'])->name('get_tenant_units_for_complaint');
Route::get('/get_sub_complaint_categories/{id}', [ComplaintRegisterController::class, 'getComplaintCategories'])->name('get_sub_complaint_categories');
Route::get('/get_sub_complaint_departments/{id}', [ComplaintRegisterController::class, 'getComplaintDepartments'])->name('get_sub_complaint_departments_categories');
Route::get('/get-receipt-no', function (Request $request) {
    return response()->json(receiptNo($request->id));
})->name('get_receipt_no');

##################################################################################################################

// Add Building To Sub Groups
Route::get('add_property_to_cost', function () {
    $units   = PropertyManagement::forUser()->get();
    $company = auth()->user() ?? User::first();
    foreach ($units as $unit_management) {
        $ledger = CostCenterCategory::create([
            'code'      => $unit_management->code,
            'name'      => $unit_management->name,
            'main_id'   => $unit_management->id,
            'main_type' => 'property',
            'status'    => 'active',
        ]);
    }
});
Route::get('add_unit_to_cost', function () {
    $units   = UnitManagement::get();
    $company = auth()->user() ?? User::first();
    foreach ($units as $unit_management_pain) {
        $property  = CostCenterCategory::where('main_id', $unit_management_pain->property_management_id)->where('main_type', 'property')->first();
        $unit_cost = CostCenter::create([

            'name'                    => $unit_management_pain->property_unit_management->name .
                '-' .
                $unit_management_pain->unit_management_main->name .
                '-' .
                $unit_management_pain->block_unit_management->block->name .
                '-' .
                $unit_management_pain->floor_unit_management->floor_management_main->name . '-' . $unit_management_pain->unit_management_main->name,
            'main_id'                 => $unit_management_pain->id,
            'main_type'               => 'unit',
            'cost_center_category_id' => $property->id,
            'status'                  => 'active',
        ]);
    }
});

Route::group(['prefix' => 'language'], function () {
    Route::get('', [LanguageController::class, 'index'])->name('admin.business-settings.language.index');
    Route::post('add-new', [LanguageController::class, 'store'])->name('admin.business-settings.language.add-new');
    Route::get('update-status', [LanguageController::class, 'update_status'])->name('admin.business-settings.language.update-status');
    Route::get('update-default-status', [LanguageController::class, 'update_default_status'])->name('admin.business-settings.language.update-default-status');
    Route::post('update', [LanguageController::class, 'update'])->name('admin.business-settings.language.update');
    Route::get('translate/{lang}', [LanguageController::class, 'translate'])->name('admin.business-settings.language.translate');
    Route::get('translate-list/{lang}', [LanguageController::class, 'translate_list'])->name('admin.business-settings.language.translate.list');
    Route::post('translate-submit/{lang}', [LanguageController::class, 'translate_submit'])->name('admin.business-settings.language.translate-submit');
    Route::post('remove-key/{lang}', [LanguageController::class, 'translate_key_remove'])->name('admin.business-settings.language.remove-key');
    Route::get('delete/{lang}', [LanguageController::class, 'delete'])->name('admin.business-settings.language.delete');
    Route::any('auto-translate/{lang}', [LanguageController::class, 'auto_translate'])->name('admin.business-settings.language.auto-translate');
});

// });
// Route::get('add_sub_groups' , function(){
//     $units   = Asset::get();
//     $company = auth()->user() ?? User::first();

//      foreach($units as $asset){
//         $asset_group   = AssetGroup::where('id',  $asset->asset_group_id )->first();
//         $group   = Groups::where('name', 'LIKE' , "%{$asset_group->name}%")->first();
//         $ledger = MainLedger::create([
//             'code'                => $asset->code,
//             'name'                => $asset->name,
//             'currency'            => $company->currency_code,
//             'country_id'          => $company->countryid,
//             'group_id'            => $group->id,
//             'is_taxable'          => $group->is_taxable ?: 0,
//             'vat_applicable_from' => $group->vat_applicable_from ?? null,
//             'tax_rate'            => $group->tax_rate ?: 0,
//             'tax_applicable'      => $group->tax_applicable ?: 0,
//             'status'              => 'active',
//         ]);
//     }
// });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



// Investment Management
Route::group(['prefix' => 'investments', 'middleware' => 'auth:web'], function () {
    Route::get('/', [InvestmentController::class, 'investments_list'])->name('investment.index');
    Route::get('/create', [InvestmentController::class, 'create'])->name('investment.create');
    Route::post('store', [InvestmentController::class, 'store'])->name('investment.store');
    Route::post('store_for_anything', [TenantController::class, 'store_for_anything'])->name('tenant.store_for_anything');
    Route::get('/edit/{id}', [InvestmentController::class, 'edit'])->name('investment.edit');
    Route::patch('/update/{id}', [InvestmentController::class, 'update'])->name('investment.update');
    Route::get('delete', [InvestmentController::class, 'delete'])->name('investment.delete');
});
// Investor Management
Route::group(['prefix' => 'investors', 'middleware' => 'auth:web'], function () {
    Route::get('/', [InvestorController::class, 'investors_list'])->name('investor.index');
    Route::get('/create', [InvestorController::class, 'create'])->name('investor.create');
    Route::post('store', [InvestorController::class, 'store'])->name('investor.store');
    Route::post('store_for_anything', [InvestorController::class, 'store_for_anything'])->name('investor.store_for_anything');
    Route::get('/edit/{id}', [InvestorController::class, 'edit'])->name('investor.edit');
    Route::patch('/update/{id}', [InvestorController::class, 'update'])->name('investor.update');
    Route::get('delete', [InvestorController::class, 'delete'])->name('investor.delete');
});

// Room Reservation 

// --------------- Master ------------------------

Route::group(['prefix' => 'room_reservation/master', 'middleware' => 'auth:web'], function () {

    // ------------------------ customer types ----------------------
    Route::group(['prefix' => 'customer', 'middleware' => 'auth:web'], function () {
        Route::get('list', [CustomerController::class, 'index'])->name('customer.list');
        Route::post('store', [CustomerController::class, 'store'])->name('customer.store');
        Route::patch('update', [CustomerController::class, 'update'])->name('customer.update');
        Route::get('delete', [CustomerController::class, 'delete'])->name('customer.delete');
        Route::get('edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    });
    // ------------------------ rental types ----------------------
    Route::group(['prefix' => 'rental-type', 'middleware' => 'auth:web'], function () {
        Route::get('list', [RentalTypeController::class, 'index'])->name('rental_type.list');
        Route::post('store', [RentalTypeController::class, 'store'])->name('rental_type.store');
        Route::patch('update', [RentalTypeController::class, 'update'])->name('rental_type.update');
        Route::get('delete', [RentalTypeController::class, 'delete'])->name('rental_type.delete');
        Route::get('edit/{id}', [RentalTypeController::class, 'edit'])->name('rental_type.edit');
    });
    // ------------------------ room types ----------------------
    Route::group(['prefix' => 'room-types', 'middleware' => 'auth:web'], function () {
        Route::get('list', [RoomTypeController::class, 'index'])->name('room_type.list');
        Route::post('store', [RoomTypeController::class, 'store'])->name('room_type.store');
        Route::patch('update', [RoomTypeController::class, 'update'])->name('room_type.update');
        Route::get('delete', [RoomTypeController::class, 'delete'])->name('room_type.delete');
        Route::get('edit/{id}', [RoomTypeController::class, 'edit'])->name('room_type.edit');
    });

    // ------------------------ room facilities ----------------------
    Route::group(['prefix' => 'room-facilities', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomFacilityController::class, 'index'])->name('room_facility.list');
        Route::post('store', [RoomFacilityController::class, 'store'])->name('room_facility.store');
        Route::patch('/update', [RoomFacilityController::class, 'update'])->name('room_facility.update');
        Route::get('delete', [RoomFacilityController::class, 'delete'])->name('room_facility.delete');
        Route::get('edit/{id}', [RoomTypeController::class, 'edit'])->name('room_facility.edit');
    });

    // ------------------------ room options ----------------------
    Route::group(['prefix' => 'room-options', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomOptionController::class, 'index'])->name('room_option.list');
        Route::post('store', [RoomOptionController::class, 'store'])->name('room_option.store');
        Route::patch('/update', [RoomOptionController::class, 'update'])->name('room_option.update');
        Route::get('delete', [RoomOptionController::class, 'delete'])->name('room_option.delete');
        Route::get('edit/{id}', [RoomOptionController::class, 'edit'])->name('room_option.edit');
    });

    // ------------------------ room meeting ----------------------
    Route::group(['prefix' => 'meeting-room', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [MeetingRoomController::class, 'index'])->name('meeting_room.list');
        Route::get('/create', [MeetingRoomController::class, 'create'])->name('meeting_room.create');
        Route::post('store', [MeetingRoomController::class, 'store'])->name('meeting_room.store');
        Route::patch('/update', [MeetingRoomController::class, 'update'])->name('meeting_room.update');
        Route::get('delete', [MeetingRoomController::class, 'delete'])->name('meeting_room.delete');
        Route::get('edit/{id}', [MeetingRoomController::class, 'edit'])->name('meeting_room.edit');
    });

    // ------------------------ room status ----------------------
    Route::group(['prefix' => 'room-status', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomStatusController::class, 'index'])->name('room_status.list');
        Route::post('store', [RoomStatusController::class, 'store'])->name('room_status.store');
        Route::patch('/update', [RoomStatusController::class, 'update'])->name('room_status.update');
        Route::get('delete', [RoomStatusController::class, 'delete'])->name('room_status.delete');
        Route::get('edit/{id}', [RoomStatusController::class, 'edit'])->name('room_status.edit');
    });


    // ------------------------ room building ----------------------
    Route::group(['prefix' => 'room-building', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomBuildingController::class, 'index'])->name('room_building.list');
        Route::post('store', [RoomBuildingController::class, 'store'])->name('room_building.store');
        Route::patch('/update', [RoomBuildingController::class, 'update'])->name('room_building.update');
        Route::get('delete', [RoomBuildingController::class, 'delete'])->name('room_building.delete');
        Route::get('edit/{id}', [RoomBuildingController::class, 'edit'])->name('room_building.edit');
    });
    // ------------------------ room block ----------------------
    Route::group(['prefix' => 'room-block', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomBlockController::class, 'index'])->name('room_block.list');
        Route::post('store', [RoomBlockController::class, 'store'])->name('room_block.store');
        Route::patch('/update', [RoomBlockController::class, 'update'])->name('room_block.update');
        Route::get('delete', [RoomBlockController::class, 'delete'])->name('room_block.delete');
        Route::get('edit/{id}', [RoomBlockController::class, 'edit'])->name('room_block.edit');
    });
    // ------------------------ room floor ----------------------
    Route::group(['prefix' => 'room-floor', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomFloorController::class, 'index'])->name('room_floor.list');
        Route::post('store', [RoomFloorController::class, 'store'])->name('room_floor.store');
        Route::patch('/update', [RoomFloorController::class, 'update'])->name('room_floor.update');
        Route::get('delete', [RoomFloorController::class, 'delete'])->name('room_floor.delete');
        Route::get('edit/{id}', [RoomFloorController::class, 'edit'])->name('room_floor.edit');
        Route::get('/get-blocks-by-building/{building_id}', [RoomFloorController::class, 'getBlocks'])->name('room_floor.get_blocks');
    });
    // ------------------------ room unit ----------------------
    Route::group(['prefix' => 'room-unit', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [RoomController::class, 'index'])->name('room_unit.list');
        Route::post('store', [RoomController::class, 'store'])->name('room_unit.store');
        Route::patch('/update', [RoomController::class, 'update'])->name('room_unit.update');
        Route::get('delete', [RoomController::class, 'delete'])->name('room_unit.delete');
        Route::get('edit/{id}', [RoomController::class, 'edit'])->name('room_unit.edit');
        Route::get('/get-floors-by-building-and-block/{building_id}/{block_id}', [RoomController::class, 'get_floors'])->name('room_unit.get_floors');
    });
});
Route::group(['prefix' => 'room_reservation/booking', 'middleware' => 'auth:web'], function () {

    // ------------------------ room types ----------------------
    Route::group(['prefix' => 'booking-room', 'middleware' => 'auth:web'], function () {
        Route::get('book-now', [BookingRoomController::class, 'index'])->name('booking_room.book_now');
        Route::get('list', [BookingRoomController::class, 'list'])->name('booking_room.list');
        Route::get('check-in-page', [BookingRoomController::class, 'check_in_page'])->name('booking_room.check_in_page');
        Route::get('check-in/{id}', [BookingRoomController::class, 'check_in'])->name('booking_room.check_in');
        Route::post('check-in/{id}', [BookingRoomController::class, 'submitCheckin'])->name('booking.checkin.submit');
        Route::post('check-in-dire', [BookingRoomController::class, 'submitCheckinDir'])->name('booking.checkin_dir.submit');
        Route::get('check-out/{id}', [BookingRoomController::class, 'submitCheckOut'])->name('booking.checkout.submit');
        Route::get('create', [BookingRoomController::class, 'create'])->name('booking_room.create');
        Route::post('store', [BookingRoomController::class, 'store'])->name('booking_room.store');
    });

    // ------------------------ room meeting booking----------------------
    Route::group(['prefix' => 'meeting-room-booking', 'middleware' => 'auth:web'], function () {
        Route::get('/list', [MeetingRoomBookingController::class, 'index'])->name('meeting_room_booking.list');
        Route::get('/create', [MeetingRoomBookingController::class, 'create'])->name('meeting_room_booking.create');
        Route::post('store', [MeetingRoomBookingController::class, 'store'])->name('meeting_room_booking.store');
        Route::patch('/update', [MeetingRoomBookingController::class, 'update'])->name('meeting_room_booking.update');
        Route::get('delete', [MeetingRoomBookingController::class, 'delete'])->name('meeting_room_booking.delete');
        Route::get('edit/{id}', [MeetingRoomBookingController::class, 'edit'])->name('meeting_room_booking.edit');
    });
});
