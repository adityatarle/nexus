@extends('layouts.app')

@section('title', 'Product Categories - Green Leaf Agriculture')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-4">Product Categories</h1>
                <p class="lead text-muted mb-4">
                    Explore our wide range of agricultural products organized by category. 
                    From fresh vegetables to organic grains, we have everything you need.
                </p>
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary btn-lg">View All Products</a>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/categories-hero.jpg') }}" alt="Categories" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Categories Grid Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($categories as $category)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm category-card {{ ($category->in_stock_products_count ?? 0) == 0 ? 'out-of-stock' : '' }}">
                        <div class="card-img-top position-relative overflow-hidden" style="height: 250px;">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('assets/organic/images/category-placeholder.jpg') }}" 
                                 alt="{{ $category->name }}" 
                                 class="img-fluid w-100 h-100 object-cover {{ ($category->in_stock_products_count ?? 0) == 0 ? 'opacity-50' : '' }}">
                            <div class="position-absolute top-0 start-0 p-3">
                                @if(($category->in_stock_products_count ?? 0) > 0)
                                    <span class="badge bg-success fs-6">{{ $category->in_stock_products_count }} In Stock</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">Out of Stock</span>
                                @endif
                            </div>
                            @if(($category->in_stock_products_count ?? 0) == 0)
                                <div class="position-absolute top-50 start-50 translate-middle text-center w-100">
                                    <div class="bg-white bg-opacity-75 rounded p-3 mx-3">
                                        <svg width="48" height="48" fill="currentColor" class="text-warning mb-2" viewBox="0 0 16 16">
                                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                                        </svg>
                                        <h5 class="text-dark fw-bold mb-1">Coming Soon</h5>
                                        <p class="text-muted small mb-0">Will be back in stock</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h4 fw-bold text-dark mb-3">{{ $category->name }}</h3>
                            <p class="card-text text-muted flex-grow-1">{{ $category->description }}</p>
                            @if(($category->in_stock_products_count ?? 0) > 0)
                                <div class="mt-auto">
                                    <a href="{{ route('agriculture.categories.show', $category) }}" class="btn btn-outline-primary w-100">
                                        View Products
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="ms-2">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <div class="mt-auto">
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                                        </svg>
                                        Notify Me When Available
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16" class="text-muted mb-3">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        <h3 class="h4 text-muted mb-3">No Categories Available</h3>
                        <p class="text-muted mb-4">We're working on adding more categories. Check back soon!</p>
                        <a href="{{ route('agriculture.home') }}" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Featured Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="h3 fw-bold text-dark mb-4">Featured Categories</h2>
                <p class="text-muted">
                    Discover our most popular product categories that our customers love
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Fresh Vegetables</h4>
                    <p class="text-muted">Locally grown, pesticide-free vegetables</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center p-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Organic Fruits</h4>
                    <p class="text-muted">Sweet, juicy fruits from certified organic farms</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center p-4">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Grains & Cereals</h4>
                    <p class="text-muted">Nutritious whole grains and cereals</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="text-center p-4">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Dairy Products</h4>
                    <p class="text-muted">Fresh milk, cheese, and dairy products</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h3 fw-bold mb-3">Can't Find What You're Looking For?</h2>
                <p class="mb-0">Contact us and we'll help you find the perfect agricultural products for your needs.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('contact') }}" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover:not(.out-of-stock) {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.category-card.out-of-stock {
    opacity: 0.9;
}

.object-cover {
    object-fit: cover;
}

.bg-opacity-75 {
    background-color: rgba(255, 255, 255, 0.95) !important;
}

.opacity-50 {
    opacity: 0.5;
}
</style>
@endpush