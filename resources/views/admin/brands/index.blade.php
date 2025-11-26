@extends('admin.layout')

@section('title', 'Brands Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Brands Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Brand
                        </a>
                    </div>
                </div>
                <div class="card-body">
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

                    @if($brands->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Logo</th>
                                        <th>Description</th>
                                        <th>Website</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brands as $brand)
                                    <tr>
                                        <td>
                                            <strong>{{ $brand->name }}</strong>
                                        </td>
                                        <td>
                                            @if($brand->logo)
                                                <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" style="max-width: 50px; max-height: 50px;">
                                            @else
                                                <span class="text-muted">No logo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ Str::limit($brand->description, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($brand->website)
                                                <a href="{{ $brand->website }}" target="_blank" class="text-primary">
                                                    <i class="fas fa-external-link-alt"></i> Visit
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $brand->products_count }} products</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                                {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $brand->sort_order }}</td>
                                        <td>{{ $brand->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline delete-form" data-name="{{ $brand->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger delete-btn" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $brands->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Brands Found</h4>
                            <p class="text-muted">Create your first brand to organize your agriculture products.</p>
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Brand
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle delete confirmations with SweetAlert
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const brandName = form.getAttribute('data-name') || 'this brand';
            
            Swal.fire({
                title: 'Delete Brand?',
                html: `Are you sure you want to delete <strong>${brandName}</strong>?<br><small class="text-danger">This action cannot be undone!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection

