<x-app-layout>
    <x-slot name="header">Create Invoice</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Invoices
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">New Invoice</h3>
                <p class="mt-1 text-sm text-gray-500">Create a new invoice for your client</p>
            </div>

            <form action="{{ route('invoices.store') }}" method="POST" class="p-6 space-y-6" id="invoiceForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">Client *</label>
                        <select name="client_id" id="client_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }} - {{ $client->contact_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700">Invoice Date *</label>
                        <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency *</label>
                        <select name="currency" id="currency" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="IDR" {{ old('currency', 'IDR') == 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                        </select>
                    </div>
                </div>

                <!-- Invoice Items Section -->
                <div class="pt-6 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-gray-900">Invoice Items</h4>
                        <button type="button" onclick="addInvoiceItem()" class="px-3 py-1.5 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">
                            + Add Item
                        </button>
                    </div>

                    <div id="invoice-items-container" class="space-y-4">
                        <!-- Item rows will be added here -->
                    </div>

                    <div class="mt-6 flex justify-end">
                        <div class="bg-gray-50 px-6 py-4 rounded-lg min-w-sm">
                            <div class="text-right">
                                <span class="text-sm text-gray-600">Total: </span>
                                <span id="grand-total" class="text-xl font-bold text-teal-700">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('invoices.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Create Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemCounter = 0;

        function addInvoiceItem() {
            itemCounter++;
            const container = document.getElementById('invoice-items-container');
            const itemHtml = `
                <div class="invoice-item p-4 bg-gray-50 rounded-lg border border-gray-200" data-item-id="${itemCounter}">
                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-12 md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type of Fees *</label>
                            <select name="items[${itemCounter}][type_of_fees]" required class="block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Select</option>
                                <option value="virtual_assistant_fee">VA Fee</option>
                                <option value="monthly_benefit">Monthly Benefit</option>
                                <option value="annual_benefit">Annual Benefit</option>
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                            <input type="text" name="items[${itemCounter}][description]" required 
                                placeholder="e.g., Dhiya - Graphic Designer" 
                                class="block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Item/Hours *</label>
                            <input type="number" name="items[${itemCounter}][item_hours]" value="1" step="0.01" min="0" required 
                                oninput="calculateItemTotal(${itemCounter})"
                                class="item-hours block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Rate *</label>
                            <input type="number" name="items[${itemCounter}][rate]" step="0.01" min="0" required 
                                oninput="calculateItemTotal(${itemCounter})"
                                class="item-rate block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                        </div>

                        <div class="col-span-11 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total</label>
                            <input type="text" readonly class="item-total block w-full text-sm rounded-lg border-gray-200 bg-gray-100 text-gray-700 font-semibold">
                            <input type="hidden" name="items[${itemCounter}][total]" class="item-total-value">
                        </div>

                        <div class="col-span-1 flex items-end justify-center">
                            <button type="button" onclick="removeInvoiceItem(${itemCounter})" 
                                class="px-2 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="items[${itemCounter}][sort_order]" value="${itemCounter}">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        }

        function removeInvoiceItem(id) {
            const item = document.querySelector(`.invoice-item[data-item-id="${id}"]`);
            if (item) {
                item.remove();
                calculateGrandTotal();
            }
        }

        function calculateItemTotal(id) {
            const item = document.querySelector(`.invoice-item[data-item-id="${id}"]`);
            if (!item) return;

            const hours = parseFloat(item.querySelector('.item-hours').value) || 0;
            const rate = parseFloat(item.querySelector('.item-rate').value) || 0;
            const currency = document.getElementById('currency').value;
            const total = hours * rate;

            const symbols = { 'IDR': 'Rp', 'USD': '$', 'AUD': 'A$' };
            const symbol = symbols[currency] || currency;
            
            const formattedTotal = currency === 'IDR' 
                ? symbol + ' ' + total.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                : symbol + ' ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            item.querySelector('.item-total').value = formattedTotal;
            item.querySelector('.item-total-value').value = total;

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-total-value').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            const currency = document.getElementById('currency').value;
            const symbols = { 'IDR': 'Rp', 'USD': '$', 'AUD': 'A$' };
            const symbol = symbols[currency] || currency;
            
            const formattedTotal = currency === 'IDR' 
                ? symbol + ' ' + total.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                : symbol + ' ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const grandTotalEl = document.getElementById('grand-total');
            grandTotalEl.textContent = formattedTotal;
        }

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addInvoiceItem();
            
            // Recalculate when currency changes
            document.getElementById('currency').addEventListener('change', function() {
                // Recalculate all items
                document.querySelectorAll('.invoice-item').forEach(item => {
                    const itemId = item.getAttribute('data-item-id');
                    calculateItemTotal(itemId);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
