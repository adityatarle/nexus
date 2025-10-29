@php
    $user = Auth::user();
    $isDealer = $user && $user->canAccessDealerPricing();
    $originalPrice = $isDealer ? ($product->dealer_price ?? 0) : ($product->price ?? 0);
    $salePrice = $isDealer ? ($product->dealer_sale_price ?? null) : ($product->sale_price ?? null);
    $currentPrice = $salePrice ?: $originalPrice;
    $discount = ($salePrice && $originalPrice && $salePrice < $originalPrice) ? round((($originalPrice - $salePrice) / $originalPrice) * 100) : 0;
@endphp

<div class="card border-0 shadow-sm h-100">
    <a href="{{ route('agriculture.products.show', $product->slug) }}" class="text-decoration-none">
        <div class="position-relative overflow-hidden">
            <img src="{{ $product->primary_image
                ? asset('storage/' . $product->primary_image)
                : ($product->featured_image
                    ? asset('storage/' . $product->featured_image)
                    : ((is_array($product->gallery_images) && count($product->gallery_images))
                        ? asset('storage/' . $product->gallery_images[0])
                        : ((is_array($product->images) && count($product->images))
                            ? asset('storage/' . $product->images[0])
                            : asset('assets/organic/images/product-thumb-1.png')))) }}" 
                 alt="{{ $product->name }}" 
                 class="card-img-top"
                 style="height: 200px; object-fit: cover;">
            @if($discount > 0)
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">-{{ $discount }}%</span>
            @endif
            @if($product->is_featured)
                <span class="badge bg-primary position-absolute top-0 start-0 m-2">Featured</span>
            @endif
        </div>
    </a>
    
    <div class="card-body d-flex flex-column">
        <a href="{{ route('agriculture.products.show', $product->slug) }}" class="text-decoration-none">
            <h5 class="card-title fs-6 text-dark mb-2">{{ Str::limit($product->name, 50) }}</h5>
        </a>
        
        @if($product->category)
            <p class="text-muted small mb-2">{{ $product->category->name }}</p>
        @endif
        
        <div class="mb-3">
            @if($discount > 0)
                <span class="text-muted text-decoration-line-through me-2">₹{{ number_format($originalPrice, 2) }}</span>
                <span class="text-primary fw-bold fs-5">₹{{ number_format($currentPrice, 2) }}</span>
            @else
                <span class="text-primary fw-bold fs-5">₹{{ number_format($currentPrice, 2) }}</span>
            @endif
        </div>
        
        <div class="mt-auto">
            <div class="row g-2">
                <div class="col-8">
                    <form action="{{ route('agriculture.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <svg width="16" height="16"><use xlink:href="#cart"></use></svg>
                            Add to Cart
                        </button>
                    </form>
                </div>
                <div class="col-4">
                    @auth
                        <form action="{{ route('agriculture.wishlist.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100" title="Add to Wishlist">
                                <svg width="16" height="16"><use xlink:href="#heart"></use></svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auth.login') }}" class="btn btn-outline-primary btn-sm w-100" title="Login to add to wishlist">
                            <svg width="16" height="16"><use xlink:href="#heart"></use></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
