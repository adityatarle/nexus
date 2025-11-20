@extends('layouts.app')

@section('title', 'My Profile - Dealer Dashboard')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Dealer Profile</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please check the form for errors.
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Business Information -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Business Information</h5>
                </div>
                <div class="card-body">
                    @if($registration)
                        <div class="mb-3">
                            <strong>Business Name:</strong>
                            <p class="text-muted">{{ $registration->business_name }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>GST Number:</strong>
                            <p class="text-muted">{{ $registration->gst_number }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>PAN Number:</strong>
                            <p class="text-muted">{{ $registration->pan_number }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Business Type:</strong>
                            <p class="text-muted">{{ $registration->business_type }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Business Address:</strong>
                            <p class="text-muted">
                                {{ $registration->business_address }}<br>
                                {{ $registration->business_city }}, {{ $registration->business_state }} {{ $registration->business_pincode }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong>Approval Status:</strong>
                            <p>
                                <span class="badge bg-success">Approved</span>
                                <br>
                                <small class="text-muted">Approved on {{ $user->dealer_approved_at->format('F d, Y') }}</small>
                            </p>
                        </div>
                    @else
                        <p class="text-muted">No business information available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information (Editable) -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dealer.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Contact Person Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alternate_phone" class="form-label">Alternate Phone</label>
                            <input type="text" class="form-control @error('alternate_phone') is-invalid @enderror" 
                                   id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone', $user->alternate_phone) }}">
                            @error('alternate_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company_website" class="form-label">Company Website</label>
                            <input type="url" class="form-control @error('company_website') is-invalid @enderror" 
                                   id="company_website" name="company_website" value="{{ old('company_website', $user->company_website) }}">
                            @error('company_website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Contact Info
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dealer Benefits Summary -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Your Dealer Benefits</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <i class="fas fa-percent fa-3x text-success mb-2"></i>
                        <h6>Exclusive Wholesale Pricing</h6>
                        <p class="text-muted small">Save up to 25% on all products</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <i class="fas fa-boxes fa-3x text-success mb-2"></i>
                        <h6>Bulk Order Discounts</h6>
                        <p class="text-muted small">Additional savings on large quantities</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <i class="fas fa-shipping-fast fa-3x text-success mb-2"></i>
                        <h6>Priority Shipping</h6>
                        <p class="text-muted small">Faster delivery for dealer orders</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="text-center">
                        <i class="fas fa-headset fa-3x text-success mb-2"></i>
                        <h6>Dedicated Support</h6>
                        <p class="text-muted small">Direct line to dealer support team</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















