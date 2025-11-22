@extends('admin.layout')

@section('title', 'Dealer Profile - Nexus Agriculture Admin')
@section('page-title', 'Dealer Profile')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Dealer Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-circle bg-primary text-white mb-2" style="width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="mb-2">
                    <strong>User ID:</strong>
                    <p class="text-muted"><code>{{ $user->id }}</code></p>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong>
                    <p class="text-muted">{{ $user->name }}</p>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong>
                    <p class="text-muted">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div class="mb-2">
                    <strong>Joined:</strong>
                    <p class="text-muted">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                @if($user->dealerRegistration)
                    <div class="mb-2">
                        <strong>Business Name:</strong>
                        <p class="text-muted">{{ $user->dealerRegistration->business_name }}</p>
                    </div>
                    <div class="mb-2">
                        <strong>GST Number:</strong>
                        <p class="text-muted"><code>{{ $user->dealerRegistration->gst_number }}</code></p>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        <p class="text-muted">
                            @if($user->is_dealer_approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning">Pending Approval</span>
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Account Management Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Account Management</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Password:</strong>
                    @if($user->viewable_password)
                        <div class="input-group mt-2">
                            <input type="text" class="form-control" id="password_{{ $user->id }}" 
                                   value="{{ $user->viewable_password }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" 
                                    onclick="copyPassword('password_{{ $user->id }}')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    @else
                        <p class="text-muted mt-2">No password stored</p>
                    @endif
                </div>
                <button type="button" class="btn btn-warning w-100 mb-2" onclick="resetPassword({{ $user->id }})">
                    <i class="fas fa-key me-2"></i>Reset/Set Password
                </button>
            </div>
        </div>

        <!-- Dealer Actions Card -->
        @if($user->isDealer())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Dealer Actions</h5>
            </div>
            <div class="card-body">
                @if($user->is_dealer_approved)
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Dealer Status:</strong> Approved
                        @if($user->dealer_approved_at)
                            <br><small>Approved on: {{ $user->dealer_approved_at->format('M d, Y') }}</small>
                        @endif
                    </div>
                    <button type="button" class="btn btn-danger w-100" onclick="revokeDealerStatus({{ $user->id }})">
                        <i class="fas fa-times-circle me-2"></i>Disapprove/Revoke Dealer Status
                    </button>
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Dealer Status:</strong> Not Approved
                    </div>
                    @if($user->dealerRegistration && $user->dealerRegistration->status === 'pending')
                        <a href="{{ route('admin.dealers.show', $user->dealerRegistration) }}" class="btn btn-primary w-100">
                            <i class="fas fa-eye me-2"></i>Review Registration
                        </a>
                    @elseif($user->dealerRegistration && $user->dealerRegistration->status === 'rejected')
                        <button type="button" class="btn btn-success w-100" onclick="restoreDealerStatus({{ $user->id }})">
                            <i class="fas fa-check me-2"></i>Restore Dealer Status
                        </button>
                    @endif
                @endif
            </div>
        </div>
        @endif

        <!-- Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Total Orders:</strong>
                    <h4 class="text-primary">{{ $stats['total_orders'] }}</h4>
                </div>
                <div class="mb-3">
                    <strong>Total Spent:</strong>
                    <h4 class="text-success">{{ $currencySymbol ?? '₹' }}{{ number_format($stats['total_spent'], 2) }}</h4>
                </div>
                <div class="mb-3">
                    <strong>Average Order Value:</strong>
                    <h4 class="text-info">{{ $currencySymbol ?? '₹' }}{{ number_format($stats['average_order_value'], 2) }}</h4>
                </div>
                @if($stats['last_order_date'])
                    <div class="mb-0">
                        <strong>Last Order:</strong>
                        <p class="text-muted">{{ $stats['last_order_date']->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order History</h5>
                <a href="{{ route('admin.dealers.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dealers
                </a>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td>{{ $currencySymbol ?? '₹' }}{{ number_format($order->total_amount, 2) }}</td>
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
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No orders yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Revoke Dealer Status Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Revoke Dealer Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revokeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to revoke dealer status for <strong>{{ $user->name }}</strong>?</p>
                    <div class="mb-3">
                        <label for="revocation_reason" class="form-label">Reason for Revocation *</label>
                        <textarea name="revocation_reason" id="revocation_reason" class="form-control" rows="3" 
                                  placeholder="Please explain why the dealer status is being revoked..." required></textarea>
                        <small class="text-muted">This reason will be sent to the dealer.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Revoke Dealer Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Dealer Status Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Dealer Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="restoreForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to restore dealer status for <strong>{{ $user->name }}</strong>?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will restore the dealer's access to dealer pricing and features.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Restore Dealer Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Reset password for <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password *</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" 
                               placeholder="Enter new password" required minlength="8">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm Password *</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                               class="form-control" placeholder="Confirm new password" required minlength="8">
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will immediately change the user's password. They will need to use the new password to log in.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetPassword(userId) {
    const form = document.getElementById('resetPasswordForm');
    form.action = `/admin/dealers/${userId}/reset-password`;
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

function revokeDealerStatus(userId) {
    const form = document.getElementById('revokeForm');
    form.action = `/admin/dealers/${userId}/revoke`;
    const modal = new bootstrap.Modal(document.getElementById('revokeModal'));
    modal.show();
}

function restoreDealerStatus(userId) {
    const form = document.getElementById('restoreForm');
    form.action = `/admin/dealers/${userId}/restore`;
    const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

function copyPassword(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(input.value).then(function() {
        // Show success message
        const btn = input.nextElementSibling;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    });
}
</script>
@endpush
@endsection






