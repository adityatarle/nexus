@include('layouts.partials.header')

<section class="py-5">
  <div class="container-lg">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="section-title m-0">My Wishlist</h2>
      @if($items->count())
      <form action="{{ route('agriculture.wishlist.clear') }}" method="POST">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger">Clear Wishlist</button>
      </form>
      @endif
    </div>

    @if(!$items->count())
      <div class="alert alert-info">Your wishlist is empty.</div>
    @else
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
      @foreach($items as $item)
        @php $product = $item->product; @endphp
        <div class="col">
          <div class="product-item card h-100">
            <figure class="m-0 p-3 text-center">
              <a href="{{ route('agriculture.products.show', $product) }}" title="{{ $product->name }}">
                <img src="{{ $product->featured_image ? asset('storage/' . $product->featured_image) : asset('assets/organic/images/product-thumb-1.png') }}" alt="{{ $product->name }}" class="img-fluid">
              </a>
            </figure>
            <div class="card-body d-flex flex-column text-center">
              <h3 class="fs-6 fw-normal">{{ $product->name }}</h3>
              @php
                $user = Auth::user();
                $isDealer = $user && $user->canAccessDealerPricing();
                $originalPrice = $isDealer ? ($product->dealer_price ?? null) : ($product->price ?? null);
                $salePrice = $isDealer ? ($product->dealer_sale_price ?? null) : ($product->sale_price ?? null);
                $current = $salePrice ?: $originalPrice;
                $discount = ($salePrice && $originalPrice && $salePrice < $originalPrice) ? round((($originalPrice - $salePrice) / $originalPrice) * 100) : 0;
              @endphp
              <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                @if($discount)
                  <del>₹{{ number_format($originalPrice, 2) }}</del>
                  <span class="text-dark fw-semibold">₹{{ number_format($current, 2) }}</span>
                  <span class="badge border border-dark-subtle rounded-0 fw-normal px-1 fs-7 lh-1 text-body-tertiary">{{ $discount }}% OFF</span>
                @else
                  <span class="text-dark fw-semibold">₹{{ number_format($current, 2) }}</span>
                @endif
              </div>
              <div class="mt-auto">
                <div class="d-flex gap-2 justify-content-center">
                  <form action="{{ route('agriculture.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary"><svg width="18" height="18"><use xlink:href="#cart"></use></svg> Add to Cart</button>
                  </form>
                  <form action="{{ route('agriculture.wishlist.remove') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn-outline-danger"><svg width="18" height="18"><use xlink:href="#trash"></use></svg></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    @endif
  </div>
</section>

@include('layouts.partials.footer')








