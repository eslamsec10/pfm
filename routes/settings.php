<?php

use App\Http\Controllers\hierarchy\LedgerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\settings\SettingController;
use App\Http\Controllers\settings\CurrencyController;
use App\Http\Controllers\settings\UserSettingsController;
use App\Http\Controllers\settings\CompanySettingsController;
use App\Http\Controllers\settings\ComplaintSettingsController;
use App\Http\Controllers\settings\NotificationSettingsController;
use App\Http\Controllers\Room_Reservation\ReservationSettingsController;
use App\Http\Controllers\property_transactions\PropertyTransactionSettingsController;
use App\Http\Controllers\property_transactions\ProposalController;

Route::group(["prefix" => "settings", 'middleware' => 'auth:web'], function () {
    Route::group(["prefix" => "ui-settings"], function () {

        Route::get("/{position?}", [SettingController::class, "ui_settings"])->name("admin.settings.ui_settings.index");
        Route::post("/update_word", [SettingController::class, "ui_settings"])->name("admin.settings.ui_settings.update-submit");
        Route::post('remove-key/{position?}', [SettingController::class, 'translate_key_remove'])->name('admin.settings.ui_settings.remove-key');
        Route::any('auto-translate/{position?}', [SettingController::class, 'auto_translate'])->name('admin.settings.ui_settings.auto-translate');
        Route::get('translate-list/{position?}', [SettingController::class, 'translate_list'])->name('admin.settings.ui_settings.translate.list');
        Route::post('change-submit/{position?}', [SettingController::class, 'translate_submit'])->name('admin.settings.ui_settings.translate-submit');
    });

    Route::get('/company', [CompanySettingsController::class, 'index'])->name('company_settings');
    Route::patch('/company/update', [CompanySettingsController::class, 'update'])->name('company_settings.store');
    Route::get('/proposal', [PropertyTransactionSettingsController::class, 'proposalIndex'])->name('proposal_settings');
    Route::patch('/proposal/update', [PropertyTransactionSettingsController::class, 'proposalUpdate'])->name('proposal_settings.store');
    Route::get('/enquiry', [PropertyTransactionSettingsController::class, 'enquiryIndex'])->name('enquiry_settings');
    Route::patch('/enquiry/update', [PropertyTransactionSettingsController::class, 'enquiryUpdate'])->name('enquiry_settings.store');
    Route::get('/booking', [PropertyTransactionSettingsController::class, 'bookingIndex'])->name('booking_settings');
    Route::patch('/booking/update', [PropertyTransactionSettingsController::class, 'bookingUpdate'])->name('booking_settings.store');
    Route::get('/agreement', [PropertyTransactionSettingsController::class, 'agreementIndex'])->name('agreement_settings');
    Route::patch('/agreement/update', [PropertyTransactionSettingsController::class, 'agreementUpdate'])->name('agreement_settings.store');
    Route::get('/investment', [PropertyTransactionSettingsController::class, 'investmentIndex'])->name('investment_settings');
    Route::patch('/investment/update', [PropertyTransactionSettingsController::class, 'investmentUpdate'])->name('investment_settings.store');
    Route::get('/complaint', [ComplaintSettingsController::class, 'complaintIndex'])->name('complaint_settings');
    Route::patch('/complaint/update', [ComplaintSettingsController::class, 'complaintUpdate'])->name('complaint_settings.store');
    Route::group(['prefix' => 'user_settings', 'as' => 'user.'], function () {
        Route::get('/', [UserSettingsController::class, 'index'])->name('user_settings');
        Route::patch('settings-update/{id}', [UserSettingsController::class, 'update'])->name('user_settings.update');
        Route::patch('settings-update-buildings', [UserSettingsController::class, 'update_buildings'])->name('user_settings.update_buildings');
    });


    Route::get('/notifications', [NotificationSettingsController::class, 'index'])->name('notifications_settings');
    Route::patch('/notifications/update', [NotificationSettingsController::class, 'update'])->name('notifications_settings.store');
});

Route::group(['prefix' => 'currency', 'as' => 'admin.currency.', 'middleware' => 'auth:web'], function () {
    Route::get('view', [CurrencyController::class, 'index'])->name('view');
    Route::get('fetch', [CurrencyController::class, 'fetch'])->name('fetch');
    Route::post('store', [CurrencyController::class, 'store'])->name('store');
    Route::get('edit/{id}', [CurrencyController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [CurrencyController::class, 'update'])->name('update');
    Route::post('delete', [CurrencyController::class, 'delete'])->name('delete');
    Route::post('status', [CurrencyController::class, 'status'])->name('status');
    Route::post('system-currency-update', [CurrencyController::class, 'systemCurrencyUpdate'])->name('system-currency-update');
});
Route::group(['prefix' => 'room-reservation/settings', 'as' => 'room_reservation.settings.', 'middleware' => 'auth:web'], function () {
    // Reservation Settings Routes can be added here in the future
    Route::get('/', [ReservationSettingsController::class, 'room_reservation'])->name('room_reservation_settings');
    Route::patch('/update', [ReservationSettingsController::class, 'room_reservation_update'])->name('room_reservation_settings.store');
});

Route::get('get_ledger/{id}' , [ProposalController::class , 'get_ledger'])->name('get_ledger');



Route::get('/unit_ledger' , [LedgerController::class ,'update_unit_ledger']);
Route::get('/tenant_ledger' , [LedgerController::class ,'update_tenant_ledger']);
// Route::get('/unit_ledger' , [LedgerController::class ,'update_unit_ledger']);