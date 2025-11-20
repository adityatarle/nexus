@extends('admin.layout')

@section('title', 'Orders Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Orders & Inquiries Management</h3>
                    <div class="card-tools">
                        <span class="badge bg-primary">{{ $orders->total() }} Total</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="mb-3">
                        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="inquiry" {{ request('status') == 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="payment_status" class="form-select">
                                    <option value="">All Payment Status</option>
                                    <option value="not_required" {{ request('payment_status') == 'not_required' ? 'selected' : '' }}>Not Required</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>#{{ $order->order_number }}</strong>
                                        </td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->customer_email }}</td>
                                        <td>
                                            <span class="text-success font-weight-bold">
                                                {{ $currencySymbol ?? '₹' }}{{ number_format($order->total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'inquiry' => 'info',
                                                    'pending' => 'warning',
                                                    'processing' => 'primary',
                                                    'shipped' => 'info',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$order->order_status ?? 'pending'] ?? 'secondary';
                                                $orderStatus = $order->order_status ?? 'pending';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                @if($orderStatus === 'inquiry')
                                                    <i class="fas fa-question-circle me-1"></i>Inquiry
                                                @else
                                                    {{ ucfirst($orderStatus) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $paymentColors = [
                                                    'not_required' => 'secondary',
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'failed' => 'danger',
                                                    'refunded' => 'info'
                                                ];
                                                $paymentColor = $paymentColors[$order->payment_status ?? 'pending'] ?? 'secondary';
                                                $paymentStatus = $order->payment_status ?? 'pending';
                                            @endphp
                                            <span class="badge bg-{{ $paymentColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Orders/Inquiries Found</h4>
                            <p class="text-muted">Orders and inquiries will appear here once customers submit them.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
