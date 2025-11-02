@extends('layouts.app')

@section('title', 'Login - Nexus Agriculture')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="h3 fw-bold text-dark mb-3">Welcome Back</h2>
                        <p class="text-muted">Sign in to your account to continue</p>
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

                    <form method="POST" action="{{ route('auth.login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Sign In
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted mb-3">Don't have an account?</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('auth.register') }}" class="btn btn-outline-primary">
                                Create Account
                            </a>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted small mb-3">Are you a dealer or customer?</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('auth.customer-login') }}" class="btn btn-outline-success btn-sm w-100">
                                    Customer Login
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('auth.dealer-login') }}" class="btn btn-outline-warning btn-sm w-100">
                                    Dealer Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection







