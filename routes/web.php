<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/settings', SettingsController::class)->name('settings');

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function (): void {
        Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [Customer\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [Customer\OrderController::class, 'store'])
            ->middleware('throttle:checkout')
            ->name('orders.store');
        Route::get('/orders/{order}/status', [Customer\OrderController::class, 'status'])->name('orders.status');
        Route::get('/orders/{order}', [Customer\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/approve-price', [Customer\OrderController::class, 'approvePrice'])->name('orders.approve-price');
        Route::post('/orders/{order}/cancel', [Customer\OrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('/addresses', [Customer\AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [Customer\AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [Customer\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
    });

    Route::middleware('role:admin,kasir')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/walk-in/create', [Admin\WalkInOrderController::class, 'create'])->name('orders.walk-in.create');
        Route::post('/orders/walk-in', [Admin\WalkInOrderController::class, 'store'])->name('orders.walk-in.store');
        Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/recalculate', [Admin\OrderController::class, 'recalculate'])->name('orders.recalculate');
        Route::post('/orders/{order}/status', [Admin\OrderController::class, 'transition'])->name('orders.status');
        Route::post('/orders/{order}/approve-price', [Admin\OrderController::class, 'approvePrice'])->name('orders.approve-price');
        Route::post('/orders/{order}/payments', [Admin\PaymentController::class, 'store'])->name('orders.payments.store');
        Route::post('/payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/orders/{order}/attachments', [Admin\AttachmentController::class, 'store'])->name('orders.attachments.store');
        Route::post('/orders/{order}/whatsapp', [Admin\WaNotificationController::class, 'store'])->name('orders.whatsapp');

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/staff', [Admin\StaffController::class, 'index'])->name('staff.index');
            Route::post('/staff', [Admin\StaffController::class, 'store'])->name('staff.store');
            Route::put('/staff/{user}', [Admin\StaffController::class, 'update'])->name('staff.update');
        });

        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [Admin\ReportController::class, 'pdf'])->middleware('throttle:export')->name('reports.pdf');
        Route::get('/reports/excel', [Admin\ReportController::class, 'excel'])->middleware('throttle:export')->name('reports.excel');

        Route::get('/catalog/categories', [Admin\ServiceCategoryController::class, 'index'])->name('catalog.categories');
        Route::post('/catalog/categories', [Admin\ServiceCategoryController::class, 'store'])->name('catalog.categories.store');
        Route::put('/catalog/categories/{serviceCategory}', [Admin\ServiceCategoryController::class, 'update'])->name('catalog.categories.update');
        Route::delete('/catalog/categories/{serviceCategory}', [Admin\ServiceCategoryController::class, 'destroy'])->name('catalog.categories.destroy');
        Route::get('/catalog/services', [Admin\ServiceController::class, 'index'])->name('catalog.services');
        Route::post('/catalog/services', [Admin\ServiceController::class, 'store'])->name('catalog.services.store');
        Route::put('/catalog/services/{laundryService}', [Admin\ServiceController::class, 'update'])->name('catalog.services.update');
        Route::delete('/catalog/services/{laundryService}', [Admin\ServiceController::class, 'destroy'])->name('catalog.services.destroy');
    });
});
