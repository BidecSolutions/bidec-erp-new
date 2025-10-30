<?php

use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowStatementController;
use App\Http\Controllers\ReturnGoodReceiptNoteController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SalesReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    LocationController,
    CustomAuthController,
    DepartmentController,
    CountryController,
    StateController,
    CityController,
    CompanyController,
    LangController,
    PayPalController,
    ChartOfAccountController,
    PaymentController,
    ReceiptController,
    JournalVoucherController,
    RoleController,
    ReportController,
    SettingController,
    NotificationController,
    CategoryController,
    BrandController,
    SizeController,
    ProductController,
    PurchaseOrderController,
    MaterialRequestController,
    GoodReceiptNoteController,
    PaymentTypeController,
    SupplierController,
    CustomerController,
    ChartOfAccountSettingController,
    MailController,
    POSController,
    DirectGoodReceiptNoteController,
    TransferNoteController,
    StockController,
    PurchasePaymentController,
    BankAccountController,
    CashAccountController,
    DirectSaleInvoiceController,
    SaleReceiptController,
    TaxAccountsController,
    PurchaseInvoiceController,
    SaleInvoiceController,
    StoreChallanController
};

//use GPBMetadata\Google\Api\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return Redirect::to('dashboard');
    } else {
        return view('auth.login');
    }
    //dd(singlePermission(getSessionCompanyId(), Auth::user()->id, 'right_approve', Auth::user()->acc_type));
});
Route::get('migrate', function () {
    echo Artisan::call('migrate');
    echo 'All migration run successfully';
});
Route::get('/set_user_db_id', [CustomAuthController::class, 'set_user_db_id']);
Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');
Route::get('/weekly-sales-purchases', [CustomAuthController::class, 'getWeeklySalesAndPurchases'])->name('weekly.sales.purchases');
Route::get('get-top-selling-products', [CustomAuthController::class, 'getTopSellingProducts'])->name('getTopSellingProducts');
Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('register', [CustomAuthController::class, 'registration'])->name('register');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');
Route::get('send-mail', [MailController::class, 'index']);
Route::get('forgetPasswordForm', [CustomAuthController::class, 'forgetPasswordForm'])->name('forgetPasswordForm');

Route::post('forget_password', [CustomAuthController::class, 'forgetPassword'])->name('forget_password');
Route::get('/resetPasswordForm/{token}', [CustomAuthController::class, 'resetPasswordForm'])->name('resetPasswordForm');
Route::post('reset_password', [CustomAuthController::class, 'resetPassword'])->name('reset_password');

