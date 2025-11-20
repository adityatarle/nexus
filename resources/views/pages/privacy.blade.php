@extends('layouts.app')

@section('title', 'Privacy Policy - Nexus Agriculture')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-dark mb-4">Privacy Policy</h1>
                <p class="lead text-muted mb-4">
                    Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.
                </p>
                <p class="text-muted small">Last updated: {{ date('F j, Y') }}</p>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/organic/images/privacy-hero.jpg') }}" alt="Privacy Policy" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Privacy Policy Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="h4 fw-bold text-dark mb-4">Information We Collect</h2>
                        <p class="text-muted mb-4">
                            We collect information you provide directly to us, such as when you create an account, make a purchase, 
                            or contact us for support. This may include your name, email address, phone number, billing and shipping 
                            addresses, and payment information.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">How We Use Your Information</h2>
                        <p class="text-muted mb-4">
                            We use the information we collect to:
                        </p>
                        <ul class="text-muted mb-4">
                            <li>Process and fulfill your orders</li>
                            <li>Provide customer support</li>
                            <li>Send you important updates about your orders</li>
                            <li>Improve our products and services</li>
                            <li>Send you marketing communications (with your consent)</li>
                        </ul>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Information Sharing</h2>
                        <p class="text-muted mb-4">
                            We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, 
                            except as described in this policy. We may share your information with trusted service providers who 
                            assist us in operating our website and conducting our business.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Data Security</h2>
                        <p class="text-muted mb-4">
                            We implement appropriate security measures to protect your personal information against unauthorized access, 
                            alteration, disclosure, or destruction. All payment information is encrypted and processed securely.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Cookies</h2>
                        <p class="text-muted mb-4">
                            We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. 
                            You can control cookie settings through your browser preferences.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Your Rights</h2>
                        <p class="text-muted mb-4">
                            You have the right to access, update, or delete your personal information. You can also opt out of 
                            marketing communications at any time. To exercise these rights, please contact us.
                        </p>
                        
                        <h2 class="h4 fw-bold text-dark mb-4">Contact Us</h2>
                        <p class="text-muted mb-4">
                            If you have any questions about this Privacy Policy, please contact us at:
                        </p>
                        <div class="bg-light p-4 rounded">
                            <p class="mb-2"><strong>Email:</strong> privacy@nexusagriculture.com</p>
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
                <h2 class="h3 fw-bold mb-3">Questions About Your Privacy?</h2>
                <p class="mb-0">Our privacy team is here to help you understand how we protect your information.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('contact') }}" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>
@endsection


















