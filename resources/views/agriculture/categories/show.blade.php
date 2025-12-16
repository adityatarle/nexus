@extends('layouts.app')

@section('title', $category->name . ' Products - Nexus Agriculture')

@section('content')
<!-- Breadcrumb Section -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('agriculture.home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agriculture.categories.index') }}" class="text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Category Hero Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold text-dark mb-3">{{ $category->name }}</h1>
                <p class="lead text-muted mb-4">{{ $category->description }}</p>
                <div class="d-flex gap-3 align-items-center">
                    <span class="badge bg-primary fs-6">{{ $products->total() }} Products Available</span>
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">View All Products</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('assets/organic/images/category-placeholder.jpg') }}" 
                     alt="{{ $category->name }}" 
                     class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Products Grid Section -->
<section class="py-5 bg-light">
    <div class="container">
        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="card-img-top position-relative overflow-hidden" style="height: 250px;">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/organic/images/product-placeholder.jpg') }}" 
                                     alt="{{ $product->name }}" 
                                     class="img-fluid w-100 h-100 object-cover">
                                @if($product->is_featured)
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <span class="badge bg-warning text-dark">Featured</span>
                                    </div>
                                @endif
                                @if($product->stock_quantity <= 10)
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-danger">Low Stock</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title h5 fw-bold text-dark mb-2">{{ $product->name }}</h3>
                                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->description, 100) }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="h5 fw-bold text-primary mb-0">₹{{ number_format($product->price, 2) }}</span>
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <small class="text-muted text-decoration-line-through ms-2">₹{{ number_format($product->original_price, 2) }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $product->stock_quantity }} in stock</small>
                                </div>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('agriculture.products.show', $product) }}" class="btn btn-primary w-100">
                                        View Details
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="ms-2">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16" class="text-muted mb-3">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                <h3 class="h4 text-muted mb-3">No Products Available</h3>
                <p class="text-muted mb-4">There are currently no products in this category. Check back soon!</p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('agriculture.categories.index') }}" class="btn btn-outline-primary">Browse Categories</a>
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary">View All Products</a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Related Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="h3 fw-bold text-dark mb-4">Other Categories</h2>
                <p class="text-muted">Explore more product categories</p>
            </div>
        </div>
        
        <div class="row g-3">
            @php
                $otherCategories = \App\Models\AgricultureCategory::active()
                    ->where('id', '!=', $category->id)
                    ->ordered()
                    ->limit(4)
                    ->get();
            @endphp
            
            @foreach($otherCategories as $otherCategory)
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <h4 class="h6 fw-bold text-dark mb-2">{{ $otherCategory->name }}</h4>
                            <p class="text-muted small mb-3">{{ Str::limit($otherCategory->description, 60) }}</p>
                            <a href="{{ route('agriculture.categories.show', $otherCategory) }}" class="btn btn-outline-primary btn-sm">
                                View Products
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.object-cover {
    object-fit: cover;
}
</style>
@endpush