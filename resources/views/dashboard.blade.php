<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            Dashboard Overview
        </h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Overview</li>
            </ol>
        </nav>
    </div>

    @if(auth()->user()->isHr())
        <!-- HR Dashboard Layout -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-2 2xl:gap-7.5 mb-6">
            <!-- Total Salary Expense -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rose-100">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalSalaryExpense, 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Total Salary Expense {{ $currentYear }}</span>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-gray-800">
                        {{ $activeEmployees }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Active Employees</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <!-- Recent Payslips -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">Recent Payslips</h4>
                    </div>
                    <a href="{{ route('payslips.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payslip</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentPayslips as $payslip)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $payslip->payslip_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $payslip->employee->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $payslip->formatted_net_salary }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $payslip->payment_date ? $payslip->payment_date->format('d M Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No payslips yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- Original Dashboard Layout (Owner/Finance) -->
        <!-- KPI Cards Row 1 -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5 mb-6">
            <!-- Unpaid Invoices -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-100">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalUnpaidInvoices, 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Unpaid Invoices</span>
                </div>
                <span class="mt-2 flex items-center gap-1 text-xs font-medium text-amber-600">
                    {{ $unpaidCount }} pending
                </span>
            </div>

            <!-- Paid Invoices -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalPaidInvoices, 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Paid Invoices</span>
                </div>
                <span class="mt-2 flex items-center gap-1 text-xs font-medium text-green-600">
                    {{ $paidCount }} completed
                </span>
            </div>

            <!-- Total Income -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold text-gray-800">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Total Income {{ $currentYear }}</span>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center rounded-full {{ $netProfit >= 0 ? 'bg-emerald-100' : 'bg-red-100' }}">
                    <svg class="w-6 h-6 {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="mt-4">
                    <h4 class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </h4>
                    <span class="text-sm font-medium text-gray-500">Net Profit {{ $currentYear }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row 2 -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 mb-6">
            <!-- Total Clients -->
            <div class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Clients</p>
                        <h4 class="mt-1 text-3xl font-bold text-white">{{ $totalClients }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Active Employees</p>
                        <h4 class="mt-1 text-3xl font-bold text-white">{{ $activeEmployees }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="rounded-lg bg-gradient-to-r from-rose-500 to-rose-600 p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-rose-100 text-sm font-medium">Total Expenses {{ $currentYear }}</p>
                        <h4 class="mt-1 text-2xl font-bold text-white">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 mb-6">
            <!-- Income vs Expense Chart -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800">Income vs Expenses</h4>
                    <p class="text-sm text-gray-500">Monthly comparison for {{ $currentYear }}</p>
                </div>
                <div class="p-6">
                    <div style="height: 300px;">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Invoice Status Chart -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800">Invoice Status</h4>
                    <p class="text-sm text-gray-500">Distribution by status</p>
                </div>
                <div class="p-6">
                    <div style="height: 300px;">
                        <canvas id="invoiceStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
            <!-- Recent Invoices -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">Recent Invoices</h4>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentInvoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->client->company_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $invoice->formatted_amount }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-600',
                                            'sent' => 'bg-blue-100 text-blue-600',
                                            'paid' => 'bg-green-100 text-green-600',
                                            'overdue' => 'bg-red-100 text-red-600',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $statusColors[$invoice->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No invoices yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">Top Clients</h4>
                    </div>
                    <a href="{{ route('clients.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">
                        View All
                    </a>
                </div>
                <div class="p-6">
                    <div class="flex flex-col gap-4">
                        @forelse($topClients as $client)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">
                                    {{ strtoupper(substr($client->company_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h5 class="text-sm font-medium text-gray-800">{{ $client->company_name }}</h5>
                                    <p class="text-xs text-gray-500">{{ $client->contact_name }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">
                                Rp {{ number_format($client->total_revenue_idr, 0, ',', '.') }}
                            </span>
                        </div>
                        @empty
                        <p class="text-center text-sm text-gray-500 py-4">No clients yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            // Income vs Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            new Chart(incomeExpenseCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Income',
                            data: @json($incomeData),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 0,
                            borderRadius: 4,
                        },
                        {
                            label: 'Expenses',
                            data: @json($expenseData),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 0,
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(0) + 'M';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(0) + 'K';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });

            // Invoice Status Chart
            const invoiceStatusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
            new Chart(invoiceStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Sent', 'Paid', 'Overdue'],
                    datasets: [{
                        data: [
                            {{ $invoiceStatusData['draft'] }},
                            {{ $invoiceStatusData['sent'] }},
                            {{ $invoiceStatusData['paid'] }},
                            {{ $invoiceStatusData['overdue'] }}
                        ],
                        backgroundColor: [
                            'rgba(107, 114, 128, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    cutout: '60%'
                }
            });
        </script>
        @endpush
    @endif
</x-app-layout>
