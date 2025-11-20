@extends('admin.layout')

@section('title', 'Offers Management')
@section('page-title', 'Offers Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Offers</h4>
    <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Offer
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.offers.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search offers..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="category" {{ request('type') == 'category' ? 'selected' : '' }}>Category</option>
                    <option value="subcategory" {{ request('type') == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Offers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Banner</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Discount</th>
                        <th>Validity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                    <tr>
                        <td>
                            @if($offer->banner_image)
                                @php
                                    $bannerUrl = \App\Helpers\ImageHelper::imageUrl($offer->banner_image);
                                @endphp
                                <img src="{{ $bannerUrl }}" alt="{{ $offer->title }}" class="rounded" width="60" height="40" style="object-fit: cover;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-1">{{ $offer->title }}</h6>
                                <small class="text-muted">{{ Str::limit($offer->description, 50) }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($offer->offer_type) }}</span>
                            @if($offer->offer_type === 'product' && $offer->product)
                                <br><small class="text-muted">{{ $offer->product->name }}</small>
                            @elseif($offer->offer_type === 'category' && $offer->category)
                                <br><small class="text-muted">{{ $offer->category->name }}</small>
                            @elseif($offer->offer_type === 'subcategory' && $offer->subcategory)
                                <br><small class="text-muted">{{ $offer->subcategory->name }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">
                                @if($offer->discount_type === 'percentage')
                                    {{ $offer->discount_value }}%
                                @else
                                    ₹{{ number_format($offer->discount_value, 2) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <small>
                                <div>{{ $offer->start_date->format('M d, Y') }}</div>
                                <div class="text-muted">to {{ $offer->end_date->format('M d, Y') }}</div>
                                @if($offer->isExpired())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($offer->isUpcoming())
                                    <span class="badge bg-warning">Upcoming</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-{{ $offer->is_active ? 'success' : 'secondary' }}">
                                    {{ $offer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if($offer->is_featured)
                                <span class="badge bg-warning">Featured</span>
                                @endif
                                @if($offer->for_customers && $offer->for_dealers)
                                    <span class="badge bg-info">All Users</span>
                                @elseif($offer->for_dealers)
                                    <span class="badge bg-primary">Dealers Only</span>
                                @else
                                    <span class="badge bg-secondary">Customers Only</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.offers.show', $offer) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="d-inline delete-form" data-name="{{ $offer->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-tags fa-2x mb-2"></i>
                            <p>No offers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($offers->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $offers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Handle delete confirmations with SweetAlert
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


