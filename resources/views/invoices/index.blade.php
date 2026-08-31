<x-app-layout>
    <x-slot name="header">Invoices</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            Invoice Management
        </h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Invoices</li>
            </ol>
        </nav>
    </div>

    <!-- Filters & Actions -->
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form action="{{ route('invoices.index') }}" method="GET">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-1 flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Invoice number or client..."
                               class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="w-full sm:w-auto min-w-[150px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Status</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto min-w-[150px]">
                        <label class="mb-2 block text-xs font-medium text-gray-600">Client</label>
                        <select name="client_id" class="w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Clients</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                        Filter
                    </button>
                    <a href="{{ route('invoices.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Clear
                    </a>
                    <a href="{{ route('invoices.create') }}" 
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Invoice
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Invoice</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Client</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Date</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Due Date</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Amount</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Status</th>
                        <th class="px-4 py-4 text-xs font-medium uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <span class="text-sm font-medium text-gray-800">{{ $invoice->invoice_number }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $invoice->client->company_name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $invoice->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-800">{{ $invoice->formatted_amount }}</td>
                        <td class="px-4 py-4">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-600',
                                    'sent' => 'bg-blue-100 text-blue-600',
                                    'paid' => 'bg-green-100 text-green-600',
                                    'overdue' => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}" 
                                   class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-500" title="View">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('invoices.pdf', $invoice) }}" 
                                   class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-green-500" title="PDF">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice) }}" 
                                   class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-amber-500" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-500" title="Delete">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <h3 class="mb-1 text-sm font-medium text-gray-800">No invoices found</h3>
                                <p class="mb-4 text-sm text-gray-500">Create your first invoice to get started.</p>
                                <a href="{{ route('invoices.create') }}" 
                                   class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    New Invoice
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($invoices->hasPages())
        <div class="border-t border-gray-200 px-4 py-4">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
