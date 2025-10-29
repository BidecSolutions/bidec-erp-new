<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Store\{
    StoreController,
    StoreDeleteController,
    StoreDataCallController,
    StoreAddDetailControler,
    StoreEditDetailControler,
    StoreMakeFormAjaxLoadController
};
use App\Http\Controllers\Store\Report\{
    StorePOReportController,
    StoreChallanReportController,
    PettyCashReportController,
    StockReportController
};

// =========================================================
// STD ROUTES
// =========================================================
Route::prefix('std')->controller(StoreDeleteController::class)->group(function () {
    Route::get('/deleteCompanyStoreThreeTableRecords', 'deleteCompanyStoreThreeTableRecords');
    Route::get('/repostCompanyStoreThreeTableRecords', 'repostCompanyStoreThreeTableRecords');
    Route::get('/approvePurchaseOrder', 'approvePurchaseOrder');
    Route::get('/deleteCompanyStoreTwoTableRecords', 'deleteCompanyStoreTwoTableRecords');
    Route::get('/repostCompanyStoreTwoTableRecords', 'repostCompanyStoreTwoTableRecords');
    Route::get('/approvePurchaseRequestSale', 'approvePurchaseRequestSale');
    Route::get('/reversePurchaseOrderDetailAfterApproval', 'reversePurchaseOrderDetailAfterApproval');
    Route::get('/reversePurchaseOrderDetailBeforeApproval', 'reversePurchaseOrderDetailBeforeApproval');
    Route::get('/deleteCompanyMaterialTwoTableRecords', 'deleteCompanyMaterialTwoTableRecords');
    Route::get('/repostCompanyMaterialTwoTableRecords', 'repostCompanyMaterialTwoTableRecords');
    Route::get('/reverseStoreChallanReturnVoucher', 'reverseStoreChallanReturnVoucher');
});

// =========================================================
// STORE ROUTES
// =========================================================
Route::prefix('store')->group(function () {

    // ---------- MAIN STORE ----------
    Route::controller(StoreController::class)->group(function () {
        Route::get('/st', 'toDayActivity');
        Route::get('/viewPurchaseRequestList', 'viewPurchaseRequestList');
        Route::get('/createStoreChallanForm', 'createStoreChallanForm');
        Route::get('/editStoreChallanVoucherForm', 'editStoreChallanVoucherForm');
        Route::get('/createPurchaseOrderForm', 'createPurchaseOrderForm');
        Route::get('/createDirectPurchaseOrderForm', 'createDirectPurchaseOrderForm');
        Route::get('/editPurchaseOrderForm/{id}', 'editPurchaseOrderForm');
        Route::get('/InvoicePO/{id}', 'InvoicePO');
        Route::get('/viewPurchaseOrderList', 'viewPurchaseOrderList');
        Route::get('/createPurchaseOrederSaleForm', 'createPurchaseOrederSaleForm');
        Route::get('/viewPurchaseOrederSaleList', 'viewPurchaseOrederSaleList');
        Route::get('/editPurchaseOrederSaleVoucherForm', 'editPurchaseOrederSaleVoucherForm');
        Route::get('/createStoreChallanReturnForm', 'createStoreChallanReturnForm');
        Route::get('/editStoreChallanReturnForm', 'editStoreChallanReturnForm');
        Route::get('/viewDateWiseStockInventoryReport', 'viewDateWiseStockInventoryReport');
        Route::get('/addMaterialRequestForm', 'addMaterialRequestForm')->name('store.addMaterialRequestForm');
        Route::get('/addMaterialRequestFormTwo', 'addMaterialRequestFormTwo');
        Route::get('/viewMaterialRequestList', 'viewMaterialRequestList')->name('store.viewMaterialRequestList');
        Route::get('/addStoreChallanForm', 'addStoreChallanForm');
        Route::get('/viewStoreChallanList', 'viewStoreChallanList');
        Route::get('/addStoreChallanReturnForm', 'addStoreChallanReturnForm');
        Route::get('/viewStoreChallanReturnList', 'viewStoreChallanReturnList');

        // Invoice + Cash + Transfers
        Route::get('/viewInvoiceSubmissionList', 'viewInvoiceSubmissionList')->name('store.viewInvoiceSubmissionList');
        Route::get('/viewPettyCashList', 'viewPettyCashList')->name('store.viewPettyCashList');
        Route::post('/UpdateMaterialRequestVoucherForm', 'UpdateMaterialRequestVoucherForm');

        // Stock transfer
        Route::get('/stock_transfer_form', 'stock_transfer_form');
        Route::get('/viewStockTransferDetail', 'viewStockTransferDetail');
        Route::get('/stock_transfer_form_production', 'stock_transfer_form_production');
        Route::get('/get_iot_products', 'get_iot_products')->name('stock.transfer.iot-products');
        Route::get('/stock_transfer_list', 'stock_transfer_list');
        Route::post('/addStockTransfer', 'addStockTransfer');
        Route::post('/addStockTransferTwo', 'addStockTransferTwo');

        // Stock trash
        Route::get('/stock_trash_list', 'stock_trash_list');
        Route::get('/viewStockTrashDetail', 'viewStockTrashDetail');
        Route::get('/stock_trash_form', 'stock_trash_form');
        Route::post('/addStockTrash', 'addStockTrash');
    });

    // ---------- DATA CALL ----------
    Route::controller(StoreDataCallController::class)->group(function () {
        Route::get('/editMaterialRequestVoucherForm', 'editMaterialRequestVoucherForm');
        Route::get('/get-recipe-material-items', 'getRecipeMaterialItems')->name('recipe.material.items');
    });

    // ---------- REPORTS ----------
    Route::prefix('report')->group(function () {
        Route::controller(StorePOReportController::class)->group(function () {
            Route::get('/purchaseOrderItemWiseReport', 'purchaseOrderItemWiseReport');
            Route::get('/exportPurchaseOrderItemWise', 'exportPurchaseOrderItemWise');
            Route::get('/invoiceSubmissionReport', 'invoiceSubmissionReport')->name('store.report.invoiceSubmissionReport');
        });

        Route::controller(StoreChallanReportController::class)->group(function () {
            Route::get('/storeChallanItemWiseReport', 'storeChallanItemWiseReport');
            Route::get('ajax/storeChallanItemWiseReport', 'filterStoreChallanItemWiseReport');
            Route::get('/ExportStoreChallanItemWiseReport', 'ExportStoreChallanItemWiseReport');
            Route::get('/stockTransferItemAndTypeWiseReport', 'stockTransferItemAndTypeWiseReport');
            Route::get('ajax/filterStockTransferItemAndTypeWiseReport', 'filterStockTransferItemAndTypeWiseReport');
            Route::get('/ExportStockTransferItemAndTypeWiseReport', 'ExportStockTransferItemAndTypeWiseReport');
        });

        Route::controller(PettyCashReportController::class)->group(function () {
            Route::get('/pettycash', 'stockReport');
            Route::get('/pettycashdata', 'pettyCashReport');
        });
    });
});

