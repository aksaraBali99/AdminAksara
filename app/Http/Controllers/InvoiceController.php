<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * A typed-in paid amount that's wildly out of proportion to the invoice
     * total is almost always a data-entry mistake (e.g. an extra "00"),
     * not a legitimate partial payment or FX swing. Real variance from
     * partial payments, discounts, or exchange rate movement stays well
     * inside this 0.3x-3x band.
     */
    private const PAID_AMOUNT_MIN_RATIO = 0.3;
    private const PAID_AMOUNT_MAX_RATIO = 3.0;

    /**
     * Sanity-check a paid amount (in IDR) against the invoice's own amount,
     * converted to IDR at the current exchange rate.
     */
    private function isPaidAmountSane(float $paidAmountIdr, float $invoiceAmount, string $currency): bool
    {
        $expectedIdr = Invoice::convertToIdr($invoiceAmount, $currency);

        if ($expectedIdr <= 0) {
            return true;
        }

        $ratio = $paidAmountIdr / $expectedIdr;

        return $ratio >= self::PAID_AMOUNT_MIN_RATIO && $ratio <= self::PAID_AMOUNT_MAX_RATIO;
    }

    private function paidAmountSanityMessage(float $paidAmountIdr, float $invoiceAmount, string $currency): string
    {
        $expectedIdr = Invoice::convertToIdr($invoiceAmount, $currency);

        return sprintf(
            'The paid amount (Rp %s) looks too far off from the invoice total (%s %s, ≈ Rp %s at the current exchange rate). Please double-check for typos such as extra digits.',
            number_format($paidAmountIdr, 0, ',', '.'),
            $currency,
            number_format($invoiceAmount, 2, '.', ','),
            number_format($expectedIdr, 0, ',', '.')
        );
    }

    public function index(Request $request)
    {
        $query = Invoice::with('client');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q2) use ($search) {
                      $q2->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $invoices = $query->paginate(10)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        
        // Update overdue status
        Invoice::whereIn('status', ['draft', 'sent'])
            ->whereDate('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);
        
        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('company_name')->get();
        $serviceTypes = Invoice::SERVICE_TYPES;
        $currencies = Invoice::CURRENCIES;
        
        return view('invoices.create', compact('clients', 'serviceTypes', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,sent,paid,overdue',
            'currency' => 'required|in:IDR,USD,AUD',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type_of_fees' => 'required|in:virtual_assistant_fee,monthly_benefit,annual_benefit',
            'items.*.description' => 'required|string',
            'items.*.item_hours' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.sort_order' => 'required|integer',
            // Fields for paid status
            'paid_amount_idr' => [
                'required_if:status,paid',
                'nullable',
                'numeric',
                'min:0',
                function (string $attribute, $value, \Closure $fail) use ($request) {
                    if ($request->input('status') !== 'paid' || $value === null) {
                        return;
                    }

                    $amount = (float) collect($request->input('items', []))->sum('total');
                    $currency = (string) $request->input('currency');

                    if (! $this->isPaidAmountSane((float) $value, $amount, $currency)) {
                        $fail($this->paidAmountSanityMessage((float) $value, $amount, $currency));
                    }
                },
            ],
            'paid_date' => 'required_if:status,paid|nullable|date',
        ]);

        // Create invoice
        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'currency' => $validated['currency'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'paid_amount_idr' => $validated['paid_amount_idr'] ?? null,
            'paid_date' => $validated['paid_date'] ?? null,
            'service_type' => $validated['items'][array_key_first($validated['items'])]['type_of_fees'],
            'amount' => array_sum(array_column($validated['items'], 'total')),
            'description' => null,
        ]);

        // Create invoice items
        foreach ($validated['items'] as $itemData) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type_of_fees' => $itemData['type_of_fees'],
                'description' => $itemData['description'],
                'item_hours' => $itemData['item_hours'],
                'currency' => $validated['currency'], // Use header currency
                'rate' => $itemData['rate'],
                'total' => $itemData['total'],
                'sort_order' => $itemData['sort_order'],
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client', 'items');
        
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $clients = Client::orderBy('company_name')->get();
        $serviceTypes = Invoice::SERVICE_TYPES;
        $currencies = Invoice::CURRENCIES;
        
        return view('invoices.edit', compact('invoice', 'clients', 'serviceTypes', 'currencies'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,sent,paid,overdue',
            'currency' => 'required|in:IDR,USD,AUD',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type_of_fees' => 'required|in:virtual_assistant_fee,monthly_benefit,annual_benefit',
            'items.*.description' => 'required|string',
            'items.*.item_hours' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.sort_order' => 'required|integer',
            // Fields for paid status
            'paid_amount_idr' => [
                'required_if:status,paid',
                'nullable',
                'numeric',
                'min:0',
                function (string $attribute, $value, \Closure $fail) use ($request) {
                    if ($request->input('status') !== 'paid' || $value === null) {
                        return;
                    }

                    $amount = (float) collect($request->input('items', []))->sum('total');
                    $currency = (string) $request->input('currency');

                    if (! $this->isPaidAmountSane((float) $value, $amount, $currency)) {
                        $fail($this->paidAmountSanityMessage((float) $value, $amount, $currency));
                    }
                },
            ],
            'paid_date' => 'required_if:status,paid|nullable|date',
        ]);

        // Update invoice
        $invoice->update([
            'client_id' => $validated['client_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'currency' => $validated['currency'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'paid_amount_idr' => $validated['paid_amount_idr'] ?? $invoice->paid_amount_idr,
            'paid_date' => $validated['paid_date'] ?? $invoice->paid_date,
            'service_type' => $validated['items'][array_key_first($validated['items'])]['type_of_fees'],
            'amount' => array_sum(array_column($validated['items'], 'total')),
        ]);

        // Delete existing items and create new ones
        $invoice->items()->delete();
        
        foreach ($validated['items'] as $itemData) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type_of_fees' => $itemData['type_of_fees'],
                'description' => $itemData['description'],
                'item_hours' => $itemData['item_hours'],
                'currency' => $validated['currency'], // Use header currency
                'rate' => $itemData['rate'],
                'total' => $itemData['total'],
                'sort_order' => $itemData['sort_order'],
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $rules = [
            'status' => 'required|in:draft,sent,paid,overdue',
        ];

        if ($request->status === 'paid') {
            $rules['paid_amount_idr'] = [
                'required',
                'numeric',
                'min:0',
                function (string $attribute, $value, \Closure $fail) use ($invoice) {
                    if (! $this->isPaidAmountSane((float) $value, (float) $invoice->amount, $invoice->currency)) {
                        $fail($this->paidAmountSanityMessage((float) $value, (float) $invoice->amount, $invoice->currency));
                    }
                },
            ];
            $rules['paid_date'] = 'required|date';
        }

        $validated = $request->validate($rules);

        $updateData = ['status' => $validated['status']];
        if ($request->status === 'paid') {
            $updateData['paid_amount_idr'] = $validated['paid_amount_idr'];
            $updateData['paid_date'] = $validated['paid_date'];
        }

        $invoice->update($updateData);

        return redirect()->back()
            ->with('success', 'Invoice status updated successfully.');
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load('client', 'items');
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
