@extends('layouts.app')

@section('title', 'Order Details - Dealer Dashboard')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details</h2>
        <div>
            <a href="{{ route('dealer.orders') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
            <a href="{{ route('dealer.invoice.download', $order->order_number) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Download Invoice
            </a>
        </div>
    </div>

    <!-- Order Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    <small>Placed on {{ $order->created_at->format('F d, Y h:i A') }}</small>
                </div>
                <div class="col-md-6 text-end">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $color = $statusColors[$order->order_status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($order->order_status) }}</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Order Items -->
            <h6 class="mb-3">Order Items (Dealer Pricing)</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Dealer Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name ?? 'Product N/A' }}</strong>
                                </td>
                                <td>{{ $item->product->sku ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tax (GST):</strong></td>
                                <td class="text-end">₹{{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if($order->shipping_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                <td class="text-end">₹{{ number_format($order->shipping_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="table-success">
                            <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Address & Payment Info -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h6>
                </div>
                <div class="card-body">
                    @if($order->shipping_address)
                        <p class="mb-1"><strong>{{ $order->shipping_address['name'] ?? Auth::user()->business_name }}</strong></p>
                        <p class="mb-1">{{ $order->shipping_address['address'] ?? '' }}</p>
                        <p class="mb-1">{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['pincode'] ?? '' }}</p>
                        <p class="mb-0">Phone: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}</p>
                    @else
                        <p class="text-muted">No shipping address provided</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                    <p class="mb-2">
                        <strong>Payment Status:</strong> 
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                    <p class="mb-2"><strong>GST Number:</strong> {{ Auth::user()->gst_number }}</p>
                    @if($order->notes)
                        <p class="mb-0"><strong>Notes:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






