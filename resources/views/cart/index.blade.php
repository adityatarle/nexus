@extends('layouts.app')

@section('title', 'Shopping Cart - Organic Store')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header mb-4">
                <h1 class="page-title">Shopping Cart</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('agriculture.home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(count($cart) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-items">
                    @foreach($cart as $id => $item)
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="cart-item-image">
                                        <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('assets/organic/images/product-thumb-1.png') }}" 
                                             alt="{{ $item['name'] }}" class="img-fluid">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="cart-item-details">
                                        <h4 class="cart-item-name">{{ $item['name'] }}</h4>
                                        <p class="cart-item-price">{{ $currencySymbol ?? '₹' }}{{ number_format($item['price'], 2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="cart-item-quantity">
                                        <form action="{{ route('cart.update') }}" method="POST" class="quantity-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="decrease">-</button>
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control quantity-input">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="increase">+</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="cart-item-total">
                                        <strong>{{ $currencySymbol ?? '₹' }}{{ number_format($item['price'] * $item['quantity'], 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="cart-item-actions">
                                        <form action="{{ route('cart.remove') }}" method="POST" class="remove-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $id }}">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="cart-actions mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Continue Shopping</a>
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to clear the cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Order Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>{{ $currencySymbol ?? '₹' }}{{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax:</span>
                                <span>{{ $currencySymbol ?? '₹' }}0.00</span>
                            </div>
                            <hr>
                            <div class="summary-row total">
                                <span><strong>Total:</strong></span>
                                <span><strong>{{ $currencySymbol ?? '₹' }}{{ number_format($cartTotal, 2) }}</strong></span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="btn btn-primary btn-lg w-100" onclick="showAlert('Checkout functionality coming soon!', 'info', 'Coming Soon')">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-lg-12">
                <div class="empty-cart text-center py-5">
                    <div class="empty-cart-icon mb-4">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z"/>
                        </svg>
                    </div>
                    <h3 class="empty-cart-title">Your cart is empty</h3>
                    <p class="empty-cart-text">Looks like you haven't added any items to your cart yet.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Start Shopping</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.cart-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
}

.cart-item-image img {
    border-radius: 8px;
}

.cart-item-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.cart-item-price {
    color: #6c757d;
    margin: 0;
}

.quantity-form .input-group {
    width: 120px;
}

.quantity-input {
    text-align: center;
}

.cart-item-total {
    font-size: 1.1rem;
    text-align: right;
}

.cart-summary .card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.summary-row.total {
    font-size: 1.2rem;
    border-top: 1px solid #e9ecef;
    padding-top: 10px;
    margin-top: 10px;
}

.empty-cart {
    background: #f8f9fa;
    border-radius: 12px;
}

.empty-cart-icon {
    color: #6c757d;
}

.empty-cart-title {
    color: #495057;
    margin-bottom: 15px;
}

.empty-cart-text {
    color: #6c757d;
    margin-bottom: 30px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity update functionality
    $('.quantity-btn').click(function() {
        var action = $(this).data('action');
        var input = $(this).siblings('.quantity-input');
        var currentValue = parseInt(input.val());
        
        if (action === 'increase') {
            input.val(currentValue + 1);
        } else if (action === 'decrease' && currentValue > 1) {
            input.val(currentValue - 1);
        }
        
        // Auto-submit form when quantity changes
        $(this).closest('.quantity-form').submit();
    });
    
    // Auto-submit quantity form on input change
    $('.quantity-input').change(function() {
        $(this).closest('.quantity-form').submit();
    });
});
</script>
@endpush


