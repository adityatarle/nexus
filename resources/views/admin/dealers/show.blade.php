@extends('admin.layout')

@section('title', 'Dealer Registration Details - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dealer Registration Details</h1>
            <p class="text-muted">{{ $dealerRegistration->business_name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dealers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Registration Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Business Name:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->business_name }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Business Type:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->business_type }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>GST Number:</strong><br>
                            <code>{{ $dealerRegistration->gst_number }}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>PAN Number:</strong><br>
                            <code>{{ $dealerRegistration->pan_number }}</code>
                        </div>
                        <div class="col-12">
                            <strong>Business Address:</strong><br>
                            <span class="text-muted">
                                {{ $dealerRegistration->business_address }},<br>
                                {{ $dealerRegistration->business_city }}, {{ $dealerRegistration->business_state }} - {{ $dealerRegistration->business_pincode }},<br>
                                {{ $dealerRegistration->business_country }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Years in Business:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->years_in_business ?? 'Not specified' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Annual Turnover:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->annual_turnover ?? 'Not specified' }}</span>
                        </div>
                        <div class="col-12">
                            <strong>Business Description:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->business_description }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Contact Person:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->contact_person }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Contact Email:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->contact_email }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Contact Phone:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->contact_phone }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Alternate Phone:</strong><br>
                            <span class="text-muted">{{ $dealerRegistration->alternate_phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="col-12">
                            <strong>Company Website:</strong><br>
                            @if($dealerRegistration->company_website)
                                <a href="{{ $dealerRegistration->company_website }}" target="_blank" class="text-primary">
                                    {{ $dealerRegistration->company_website }}
                                </a>
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            @if($dealerRegistration->business_documents && count($dealerRegistration->business_documents) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Business Documents</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($dealerRegistration->business_documents as $type => $path)
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                    <h6 class="card-title">{{ ucfirst(str_replace('_', ' ', $type)) }}</h6>
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i> View Document
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Registration Status</h6>
                </div>
                <div class="card-body text-center">
                    @if($dealerRegistration->status === 'pending')
                        <div class="mb-3">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <h5 class="text-warning">Pending Review</h5>
                        <p class="text-muted">Submitted on {{ $dealerRegistration->created_at->format('M d, Y') }}</p>
                    @elseif($dealerRegistration->status === 'approved')
                        <div class="mb-3">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        </div>
                        <h5 class="text-success">Approved</h5>
                        <p class="text-muted">Approved on {{ $dealerRegistration->reviewed_at->format('M d, Y') }}</p>
                        @if($dealerRegistration->reviewer)
                            <small class="text-muted">By: {{ $dealerRegistration->reviewer->name }}</small>
                        @endif
                    @else
                        <div class="mb-3">
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <h5 class="text-danger">Rejected</h5>
                        <p class="text-muted">Rejected on {{ $dealerRegistration->reviewed_at->format('M d, Y') }}</p>
                        @if($dealerRegistration->reviewer)
                            <small class="text-muted">By: {{ $dealerRegistration->reviewer->name }}</small>
                        @endif
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Account</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>User ID:</strong><br>
                        <span class="text-muted"><code>{{ $dealerRegistration->user->id }}</code></span>
                    </div>
                    <div class="mb-3">
                        <strong>Account Name:</strong><br>
                        <span class="text-muted">{{ $dealerRegistration->user->name }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-muted">{{ $dealerRegistration->user->email }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <span class="text-muted">{{ $dealerRegistration->user->phone }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Account Created:</strong><br>
                        <span class="text-muted">{{ $dealerRegistration->user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Password:</strong>
                        @if($dealerRegistration->user->viewable_password)
                            <div class="input-group mt-2">
                                <input type="text" class="form-control" id="password_{{ $dealerRegistration->user->id }}" 
                                       value="{{ $dealerRegistration->user->viewable_password }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="copyPassword('password_{{ $dealerRegistration->user->id }}')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        @else
                            <p class="text-muted mt-2">No password stored</p>
                        @endif
                    </div>
                    @if($dealerRegistration->user && $dealerRegistration->user->id)
                    <div class="d-grid">
                        <a href="{{ route('admin.dealers.profile', $dealerRegistration->user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <small>User account not found</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Admin Actions -->
            @if($dealerRegistration->status === 'pending')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="approveRegistration({{ $dealerRegistration->id }})">
                            <i class="fas fa-check"></i> Approve Registration
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectRegistration({{ $dealerRegistration->id }})">
                            <i class="fas fa-times"></i> Reject Registration
                        </button>
                    </div>
                </div>
            </div>
            @elseif($dealerRegistration->status === 'approved' && $dealerRegistration->user && $dealerRegistration->user->is_dealer_approved)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-danger" onclick="revokeDealerStatus({{ $dealerRegistration->user->id }})">
                            <i class="fas fa-times-circle"></i> Disapprove/Revoke Dealer Status
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        This will revoke the dealer's approved status and remove access to dealer pricing.
                    </small>
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            @if($dealerRegistration->admin_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Admin Notes</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $dealerRegistration->admin_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Rejection Reason -->
            @if($dealerRegistration->rejection_reason)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Rejection Reason</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $dealerRegistration->rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Dealer Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve <strong>{{ $dealerRegistration->business_name }}</strong> as a dealer?</p>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" 
                                  placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Dealer Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Please provide a reason for rejecting <strong>{{ $dealerRegistration->business_name }}</strong>:</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" 
                                  placeholder="Please explain why this registration is being rejected..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" id="admin_notes" class="form-control" rows="2" 
                                  placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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
                    <p>Are you sure you want to revoke dealer status for <strong>{{ $dealerRegistration->business_name }}</strong>?</p>
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

@push('scripts')
<script>
function approveRegistration(registrationId) {
    const form = document.getElementById('approvalForm');
    form.action = `/admin/dealers/${registrationId}/approve`;
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}

function rejectRegistration(registrationId) {
    const form = document.getElementById('rejectionForm');
    form.action = `/admin/dealers/${registrationId}/reject`;
    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    modal.show();
}

function revokeDealerStatus(userId) {
    const form = document.getElementById('revokeForm');
    form.action = `/admin/dealers/${userId}/revoke`;
    const modal = new bootstrap.Modal(document.getElementById('revokeModal'));
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


















