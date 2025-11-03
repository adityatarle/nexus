@extends('layouts.app')

@section('title', 'Contact Us - Nexus Agriculture')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-4">Contact Us</h1>
                <p class="lead text-muted mb-4">
                    Have questions about our products or services? We'd love to hear from you. 
                    Get in touch with our team and we'll respond as soon as possible.
                </p>
                <div class="d-flex gap-3">
                    <a href="tel:+1234567890" class="btn btn-primary btn-lg">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.65 10.5a.678.678 0 0 0-.65-.5H7.5a.5.5 0 0 1-.5-.5V8.15a.678.678 0 0 0-.122-.58L5.594 5.27a.678.678 0 0 0-.122-.58z"/>
                        </svg>
                        Call Us
                    </a>
                    <a href="mailto:info@nexusagriculture.com" class="btn btn-outline-primary btn-lg">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.708 2.825a.5.5 0 0 1-.584 0L5 5.383V13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5.383z"/>
                        </svg>
                        Email Us
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/contact-hero.jpg') }}" alt="Contact Us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <h2 class="h3 fw-bold text-dark mb-4 text-center">Send us a Message</h2>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">Full Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email Address *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label fw-bold">Subject *</label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-bold">Message *</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                                        </svg>
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Visit Our Store</h4>
                    <p class="text-muted mb-0">
                        123 Agriculture Street<br>
                        Farm City, FC 12345<br>
                        United States
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.65 10.5a.678.678 0 0 0-.65-.5H7.5a.5.5 0 0 1-.5-.5V8.15a.678.678 0 0 0-.122-.58L5.594 5.27a.678.678 0 0 0-.122-.58z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Call Us</h4>
                    <p class="text-muted mb-0">
                        Phone: +1 (555) 123-4567<br>
                        Fax: +1 (555) 123-4568<br>
                        Toll Free: 1-800-AGRICULTURE
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.708 2.825a.5.5 0 0 1-.584 0L5 5.383V13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5.383z"/>
                        </svg>
                    </div>
                    <h4 class="h5 fw-bold text-dark mb-3">Email Us</h4>
                    <p class="text-muted mb-0">
                        General: info@nexusagriculture.com<br>
                        Support: support@nexusagriculture.com<br>
                        Sales: sales@nexusagriculture.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Business Hours Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="h3 fw-bold text-dark mb-4">Business Hours</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h4 class="h5 fw-bold text-dark mb-3">Store Hours</h4>
                                <div class="text-start">
                                    <p class="mb-2"><strong>Monday - Friday:</strong> 8:00 AM - 8:00 PM</p>
                                    <p class="mb-2"><strong>Saturday:</strong> 9:00 AM - 6:00 PM</p>
                                    <p class="mb-0"><strong>Sunday:</strong> 10:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h4 class="h5 fw-bold text-dark mb-3">Customer Service</h4>
                                <div class="text-start">
                                    <p class="mb-2"><strong>Monday - Friday:</strong> 7:00 AM - 9:00 PM</p>
                                    <p class="mb-2"><strong>Saturday:</strong> 8:00 AM - 7:00 PM</p>
                                    <p class="mb-0"><strong>Sunday:</strong> 9:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="h3 fw-bold text-dark mb-5 text-center">Frequently Asked Questions</h2>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I place an order?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Simply browse our products, add items to your cart, and proceed to checkout. You can also call us directly to place an order over the phone.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What are your delivery options?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer same-day delivery for local orders and standard shipping for out-of-town customers. Delivery fees vary based on location and order size.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Do you offer organic products?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! We have a wide selection of certified organic products from local farms. Look for the organic label on our product pages.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What is your return policy?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer a 30-day return policy for most products. Fresh produce can be returned within 48 hours if there are quality issues.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection








