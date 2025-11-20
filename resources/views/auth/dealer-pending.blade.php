@extends('layouts.app')

@section('title', 'Dealer Registration Pending - Nexus Agriculture')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <!-- Status Icon -->
                    <div class="mb-4">
                        <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <svg width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Message -->
                    <h2 class="h3 fw-bold text-dark mb-3">Registration Under Review</h2>
                    <p class="text-muted mb-4">
                        Your dealer registration for <strong>{{ $registration->business_name }}</strong> 
                        is currently under review by our team.
                    </p>

                    <!-- Registration Details -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3">Registration Details</h5>
                            <div class="row g-3 text-start">
                                <div class="col-md-6">
                                    <strong>Business Name:</strong><br>
                                    <span class="text-muted">{{ $registration->business_name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>GST Number:</strong><br>
                                    <span class="text-muted">{{ $registration->gst_number }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Contact Person:</strong><br>
                                    <span class="text-muted">{{ $registration->contact_person }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Contact Email:</strong><br>
                                    <span class="text-muted">{{ $registration->contact_email }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Business Address:</strong><br>
                                    <span class="text-muted">{{ $registration->business_address }}, {{ $registration->business_city }}, {{ $registration->business_state }} - {{ $registration->business_pincode }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Submitted On:</strong><br>
                                    <span class="text-muted">{{ $registration->created_at->format('F j, Y \a\t g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- What Happens Next -->
                    <div class="card border-0 bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">What Happens Next?</h5>
                            <div class="row g-3 text-start">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; min-width: 30px;">
                                            <span class="fw-bold small">1</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Document Review</h6>
                                            <p class="small mb-0">Our team reviews your business documents and information</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; min-width: 30px;">
                                            <span class="fw-bold small">2</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Verification</h6>
                                            <p class="small mb-0">We verify your GST and business details</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; min-width: 30px;">
                                            <span class="fw-bold small">3</span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Approval</h6>
                                            <p class="small mb-0">You'll receive notification of approval or rejection</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark mb-3">Review Timeline</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Registration Submitted</h6>
                                            <p class="text-muted small mb-0">{{ $registration->created_at->format('M j, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px;">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Under Review</h6>
                                            <p class="text-muted small mb-0">Processing...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="alert alert-info border-0">
                        <h6 class="fw-bold mb-2">Need Help?</h6>
                        <p class="mb-2">If you have any questions about your registration, please contact our support team:</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <strong>Email:</strong> dealers@nexusagriculture.com
                            </div>
                            <div class="col-md-6">
                                <strong>Phone:</strong> +1 (555) 123-4567
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('agriculture.home') }}" class="btn btn-primary">
                            Continue Shopping (Retail Prices)
                        </a>
                        <a href="{{ route('auth.logout') }}" class="btn btn-outline-secondary">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