// =========================================================
// STAD ROUTES (POST)
// =========================================================
Route::prefix('stad')->controller(StoreAddDetailControler::class)->group(function () {
    Route::post('/addMaterialRequestDetail', 'addMaterialRequestDetail');
    Route::post('/addStoreChallanDetail', 'addStoreChallanDetail');
    Route::post('/addStoreChallanReturnDetail', 'addStoreChallanReturnDetail');
    Route::post('/addPurchaseOrderDetail', 'addPurchaseOrderDetail');
    Route::post('/addPurchaseOrderDetailDirect', 'addPurchaseOrderDetailDirect');
    Route::post('/updatePurchaseOrderDetail', 'updatePurchaseOrderDetail');
    Route::post('/updateInvoicePO', 'updateInvoicePO');
    Route::post('/addPurchaseRequestSaleDetail', 'addPurchaseRequestSaleDetail');
    Route::post('/addStoreChallanReturnDetail', 'addStoreChallanReturnDetail');
});

// EDIT DETAIL CONTROLLER
Route::prefix('stad')->controller(StoreEditDetailControler::class)->group(function () {
    Route::post('/editStoreChallanVoucherDetail', 'editStoreChallanVoucherDetail');
    Route::post('/editPurchaseOrderVoucherDetail', 'editPurchaseOrderVoucherDetail');
    Route::post('/updateTaxPurchaseOrderVoucherDetail', 'updateTaxPurchaseOrderVoucherDetail')->name('update.tax.purchase.voucher');
    Route::post('/editPurchaseRequestSaleVoucherDetail', 'editPurchaseRequestSaleVoucherDetail');
    Route::post('/editStoreChallanReturnDetail', 'editStoreChallanReturnDetail');
    Route::post('/updateMaterialRequestDetailandApprove', 'updateMaterialRequestDetailandApprove');
    Route::post('/updateStoreChallanDetailandApprove', 'updateStoreChallanDetailandApprove');
});

// DATA CALL POST
Route::prefix('stad')->controller(StoreDataCallController::class)->group(function () {
    Route::post('/createStoreChallanDetailForm', 'createStoreChallanDetailForm');
    Route::post('/createPurchaseOrderDetailForm', 'createPurchaseOrderDetailForm');
    Route::post('/createPurchaseRequestSaleDetailForm', 'createPurchaseRequestSaleDetailForm');
    Route::post('/createStoreChallanReturnDetailForm', 'createStoreChallanReturnDetailForm');
});

// =========================================================
// AJAX LOAD ROUTES
// =========================================================
Route::prefix('stmfal')->controller(StoreMakeFormAjaxLoadController::class)->group(function () {
    Route::get('/makeFormPurchaseOrderDetailByPRNo', 'makeFormPurchaseOrderDetailByPRNo');
    Route::get('/addMoreMaterialRequestsDetailRows', 'addMoreMaterialRequestsDetailRows');
    Route::get('/makeFormStoreChallanDetailByMRNo', 'makeFormStoreChallanDetailByMRNo');
    Route::get('/makeFormStoreChallanReturnByMRNo', 'makeFormStoreChallanReturnByMRNo');
});

// =========================================================
// INVENTORY REPORT ROUTES
// =========================================================
Route::prefix('store')->controller(StockReportController::class)->group(function () {
    Route::get('/get-stocks-location-wise', 'get_stock_location_wise')->name('inventory.report.stock-locationwise');
    Route::get('/stockReportLocationWiseView', 'stockReportLocationWiseView');
    Route::get('/stockReportSingleLocationView/{location_id}', 'stockReportSingleLocationView');
    Route::get('/get-stocks-in-out', 'get_stock_in_out_wise')->name('inventory.report.stock-in-out');
    Route::get('/stockReportInOutView', 'stockReportInOutView');
});
