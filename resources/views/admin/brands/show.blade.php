@extends('admin.layout')

@section('title', 'Brand Details')
@section('page-title', 'Brand Details')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Brand Information</h5>
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                @if($brand->logo)
                    <div class="text-center mb-3">
                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="img-fluid" style="max-height: 150px;">
                    </div>
                @endif
                <div class="mb-3">
                    <small class="text-muted d-block">Name</small>
                    <strong>{{ $brand->name }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Slug</small>
                    <span>{{ $brand->slug }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <span class="text-muted">{{ $brand->description ?: '—' }}</span>
                </div>
                @if($brand->website)
                <div class="mb-3">
                    <small class="text-muted d-block">Website</small>
                    <a href="{{ $brand->website }}" target="_blank" class="text-primary">
                        {{ $brand->website }} <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                @endif
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Sort Order</small>
                    <span>{{ $brand->sort_order }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <span>{{ $brand->created_at->format('M d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Products</small>
                    <span class="badge bg-info">{{ $brand->products_count }} products</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products by {{ $brand->name }}</h5>
                <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($brand->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brand->products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $product->featured_image ? asset('storage/' . $product->featured_image) : asset('assets/organic/images/product-thumb-1.png') }}" 
                                             alt="{{ $product->name }}" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->sale_price)
                                                <span class="badge bg-warning ms-2">Sale</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>₹{{ number_format($product->current_price, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->stock_quantity < 10 ? 'warning' : 'success' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No products found for this brand.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

