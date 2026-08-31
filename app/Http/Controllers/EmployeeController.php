<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        $query = Employee::withCount(['documents', 'payslips', 'assignedClients']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('employee_status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('employee_type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $employees = $query->paginate(10)->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $types = Employee::EMPLOYEE_TYPES;
        $statuses = Employee::EMPLOYEE_STATUSES;
        $ktpStatuses = Employee::KTP_STATUSES;

        return view('employees.create', compact('types', 'statuses', 'ktpStatuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:30',
            'ktp_number' => 'nullable|string|max:20',
            'ktp_status' => 'required|in:verified,pending,not_submitted',
            'start_work_date' => 'required|date',
            'position' => 'required|string|max:255',
            'employee_type' => 'required|in:freelance,full_time,part_time',
            'employee_status' => 'required|in:active,terminated,resigned',
            'base_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['documents', 'payslips' => function ($query) {
            $query->orderBy('period_end', 'desc')->limit(12);
        }, 'assignedClients']);

        $documentTypes = EmployeeDocument::DOCUMENT_TYPES;

        return view('employees.show', compact('employee', 'documentTypes'));
    }

    public function edit(Employee $employee)
    {
        $types = Employee::EMPLOYEE_TYPES;
        $statuses = Employee::EMPLOYEE_STATUSES;
        $ktpStatuses = Employee::KTP_STATUSES;

        return view('employees.edit', compact('employee', 'types', 'statuses', 'ktpStatuses'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee->id)],
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:30',
            'ktp_number' => 'nullable|string|max:20',
            'ktp_status' => 'required|in:verified,pending,not_submitted',
            'start_work_date' => 'required|date',
            'position' => 'required|string|max:255',
            'employee_type' => 'required|in:freelance,full_time,part_time',
            'employee_status' => 'required|in:active,terminated,resigned',
            'base_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Delete associated documents from storage
        foreach ($employee->documents as $document) {
            Storage::delete($document->file_path);
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function storeDocument(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:50',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store("employee-documents/{$employee->id}", 'public');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'document_type' => $validated['document_type'],
            'file_name' => $fileName,
            'file_path' => $filePath,
        ]);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Document uploaded successfully.');
    }

    public function destroyDocument(Employee $employee, EmployeeDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Document deleted successfully.');
    }
}
