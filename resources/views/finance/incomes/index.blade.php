<x-app-layout>
    <x-slot name="header">Income</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Income Records</h2>
            <p class="mt-1 text-sm text-gray-500">Track all income from invoices and manual entries</p>
        </div>
        <a href="{{ route('incomes.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-teal-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Income
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
        <div class="p-4">
            <form action="{{ route('incomes.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                <select name="source" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">All Sources</option>
                    <option value="invoice_payment" {{ request('source') == 'invoice_payment' ? 'selected' : '' }}>Invoice Payment</option>
                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manual</option>
                </select>
                <select name="client_id" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Filter</button>
                    <a href="{{ route('incomes.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-green-800">Total Income (filtered, converted to IDR)</span>
                <p class="text-xs text-green-600 mt-1">Kurs: 1 USD = Rp {{ number_format($exchangeRates['USD'], 0, ',', '.') }} | 1 AUD = Rp {{ number_format($exchangeRates['AUD'], 0, ',', '.') }}</p>
            </div>
            <span class="text-xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (IDR)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($incomes as $income)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $income->income_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $income->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $income->client->company_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $income->source == 'invoice_payment' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $income->source == 'invoice_payment' ? 'Invoice' : 'Manual' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                            {{ $income->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-teal-600">
                            Rp {{ number_format($income->amount_idr, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if($income->source == 'manual')
                                <a href="{{ route('incomes.edit', $income) }}" class="text-gray-400 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @endif
                                
                                @if($income->source == 'manual' || auth()->user()->isOwner())
                                <form action="{{ route('incomes.destroy', $income) }}" method="POST" class="inline" onsubmit="return confirm('Delete this income?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                <span class="text-gray-400 text-xs">Auto</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No income records</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incomes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $incomes->links() }}</div>
        @endif
    </div>
</x-app-layout>
