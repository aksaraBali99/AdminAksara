<x-app-layout>
    <x-slot name="header">Finance Overview</x-slot>

    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Finance Overview</h2>
        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-500">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600">Finance</li>
            </ol>
        </nav>
    </div>

    <!-- Year Filter -->
    <div class="mb-6 flex items-center gap-4">
        <form action="{{ route('finance.index') }}" method="GET" class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-600">Year:</label>
            <select name="year" onchange="this.form.submit()" style="width: 200px"
                    class="rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="month" onchange="this.form.submit()" style="width: 200px"
                    class="rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 mb-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Income</p>
                    <h4 class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                    <h4 class="mt-2 text-2xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Net Profit</p>
                    <h4 class="mt-2 text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full {{ $netProfit >= 0 ? 'bg-emerald-100' : 'bg-red-100' }}">
                    <svg class="h-6 w-6 {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h4 class="text-lg font-semibold text-gray-800">Monthly Income vs Expenses ({{ $year }})</h4>
        </div>
        <div class="p-6">
            <div style="height: 350px;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
        <!-- Recent Income -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h4 class="text-lg font-semibold text-gray-800">Recent Income</h4>
                <a href="{{ route('incomes.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">View All</a>
            </div>
            <div class="p-4">
                @forelse($recentIncomes as $income)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($income->description, 30) }}</p>
                        <p class="text-xs text-gray-400">{{ $income->income_date->format('d M Y') }} • {{ $income->currency ?? 'IDR' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-green-600">+Rp {{ number_format($income->amount_idr, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500 py-4">No income records</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h4 class="text-lg font-semibold text-gray-800">Recent Expenses</h4>
                <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-blue-500 hover:text-blue-600">View All</a>
            </div>
            <div class="p-4">
                @forelse($recentExpenses as $expense)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ Str::limit($expense->description, 30) }}</p>
                        <p class="text-xs text-gray-400">{{ $expense->expense_date->format('d M Y') }} • {{ $expense->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <span class="text-sm font-semibold text-red-600">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500 py-4">No expense records</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ctx = document.getElementById('financeChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Income',
                        data: @json($incomeChartData),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: @json($expenseChartData),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(0) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
