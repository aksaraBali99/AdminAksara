<x-app-layout>
    <x-slot name="header">Payslip Details</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('payslips.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Payslips
            </a>
            <div class="flex gap-2">
                <a href="{{ route('payslips.pdf', $payslip) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('payslips.edit', $payslip) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Edit</a>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold text-teal-700">AKSARA VIRTUAL AGENCY</h2>
                        <p class="text-sm text-gray-500 mt-1">Payslip / Slip Gaji</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-900">{{ $payslip->payslip_number }}</p>
                        <p class="text-sm text-gray-500">{{ $payslip->period_start->format('d M') }} - {{ $payslip->period_end->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Employee Info -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Employee Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-medium text-gray-900">{{ $payslip->employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Employee ID</p>
                        <p class="font-medium text-gray-900">{{ $payslip->employee->employee_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Position</p>
                        <p class="font-medium text-gray-900">{{ $payslip->employee->position }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="font-medium text-gray-900">{{ $payslip->employee->type_label }}</p>
                    </div>
                </div>
            </div>

            <!-- Earnings -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Earnings</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Salary</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Allowances</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($payslip->allowances, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="font-semibold text-gray-900">Gross Salary</span>
                        <span class="font-semibold text-green-600">Rp {{ number_format($payslip->base_salary + $payslip->allowances, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Deductions</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">PPh 21</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($payslip->pph21, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">BPJS Kesehatan</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($payslip->bpjs_kes, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">BPJS Ketenagakerjaan</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($payslip->bpjs_tk, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Other Deductions</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($payslip->deductions, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100">
                        <span class="font-semibold text-gray-900">Total Deductions</span>
                        <span class="font-semibold text-red-600">-Rp {{ number_format($payslip->pph21 + $payslip->bpjs_kes + $payslip->bpjs_tk + $payslip->deductions, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Net Salary -->
            <div class="p-6 bg-teal-50">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">Net Salary (Take Home Pay)</span>
                    <span class="text-2xl font-bold text-teal-600">{{ $payslip->formatted_net_salary }}</span>
                </div>
            </div>

            @if($payslip->notes)
            <div class="p-6 border-t border-gray-100">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Notes</h3>
                <p class="text-sm text-gray-600">{{ $payslip->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
