@extends('layouts.app')

@section('title', 'Nexus Agriculture - Empowering Agriculture, Enriching Harvests')
@section('description', 'Leading manufacturer of agricultural implements and machinery. High quality, sturdy, long lasting equipment for modern farming.')

@section('content')

<!-- Hero Section with Get Quote Button -->
<section class="hero-section position-relative" style="background: linear-gradient(135deg, #6BB252 0%, #4a8a3a 100%); min-height: auto; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6 text-white mb-4 mb-lg-0">
                <h1 class="display-4 display-lg-3 fw-bold mb-3 mb-lg-4">NEXUS AGRO IMPLEMENTS & ALLIED INDUSTRY</h1>
                <p class="lead mb-3 mb-lg-4">Empowering Agriculture, Enriching Harvests</p>
                <p class="mb-3 mb-lg-4">We are the leading manufacturer of tractor operated agricultural implements & machineries from last 33 years. High quality, sturdy, long lasting and easy to use equipment for modern farming.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3">
                    <button type="button" class="btn btn-light btn-lg px-3 px-md-5" data-bs-toggle="modal" data-bs-target="#quoteModal">
                        Get a Quote
                    </button>
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-light btn-lg px-3 px-md-5">
                        View Products
                    </a>
                </div>
            </div>
            <div class="col-12 col-lg-6 text-center mt-4 mt-lg-0">
                <img src="{{ asset('assets/organic/images/banner-1.jpg') }}" alt="Agriculture Equipment" class="img-fluid rounded shadow-lg" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Get Quote Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quoteModalLabel">Get a Quote</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="quoteForm" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="quote_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quote_name" name="name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="quote_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="quote_email" name="email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="quote_phone" class="form-label">Phone No. <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="quote_phone" name="phone" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="quote_city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quote_city" name="city" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Interested In <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Boom Sprayer" id="product1">
                                    <label class="form-check-label" for="product1">Boom Sprayer (Spraying Units)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Air Assisted Sprayer-STD" id="product2">
                                    <label class="form-check-label" for="product2">Air Assisted Sprayer-STD Series</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Air Assisted Sprayer-ECO" id="product3">
                                    <label class="form-check-label" for="product3">Air Assisted Sprayer-ECO series</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Low Volume Sprayer" id="product4">
                                    <label class="form-check-label" for="product4">Low Volume Sprayer</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Slurry Units" id="product5">
                                    <label class="form-check-label" for="product5">Slurry Units</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Mini Tractor Trolleys" id="product6">
                                    <label class="form-check-label" for="product6">Mini Tractor Trolleys / Trailer</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Cultivators" id="product7">
                                    <label class="form-check-label" for="product7">Cultivators</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Ridger" id="product8">
                                    <label class="form-check-label" for="product8">Ridger (Single Bottom Ridger)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Harrow" id="product9">
                                    <label class="form-check-label" for="product9">Harrow</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Bed Maker" id="product10">
                                    <label class="form-check-label" for="product10">Bed Maker (Double Bottom Ridger)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Plough" id="product11">
                                    <label class="form-check-label" for="product11">Plough</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="products[]" value="Powder Duster" id="product12">
                                    <label class="form-check-label" for="product12">Powder Duster</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="quote_message" class="form-label">Message / Request</label>
                        <textarea class="form-control" id="quote_message" name="message" rows="4"></textarea>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <p class="mb-0"><strong>For more information contact with us</strong></p>
                            <p class="mb-0">+91 9960851222</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- About Us Section -->
