@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center min-vh-50 align-items-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-primary" style="font-size: 8rem;">404</h1>
                <h2 class="h3 mb-4">Oops! Page Not Found</h2>
                <p class="lead mb-4">
                    The page you're looking for doesn't exist or has been moved.
                </p>
                
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('agriculture.home') }}" class="btn btn-primary btn-lg">
                        <svg class="me-2" width="20" height="20" fill="currentColor">
                            <use xlink:href="#house"></use>
                        </svg>
                        Go Home
                    </a>
                    <a href="{{ route('agriculture.products.index') }}" class="btn btn-outline-primary btn-lg">
                        <svg class="me-2" width="20" height="20" fill="currentColor">
                            <use xlink:href="#cart"></use>
                        </svg>
                        Browse Products
                    </a>
                </div>

                <div class="mt-5">
                    <p class="text-muted">
                        Need help? <a href="{{ route('contact') }}" class="text-primary">Contact us</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-50 {
        min-height: 50vh;
    }
</style>
@endsection


