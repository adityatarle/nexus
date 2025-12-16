@extends('layouts.app')

@section('title', 'Agriculture Products - Equipment & Machinery')
@section('description', 'Browse our comprehensive collection of agriculture equipment, machinery, and farming supplies')

@section('content')
<div class="container-lg py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Filter Products</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('agriculture.products.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search products...">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Brand Filter -->
                        @if($brands->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand" class="form-select">
                                <option value="">All Brands</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                    {{ $brand }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <!-- Power Source Filter -->
                        @if($powerSources->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Power Source</label>
                            <select name="power_source" class="form-select">
                                <option value="">All Power Sources</option>
                                @foreach($powerSources as $powerSource)
                                <option value="{{ $powerSource }}" {{ request('power_source') == $powerSource ? 'selected' : '' }}>
                                    {{ $powerSource }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}" placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" value="{{ request('max_price') }}" placeholder="Max">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Agriculture Products</h2>
                <div class="d-flex gap-2">
                    <select class="form-select" onchange="this.form.submit()">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="row">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ $product->primary_image
                                ? asset('storage/' . $product->primary_image)
                                : ($product->featured_image
                                    ? asset('storage/' . $product->featured_image)
                                    : ((is_array($product->gallery_images) && count($product->gallery_images))
                                        ? asset('storage/' . $product->gallery_images[0])
                                        : ((is_array($product->images) && count($product->images))
                                            ? asset('storage/' . $product->images[0])
                                            : asset('assets/organic/images/product-thumb-1.png')))) }}" alt="{{ $product->name }}" class="img-fluid">
                            @if($product->sale_price)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
                            @endif
                            @if($user && $user->canAccessDealerPricing() && $product->dealer_price)
                            <span class="badge bg-success position-absolute top-0 start-0 m-2">Dealer Price</span>
                            @endif
                        </div>
                        <div class="product-content">
                            <p class="product-category">{{ $product->category->name }}</p>
                            <h5 class="product-name">
                                <a href="{{ route('agriculture.products.show', $product) }}">{{ $product->name }}</a>
                            </h5>
                            <div class="product-price">
                                @if($user && $user->canAccessDealerPricing() && $product->dealer_price)
                                    <!-- Dealer Pricing -->
                                    <div class="dealer-pricing">
                                        <span class="current-price text-success fw-bold">₹{{ number_format($product->getPriceForUser($user), 2) }}</span>
                                        <small class="text-success d-block">Dealer Price</small>
                                        @if($product->dealer_price < $product->price)
                                            <small class="text-muted">Retail: ₹{{ number_format($product->price, 2) }}</small>
                                        @endif
                                    </div>
                                @else
                                    <!-- Retail Pricing -->
                                    <span class="current-price">₹{{ number_format($product->current_price, 2) }}</span>
                                    @if($product->sale_price)
                                        <span class="old-price">₹{{ number_format($product->price, 2) }}</span>
                                    @endif
                                    @if($product->dealer_price)
                                        <small class="text-muted d-block">Dealer pricing available</small>
                                    @endif
                                @endif
                            </div>
                            <div class="product-specs">
                                @if($product->brand)
                                <small class="text-muted">Brand: {{ $product->brand }}</small><br>
                                @endif
                                @if($product->model)
                                <small class="text-muted">Model: {{ $product->model }}</small><br>
                                @endif
                                @if($product->power_source)
                                <small class="text-muted">Power: {{ $product->power_source }}</small>
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
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <h3>No products found</h3>
                        <p>Try adjusting your search criteria or browse all products.</p>
                        <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary">View All Products</a>
                    </div>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-submit form when sort changes
document.querySelector('select[onchange]').addEventListener('change', function() {
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route("agriculture.products.index") }}';
    
    // Add current query parameters
    const currentParams = new URLSearchParams(window.location.search);
    currentParams.set('sort', this.value);
    
    for (const [key, value] of currentParams) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
});
</script>
@endsection
