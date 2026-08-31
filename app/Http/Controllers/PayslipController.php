<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\PayslipItem;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PayslipController extends Controller
{

    public function index(Request $request)
    {
        $query = Payslip::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by period
        if ($request->filled('period_month') && $request->filled('period_year')) {
            $query->whereMonth('period_end', $request->period_month)
                  ->whereYear('period_end', $request->period_year);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payslip_number', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payslips = $query->orderBy('period_end', 'desc')->paginate(15)->withQueryString();
        $employees = Employee::where('employee_status', 'active')->orderBy('name')->get();

        return view('employees.payslips.index', compact('payslips', 'employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('employee_status', 'active')->orderBy('name')->get();
        $selectedEmployee = null;
        
        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        return view('employees.payslips.create', compact('employees', 'selectedEmployee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pph21' => 'nullable|numeric|min:0',
            'bpjs_kes' => 'nullable|numeric|min:0',
            'bpjs_tk' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.amount' => 'required_with:items|numeric',
            'items.*.sort_order' => 'required_with:items|integer',
        ]);

        // Set default values for nullable fields
        $validated['allowances'] = $validated['allowances'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['pph21'] = $validated['pph21'] ?? 0;
        $validated['bpjs_kes'] = $validated['bpjs_kes'] ?? 0;
        $validated['bpjs_tk'] = $validated['bpjs_tk'] ?? 0;

        // Calculate additional items total
        $itemsTotal = 0;
        if (!empty($validated['items'])) {
            $itemsTotal = array_sum(array_column($validated['items'], 'amount'));
        }

        // Calculate net salary (base_salary + allowances + items_total - deductions)
        $gross = $validated['base_salary'] + $validated['allowances'] + $itemsTotal;
        $totalDeductions = $validated['deductions'] + $validated['pph21'] + $validated['bpjs_kes'] + $validated['bpjs_tk'];
        $validated['net_salary'] = $gross - $totalDeductions;

        // Create payslip
        $payslip = Payslip::create([
            'employee_id' => $validated['employee_id'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'base_salary' => $validated['base_salary'],
            'allowances' => $validated['allowances'],
            'deductions' => $validated['deductions'],
            'pph21' => $validated['pph21'],
            'bpjs_kes' => $validated['bpjs_kes'],
            'bpjs_tk' => $validated['bpjs_tk'],
            'net_salary' => $validated['net_salary'],
            'payment_date' => $validated['payment_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Create payslip items
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                PayslipItem::create([
                    'payslip_id' => $payslip->id,
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'sort_order' => $itemData['sort_order'],
                ]);
            }
        }
        
        // Get the employee for expense description
        $employee = Employee::find($validated['employee_id']);
        
        // Create expense record for salary payment
        $expense = $this->createSalaryExpense($payslip, $employee);
        
        // Link expense to payslip
        if ($expense) {
            $payslip->update(['expense_id' => $expense->id]);
        }

        return redirect()->route('payslips.index')
            ->with('success', 'Payslip created successfully and expense recorded.');
    }

    public function show(Payslip $payslip)
    {
        $payslip->load('employee', 'items');

        return view('employees.payslips.show', compact('payslip'));
    }

    public function edit(Payslip $payslip)
    {
        $payslip->load('items');
        $employees = Employee::orderBy('name')->get();

        return view('employees.payslips.edit', compact('payslip', 'employees'));
    }

    public function update(Request $request, Payslip $payslip)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pph21' => 'nullable|numeric|min:0',
            'bpjs_kes' => 'nullable|numeric|min:0',
            'bpjs_tk' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.amount' => 'required_with:items|numeric',
            'items.*.sort_order' => 'required_with:items|integer',
        ]);

        // Set default values for nullable fields
        $validated['allowances'] = $validated['allowances'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['pph21'] = $validated['pph21'] ?? 0;
        $validated['bpjs_kes'] = $validated['bpjs_kes'] ?? 0;
        $validated['bpjs_tk'] = $validated['bpjs_tk'] ?? 0;

        // Calculate additional items total
        $itemsTotal = 0;
        if (!empty($validated['items'])) {
            $itemsTotal = array_sum(array_column($validated['items'], 'amount'));
        }

        // Calculate net salary (base_salary + allowances + items_total - deductions)
        $gross = $validated['base_salary'] + $validated['allowances'] + $itemsTotal;
        $totalDeductions = $validated['deductions'] + $validated['pph21'] + $validated['bpjs_kes'] + $validated['bpjs_tk'];
        $validated['net_salary'] = $gross - $totalDeductions;

        // Update payslip
        $payslip->update([
            'employee_id' => $validated['employee_id'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'base_salary' => $validated['base_salary'],
            'allowances' => $validated['allowances'],
            'deductions' => $validated['deductions'],
            'pph21' => $validated['pph21'],
            'bpjs_kes' => $validated['bpjs_kes'],
            'bpjs_tk' => $validated['bpjs_tk'],
            'net_salary' => $validated['net_salary'],
            'payment_date' => $validated['payment_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Delete existing items and create new ones
        $payslip->items()->delete();
        
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                PayslipItem::create([
                    'payslip_id' => $payslip->id,
                    'description' => $itemData['description'],
                    'amount' => $itemData['amount'],
                    'sort_order' => $itemData['sort_order'],
                ]);
            }
        }
        
        // Get the employee for expense description
        $employee = Employee::find($validated['employee_id']);
        
        // Update linked expense if exists
        if ($payslip->expense_id) {
            $this->updateSalaryExpense($payslip, $employee);
        } else {
            // Create expense if not exists
            $expense = $this->createSalaryExpense($payslip, $employee);
            if ($expense) {
                $payslip->update(['expense_id' => $expense->id]);
            }
        }

        return redirect()->route('payslips.index')
            ->with('success', 'Payslip updated successfully.');
    }

    public function destroy(Payslip $payslip)
    {
        // Delete linked expense if exists
        if ($payslip->expense_id) {
            Expense::where('id', $payslip->expense_id)->delete();
        }
        
        $payslip->delete();

        return redirect()->route('payslips.index')
            ->with('success', 'Payslip deleted successfully.');
    }

    public function exportPdf(Payslip $payslip)
    {
        $payslip->load('employee', 'items');

        $pdf = Pdf::loadView('employees.payslips.pdf', compact('payslip'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("payslip-{$payslip->payslip_number}.pdf");
    }
    
    /**
     * Create an expense record for salary payment
     */
    private function createSalaryExpense(Payslip $payslip, Employee $employee): ?Expense
    {
        // Find or create "Salary & Wages" category
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'Salary & Wages'],
            ['description' => 'Employee salaries and wages']
        );
        
        // Create expense record
        return Expense::create([
            'category_id' => $category->id,
            'description' => "Salary payment - {$employee->name} ({$payslip->payslip_number})",
            'amount' => $payslip->net_salary,
            'currency' => 'IDR',
            'expense_date' => $payslip->payment_date ?? $payslip->period_end,
            'notes' => "Period: {$payslip->period_label}",
        ]);
    }
    
    /**
     * Update an expense record for salary payment
     */
    private function updateSalaryExpense(Payslip $payslip, Employee $employee): void
    {
        $expense = Expense::find($payslip->expense_id);
        
        if ($expense) {
            $expense->update([
                'description' => "Salary payment - {$employee->name} ({$payslip->payslip_number})",
                'amount' => $payslip->net_salary,
                'expense_date' => $payslip->payment_date ?? $payslip->period_end,
                'notes' => "Period: {$payslip->period_label}",
            ]);
        }
    }
}
