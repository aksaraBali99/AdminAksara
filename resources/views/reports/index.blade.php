<x-app-layout>
    <x-slot name="header">Reports</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Financial Reports</h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Reports</li>
            </ol>
        </nav>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-2 block text-xs font-medium text-gray-600">Year</label>
                <select name="year" class="rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" style="width: 100px">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-xs font-medium text-gray-600">Month</label>
                <select name="month" class="rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" style="width: 200px">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600">
                    Filter
                </button>
                <a href="{{ route('reports.pdf', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </form>
        <!-- Exchange Rate Info -->
        <div class="mt-3 text-xs text-gray-400">
            Kurs konversi: 1 USD = Rp {{ number_format($exchangeRates['USD'], 0, ',', '.') }} | 1 AUD = Rp {{ number_format($exchangeRates['AUD'], 0, ',', '.') }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-6">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Unpaid Invoices</p>
            <h4 class="mt-2 text-xl font-bold text-amber-600">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h4>
            <p class="text-xs text-gray-400">{{ $unpaidInvoices->count() }} invoices (converted to IDR)</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Income</p>
            <h4 class="mt-2 text-xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Expenses</p>
            <h4 class="mt-2 text-xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Net Profit</p>
            @php $netProfit = $totalIncome - $totalExpense; @endphp
            <h4 class="mt-2 text-xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Unpaid Invoices -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800">Unpaid Invoices</h4>
                <p class="text-sm text-gray-500">Invoices pending payment</p>
            </div>
            <div class="max-h-80 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Due Date</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($unpaidInvoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->client->company_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm {{ $invoice->due_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                {{ $invoice->due_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 text-right">{{ $invoice->formatted_amount }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No unpaid invoices</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Income by Client -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800">Income by Client</h4>
                <p class="text-sm text-gray-500">Total income per client (converted to IDR)</p>
            </div>
            <div class="p-4 max-h-80 overflow-y-auto">
                @forelse($incomeByClient as $client)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-semibold text-sm">
                            {{ strtoupper(substr($client->company_name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $client->company_name }}</p>
                            <p class="text-xs text-gray-400">{{ $client->contact_name }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-green-600">
                        Rp {{ number_format($client->total_income_idr, 0, ',', '.') }}
                    </span>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500 py-8">No income data</p>
                @endforelse
            </div>
        </div>

        <!-- Expenses by Category -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-gray-200 px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800">Expenses by Category</h4>
                <p class="text-sm text-gray-500">Breakdown of expenses</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse($expenseByCategory as $category)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-600">{{ $category->name }}</p>
                        <p class="mt-1 text-lg font-bold text-red-600">Rp {{ number_format($category->expenses_sum_amount, 0, ',', '.') }}</p>
                        @php
                            $percentage = $totalExpense > 0 ? ($category->expenses_sum_amount / $totalExpense) * 100 : 0;
                        @endphp
                        <div class="mt-2 h-2 w-full rounded-full bg-gray-200">
                            <div class="h-2 rounded-full bg-red-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ number_format($percentage, 1) }}% of total</p>
                    </div>
                    @empty
                    <p class="col-span-4 text-center text-sm text-gray-500">No expense data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-gray-200 px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800">Monthly Summary {{ $year }}</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Month</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Income</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Expenses</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthlyData as $m => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</td>
                            <td class="px-4 py-3 text-sm text-green-600 text-right">Rp {{ number_format($data['income'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-red-600 text-right">Rp {{ number_format($data['expense'], 0, ',', '.') }}</td>
                            @php $net = $data['income'] - $data['expense']; @endphp
                            <td class="px-4 py-3 text-sm font-medium text-right {{ $net >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($net, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-800">Total</td>
                            <td class="px-4 py-3 text-sm text-green-600 text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-red-600 text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm text-right {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                Rp {{ number_format($netProfit, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
