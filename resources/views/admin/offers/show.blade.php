@extends('admin.layout')

@section('title', 'Offer Details')
@section('page-title', 'Offer Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Offers
        </a>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Offer
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Offer Information</h5>
                <div class="d-flex gap-1">
                    <span class="badge bg-{{ $offer->is_active ? 'success' : 'secondary' }}">
                        {{ $offer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if($offer->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($offer->banner_image)
                    @php
                        $bannerUrl = \App\Helpers\ImageHelper::imageUrl($offer->banner_image);
                    @endphp
                    <div class="mb-4 text-center">
                        <img src="{{ $bannerUrl }}" alt="{{ $offer->title }}" class="img-fluid rounded" style="max-height: 300px;">
                    </div>
                @endif

                <div class="mb-3">
                    <small class="text-muted d-block">Title</small>
                    <strong class="fs-5">{{ $offer->title }}</strong>
                </div>

                @if($offer->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <p class="mb-0">{{ $offer->description }}</p>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Offer Type</small>
                        <span class="badge bg-info">{{ ucfirst($offer->offer_type) }}</span>
                        @if($offer->offer_type === 'product' && $offer->product)
                            <br><small class="text-muted">{{ $offer->product->name }}</small>
                        @elseif($offer->offer_type === 'category' && $offer->category)
                            <br><small class="text-muted">{{ $offer->category->name }}</small>
                        @elseif($offer->offer_type === 'subcategory' && $offer->subcategory)
                            <br><small class="text-muted">{{ $offer->subcategory->name }}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Discount</small>
                        <strong class="fs-5 text-success">
                            @if($offer->discount_type === 'percentage')
                                {{ $offer->discount_value }}%
                            @else
                                ₹{{ number_format($offer->discount_value, 2) }}
                            @endif
                        </strong>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Validity</small>
                        <div>
                            <div>{{ $offer->start_date->format('M d, Y h:i A') }}</div>
                            <div class="text-muted">to {{ $offer->end_date->format('M d, Y h:i A') }}</div>
                            @if($offer->isExpired())
                                <span class="badge bg-danger">Expired</span>
                            @elseif($offer->isUpcoming())
                                <span class="badge bg-warning">Upcoming</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Usage</small>
                        <div>
                            @if($offer->max_uses)
                                <div>Used: {{ $offer->used_count }} / {{ $offer->max_uses }}</div>
                            @else
                                <div>Used: {{ $offer->used_count }} (Unlimited)</div>
                            @endif
                            @if($offer->max_uses_per_user)
                                <small class="text-muted">Max per user: {{ $offer->max_uses_per_user }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                @if($offer->min_purchase_amount || $offer->min_quantity)
                <div class="row mb-3">
                    @if($offer->min_purchase_amount)
                    <div class="col-md-6">
                        <small class="text-muted d-block">Minimum Purchase</small>
                        <strong>₹{{ number_format($offer->min_purchase_amount, 2) }}</strong>
                    </div>
                    @endif
                    @if($offer->min_quantity)
                    <div class="col-md-6">
                        <small class="text-muted d-block">Minimum Quantity</small>
                        <strong>{{ $offer->min_quantity }} units</strong>
                    </div>
                    @endif
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Applicable To</small>
                        <div class="d-flex gap-1">
                            @if($offer->for_customers)
                                <span class="badge bg-secondary">Customers</span>
                            @endif
                            @if($offer->for_dealers)
                                <span class="badge bg-primary">Dealers</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Priority</small>
                        <span class="badge bg-info">{{ $offer->priority }}</span>
                    </div>
                </div>

                @if($offer->terms_conditions)
                <div class="mb-3">
                    <small class="text-muted d-block">Terms & Conditions</small>
                    <p class="mb-0">{{ $offer->terms_conditions }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $offer->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $offer->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $offer->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.offers.toggle-featured', $offer) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $offer->is_featured ? 'secondary' : 'warning' }} w-100">
                            <i class="fas fa-star me-2"></i>
                            {{ $offer->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="d-inline delete-form" data-name="{{ $offer->title }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100 delete-btn">
                            <i class="fas fa-trash me-2"></i>Delete Offer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle delete confirmation with SweetAlert
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const offerName = form.getAttribute('data-name') || 'this offer';
            
            Swal.fire({
                title: 'Delete Offer?',
                html: `Are you sure you want to delete <strong>${offerName}</strong>?<br><small class="text-danger">This action cannot be undone!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection


