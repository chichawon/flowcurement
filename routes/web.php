<?php

use App\Http\Controllers\BusinessPartnerPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryReceiptPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationPageController;
use App\Http\Controllers\SalesOrderPageController;
use App\Modules\UserManagement\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('login');

Route::get('/login', HomeController::class);

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->middleware(['permission:dashboard.view'])->name('dashboard');

    Route::prefix('user-management')
        ->name('user-management.')
        ->middleware(['permission:user-management.view'])
        ->group(function (): void {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->middleware('permission:user-management.create')->name('create');
            Route::post('/', [UserController::class, 'store'])->middleware('permission:user-management.create')->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:user-management.update')->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:user-management.update')->name('update');
        });

    Route::prefix('clients')
        ->name('clients.')
        ->middleware(['permission:business-partners.view'])
        ->group(function (): void {
            Route::get('/', [BusinessPartnerPageController::class, 'clientsIndex'])->name('index');
            Route::get('/create', [BusinessPartnerPageController::class, 'clientsCreate'])->middleware('permission:business-partners.create')->name('create');
            Route::get('/{businessPartner}', [BusinessPartnerPageController::class, 'clientsShow'])->name('show');
            Route::get('/{businessPartner}/edit', [BusinessPartnerPageController::class, 'clientsEdit'])->middleware('permission:business-partners.update')->name('edit');
        });

    Route::prefix('suppliers')
        ->name('suppliers.')
        ->middleware(['permission:business-partners.view'])
        ->group(function (): void {
            Route::get('/', [BusinessPartnerPageController::class, 'suppliersIndex'])->name('index');
            Route::get('/create', [BusinessPartnerPageController::class, 'suppliersCreate'])->middleware('permission:business-partners.create')->name('create');
            Route::get('/{businessPartner}', [BusinessPartnerPageController::class, 'suppliersShow'])->name('show');
            Route::get('/{businessPartner}/edit', [BusinessPartnerPageController::class, 'suppliersEdit'])->middleware('permission:business-partners.update')->name('edit');
        });

    Route::get('/items', [ItemPageController::class, 'redirectIndex'])->name('items.index');

    Route::prefix('items/local')
        ->name('local-items.')
        ->middleware(['permission:items.view'])
        ->group(function (): void {
            Route::get('/', [ItemPageController::class, 'localIndex'])->name('index');
            Route::get('/create', [ItemPageController::class, 'localCreate'])->middleware('permission:items.create')->name('create');
            Route::get('/{item}', [ItemPageController::class, 'localShow'])->name('show');
            Route::get('/{item}/edit', [ItemPageController::class, 'localEdit'])->middleware('permission:items.update')->name('edit');
        });

    Route::prefix('items/import')
        ->name('import-items.')
        ->middleware(['permission:items.view'])
        ->group(function (): void {
            Route::get('/', [ItemPageController::class, 'importIndex'])->name('index');
            Route::get('/create', [ItemPageController::class, 'importCreate'])->middleware('permission:items.create')->name('create');
            Route::get('/{item}', [ItemPageController::class, 'importShow'])->name('show');
            Route::get('/{item}/edit', [ItemPageController::class, 'importEdit'])->middleware('permission:items.update')->name('edit');
        });

    Route::prefix('quotations')
        ->name('quotations.')
        ->middleware(['permission:quotations.view'])
        ->group(function (): void {
            Route::get('/', [QuotationPageController::class, 'index'])->name('index');
            Route::get('/create', [QuotationPageController::class, 'create'])->middleware('permission:quotations.create')->name('create');
            Route::get('/{quotation}', [QuotationPageController::class, 'show'])->name('show');
            Route::get('/{quotation}/edit', [QuotationPageController::class, 'edit'])->middleware('permission:quotations.update')->name('edit');
            Route::get('/{quotation}/print', [QuotationPageController::class, 'print'])->middleware('permission:quotations.print')->name('print');
        });

    Route::prefix('sales/orders')
        ->name('sales.orders.')
        ->middleware(['permission:sales-orders.view'])
        ->group(function (): void {
            Route::get('/', [SalesOrderPageController::class, 'index'])->name('index');
            Route::get('/create', [SalesOrderPageController::class, 'create'])->middleware('permission:sales-orders.create')->name('create');
            Route::get('/{salesOrder}', [SalesOrderPageController::class, 'show'])->name('show');
            Route::get('/{salesOrder}/edit', [SalesOrderPageController::class, 'edit'])->middleware('permission:sales-orders.update')->name('edit');
        });

    Route::prefix('sales/delivery-receipts')
        ->name('sales.delivery-receipts.')
        ->middleware(['permission:sales-orders.view'])
        ->group(function (): void {
            Route::get('/', [DeliveryReceiptPageController::class, 'index'])->name('index');
            Route::get('/create', [DeliveryReceiptPageController::class, 'create'])->middleware('permission:sales-orders.create')->name('create');
            Route::get('/{deliveryReceipt}', [DeliveryReceiptPageController::class, 'show'])->name('show');
            Route::get('/{deliveryReceipt}/upload-details', [DeliveryReceiptPageController::class, 'uploadDetails'])->middleware('permission:sales-orders.update')->name('upload-details');
            Route::get('/{deliveryReceipt}/edit', [DeliveryReceiptPageController::class, 'edit'])->middleware('permission:sales-orders.update')->name('edit');
        });
});

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
