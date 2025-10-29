@extends('layouts.app')

@section('title', 'Order Confirmation - Agriculture Equipment')
@section('description', 'Your agriculture equipment order has been confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="text-center mb-5">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h2 class="text-success mb-3">Order Confirmed!</h2>
                <p class="lead text-muted">Thank you for your purchase. Your agriculture equipment order has been successfully placed.</p>
            </div>

            <!-- Order Details -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Order Details
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Number</h6>
                            <p class="h5 text-primary">{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Date</h6>
                            <p class="h6">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Information</h6>
                            <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                            <p class="mb-1">{{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                                <p class="mb-0">{{ $order->customer_phone }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Status</h6>
                            <span class="badge badge-warning badge-lg">{{ ucfirst($order->order_status) }}</span>
                            <br>
                            <small class="text-muted mt-2 d-block">Payment: {{ ucfirst($order->payment_status) }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                    </td>
                                    <td>{{ $item->product_sku }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Billing Address</h6>
                            <p class="mb-0">{{ $order->billing_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Shipping Address</h6>
                            <p class="mb-0">{{ $order->shipping_address }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>${{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-right">
                                <h5 class="text-success mb-0">
                                    Total: ${{ number_format($order->total_amount, 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-info-circle me-2"></i>What's Next?
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                <h6>Email Confirmation</h6>
                                <small class="text-muted">We'll send you an email confirmation shortly</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                                <h6>Processing</h6>
                                <small class="text-muted">Your order will be processed within 1-2 business days</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                                <h6>Delivery</h6>
                                <small class="text-muted">Expected delivery: 3-5 business days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary me-3">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
                <a href="{{ route('agriculture.home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.success-icon {
    animation: bounce 1s ease-in-out;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection
