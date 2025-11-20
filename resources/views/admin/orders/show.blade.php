@extends('admin.layout')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        @if($order->order_status === 'inquiry')
                            <i class="fas fa-question-circle me-2"></i>Inquiry #{{ $order->order_number }}
                        @else
                            Order #{{ $order->order_number }}
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Order Number:</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date:</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'inquiry' => 'info',
                                                'pending' => 'warning',
                                                'processing' => 'primary',
                                                'shipped' => 'info',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusColor = $statusColors[$order->order_status ?? 'pending'] ?? 'secondary';
                                            $orderStatus = $order->order_status ?? 'pending';
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            @if($orderStatus === 'inquiry')
                                                <i class="fas fa-question-circle me-1"></i>Inquiry
                                            @else
                                                {{ ucfirst($orderStatus) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>
                                        @php
                                            $paymentColors = [
                                                'not_required' => 'secondary',
                                                'pending' => 'warning',
                                                'paid' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'info'
                                            ];
                                            $paymentColor = $paymentColors[$order->payment_status ?? 'pending'] ?? 'secondary';
                                            $paymentStatus = $order->payment_status ?? 'pending';
                                        @endphp
                                        <span class="badge bg-{{ $paymentColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>
                                        @if($order->payment_method === 'inquiry')
                                            <span class="text-muted">N/A - Inquiry Only</span>
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $order->customer_email }}</td>
                                </tr>
                                @if($order->customer_phone)
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $order->customer_phone }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Order Items -->
                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Original Price</th>
                                    <th>Discount</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                    $originalPrice = $item->original_price ?? $item->price;
                                    $discountAmount = $item->discount_amount ?? 0;
                                    $hasDiscount = $discountAmount > 0;
                                    $offerDetails = $item->offer_details ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($offerDetails)
                                            <br><small class="text-success">
                                                <i class="fas fa-tag me-1"></i>{{ $offerDetails['title'] ?? 'Offer Applied' }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $item->product_sku }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        @if($hasDiscount)
                                            <span class="text-muted text-decoration-line-through">{{ $currencySymbol ?? '₹' }}{{ number_format($originalPrice, 2) }}</span>
                                        @else
                                            {{ $currencySymbol ?? '₹' }}{{ number_format($originalPrice, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasDiscount)
                                            <span class="text-danger">
                                                -{{ $currencySymbol ?? '₹' }}{{ number_format($discountAmount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $currencySymbol ?? '₹' }}{{ number_format($item->price, 2) }}</strong>
                                    </td>
                                    <td><strong>{{ $currencySymbol ?? '₹' }}{{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <!-- Order Summary -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Addresses</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Billing Address:</strong>
                                    <p class="text-muted">{{ is_array($order->billing_address) ? ($order->billing_address['address'] ?? implode(', ', $order->billing_address)) : $order->billing_address }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Shipping Address:</strong>
                                    <p class="text-muted">{{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? implode(', ', $order->shipping_address)) : $order->shipping_address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Summary</h5>
                            <table class="table table-sm">
                                @php
                                    $totalDiscount = $order->items->sum('discount_amount') ?? 0;
                                    $originalSubtotal = $order->items->sum(function($item) {
                                        $originalPrice = $item->original_price ?? $item->price;
                                        return $originalPrice * $item->quantity;
                                    });
                                @endphp
                                @if($totalDiscount > 0)
                                <tr>
                                    <td>Subtotal (Original):</td>
                                    <td class="text-right">
                                        <span class="text-muted text-decoration-line-through">{{ $currencySymbol ?? '₹' }}{{ number_format($originalSubtotal, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Discount:</td>
                                    <td class="text-right text-danger">
                                        <strong>-{{ $currencySymbol ?? '₹' }}{{ number_format($totalDiscount, 2) }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-right">{{ $currencySymbol ?? '₹' }}{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax:</td>
                                    <td class="text-right">{{ $currencySymbol ?? '₹' }}{{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-right">{{ $currencySymbol ?? '₹' }}{{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-right"><strong>{{ $currencySymbol ?? '₹' }}{{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($order->order_status === 'inquiry')
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>This is a customer inquiry.</strong> Please contact the customer to confirm the order details before processing.
                        <br><small class="mt-2 d-block">Customer Phone: <strong>{{ $order->customer_phone ?? 'Not provided' }}</strong> | Email: <strong>{{ $order->customer_email }}</strong></small>
                    </div>
                    @endif

                    @if($order->notes)
                    <hr>
                    <h5>Order Notes</h5>
                    <p class="text-muted">{{ $order->notes }}</p>
                    @endif

                    <!-- Status Update Form -->
                    <hr>
                    <h5>Update Order Status</h5>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="row">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-4">
                            <label for="order_status">Order Status:</label>
                            <select name="order_status" id="order_status" class="form-control">
                                <option value="inquiry" {{ $order->order_status === 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payment_status">Payment Status:</label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="not_required" {{ $order->payment_status === 'not_required' ? 'selected' : '' }}>Not Required</option>
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
