<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip {{ $payslip->payslip_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; line-height: 1.5; }
        .container { padding: 30px 40px; }
        
        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0f766e;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
        .company-header {
            font-size: 13px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 3px;
        }
        .company-detail {
            font-size: 9px;
            color: #555;
            line-height: 1.6;
        }
        
        /* Payslip Info */
        .payslip-info {
            margin: 20px 0;
        }
        .payslip-number {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payslip-period {
            font-size: 9px;
            color: #666;
        }
        
        /* Employee Info */
        .employee-section {
            margin: 20px 0;
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
        }
        .employee-label {
            font-size: 9px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
        }
        .employee-value {
            font-size: 10px;
            color: #333;
            font-weight: bold;
        }
        .employee-grid {
            display: table;
            width: 100%;
            margin-top: 5px;
        }
        .employee-row {
            display: table-row;
        }
        .employee-cell {
            display: table-cell;
            padding: 3px 5px;
        }
        
        /* Salary Table */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #0f766e;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .salary-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .salary-table tr td {
            padding: 5px 0;
            font-size: 9px;
        }
        .salary-table .label {
            color: #666;
            width: 60%;
        }
        .salary-table .amount {
            text-align: right;
            font-weight: bold;
            color: #333;
        }
        .salary-table .amount.positive {
            color: #16a34a;
        }
        .salary-table .amount.negative {
            color: #dc2626;
        }
        .salary-table .subtotal {
            border-top: 1px solid #ddd;
            padding-top: 8px !important;
        }
        
        /* Additional Items */
        .items-section {
            margin: 15px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .items-table thead th {
            border-bottom: 1px solid #333;
            padding: 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            background-color: #f9fafb;
        }
        .items-table thead th.right {
            text-align: right;
        }
        .items-table tbody td {
            padding: 6px 5px;
            font-size: 9px;
            border-bottom: 0.5px solid #e5e7eb;
        }
        .items-table tbody td.right {
            text-align: right;
            font-weight: bold;
        }
        
        /* Net Salary */
        .net-salary {
            background: #f0fdfa;
            padding: 12px;
            margin-top: 15px;
            border-radius: 5px;
            border: 1px solid #0f766e;
        }
        .net-salary table {
            width: 100%;
        }
        .net-salary .label {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }
        .net-salary .amount {
            font-size: 14px;
            font-weight: bold;
            color: #0f766e;
            text-align: right;
        }
        
        /* Notes */
        .notes {
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .notes-title {
            font-size: 9px;
            color: #666;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .notes-content {
            font-size: 9px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- LOGO & HEADER -->
        <div class="header-section">
            @if(file_exists(public_path('images/aksara.png')))
            <img src="{{ public_path('images/aksara.png') }}" alt="Aksara Logo" class="logo">
            @endif
            
            <div class="company-header">Aksara Virtual Agency</div>
            <div class="company-detail">PT Solusi Mitra Virtual</div>
            <div class="company-detail">Jl Tukad Citarum No:14 Panjer, Denpasar Bali 80225</div>
            <div class="company-detail">info@aksaravirtualagency.com | www.aksaravirtualagency.com</div>
        </div>
        
        <!-- Payslip Info -->
        <div class="payslip-info">
            <div class="payslip-number">Payslip Number: {{ $payslip->payslip_number }}</div>
            <div class="payslip-period">Period: {{ $payslip->period_start->format('d M') }} - {{ $payslip->period_end->format('d M Y') }}</div>
            @if($payslip->payment_date)
            <div class="payslip-period">Payment Date: {{ $payslip->payment_date->format('d F Y') }}</div>
            @endif
        </div>
        
        <!-- Employee Info -->
        <div class="employee-section">
            <div class="employee-label">Employee Information</div>
            <div class="employee-grid">
                <div class="employee-row">
                    <div class="employee-cell" style="width: 25%;">
                        <span style="color: #666; font-size: 8px;">Name:</span><br>
                        <span class="employee-value">{{ $payslip->employee->name }}</span>
                    </div>
                    <div class="employee-cell" style="width: 25%;">
                        <span style="color: #666; font-size: 8px;">Employee ID:</span><br>
                        <span class="employee-value">{{ $payslip->employee->employee_id }}</span>
                    </div>
                    <div class="employee-cell" style="width: 25%;">
                        <span style="color: #666; font-size: 8px;">Position:</span><br>
                        <span class="employee-value">{{ $payslip->employee->position }}</span>
                    </div>
                    <div class="employee-cell" style="width: 25%;">
                        <span style="color: #666; font-size: 8px;">Type:</span><br>
                        <span class="employee-value">{{ $payslip->employee->type_label }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- EARNINGS -->
        <div class="section-title">Earnings</div>
        <table class="salary-table">
            <tr>
                <td class="label">Base Salary</td>
                <td class="amount positive">Rp {{ number_format($payslip->base_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Allowances</td>
                <td class="amount positive">Rp {{ number_format($payslip->allowances, 0, ',', '.') }}</td>
            </tr>
            @php
                $earningsSubtotal = $payslip->base_salary + $payslip->allowances;
            @endphp
        </table>
        
        <!-- ADDITIONAL ITEMS (if any) -->
        @if($payslip->items->isNotEmpty())
        <div class="items-section">
            <div class="section-title">Additional Items</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 70%;">Description</th>
                        <th class="right" style="width: 20%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payslip->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $earningsSubtotal += $item->amount;
                    @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Gross Salary -->
        <table class="salary-table">
            <tr class="subtotal">
                <td class="label" style="font-weight: bold;">Gross Salary</td>
                <td class="amount positive">Rp {{ number_format($earningsSubtotal, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <!-- DEDUCTIONS -->
        <div class="section-title">Deductions</div>
        <table class="salary-table">
            <tr>
                <td class="label">PPh 21</td>
                <td class="amount negative">-Rp {{ number_format($payslip->pph21, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">BPJS Kesehatan</td>
                <td class="amount negative">-Rp {{ number_format($payslip->bpjs_kes, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">BPJS Ketenagakerjaan</td>
                <td class="amount negative">-Rp {{ number_format($payslip->bpjs_tk, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Other Deductions</td>
                <td class="amount negative">-Rp {{ number_format($payslip->deductions, 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal">
                <td class="label" style="font-weight: bold;">Total Deductions</td>
                <td class="amount negative">-Rp {{ number_format($payslip->pph21 + $payslip->bpjs_kes + $payslip->bpjs_tk + $payslip->deductions, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <!-- NET SALARY -->
        <div class="net-salary">
            <table>
                <tr>
                    <td class="label">Net Salary (Take Home Pay)</td>
                    <td class="amount">{{ $payslip->formatted_net_salary }}</td>
                </tr>
            </table>
        </div>
        
        <!-- NOTES -->
        @if($payslip->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <div class="notes-content">{{ $payslip->notes }}</div>
        </div>
        @endif
    </div>
</body>
</html>
