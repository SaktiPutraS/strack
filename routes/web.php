<?php
// routes/web.php - FIXED VERSION

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\GoldTransactionController;
use App\Http\Controllers\FinancialReportController;
use App\Models\Expense;
use App\Models\BankBalance;
use App\Models\GoldTransaction;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ✅ FIX: Use only one dashboard route
Route::get('/', function () {
    // Temporary dashboard with sample data until FinancialReportController is ready
    return view('financial-reports.dashboard', [
        'bankBalance' => 5000000,
        'goldPortfolio' => [
            'summary' => [
                'total_grams' => 2.5,
                'current_value' => 5250000,
            ],
            'transactions' => collect([]),
        ],
        'netWorth' => 10250000,
        'monthlyProfit' => [
            'result' => [
                'operational_profit' => 3500000,
            ],
        ],
        'untransferredAmount' => 1500000,
        'monthlyExpensesByCategory' => collect([
            'Operasional' => 800000,
            'Marketing' => 500000,
            'Entertainment' => 200000,
        ]),
    ]);
})->name('dashboard');

// Projects
Route::resource('projects', ProjectController::class);
Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status');

// Clients
Route::resource('clients', ClientController::class);

// Project Types
Route::resource('project-types', ProjectTypeController::class)->except(['show', 'edit', 'update', 'destroy']);

// Payments
Route::resource('payments', PaymentController::class);
Route::get('projects/{project}/payments/create', [PaymentController::class, 'createForProject'])->name('payments.create-for-project');

// Financial System Routes
Route::prefix('financial')->group(function () {

    // Expenses Management
    Route::get('expenses', function () {
        return view('expenses.index', [
            'expenses' => collect([]),
            'totalExpenses' => 0,
            'monthlyExpenses' => 0,
            'expensesByCategory' => collect([]),
        ]);
    })->name('expenses.index');

    Route::get('expenses/create', function () {
        return view('expenses.create');
    })->name('expenses.create');

    Route::post('expenses', function (Request $request) {
        // Temporary store logic
        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil ditambahkan!');
    })->name('expenses.store');

    // ✅ FIX: Subcategories route with correct path
    Route::get('expenses/subcategories/{category}', function ($category) {
        $subcategories = [
            'OPERASIONAL' => [
                'hosting_domain' => 'Hosting & Domain',
                'software_tools' => 'Software & Tools',
                'internet_komunikasi' => 'Internet & Komunikasi',
                'listrik_utilitas' => 'Listrik & Utilitas',
            ],
            'MARKETING' => [
                'iklan_online' => 'Iklan Online',
                'promosi_campaign' => 'Promosi & Campaign',
                'content_tools' => 'Content Creation Tools',
            ],
            'PENGEMBANGAN' => [
                'training_course' => 'Training & Course',
                'hardware_equipment' => 'Hardware & Equipment',
                'third_party_services' => 'Third-party Services',
            ],
            'GAJI_FREELANCE' => [
                'gaji_freelancer' => 'Gaji Freelancer',
                'fee_project' => 'Fee Project',
                'bonus_insentif' => 'Bonus & Insentif',
            ],
            'ENTERTAINMENT' => [
                'kopi_makanan' => 'Kopi & Makanan',
                'makan_kerja' => 'Makan Kerja',
                'snack_minuman' => 'Snack & Minuman',
                'entertainment_pribadi' => 'Entertainment Pribadi',
            ],
            'LAIN_LAIN' => [
                'transportasi' => 'Transportasi',
                'pajak_admin' => 'Pajak & Administrasi',
                'misc' => 'Misc Expenses',
            ],
        ];

        return response()->json($subcategories[$category] ?? []);
    })->name('expenses.subcategories');

    // Bank Transfers Management
    Route::get('bank-transfers', function () {
        return view('bank-transfers.index', [
            'totalTransferred' => 5000000,
            'totalUntransferred' => 2000000,
            'untransferredPayments' => collect([]),
            'transfers' => collect([]),
        ]);
    })->name('bank-transfers.index');

    Route::get('bank-transfers/create', function () {
        return view('bank-transfers.create', [
            'untransferredPayments' => collect([]),
        ]);
    })->name('bank-transfers.create');

    Route::post('bank-transfers', function (Request $request) {
        return redirect()->route('bank-transfers.index')->with('success', 'Transfer berhasil dicatat!');
    })->name('bank-transfers.store');

    Route::post('bank-transfers/batch', function (Request $request) {
        return redirect()->route('bank-transfers.index')->with('success', 'Transfer batch berhasil!');
    })->name('bank-transfers.batch');

    // Gold Transactions Management
    Route::get('gold', function () {
        return view('gold.index', [
            'currentGrams' => 2.5,
            'averageBuyPrice' => 2100000,
            'totalInvestment' => 5250000,
            'currentValue' => 5250000,
            'transactions' => collect([]),
        ]);
    })->name('gold.index');

    Route::get('gold/create', function () {
        $type = request('type', 'BUY');
        return view('gold.create', [
            'type' => $type,
            'currentBalance' => 5000000,
        ]);
    })->name('gold.create');

    Route::post('gold', function (Request $request) {
        return redirect()->route('gold.index')->with('success', 'Transaksi emas berhasil dicatat!');
    })->name('gold.store');

    // Financial Reports
    Route::get('reports', function () {
        return view('financial-reports.index', [
            'profitLoss' => [],
            'balanceSheet' => [],
            'goldPortfolio' => [],
            'startDate' => now()->startOfMonth()->toDateString(),
            'endDate' => now()->endOfMonth()->toDateString(),
        ]);
    })->name('financial-reports.index');
});

