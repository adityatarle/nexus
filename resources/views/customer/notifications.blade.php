@extends('layouts.app')

@section('title', 'Notifications - Nexus Agriculture')

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
                            <a class="nav-link" href="{{ route('customer.orders') }}">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.profile') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('customer.notifications') }}">
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
                <h2>Notifications</h2>
                @if($notifications->total() > 0)
                    <form action="{{ route('customer.notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double me-2"></i>Mark All as Read
                        </button>
                    </form>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($notifications->count() > 0)
                <div class="card">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ $notification->is_read ? '' : 'bg-light border-start border-primary border-4' }}">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            @php
                                                $iconClass = 'fa-bell';
                                                $iconColor = 'text-primary';
                                                switch($notification->type) {
                                                    case 'order_status':
                                                        $iconClass = 'fa-shopping-cart';
                                                        $iconColor = 'text-success';
                                                        break;
                                                    case 'dealer_approval':
                                                        $iconClass = 'fa-check-circle';
                                                        $iconColor = 'text-success';
                                                        break;
                                                    case 'dealer_rejection':
                                                        $iconClass = 'fa-times-circle';
                                                        $iconColor = 'text-danger';
                                                        break;
                                                    case 'low_stock':
                                                        $iconClass = 'fa-exclamation-triangle';
                                                        $iconColor = 'text-warning';
                                                        break;
                                                }
                                            @endphp
                                            <i class="fas {{ $iconClass }} {{ $iconColor }} me-2"></i>
                                            <h6 class="mb-0">{{ $notification->title }}</h6>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary ms-2">New</span>
                                            @endif
                                        </div>
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="ms-3">
                                        @if(!$notification->is_read)
                                            <form action="{{ route('customer.notifications.mark-read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h4>No notifications</h4>
                        <p class="text-muted">You don't have any notifications yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


















