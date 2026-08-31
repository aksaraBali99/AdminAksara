<x-app-layout>
    <x-slot name="header">Edit Payslip</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('payslips.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Payslips
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Edit Payslip - {{ $payslip->payslip_number }}</h3>
            </div>

            <form action="{{ route('payslips.update', $payslip) }}" method="POST" class="p-6 space-y-6" id="payslipForm">
                @csrf
                @method('PUT')

                <!-- Employee & Period -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee *</label>
                        <select name="employee_id" id="employee_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="updateSalary(this)">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-salary="{{ $employee->base_salary }}" {{ old('employee_id', $payslip->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} - {{ $employee->position }} (Rp {{ number_format($employee->base_salary, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="period_start" class="block text-sm font-medium text-gray-700">Period Start *</label>
                        <input type="date" name="period_start" id="period_start" value="{{ old('period_start', $payslip->period_start->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="period_end" class="block text-sm font-medium text-gray-700">Period End *</label>
                        <input type="date" name="period_end" id="period_end" value="{{ old('period_end', $payslip->period_end->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $payslip->payment_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    </div>
                </div>

                <!-- Salary & Allowances -->
                <div class="pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Earnings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700">Base Salary *</label>
                            <input type="number" name="base_salary" id="base_salary" value="{{ old('base_salary', $payslip->base_salary) }}" step="1000" min="0" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>

                        <div>
                            <label for="allowances" class="block text-sm font-medium text-gray-700">Allowances</label>
                            <input type="number" name="allowances" id="allowances" value="{{ old('allowances', $payslip->allowances) }}" step="1000" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>
                    </div>
                </div>

                <!-- Additional Items Section -->
                <div class="pt-6 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-sm font-semibold text-gray-900">Additional Items (Bonus, Incentive, etc.)</h4>
                        <button type="button" onclick="addPayslipItem()" class="px-3 py-1.5 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">
                            + Add Item
                        </button>
                    </div>

                    <div id="payslip-items-container" class="space-y-3">
                        <!-- Items will be loaded here -->
                    </div>
                </div>

                <!-- Deductions -->
                <div class="pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">Deductions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="deductions" class="block text-sm font-medium text-gray-700">Other Deductions</label>
                            <input type="number" name="deductions" id="deductions" value="{{ old('deductions', $payslip->deductions) }}" step="1000" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>

                        <div>
                            <label for="pph21" class="block text-sm font-medium text-gray-700">PPh 21</label>
                            <input type="number" name="pph21" id="pph21" value="{{ old('pph21', $payslip->pph21) }}" step="1000" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>

                        <div>
                            <label for="bpjs_kes" class="block text-sm font-medium text-gray-700">BPJS Kesehatan</label>
                            <input type="number" name="bpjs_kes" id="bpjs_kes" value="{{ old('bpjs_kes', $payslip->bpjs_kes) }}" step="1000" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>

                        <div>
                            <label for="bpjs_tk" class="block text-sm font-medium text-gray-700">BPJS Ketenagakerjaan</label>
                            <input type="number" name="bpjs_tk" id="bpjs_tk" value="{{ old('bpjs_tk', $payslip->bpjs_tk) }}" step="1000" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onchange="calculateNetSalary()">
                        </div>
                    </div>
                </div>

                <!-- Net Salary Summary -->
                <div class="pt-6 border-t border-gray-100 bg-teal-50 -mx-6 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">Net Salary (Take Home Pay):</span>
                        <span id="net-salary-display" class="text-2xl font-bold text-teal-700">Rp 0</span>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">{{ old('notes', $payslip->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('payslips.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">Update Payslip</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemCounter = 0;
        const existingItems = @json($payslip->items);

        function updateSalary(select) {
            const option = select.options[select.selectedIndex];
            const salary = option.dataset.salary || 0;
            document.getElementById('base_salary').value = salary;
            calculateNetSalary();
        }

        function addPayslipItem(itemData = null) {
            itemCounter++;
            const container = document.getElementById('payslip-items-container');
            
            const description = itemData?.description || '';
            const amount = itemData?.amount || 0;
            
            const itemHtml = `
                <div class="payslip-item flex gap-3 items-start p-3 bg-gray-50 rounded-lg border border-gray-200" data-item-id="${itemCounter}">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                        <input type="text" name="items[${itemCounter}][description]" value="${description}" required 
                            placeholder="e.g., Performance Bonus, Incentive, etc." 
                            class="block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div style="width: 180px;">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="items[${itemCounter}][amount]" value="${amount}" step="1000" min="0" required 
                            onchange="calculateNetSalary()"
                            class="item-amount block w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                    </div>

                    <div class="pt-6">
                        <button type="button" onclick="removePayslipItem(${itemCounter})" 
                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <input type="hidden" name="items[${itemCounter}][sort_order]" value="${itemCounter}">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        }

        function removePayslipItem(id) {
            const item = document.querySelector(`.payslip-item[data-item-id="${id}"]`);
            if (item) {
                item.remove();
                calculateNetSalary();
            }
        }

        function calculateNetSalary() {
            const baseSalary = parseFloat(document.getElementById('base_salary').value) || 0;
            const allowances = parseFloat(document.getElementById('allowances').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;
            const pph21 = parseFloat(document.getElementById('pph21').value) || 0;
            const bpjsKes = parseFloat(document.getElementById('bpjs_kes').value) || 0;
            const bpjsTk = parseFloat(document.getElementById('bpjs_tk').value) || 0;

            // Calculate additional items total
            let itemsTotal = 0;
            document.querySelectorAll('.item-amount').forEach(input => {
                itemsTotal += parseFloat(input.value) || 0;
            });

            const gross = baseSalary + allowances + itemsTotal;
            const totalDeductions = deductions + pph21 + bpjsKes + bpjsTk;
            const netSalary = gross - totalDeductions;

            const netSalaryDisplay = document.getElementById('net-salary-display');
            netSalaryDisplay.textContent = 'Rp ' + netSalary.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        // Load existing items
        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    addPayslipItem(item);
                });
            }
            calculateNetSalary();
        });
    </script>
    @endpush
</x-app-layout>
