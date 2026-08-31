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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Client Management
    Route::resource('clients', ClientController::class);
    
    // Invoice System
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    
    // Finance
    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::resource('incomes', IncomeController::class)->except(['show']);
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);
    
    // HR Management
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/{employee}/documents', [EmployeeController::class, 'storeDocument'])->name('employees.documents.store');
    Route::delete('employees/{employee}/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
    
    // Payslips
    Route::resource('payslips', PayslipController::class);
    Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'exportPdf'])->name('payslips.pdf');
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    
    // User Management (Owner only)
    Route::resource('users', UserController::class)->except(['show']);
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
