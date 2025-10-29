@extends('layouts.app')

@section('content')

   {{-- Hero Banner Section --}}
<section 
  style="background-image: url('{{ asset('assets/organic/images/banner-1.jpg') }}');
         background-repeat: no-repeat;
         background-size: cover;
         background-position: center;
         min-height: 100vh;">
  <div class="container-lg">
    <div class="row">
      <div class="col-lg-6 pt-5 mt-5">
        <h2 class="display-1 ls-1 text-dark">
          <span class="fw-bold text-primary">Agriculture</span> Equipment & 
          <span class="fw-bold">Products</span>
        </h2>
        <p class="fs-4 text-dark">High-quality farming tools and organic solutions at your doorstep.</p>
        <div class="d-flex gap-3">
          <a href="{{ route('agriculture.products.index') }}" 
             class="btn btn-primary text-uppercase fs-6 rounded-pill px-4 py-3 mt-3">
             Shop Now
          </a>
          <a href="{{ route('dealer.registration') }}" 
             class="btn btn-dark text-uppercase fs-6 rounded-pill px-4 py-3 mt-3">
             Become a Wholesaler
          </a>
        </div>
        <div class="row my-5">
          <div class="col">
            <div class="row text-dark">
              <div class="col-auto">
                <p class="fs-1 fw-bold lh-sm mb-0">200+</p>
              </div>
              <div class="col">
                <p class="text-uppercase lh-sm mb-0">Agriculture Products</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="row text-dark">
              <div class="col-auto">
                <p class="fs-1 fw-bold lh-sm mb-0">5k+</p>
              </div>
              <div class="col">
                <p class="text-uppercase lh-sm mb-0">Happy Farmers</p>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="row text-dark">
              <div class="col-auto">
                <p class="fs-1 fw-bold lh-sm mb-0">50+</p>
              </div>
              <div class="col">
                <p class="text-uppercase lh-sm mb-0">Wholesale Partners</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    {{-- Feature cards --}}
    <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-3 g-0 justify-content-center">
      <div class="col">
        <div class="card border-0 bg-primary rounded-0 p-4 text-light">
          <div class="row">
            <div class="col-md-3 text-center">
              <svg width="60" height="60"><use xlink:href="#fresh"></use></svg>
            </div>
            <div class="col-md-9">
              <div class="card-body p-0">
                <h5 class="text-light">Farm Fresh Equipment</h5>
                <p class="card-text">Top-quality tools and products sourced directly from trusted manufacturers.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col">
        <div class="card border-0 bg-success rounded-0 p-4 text-light">
          <div class="row">
            <div class="col-md-3 text-center">
              <svg width="60" height="60"><use xlink:href="#organic"></use></svg>
            </div>
            <div class="col-md-9">
              <div class="card-body p-0">
                <h5 class="text-light">100% Organic Solutions</h5>
                <p class="card-text">Eco-friendly fertilizers, seeds, and pesticides to ensure healthy crops.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col">
        <div class="card border-0 bg-danger rounded-0 p-4 text-light">
          <div class="row">
            <div class="col-md-3 text-center">
              <svg width="60" height="60"><use xlink:href="#delivery"></use></svg>
            </div>
            <div class="col-md-9">
              <div class="card-body p-0">
                <h5 class="text-light">Nationwide Delivery</h5>
                <p class="card-text">Fast and free shipping across India for all bulk and retail orders.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</section>



    {{-- Stats Section --}}
    <section id="company-services" class="padding-large">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6 pb-3">
            <div class="icon-box d-flex">
              <div class="icon-box-icon pe-3 pb-3">
                <svg class="cart-outline">
                  <use xlink:href="#cart-outline"></use>
                </svg>
              </div>
              <div class="icon-box-content">
                <h3 class="card-title text-uppercase text-dark">{{ $stats['total_products'] ?? 0 }}+</h3>
                <p>Quality Products Available</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 pb-3">
            <div class="icon-box d-flex">
              <div class="icon-box-icon pe-3 pb-3">
                <svg class="quality">
                  <use xlink:href="#quality"></use>
                </svg>
              </div>
              <div class="icon-box-content">
                <h3 class="card-title text-uppercase text-dark">{{ $stats['total_categories'] ?? 0 }}+</h3>
                <p>Product Categories</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 pb-3">
            <div class="icon-box d-flex">
              <div class="icon-box-icon pe-3 pb-3">
                <svg class="price-tag">
                  <use xlink:href="#price-tag"></use>
                </svg>
              </div>
              <div class="icon-box-content">
                <h3 class="card-title text-uppercase text-dark">{{ $stats['total_customers'] ?? 0 }}+</h3>
                <p>Happy Customers</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 pb-3">
            <div class="icon-box d-flex">
              <div class="icon-box-icon pe-3 pb-3">
                <svg class="shield-plus">
                  <use xlink:href="#shield-plus"></use>
                </svg>
              </div>
              <div class="icon-box-content">
                <h3 class="card-title text-uppercase text-dark">100%</h3>
                <p>Secure Payment</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Categories Section --}}
    <section id="categories" class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-header d-flex flex-wrap justify-content-between my-4">
              <h2 class="section-title">Equipment Categories</h2>
              <div class="d-flex align-items-center">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary rounded-1">View All</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-3">
          @forelse($categories as $category)
          <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="{{ route('agriculture.products.index', ['category' => $category->slug]) }}" class="text-decoration-none">
              <div class="card border-0 text-center shadow-sm h-100">
                <div class="card-body">
                  <div class="category-icon mb-3">
                    @if($category->image)
                      <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid" style="max-height: 80px;">
                    @else
                      <svg width="60" height="60" class="text-primary"><use xlink:href="#cart-outline"></use></svg>
                    @endif
                  </div>
                  <h5 class="fs-6">{{ $category->name }}</h5>
                  <p class="text-muted small">{{ $category->products_count }} items</p>
                </div>
              </div>
            </a>
          </div>
          @empty
          <div class="col-12 text-center py-5">
            <p class="text-muted">No categories available</p>
          </div>
          @endforelse
        </div>
      </div>
    </section>

    {{-- Best Selling Products --}}
    <section id="best-selling" class="py-5 bg-light">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-header d-flex flex-wrap justify-content-between my-4">
              <h2 class="section-title">Best Selling Products</h2>
              <div class="d-flex align-items-center">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary rounded-1">View All</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-3">
          @forelse($bestSellers as $product)
          <div class="col-6 col-sm-6 col-md-4 col-lg-3">
            @include('components.product-card', ['product' => $product])
          </div>
          @empty
          <div class="col-12 text-center py-5">
            <p class="text-muted">No products available at the moment.</p>
            <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary">Browse All Products</a>
          </div>
          @endforelse
        </div>
      </div>
    </section>

    {{-- Featured Products --}}
    <section id="featured-products" class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-header d-flex flex-wrap justify-content-between my-4">
              <h2 class="section-title">Featured Products</h2>
              <div class="d-flex align-items-center">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary rounded-1">View All</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-3">
          @forelse($featuredProducts as $product)
          <div class="col-6 col-sm-6 col-md-4 col-lg-3">
            @include('components.product-card', ['product' => $product])
          </div>
          @empty
          <div class="col-12 text-center py-5">
            <p class="text-muted">No featured products available.</p>
          </div>
          @endforelse
        </div>
      </div>
    </section>

    {{-- Promotional Banner --}}
    <section class="py-5 bg-primary text-white">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="display-5 mb-3">Get 25% Discount on Your First Purchase!</h2>
            <p class="lead mb-0">Sign up now and enjoy exclusive deals on agricultural equipment and products.</p>
          </div>
          <div class="col-md-4 text-md-end">
            <a href="{{ route('auth.register') }}" class="btn btn-light btn-lg">Register Now</a>
          </div>
        </div>
      </div>
    </section>

    {{-- Just Arrived (New Products) --}}
    <section id="new-arrivals" class="py-5 bg-light">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-header d-flex flex-wrap justify-content-between my-4">
              <h2 class="section-title">Just Arrived</h2>
              <div class="d-flex align-items-center">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary rounded-1">View All</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row g-3">
          @forelse($newArrivals as $product)
          <div class="col-6 col-sm-6 col-md-4 col-lg-3">
            @include('components.product-card', ['product' => $product])
          </div>
          @empty
          <div class="col-12 text-center py-5">
            <p class="text-muted">No new arrivals at the moment.</p>
          </div>
          @endforelse
        </div>
      </div>
    </section>

    {{-- Features Section --}}
    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body text-center p-4">
                <div class="text-primary mb-3">
                  <svg width="48" height="48"><use xlink:href="#package"></use></svg>
                </div>
                <h5 class="card-title">Free Delivery</h5>
                <p class="card-text text-muted">On orders above ₹5,000</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body text-center p-4">
                <div class="text-primary mb-3">
                  <svg width="48" height="48"><use xlink:href="#secure"></use></svg>
                </div>
                <h5 class="card-title">100% Secure Payment</h5>
                <p class="card-text text-muted">Your money is safe with us</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body text-center p-4">
                <div class="text-primary mb-3">
                  <svg width="48" height="48"><use xlink:href="#quality"></use></svg>
                </div>
                <h5 class="card-title">Quality Guarantee</h5>
                <p class="card-text text-muted">Certified products only</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection
