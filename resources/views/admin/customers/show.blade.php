@extends('admin.layout')

@section('title', 'Customer Details - Nexus Agriculture Admin')
@section('page-title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-circle bg-primary text-white mb-2" style="width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong>
                    <p class="text-muted">{{ $user->name }}</p>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong>
                    <p class="text-muted">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div class="mb-2">
                    <strong>Joined:</strong>
                    <p class="text-muted">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Total Orders:</strong>
                    <h4 class="text-primary">{{ $stats['total_orders'] }}</h4>
                </div>
                <div class="mb-3">
                    <strong>Total Spent:</strong>
                    <h4 class="text-success">₹{{ number_format($stats['total_spent'], 2) }}</h4>
                </div>
                <div class="mb-3">
                    <strong>Average Order Value:</strong>
                    <h4 class="text-info">₹{{ number_format($stats['average_order_value'], 2) }}</h4>
                </div>
                @if($stats['last_order_date'])
                    <div class="mb-0">
                        <strong>Last Order:</strong>
                        <p class="text-muted">{{ $stats['last_order_date']->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order History</h5>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
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
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No orders yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection







