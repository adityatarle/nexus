@extends('layouts.app')

@section('title', 'My Orders - Dealer Dashboard')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('dealer.orders') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by order number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="{{ route('dealer.orders') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Order #{{ $order->order_number }}</h6>
                        <small class="text-muted">Placed on {{ $order->created_at->format('F d, Y h:i A') }}</small>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $color = $statusColors[$order->order_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($order->order_status) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Items:</h6>
                            @foreach($order->items as $item)
                                <div class="d-flex mb-2">
                                    <div>
                                        <strong>{{ $item->product->name ?? 'Product N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">Quantity: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-4 text-end">
                            <h6>Total Amount</h6>
                            <h4 class="text-success">₹{{ number_format($order->total_amount, 2) }}</h4>
                            <a href="{{ route('dealer.orders.show', $order->order_number) }}" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('dealer.invoice.download', $order->order_number) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <h4>No orders found</h4>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="{{ route('dealer.products') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i>Browse Products
                </a>
            </div>
        </div>
    @endif
</div>
@endsection


















