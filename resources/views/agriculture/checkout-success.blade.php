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
                <h2 class="text-success mb-3">Inquiry Received!</h2>
                <p class="lead text-muted mb-4">
                    Thank you for your interest! We have received your inquiry and our team will contact you shortly to confirm your order details.
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-phone me-2"></i>
                    <strong>What happens next?</strong> We'll reach out to you via phone or email within 24 hours to discuss your requirements and finalize the order.
                </div>
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
                            <h6 class="text-muted">Inquiry Status</h6>
                            <span class="badge badge-info badge-lg">Inquiry Received</span>
                            <br>
                            <small class="text-muted mt-2 d-block">We'll contact you soon to confirm</small>
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
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Billing Address</h6>
                            <p class="mb-0">{{ is_array($order->billing_address) ? ($order->billing_address['address'] ?? implode(', ', $order->billing_address)) : $order->billing_address }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Shipping Address</h6>
                            <p class="mb-0">{{ is_array($order->shipping_address) ? ($order->shipping_address['address'] ?? implode(', ', $order->shipping_address)) : $order->shipping_address }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-8">
                            @php
                                $totalDiscount = $order->items->sum('discount_amount') ?? 0;
                                $originalSubtotal = $order->items->sum(function($item) {
                                    $originalPrice = $item->original_price ?? $item->price;
                                    return $originalPrice * $item->quantity;
                                });
                            @endphp
                            @if($totalDiscount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (Original):</span>
                                <span class="text-muted text-decoration-line-through">{{ $currencySymbol ?? '₹' }}{{ number_format($originalSubtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-danger">Discount:</span>
                                <span class="text-danger"><strong>-{{ $currencySymbol ?? '₹' }}{{ number_format($totalDiscount, 2) }}</strong></span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>{{ $currencySymbol ?? '₹' }}{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>{{ $currencySymbol ?? '₹' }}{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>{{ $currencySymbol ?? '₹' }}{{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-right">
                                <h5 class="text-success mb-0">
                                    Total: {{ $currencySymbol ?? '₹' }}{{ number_format($order->total_amount, 2) }}
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
                        <i class="fas fa-info-circle me-2"></i>What Happens Next?
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                                <h6>We'll Contact You</h6>
                                <small class="text-muted">Our team will call you within 24 hours to confirm your order</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-handshake fa-2x text-primary mb-2"></i>
                                <h6>Confirm Details</h6>
                                <small class="text-muted">We'll discuss your requirements and finalize the order</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <i class="fas fa-check-double fa-2x text-primary mb-2"></i>
                                <h6>Order Confirmation</h6>
                                <small class="text-muted">Once confirmed, we'll process and deliver your order</small>
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
