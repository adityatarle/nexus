@extends('layouts.app')

@section('title', 'Dealer Registration - Green Leaf Agriculture')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="h3 fw-bold text-dark mb-3">Dealer Registration</h2>
                        <p class="text-muted">Complete your dealer registration to access wholesale pricing</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dealer.register') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Business Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-dark mb-3">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                    Business Information
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="business_name" class="form-label fw-bold">Business Name *</label>
                                        <input type="text" 
                                               class="form-control @error('business_name') is-invalid @enderror" 
                                               id="business_name" 
                                               name="business_name" 
                                               value="{{ old('business_name') }}" 
                                               required>
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="business_type" class="form-label fw-bold">Business Type *</label>
                                        <select class="form-select @error('business_type') is-invalid @enderror" 
                                                id="business_type" 
                                                name="business_type" 
                                                required>
                                            <option value="">Select Business Type</option>
                                            <option value="Individual" {{ old('business_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                                            <option value="Partnership" {{ old('business_type') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                            <option value="Company" {{ old('business_type') == 'Company' ? 'selected' : '' }}>Company</option>
                                            <option value="LLP" {{ old('business_type') == 'LLP' ? 'selected' : '' }}>LLP</option>
                                            <option value="HUF" {{ old('business_type') == 'HUF' ? 'selected' : '' }}>HUF</option>
                                        </select>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="gst_number" class="form-label fw-bold">GST Number</label>
                                        <input type="text" 
                                               class="form-control @error('gst_number') is-invalid @enderror" 
                                               id="gst_number" 
                                               name="gst_number" 
                                               value="{{ old('gst_number') }}" 
                                               placeholder="22ABCDE1234F1Z5">
                                        <small class="text-muted">Format: 22ABCDE1234F1Z5 (Optional)</small>
                                        @error('gst_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pan_number" class="form-label fw-bold">PAN Number *</label>
                                        <input type="text" 
                                               class="form-control @error('pan_number') is-invalid @enderror" 
                                               id="pan_number" 
                                               name="pan_number" 
                                               value="{{ old('pan_number') }}" 
                                               placeholder="ABCDE1234F"
                                               required>
                                        <small class="text-muted">Format: ABCDE1234F</small>
                                        @error('pan_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="business_address" class="form-label fw-bold">Business Address *</label>
                                        <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                                  id="business_address" 
                                                  name="business_address" 
                                                  rows="3" 
                                                  required>{{ old('business_address') }}</textarea>
                                        @error('business_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="business_city" class="form-label fw-bold">City *</label>
                                        <input type="text" 
                                               class="form-control @error('business_city') is-invalid @enderror" 
                                               id="business_city" 
                                               name="business_city" 
                                               value="{{ old('business_city') }}" 
                                               required>
                                        @error('business_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="business_state" class="form-label fw-bold">State *</label>
                                        <input type="text" 
                                               class="form-control @error('business_state') is-invalid @enderror" 
                                               id="business_state" 
                                               name="business_state" 
                                               value="{{ old('business_state') }}" 
                                               required>
                                        @error('business_state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="business_pincode" class="form-label fw-bold">Pincode *</label>
                                        <input type="text" 
                                               class="form-control @error('business_pincode') is-invalid @enderror" 
                                               id="business_pincode" 
                                               name="business_pincode" 
                                               value="{{ old('business_pincode') }}" 
                                               placeholder="123456"
                                               required>
                                        @error('business_pincode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-dark mb-3">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                        <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.65 10.5a.678.678 0 0 0-.65-.5H7.5a.5.5 0 0 1-.5-.5V8.15a.678.678 0 0 0-.122-.58L5.594 5.27a.678.678 0 0 0-.122-.58z"/>
                                    </svg>
                                    Contact Information
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="contact_person" class="form-label fw-bold">Contact Person *</label>
                                        <input type="text" 
                                               class="form-control @error('contact_person') is-invalid @enderror" 
                                               id="contact_person" 
                                               name="contact_person" 
                                               value="{{ old('contact_person') }}" 
                                               required>
                                        @error('contact_person')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="contact_email" class="form-label fw-bold">Contact Email *</label>
                                        <input type="email" 
                                               class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" 
                                               name="contact_email" 
                                               value="{{ old('contact_email') }}" 
                                               required>
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="contact_phone" class="form-label fw-bold">Contact Phone *</label>
                                        <input type="tel" 
                                               class="form-control @error('contact_phone') is-invalid @enderror" 
                                               id="contact_phone" 
                                               name="contact_phone" 
                                               value="{{ old('contact_phone') }}" 
                                               required>
                                        @error('contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="alternate_phone" class="form-label fw-bold">Alternate Phone</label>
                                        <input type="tel" 
                                               class="form-control @error('alternate_phone') is-invalid @enderror" 
                                               id="alternate_phone" 
                                               name="alternate_phone" 
                                               value="{{ old('alternate_phone') }}">
                                        @error('alternate_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="company_website" class="form-label fw-bold">Company Website</label>
                                        <input type="url" 
                                               class="form-control @error('company_website') is-invalid @enderror" 
                                               id="company_website" 
                                               name="company_website" 
                                               value="{{ old('company_website') }}" 
                                               placeholder="https://www.example.com">
                                        @error('company_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Details -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-dark mb-3">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                    </svg>
                                    Business Details
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="business_description" class="form-label fw-bold">Business Description *</label>
                                        <textarea class="form-control @error('business_description') is-invalid @enderror" 
                                                  id="business_description" 
                                                  name="business_description" 
                                                  rows="4" 
                                                  required>{{ old('business_description') }}</textarea>
                                        <small class="text-muted">Describe your business, products, and experience in agriculture</small>
                                        @error('business_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="years_in_business" class="form-label fw-bold">Years in Business</label>
                                        <input type="number" 
                                               class="form-control @error('years_in_business') is-invalid @enderror" 
                                               id="years_in_business" 
                                               name="years_in_business" 
                                               value="{{ old('years_in_business') }}" 
                                               min="0" 
                                               max="100">
                                        @error('years_in_business')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="annual_turnover" class="form-label fw-bold">Annual Turnover</label>
                                        <select class="form-select @error('annual_turnover') is-invalid @enderror" 
                                                id="annual_turnover" 
                                                name="annual_turnover">
                                            <option value="">Select Annual Turnover</option>
                                            <option value="0-10L" {{ old('annual_turnover') == '0-10L' ? 'selected' : '' }}>0-10 Lakhs</option>
                                            <option value="10L-50L" {{ old('annual_turnover') == '10L-50L' ? 'selected' : '' }}>10-50 Lakhs</option>
                                            <option value="50L-1Cr" {{ old('annual_turnover') == '50L-1Cr' ? 'selected' : '' }}>50 Lakhs - 1 Crore</option>
                                            <option value="1Cr-5Cr" {{ old('annual_turnover') == '1Cr-5Cr' ? 'selected' : '' }}>1-5 Crores</option>
                                            <option value="5Cr+" {{ old('annual_turnover') == '5Cr+' ? 'selected' : '' }}>5+ Crores</option>
                                        </select>
                                        @error('annual_turnover')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold text-dark mb-3">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                    </svg>
                                    Business Documents (Optional)
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="gst_certificate" class="form-label fw-bold">GST Certificate</label>
                                        <input type="file" 
                                               class="form-control @error('gst_certificate') is-invalid @enderror" 
                                               id="gst_certificate" 
                                               name="gst_certificate" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                        @error('gst_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="pan_certificate" class="form-label fw-bold">PAN Certificate</label>
                                        <input type="file" 
                                               class="form-control @error('pan_certificate') is-invalid @enderror" 
                                               id="pan_certificate" 
                                               name="pan_certificate" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                        @error('pan_certificate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="business_license" class="form-label fw-bold">Business License</label>
                                        <input type="file" 
                                               class="form-control @error('business_license') is-invalid @enderror" 
                                               id="business_license" 
                                               name="business_license" 
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                                        @error('business_license')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                           id="terms_accepted" 
                                           name="terms_accepted" 
                                           required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="{{ route('terms') }}" target="_blank">Terms of Service</a> 
                                        and <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a> *
                                    </label>
                                    @error('terms_accepted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Submit Registration
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-0">
                            <small>Your registration will be reviewed by our team. You'll be notified once approved.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















