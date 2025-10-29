@extends('layouts.app')

@section('title', 'Dealer Product Catalog - Nexus Agriculture')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Wholesale Product Catalog</h2>
    <p class="text-muted mb-4">Exclusive dealer pricing - Save up to 25% on all products</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('dealer.products') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="{{ route('dealer.products') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @php
                            $dealerImg = $product->primary_image
                                ?? $product->featured_image
                                ?? ((is_array($product->gallery_images) && count($product->gallery_images)) ? $product->gallery_images[0] : null)
                                ?? ((is_array($product->images) && count($product->images)) ? $product->images[0] : null);
                        @endphp
                        @if($dealerImg)
                            <img src="{{ asset('storage/' . $dealerImg) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($product->short_description ?? $product->description, 100) }}</p>
                            
                            <div class="mb-3">
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <div class="mb-2">
                                        <span class="text-decoration-line-through text-muted">Retail: ₹{{ number_format($product->price, 2) }}</span>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small">Dealer Price:</span>
                                        <h4 class="text-success mb-0">₹{{ number_format($product->current_dealer_price, 2) }}</h4>
                                    </div>
                                    @if($product->dealer_discount_percentage > 0)
                                        <span class="badge bg-success">{{ $product->dealer_discount_percentage }}% OFF</span>
                                    @endif
                                </div>
                                
                                @if($product->price && $product->dealer_price)
                                    <small class="text-muted">Save ₹{{ number_format($product->price - $product->dealer_price, 2) }} per unit</small>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-grid gap-2">
                                <a href="{{ route('agriculture.products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                                <form action="{{ route('agriculture.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>No products found</h4>
                <p class="text-muted">Try adjusting your filters</p>
            </div>
        </div>
    @endif
</div>
@endsection




