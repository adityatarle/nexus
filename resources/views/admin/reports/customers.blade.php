@extends('admin.layout')

@section('title', 'Customer Report - Nexus Agriculture Admin')
@section('page-title', 'Customer Report')

@section('content')
<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-primary">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_customers'] }}</div>
                <div class="stat-label">Total Customers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-success">
            <div class="card-body">
                <div class="stat-number">{{ $stats['new_this_month'] }}</div>
                <div class="stat-label">New This Month</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-info">
            <div class="card-body">
                <div class="stat-number">{{ $stats['active_customers'] }}</div>
                <div class="stat-label">Active Customers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-warning">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($stats['average_lifetime_value'], 2) }}</div>
                <div class="stat-label">Average Lifetime Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Sort Options -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.customers') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                    <option value="orders" {{ request('sort') == 'orders' ? 'selected' : '' }}>Most Orders</option>
                    <option value="spent" {{ request('sort') == 'spent' ? 'selected' : '' }}>Highest Spender</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sort me-2"></i>Apply Sort
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customer Analysis</h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Avg Order Value</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->agriculture_orders_count }}</span>
                            </td>
                            <td>₹{{ number_format($customer->agriculture_orders_sum_total_amount ?? 0, 2) }}</td>
                            <td>
                                ₹{{ number_format(($customer->agriculture_orders_count > 0 ? $customer->agriculture_orders_sum_total_amount / $customer->agriculture_orders_count : 0), 2) }}
                            </td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No customers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $customers->links() }}
    </div>
</div>
@endsection


















