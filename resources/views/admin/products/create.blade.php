@extends('admin.layout')

@section('title', 'Create Product')
@section('page-title', 'Create New Product')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="alert alert-primary mb-3">
                        <strong><i class="fas fa-image me-2"></i>Product Images</strong>
                    </div>

                    <div class="mb-3">
                        <label for="primary_image" class="form-label">Primary Image *</label>
                        <input type="file" class="form-control @error('primary_image') is-invalid @enderror" 
                               id="primary_image" name="primary_image" accept="image/*">
                        @error('primary_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Accepted: All image types | Max size: 2MB | Min dimensions: 400x400px
                        </small>
                        <div id="primary_image_preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="gallery_images" class="form-label">Gallery Images (Optional)</label>
                        <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                               id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                        @error('gallery_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            You can select up to 5 images | Each max 2MB
                        </small>
                        <div id="gallery_images_preview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                    </div>

                    <!-- Retail Pricing -->
                    <div class="alert alert-info mb-3">
                        <strong><i class="fas fa-info-circle me-2"></i>Retail Pricing</strong> - Prices for normal customers
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Retail Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Regular price for customers</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Retail Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" 
                                           id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Optional discounted price</small>
                            </div>
                        </div>
                    </div>

                    <!-- Dealer/Wholesale Pricing -->
                    <div class="alert alert-success mb-3">
                        <strong><i class="fas fa-user-tie me-2"></i>Dealer/Wholesale Pricing</strong> - Prices for approved dealers
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dealer_price" class="form-label">Dealer Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control @error('dealer_price') is-invalid @enderror" 
                                           id="dealer_price" name="dealer_price" value="{{ old('dealer_price') }}" required>
                                    @error('dealer_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Wholesale price for dealers</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dealer_sale_price" class="form-label">Dealer Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control @error('dealer_sale_price') is-invalid @enderror" 
                                           id="dealer_sale_price" name="dealer_sale_price" value="{{ old('dealer_sale_price') }}">
                                    @error('dealer_sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Optional dealer discount price</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-calculator me-2"></i>
                                    <strong>Price Calculation:</strong>
                                    <span id="price-info">Enter prices to see discount percentage</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="agriculture_category_id" class="form-label">Category *</label>
                                <select class="form-select @error('agriculture_category_id') is-invalid @enderror" 
                                        id="agriculture_category_id" name="agriculture_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('agriculture_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('agriculture_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="agriculture_subcategory_id" class="form-label">Subcategory (Optional)</label>
                                <select class="form-select @error('agriculture_subcategory_id') is-invalid @enderror" 
                                        id="agriculture_subcategory_id" name="agriculture_subcategory_id">
                                    <option value="">Select Subcategory</option>
                                    @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" 
                                            data-category-id="{{ $subcategory->agriculture_category_id }}"
                                            {{ old('agriculture_subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }} ({{ $subcategory->category->name ?? '' }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('agriculture_subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select a category first to filter subcategories</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand') }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                  id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="in_stock" name="in_stock" value="1" {{ old('in_stock', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_stock">In Stock</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="manage_stock" name="manage_stock" value="1" {{ old('manage_stock', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manage_stock">Manage Stock</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Use descriptive product names
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        SKU must be unique
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-image text-primary me-2"></i>
                        <strong>Upload product images (Required)</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Set BOTH retail and dealer prices</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Dealer price should be lower than retail
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Manage stock levels
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Add detailed descriptions
                    </li>
                </ul>
                
                <div class="alert alert-warning mt-3">
                    <strong><i class="fas fa-image me-2"></i>Image Requirements:</strong><br>
                    • Format: All image types<br>
                    • Max size: 2MB per image<br>
                    • Min size: 400x400 pixels<br>
                    • Gallery: Max 5 images
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Dual Pricing Example:</strong><br>
                    Retail Price: ₹10,000<br>
                    Dealer Price: ₹7,500 (25% OFF)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Image Preview for Primary Image
document.getElementById('primary_image').addEventListener('change', function(e) {
    const preview = document.getElementById('primary_image_preview');
    preview.innerHTML = '';
    
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('File size must not exceed 2MB', 'error', 'File Too Large');
            e.target.value = '';
            return;
        }
        
        // Validate file type - accept all image types
        if (!file.type.startsWith('image/')) {
            showAlert('Please select a valid image file', 'error', 'Invalid File Type');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'border rounded';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});

// Image Preview for Gallery Images
document.getElementById('gallery_images').addEventListener('change', function(e) {
    const preview = document.getElementById('gallery_images_preview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    // Validate number of files
    if (files.length > 5) {
        showAlert('You can upload maximum 5 gallery images', 'warning', 'Too Many Files');
        e.target.value = '';
        return;
    }
    
    files.forEach((file, index) => {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showAlert(`File ${index + 1} size must not exceed 2MB`, 'error', 'File Too Large');
            e.target.value = '';
            return;
        }
        
        // Validate file type - accept all image types
        if (!file.type.startsWith('image/')) {
            showAlert(`File ${index + 1} must be a valid image file`, 'error', 'Invalid File Type');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.className = 'border rounded';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

// Price Calculation Display
function updatePriceInfo() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
    const dealerPrice = parseFloat(document.getElementById('dealer_price').value) || 0;
    const dealerSalePrice = parseFloat(document.getElementById('dealer_sale_price').value) || 0;
    
    let info = '';
    
    if (price > 0 && dealerPrice > 0) {
        const discount = ((price - dealerPrice) / price * 100).toFixed(1);
        info += `Dealer gets <strong>${discount}%</strong> discount from retail price. `;
    }
    
    if (salePrice > 0 && price > 0) {
        const retailDiscount = ((price - salePrice) / price * 100).toFixed(1);
        info += `Retail sale: <strong>${retailDiscount}%</strong> off. `;
    }
    
    // Filter subcategories based on selected category
    document.getElementById('agriculture_category_id').addEventListener('change', function() {
        const categoryId = this.value;
        const subcategorySelect = document.getElementById('agriculture_subcategory_id');
        const options = subcategorySelect.querySelectorAll('option');
        
        // Show/hide subcategories based on selected category
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block'; // Always show the "Select Subcategory" option
            } else {
                const subcategoryCategoryId = option.getAttribute('data-category-id');
                if (categoryId === '' || subcategoryCategoryId === categoryId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });
        
        // Reset subcategory selection if it doesn't belong to selected category
        if (categoryId && subcategorySelect.value) {
            const selectedOption = subcategorySelect.options[subcategorySelect.selectedIndex];
            if (selectedOption.getAttribute('data-category-id') !== categoryId) {
                subcategorySelect.value = '';
            }
        }
    });
    
    // Trigger on page load if category is already selected
    if (document.getElementById('agriculture_category_id').value) {
        document.getElementById('agriculture_category_id').dispatchEvent(new Event('change'));
    }
    
    if (dealerSalePrice > 0 && dealerPrice > 0) {
        const dealerDiscount = ((dealerPrice - dealerSalePrice) / dealerPrice * 100).toFixed(1);
        info += `Dealer sale: <strong>${dealerDiscount}%</strong> off.`;
    }
    
    document.getElementById('price-info').innerHTML = info || 'Enter prices to see discount percentage';
}

// Add event listeners for price fields
['price', 'sale_price', 'dealer_price', 'dealer_sale_price'].forEach(id => {
    document.getElementById(id).addEventListener('input', updatePriceInfo);
});
</script>
@endpush
