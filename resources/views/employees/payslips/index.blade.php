<x-app-layout>
    <x-slot name="header">Payslips</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Payslip Management</h2>
            <p class="mt-1 text-sm text-gray-500">Generate and manage employee payslips</p>
        </div>
        <a href="{{ route('payslips.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-teal-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create Payslip
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-6">
        <div class="p-4">
            <form action="{{ route('payslips.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payslip..." class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                <select name="employee_id" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                    @endforeach
                </select>
                <select name="period_month" class="rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('period_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm">Filter</button>
                    <a href="{{ route('payslips.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payslip #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payslips as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payslip->payslip_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payslip->employee->name }}</div>
                            <div class="text-xs text-gray-500">{{ $payslip->employee->position }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payslip->period_start->format('d M') }} - {{ $payslip->period_end->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                            -Rp {{ number_format($payslip->deductions + $payslip->pph21 + $payslip->bpjs_kes + $payslip->bpjs_tk, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-teal-600">
                            {{ $payslip->formatted_net_salary }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('payslips.show', $payslip) }}" class="text-gray-400 hover:text-teal-600" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('payslips.pdf', $payslip) }}" class="text-gray-400 hover:text-red-600" title="Download PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                <a href="{{ route('payslips.edit', $payslip) }}" class="text-gray-400 hover:text-blue-600" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('payslips.destroy', $payslip) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payslip?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No payslips found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payslips->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $payslips->links() }}</div>
        @endif
    </div>
</x-app-layout>
