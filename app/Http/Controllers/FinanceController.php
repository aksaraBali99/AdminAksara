<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Exchange rates to IDR (Indonesian Rupiah)
     */
    const EXCHANGE_RATES = [
        'IDR' => 1,
        'USD' => 15800,
        'AUD' => 10500,
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

        // Get incomes with currency for conversion
        $incomesQuery = Income::whereYear('income_date', $year);
        if ($month) {
            $incomesQuery->whereMonth('income_date', $month);
        }
        $incomes = $incomesQuery->get();

        // Get expenses
        $expensesQuery = Expense::whereYear('expense_date', $year);
        if ($month) {
            $expensesQuery->whereMonth('expense_date', $month);
        }
        
        // Calculate totals with currency conversion
        $totalIncome = $incomes->sum(function($income) {
            return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
        });
        $totalExpense = $expensesQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        // Monthly breakdown for chart - with currency conversion
        $incomeChartData = [];
        $expenseChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthIncomes = Income::whereYear('income_date', $year)
                ->whereMonth('income_date', $i)
                ->get();
            $incomeChartData[] = $monthIncomes->sum(function($income) {
                return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
            });
            
            $expenseChartData[] = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $i)
                ->sum('amount');
        }

        // Income by client with currency conversion
        $incomeByClient = Client::with(['incomes' => function($query) use ($year) {
                $query->whereYear('income_date', $year);
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
            ->take(10)
            ->values();

        // Recent transactions - add converted amount for display
        $recentIncomes = Income::with('client')
            ->orderBy('income_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function($income) {
                $income->amount_idr = $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
                return $income;
            });

        $recentExpenses = Expense::with('category')
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();

        // Years for filter
        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 1);
        
        // Exchange rates for display
        $exchangeRates = self::EXCHANGE_RATES;

        return view('finance.index', compact(
            'totalIncome',
            'totalExpense',
            'netProfit',
            'incomeChartData',
            'expenseChartData',
            'incomeByClient',
            'recentIncomes',
            'recentExpenses',
            'year',
            'month',
            'years',
            'exchangeRates'
        ));
    }
}
