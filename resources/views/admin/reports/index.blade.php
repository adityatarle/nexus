@extends('admin.layout')

@section('title', 'Reports & Analytics - Nexus Agriculture Admin')
@section('page-title', 'Reports & Analytics')

@section('content')
<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-md-2.4 mb-3">
        <div class="card stat-card border-left-primary h-100">
            <div class="card-body">
                <div class="stat-number">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-md-2.4 mb-3">
        <div class="card stat-card border-left-success h-100">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-md-2.4 mb-3">
        <div class="card stat-card border-left-info h-100">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_products'] }}</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-2.4 mb-3">
        <div class="card stat-card border-left-warning h-100">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_customers'] }}</div>
                <div class="stat-label">Total Customers</div>
            </div>
        </div>
    </div>
    <div class="col-md-2.4 mb-3">
        <div class="card stat-card border-left-danger h-100">
            <div class="card-body">
                <div class="stat-number">{{ $stats['total_dealers'] }}</div>
                <div class="stat-label">Approved Dealers</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Report Links -->
<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Sales Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                                <h5>All Sales</h5>
                                <p class="text-muted">Customer & Dealer combined</p>
                                <a href="{{ route('admin.reports.sales', ['type' => 'both']) }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-info h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user fa-3x text-info mb-3"></i>
                                <h5>Customer Sales</h5>
                                <p class="text-muted">Customer-only sales report</p>
                                <a href="{{ route('admin.reports.sales', ['type' => 'customer']) }}" class="btn btn-info">
                                    <i class="fas fa-arrow-right me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-success h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tie fa-3x text-success mb-3"></i>
                                <h5>Dealer Sales</h5>
                                <p class="text-muted">Dealer-only sales report</p>
                                <a href="{{ route('admin.reports.sales', ['type' => 'dealer']) }}" class="btn btn-success">
                                    <i class="fas fa-arrow-right me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-boxes fa-3x text-warning mb-3"></i>
                <h5>Inventory Report</h5>
                <p class="text-muted">Stock levels and low stock alerts</p>
                <a href="{{ route('admin.reports.inventory') }}" class="btn btn-warning">
                    <i class="fas fa-arrow-right me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h5>Customer Report</h5>
                <p class="text-muted">Customer insights and analysis</p>
                <a href="{{ route('admin.reports.customers') }}" class="btn btn-info">
                    <i class="fas fa-arrow-right me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-tie fa-3x text-success mb-3"></i>
                <h5>Dealer Report</h5>
                <p class="text-muted">Dealer insights and analysis</p>
                <a href="{{ route('admin.reports.dealers') }}" class="btn btn-success">
                    <i class="fas fa-arrow-right me-2"></i>View Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Trends Chart -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Revenue Trends (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendsChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Selling Products -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Selling Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product</th>
                                <th>Sales</th>
                                <th>Qty Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ Str::limit($product->name, 30) }}</td>
                                    <td>{{ $product->order_items_count }}</td>
                                    <td>{{ $product->order_items_sum_quantity ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Category -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Sales by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Trends Chart
const revenueTrendsCtx = document.getElementById('revenueTrendsChart').getContext('2d');
const revenueTrendsChart = new Chart(revenueTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($revenueTrends, 'month')) !!},
        datasets: [{
            label: 'Revenue (₹)',
            data: {!! json_encode(array_column($revenueTrends, 'revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryStats->pluck('name')) !!},
        datasets: [{
            label: 'Sales',
            data: {!! json_encode($categoryStats->pluck('total_sales')) !!},
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush


















