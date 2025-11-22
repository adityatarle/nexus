@extends('admin.layout')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
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

                    <!-- Current Primary Image -->
                    @if($product->primary_image)
                    <div class="mb-3">
                        <label class="form-label">Current Primary Image</label>
                        <div class="border rounded p-2 mb-2" style="max-width: 200px;">
                            @php
                                $primaryImageUrl = \App\Helpers\ImageHelper::imageUrl($product->primary_image);
                            @endphp
                            <img src="{{ $primaryImageUrl }}" 
                                 alt="Primary Image" 
                                 class="img-fluid rounded">
                        </div>
                        <small class="text-muted">Upload a new image to replace this one</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="primary_image" class="form-label">
                            {{ $product->primary_image ? 'Replace Primary Image' : 'Primary Image *' }}
                        </label>
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

                    <!-- Current Gallery Images -->
                    @php
                        $galleryImages = is_array($product->gallery_images)
                            ? $product->gallery_images
                            : (json_decode($product->gallery_images ?? '[]', true) ?? []);
                    @endphp
                    @if(!empty($galleryImages) && count($galleryImages) > 0)
                    <div class="mb-3">
                        <label class="form-label">Current Gallery Images</label>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            @foreach($galleryImages as $index => $image)
                            <div class="position-relative" style="width: 100px; height: 100px;">
                                @php
                                    $galleryImageUrl = \App\Helpers\ImageHelper::imageUrl($image);
                                @endphp
                                <img src="{{ $galleryImageUrl }}" 
                                     alt="Gallery {{ $index + 1 }}" 
                                     class="img-fluid rounded border"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-gallery-image"
                                        data-index="{{ $index }}"
                                        title="Remove this image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="removed_gallery_images" id="removed_gallery_images" value="">
                        <small class="text-muted">Click X to remove an image, or upload new ones below</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="gallery_images" class="form-label">
                            {{ !empty($galleryImages) ? 'Add More Gallery Images' : 'Gallery Images (Optional)' }}
                        </label>
                        <input type="file" class="form-control @error('gallery_images.*') is-invalid @enderror" 
                               id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                        @error('gallery_images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            You can add up to {{ 5 - (count($galleryImages) > 5 ? 5 : count($galleryImages)) }} more images | Each max 2MB
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
                                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
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
                                           id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}">
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
                                           id="dealer_price" name="dealer_price" value="{{ old('dealer_price', $product->dealer_price) }}" required>
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
                                           id="dealer_sale_price" name="dealer_sale_price" value="{{ old('dealer_sale_price', $product->dealer_sale_price) }}">
                                    @error('dealer_sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Optional dealer discount price</small>
                            </div>
                        </div>
                    </div>

                    @if($product->price && $product->dealer_price)
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-calculator me-2"></i>
                        <strong>Current Discount:</strong>
                        {{ round((($product->price - $product->dealer_price) / $product->price) * 100, 2) }}% OFF for dealers
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
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
                                    <option value="{{ $category->id }}" {{ old('agriculture_category_id', $product->agriculture_category_id) == $category->id ? 'selected' : '' }}>
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
                                            {{ old('agriculture_subcategory_id', $product->agriculture_subcategory_id) == $subcategory->id ? 'selected' : '' }}>
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
                                       id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $product->model) }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                  id="short_description" name="short_description" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="in_stock" name="in_stock" value="1" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="in_stock">In Stock</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="manage_stock" name="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manage_stock">Manage Stock</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Product
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
                <h5 class="mb-0">Product Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>View Product
                    </a>
                    <a href="{{ route('agriculture.products.show', $product) }}" class="btn btn-outline-info" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>View on Website
                    </a>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Current Status</small>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($product->is_featured)
                        <span class="badge bg-warning">Featured</span>
                        @endif
                        <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">
                            {{ $product->in_stock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Stock Level</small>
                    <span class="badge bg-{{ $product->stock_quantity < 10 ? 'warning' : 'success' }}">
                        {{ $product->stock_quantity }} units
                    </span>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Pricing</small>
                    <div>
                        <strong>${{ number_format($product->current_price, 2) }}</strong>
                        @if($product->sale_price)
                        <br><small class="text-muted">Regular: ${{ number_format($product->price, 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $product->is_active ? 'pause' : 'play' }} me-2"></i>
                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-{{ $product->is_featured ? 'secondary' : 'warning' }} w-100">
                            <i class="fas fa-star me-2"></i>
                            {{ $product->is_featured ? 'Remove from Featured' : 'Add to Featured' }}
                        </button>
                    </form>
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
            const div = document.createElement('div');
            div.innerHTML = '<strong class="text-success">New image selected:</strong>';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'border rounded mt-2';
            div.appendChild(img);
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
});

// Image Preview for Gallery Images
document.getElementById('gallery_images').addEventListener('change', function(e) {
    const preview = document.getElementById('gallery_images_preview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    const currentCount = {{ isset($galleryImages) ? count($galleryImages) : 0 }};
    const maxAllowed = 5 - currentCount;
    
    // Validate number of files
    if (files.length > maxAllowed) {
        showAlert(`You can only add ${maxAllowed} more gallery images (current: ${currentCount}, max: 5)`, 'warning', 'Too Many Files');
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
            const div = document.createElement('div');
            div.style.position = 'relative';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.className = 'border rounded';
            div.appendChild(img);
            const badge = document.createElement('span');
            badge.className = 'badge bg-success position-absolute top-0 start-0 m-1';
            badge.textContent = 'New';
            div.appendChild(badge);
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// Gallery Image Removal
const removedImages = [];
document.querySelectorAll('.remove-gallery-image').forEach(button => {
    button.addEventListener('click', function() {
        const index = this.getAttribute('data-index');
        const imageContainer = this.closest('.position-relative');
        
        if (confirm('Are you sure you want to remove this image?')) {
            removedImages.push(index);
            document.getElementById('removed_gallery_images').value = removedImages.join(',');
            imageContainer.style.opacity = '0.3';
            imageContainer.querySelector('img').style.filter = 'grayscale(100%)';
            this.innerHTML = '<i class="fas fa-check"></i>';
            this.classList.remove('btn-danger');
            this.classList.add('btn-secondary');
            this.disabled = true;
        }
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
    
    if (dealerSalePrice > 0 && dealerPrice > 0) {
        const dealerDiscount = ((dealerPrice - dealerSalePrice) / dealerPrice * 100).toFixed(1);
        info += `Dealer sale: <strong>${dealerDiscount}%</strong> off.`;
    }
    
    const priceInfoElement = document.getElementById('price-info');
    if (priceInfoElement) {
        priceInfoElement.innerHTML = info || 'Enter prices to see discount percentage';
    }
}

// Add event listeners for price fields
['price', 'sale_price', 'dealer_price', 'dealer_sale_price'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', updatePriceInfo);
    }
});

// Initialize price info on load
updatePriceInfo();

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
</script>
@endpush
