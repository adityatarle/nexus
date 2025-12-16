@extends('layouts.app')

@section('title', 'Products - Organic Store')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header mb-4">
                <h1 class="page-title">Our Products</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('agriculture.home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="sidebar">
                <div class="card">
                    <div class="card-header">
                        <h5>Categories</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><a href="{{ route('products.index') }}" class="text-decoration-none">All Products</a></li>
                            @foreach($categories as $category)
                                <li><a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="products-grid">
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <img src="{{ $product->primary_image
                                            ? asset('storage/' . $product->primary_image)
                                            : ($product->featured_image
                                                ? asset('storage/' . $product->featured_image)
                                                : ((is_array($product->gallery_images) && count($product->gallery_images))
                                                    ? asset('storage/' . $product->gallery_images[0])
                                                    : ((is_array($product->images) && count($product->images))
                                                        ? asset('storage/' . $product->images[0])
                                                        : asset('assets/organic/images/product-thumb-1.png')))) }}" 
                                             alt="{{ $product->name }}" class="img-fluid">
                                    </a>
                                    @if($product->discount_percentage > 0)
                                        <div class="product-badge">
                                            <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-content">
                                    <div class="product-category">{{ $product->category->name }}</div>
                                    <h4 class="product-name">
                                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h4>
                                    <div class="product-price">
                                        @if($product->sale_price)
                                            <span class="current-price">₹{{ number_format($product->sale_price, 2) }}</span>
                                            <span class="old-price">₹{{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="product-actions">
                                        <button class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-lg-12">
                            <div class="text-center py-5">
                                <h3>No products found</h3>
                                <p>Try adjusting your search criteria.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                @if($products->hasPages())
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="pagination-wrapper">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.product-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.product-content {
    padding: 20px;
}

.product-category {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.product-name a {
    color: #333;
    text-decoration: none;
}

.product-name a:hover {
    color: #6BB252;
}

.product-price {
    margin-bottom: 15px;
}

.current-price {
    font-size: 1.2rem;
    font-weight: 600;
    color: #6BB252;
}

.old-price {
    font-size: 1rem;
    color: #6c757d;
    text-decoration: line-through;
    margin-left: 10px;
}

.sidebar .card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.sidebar .list-unstyled li {
    margin-bottom: 8px;
}

.sidebar .list-unstyled a {
    color: #333;
    padding: 5px 0;
    display: block;
}

.sidebar .list-unstyled a:hover {
    color: #6BB252;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').click(function() {
        var productId = $(this).data('product-id');
        var button = $(this);
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Update cart count
                $('#cart-count').text(response.cart_count);
                $('#cart-badge').text(response.cart_count);
                
                // Show success message
                button.html('<i class="fas fa-check"></i> Added!');
                button.removeClass('btn-primary').addClass('btn-success');
                
                setTimeout(function() {
                    button.html('Add to Cart');
                    button.removeClass('btn-success').addClass('btn-primary');
                }, 2000);
            },
            error: function(xhr) {
                showAlert('Error adding product to cart', 'error', 'Cart Error');
            }
        });
    });
});
</script>
@endpush
