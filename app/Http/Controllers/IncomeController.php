<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Client;
use Illuminate\Http\Request;

class IncomeController extends Controller
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
        $query = Income::with(['client', 'invoice']);

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('income_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('income_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        $incomes = $query->orderBy('income_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Add converted amount to each income
        $incomes->getCollection()->transform(function ($income) {
            $income->amount_idr = $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
            return $income;
        });

        // Calculate total income in IDR
        $allIncomes = (clone $query)->get();
        $totalIncome = $allIncomes->sum(function ($income) {
            return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
        });

        $clients = Client::orderBy('company_name')->get();
        $exchangeRates = self::EXCHANGE_RATES;

        return view('finance.incomes.index', compact('incomes', 'clients', 'totalIncome', 'exchangeRates'));
    }

    public function create()
    {
        $clients = Client::orderBy('company_name')->get();

        return view('finance.incomes.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:IDR,USD,AUD',
            'income_date' => 'required|date',
        ]);

        $validated['source'] = 'manual';

        Income::create($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        $clients = Client::orderBy('company_name')->get();

        return view('finance.incomes.edit', compact('income', 'clients'));
    }

    public function update(Request $request, Income $income)
    {
        // Only allow editing manual incomes
        if ($income->source !== 'manual') {
            return redirect()->route('incomes.index')
                ->with('error', 'Cannot edit invoice-generated income.');
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:IDR,USD,AUD',
            'income_date' => 'required|date',
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        // Only allow deleting manual incomes OR owner can delete any income
        if ($income->source !== 'manual' && !auth()->user()->isOwner()) {
            return redirect()->route('incomes.index')
                ->with('error', 'Cannot delete invoice-generated income.');
        }

        $income->delete();

        return redirect()->route('incomes.index')
            ->with('success', 'Income deleted successfully.');
    }
}
