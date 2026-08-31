<x-app-layout>
    <x-slot name="header">Client Details</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Clients
            </a>
            <div class="flex gap-2">
                <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Invoice
                </a>
                <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Client Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16 bg-teal-100 rounded-full flex items-center justify-center">
                                <span class="text-teal-700 font-bold text-xl">{{ strtoupper(substr($client->company_name, 0, 2)) }}</span>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-bold text-gray-900">{{ $client->company_name }}</h2>
                                <p class="text-sm text-gray-500">{{ $client->contact_name }}</p>
                            </div>
                        </div>

                        <dl class="mt-6 space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->email }}</dd>
                            </div>
                            @if($client->phone_number)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->phone_number }}</dd>
                            </div>
                            @endif
                            @if($client->address)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->address }}</dd>
                            </div>
                            @endif
                            @if($client->start_date)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client Since</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->start_date->format('d M Y') }}</dd>
                            </div>
                            @endif
                            @if($client->assignedEmployee)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Employee</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $client->assignedEmployee->name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Revenue</span>
                            <span class="text-lg font-bold text-teal-600">Rp {{ number_format($client->total_revenue_idr, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Invoices</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($client->invoices as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $invoice->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->invoice_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $invoice->formatted_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800',
                                                'sent' => 'bg-blue-100 text-blue-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'overdue' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-teal-600 hover:text-teal-900">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No invoices yet for this client
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
