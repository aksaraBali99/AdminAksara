<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\Payslip;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Exchange rates are now fetched from settings table in DB

    /**
     * Convert amount to IDR
     */
    private function convertToIDR($amount, $currency): float
    {
        if ($currency === 'IDR') return $amount;
        
        $key = 'exchange_rate_' . strtolower($currency);
        $rate = \App\Models\Setting::getValue($key, 1);
        
        return $amount * (float) $rate;
    }

    public function index()
    {
        // Invoice Statistics with currency conversion
        $unpaidInvoices = Invoice::whereIn('status', ['draft', 'sent', 'overdue'])->get();
        $paidInvoices = Invoice::where('status', 'paid')->get();
        
        $totalUnpaidInvoices = $unpaidInvoices->sum(function($invoice) {
            return $this->convertToIDR($invoice->amount, $invoice->currency ?? 'IDR');
        });
        $totalPaidInvoices = $paidInvoices->sum(function($invoice) {
            // Prioritize real IDR nominal received, fallback to converted amount
            return $invoice->paid_amount_idr ?? $this->convertToIDR($invoice->amount, $invoice->currency ?? 'IDR');
        });
        $unpaidCount = $unpaidInvoices->count();
        $paidCount = $paidInvoices->count();

        // Finance Statistics with currency conversion
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        $incomes = Income::whereYear('income_date', $currentYear)->get();
        $totalIncome = $incomes->sum(function($income) {
            // Since we're now enforcing IDR for new incomes, this direct sum is more accurate
            return $income->currency === 'IDR' ? $income->amount : $this->convertToIDR($income->amount, $income->currency);
        });
        $totalExpense = Expense::whereYear('expense_date', $currentYear)->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        // Monthly Income (for chart) with currency conversion
        $incomeData = [];
        $expenseData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthIncomes = Income::whereYear('income_date', $currentYear)
                ->whereMonth('income_date', $i)
                ->get();
            $incomeData[] = $monthIncomes->sum(function($income) {
                return $income->currency === 'IDR' ? $income->amount : $this->convertToIDR($income->amount, $income->currency);
            });
            
            $expenseData[] = Expense::whereYear('expense_date', $currentYear)
                ->whereMonth('expense_date', $i)
                ->sum('amount');
        }

        // Invoice Status Distribution (for pie chart)
        $invoiceStatusData = [
            'draft' => Invoice::where('status', 'draft')->count(),
            'sent' => Invoice::where('status', 'sent')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        // Recent Invoices
        $recentInvoices = Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top Clients by Revenue with conversion
        $topClients = Client::with(['incomes'])->get()
            ->map(function($client) {
                $client->total_revenue_idr = $client->incomes->sum(function($income) {
                    return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
                });
                return $client;
            })
            ->sortByDesc('total_revenue_idr')
            ->take(5)
            ->values();

        // Employee Statistics
        $activeEmployees = Employee::where('employee_status', 'active')->count();
        $totalClients = Client::count();

        // HR specific data
        $totalSalaryExpense = Payslip::whereYear('payment_date', $currentYear)->sum('net_salary');
        $recentPayslips = Payslip::with('employee')->orderBy('payment_date', 'desc')->limit(5)->get();

        return view('dashboard', compact(
            'totalUnpaidInvoices',
            'totalPaidInvoices',
            'unpaidCount',
            'paidCount',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'incomeData',
            'expenseData',
            'invoiceStatusData',
            'recentInvoices',
            'topClients',
            'activeEmployees',
            'totalClients',
            'currentYear',
            'totalSalaryExpense',
            'recentPayslips'
        ));
    }
}
