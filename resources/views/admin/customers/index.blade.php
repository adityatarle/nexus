@extends('admin.layout')

@section('title', 'Customer Management - Green Leaf Agriculture Admin')
@section('page-title', 'Customer Management')

@section('content')
<!-- Statistics Cards -->
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
                <div class="stat-number">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-warning">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
</div>

<!-- Customers List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Customers</h5>
        <!-- Search Form -->
        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->agriculture_orders_count ?? 0 }}</span>
                            </td>
                            <td>₹{{ number_format($customer->agricultureOrders->sum('total_amount'), 2) }}</td>
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


















