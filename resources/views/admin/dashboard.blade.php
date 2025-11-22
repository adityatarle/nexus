@extends('admin.layout')

@section('title', 'Admin Dashboard - Nexus Agriculture')
@section('page-title', 'Dashboard Overview')

@push('styles')
<style>
.clickable-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.clickable-card a {
    color: inherit;
    text-decoration: none;
}
</style>
@endpush

@section('content')
<!-- Main Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
            <div class="card stat-card border-left-primary clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['total_products'] }}</div>
                            <div class="stat-label">Total Products</div>
                            <small class="text-muted">{{ $stats['active_products'] }} active</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tractor fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
            <div class="card stat-card border-left-success clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['total_customers'] }}</div>
                            <div class="stat-label">Total Customers</div>
                            <small class="text-muted">Registered customers</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.dealers.index') }}" class="text-decoration-none">
            <div class="card stat-card border-left-info clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['total_dealers'] }}</div>
                            <div class="stat-label">Total Dealers</div>
                            <small class="text-muted">{{ $stats['approved_dealers'] }} approved</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
            <div class="card stat-card border-left-primary clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['total_orders'] }}</div>
                            <div class="stat-label">Total Orders</div>
                            <small class="text-muted">{{ $stats['pending_orders'] }} pending</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.reports.sales') }}" class="text-decoration-none">
            <div class="card stat-card border-left-warning clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $currencySymbol ?? '₹' }}{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                            <div class="stat-label">Total Revenue</div>
                            <small class="text-muted">{{ $currencySymbol ?? '₹' }}{{ number_format($stats['monthly_revenue'] ?? 0, 2) }} this month</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-rupee-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Secondary Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.dealers.index') }}?status=pending" class="text-decoration-none">
            <div class="card stat-card border-left-danger clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['pending_dealer_registrations'] }}</div>
                            <div class="stat-label">Pending Dealer Approvals</div>
                            <small class="text-muted">{{ $stats['approved_dealers'] }} approved</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.reports.inventory') }}" class="text-decoration-none">
            <div class="card stat-card border-left-warning clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['low_stock_products'] }}</div>
                            <div class="stat-label">Low Stock Alerts</div>
                            <small class="text-muted">{{ $stats['out_of_stock_products'] }} out of stock</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.orders.index') }}?status=confirmed" class="text-decoration-none">
            <div class="card stat-card border-left-info clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['confirmed_orders'] }}</div>
                            <div class="stat-label">Confirmed Orders</div>
                            <small class="text-muted">{{ $stats['shipped_orders'] }} shipped</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.orders.index') }}?status=delivered" class="text-decoration-none">
            <div class="card stat-card border-left-success clickable-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-number">{{ $stats['delivered_orders'] }}</div>
                            <div class="stat-label">Delivered Orders</div>
                            <small class="text-muted">{{ $stats['cancelled_orders'] }} cancelled</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Revenue Trends (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Orders by Status</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Management Sections -->
<div class="row mb-4">
    <!-- Recent Orders -->
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if($order->user && $order->user->isDealer())
                                        <span class="badge bg-success">Dealer</span>
                                    @else
                                        <span class="badge bg-primary">Customer</span>
                                    @endif
                                </td>
                                <td>{{ $currencySymbol ?? '₹' }}{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            'inquiry' => 'secondary'
                                        ];
                                        $status = $order->order_status ?? 'pending';
                                        $color = $statusColors[$status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Selling Products -->
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Top Selling Products</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">Manage</a>
            </div>
            <div class="card-body">
                @forelse($topSellingProducts as $product)
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        @php
                            $productImageUrl = \App\Helpers\ImageHelper::productImageUrl($product);
                        @endphp
                        <img src="{{ $productImageUrl }}" 
                             alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $product->name }}</h6>
                        <small class="text-muted">{{ $product->order_items_count ?? 0 }} orders</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">{{ $currencySymbol ?? '₹' }}{{ number_format($product->price ?? 0, 2) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No products found</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Dealer Management Section -->
@if($recentDealerRegistrations->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Dealer Registrations</h5>
                <a href="{{ route('admin.dealers.index') }}" class="btn btn-sm btn-primary">Manage Dealers</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Contact Person</th>
                                <th>GST Number</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDealerRegistrations as $registration)
                            <tr>
                                <td><strong>{{ $registration->business_name }}</strong></td>
                                <td>{{ $registration->contact_person }}</td>
                                <td><code>{{ $registration->gst_number }}</code></td>
                                <td>
                                    @if($registration->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($registration->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.dealers.show', $registration) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($registration->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="approveRegistration({{ $registration->id }})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Low Stock Alerts -->
@if($lowStockProducts->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($lowStockProducts->take(6) as $product)
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded">
                            <div class="me-3">
                                @php
                                    $productImageUrl = \App\Helpers\ImageHelper::productImageUrl($product);
                                @endphp
                                <img src="{{ $productImageUrl }}" 
                                     alt="{{ $product->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                            </div>
                            <div>
                                @if($product->stock_quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning">Low Stock</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($lowStockProducts->count() > 6)
                <div class="text-center mt-3">
                    <a href="{{ route('admin.products.index') }}?filter=low_stock" class="btn btn-warning">
                        View All Low Stock Products ({{ $lowStockProducts->count() }})
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Customers -->
<div class="row mb-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Customers</h5>
            </div>
            <div class="card-body">
                @forelse($recentCustomers as $customer)
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $customer->name }}</h6>
                        <small class="text-muted">{{ $customer->email }}</small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">{{ $customer->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No customers found</p>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Distribution</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($revenueByMonth ?? [], 'month')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_map(function($r) { return $r ?? 0; }, array_column($revenueByMonth ?? [], 'revenue'))) !!},
            borderColor: '#6BB252',
            backgroundColor: 'rgba(107, 178, 82, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '{{ $currencySymbol ?? '₹' }}' + value.toLocaleString();
                            }
                        }
                    }
                }
    }
});

// Orders Chart
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'],
        datasets: [{
            data: [
                {{ $ordersByStatus['pending'] }},
                {{ $ordersByStatus['confirmed'] }},
                {{ $ordersByStatus['shipped'] }},
                {{ $ordersByStatus['delivered'] }},
                {{ $ordersByStatus['cancelled'] }}
            ],
            backgroundColor: [
                '#ffc107',
                '#17a2b8',
                '#6f42c1',
                '#28a745',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($categoryStats->pluck('name') ?? []) !!},
        datasets: [{
            label: 'Products',
            data: {!! json_encode($categoryStats->pluck('products_count') ?? []) !!},
            backgroundColor: '#6BB252',
            borderColor: '#5a9e47',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function approveRegistration(registrationId) {
    Swal.fire({
        title: 'Approve Registration?',
        text: 'Are you sure you want to approve this dealer registration?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, approve it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/dealers/${registrationId}/approve`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush