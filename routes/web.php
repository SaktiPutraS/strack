<?php

use App\Http\Controllers\SimpleLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInvoiceController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\CashWithdrawalController;
use App\Http\Controllers\GoldTransactionController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UrfavController;
use App\Http\Controllers\CalendarNoteController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GuideController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [SimpleLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SimpleLoginController::class, 'login']);
Route::get('/logout', [SimpleLoginController::class, 'logout']);

Route::middleware('simpleauth')->group(function () {

    Route::get('/dashboard-admin', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('prospects', ProspectController::class);

    Route::get('projects/export/excel', [ProjectController::class, 'exportExcel'])->name('projects.export.excel');

    Route::get('projects/{project}/preview-invoice', [ProjectInvoiceController::class, 'previewInvoice'])->name('projects.preview-invoice');
    Route::get('projects/{project}/print-invoice', [ProjectInvoiceController::class, 'printInvoice'])->name('projects.print-invoice');
    Route::get('projects/{project}/preview-quotation', [ProjectInvoiceController::class, 'previewQuotation'])->name('projects.preview-quotation');
    Route::get('projects/{project}/print-quotation', [ProjectInvoiceController::class, 'printQuotation'])->name('projects.print-quotation');

    Route::resource('projects', ProjectController::class);
    Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status');
    Route::patch('projects/{project}/testimoni', [ProjectController::class, 'updateTestimoni'])->name('projects.testimoni');

    Route::resource('project-types', ProjectTypeController::class);
    Route::patch('project-types/{projectType}/toggle', [ProjectTypeController::class, 'toggle'])
        ->name('project-types.toggle');

    Route::resource('clients', ClientController::class);

    Route::resource('payments', PaymentController::class);
    Route::get('projects/{project}/payments/create', [PaymentController::class, 'createForProject'])->name('payments.create-for-project');

    Route::get('expenses/analysis', [ExpenseController::class, 'analysis'])->name('expenses.analysis');
    Route::get('expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
    Route::resource('expenses', ExpenseController::class);

    Route::prefix('financial')->group(function () {
        Route::get('expenses/export/excel', [ExpenseController::class, 'exportExcel'])->name('expenses.export.excel');
        Route::resource('expenses', ExpenseController::class);
        Route::get('expenses/subcategories/{category}', [ExpenseController::class, 'getSubcategories'])->name('expenses.subcategories');

        Route::resource('bank-transfers', BankTransferController::class)->except(['edit', 'update', 'show']);
        Route::post('bank-transfers/batch', [BankTransferController::class, 'batchTransfer'])->name('bank-transfers.batch');

        Route::resource('cash-withdrawals', CashWithdrawalController::class);

        Route::resource('gold', GoldTransactionController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::get('reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');
    });

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        Route::get('/export/excel', [TaskController::class, 'exportExcel'])->name('export-excel');

        Route::get('/validation/pending', [TaskController::class, 'validation'])->name('validation');
        Route::post('/assignments/{assignment}/validate', [TaskController::class, 'validateAssignment'])->name('validate-assignment');

        Route::get('/assignments/{assignment}/download', [TaskController::class, 'downloadAttachment'])->name('download-attachment');
    });

    Route::prefix('api')->group(function () {
        Route::post('clients', [ClientController::class, 'store'])->name('api.clients.store');

        Route::get('gold/portfolio', [GoldTransactionController::class, 'getPortfolio'])->name('api.gold.portfolio');

        Route::get('balances', [ExpenseController::class, 'getBalances'])->name('api.balances');
    });

    Route::get('/dashboard-user', [DashboardController::class, 'userIndex'])->name('dashboard.user');

    Route::prefix('tasks-user')->name('tasks.user.')->group(function () {
        Route::get('/', [TaskController::class, 'userIndex'])->name('index');
        Route::get('/assignments/{assignment}', [TaskController::class, 'userShow'])->name('show');
        Route::post('/assignments/{assignment}/submit', [TaskController::class, 'userSubmit'])->name('submit');
    });

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

    Route::prefix('sierra-berak')->name('sierra-berak.')->group(function () {
        Route::get('/', [App\Http\Controllers\SierraBerakController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\SierraBerakController::class, 'store'])->name('store');
        Route::get('/date/{date}', [App\Http\Controllers\SierraBerakController::class, 'getByDate'])->name('get-by-date');
        Route::get('/{id}', [App\Http\Controllers\SierraBerakController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\SierraBerakController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\SierraBerakController::class, 'destroy'])->name('destroy');
    });

    Route::get('/calendar-notes/month/{year}/{month}', [CalendarNoteController::class, 'getMonthNotes'])->name('calendar-notes.month');
    Route::post('/calendar-notes', [CalendarNoteController::class, 'store'])->name('calendar-notes.store');
    Route::put('/calendar-notes/{id}', [CalendarNoteController::class, 'update'])->name('calendar-notes.update');
    Route::delete('/calendar-notes/{id}', [CalendarNoteController::class, 'destroy'])->name('calendar-notes.destroy');

    Route::get('/projects/deadlines/month/{year}/{month}', [ProjectController::class, 'getMonthDeadlines'])->name('projects.deadlines.month');

    Route::get('/price-list', function () {
        return view('price-list.index');
    })->name('price-list');

    Route::prefix('guide-chat')->name('guide-chat.')->group(function () {
        Route::get('/', [GuideController::class, 'index'])->name('index');
        Route::get('/phase1', [GuideController::class, 'phase1'])->name('phase1');
        Route::get('/phase2', [GuideController::class, 'phase2'])->name('phase2');
        Route::get('/phase3', [GuideController::class, 'phase3'])->name('phase3');
        Route::get('/phase4', [GuideController::class, 'phase4'])->name('phase4');
        Route::get('/phase5', [GuideController::class, 'phase5'])->name('phase5');
        Route::get('/pricing', [GuideController::class, 'pricing'])->name('pricing');
    });

    Route::resource('supplies', SupplyController::class);
    Route::get('supplies/{supply}/use', [SupplyController::class, 'showUseForm'])->name('supplies.use-form');
    Route::post('supplies/{supply}/use', [SupplyController::class, 'recordUsage'])->name('supplies.record-usage');
    Route::delete('supply-usages/{usage}', [SupplyController::class, 'deleteUsage'])->name('supply-usages.destroy');
    Route::get('supplies/{supply}/add-stock', [SupplyController::class, 'showAddStockForm'])->name('supplies.add-stock-form');
    Route::post('supplies/{supply}/add-stock', [SupplyController::class, 'addStock'])->name('supplies.add-stock');

    Route::resource('budgets', BudgetController::class);
    Route::post('budget-items/{budgetItem}/toggle-complete', [BudgetController::class, 'toggleItemComplete'])
        ->name('budget-items.toggle-complete');
    Route::post('budget-items/bulk-toggle', [BudgetController::class, 'bulkToggleComplete'])
        ->name('budget-items.bulk-toggle');
    Route::post('budget-category/toggle', [BudgetController::class, 'toggleCategoryComplete'])
        ->name('budget-category.toggle');
    Route::put('budget-items/{budgetItem}', [BudgetController::class, 'updateItem'])
        ->name('budget-items.update');
    Route::get('budgets/{budget}/export', [BudgetController::class, 'exportExcel'])
        ->name('budgets.export');
    Route::post('budgets/{budget}/import', [BudgetController::class, 'importExcel'])
        ->name('budgets.import');
    Route::get('budgets-export-all', [BudgetController::class, 'exportAllExcel'])
        ->name('budgets.export-all');
    Route::post('budgets-import-all', [BudgetController::class, 'importAllExcel'])
        ->name('budgets.import-all');
    Route::get('budgets-report/{year?}', [BudgetController::class, 'report'])->name('budgets.report');
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
