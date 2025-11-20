@extends('layouts.app')

@section('title', 'Checkout - Agriculture Equipment')
@section('description', 'Complete your agriculture equipment purchase')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Checkout</h2>
        </div>
    </div>

    @if(session('cart') && count(session('cart')) > 0)
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Billing Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('agriculture.checkout.process') }}" method="POST" id="checkout-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="customer_name">Full Name *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="customer_email">Email Address *</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="customer_phone">Phone Number *</label>
                                    <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">We'll contact you on this number to confirm your order</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="billing_address">Billing Address *</label>
                            <textarea class="form-control @error('billing_address') is-invalid @enderror" 
                                      id="billing_address" name="billing_address" rows="3" required>{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="shipping_address">Shipping Address</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                      id="shipping_address" name="shipping_address" rows="3">{{ old('shipping_address') }}</textarea>
                            <small class="form-text text-muted">Leave blank to use billing address</small>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Order Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" onclick="showAlert('Terms and conditions coming soon!', 'info', 'Coming Soon')">Terms and Conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Submit Inquiry
                        </button>
                        <p class="text-center text-muted mt-3 small">
                            <i class="fas fa-info-circle me-1"></i>
                            By submitting, you agree that we will contact you to confirm your order details.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Order Summary</h4>
                </div>
                <div class="card-body">
                    @php
                        $cart = session('cart', []);
                        $subtotal = 0;
                        $taxRate = 0.08; // 8% tax
                        $user = Auth::user(); // Get authenticated user for dealer pricing
                    @endphp

                    @foreach($cart as $item)
                        @php
                            $product = \App\Models\AgricultureProduct::find($item['product_id']);
                            // Use getPriceForUser to ensure consistent pricing (handles dealer pricing)
                            $price = $item['price'] ?? $product->getPriceForUser($user);
                            $itemTotal = round($price * $item['quantity'], 2);
                            $subtotal += $itemTotal;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $product->name }}</h6>
                                <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                            </div>
                            <span class="font-weight-bold">{{ $currencySymbol ?? '₹' }}{{ number_format($itemTotal, 2) }}</span>
                        </div>
                    @endforeach

                    <hr>

                    @php
                        $subtotal = round($subtotal, 2);
                        $taxAmount = round($subtotal * $taxRate, 2);
                        $shippingCost = 25;
                        $totalAmount = round($subtotal + $taxAmount + $shippingCost, 2);
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>{{ $currencySymbol ?? '₹' }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (8%):</span>
                        <span>{{ $currencySymbol ?? '₹' }}{{ number_format($taxAmount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span>{{ $currencySymbol ?? '₹' }}{{ number_format($shippingCost, 2) }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-success">{{ $currencySymbol ?? '₹' }}{{ number_format($totalAmount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shield-alt text-success fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">Secure Checkout</h6>
                            <small class="text-muted">Your information is protected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">Your cart is empty</h3>
                <p class="text-muted">Add some agriculture equipment to your cart before checkout.</p>
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-control:focus {
    border-color: #6BB252;
    box-shadow: 0 0 0 0.2rem rgba(107, 178, 82, 0.25);
}

.btn-primary {
    background: #6BB252;
    border-color: #6BB252;
}

.btn-primary:hover {
    background: #4a8a3a;
    border-color: #4a8a3a;
}
</style>
@endsection
