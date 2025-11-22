@extends('layouts.app')

@section('title', $product->name . ' - Agriculture Equipment')
@section('description', $product->short_description ?? $product->description)

@section('content')
<div class="container-lg py-4">
    @php
        $mainImageUrl = \App\Helpers\ImageHelper::productImageUrl($product);
    @endphp
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="product-image-main mb-4">
                <img id="mainProductImage" src="{{ $mainImageUrl }}" 
                     alt="{{ $product->name }}" class="img-fluid rounded">
                @if($product->sale_price)
                <span class="badge bg-danger position-absolute top-0 end-0 m-3 fs-6">Sale</span>
                @endif
            </div>
            
            @php
                // Get gallery images (handle both array and JSON)
                $galleryImages = is_array($product->gallery_images)
                    ? $product->gallery_images
                    : (json_decode($product->gallery_images ?? '[]', true) ?? []);
                
                // Also check for primary image to include in gallery
                $allImages = [];
                if ($product->primary_image) {
                    $allImages[] = $product->primary_image;
                }
                $allImages = array_merge($allImages, $galleryImages);
            @endphp
            
            @if(!empty($allImages) && count($allImages) > 0)
            <div class="product-thumbnails">
                <div class="row">
                    @foreach($allImages as $index => $image)
                    <div class="col-3 mb-2">
                        @php
                            $thumbUrl = \App\Helpers\ImageHelper::imageUrl($image);
                        @endphp
                        <img src="{{ $thumbUrl }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded thumbnail {{ $index === 0 ? 'active' : '' }}"
                             data-image-url="{{ $thumbUrl }}"
                             onclick="changeMainImage(this)">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('agriculture.home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('agriculture.categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                        <li class="breadcrumb-item active">{{ $product->name }}</li>
                    </ol>
                </nav>
                
                <h1 class="product-title mb-3">{{ $product->name }}</h1>
                
                <div class="product-price mb-3">
                    <span class="current-price fs-3 fw-bold text-primary">{{ $currencySymbol ?? '₹' }}{{ number_format($product->current_price, 2) }}</span>
                    @if($product->sale_price)
                    <span class="old-price fs-5 text-muted ms-2">{{ $currencySymbol ?? '₹' }}{{ number_format($product->price, 2) }}</span>
                    <span class="discount-badge badge bg-success ms-2">{{ $product->discount_percentage }}% OFF</span>
                    @endif
                </div>
                
                <div class="product-specs mb-4">
                    <div class="row">
                        @if($product->brand)
                        <div class="col-md-6 mb-2">
                            <strong>Brand:</strong> {{ $product->brand }}
                        </div>
                        @endif
                        @if($product->model)
                        <div class="col-md-6 mb-2">
                            <strong>Model:</strong> {{ $product->model }}
                        </div>
                        @endif
                    </div>
                </div>
                
                @if($product->description)
                <div class="product-description mb-4">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>
                @endif
                
                <div class="product-actions">
                    @if($product->in_stock)
                    <form action="{{ route('agriculture.cart.add') }}" method="POST" class="d-flex gap-3 align-items-center">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="quantity-selector">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control" style="width: 80px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                <path d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z"/>
                            </svg>
                            Add to Cart
                        </button>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <strong>Out of Stock</strong> - This product is currently unavailable.
                    </div>
                    @endif
                </div>
                
                <div class="product-features mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center mb-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-success me-3">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <div>
                                    <strong>Quality Guaranteed</strong><br>
                                    <small class="text-muted">Premium agriculture equipment</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center mb-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-primary me-3">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <div>
                                    <strong>Expert Support</strong><br>
                                    <small class="text-muted">Professional guidance available</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center mb-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-warning me-3">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                </svg>
                                <div>
                                    <strong>Fast Delivery</strong><br>
                                    <small class="text-muted">Quick shipping to your location</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item d-flex align-items-center mb-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="text-info me-3">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <div>
                                    <strong>Warranty Included</strong><br>
                                    <small class="text-muted">Standard warranty</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Related Products</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card">
                        <div class="product-image">
                            @php
                                $relatedImageUrl = \App\Helpers\ImageHelper::productImageUrl($relatedProduct);
                            @endphp
                            <img src="{{ $relatedImageUrl }}" 
                                 alt="{{ $relatedProduct->name }}" class="img-fluid">
                        </div>
                        <div class="product-content">
                            <h5 class="product-name">
                                <a href="{{ route('agriculture.products.show', $relatedProduct) }}">{{ $relatedProduct->name }}</a>
                            </h5>
                            <div class="product-price">
                                <span class="current-price">{{ $currencySymbol ?? '₹' }}{{ number_format($relatedProduct->current_price, 2) }}</span>
                                @if($relatedProduct->sale_price)
                                <span class="old-price">{{ $currencySymbol ?? '₹' }}{{ number_format($relatedProduct->price, 2) }}</span>
                                @endif
                            </div>
                            <form action="{{ route('agriculture.cart.add') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.product-image-main {
    position: relative;
}

.thumbnail {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
    padding: 2px;
}

.thumbnail:hover {
    opacity: 0.7;
    border-color: #007bff;
}

.thumbnail.active {
    border-color: #007bff;
    opacity: 1;
}

.product-title {
    color: #333;
    font-weight: 600;
}

.discount-badge {
    font-size: 0.8rem;
}

.feature-item {
    padding: 10px;
    border-radius: 8px;
    background: #f8f9fa;
}
</style>

<script>
function changeMainImage(thumbnail) {
    const mainImage = document.getElementById('mainProductImage');
    const imageUrl = thumbnail.getAttribute('data-image-url');
    
    if (mainImage && imageUrl) {
        mainImage.src = imageUrl;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    }
}
</script>
@endsection
