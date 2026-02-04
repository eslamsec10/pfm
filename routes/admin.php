<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SchemaController;
use App\Http\Controllers\settings\AdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\RolesAndCompanyManagement\CompanyRegistrationController;
use App\Http\Controllers\Admin\RolesAndCompanyManagement\CompanyManagementController;

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

Route::group(["prefix" => "auth/admin"], function () {
    Route::post("login", [AuthController::class, "admin_login"])->name("admin.login")->withoutMiddleware('auth');
    Route::get("logout", [AuthController::class, "admin_logout"])->name("admin.logout")->middleware('auth');
    Route::get("login-page", [AuthController::class, "login_page"])->name("admin.login-page")->middleware('auth');
});
Route::group(['middleware' => 'auth:admins', 'prefix' => 'admin'], function () {
    // dd(auth()->guard('admins')->check());
    Route::group(["prefix" => "dashboard"], function () {
        Route::get("/", [AdminDashboardController::class, "admin_dashboard"])->name("admin.dashboard")->middleware('auth:admins');
        Route::get("/get_unit_details/{id}", [AdminDashboardController::class, "get_unit_details"])->name("get_unit_details")->middleware('auth:admins');
    });
    Route::get('get_country_master/{id}', [CompanyManagementController::class, 'get_country'])->name('admin.get_country_master');

// // Translation

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
    session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('admin.lang');

Route::group(['prefix' => 'companies'], function () {

    Route::get('/', [CompanyManagementController::class, 'index'])->name('admin.companies');
    Route::get('/schedules/{id}', [CompanyManagementController::class, 'schedules'])->name('admin.companies.schedules');
    Route::get('/create', [CompanyManagementController::class , 'create'])->name('admin.companies.create');
    Route::post('/store', [CompanyManagementController::class , 'store'])->name('admin.companies.store');
    Route::get('/edit/{id}' , [CompanyManagementController::class , 'edit'])->name('admin.companies.edit');
    Route::get('/show/{id}' , [CompanyManagementController::class , 'show'])->name('admin.companies.show');
    Route::patch('/update/{id}' , [CompanyManagementController::class , 'update'])->name('admin.companies.update');
    Route::get('/delete', [CompanyManagementController::class ,'delete'])->name('admin.companies.delete');

});

Route::group(['prefix' => 'requests'], function () {

    Route::get('/', [CompanyRegistrationController::class, 'index'])->name('admin.requests');  
    Route::get('/confirm/{id}' , [CompanyRegistrationController::class , 'confirm'])->name('admin.requests.confirm'); 
    Route::patch('/approve-confirm/{id}' , [CompanyRegistrationController::class , 'ApproveConfirm'])->name('admin.requests.ApproveConfirm'); 
    Route::get('/show/{id}' , [CompanyRegistrationController::class , 'show'])->name('admin.requests.show'); 
    Route::get('/delete', [CompanyRegistrationController::class ,'delete'])->name('admin.requests.delete');

});

Route::group(['prefix' => 'admin_settings'], function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin_settings');  
    Route::patch('/update/{id}' , [AdminController::class , 'update'])->name('admin_settings.update'); 
    Route::post('/update_password/{id}' , [AdminController::class , 'update_password'])->name('admin_settings.profile.settings-password'); 

});


Route::group(['prefix' => 'schema'], function () {

    Route::get('/', [SchemaController::class, 'index'])->name('admin.schema');   
    Route::get('/create', [SchemaController::class, 'create'])->name('admin.schema.create');   
    Route::post('/store', [SchemaController::class, 'store'])->name('admin.schema.store');
    Route::get('/edit/{id}', [SchemaController::class, 'edit'])->name('admin.schema.edit');   
    Route::patch('/update/{id}', [SchemaController::class, 'update'])->name('admin.schema.update');   
    Route::get('/delete', [SchemaController::class ,'delete'])->name('admin.schema.delete');
    Route::post('/status-update', [SchemaController::class, 'statusUpdate'])->name('admin.schema.status-update');
    Route::post('/display-update', [SchemaController::class, 'displayUpdate'])->name('admin.schema.display-update');

});











});
