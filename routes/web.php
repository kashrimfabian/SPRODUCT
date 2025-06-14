<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CanSizeController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ProductionBatchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\AlizetiController;
use App\Http\Controllers\UzalishajiController;
use App\Http\Controllers\UchujajiController;
use App\Http\Controllers\MauzoController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\CustomerDebitController;
use App\Http\Controllers\CustomerDebitPaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [CustomAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login']);
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');


Route::middleware(['admin'])->group(function ()
{
    Route::resource('users', UserController::class);
    Route::get('/price/{prices_id}/edit', [PriceController::class, 'edit'])->name('price.edit');
    Route::resource('can-sizes', CanSizeController::class);
    Route::get('/register', [CustomAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [CustomAuthController::class, 'register']);
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    Route::get('/reports/profit-loss/export', [ReportController::class, 'exportProfitLoss'])->name('reports.profit_loss.export');
    Route::resource('price', PriceController::class)->parameters(['price' => 'prices_id']);
    Route::get('/users/{id}/reset-password', [UserController::class, 'showResetPasswordForm'])->name('users.reset-password');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password.update');
    Route::post('/users/{id}/disable', [UserController::class, 'disableUser'])->name('users.disable');
    Route::post('/users/{id}/enable', [UserController::class, 'enableUser'])->name('users.enable');
    Route::resource('payment_methods', PaymentMethodController::class);
    Route::resource('products', ProductController::class);
});

Route::middleware(['user'])->group(function () {

    Route::resource('stocks', StockController::class);
    
    // Alizeti Routes
    Route::prefix('alizeti')->name('alizeti.')->group(function () {
        Route::get('/', [AlizetiController::class, 'index'])->name('index'); // This would usually be handled by resource, but explicit is fine
        Route::get('/create', [AlizetiController::class, 'create'])->name('create');
        Route::post('/', [AlizetiController::class, 'store'])->name('store');
        Route::get('/{alizeti}/edit', [AlizetiController::class, 'edit'])->name('edit');
        Route::put('/{alizeti}', [AlizetiController::class, 'update'])->name('update');
        Route::delete('/{alizeti}', [AlizetiController::class, 'destroy'])->name('destroy');
        Route::get('/summary', [AlizetiController::class, 'displaySummary'])->name('summary');
        Route::post('/generate-batch', [AlizetiController::class, 'generateBatch'])->name('generate-batch');
        Route::get('/export', [AlizetiController::class, 'exportExcel'])->name('export'); // For Excel Export
    });
    // Remove the redundant `Route::resource('alizeti', AlizetiController::class);` if it duplicates these

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('raw-materials', RawMaterialController::class);
    Route::resource('production-batches', ProductionBatchController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('sales', SaleController::class); // Note: You have 'Mauzo' for sales, consider consolidating.
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('change-password.update');
    
    Route::resource('uzalishaji', UzalishajiController::class);
    Route::resource('uchujaji', UchujajiController::class);

    // Mauzo (Sales) Routes - Grouped for consistency
    Route::prefix('mauzo')->name('mauzo.')->group(function () {
        // Summary Routes, all pointing to the single mauzo_summary view (with dynamic title)
        Route::get('/', [MauzoController::class, 'index'])->name('index'); // Mafuta Summary
        Route::get('/mashudu-summary', [MauzoController::class, 'mashuduSummary'])->name('mashudu_summary');
        Route::get('/ugido-summary', [MauzoController::class, 'ugidoSummary'])->name('ugido_summary');
        Route::get('/lami-summary', [MauzoController::class, 'lamiSummary'])->name('lami_summary');
        Route::get('/mafuta-summary', [MauzoController::class, 'mafutaSummary'])->name('mafuta_summary');

        // CRUD Operations
        Route::get('/create', [MauzoController::class, 'create'])->name('create');
        Route::post('/', [MauzoController::class, 'store'])->name('store');
        Route::get('/{mauzo}/edit', [MauzoController::class, 'edit'])->name('edit');
        Route::put('/{mauzo}', [MauzoController::class, 'update'])->name('update');
        Route::delete('/{mauzo}', [MauzoController::class, 'destroy'])->name('destroy'); // Moved inside group
        Route::post('/{mauzo}/confirm', [MauzoController::class, 'confirm'])->name('confirm'); // Moved inside group
    });

    
    Route::get('/get-price/{alizetiId}/{productId}', [MauzoController::class, 'getPrice'])->name('get-price');

    

    Route::resource('categories', CategoryController::class);
    Route::resource('loan_payments',LoanPaymentController::class);
    Route::resource('loans', LoanController::class);
    Route::resource('customer_debits', CustomerDebitController::class);
    Route::resource('customer_debit_payments', CustomerDebitPaymentController::class);
    Route::post('/loans/{loan}/confirm', [LoanController::class, 'confirm'])->name('loans.confirm');
});