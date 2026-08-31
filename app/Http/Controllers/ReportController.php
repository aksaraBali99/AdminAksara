<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Client;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    /**
     * Exchange rates to IDR (Indonesian Rupiah)
     * Update these rates as needed
     */
    const EXCHANGE_RATES = [
        'IDR' => 1,
        'USD' => 15800,  // 1 USD = 15,800 IDR
        'AUD' => 10500,  // 1 AUD = 10,500 IDR
    ];

    /**
     * Convert amount to IDR
     */
    private function convertToIDR($amount, $currency): float
    {
        $rate = self::EXCHANGE_RATES[$currency] ?? 1;
        return $amount * $rate;
    }

    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', null);

        // Date range
        $startDate = $month 
            ? Carbon::create($year, $month, 1)->startOfMonth()
            : Carbon::create($year, 1, 1)->startOfYear();
        $endDate = $month 
            ? Carbon::create($year, $month, 1)->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfYear();

        // 1. Unpaid Invoices Report (with currency conversion)
        $unpaidInvoices = Invoice::with('client')
            ->whereIn('status', ['draft', 'sent', 'overdue'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->orderBy('due_date')
            ->get();
        
        // Convert total unpaid to IDR
        $totalUnpaid = $unpaidInvoices->sum(function($invoice) {
            return $this->convertToIDR($invoice->amount, $invoice->currency);
        });

        // 2. Income Report (Total & Per Client) - with currency conversion
        $incomes = Income::whereBetween('income_date', [$startDate, $endDate])->get();
        $totalIncome = $incomes->sum(function($income) {
            return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
        });

        // Income by client with currency conversion
        $incomeByClient = Client::with(['incomes' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('income_date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($client) {
                $client->total_income_idr = $client->incomes->sum(function($income) {
                    return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
                });
                return $client;
            })
            ->filter(function($client) {
                return $client->total_income_idr > 0;
            })
            ->sortByDesc('total_income_idr')
            ->values();

        // 3. Expense Report (Total & By Category)
        $totalExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // withSum() computes expenses_sum_amount as a per-row correlated
        // subquery, not a true GROUP BY aggregate, so filtering/ordering by
        // it has to happen in PHP rather than via having()/orderByDesc():
        // MySQL tolerates HAVING on a plain SELECT-list alias as a
        // non-standard extension, but SQLite (and Postgres) reject it with
        // "HAVING clause on a non-aggregate query". Mirrors the same
        // filter+sort-in-PHP pattern already used for $incomeByClient above.
        $expenseByCategory = ExpenseCategory::withSum(['expenses' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }], 'amount')
            ->get()
            ->filter(fn ($category) => $category->expenses_sum_amount > 0)
            ->sortByDesc('expenses_sum_amount')
            ->values();

        // Monthly breakdown with currency conversion
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create($year, $m, 1)->startOfMonth();
            $monthEnd = Carbon::create($year, $m, 1)->endOfMonth();
            
            // Convert incomes to IDR
            $monthIncomes = Income::whereBetween('income_date', [$monthStart, $monthEnd])->get();
            $monthIncomeTotal = $monthIncomes->sum(function($income) {
                return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
            });
            
            $monthlyData[$m] = [
                'income' => $monthIncomeTotal,
                'expense' => Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            ];
        }

        // Years for filter
        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 1);

        // Exchange rates for display
        $exchangeRates = self::EXCHANGE_RATES;

        return view('reports.index', compact(
            'year',
            'month',
            'years',
            'startDate',
            'endDate',
            'unpaidInvoices',
            'totalUnpaid',
            'totalIncome',
            'incomeByClient',
            'totalExpense',
            'expenseByCategory',
            'monthlyData',
            'exchangeRates'
        ));
    }

    public function exportPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', null);

        $startDate = $month 
            ? Carbon::create($year, $month, 1)->startOfMonth()
            : Carbon::create($year, 1, 1)->startOfYear();
        $endDate = $month 
            ? Carbon::create($year, $month, 1)->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfYear();

        // Get all data with currency conversion
        $unpaidInvoices = Invoice::with('client')
            ->whereIn('status', ['draft', 'sent', 'overdue'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();
        
        $totalUnpaid = $unpaidInvoices->sum(function($invoice) {
            return $this->convertToIDR($invoice->amount, $invoice->currency);
        });

        $incomes = Income::whereBetween('income_date', [$startDate, $endDate])->get();
        $totalIncome = $incomes->sum(function($income) {
            return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
        });

        $incomeByClient = Client::with(['incomes' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('income_date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($client) {
                $client->total_income_idr = $client->incomes->sum(function($income) {
                    return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
                });
                return $client;
            })
            ->filter(function($client) {
                return $client->total_income_idr > 0;
            })
            ->sortByDesc('total_income_idr')
            ->values();

        $totalExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        // See the equivalent query in index() for why filtering happens in
        // PHP rather than via having() - MySQL-only SQL, not portable.
        $expenseByCategory = ExpenseCategory::withSum(['expenses' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }], 'amount')
            ->get()
            ->filter(fn ($category) => $category->expenses_sum_amount > 0)
            ->sortByDesc('expenses_sum_amount')
            ->values();

        $netProfit = $totalIncome - $totalExpense;
        $periodLabel = $month 
            ? Carbon::create($year, $month, 1)->format('F Y')
            : "Year $year";

        $exchangeRates = self::EXCHANGE_RATES;

        $pdf = PDF::loadView('reports.pdf', compact(
            'periodLabel',
            'unpaidInvoices',
            'totalUnpaid',
            'totalIncome',
            'incomeByClient',
            'totalExpense',
            'expenseByCategory',
            'netProfit',
            'exchangeRates'
        ));

        return $pdf->download("financial-report-{$year}" . ($month ? "-{$month}" : '') . ".pdf");
    }
}
