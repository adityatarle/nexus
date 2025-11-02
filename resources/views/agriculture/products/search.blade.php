@extends('layouts.app')

@section('title', 'Search Results - Nexus Agriculture')

@section('content')
<!-- Breadcrumb Section -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('agriculture.home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('agriculture.products.index') }}" class="text-decoration-none">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search Results</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Search Results Header -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold text-dark mb-3">Search Results</h1>
                @if(request('q'))
                    <p class="lead text-muted mb-4">
                        Showing results for: <strong>"{{ request('q') }}"</strong>
                    </p>
                @endif
                
                <!-- Search Form -->
                <form action="{{ route('agriculture.products.search') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Search for products...">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-5 bg-light">
    <div class="container">
        @if($products->count() > 0)
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Found {{ $products->total() }} result(s) for "{{ request('q') }}"
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm">Sort by Name</button>
                        <button type="button" class="btn btn-outline-primary btn-sm">Sort by Price</button>
                        <button type="button" class="btn btn-outline-primary btn-sm">Sort by Newest</button>
                    </div>
                </div>
            </div>
            
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
                                        <span class="h5 fw-bold text-primary mb-0">${{ number_format($product->price, 2) }}</span>
                                        @if($product->original_price && $product->original_price > $product->price)
                                            <small class="text-muted text-decoration-line-through ms-2">${{ number_format($product->original_price, 2) }}</small>
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
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <svg width="64" height="64" fill="currentColor" viewBox="0 0 16 16" class="text-muted mb-3">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
                <h3 class="h4 text-muted mb-3">No Results Found</h3>
                <p class="text-muted mb-4">
                    Sorry, we couldn't find any products matching "{{ request('q') }}". 
                    Try adjusting your search terms or browse our categories.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">Browse All Products</a>
                    <a href="{{ route('agriculture.categories.index') }}" class="btn btn-primary">Browse Categories</a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Suggested Searches -->
@if($products->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="h3 fw-bold text-dark mb-4">You Might Also Like</h2>
                <p class="text-muted mb-4">Explore these related categories and products</p>
                
                <div class="row g-3">
                    @php
                        $suggestedCategories = \App\Models\AgricultureCategory::active()
                            ->whereHas('products')
                            ->ordered()
                            ->limit(4)
                            ->get();
                    @endphp
                    
                    @foreach($suggestedCategories as $category)
                        <div class="col-md-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <h4 class="h6 fw-bold text-dark mb-2">{{ $category->name }}</h4>
                                    <p class="text-muted small mb-3">{{ Str::limit($category->description, 60) }}</p>
                                    <a href="{{ route('agriculture.categories.show', $category) }}" class="btn btn-outline-primary btn-sm">
                                        View Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
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







