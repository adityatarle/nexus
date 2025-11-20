@extends('layouts.app')

@section('title', 'Notifications - Dealer Dashboard')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Notifications</h2>
        @if($notifications->total() > 0)
            <form action="{{ route('dealer.notifications.mark-all-read') }}" method="POST">
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
                    <div class="list-group-item {{ $notification->is_read ? '' : 'bg-light border-start border-success border-4' }}">
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
                                            case 'dealer_revocation':
                                                $iconClass = 'fa-times-circle';
                                                $iconColor = 'text-danger';
                                                break;
                                            case 'dealer_restoration':
                                                $iconClass = 'fa-undo';
                                                $iconColor = 'text-info';
                                                break;
                                        }
                                    @endphp
                                    <i class="fas {{ $iconClass }} {{ $iconColor }} me-2"></i>
                                    <h6 class="mb-0">{{ $notification->title }}</h6>
                                    @if(!$notification->is_read)
                                        <span class="badge bg-success ms-2">New</span>
                                    @endif
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="ms-3">
                                @if(!$notification->is_read)
                                    <form action="{{ route('dealer.notifications.mark-read', $notification->id) }}" method="POST">
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
@endsection


















