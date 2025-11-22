@extends('admin.layout')

@section('title', 'Sales Report - Nexus Agriculture Admin')
@section('page-title', 'Sales Report')

@section('content')
<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-primary">
            <div class="card-body">
                <div class="stat-number">{{ $summary['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-success">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($summary['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-info">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($summary['average_order_value'], 2) }}</div>
                <div class="stat-label">Average Order Value</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-warning">
            <div class="card-body">
                <div class="stat-number">{{ $summary['customer_orders'] }} / {{ $summary['dealer_orders'] }}</div>
                <div class="stat-label">Customer / Dealer Orders</div>
            </div>
        </div>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="both" {{ $type == 'both' ? 'selected' : '' }}>Both (Customer & Dealer)</option>
                    <option value="customer" {{ $type == 'customer' ? 'selected' : '' }}>Customer Only</option>
                    <option value="dealer" {{ $type == 'dealer' ? 'selected' : '' }}>Dealer Only</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Sales Orders 
            @if($type === 'customer')
                <span class="badge bg-info ms-2">Customer Only</span>
            @elseif($type === 'dealer')
                <span class="badge bg-success ms-2">Dealer Only</span>
            @else
                <span class="badge bg-primary ms-2">All (Customer & Dealer)</span>
            @endif
        </h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->user->name ?? $order->customer_name }}</td>
                            <td>
                                <span class="badge bg-{{ $order->user && $order->user->isDealer() ? 'success' : 'primary' }}">
                                    {{ $order->user && $order->user->isDealer() ? 'Dealer' : 'Customer' }}
                                </span>
                            </td>
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
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $orders->links() }}
    </div>
</div>
@endsection


















