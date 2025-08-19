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
use App\Http\Controllers\CashWithdrawalController; // NEW
use App\Http\Controllers\GoldTransactionController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UrfavController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes (No Auth)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [SimpleLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SimpleLoginController::class, 'login']);
Route::get('/logout', [SimpleLoginController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Protected Routes (With simpleauth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('simpleauth')->group(function () {

    // Dashboard
    Route::get('/dashboard-admin', [DashboardController::class, 'index'])->name('dashboard');

    // Projects - Export route harus di atas resource route
    Route::get('projects/export/excel', [ProjectController::class, 'exportExcel'])->name('projects.export.excel');
    Route::get('projects/{project}/print-invoice', [ProjectController::class, 'printInvoice'])->name('projects.print-invoice');
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
        // Expenses Management - Export route harus di atas resource
        Route::get('expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
        Route::resource('expenses', ExpenseController::class);
        Route::get('expenses/subcategories/{category}', [ExpenseController::class, 'getSubcategories'])->name('expenses.subcategories');

        // Bank Transfers Management
        Route::resource('bank-transfers', BankTransferController::class)->except(['edit', 'update', 'show']);
        Route::post('bank-transfers/batch', [BankTransferController::class, 'batchTransfer'])->name('bank-transfers.batch');

        // Cash Withdrawals Management - NEW
        Route::resource('cash-withdrawals', CashWithdrawalController::class);

        // Gold Transactions Management
        Route::resource('gold', GoldTransactionController::class)->only(['index', 'create', 'store', 'destroy']);

        // Financial Reports
        Route::get('reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');
    });

    // Task Management Routes untuk Admin
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        // Export routes
        Route::get('/export/excel', [TaskController::class, 'exportExcel'])->name('export-excel');

        // Validation routes
        Route::get('/validation/pending', [TaskController::class, 'validation'])->name('validation');
        Route::post('/assignments/{assignment}/validate', [TaskController::class, 'validateAssignment'])->name('validate-assignment');

        // Download attachment
        Route::get('/assignments/{assignment}/download', [TaskController::class, 'downloadAttachment'])->name('download-attachment');
    });

    // Minimal API Routes for AJAX requests
    Route::prefix('api')->group(function () {
        // Client creation for project form
        Route::post('clients', [ClientController::class, 'store'])->name('api.clients.store');

        // Gold portfolio data for forms that need it
        Route::get('gold/portfolio', [GoldTransactionController::class, 'getPortfolio'])->name('api.gold.portfolio');

        // Balance data for expense forms - NEW
        Route::get('balances', [ExpenseController::class, 'getBalances'])->name('api.balances');
    });

    // User Dashboard
    Route::get('/dashboard-user', [DashboardController::class, 'userIndex'])->name('dashboard.user');

    Route::prefix('tasks-user')->name('tasks.user.')->group(function () {
        Route::get('/', [TaskController::class, 'userIndex'])->name('index');
        Route::get('/assignments/{assignment}', [TaskController::class, 'userShow'])->name('show');
        Route::post('/assignments/{assignment}/submit', [TaskController::class, 'userSubmit'])->name('submit');
    });

    // Urfav Management Routes
    Route::prefix('urfav')->name('urfav.')->group(function () {
        Route::get('/', [UrfavController::class, 'index'])->name('index');
        Route::post('/', [UrfavController::class, 'store'])->name('store');
        Route::post('/import-jakmall', [UrfavController::class, 'importJakmall'])->name('import-jakmall');
        Route::post('/sync-shopee', [UrfavController::class, 'syncToShopee'])->name('sync-shopee');
        Route::post('/update-urutan', [UrfavController::class, 'updateUrutan'])->name('update-urutan');
        Route::post('/update-urutan-file', [UrfavController::class, 'updateUrutanFromFile'])->name('update-urutan-file');
        Route::patch('/products/{product}', [UrfavController::class, 'updateProduct'])->name('update-product');
        Route::get('/export-shopee', [UrfavController::class, 'exportShopee'])->name('export-shopee');
        Route::get('/export-all', [UrfavController::class, 'exportAll'])->name('export-all');
        Route::delete('/products/{product}', [UrfavController::class, 'destroy'])->name('destroy');
    });

    // Sierra Berak Routes
    Route::prefix('sierra-berak')->name('sierra-berak.')->group(function () {
        Route::get('/', [App\Http\Controllers\SierraBerakController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\SierraBerakController::class, 'store'])->name('store');
        Route::get('/date/{date}', [App\Http\Controllers\SierraBerakController::class, 'getByDate'])->name('get-by-date');
        Route::get('/{id}', [App\Http\Controllers\SierraBerakController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\SierraBerakController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\SierraBerakController::class, 'destroy'])->name('destroy');
    });

    Route::get('/price-list', function () {
        return view('price-list.index');
    })->name('price-list');
});

Route::get('/image/{filename}', function ($filename) {
    $path = public_path('image/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $file = file_get_contents($path);
    $type = mime_content_type($path);

    return response($file, 200)->header('Content-Type', $type);
})->where('filename', '.*\.(png|jpg|jpeg|gif|svg|ico|webp)$');
