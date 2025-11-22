@extends('admin.layout')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Edit Product
        </a>
        <a href="{{ route('agriculture.products.show', $product) }}" class="btn btn-outline-info" target="_blank">
            <i class="fas fa-external-link-alt me-2"></i>View on Website
        </a>
    </div>
</div>

<div class="row">
    <!-- Product Images -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Images</h5>
            </div>
            <div class="card-body">
                @php
                    $primaryImageUrl = \App\Helpers\ImageHelper::productImageUrl($product);
                    $galleryImages = is_array($product->gallery_images)
                        ? $product->gallery_images
                        : (json_decode($product->gallery_images ?? '[]', true) ?? []);
                @endphp
                
                <!-- Primary Image -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Primary Image</label>
                    <div class="border rounded p-3 text-center" style="min-height: 300px; background: #f8f9fa;">
                        <img src="{{ $primaryImageUrl }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded"
                             style="max-height: 300px; object-fit: contain;">
                    </div>
                </div>
                
                <!-- Gallery Images -->
                @if(!empty($galleryImages) && count($galleryImages) > 0)
                <div>
                    <label class="form-label fw-bold">Gallery Images</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($galleryImages as $index => $image)
                        <div class="border rounded p-2" style="width: 100px; height: 100px;">
                            @php
                                $galleryImageUrl = \App\Helpers\ImageHelper::imageUrl($image);
                            @endphp
                            <img src="{{ $galleryImageUrl }}" 
                                 alt="Gallery {{ $index + 1 }}" 
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="fas fa-images fa-2x mb-2"></i>
                    <p class="mb-0">No gallery images</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Information -->
    <div class="col-lg-7">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Information</h5>
                <div class="d-flex gap-1">
                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if($product->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @endif
                    <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">
                        {{ $product->in_stock ? 'In Stock' : 'Out of Stock' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Product Name</small>
                        <strong class="fs-5">{{ $product->name }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">SKU</small>
                        <span class="badge bg-info">{{ $product->sku }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Category</small>
                        <span class="badge bg-primary">{{ $product->category->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Subcategory</small>
                        @if($product->subcategory)
                            <span class="badge bg-secondary">{{ $product->subcategory->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                
                @if($product->short_description)
                <div class="mb-3">
                    <small class="text-muted d-block">Short Description</small>
                    <p class="mb-0">{{ $product->short_description }}</p>
                </div>
                @endif
                
                @if($product->description)
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <div class="text-muted">{!! nl2br(e($product->description)) !!}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pricing Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Retail Price</small>
                        <strong class="fs-5">₹{{ number_format($product->price, 2) }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Retail Sale Price</small>
                        @if($product->sale_price)
                            <strong class="fs-5 text-success">₹{{ number_format($product->sale_price, 2) }}</strong>
                            <br><small class="text-muted">Current Price: ₹{{ number_format($product->current_price, 2) }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dealer Price</small>
                        <strong class="fs-5 text-primary">₹{{ number_format($product->dealer_price, 2) }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dealer Sale Price</small>
                        @if($product->dealer_sale_price)
                            <strong class="fs-5 text-success">₹{{ number_format($product->dealer_sale_price, 2) }}</strong>
                            <br><small class="text-muted">Current: ₹{{ number_format($product->current_dealer_price, 2) }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                
                @if($product->price && $product->dealer_price)
                <div class="alert alert-info mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    <strong>Dealer Discount:</strong> 
                    {{ round((($product->price - $product->dealer_price) / $product->price) * 100, 2) }}% OFF from retail price
                </div>
                @endif
            </div>
        </div>

        <!-- Product Specifications -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Specifications</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Brand</small>
                        <span>{{ $product->brand ?: '—' }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Model</small>
                        <span>{{ $product->model ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Stock Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Stock Quantity</small>
                        <span class="badge bg-{{ $product->stock_quantity < 10 ? 'warning' : 'success' }} fs-6">
                            {{ $product->stock_quantity }} units
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Manage Stock</small>
                        <span class="badge bg-{{ $product->manage_stock ? 'info' : 'secondary' }}">
                            {{ $product->manage_stock ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">In Stock</small>
                        <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">
                            {{ $product->in_stock ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    @if($product->is_dealer_exclusive)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dealer Exclusive</small>
                        <span class="badge bg-warning">Yes</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order History -->
        @if($product->orderItems && $product->orderItems->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->orderItems->take(10) as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $item->order) }}" class="text-decoration-none">
                                        #{{ $item->order->id ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>₹{{ number_format($item->total, 2) }}</td>
                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($product->orderItems->count() > 10)
                <div class="text-center mt-2">
                    <small class="text-muted">Showing last 10 orders. Total: {{ $product->orderItems->count() }} orders</small>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Additional Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Slug</small>
                        <code>{{ $product->slug }}</code>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Created</small>
                        <span>{{ $product->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Last Updated</small>
                        <span>{{ $product->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($product->dealer_min_quantity)
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dealer Min Quantity</small>
                        <span>{{ $product->dealer_min_quantity }} units</span>
                    </div>
                    @endif
                    @if($product->dealer_notes)
                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">Dealer Notes</small>
                        <p class="mb-0">{{ $product->dealer_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }}">
                    <i class="fas fa-{{ $product->is_active ? 'pause' : 'play' }} me-2"></i>
                    {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            
            <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-{{ $product->is_featured ? 'secondary' : 'warning' }}">
                    <i class="fas fa-star me-2"></i>
                    {{ $product->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                </button>
            </form>
            
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline delete-form" data-name="{{ $product->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger delete-btn">
                    <i class="fas fa-trash me-2"></i>Delete Product
                </button>
            </form>
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
            const productName = form.getAttribute('data-name') || 'this product';
            
            Swal.fire({
                title: 'Delete Product?',
                html: `Are you sure you want to delete <strong>${productName}</strong>?<br><small class="text-danger">This action cannot be undone!</small>`,
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