<section class="py-4 py-md-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <h2 class="h1 display-5 fw-bold mb-3 mb-md-4">ABOUT US</h2>
                <p class="lead">We Nexus Agro Implements & Allied Industry are the leading manufacturer of tractor operated agricultural implements & machineries (sprayers) from last 33 years under the brand name <strong>Nexus ADITI</strong>.</p>
                <p>That are high quality, sturdy, long lasting and easy to use for spraying units, blowers, slurry units, weed killing sprayer etc. We have always believed in innovative and progressive research and development, due to which we have been able to design and produce new farming tools, which enable farmers to maximize the productivity of their land.</p>
                <a href="{{ route('about') }}" class="btn btn-primary btn-lg mt-3">Read More...</a>
            </div>
            <div class="col-12 col-lg-6">
                <div class="row g-3">
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 text-center p-3 p-md-4">
                            <h3 class="h2 display-6 fw-bold text-primary mb-2">{{ $stats['total_products'] ?? 0 }}+</h3>
                            <p class="mb-0 small">Products</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 text-center p-3 p-md-4">
                            <h3 class="h2 display-6 fw-bold text-success mb-2">{{ $stats['total_farmers_served'] ?? 0 }}+</h3>
                            <p class="mb-0 small">Happy Farmers</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 text-center p-3 p-md-4">
                            <h3 class="h2 display-6 fw-bold text-warning mb-2">33+</h3>
                            <p class="mb-0 small">Years Experience</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 text-center p-3 p-md-4">
                            <h3 class="h2 display-6 fw-bold text-info mb-2">{{ $stats['total_service_centers'] ?? 15 }}+</h3>
                            <p class="mb-0 small">Service Centers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Agriculture Products Section -->
