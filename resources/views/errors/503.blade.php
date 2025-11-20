@extends('layouts.app')

@section('title', '503 - Service Unavailable')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center min-vh-50 align-items-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-warning" style="font-size: 8rem;">503</h1>
                <h2 class="h3 mb-4">We'll Be Right Back</h2>
                <p class="lead mb-4">
                    Our site is currently undergoing scheduled maintenance.
                </p>
                
                <div class="alert alert-warning border-warning mb-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <svg class="me-3" width="30" height="30" fill="currentColor">
                            <use xlink:href="#exclamation-triangle"></use>
                        </svg>
                        <div>
                            <p class="mb-0">
                                <strong>Maintenance in Progress</strong><br>
                                We're making improvements to serve you better. Check back soon!
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-muted">
                        Expected downtime: <strong>{{ $exception->retry ?? 'A few minutes' }}</strong>
                    </p>
                </div>

                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <button onclick="window.location.reload()" class="btn btn-primary btn-lg">
                        <svg class="me-2" width="20" height="20" fill="currentColor">
                            <use xlink:href="#arrow-clockwise"></use>
                        </svg>
                        Try Again
                    </button>
                </div>

                <div class="mt-5">
                    <p class="text-muted small">
                        Follow us for updates:
                        <a href="#" class="text-primary ms-2">Facebook</a> |
                        <a href="#" class="text-primary">Twitter</a> |
                        <a href="#" class="text-primary">Instagram</a>
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

<script>
    // Auto-refresh every 30 seconds during maintenance
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection

















