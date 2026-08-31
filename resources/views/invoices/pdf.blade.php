<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            padding: 30px 40px;
        }
        
        /* Logo Section */
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
        
        /* Invoice Info */
        .invoice-info {
            margin: 20px 0;
        }
        .invoice-number {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-date {
            font-size: 9px;
            color: #666;
        }
        
        /* Bill To */
        .bill-to {
            margin: 20px 0;
        }
        .bill-to-label {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .client-info {
            font-size: 9px;
            color: #555;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table thead th {
            border-top: 1.5px solid #333;
            border-bottom: 1.5px solid #333;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            background-color: #f9fafb;
        }
        .items-table thead th.center {
            text-align: center;
        }
        .items-table thead th.right {
            text-align: right;
        }
        .items-table tbody td {
            padding: 10px 5px;
            font-size: 9px;
            border-bottom: 0.5px solid #e5e7eb;
        }
        .items-table tbody td.center {
            text-align: center;
        }
        .items-table tbody td.right {
            text-align: right;
        }
        .item-type {
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 2px;
        }
        .item-desc {
            color: #666;
        }
        
        /* Total Section */
        .total-section {
            margin-top: 15px;
            border-top: 1.5px solid #333;
            padding-top: 10px;
        }
        .total-row {
            width: 100%;
        }
        .total-row td {
            padding: 5px;
            font-size: 10px;
        }
        .total-label {
            text-align: right;
            padding-right: 15px;
            width: 70%;
        }
        .total-value {
            text-align: right;
            font-weight: bold;
            width: 30%;
        }
        .grand-total {
            font-size: 12px;
            font-weight: bold;
            color: #0f766e;
            border-top: 1px solid #333;
            padding-top: 8px !important;
        }
        
        /* Footer - Payment Info */
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
           border-top: 1px solid #ddd;
        }
        .footer-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .payment-info {
            font-size: 9px;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        .payment-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .thank-you {
            font-size: 9px;
            font-style: italic;
            margin: 15px 0;
            color: #666;
        }
        .accounts-signature {
            font-size: 9px;
            font-weight: bold;
            margin-top: 10px;
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
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-number">Invoice Number: {{ $invoice->invoice_number }}</div>
            <div class="invoice-date">Invoice Date: {{ $invoice->invoice_date->format('d F Y') }}</div>
            <div class="invoice-date">Due Date: {{ $invoice->due_date->format('d F Y') }}</div>
        </div>
        
        <!-- Bill To -->
        <div class="bill-to">
            <div class="bill-to-label">Bill To:</div>
            <div class="client-info">{{ $invoice->client->company_name ?? '' }}</div>
            @if($invoice->client->contact_name)
            <div class="client-info">{{ $invoice->client->contact_name }}</div>
            @endif
            @if($invoice->client->address)
            <div class="client-info">{{ $invoice->client->address }}</div>
            @endif
        </div>
        
        <!-- BODY - Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Type of Fees</th>
                    <th style="width: 35%;">Description</th>
                    <th class="center" style="width: 10%;">Item/Hours</th>
                    <th class="right" style="width: 10%;">Rate</th>
                    <th class="right" style="width: 10%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $no = 1;
                    $typeLabels = [
                        'virtual_assistant_fee' => 'Virtual Assistant Fee',
                        'monthly_benefit' => 'Monthly Benefit',
                        'annual_benefit' => 'Annual Benefit'
                    ];
                @endphp
                @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>
                        <div class="item-type">{{ $typeLabels[$item->type_of_fees] ?? $item->type_of_fees }}</div>
                    </td>
                    <td>
                        <div class="item-desc">{{ $item->description }}</div>
                    </td>
                    <td class="center">{{ number_format($item->item_hours, 2) }}</td>
                    <td class="right">
                        @php
                            $symbol = $invoice->currency === 'IDR' ? 'Rp' : ($invoice->currency === 'USD' ? '$' : 'A$');
                        @endphp
                        @if($invoice->currency === 'IDR')
                            {{ $symbol }} {{ number_format($item->rate, 0, ',', '.') }}
                        @else
                            {{ $symbol }} {{ number_format($item->rate, 2, '.', ',') }}
                        @endif
                    </td>
                    <td class="right">
                        @if($invoice->currency === 'IDR')
                            {{ $symbol }} {{ number_format($item->total, 0, ',', '.') }}
                        @else
                            {{ $symbol }} {{ number_format($item->total, 2, '.', ',') }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No items</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <table class="total-row">
                <tr class="grand-total">
                    <td class="total-label">TOTAL:</td>
                    <td class="total-value">
                        @if($invoice->items->isNotEmpty())
                            {{ $invoice->formatted_amount }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- FOOTER - Payment Info -->
        <div class="footer-section">
            <div class="footer-title">Payment Info</div>
            <div class="payment-info">
                <div><strong>WISE</strong></div>
                <div><span class="payment-label">Bank Name</span>: PT Bank Mandiri (Persero) Tbk.</div>
                <div><span class="payment-label">SWIFT / BIC code</span>: BMRIIDJAXXX</div>
                <div><span class="payment-label">Account Number</span>: 145-00-7566667-3</div>
                <div><span class="payment-label">Name</span>: PT Solusi Mitra Virtual</div>
            </div>
            
            <div class="thank-you">
                Thank you for your business and your support for local Balinese virtual assistants
            </div>
            
            <div class="accounts-signature">
                Accounts<br>
                Aksara Virtual Agency
            </div>
        </div>
    </div>
</body>
</html>
