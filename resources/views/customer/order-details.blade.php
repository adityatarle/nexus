@extends('layouts.app')

@section('title', 'Order Details - Nexus Agriculture')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-primary text-white mb-2" style="width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->email }}</small>
                    </div>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('customer.orders') }}">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.profile') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.notifications') }}">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('auth.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order Details</h2>
                <a href="{{ route('customer.orders') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
            </div>

            <!-- Order Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                            <small>Placed on {{ $order->created_at->format('F d, Y h:i A') }}</small>
                        </div>
                        <div class="col-md-6 text-end">
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
                            <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($order->order_status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Tracking -->
                    <div class="mb-4">
                        <h6 class="mb-3">Order Tracking</h6>
                        <div class="progress" style="height: 30px;">
                            @php
                                $progress = 0;
                                switch($order->order_status) {
                                    case 'pending': $progress = 20; break;
                                    case 'confirmed': $progress = 40; break;
                                    case 'shipped': $progress = 70; break;
                                    case 'delivered': $progress = 100; break;
                                    case 'cancelled': $progress = 0; break;
                                }
                            @endphp
                            <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $progress }}%">
                                {{ $progress }}%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-{{ $order->order_status == 'pending' || $order->order_status == 'confirmed' || $order->order_status == 'shipped' || $order->order_status == 'delivered' ? 'primary' : 'muted' }}">Pending</small>
                            <small class="text-{{ $order->order_status == 'confirmed' || $order->order_status == 'shipped' || $order->order_status == 'delivered' ? 'primary' : 'muted' }}">Confirmed</small>
                            <small class="text-{{ $order->order_status == 'shipped' || $order->order_status == 'delivered' ? 'primary' : 'muted' }}">Shipped</small>
                            <small class="text-{{ $order->order_status == 'delivered' ? 'success' : 'muted' }}">Delivered</small>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h6 class="mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Original Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                    $originalPrice = $item->original_price ?? $item->price;
                                    $discountAmount = $item->discount_amount ?? 0;
                                    $hasDiscount = $discountAmount > 0;
                                    $offerDetails = $item->offer_details ?? null;
                                @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name ?? $item->product_name ?? 'Product N/A' }}</strong>
                                            @if($offerDetails)
                                                <br><small class="text-success">
                                                    <i class="fas fa-tag me-1"></i>{{ $offerDetails['title'] ?? 'Offer Applied' }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $item->product->sku ?? $item->product_sku ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            @if($hasDiscount)
                                                <span class="text-muted text-decoration-line-through">₹{{ number_format($originalPrice, 2) }}</span>
                                            @else
                                                ₹{{ number_format($originalPrice, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($hasDiscount)
                                                <span class="text-danger">
                                                    -₹{{ number_format($discountAmount, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end"><strong>₹{{ number_format($item->price, 2) }}</strong></td>
                                        <td class="text-end"><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $totalDiscount = $order->items->sum('discount_amount') ?? 0;
                                    $originalSubtotal = $order->items->sum(function($item) {
                                        $originalPrice = $item->original_price ?? $item->price;
                                        return $originalPrice * $item->quantity;
                                    });
                                @endphp
                                @if($totalDiscount > 0)
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Subtotal (Original):</strong></td>
                                    <td class="text-end">
                                        <span class="text-muted text-decoration-line-through">₹{{ number_format($originalSubtotal, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end text-danger"><strong>Discount:</strong></td>
                                    <td class="text-end text-danger">
                                        <strong>-₹{{ number_format($totalDiscount, 2) }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end">₹{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->tax_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Tax:</strong></td>
                                        <td class="text-end">₹{{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($order->shipping_amount > 0)
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Shipping:</strong></td>
                                        <td class="text-end">₹{{ number_format($order->shipping_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="6" class="text-end"><strong>Total Amount:</strong></td>
                                    <td class="text-end"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Address & Payment Info -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h6>
                        </div>
                        <div class="card-body">
                            @if($order->shipping_address)
                                <p class="mb-1"><strong>{{ $order->shipping_address['name'] ?? 'N/A' }}</strong></p>
                                <p class="mb-1">{{ $order->shipping_address['address'] ?? 'N/A' }}</p>
                                <p class="mb-1">{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['pincode'] ?? '' }}</p>
                                <p class="mb-0">Phone: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}</p>
                            @else
                                <p class="text-muted">No shipping address provided</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
                            <p class="mb-2">
                                <strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                            @if($order->notes)
                                <p class="mb-0"><strong>Notes:</strong> {{ $order->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


















