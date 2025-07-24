<?php
// routes/web.php

use App\Http\Controllers\SimpleLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\GoldTransactionController;
use App\Http\Controllers\FinancialReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Auth)
|--------------------------------------------------------------------------
*/

Route::get('/login', [SimpleLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SimpleLoginController::class, 'login']);
Route::get('/logout', [SimpleLoginController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Protected Routes (With simpleauth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('simpleauth')->group(function () {

    if (session('role') === 'admin') {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Projects
        Route::resource('projects', ProjectController::class);
        Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status');
        Route::patch('projects/{project}/testimoni', [ProjectController::class, 'updateTestimoni'])->name('projects.testimoni');

        // Project Types Management
        Route::resource('project-types', ProjectTypeController::class);
        Route::patch('project-types/{projectType}/toggle', [ProjectTypeController::class, 'toggle'])
            ->name('project-types.toggle');

        // Clients
        Route::resource('clients', ClientController::class);

        // Payments
        Route::resource('payments', PaymentController::class);
        Route::get('projects/{project}/payments/create', [PaymentController::class, 'createForProject'])->name('payments.create-for-project');

        // Financial Management Routes
        Route::prefix('financial')->group(function () {
            // Expenses Management
            Route::resource('expenses', ExpenseController::class);
            Route::get('expenses/subcategories/{category}', [ExpenseController::class, 'getSubcategories'])->name('expenses.subcategories');

            // Bank Transfers Management
            Route::resource('bank-transfers', BankTransferController::class)->except(['edit', 'update', 'show']);
            Route::post('bank-transfers/batch', [BankTransferController::class, 'batchTransfer'])->name('bank-transfers.batch');

            // Gold Transactions Management
            Route::resource('gold', GoldTransactionController::class)->only(['index', 'create', 'store', 'destroy']);

            // Financial Reports
            Route::get('reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');
        });

        // Minimal API Routes for AJAX requests
        Route::prefix('api')->group(function () {
            // Client creation for project form
            Route::post('clients', [ClientController::class, 'store'])->name('api.clients.store');

            // Gold portfolio data for forms that need it
            Route::get('gold/portfolio', [GoldTransactionController::class, 'getPortfolio'])->name('api.gold.portfolio');
        });
    } elseif (session('role') === 'user') {
        // User Dashboard
        Route::get('/', [DashboardController::class, 'userIndex'])->name('dashboard.user');
    }
});
