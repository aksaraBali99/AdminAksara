<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report - {{ $periodLabel }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #1e40af;
            color: white;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-amber { color: #d97706; }
        .summary-box {
            display: inline-block;
            width: 23%;
            margin-right: 1%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: top;
        }
        .summary-box h3 {
            margin: 0;
            font-size: 10px;
            color: #666;
        }
        .summary-box p {
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Aksara Virtual Agency</h1>
        <p>Financial Report - {{ $periodLabel }}</p>
        <p style="font-size: 9px; color: #999;">Generated: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <!-- Summary -->
    <div class="section">
        <div class="summary-box">
            <h3>Total Unpaid</h3>
            <p class="text-amber">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
        </div>
        <div class="summary-box">
            <h3>Total Income</h3>
            <p class="text-green">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="summary-box">
            <h3>Total Expenses</h3>
            <p class="text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="summary-box">
            <h3>Net Profit</h3>
            <p class="{{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Unpaid Invoices -->
    <div class="section">
        <div class="section-title">Unpaid Invoices</div>
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unpaidInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client->company_name ?? '-' }}</td>
                    <td>{{ $invoice->due_date->format('d M Y') }}</td>
                    <td>{{ ucfirst($invoice->status) }}</td>
                    <td class="text-right">{{ $invoice->formatted_amount }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No unpaid invoices</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Total Unpaid:</th>
                    <th class="text-right text-amber">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Income by Client -->
    <div class="section">
        <div class="section-title">Income by Client</div>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Contact</th>
                    <th class="text-right">Total Income</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomeByClient as $client)
                <tr>
                    <td>{{ $client->company_name }}</td>
                    <td>{{ $client->contact_name }}</td>
                    <td class="text-right text-green">Rp {{ number_format($client->incomes_sum_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No income data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">Total Income:</th>
                    <th class="text-right text-green">Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Expenses by Category -->
    <div class="section">
        <div class="section-title">Expenses by Category</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseByCategory as $category)
                @php $percentage = $totalExpense > 0 ? ($category->expenses_sum_amount / $totalExpense) * 100 : 0; @endphp
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-right text-red">Rp {{ number_format($category->expenses_sum_amount, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($percentage, 1) }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center;">No expense data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right">Total Expenses:</th>
                    <th class="text-right text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                    <th class="text-right">100%</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        Aksara Virtual Agency - Financial Report
    </div>
</body>
</html>
