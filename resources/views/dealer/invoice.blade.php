<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 20px;
            color: #333;
            margin-top: 10px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #4CAF50;
        }
        .info-box p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            margin: 5px 0;
        }
        .total-label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        .total-amount {
            display: inline-block;
            width: 120px;
            text-align: right;
        }
        .grand-total {
            font-size: 16px;
            color: #4CAF50;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid #4CAF50;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d1ecf1; color: #0c5460; }
        .status-shipped { background-color: #cce5ff; color: #004085; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">NEXUS AGRICULTURE</div>
            <div style="font-size: 11px; color: #666;">
                Agricultural Equipment & Supplies<br>
                info@nexusagriculture.com | +1 234 567 8900
            </div>
            <div class="invoice-title">TAX INVOICE</div>
        </div>

        <!-- Invoice Info -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Bill To (Dealer)</h3>
                    <p><strong>{{ $order->user->business_name }}</strong></p>
                    <p>{{ $order->user->business_address }}</p>
                    <p>GST: {{ $order->user->gst_number }}</p>
                    <p>Contact: {{ $order->customer_name }}</p>
                    <p>Phone: {{ $order->customer_phone }}</p>
                    <p>Email: {{ $order->customer_email }}</p>
                </div>
            </div>
            <div class="info-right">
                <div class="info-box">
                    <h3>Invoice Details</h3>
                    <p><strong>Invoice No:</strong> {{ $order->order_number }}</p>
                    <p><strong>Invoice Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                    <p><strong>Status:</strong> 
                        @php
                            $statusClass = 'status-' . $order->order_status;
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ strtoupper($order->order_status) }}</span>
                    </p>
                    <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                    <p><strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}</p>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        @if($order->shipping_address)
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Shipping Address</h3>
                    <p><strong>{{ $order->shipping_address['name'] ?? $order->user->business_name }}</strong></p>
                    <p>{{ $order->shipping_address['address'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['pincode'] ?? '' }}</p>
                    <p>Phone: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Order Items -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Product Description</th>
                    <th style="width: 15%;">SKU</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Dealer Price</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name ?? 'Product N/A' }}</strong>
                        @if($item->product && $item->product->short_description)
                            <br><small style="color: #666;">{{ Str::limit($item->product->short_description, 50) }}</small>
                        @endif
                    </td>
                    <td>{{ $item->product->sku ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-amount">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->tax_amount > 0)
            <div class="total-row">
                <span class="total-label">Tax (GST @ 18%):</span>
                <span class="total-amount">₹{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            @if($order->shipping_amount > 0)
            <div class="total-row">
                <span class="total-label">Shipping Charges:</span>
                <span class="total-amount">₹{{ number_format($order->shipping_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span class="total-label">Grand Total:</span>
                <span class="total-amount">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Amount in Words -->
        <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-left: 3px solid #4CAF50;">
            <strong>Amount in Words:</strong> <em>Rupees {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($order->total_amount)) }} Only</em>
        </div>

        <!-- Terms & Conditions -->
        <div style="margin-top: 30px;">
            <h3 style="font-size: 14px; color: #4CAF50;">Terms & Conditions</h3>
            <ul style="font-size: 11px; color: #666; line-height: 1.8;">
                <li>This is a dealer invoice with exclusive wholesale pricing</li>
                <li>Payment is due as per agreed terms</li>
                <li>Goods once sold will not be taken back or exchanged</li>
                <li>Subject to local jurisdiction only</li>
                <li>All disputes are subject to arbitration</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>For any queries, please contact us at info@nexusagriculture.com or call +1 234 567 8900</p>
            <p style="margin-top: 10px;">This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>
</body>
</html>





