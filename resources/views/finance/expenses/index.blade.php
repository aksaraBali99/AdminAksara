<x-app-layout>
    <x-slot name="header">Expenses</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Expense Records</h2>
            <p class="mt-1 text-sm text-gray-500">Track all business expenses</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-teal-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Expense
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
        <div class="p-4">
            <form action="{{ route('expenses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                <select name="category_id" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Filter</button>
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-red-800">Total Expenses (filtered, converted to IDR)</span>
                <p class="text-xs text-red-600 mt-1">Kurs: 1 USD = Rp {{ number_format($exchangeRates['USD'], 0, ',', '.') }} | 1 AUD = Rp {{ number_format($exchangeRates['AUD'], 0, ',', '.') }}</p>
            </div>
            <span class="text-xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (IDR)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $expense->description }}
                            @if($expense->notes)
                            <p class="text-xs text-gray-400 mt-1">{{ Str::limit($expense->notes, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $expense->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                            {{ $expense->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-rose-600">
                            Rp {{ number_format($expense->amount_idr, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-gray-400 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No expense records</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $expenses->links() }}</div>
        @endif
    </div>
</x-app-layout>
