@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center min-vh-50 align-items-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-danger" style="font-size: 8rem;">500</h1>
                <h2 class="h3 mb-4">Internal Server Error</h2>
                <p class="lead mb-4">
                    Something went wrong on our end. We're working to fix it.
                </p>
                
                <div class="alert alert-light border mb-4">
                    <p class="mb-0">
                        <strong>What can you do?</strong><br>
                        Try refreshing the page or come back in a few minutes.
                    </p>
                </div>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('agriculture.home') }}" class="btn btn-primary btn-lg">
                        <svg class="me-2" width="20" height="20" fill="currentColor">
                            <use xlink:href="#house"></use>
                        </svg>
                        Go Home
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-outline-primary btn-lg">
                        <svg class="me-2" width="20" height="20" fill="currentColor">
                            <use xlink:href="#arrow-clockwise"></use>
                        </svg>
                        Refresh Page
                    </button>
                </div>

                <div class="mt-5">
                    <p class="text-muted">
                        If the problem persists, please <a href="{{ route('contact') }}" class="text-primary">contact support</a>
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


