<x-app-layout>
    <x-slot name="header">Invoice Details</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Invoices
            </a>
            <div class="flex gap-2">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <!-- Invoice Preview -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="p-8 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-1 mb-4">
                            @if(file_exists(public_path('images/aksara.png')))
                            <img src="{{ asset('images/aksara.png') }}" alt="Aksara Logo" class="h-20">
                            @else
                            <div>
                                <h1 class="text-xl font-bold text-teal-700">AKSARA VIRTUAL</h1>
                                <p class="text-xs text-gray-400 tracking-wider">AGENCY</p>
                            </div>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">
                            <div class="font-semibold">PT Solusi Mitra Virtual</div>
                            <div>Jl Tukad Citarum No:14 Panjer, Denpasar Bali 80225</div>
                            <div>info@aksaravirtualagency.com</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-2xl font-bold text-gray-900">INVOICE</h2>
                        <p class="text-lg font-semibold text-teal-600 mt-1">{{ $invoice->invoice_number }}</p>
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'sent' => 'bg-blue-100 text-blue-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'overdue' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$invoice->status] }} mt-2">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Dates & Client Info -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100 bg-gray-50">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bill To:</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $invoice->client->company_name }}</p>
                    <p class="text-sm text-gray-600">{{ $invoice->client->contact_name }}</p>
                    <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                    @if($invoice->client->address)
                    <p class="text-sm text-gray-500 mt-2">{{ $invoice->client->address }}</p>
                    @endif
                </div>
                <div class="md:text-right">
                    <div class="space-y-2">
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice Date:</span>
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->invoice_date->format('d F Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date:</span>
                            <p class="text-sm font-medium {{ $invoice->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">{{ $invoice->due_date->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items Table -->
            <div class="p-8">
                @php
                    $typeLabels = [
                        'virtual_assistant_fee' => 'Virtual Assistant Fee',
                        'monthly_benefit' => 'Monthly Benefit',
                        'annual_benefit' => 'Annual Benefit'
                    ];
                @endphp
                
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-900">
                                <th class="pb-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 5%;">#</th>
                                <th class="pb-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 25%;">Type of Fees</th>
                                <th class="pb-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 35%;">Description</th>
                                <th class="pb-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 15%;">Item/Hours</th>
                                <th class="pb-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 10%;">Rate</th>
                                <th class="pb-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider" style="width: 10%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $index => $item)
                            <tr class="border-b border-gray-200">
                                <td class="py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="py-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $typeLabels[$item->type_of_fees] ?? $item->type_of_fees }}</p>
                                </td>
                                <td class="py-4">
                                    <p class="text-sm text-gray-600">{{ $item->description }}</p>
                                </td>
                                <td class="py-4 text-center text-sm text-gray-900">
                                    {{ number_format($item->item_hours, 2) }}
                                </td>
                                <td class="py-4 text-right text-sm text-gray-900">
                                    @if($invoice->currency === 'IDR')
                                        Rp {{ number_format($item->rate, 0, ',', '.') }}
                                    @else
                                        {{ $invoice->currency }} {{ number_format($item->rate, 2, '.', ',') }}
                                    @endif
                                </td>
                                <td class="py-4 text-right font-semibold text-sm text-gray-900">
                                    {{ $item->formatted_total }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-sm text-gray-500">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($invoice->items->isNotEmpty())
                        <tfoot>
                            <tr class="border-t-2 border-gray-900">
                                <td colspan="5" class="pt-4 text-right font-bold text-gray-900 text-base">TOTAL:</td>
                                <td class="pt-4 text-right text-xl font-bold text-teal-600">
                                    {{ $invoice->formatted_amount }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="px-8 pb-8 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notes:</h3>
                <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
            </div>
            @endif

            <!-- Payment Info -->
            <div class="px-8 py-6 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Payment Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="font-semibold text-gray-900">WISE</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="text-gray-600">Bank Name:</div>
                        <div class="text-gray-900">PT Bank Mandiri (Persero) Tbk.</div>
                        
                        <div class="text-gray-600">SWIFT / BIC code:</div>
                        <div class="text-gray-900">BMRIIDJAXXX</div>
                        
                        <div class="text-gray-600">Account Number:</div>
                        <div class="text-gray-900">145-00-7566667-3</div>
                        
                        <div class="text-gray-600">Name:</div>
                        <div class="text-gray-900">PT Solusi Mitra Virtual</div>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600 italic">
                    Thank you for your business and your support for local Balinese virtual assistants
                </div>
                <div class="mt-3 text-sm font-semibold text-gray-900">
                    Accounts<br>
                    Aksara Virtual Agency
                </div>
            </div>

            <!-- Quick Status Update -->
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <form action="{{ route('invoices.status', $invoice) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex-grow max-w-xs">
                            <label for="status_select" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Update Status:</label>
                            <select name="status" id="status_select" onchange="togglePaidForm(this.value)" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm py-2">
                                <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ old('status', $invoice->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <div id="paid_form_fields" class="{{ old('status', $invoice->status) == 'paid' ? 'flex' : 'hidden' }} flex-wrap gap-3">
                            <div class="w-48">
                                <label for="paid_amount_idr" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Real Nominal (IDR) *</label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <!-- <span class="text-gray-400 sm:text-sm font-medium">Rp</span> -->
                                    </div>
                                    <input type="number" name="paid_amount_idr" id="paid_amount_idr" value="{{ old('paid_amount_idr', $invoice->paid_amount_idr) }}" 
                                        class="block w-full pl-9 pr-3 py-2 border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm" 
                                        placeholder="0">
                                </div>
                                @error('paid_amount_idr') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="w-40">
                                <label for="paid_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Payment Date *</label>
                                <input type="date" name="paid_date" id="paid_date" value="{{ old('paid_date', $invoice->paid_date ? $invoice->paid_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                    class="block w-full border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm py-2">
                                @error('paid_date') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" class="px-5 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition-colors h-[38px] flex items-center">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePaidForm(status) {
            const paidFields = document.getElementById('paid_form_fields');
            if (status === 'paid') {
                paidFields.classList.remove('hidden');
                paidFields.classList.add('flex');
                document.getElementById('paid_amount_idr').required = true;
                document.getElementById('paid_date').required = true;
            } else {
                paidFields.classList.add('hidden');
                paidFields.classList.remove('flex');
                document.getElementById('paid_amount_idr').required = false;
                document.getElementById('paid_date').required = false;
            }
        }

        // Keep the "required" state in sync with the select's actual value on
        // load too, not just on change - e.g. after a failed status-update
        // submission where the fields re-render visible with old('status').
        togglePaidForm(document.getElementById('status_select').value);
    </script>
    @endpush
</x-app-layout>
