@extends('layouts.app')

@section('title', 'About Us - Nexus Agriculture')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-4">About Nexus Agriculture</h1>
                <p class="lead text-muted mb-4">
                    We are passionate about bringing you the freshest, highest-quality agricultural products 
                    directly from local farms to your table.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary btn-lg">Shop Now</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg">Contact Us</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/about-hero.jpg') }}" alt="About Us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="h3 fw-bold text-dark mb-4">Our Mission</h2>
                <p class="text-muted mb-5">
                    To connect consumers with the finest agricultural products while supporting local farmers 
                    and promoting sustainable farming practices. We believe in transparency, quality, and 
                    the power of fresh, organic produce to transform lives.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Quality First</h4>
                    <p class="text-muted">We source only the highest quality products from trusted local farmers.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Sustainable</h4>
                    <p class="text-muted">Promoting eco-friendly farming practices and sustainable agriculture.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Local Support</h4>
                    <p class="text-muted">Supporting local farmers and building stronger communities.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/story.jpg') }}" alt="Our Story" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold text-dark mb-4">Our Story</h2>
                <p class="text-muted mb-4">
                    Founded in 2020, Nexus Agriculture began as a small initiative to connect local farmers 
                    with consumers who value fresh, organic produce. What started as a weekend farmers market 
                    has grown into a comprehensive platform serving thousands of customers.
                </p>
                <p class="text-muted mb-4">
                    Our journey has been driven by a simple belief: everyone deserves access to fresh, 
                    nutritious food that's grown with care and respect for the environment. We work directly 
                    with farmers to ensure fair prices and sustainable practices.
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="h4 fw-bold text-primary mb-1">1000+</h3>
                            <p class="text-muted mb-0">Happy Customers</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h3 class="h4 fw-bold text-primary mb-1">50+</h3>
                            <p class="text-muted mb-0">Local Farmers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="h3 fw-bold text-dark mb-4">Meet Our Team</h2>
                <p class="text-muted">
                    Our dedicated team works tirelessly to bring you the best agricultural products 
                    and exceptional customer service.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <img src="{{ asset('assets/organic/images/team-1.jpg') }}" alt="Team Member" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="h5 fw-bold text-dark mb-2">Sarah Johnson</h4>
                    <p class="text-primary mb-2">Founder & CEO</p>
                    <p class="text-muted small">Passionate about sustainable agriculture and community building.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <img src="{{ asset('assets/organic/images/team-2.jpg') }}" alt="Team Member" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="h5 fw-bold text-dark mb-2">Mike Chen</h4>
                    <p class="text-primary mb-2">Operations Manager</p>
                    <p class="text-muted small">Expert in supply chain management and farmer relations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <img src="{{ asset('assets/organic/images/team-3.jpg') }}" alt="Team Member" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="h5 fw-bold text-dark mb-2">Emily Rodriguez</h4>
                    <p class="text-primary mb-2">Customer Success</p>
                    <p class="text-muted small">Dedicated to ensuring every customer has an amazing experience.</p>
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
                <h2 class="h3 fw-bold mb-3">Ready to Experience Fresh Agriculture?</h2>
                <p class="mb-0">Join thousands of satisfied customers who trust us for their fresh produce needs.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('agriculture.products.index') }}" class="btn btn-light btn-lg">Start Shopping</a>
            </div>
        </div>
    </div>
</section>
@endsection

