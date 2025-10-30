<?php
    use App\Http\Controllers\BOMController;
    use Illuminate\Support\Facades\Route;

    Route::controller(BOMController::class)->group(function () {
        Route::prefix('bom')->group(function () {
            Route::get('/', 'index')->name('bom.index');
            Route::get('/create', 'create')->name('bom.create');
            Route::post('/store', 'store')->name('bom.store');
        });
    });
?>