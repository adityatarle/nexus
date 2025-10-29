@extends('layouts.app')

@section('title', 'Admin Login - Agriculture Equipment')
@section('description', 'Admin login for agriculture equipment management')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 mt-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-tractor text-primary fa-3x mb-3"></i>
                        <h3 class="fw-bold">Admin Login</h3>
                        <p class="text-muted">Agriculture Equipment Dashboard</p>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="{{ route('agriculture.home') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Back to Website
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Demo Credentials</h6>
                    <p class="card-text small text-muted">
                        <strong>Email:</strong> admin@agriculture.com<br>
                        <strong>Password:</strong> password
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
}

.btn-primary {
    background: #6BB252;
    border-color: #6BB252;
}

.btn-primary:hover {
    background: #4a8a3a;
    border-color: #4a8a3a;
}
</style>
@endsection
