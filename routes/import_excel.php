
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\import\TenantImportController;
use App\Http\Controllers\import\ContractImportController;
use App\Http\Controllers\import\PropertyMasterImportController;



Route::get('/import_excel-side', function () {
    return view('dashboard.import_excel_side');
})->name('import_excel_side');




// Import Excel
Route::group(['prefix' => 'import_excel', 'middleware' => 'auth:web'], function () {
    Route::get('/import_property_master', [PropertyMasterImportController::class, 'import_page'])->name('import_property_master'); 
    Route::post('/import_property_master', [PropertyMasterImportController::class, 'import'])->name('property.import'); 
    Route::get('/import_contract', [ContractImportController::class, 'import_page'])->name('import_contract'); 
    Route::post('/import_contract', [ContractImportController::class, 'import'])->name('agreement.import'); 
    Route::get('/import_tenant', [TenantImportController::class, 'import_page'])->name('import_tenant'); 
    Route::post('/preview_tenant', [TenantImportController::class, 'preview'])->name('preview_tenant'); 
    Route::post('/preview_agreement', [ContractImportController::class, 'preview'])->name('preview_agreement'); 
    Route::post('/preview_property_master', [PropertyMasterImportController::class, 'preview'])->name('preview_property'); 
    Route::post('/confirm_tenant', [TenantImportController::class, 'confirm_tenant'])->name('import.confirm_tenant'); 
    Route::post('/confirm_agreement', [ContractImportController::class, 'confirm_agreement'])->name('import.confirm_agreement'); 
    Route::post('/confirm_property_master', [PropertyMasterImportController::class, 'confirm_property_master'])->name('import.confirm_property_master'); 
    
    Route::post('/import_tenant', [TenantImportController::class, 'import'])->name('tenant.import'); 
});
Route::get('/export-units', [ContractImportController::class, 'export_master'])->name('export_units');
