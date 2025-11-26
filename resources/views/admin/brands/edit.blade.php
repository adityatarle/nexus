@extends('admin.layout')

@section('title', 'Edit Brand')
@section('page-title', 'Edit Brand')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Brand Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $brand->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $brand->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo URL</label>
                        <input type="text" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" value="{{ old('logo', $brand->logo) }}" placeholder="https://example.com/logo.png">
                        <small class="text-muted">Enter the URL of the brand logo image</small>
                        @if($brand->logo)
                            <div class="mt-2">
                                <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" style="max-width: 100px; max-height: 100px;" class="border rounded p-1">
                            </div>
                        @endif
                        @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">Website URL</label>
                        <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $brand->website) }}" placeholder="https://example.com">
                        @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $brand->sort_order) }}" min="0">
                                @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Brand
                        </button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

