<?php
    use App\Http\Controllers\BOMController;
    use Illuminate\Support\Facades\Route;

    Route::controller(BOMController::class)->group(function () {
        Route::prefix('bom')->group(function () {
            Route::get('/', 'payableAndReceivableReportSettingIndex')->name('bom.index');
            Route::get('/create', 'payableAndReceivableReportSettingCreate')->name('bom.create');
            Route::post('/store', 'payableAndReceivableReportSettingStore')->name('bom.store');
        });
    });
?>