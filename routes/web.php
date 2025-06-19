<?php
// routes/web.php - Updated Savings Routes

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\ProjectTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Projects (Combined menu)
Route::resource('projects', ProjectController::class);
Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status');
Route::patch('projects/{project}/testimonial', [ProjectController::class, 'toggleTestimonial'])->name('projects.toggle-testimonial');

// Clients
Route::resource('clients', ClientController::class);

// Project Types
Route::resource('project-types', ProjectTypeController::class)->except(['show', 'edit', 'update', 'destroy']);

// Payments
Route::resource('payments', PaymentController::class);
Route::get('projects/{project}/payments/create', [PaymentController::class, 'createForProject'])->name('payments.create-for-project');

// Savings - Updated with manual transfer features
Route::resource('savings', SavingController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

// Savings Transfer Management
Route::get('savings/pending', [SavingController::class, 'getPendingSavings'])->name('savings.pending');
Route::post('savings/transfer', [SavingController::class, 'transferToBank'])->name('savings.transfer');
Route::post('savings/update-bank-balance', [SavingController::class, 'updateBankBalance'])->name('savings.update-bank-balance');
Route::get('savings/check-balance', [SavingController::class, 'checkBalance'])->name('savings.check-balance');
Route::get('savings/transfer-history', [SavingController::class, 'getTransferHistory'])->name('savings.transfer-history');

// API Routes for AJAX requests
Route::prefix('api')->group(function () {
    // Dashboard APIs
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('api.stats');

    // Project APIs
    Route::get('/projects/active', [ProjectController::class, 'getActiveProjects'])->name('api.projects.active');
    Route::get('/projects/deadlines', [ProjectController::class, 'getUpcomingDeadlines'])->name('api.projects.deadlines');

    // Client APIs
    Route::post('/clients', [ClientController::class, 'store'])->name('api.clients.store');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('api.clients.search');

    // Project Type APIs
    Route::post('/project-types', [ProjectTypeController::class, 'store'])->name('api.project-types.store');
    Route::get('/project-types/active', [ProjectTypeController::class, 'getActive'])->name('api.project-types.active');

    // Payment APIs
    Route::get('/payments/recent', [PaymentController::class, 'getRecentPayments'])->name('api.payments.recent');
});