Route::middleware(['auth'])->group(function () {

    Route::get('/change_password', [CustomAuthController::class, 'change_password_form'])->name('change_password');
    Route::post('users/change_password', [CustomAuthController::class, 'changePassword'])->name('changePassword');
    Route::post('users/change_password_two', [CustomAuthController::class, 'changePasswordTwo'])->name('changePasswordTwo');

    Route::post('companies/active/{id}', [CompanyController::class, 'changeInactiveToActiveRecord'])->name('companies.active');
    Route::post('companies/status/{id}', [CompanyController::class, 'status'])->name('companies.status');
    Route::get('companies/addMadrasaAdditionalForm', [CompanyController::class, 'addMadrasaAdditionalForm'])->name('companies.addMadrasaAdditionalForm');
    Route::post('companies/addMadrasaAdditionalDetail', [CompanyController::class, 'addMadrasaAdditionalDetail'])->name('companies.addMadrasaAdditionalDetail');
    Route::get('loadCompanies', [CompanyController::class, 'loadCompanies'])->name('loadCompanies');
    Route::get('loadLocations', [CompanyController::class, 'loadLocations'])->name('loadLocations');
    Route::get('loadSchoolCampusDetailDependCampusIds', [CompanyController::class, 'loadSchoolCampusDetailDependCampusIds'])->name('loadSchoolCampusDetailDependCampusIds');

    Route::resource('companies', CompanyController::class);

    Route::post('locations/active/{id}', [LocationController::class, 'changeInactiveToActiveRecord'])->name('locations.active');
    Route::post('locations/status/{id}', [LocationController::class, 'status'])->name('locations.status');
    Route::resource('locations', LocationController::class);

    Route::post('departments/active/{id}', [DepartmentController::class, 'changeInactiveToActiveRecord'])->name('departments.active');
    Route::post('departments/status/{id}', [DepartmentController::class, 'status'])->name('departments.status');
    Route::resource('departments', DepartmentController::class);

    Route::post('countries/active/{id}', [CountryController::class, 'changeInactiveToActiveRecord'])->name('countries.active');
    Route::post('countries/status/{id}', [CountryController::class, 'status'])->name('countries.status');
    Route::resource('countries', CountryController::class);

    Route::post('states/active/{id}', [StateController::class, 'changeInactiveToActiveRecord'])->name('states.active');

    Route::post('states/status/{id}', [StateController::class, 'status'])->name('states.status');
    Route::resource('states', StateController::class);

    Route::post('cities/active/{id}', [CityController::class, 'changeInactiveToActiveRecord'])->name('cities.active');
    Route::post('cities/status/{id}', [CityController::class, 'status'])->name('cities.status');
    Route::resource('cities', CityController::class);

    Route::get('/send/notification/to/device', [NotificationController::class, 'sendPushNotification'])->name('sendPushNotification');
    
    Route::get('lang/home', [LangController::class, 'index']);
    Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');

    Route::group(['prefix' => 'finance', 'before' => 'csrf'], function () {
        Route::resource('chartofaccounts', ChartOfAccountController::class);
        Route::post('chartofaccounts/active/{id}', [ChartOfAccountController::class, 'changeInactiveToActiveRecord'])->name('chartofaccounts.activeStatus');
        Route::post('chartofaccounts/status/{id}', [ChartOfAccountController::class, 'status'])->name('chartofaccounts.status');

        Route::controller(PurchasePaymentController::class)->group(function () {
            Route::prefix('purchase-payments')->group(function () {
                Route::get('/', 'index')->name('purchase-payments.index');
                Route::get('/create', 'create')->name('purchase-payments.create');
                Route::post('/store', 'store')->name('purchase-payments.store');
                Route::post('/status', 'status')->name('purchase-payments.status');
                Route::get('/{id}/edit', 'edit')->name('purchase-payments.edit');
                Route::post('/{id}/update', 'update')->name('purchase-payments.update');
                Route::post('/destroy/{id}', 'destroy')->name('purchase-payments.destroy');
                Route::get('/show', 'show')->name('purchase-payments.show');
                Route::get('/edit', 'edit')->name('purchase-payments.edit');
            });
        });
        Route::get('/purchase-payments/loadPurchasePaymentVoucherDetailByPONo', [PurchasePaymentController::class, 'loadPurchasePaymentVoucherDetailByPONo']);
        Route::get('/purchase-payments/loadPurchasePaymentVoucherDetailByGRNNo', [PurchasePaymentController::class, 'loadPurchasePaymentVoucherDetailByGRNNo']);
        Route::get('/purchase-payments/loadPurchasePaymentVoucherDetailByInvoiceId', [PurchasePaymentController::class, 'loadPurchasePaymentVoucherDetailByInvoiceId']);
        

        Route::controller(SaleReceiptController::class)->group(function () {
            Route::prefix('sale-receipts')->group(function () {
                Route::get('/', 'index')->name('sale-receipts.index');
                Route::get('/create', 'create')->name('sale-receipts.create');
                Route::post('/store', 'store')->name('sale-receipts.store');
                Route::post('/status', 'status')->name('sale-receipts.status');
                Route::get('/{id}/edit', 'edit')->name('sale-receipts.edit');
                Route::post('/{id}/update', 'update')->name('sale-receipts.update');
                Route::post('/destroy/{id}', 'destroy')->name('sale-receipts.destroy');
                Route::get('/show', 'show')->name('sale-receipts.show');
                Route::get('/edit', 'edit')->name('sale-receipts.edit');
            });
        });
        Route::get('/sale-receipts/loadSaleReceiptVoucherDetailByDSINO', [SaleReceiptController::class, 'loadSaleReceiptVoucherDetailByDSINO']);
        Route::get('/sale-receipts/loadSaleReceiptVoucherDetailByInvoiceId', [SaleReceiptController::class, 'loadSaleReceiptVoucherDetailByInvoiceId']);
        

        Route::controller(PaymentController::class)->group(function () {
            Route::prefix('payments')->group(function () {
                Route::get('/', 'index')->name('payments.index');
                Route::get('/create', 'create')->name('payments.create');
                Route::post('/store', 'store')->name('payments.store');
                Route::post('/status', 'status')->name('payments.status');
                Route::get('/{id}/edit', 'edit')->name('payments.edit');
                Route::post('/{id}/update', 'update')->name('payments.update');
                Route::post('/destroy/{id}', 'destroy')->name('payments.destroy');
                Route::get('/show', 'show')->name('payments.show');
                Route::get('/edit', 'edit')->name('payments.edit');
            });
        });
        Route::post('/payments/reversePaymentVoucher', [PaymentController::class, 'reversePaymentVoucher']);
        Route::post('/payments/approvePaymentVoucher', [PaymentController::class, 'approvePaymentVoucher']);
        Route::post('/payments/deletePaymentVoucher', [PaymentController::class, 'deletePaymentVoucher']);
        Route::post('/payments/paymentVoucherRejectAndRepost', [PaymentController::class, 'paymentVoucherRejectAndRepost']);
        Route::post('/payments/paymentVoucherActiveAndInactive', [PaymentController::class, 'paymentVoucherActiveAndInactive']);

        Route::controller(ReceiptController::class)->group(function () {
            Route::prefix('receipts')->group(function () {
                Route::get('/', 'index')->name('receipts.index');
                Route::get('/create', 'create')->name('receipts.create');
                Route::post('/store', 'store')->name('receipts.store');
                Route::post('/status', 'status')->name('receipts.status');
                Route::get('/{id}/edit', 'edit')->name('payments.edit');
                Route::post('/{id}/update', 'update')->name('receipts.update');
                Route::post('/destroy/{id}', 'destroy')->name('receipts.destroy');
                Route::get('/show', 'show')->name('receipts.show');
                Route::get('/edit', 'edit')->name('receipts.edit');
            });
        });
        Route::post('/receipts/reverseReceiptVoucher', [ReceiptController::class, 'reverseReceiptVoucher']);
        Route::post('/receipts/approveReceiptVoucher', [ReceiptController::class, 'approveReceiptVoucher']);
        Route::post('/receipts/deleteReceiptVoucher', [ReceiptController::class, 'deleteReceiptVoucher']);
        Route::post('/receipts/receiptVoucherRejectAndRepost', [ReceiptController::class, 'receiptVoucherRejectAndRepost']);
        Route::post('/receipts/receiptVoucherActiveAndInactive', [ReceiptController::class, 'receiptVoucherActiveAndInactive']);


        Route::controller(JournalVoucherController::class)->group(function () {
            Route::prefix('journalvouchers')->group(function () {
                Route::get('/', 'index')->name('journalvouchers.index');
                Route::get('/create', 'create')->name('journalvouchers.create');
                Route::post('/store', 'store')->name('journalvouchers.store');
                Route::post('/status', 'status')->name('journalvouchers.status');
                Route::get('/{id}/edit', 'edit')->name('journalvouchers.edit');
                Route::post('/{id}/update', 'update')->name('journalvouchers.update');
                Route::post('/destroy/{id}', 'destroy')->name('journalvouchers.destroy');
                Route::get('/show', 'show')->name('journalvouchers.show');
                Route::get('/edit', 'edit')->name('journalvouchers.edit');
            });
        });
        Route::post('/journalvouchers/reverseJournalVoucher', [JournalVoucherController::class, 'reverseJournalVoucher']);
        Route::post('/journalvouchers/approveJournalVoucher', [JournalVoucherController::class, 'approveJournalVoucher']);
        Route::post('/journalvouchers/deleteJournalVoucher', [JournalVoucherController::class, 'deleteJournalVoucher']);
        Route::post('/journalvouchers/journalVoucherRejectAndRepost', [JournalVoucherController::class, 'journalVoucherRejectAndRepost']);
        Route::post('/journalvouchers/journalVoucherActiveAndInactive', [JournalVoucherController::class, 'journalVoucherActiveAndInactive']);



        //Route::resource('journalvouchers', JournalVoucherController::class);
    });

    Route::controller(PurchaseInvoiceController::class)->group(function () {
        Route::prefix('purchase-invoice')->group(function () {
            Route::get('/', 'index')->name('purchase-invoice.index');
            Route::get('/create', 'create')->name('purchase-invoice.create');
            Route::post('/store', 'store')->name('purchase-invoice.store');
            Route::post('/status', 'status')->name('purchase-invoice.status');
            Route::get('/{id}/edit', 'edit')->name('purchase-invoice.edit');
            Route::post('/update/{id}', 'update')->name('purchase-invoice.update');
            Route::post('/destroy/{id}', 'destroy')->name('purchase-invoice.destroy');
            Route::get('/show', 'show')->name('purchase-invoice.show');
        });
    });

    Route::post('/purchase-invoice/approvePurchaseInvoiceVoucher', [PurchaseInvoiceController::class, 'approvePurchaseInvoiceVoucher']);
    Route::post('/purchase-invoice/reversePurchaseInvoiceVoucher', [PurchaseInvoiceController::class, 'reversePurchaseInvoiceVoucher']);

    Route::controller(SaleInvoiceController::class)->group(function () {
        Route::prefix('sale-invoice')->group(function () {
            Route::get('/', 'index')->name('sale-invoice.index');
            Route::get('/create', 'create')->name('sale-invoice.create');
            Route::post('/store', 'store')->name('sale-invoice.store');
            Route::post('/status', 'status')->name('sale-invoice.status');
            Route::get('/{id}/edit', 'edit')->name('sale-invoice.edit');
            Route::post('/update/{id}', 'update')->name('sale-invoice.update');
            Route::post('/destroy/{id}', 'destroy')->name('sale-invoice.destroy');
            Route::get('/show', 'show')->name('sale-invoice.show');
        });
    });

    Route::post('/sale-invoice/approveSaleInvoiceVoucher', [SaleInvoiceController::class, 'approveSaleInvoiceVoucher']);
    Route::post('/sale-invoice/reverseSaleInvoiceVoucher', [SaleInvoiceController::class, 'reverseSaleInvoiceVoucher']);
    
    Route::controller(RoleController::class)->group(function () {
        Route::prefix('roles')->group(function () {
            Route::get('/', 'index')->name('roles.index');
            Route::get('/create', 'create')->name('roles.create');
            Route::post('/store', 'store')->name('roles.store');
            Route::post('/status', 'status')->name('roles.status');
            Route::get('/{id}/edit', 'edit')->name('roles.edit');
            Route::post('/update/{id}', 'update')->name('roles.update');
            Route::post('/destroy/{id}', 'destroy')->name('roles.destroy');
        });
    });
    Route::controller(PurchaseOrderController::class)->group(function () {
        Route::prefix('purchase-orders')->group(function () {
            Route::get('/', 'index')->name('purchase-orders.index');
            Route::get('/create', 'create')->name('purchase-orders.create');
            Route::post('/store', 'store')->name('purchase-orders.store');
            Route::post('/status/{id}', 'status')->name('purchase-orders.status');
            Route::get('/{id}/edit', 'edit')->name('purchase-orders.edit');
            Route::post('/update/{id}', 'update')->name('purchase-orders.update');
            Route::get('/show', 'show')->name('payments.show');
            Route::post('/destroy/{id}', 'destroy')->name('purchase-orders.destroy');
            Route::get('/get-last-purchase-price/{productId}', 'getLastPurchasePrice');

        });
    });


    Route::post('/purchase-orders/approvePurchaseOrderVoucher', [PurchaseOrderController::class, 'approvePurchaseOrderVoucher']);
    Route::post('/purchase-orders/purchaseOrderVoucherRejectAndRepost', [PurchaseOrderController::class, 'purchaseOrderVoucherRejectAndRepost']);
    Route::post('/purchase-orders/purchaseOrderVoucherActiveAndInactive', [PurchaseOrderController::class, 'purchaseOrderVoucherActiveAndInactive']);




    Route::controller(MaterialRequestController::class)->group(function () {
        Route::prefix('material-requests')->group(function () {
            Route::get('/', 'index')->name('material-requests.index');
            Route::get('/create', 'create')->name('material-requests.create');
            Route::post('/store', 'store')->name('material-requests.store');
            Route::post('/status/{id}', 'status')->name('material-requests.status');
            Route::get('/{id}/edit', 'edit')->name('material-requests.edit');
            Route::post('/update/{id}', 'update')->name('material-requests.update');
            Route::get('/show', 'show')->name('payments.show');
            Route::post('/destroy/{id}', 'destroy')->name('material-requests.destroy');
        });
    });


    Route::post('/material-requests/approveMaterialRequestVoucher', [MaterialRequestController::class, 'approveMaterialRequestVoucher']);
    Route::post('/material-requests/materialRequestVoucherRejectAndRepost', [MaterialRequestController::class, 'materialRequestVoucherRejectAndRepost']);
    Route::post('/material-requests/materialRequestVoucherActiveAndInactive', [MaterialRequestController::class, 'materialRequestVoucherActiveAndInactive']);


    Route::controller(StoreChallanController::class)->group(function () {
        Route::prefix('store-challans')->group(function () {
            Route::get('/', 'index')->name('store-challans.index');
            Route::get('/create', 'create')->name('store-challans.create');
            Route::post('/store', 'store')->name('store-challans.store');
            Route::post('/status/{id}', 'status')->name('store-challans.status');
            Route::get('/{id}/edit', 'edit')->name('store-challans.edit');
            Route::post('/update/{goodReceiptNote}', 'update')->name('store-challans.update');
            Route::post('/destroy/{id}', 'destroy')->name('store-challans.destroy');
            Route::get('/getMaterialRequestsByDepartmentId', 'getMaterialRequestsByDepartmentId')->name('getMaterialRequestsByDepartmentId');
            Route::get('/getMaterialRequestsForEdit', 'getMaterialRequestsForEdit')->name('getMaterialRequestsForEdit');
            Route::get('/show', 'show')->name('store-challans.show');
        });
    });


    Route::post('/store-challans/approveStoreChallanVoucher', [StoreChallanController::class, 'approveStoreChallanVoucher']);
    Route::post('/store-challans/storeChallanVoucherRejectAndRepost', [StoreChallanController::class, 'storeChallanVoucherRejectAndRepost']);
    Route::post('/store-challans/storeChallanVoucherActiveAndInactive', [StoreChallanController::class, 'storeChallanVoucherActiveAndInactive']);


    Route::controller(TransferNoteController::class)->group(function () {
        Route::prefix('transfer-notes')->group(function () {
            Route::get('/', 'index')->name('transfer-notes.index');
            Route::get('/create', 'create')->name('transfer-notes.create');
            Route::post('/store', 'store')->name('transfer-notes.store');
            Route::post('/status/{id}', 'status')->name('transfer-notes.status');
            Route::get('/{id}/edit', 'edit')->name('transfer-notes.edit');
            Route::post('/update/{id}', 'update')->name('transfer-notes.update');
            Route::post('/destroy/{id}', 'destroy')->name('transfer-notes.destroy');
            Route::get('/show', 'show')->name('transfer-notes.show');
            Route::get('/viewReceiptDetail', 'viewReceiptDetail')->name('transfer-notes.viewReceiptDetail');
            Route::post('/updateTransferNotesReceiptDetail', 'updateTransferNotesReceiptDetail')->name('transfer-notes.updateTransferNotesReceiptDetail');
        });
    });

    Route::post('/transfer-notes/approveTransferNoteVoucher', [TransferNoteController::class, 'approveTransferNoteVoucher']);
    Route::post('/transfer-notes/transferNoteVoucherRejectAndRepost', [TransferNoteController::class, 'transferNoteVoucherRejectAndRepost']);
    Route::post('/transfer-notes/transferNoteVoucherActiveAndInactive', [TransferNoteController::class, 'transferNoteVoucherActiveAndInactive']);


    Route::controller(GoodReceiptNoteController::class)->group(function () {
        Route::prefix('good-receipt-notes')->group(function () {
            Route::get('/', 'index')->name('good-receipt-notes.index');
            Route::get('/create', 'create')->name('good-receipt-notes.create');
            Route::post('/store', 'store')->name('good-receipt-notes.store');
            Route::post('/status/{id}', 'status')->name('good-receipt-notes.status');
            Route::get('/{id}/edit', 'edit')->name('good-receipt-notes.edit');
            Route::post('/update/{goodReceiptNote}', 'update')->name('good-receipt-notes.update');
            Route::post('/destroy/{id}', 'destroy')->name('good-receipt-notes.destroy');
            Route::get('/getPurchaseOrdersBySupplierId', 'getPurchaseOrdersBySupplierId')->name('getPurchaseOrdersBySupplierId');
            Route::get('/getPurchaseOrdersForEdit', 'getPurchaseOrdersForEdit')->name('getPurchaseOrdersForEdit');
            Route::get('/show', 'show')->name('good-receipt-notes.show');
        });
    });


    Route::post('/good-receipt-notes/approveGoodReceiptNoteVoucher', [GoodReceiptNoteController::class, 'approveGoodReceiptNoteVoucher']);
    Route::post('/good-receipt-notes/goodReceiptNoteVoucherRejectAndRepost', [GoodReceiptNoteController::class, 'goodReceiptNoteVoucherRejectAndRepost']);
    Route::post('/good-receipt-notes/goodReceiptNoteVoucherActiveAndInactive', [GoodReceiptNoteController::class, 'goodReceiptNoteVoucherActiveAndInactive']);

    Route::controller(DirectGoodReceiptNoteController::class)->group(function () {
        Route::prefix('direct-good-receipt-note')->group(function () {
            Route::get('/', 'index')->name('direct-good-receipt-note.index');
            Route::get('/create', 'create')->name('direct-good-receipt-note.create');
            Route::post('/store', 'store')->name('direct-good-receipt-note.store');
            Route::post('/status/{id}', 'status')->name('direct-good-receipt-note.status');
            Route::get('/edit/{id}', 'edit')->name('direct-good-receipt-note.edit');
            Route::post('/update/{id}', 'update')->name('direct-good-receipt-note.update');
            Route::post('/destroy/{id}', 'destroy')->name('direct-good-receipt-note.destroy');
            Route::get('/getPurchaseOrdersBySupplierId', 'getPurchaseOrdersBySupplierId')->name('getPurchaseOrdersBySupplierId');
            Route::get('/show', 'show')->name('direct-good-receipt-note.show');
        });
    });

    Route::controller(DirectSaleInvoiceController::class)->group(function () {
        Route::prefix('direct-sale-invoices')->group(function () {
            Route::get('/', 'index')->name('direct-sale-invoices.index');
            Route::get('/create', 'create')->name('direct-sale-invoices.create');
            Route::post('/store', 'store')->name('direct-sale-invoices.store');
            Route::post('/status/{id}', 'status')->name('direct-sale-invoices.status');
            Route::get('/edit/{id}', 'edit')->name('direct-sale-invoices.edit');
            Route::post('/update/{id}', 'update')->name('direct-sale-invoices.update');
            Route::post('/destroy/{id}', 'destroy')->name('direct-sale-invoices.destroy');
            Route::get('/show', 'show')->name('direct-sale-invoices.show');
            Route::get('/getPurchaseOrdersBySupplierId', 'getPurchaseOrdersBySupplierId')->name('getPurchaseOrdersBySupplierId');
            Route::get('/product-wise-average-rate', 'productWiseAverageRate')->name('direct-sale-invoices.product-wise-average-rate');

        });
    });

    Route::post('/direct-sale-invoices/approveDirectSaleInvoiceVoucher', [DirectSaleInvoiceController::class, 'approveDirectSaleInvoiceVoucher']);
    Route::post('/direct-sale-invoices/directSaleInvoiceVoucherRejectAndRepost', [DirectSaleInvoiceController::class, 'directSaleInvoiceVoucherRejectAndRepost']);
    Route::post('/direct-sale-invoices/directSaleInvoiceVoucherActiveAndInactive', [DirectSaleInvoiceController::class, 'directSaleInvoiceVoucherActiveAndInactive']);


    Route::controller(ReturnGoodReceiptNoteController::class)->group(function () {
        Route::prefix('return-good-receipt-notes')->group(function () {
            Route::get('/', 'index')->name('return-good-receipt-notes.index');
            Route::get('/create', 'create')->name('return-good-receipt-notes.create');
            Route::post('/store', 'store')->name('return-good-receipt-notes.store');
            Route::get('load-grn-details',  'loadGRNDetails');
            Route::get('/{id}/edit', 'edit')->name('return-good-receipt-notes.edit');
            Route::post('/update/{goodReceiptNote}', 'update')->name('return-good-receipt-notes.update');
            Route::post('/destroy/{id}', 'destroy')->name('return-good-receipt-notes.destroy');
            Route::post('/status/{id}', 'status')->name('return-good-receipt-notes.status');
            Route::post('/returnGoodReceiptNoteVoucherReject/{id}', 'returnGoodReceiptNoteVoucherReject')->name('return-good-receipt-notes.returnGoodReceiptNoteVoucherReject');
            Route::post('/returnGoodReceiptNoteVoucherRepost/{id}', 'returnGoodReceiptNoteVoucherRepost')->name('return-good-receipt-notes.returnGoodReceiptNoteVoucherRepost');
            Route::post('/approveReturnGoodReceiptNoteVoucher', 'approveReturnGoodReceiptNoteVoucher')->name('return-good-receipt-notes.approveReturnGoodReceiptNoteVoucher');
            Route::get('/getPurchaseOrdersBySupplierId', 'getPurchaseOrdersBySupplierId')->name('getPurchaseOrdersBySupplierId');
            Route::get('/getPurchaseOrdersForEdit', 'getPurchaseOrdersForEdit')->name('getPurchaseOrdersForEdit');
            Route::get('/show', 'show')->name('return-good-receipt-notes.show');
        });
    });

    Route::controller(PaymentTypeController::class)->group(function () {
        Route::prefix('payment-types')->group(function () {
            Route::get('/', 'index')->name('payment-types.index');
            Route::get('/create', 'create')->name('payment-types.create');
            Route::post('/store', 'store')->name('payment-types.store');
            Route::post('/status/{id}', 'status')->name('payment-types.status');
            Route::get('/{id}/edit', 'edit')->name('payment-types.edit');
            Route::post('/update/{id}', 'update')->name('payment-types.update');
            Route::post('/destroy/{id}', 'destroy')->name('payment-types.destroy');
        });
    });

    Route::controller(BankAccountController::class)->group(function () {
        Route::prefix('bank-accounts')->group(function () {
            Route::get('/', 'index')->name('bank-accounts.index');
            Route::get('/create', 'create')->name('bank-accounts.create');
            Route::post('/store', 'store')->name('bank-accounts.store');
            Route::post('/status/{id}', 'status')->name('bank-accounts.status');
            Route::get('/{id}/edit', 'edit')->name('bank-accounts.edit');
            Route::post('/update/{id}', 'update')->name('bank-accounts.update');
            Route::post('/destroy/{id}', 'destroy')->name('bank-accounts.destroy');
        });
    });

    Route::controller(CashAccountController::class)->group(function () {
        Route::prefix('cash-accounts')->group(function () {
            Route::get('/', 'index')->name('cash-accounts.index');
            Route::get('/create', 'create')->name('cash-accounts.create');
            Route::post('/store', 'store')->name('cash-accounts.store');
            Route::post('/status/{id}', 'status')->name('cash-accounts.status');
            Route::get('/{id}/edit', 'edit')->name('cash-accounts.edit');
            Route::post('/update/{id}', 'update')->name('cash-accounts.update');
            Route::post('/destroy/{id}', 'destroy')->name('cash-accounts.destroy');
        });
    });

    Route::controller(SupplierController::class)->group(function () {
        Route::prefix('suppliers')->group(function () {
            Route::get('/', 'index')->name('suppliers.index');
            Route::get('/create', 'create')->name('suppliers.create');
            Route::post('/store', 'store')->name('suppliers.store');
            Route::post('/status/{id}', 'status')->name('suppliers.status');
            Route::get('/{id}/edit', 'edit')->name('suppliers.edit');
            Route::post('/update/{id}', 'update')->name('suppliers.update');
            Route::post('/destroy/{id}', 'destroy')->name('suppliers.destroy');
        });
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::prefix('customers')->group(function () {
            Route::get('/', 'index')->name('customers.index');
            Route::get('/create', 'create')->name('customers.create');
            Route::post('/store', 'store')->name('customers.store');
            Route::post('/status{id}', 'status')->name('customers.status');
            Route::get('/{id}/edit', 'edit')->name('customers.edit');
            Route::post('/update/{id}', 'update')->name('customers.update');
            Route::post('/destroy/{id}', 'destroy')->name('customers.destroy');
        });
    });

    Route::controller(ChartOfAccountSettingController::class)->group(function () {
        Route::prefix('chart-of-account-settings')->group(function () {
            Route::get('/', 'index')->name('chart-of-account-settings.index');
            Route::get('/create', 'create')->name('chart-of-account-settings.create');
            Route::post('/store', 'store')->name('chart-of-account-settings.store');
            Route::post('/active/{id}', 'active')->name('chart-of-account-settings.active');
            Route::get('/{id}/edit', 'edit')->name('chart-of-account-settings.edit');
            Route::post('/update/{id}', 'update')->name('chart-of-account-settings.update');
            Route::post('/destroy/{id}', 'destroy')->name('chart-of-account-settings.destroy');
        });
    });

    Route::controller(StockController::class)->group(function () {
        Route::prefix('stocks')->group(function () {
            Route::get('/openTraceStockModel', 'openTraceStockModel')->name('stocks.openTraceStockModel');
            Route::get('/loadTraceStockDetail', 'loadTraceStockDetail')->name('stocks.loadTraceStockDetail');
        });
    });

    Route::controller(POSController::class)->group(function () {
        Route::prefix('pos')->group(function () {
            Route::get('/', 'index')->name('pos.index');
            Route::get('/create', 'create')->name('pos.create');
            Route::post('/store', 'store')->name('pos.store');
            Route::post('/status', 'status')->name('pos.status');
            Route::get('/{id}/edit', 'edit')->name('pos.edit');
            Route::get('/show', 'show')->name('pos.show');
            Route::post('/update', 'update')->name('pos.update');
            Route::post('/destroy/{id}', 'destroy')->name('pos.destroy');
            Route::post('/filterProducts', 'filterProducts')->name('pos.filterProducts');
            Route::get('/getPurchaseOrdersBySupplierId', 'getPurchaseOrdersBySupplierId')->name('getPurchaseOrdersBySupplierId');
            Route::get('/loadAccountsDependPaymentType', 'loadAccountsDependPaymentType')->name('loadAccountsDependPaymentType');
            Route::get('/today-sales', [POSController::class, 'getTodaySales'])->name('pos.today-sales');
            Route::get('/last-month-sales', [POSController::class, 'getLastMonthSales'])->name('pos.last-month-sales');
        });
    });
    Route::controller(SaleReturnController::class)->group(function () {
        Route::prefix('sales-return')->group(function () {
            Route::get('/', 'index')->name('sales-return.index');
            Route::get('/create', 'create')->name('sales-return.create');
            Route::get('/load-order-details', 'loadOrderDetails')->name('sales-return.loadOrderDetails');
            Route::post('/store', 'store')->name('sales-return.store');
            Route::get('/{id}/edit', 'edit')->name('sales-return.edit');
            Route::get('/show', 'show')->name('sales-return.show');
            Route::post('/update', 'update')->name('sales-return.update');
            Route::post('/destroy/{id}', 'destroy')->name('sales-return.destroy');
            Route::post('/status/{id}', 'status')->name('sales-return.status');
            Route::post('/returnSaleReject/{id}', 'returnSaleReject')->name('sales-return.returnSaleReject');
            Route::post('/returnSaleRepost/{id}', 'returnSaleRepost')->name('sales-return.returnSaleRepost');
            Route::post('/returnSaleApprove/{id}', 'returnSaleApprove')->name('sales-return.returnSaleApprove');
            Route::post('/filterProducts', 'filterProducts')->name('sales-return.filterProducts');
        });
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::prefix('categories')->group(function () {
            Route::get('/', 'index')->name('categories.index');
            Route::get('/create', 'create')->name('categories.create');
            Route::post('/store', 'store')->name('categories.store');
            Route::post('/status/{id}', 'status')->name('categories.status');
            Route::get('/{id}/edit', 'edit')->name('categories.edit');
            Route::post('/update/{id}', 'update')->name('categories.update');
            Route::post('/destroy/{id}', 'destroy')->name('categories.destroy');
        });
    });

    Route::controller(TaxAccountsController::class)->group(function () {
        Route::prefix('tax-accounts')->group(function () {
            Route::get('/', 'index')->name('tax-accounts.index');
            Route::get('/create', 'create')->name('tax-accounts.create');
            Route::post('/store', 'store')->name('tax-accounts.store');
            Route::post('/status/{id}', 'status')->name('tax-accounts.status');
            Route::get('/{id}/edit', 'edit')->name('tax-accounts.edit');
            Route::post('/update/{id}', 'update')->name('tax-accounts.update');
            Route::post('/destroy/{id}', 'destroy')->name('tax-accounts.destroy');
        });
    });

    

    Route::controller(BrandController::class)->group(function () {
        Route::prefix('brands')->group(function () {
            Route::get('/', 'index')->name('brands.index');
            Route::get('/create', 'create')->name('brands.create');
            Route::post('/store', 'store')->name('brands.store');
            Route::post('/status/{id}', 'status')->name('brands.status');
            Route::get('/{id}/edit', 'edit')->name('brands.edit');
            Route::post('/update/{id}', 'update')->name('brands.update');
            Route::post('/destroy/{id}', 'destroy')->name('brands.destroy');
        });
    });

    Route::controller(SizeController::class)->group(function () {
        Route::prefix('sizes')->group(function () {
            Route::get('/', 'index')->name('sizes.index');
            Route::get('/create', 'create')->name('sizes.create');
            Route::post('/store', 'store')->name('sizes.store');
            Route::post('/status/{id}', 'status')->name('sizes.status');
            Route::get('/{id}/edit', 'edit')->name('sizes.edit');
            Route::post('/update/{id}', 'update')->name('sizes.update');
            Route::post('/destroy/{id}', 'destroy')->name('sizes.destroy');
        });
    });

    Route::controller(ProductController::class)->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', 'index')->name('products.index');
            Route::get('/create', 'create')->name('products.create');
            Route::post('/store', 'store')->name('products.store');
            Route::post('/status/{id}', 'status')->name('products.status');
            Route::get('/{id}/edit', 'edit')->name('products.edit');
            Route::post('/update/{id}', 'update')->name('products.update');
            Route::post('/destroy/{id}', 'destroy')->name('products.destroy');
        });
    });

    Route::controller(SettingController::class)->group(function () {
        Route::prefix('settings')->group(function () {
            Route::get('/', 'index')->name('settings.index');
            Route::get('/create', 'create')->name('settings.create');
            Route::post('/store', 'store')->name('settings.store');
        });
    });

    Route::controller(ReportController::class)->group(function () {
        Route::prefix('reports')->group(function () {
            Route::get('viewMonthlySummaryReport', 'viewMonthlySummaryReport')->name('reports.viewMonthlySummaryReport');
            Route::get('viewLedgerReport', 'viewLedgerReport')->name('reports.viewLedgerReport');
            Route::get('viewTrialBalanceReport', 'viewTrialBalanceReport')->name('reports.viewTrialBalanceReport');
            Route::get('viewProfitLossReport', 'viewProfitLossReport')->name('reports.viewProfitLossReport');
            Route::get('viewStockReport', 'viewStockReport')->name('reports.viewStockReport');
            Route::get('viewPayableReport', 'viewPayableReport')->name('reports.viewPayableReport');
            Route::get('viewAccountWisePayableSummary', 'viewAccountWisePayableSummary')->name('reports.viewAccountWisePayableSummary');
            Route::get('viewReceivableReport', 'viewReceivableReport')->name('reports.viewReceivableReport');
            Route::get('viewAccountWiseReceivableSummary', 'viewAccountWiseReceivableSummary')->name('reports.viewAccountWiseReceivableSummary');
        });
    });

    Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance-sheet.index');
    Route::controller(BalanceSheetController::class)->group(function () {
        Route::prefix('balance-sheet-report-settings')->group(function () {
            Route::get('/', 'balanceSheetReportSettingIndex')->name('balance-sheet-report-settings.index');
            Route::get('/create', 'create')->name('balance-sheet-report-settings.create');
            Route::post('/store', 'balanceSheetReportSettingStore')->name('balance-sheet-report-settings.store');
        });
    });


    Route::controller(SettingController::class)->group(function () {
        Route::prefix('profit-and-loss-report-settings')->group(function () {
            Route::get('/', 'profitAndLossReportSettingIndex')->name('profit-and-loss-report-settings.index');
            Route::get('/create', 'profitAndLossReportSettingCreate')->name('profit-and-loss-report-settings.create');
            Route::post('/store', 'profitAndLossReportSettingStore')->name('profit-and-loss-report-settings.store');
        });
    });

    Route::controller(SettingController::class)->group(function () {
        Route::prefix('purchase-invoice-and-payment-setting')->group(function () {
            Route::get('/create', 'purchaseInvoiceAndPaymentSettingCreate')->name('purchase-invoice-and-payment-setting.create');
            Route::post('/store', 'purchaseInvoiceAndPaymentSettingStore')->name('purchase-invoice-and-payment-setting.store');
        });
    });

    Route::controller(SettingController::class)->group(function () {
        Route::prefix('sale-invoice-and-payment-setting')->group(function () {
            Route::get('/create', 'saleInvoiceAndPaymentSettingCreate')->name('sale-invoice-and-payment-setting.create');
            Route::post('/store', 'saleInvoiceAndPaymentSettingStore')->name('sale-invoice-and-payment-setting.store');
        });
    });


    Route::controller(SettingController::class)->group(function () {
        Route::prefix('payable-and-receivable-report-settings')->group(function () {
            Route::get('/', 'payableAndReceivableReportSettingIndex')->name('payable-and-receivable-report-settings.index');
            Route::get('/create', 'payableAndReceivableReportSettingCreate')->name('payable-and-receivable-report-settings.create');
            Route::post('/store', 'payableAndReceivableReportSettingStore')->name('payable-and-receivable-report-settings.store');
        });
    });


    Route::get('cash-flow-statement', [CashFlowStatementController::class, 'index'])->name('reports.cash-flow-statement');
    Route::get('sales-report', [SalesReportController::class, 'index'])->name('reports.viewSalesReport');
});






include 'module/users.php';
//include 'module/store.php';
