@extends('admin.layout')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Category Information</h5>
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Name</small>
                    <strong>{{ $category->name }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Slug</small>
                    <span>{{ $category->slug }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Description</small>
                    <span class="text-muted">{{ $category->description ?: '—' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Sort Order</small>
                    <span>{{ $category->sort_order }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <span>{{ $category->created_at->format('M d, Y') }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Subcategories</small>
                    <span class="badge bg-info">{{ $category->subcategories()->count() }} subcategories</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($category->subcategories()->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subcategories</h5>
                <a href="{{ route('admin.subcategories.create', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Subcategory
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->subcategories as $subcategory)
                            <tr>
                                <td>{{ $subcategory->name }}</td>
                                <td><span class="badge bg-info">{{ $subcategory->products()->count() }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $subcategory->is_active ? 'success' : 'secondary' }}">
                                        {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products in {{ $category->name }}</h5>
                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
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
                            @foreach($category->products as $product)
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
                                <td>{{ $currencySymbol ?? '₹' }}{{ number_format($product->current_price, 2) }}</td>
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
                    <p class="text-muted mb-0">No products found in this category.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection



