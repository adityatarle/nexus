@extends('layouts.app')

@section('title', 'Agriculture Equipment & Machinery Store')
@section('description', 'Premium agriculture equipment, machinery, and farming supplies for modern farmers')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container-lg">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">Premium Agriculture Equipment</h1>
                <p class="hero-subtitle">Discover the latest farming machinery, equipment, and supplies to boost your agricultural productivity</p>
                <div class="hero-buttons">
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary btn-lg">Shop Equipment</a>
                    <a href="#" class="btn btn-outline-primary btn-lg" onclick="showAlert('About page coming soon!', 'info', 'Coming Soon')">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/banner-1.jpg') }}" alt="Agriculture Equipment" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container-lg">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h3>Quality Equipment</h3>
                    <p>Premium agriculture machinery and equipment from trusted manufacturers</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3>Expert Support</h3>
                    <p>Professional guidance and technical support for all your farming needs</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Quick and reliable delivery to your farm or agricultural facility</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container-lg">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Agriculture Categories</h2>
                <p class="section-subtitle">Explore our comprehensive range of farming equipment and supplies</p>
            </div>
        </div>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="category-card">
                    <div class="category-image">
                        <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('assets/organic/images/category-thumb-1.jpg') }}" alt="{{ $category->name }}" class="img-fluid">
                    </div>
                    <div class="category-content">
                        <h4 class="category-name">{{ $category->name }}</h4>
                        <p class="category-count">{{ $category->products->count() }} Products</p>
                        <a href="{{ route('agriculture.categories.show', $category) }}" class="btn btn-outline-primary btn-sm">View Products</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container-lg">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Featured Equipment</h2>
                <p class="section-subtitle">Handpicked premium agriculture machinery and tools</p>
            </div>
        </div>
        <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ $product->featured_image ? asset('storage/' . $product->featured_image) : asset('assets/organic/images/product-thumb-1.png') }}" alt="{{ $product->name }}" class="img-fluid">
                        @if($product->sale_price)
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
                        @endif
                    </div>
                    <div class="product-content">
                        <p class="product-category">{{ $product->category->name }}</p>
                        <h5 class="product-name">
                            <a href="{{ route('agriculture.products.show', $product) }}">{{ $product->name }}</a>
                        </h5>
                        <div class="product-price">
                            <span class="current-price">₹{{ number_format($product->current_price, 2) }}</span>
                            @if($product->sale_price)
                            <span class="old-price">₹{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="product-specs">
                            @if($product->brand)
                            <small class="text-muted">Brand: {{ $product->brand }}</small>
                            @endif
                        </div>
                        <form action="{{ route('agriculture.cart.add') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary btn-lg">View All Products</a>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section py-5">
    <div class="container-lg">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6">
                <h2 class="newsletter-title">Stay Updated</h2>
                <p class="newsletter-subtitle">Get the latest updates on agriculture equipment and farming technology</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2 mt-4">
                    @csrf
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-light">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
