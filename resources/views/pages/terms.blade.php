@extends('layouts.app')

@section('title', 'Terms of Service - Nexus Agriculture')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-4">Terms of Service</h1>
                <p class="lead text-muted mb-4">
                    Please read these terms carefully before using our services. By using our website, you agree to be bound by these terms.
                </p>
                <p class="text-muted small">Last updated: {{ date('F j, Y') }}</p>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/terms-hero.jpg') }}" alt="Terms of Service" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Terms Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="h4 fw-bold text-dark mb-4">Acceptance of Terms</h2>
                        <p class="text-muted mb-4">
                            By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement. 
                            If you do not agree to abide by the above, please do not use this service.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Use License</h2>
                        <p class="text-muted mb-4">
                            Permission is granted to temporarily download one copy of the materials on Nexus Agriculture's website for personal, 
                            non-commercial transitory viewing only. This is the grant of a license, not a transfer of title.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Product Information</h2>
                        <p class="text-muted mb-4">
                            We strive to provide accurate product information, but we cannot guarantee that all product descriptions, 
                            images, or other content is accurate, complete, or current. Product availability and pricing are subject to change without notice.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Orders and Payment</h2>
                        <p class="text-muted mb-4">
                            All orders are subject to acceptance and availability. We reserve the right to refuse or cancel any order. 
                            Payment must be received before order processing begins. We accept all major credit cards and PayPal.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Shipping and Delivery</h2>
                        <p class="text-muted mb-4">
                            Delivery times are estimates and not guaranteed. We are not responsible for delays caused by weather, 
                            shipping carriers, or other circumstances beyond our control. Risk of loss passes to you upon delivery.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Returns and Refunds</h2>
                        <p class="text-muted mb-4">
                            Returns must be made within 30 days of purchase with original packaging. Fresh produce returns must be made within 48 hours. 
                            Refunds will be processed within 5-7 business days after we receive the returned items.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Limitation of Liability</h2>
                        <p class="text-muted mb-4">
                            In no event shall Nexus Agriculture or its suppliers be liable for any damages arising out of the use or inability to use 
                            the materials on this website, even if Nexus Agriculture or an authorized representative has been notified.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Governing Law</h2>
                        <p class="text-muted mb-4">
                            These terms and conditions are governed by and construed in accordance with the laws of the United States and you irrevocably 
                            submit to the exclusive jurisdiction of the courts in that state or location.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Changes to Terms</h2>
                        <p class="text-muted mb-4">
                            Nexus Agriculture reserves the right to revise these terms at any time without notice. By using this website, 
                            you are agreeing to be bound by the then current version of these terms.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Contact Information</h2>
                        <p class="text-muted mb-4">
                            If you have any questions about these Terms of Service, please contact us at:
                        </p>
                        <div class="bg-light p-4 rounded">
                            <p class="mb-2"><strong>Email:</strong> legal@nexusagriculture.com</p>
                            <p class="mb-2"><strong>Phone:</strong> +1 (555) 123-4567</p>
                            <p class="mb-0"><strong>Address:</strong> 123 Agriculture Street, Farm City, FC 12345</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h3 fw-bold mb-3">Questions About Our Terms?</h2>
                <p class="mb-0">Our legal team is available to help clarify any questions about our terms of service.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('contact') }}" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>
@endsection


















