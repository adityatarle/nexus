@extends('admin.layout')

@section('title', 'Dealer Report - Nexus Agriculture Admin')
@section('page-title', 'Dealer Report')

@section('content')
<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-primary">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_dealers'] }}</div>
                <div class="stat-label">Total Dealers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-success">
            <div class="card-body">
                <div class="stat-number">{{ $stats['approved_dealers'] }}</div>
                <div class="stat-label">Approved Dealers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-warning">
            <div class="card-body">
                <div class="stat-number">{{ $stats['pending_dealers'] }}</div>
                <div class="stat-label">Pending Approval</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-info">
            <div class="card-body">
                <div class="stat-number">{{ $stats['active_dealers'] }}</div>
                <div class="stat-label">Active Dealers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-secondary">
            <div class="card-body">
                <div class="stat-number">{{ $stats['new_this_month'] }}</div>
                <div class="stat-label">New This Month</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-left-danger">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($stats['average_lifetime_value'], 2) }}</div>
                <div class="stat-label">Avg Lifetime Value</div>
            </div>
        </div>
    </div>
</div>

<!-- Sort Options -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.reports.dealers') }}" method="GET" class="row g-3">
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

<!-- Dealers Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Dealer Analysis</h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Dealer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Business Name</th>
                        <th>Status</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Avg Order Value</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dealers as $dealer)
                        <tr>
                            <td>{{ $dealer->name }}</td>
                            <td>{{ $dealer->email }}</td>
                            <td>{{ $dealer->phone ?? 'N/A' }}</td>
                            <td>{{ $dealer->business_name ?? 'N/A' }}</td>
                            <td>
                                @if($dealer->is_dealer_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $dealer->agriculture_orders_count }}</span>
                            </td>
                            <td>₹{{ number_format($dealer->agriculture_orders_sum_total_amount ?? 0, 2) }}</td>
                            <td>
                                ₹{{ number_format(($dealer->agriculture_orders_count > 0 ? $dealer->agriculture_orders_sum_total_amount / $dealer->agriculture_orders_count : 0), 2) }}
                            </td>
                            <td>{{ $dealer->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.dealers.profile', $dealer->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No dealers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $dealers->links() }}
    </div>
</div>
@endsection