<section class="py-4 py-md-5">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h1 display-5 fw-bold mb-2 mb-md-3">AGRICULTURE PRODUCTS</h2>
            <p class="lead text-muted">High-quality farming equipment for modern agriculture</p>
        </div>
        <div class="row g-3 g-md-4">
            @forelse($featuredProducts->take(6) as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100 product-card">
                    <div class="position-relative">
                        <a href="{{ route('agriculture.products.show', $product->slug) }}">
                            <img src="{{ $product->featured_image ? asset('storage/' . $product->featured_image) : asset('assets/organic/images/product-thumb-1.png') }}" 
                                 class="card-img-top product-image" 
                                 alt="{{ $product->name }}">
                        </a>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('agriculture.products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($product->name, 50) }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-2">{{ $product->category->name ?? '' }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary mb-0">₹{{ number_format($product->getPriceForUser(auth()->user()), 2) }}</span>
                            <a href="{{ route('agriculture.products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No products available at the moment.</p>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4 mt-md-5">
            <a href="{{ route('agriculture.products.index') }}" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- Storage Tanks Section -->
<section class="py-4 py-md-5 bg-light">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h1 display-5 fw-bold mb-2 mb-md-3">WATER STORAGE TANKS</h2>
            <p class="lead">With over three decades of excellence in the field, we are proud to announce the launch of our new line of water storage tanks. Leveraging our 33 years of expertise in rotomoulding, we are excited to offer superior quality water storage tanks designed to meet diverse needs.</p>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">Customised Agricultural Pesticide Tanks</h5>
                        <p class="text-muted">Customized solutions for agricultural pesticide storage</p>
                        <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">Household Water Storage Tanks</h5>
                        <p class="text-muted">Reliable water storage solutions for households</p>
                        <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">Industrial Water Tanks</h5>
                        <p class="text-muted">Heavy-duty industrial water storage solutions</p>
                        <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chairman Message Section -->
<section class="py-4 py-md-5">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h1 display-5 fw-bold mb-2 mb-md-3">CHAIRMAN MESSAGE</h2>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center avatar-circle">
                                    <span class="fs-4 fs-lg-1">RB</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Mr. Ramesh Boraste</h5>
                                <p class="text-muted mb-0"><strong>(Chairman)</strong></p>
                            </div>
                        </div>
                        <p class="mb-0"><strong>"Dear Farmer friends"</strong></p>
                        <p>I am very happy to be presenting this information on website of <strong>Nexus ADITI branded products</strong> to you. We established to manufacture modern and innovative agricultural equipment...</p>
                        <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary">Read More...</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center avatar-circle">
                                    <span class="fs-4 fs-lg-1">RRB</span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Mr. Rishikesh Ramesh Boraste</h5>
                                <p class="text-muted mb-0"><strong>(Business Development Head)</strong></p>
                            </div>
                        </div>
                        <p>"We aim to grow as a world class agricultural sprayers, implements & machinery manufacturing unit in upcoming years by serving our customers with the highest quality of product available"</p>
                        <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary">Read More...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Customer Testimonials Section -->
<section class="py-4 py-md-5 bg-light">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h1 display-5 fw-bold mb-2 mb-md-3">WHAT OUR CUSTOMER SAYS</h2>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                        </div>
                        <p class="mb-3">"Excellent quality products and great customer service. Highly recommended for all farmers!"</p>
                        <p class="mb-0"><strong>- Happy Customer</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                        </div>
                        <p class="mb-3">"The equipment is very durable and has increased our farm productivity significantly."</p>
                        <p class="mb-0"><strong>- Satisfied Farmer</strong></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                            <svg width="24" height="24" class="text-warning"><use xlink:href="#star-full"></use></svg>
                        </div>
                        <p class="mb-3">"Best agricultural implements in the market. Great value for money!"</p>
                        <p class="mb-0"><strong>- Loyal Customer</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Become A Dealer Section -->
<section class="py-4 py-md-5" style="background: linear-gradient(135deg, #6BB252 0%, #4a8a3a 100%);">
    <div class="container text-white text-center">
        <h2 class="h1 display-5 fw-bold mb-3 mb-md-4">Become A Dealer</h2>
        <p class="lead mb-3 mb-md-4">Interested in distributing our farming sprayers & equipment's? <strong>Nexus ADITI branded</strong> sprayers & equipment's range is evolving rapidly, offering innovative solutions to farmers to reduce waste, increase operational efficiency, yield of the farm, increase productivity & promote environmental stewardship- all while enhancing profitability.</p>
        <p class="mb-3 mb-md-4">If you are ready to join our journey, register your interest today. Want to know more? Contact us!</p>
        <a href="{{ route('dealer.register') }}" class="btn btn-light btn-lg px-3 px-md-5">Connect with Us</a>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-4 py-md-5 bg-light">
    <div class="container">
        <div class="text-center mb-4 mb-md-5">
            <h2 class="h1 display-5 fw-bold mb-2 mb-md-3">Why to choose Nexus ADITI Branded Sprayers & Implements?</h2>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#quality"></use></svg>
                    </div>
                    <h5>Uniform Coverage</h5>
                    <p class="text-muted">Due to balanced air on both side & proper division of air sprayer gives uniform coverage which provides best crop protection & gives best quality of crop</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#delivery"></use></svg>
                    </div>
                    <h5>Time & Labour saving</h5>
                    <p class="text-muted">Nexus ADITI Branded sprayers & other Implements are manufactured to reduce the dependency on labour which also helps to save time & money of the farmer</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#package"></use></svg>
                    </div>
                    <h5>Customized Sprayers & Implements</h5>
                    <p class="text-muted">We are committed to give robust & Durable Sprayers & Implements as per the customer's / farmers / clients requirement</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#secure"></use></svg>
                    </div>
                    <h5>Doorstep Service</h5>
                    <p class="text-muted">Our companies trained service team provides you the doorstep service to install the sprayer</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#quality"></use></svg>
                    </div>
                    <h5>Advance Technology</h5>
                    <p class="text-muted">We are committed to give best quality & advanced technology of sprayer & Implements because agriculture is constantly evolving</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#secure"></use></svg>
                    </div>
                    <h5>After sales service</h5>
                    <p class="text-muted">Our Company gives after sales service & Spare parts at your doorstep</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#delivery"></use></svg>
                    </div>
                    <h5>Pan India Presence</h5>
                    <p class="text-muted">We have Pan India presence to give best quality at the most competitive price for all kind of horticulture farmers</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#quality"></use></svg>
                    </div>
                    <h5>FMTIC Tested & Approved</h5>
                    <p class="text-muted">Our ADITI Branded Quality products are tested from Farm Machinery Testing & Inspection Centre INDIA</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-center p-4">
                    <div class="mb-3">
                        <svg width="48" height="48" class="text-primary"><use xlink:href="#savings"></use></svg>
                    </div>
                    <h5>Subsidy Available</h5>
                    <p class="text-muted">Central government subsidy is available for our products in most of the states of INDIA</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quality Assurance Section -->
<section class="py-4 py-md-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <h2 class="h1 display-5 fw-bold mb-3 mb-md-4">QUALITY ASSURANCE</h2>
                <p>We have been following stringent quality measures since our inception in order to maintain international quality standards in our manufactured and traded range of agricultural equipment. The quality controllers at our premises are well aware of the changing market trends and assure the clients regarding the efficacy of the offered range.</p>
                <h5 class="mt-4 mb-3">As per the quality policy, we ensure the following:</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><svg width="20" height="20" class="text-success me-2"><use xlink:href="#check"></use></svg> Stringent checking of the raw material as well as the final products</li>
                    <li class="mb-2"><svg width="20" height="20" class="text-success me-2"><use xlink:href="#check"></use></svg> Regular training for the employed professionals</li>
                    <li class="mb-2"><svg width="20" height="20" class="text-success me-2"><use xlink:href="#check"></use></svg> Regular up gradation of the installed machines</li>
                    <li class="mb-2"><svg width="20" height="20" class="text-success me-2"><use xlink:href="#check"></use></svg> Safe storage and packaging of the range</li>
                </ul>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5 text-center">
                        <svg class="text-primary mb-3 mb-md-4 quality-icon"><use xlink:href="#quality"></use></svg>
                        <h3 class="h4 mb-2 mb-md-3">Do you want more information?</h3>
                        <p class="text-muted mb-3 mb-md-4">WE ARE READY TO LISTEN TO YOU.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .hero-section {
        padding: 40px 0;
    }
    
    @media (min-width: 768px) {
        .hero-section {
            padding: 60px 0;
        }
    }
    
    @media (min-width: 992px) {
        .hero-section {
            padding: 80px 0;
        }
    }
    
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .card {
        border-radius: 10px;
    }
    
    /* Responsive text sizes */
    @media (max-width: 575.98px) {
        .display-4 {
            font-size: 1.75rem;
        }
        .display-5 {
            font-size: 1.5rem;
        }
        .display-6 {
            font-size: 1.25rem;
        }
        .lead {
            font-size: 1rem;
        }
    }
    
    /* Modal responsive */
    @media (max-width: 575.98px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        .modal-body {
            padding: 1rem !important;
        }
    }
    
    /* Button responsive */
    @media (max-width: 575.98px) {
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
    
    /* Avatar circles */
    .avatar-circle {
        width: 60px;
        height: 60px;
    }
    
    @media (min-width: 992px) {
        .avatar-circle {
            width: 80px;
            height: 80px;
        }
    }
    
    /* Quality icon */
    .quality-icon {
        width: 80px;
        height: 80px;
    }
    
    @media (min-width: 768px) {
        .quality-icon {
            width: 120px;
            height: 120px;
        }
    }
    
    /* Product images */
    .product-image {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }
    
    @media (min-width: 576px) {
        .product-image {
            height: 200px;
        }
    }
    
    @media (min-width: 768px) {
        .product-image {
            height: 250px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Quote form validation
    document.getElementById('quoteForm').addEventListener('submit', function(e) {
        const checkboxes = this.querySelectorAll('input[type="checkbox"][name="products[]"]');
        const checked = Array.from(checkboxes).some(cb => cb.checked);
        
        if (!checked) {
            e.preventDefault();
            alert('Please select at least one product you are interested in.');
            return false;
        }
    });
</script>
@endpush

@endsection