// API Routes for AJAX requests
Route::prefix('api')->group(function () {
    // Dashboard APIs
    Route::get('/stats', function () {
        return response()->json([
            'projects' => ['total' => 10, 'active' => 5],
            'financial' => ['total_value' => 50000000],
        ]);
    })->name('api.stats');

    // Project APIs
    Route::get('/projects/active', function () {
        return response()->json([]);
    })->name('api.projects.active');

    // Client APIs
    Route::post('/clients', [ClientController::class, 'store'])->name('api.clients.store');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('api.clients.search');

    // Project Type APIs
    Route::post('/project-types', function (Request $request) {
        return response()->json(['success' => true, 'project_type' => ['id' => 1, 'name' => 'TEST']]);
    })->name('api.project-types.store');
});

// API Routes for Financial System
Route::prefix('api/financial')->group(function () {

    // Expense APIs
    Route::get('expenses/categories', function () {
        return response()->json([
            'OPERASIONAL' => 'Operasional',
            'MARKETING' => 'Marketing',
            'PENGEMBANGAN' => 'Pengembangan',
            'GAJI_FREELANCE' => 'Gaji & Freelance',
            'ENTERTAINMENT' => 'Entertainment',
            'LAIN_LAIN' => 'Lain-lain',
        ]);
    })->name('api.expenses.categories');

    // Bank Transfer APIs
    Route::get('bank-transfers/untransferred', function () {
        return response()->json([]);
    })->name('api.bank-transfers.untransferred');

    // Gold APIs
    Route::get('gold/portfolio', function () {
        return response()->json([
            'current_grams' => 2.5,
            'average_buy_price' => 2100000,
            'total_investment' => 5250000,
            'current_value' => 5250000,
            'formatted_current_grams' => '2.500',
            'formatted_average_buy_price' => 'Rp 2.100.000',
            'formatted_total_investment' => 'Rp 5.250.000',
            'formatted_current_value' => 'Rp 5.250.000',
        ]);
    })->name('api.gold.portfolio');

    Route::get('gold/current-balance', function () {
        return response()->json([
            'current_balance' => 5000000,
            'formatted_balance' => 'Rp 5.000.000',
        ]);
    })->name('api.gold.current-balance');

    // Financial Reports APIs
    Route::get('reports/summary', function () {
        return response()->json([
            'profit_loss' => ['result' => ['operational_profit' => 3500000]],
            'balance_sheet' => ['assets' => ['bank_octo_balance' => 5000000]],
            'gold_portfolio' => ['summary' => ['total_grams' => 2.5]],
        ]);
    })->name('api.financial-reports.summary');
});

// Quick Actions Routes (for dashboard buttons)
Route::post('quick-expense', function (Request $request) {
    $validated = $request->validate([
        'amount' => 'required|numeric|min:1',
        'category' => 'required|string',
        'description' => 'required|string|max:255',
    ]);

    // Temporary success response
    return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil ditambahkan!']);
})->name('quick-expense');

Route::post('quick-gold-buy', function (Request $request) {
    $validated = $request->validate([
        'grams' => 'required|numeric|min:0.001',
        'total_price' => 'required|numeric|min:1',
        'notes' => 'nullable|string|max:255',
    ]);

    // Check balance (temporary)
    if ($validated['total_price'] > 5000000) {
        return response()->json(['success' => false, 'message' => 'Saldo tidak mencukupi!'], 400);
    }

    return response()->json(['success' => true, 'message' => 'Pembelian emas berhasil!']);
})->name('quick-gold-buy');
