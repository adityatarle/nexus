@extends('admin.layout')

@section('title', 'Inventory Report - Nexus Agriculture Admin')
@section('page-title', 'Inventory Report')

@section('content')
<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-primary">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_products'] }}</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-warning">
            <div class="card-body">
                <div class="stat-number">{{ $stats['low_stock'] }}</div>
                <div class="stat-label">Low Stock Items</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-danger">
            <div class="card-body">
                <div class="stat-number">{{ $stats['out_of_stock'] }}</div>
                <div class="stat-label">Out of Stock</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-success">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($stats['total_stock_value'], 2) }}</div>
                <div class="stat-label">Total Stock Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alerts -->
@if($lowStockProducts->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->stock_quantity == 0 ? 'danger' : 'warning' }}">
                                        {{ $product->stock_quantity }} units
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Update Stock
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

<!-- All Products Inventory -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Products Inventory</h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allProducts as $product)
                        <tr>
                            <td>{{ Str::limit($product->name, 40) }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->stock_quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->stock_quantity < 10)
                                    <span class="badge bg-warning">{{ $product->stock_quantity }} units</span>
                                @else
                                    <span class="badge bg-success">{{ $product->stock_quantity }} units</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($product->stock_quantity * $product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $allProducts->links() }}
    </div>
</div>
@endsection


















