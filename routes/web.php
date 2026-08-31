<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - all authenticated roles see it (content adapts per role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Client Management - owner, finance
    // These Gates already existed in AppServiceProvider and were used to hide
    // sidebar links, but were never enforced at the route level: any
    // authenticated user could reach any URL directly regardless of role.
    Route::middleware('can:manage-clients')->group(function () {
        Route::resource('clients', ClientController::class);
    });

    // Invoice System - owner, finance
    Route::middleware('can:manage-invoices')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    });

    // Finance & Reports - owner, finance
    Route::middleware('can:manage-finance')->group(function () {
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::resource('incomes', IncomeController::class)->except(['show']);
        Route::resource('expenses', ExpenseController::class)->except(['show']);
        Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    });

    // HR Management - owner, finance, hr
    Route::middleware('can:manage-employees')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/{employee}/documents', [EmployeeController::class, 'storeDocument'])->name('employees.documents.store');
        Route::delete('employees/{employee}/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
    });

    // Payslips - owner, finance, hr
    Route::middleware('can:manage-payslips')->group(function () {
        Route::resource('payslips', PayslipController::class);
        Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'exportPdf'])->name('payslips.pdf');
    });

    // User Management - owner only
    Route::middleware('can:manage-settings')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Profile - every authenticated role manages their own account regardless
    // of role, so this intentionally has no additional Gate.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
