<x-app-layout>
    <x-slot name="header">Edit Expense</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('expenses.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Expenses
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Edit Expense</h3>
            </div>

            <form action="{{ route('expenses.update', $expense) }}" method="POST" class="p-6 space-y-6">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                        <input type="text" name="description" id="description" value="{{ old('description', $expense->description) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency *</label>
                        <select name="currency" id="currency" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="IDR" {{ old('currency', $expense->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="USD" {{ old('currency', $expense->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="AUD" {{ old('currency', $expense->currency) == 'AUD' ? 'selected' : '' }}>AUD</option>
                        </select>
                    </div>

                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes', $expense->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Update Expense</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
