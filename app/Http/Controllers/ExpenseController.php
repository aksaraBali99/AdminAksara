<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
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
        $query = Expense::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();
        
        // Add converted amount to each expense
        $expenses->getCollection()->transform(function($expense) {
            $expense->amount_idr = $this->convertToIDR($expense->amount, $expense->currency ?? 'IDR');
            return $expense;
        });
        
        // Calculate total expense in IDR
        $allExpenses = (clone $query)->get();
        $totalExpense = $allExpenses->sum(function($expense) {
            return $this->convertToIDR($expense->amount, $expense->currency ?? 'IDR');
        });
        
        $categories = ExpenseCategory::orderBy('name')->get();
        $exchangeRates = self::EXCHANGE_RATES;

        return view('finance.expenses.index', compact('expenses', 'categories', 'totalExpense', 'exchangeRates'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        
        return view('finance.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:IDR,USD,AUD',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        
        return view('finance.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:IDR,USD,AUD',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
