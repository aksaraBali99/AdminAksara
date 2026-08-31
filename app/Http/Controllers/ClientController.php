<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
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
        $query = Client::with(['assignedEmployee', 'incomes'])
            ->withCount('invoices');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $clients = $query->paginate(10)->withQueryString();
        
        // Add converted revenue to each client
        $clients->getCollection()->transform(function($client) {
            $client->total_revenue_idr = $client->incomes->sum(function($income) {
                return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
            });
            return $client;
        });
        
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('clients.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => 'required|email|unique:clients,email',
            'phone_number' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'assigned_employee_id' => 'nullable|exists:employees,id',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['invoices' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'assignedEmployee', 'incomes']);

        // Calculate total revenue in IDR
        $client->total_revenue_idr = $client->incomes->sum(function($income) {
            return $this->convertToIDR($income->amount, $income->currency ?? 'IDR');
        });

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $employees = Employee::where('employee_status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('clients.edit', compact('client', 'employees'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'email' => ['required', 'email', Rule::unique('clients')->ignore($client->id)],
            'phone_number' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'assigned_employee_id' => 'nullable|exists:employees,id',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
