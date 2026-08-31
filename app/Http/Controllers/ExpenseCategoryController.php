<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{

    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->paginate(15);

        return view('finance.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('finance.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return view('finance.categories.edit', ['category' => $expense_category]);
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('expense_categories')->ignore($expense_category->id)],
            'description' => 'nullable|string',
        ]);

        $expense_category->update($validated);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        if ($expense_category->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', 'Cannot delete category with existing expenses.');
        }

        $expense_category->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
