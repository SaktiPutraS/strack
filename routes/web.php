<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Projects
Route::resource('projects', ProjectController::class);
Route::patch('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.status');

// Clients
Route::resource('clients', ClientController::class);

// Payments
Route::resource('payments', PaymentController::class);
Route::get('projects/{project}/payments/create', [PaymentController::class, 'createForProject'])->name('payments.create-for-project');

// Savings
Route::resource('savings', SavingController::class);
Route::patch('savings/{saving}/verify', [SavingController::class, 'verify'])->name('savings.verify');
Route::post('savings/bulk-verify', [SavingController::class, 'bulkVerify'])->name('savings.bulk-verify');

// Testimonials
Route::resource('testimonials', TestimonialController::class);
Route::patch('testimonials/{testimonial}/publish', [TestimonialController::class, 'togglePublish'])->name('testimonials.publish');

// API Routes for AJAX requests
Route::prefix('api')->group(function () {
    // Dashboard APIs
    Route::get('/stats', [DashboardController::class, 'getStats'])->name('api.stats');
    Route::get('/financial-chart', [DashboardController::class, 'getFinancialChart'])->name('api.financial-chart');
    Route::get('/overdue-projects', [DashboardController::class, 'getOverdueProjects'])->name('api.overdue-projects');
    Route::get('/quick-actions', [DashboardController::class, 'getQuickActions'])->name('api.quick-actions');

    // Project APIs
    Route::get('/projects/active', [ProjectController::class, 'getActiveProjects'])->name('api.projects.active');
    Route::get('/projects/deadlines', [ProjectController::class, 'getUpcomingDeadlines'])->name('api.projects.deadlines');
    Route::get('/projects/{project}/payments', [ProjectController::class, 'getProjectPayments'])->name('api.projects.payments');

    // Client APIs
    Route::get('/clients/search', [ClientController::class, 'search'])->name('api.clients.search');
    Route::get('/clients/{client}/projects', [ClientController::class, 'getClientProjects'])->name('api.clients.projects');

    // Payment APIs
    Route::get('/payments/recent', [PaymentController::class, 'getRecentPayments'])->name('api.payments.recent');
    Route::get('/payments/monthly', [PaymentController::class, 'getMonthlyPayments'])->name('api.payments.monthly');

    // Saving APIs
    Route::get('/savings/balance-check', [SavingController::class, 'checkBalance'])->name('api.savings.balance-check');
    Route::get('/savings/summary', [SavingController::class, 'getSummary'])->name('api.savings.summary');

    // Testimonial APIs
    Route::get('/testimonials/published', [TestimonialController::class, 'getPublished'])->name('api.testimonials.published');
});

// Export Routes
Route::prefix('export')->group(function () {
    Route::get('/projects', [ProjectController::class, 'export'])->name('projects.export');
    Route::get('/payments', [PaymentController::class, 'export'])->name('payments.export');
    Route::get('/clients', [ClientController::class, 'export'])->name('clients.export');
    Route::get('/financial-report', [DashboardController::class, 'exportFinancialReport'])->name('export.financial-report');
});

// Import Routes
Route::prefix('import')->group(function () {
    Route::post('/projects', [ProjectController::class, 'import'])->name('projects.import');
    Route::post('/clients', [ClientController::class, 'import'])->name('clients.import');
});
