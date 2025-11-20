@extends('admin.layout')

@section('title', 'Edit Offer')
@section('page-title', 'Edit Offer')

@section('content')
{{-- Session messages and validation errors are now handled by SweetAlert2 in the layout --}}

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Offer Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $offer->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $offer->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="banner_image" class="form-label">Banner Image</label>
                        <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                               id="banner_image" name="banner_image" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @error('banner_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Recommended: 1200x400px, Max 2MB</small>
                        @if($offer->banner_image)
                            @php
                                $bannerUrl = \App\Helpers\ImageHelper::imageUrl($offer->banner_image);
                            @endphp
                            <div class="mt-2">
                                <img src="{{ $bannerUrl }}" alt="Current Banner" class="border rounded" style="max-width: 300px; max-height: 150px;">
                                <br><small class="text-muted">Current banner image</small>
                            </div>
                        @endif
                        <div id="banner_preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="offer_type" class="form-label">Offer Type *</label>
                        <select class="form-select @error('offer_type') is-invalid @enderror" 
                                id="offer_type" name="offer_type" required>
                            <option value="general" {{ old('offer_type', $offer->offer_type) == 'general' ? 'selected' : '' }}>General (All Products)</option>
                            <option value="product" {{ old('offer_type', $offer->offer_type) == 'product' ? 'selected' : '' }}>Specific Product</option>
                            <option value="category" {{ old('offer_type', $offer->offer_type) == 'category' ? 'selected' : '' }}>Category</option>
                            <option value="subcategory" {{ old('offer_type', $offer->offer_type) == 'subcategory' ? 'selected' : '' }}>Subcategory</option>
                        </select>
                        @error('offer_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="product_select" style="display: none;">
                        <label for="product_id" class="form-label">Product *</label>
                        <select class="form-select @error('product_id') is-invalid @enderror" 
                                id="product_id" name="product_id">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $offer->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="category_select" style="display: none;">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $offer->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="subcategory_select" style="display: none;">
                        <label for="subcategory_id" class="form-label">Subcategory *</label>
                        <select class="form-select @error('subcategory_id') is-invalid @enderror" 
                                id="subcategory_id" name="subcategory_id">
                            <option value="">Select Subcategory</option>
                            @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $offer->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('subcategory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_type" class="form-label">Discount Type *</label>
                                <select class="form-select @error('discount_type') is-invalid @enderror" 
                                        id="discount_type" name="discount_type" required>
                                    <option value="percentage" {{ old('discount_type', $offer->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('discount_type', $offer->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_value" class="form-label">Discount Value *</label>
                                <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" 
                                       id="discount_value" name="discount_value" value="{{ old('discount_value', $offer->discount_value) }}" required>
                                @error('discount_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="discount_hint">Enter percentage (e.g., 10 for 10%)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_purchase_amount" class="form-label">Minimum Purchase Amount (₹)</label>
                                <input type="number" step="0.01" class="form-control @error('min_purchase_amount') is-invalid @enderror" 
                                       id="min_purchase_amount" name="min_purchase_amount" value="{{ old('min_purchase_amount', $offer->min_purchase_amount) }}">
                                @error('min_purchase_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_quantity" class="form-label">Minimum Quantity</label>
                                <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" 
                                       id="min_quantity" name="min_quantity" value="{{ old('min_quantity', $offer->min_quantity) }}">
                                @error('min_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $offer->start_date->format('Y-m-d\TH:i')) }}" 
                                       placeholder="YYYY-MM-DD HH:MM" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YYYY-MM-DD HH:MM</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date *</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $offer->end_date->format('Y-m-d\TH:i')) }}" 
                                       placeholder="YYYY-MM-DD HH:MM" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses" class="form-label">Maximum Uses (Total)</label>
                                <input type="number" class="form-control @error('max_uses') is-invalid @enderror" 
                                       id="max_uses" name="max_uses" value="{{ old('max_uses', $offer->max_uses) }}">
                                @error('max_uses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty for unlimited</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses_per_user" class="form-label">Maximum Uses Per User</label>
                                <input type="number" class="form-control @error('max_uses_per_user') is-invalid @enderror" 
                                       id="max_uses_per_user" name="max_uses_per_user" value="{{ old('max_uses_per_user', $offer->max_uses_per_user) }}">
                                @error('max_uses_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty for unlimited</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $offer->sort_order) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                       id="priority" name="priority" value="{{ old('priority', $offer->priority) }}">
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Higher priority offers apply first</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                        <textarea class="form-control @error('terms_conditions') is-invalid @enderror" 
                                  id="terms_conditions" name="terms_conditions" rows="3">{{ old('terms_conditions', $offer->terms_conditions) }}</textarea>
                        @error('terms_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $offer->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="for_customers" name="for_customers" value="1" {{ old('for_customers', $offer->for_customers) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="for_customers">For Customers</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="for_dealers" name="for_dealers" value="1" {{ old('for_dealers', $offer->for_dealers) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="for_dealers">For Dealers</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Offer
                        </button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show/hide related entity selects based on offer type
document.getElementById('offer_type').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('product_select').style.display = type === 'product' ? 'block' : 'none';
    document.getElementById('category_select').style.display = type === 'category' ? 'block' : 'none';
    document.getElementById('subcategory_select').style.display = type === 'subcategory' ? 'block' : 'none';
    
    // Clear values when hidden
    if (type !== 'product') document.getElementById('product_id').value = '';
    if (type !== 'category') document.getElementById('category_id').value = '';
    if (type !== 'subcategory') document.getElementById('subcategory_id').value = '';
});

// Update discount hint based on discount type
document.getElementById('discount_type').addEventListener('change', function() {
    const hint = document.getElementById('discount_hint');
    if (this.value === 'percentage') {
        hint.textContent = 'Enter percentage (e.g., 10 for 10%)';
    } else {
        hint.textContent = 'Enter fixed amount in ₹ (e.g., 100 for ₹100)';
    }
});

// Banner image preview
document.getElementById('banner_image').addEventListener('change', function(e) {
    const preview = document.getElementById('banner_preview');
    preview.innerHTML = '';
    
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '300px';
            img.style.maxHeight = '150px';
            img.className = 'border rounded mt-2';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});

// Trigger on page load
if (document.getElementById('offer_type').value) {
    document.getElementById('offer_type').dispatchEvent(new Event('change'));
}
document.getElementById('discount_type').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

