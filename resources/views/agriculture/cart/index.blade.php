@extends('layouts.app')

@section('title', 'Shopping Cart - Agriculture Equipment')
@section('description', 'Review your selected agriculture equipment and proceed to checkout')

@section('content')
<div class="container-lg py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Shopping Cart</h2>
            
            @if(count($cartItems) > 0)
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            @foreach($cartItems as $item)
                            <div class="row align-items-center mb-4 pb-4 border-bottom">
                                <div class="col-md-2">
                                    <img src="{{ $item['product']->featured_image ? asset('storage/' . $item['product']->featured_image) : asset('assets/organic/images/product-thumb-1.png') }}" 
                                         alt="{{ $item['product']->name }}" class="img-fluid rounded">
                                </div>
                                <div class="col-md-4">
                                    <h5 class="mb-1">{{ $item['product']->name }}</h5>
                                    <p class="text-muted mb-1">{{ $item['product']->category->name }}</p>
                                    @if($item['product']->brand)
                                    <small class="text-muted">Brand: {{ $item['product']->brand }}</small>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <form action="{{ route('agriculture.cart.update') }}" method="POST" class="d-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control text-center" style="width: 60px;">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Update</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <span class="fw-bold">₹{{ number_format($item['subtotal'], 2) }}</span>
                                </div>
                                <div class="col-md-2">
                                    <form action="{{ route('agriculture.cart.remove') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this item from cart?')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">Continue Shopping</a>
                                <form action="{{ route('agriculture.cart.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Clear entire cart?')">Clear Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>₹{{ number_format($total, 2) }}</strong>
                            </div>
                            
                            <a href="{{ route('agriculture.checkout.index') }}" class="btn btn-primary btn-lg w-100">Proceed to Checkout</a>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    Secure checkout
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Empty Cart -->
            <div class="text-center py-5">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" class="text-muted mb-3">
                    <path d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z"/>
                </svg>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Add some agriculture equipment to get started!</p>
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary">Browse Products</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
