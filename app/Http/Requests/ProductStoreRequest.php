<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow admins to create/update products
        // Middleware already protects admin routes, but we double-check here for security
        $isAuthenticated = auth()->check();
        $user = $isAuthenticated ? auth()->user() : null;
        $isAdmin = $user && $user->isAdmin();
        
        // Log for debugging
        \Log::info('ProductStoreRequest Authorization Check', [
            'is_authenticated' => $isAuthenticated,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'user_role' => $user ? $user->role : null,
            'is_admin' => $isAdmin,
        ]);
        
        return $isAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'slug' => [
                'nullable', // Make it nullable since we generate it in controller
                'string',
                'max:255',
                'unique:agriculture_products,slug,' . $productId,
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', // Lowercase with hyphens only
            ],
            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],
            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                'lt:price', // Sale price must be less than regular price
            ],
            'dealer_price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
                'lte:price', // Dealer price cannot exceed regular price
            ],
            'dealer_sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                'lt:dealer_price',
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:agriculture_products,sku,' . $productId,
                'regex:/^[A-Z0-9-]+$/', // Uppercase letters, numbers, and hyphens
            ],
            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
            'agriculture_category_id' => [
                'required',
                'exists:agriculture_categories,id',
            ],
            'agriculture_subcategory_id' => [
                'nullable',
                'exists:agriculture_subcategories,id',
            ],
            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],
            'brand_id' => [
                'nullable',
                'exists:brands,id',
            ],
            'brand_custom' => [
                'nullable',
                'string',
                'max:100',
            ],
            'model' => [
                'nullable',
                'string',
                'max:100',
            ],
            'power_source' => [
                'nullable',
                'string',
                'max:50',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999',
            ],
            'dimensions' => [
                'nullable',
                'string',
                'max:100',
            ],
            'warranty' => [
                'nullable',
                'string',
                'max:100',
            ],
            'is_featured' => [
                'boolean',
            ],
            'is_active' => [
                'boolean',
            ],
            'primary_image' => [
                'required',
                'image',
                'max:2048', // 2MB
                'dimensions:min_width=400,min_height=400,max_width=4000,max_height=4000',
            ],
            'gallery_images' => [
                'nullable',
                'array',
                'max:5', // Maximum 5 gallery images
            ],
            'gallery_images.*' => [
                'nullable',
                'image',
                'max:2048',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.min' => 'Product name must be at least 3 characters.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            
            'slug.required' => 'Product slug is required.',
            'slug.unique' => 'This slug is already taken. Please use a different one.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            
            'description.required' => 'Product description is required.',
            'description.min' => 'Description must be at least 50 characters.',
            
            'price.required' => 'Regular price is required.',
            'price.min' => 'Price must be greater than or equal to 0.',
            
            'sale_price.lt' => 'Sale price must be less than regular price.',
            
            'dealer_price.required' => 'Dealer price is required.',
            'dealer_price.lte' => 'Dealer price cannot exceed regular price.',
            
            'dealer_sale_price.lt' => 'Dealer sale price must be less than dealer regular price.',
            
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU is already in use.',
            'sku.regex' => 'SKU must contain only uppercase letters, numbers, and hyphens.',
            
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            
            'agriculture_category_id.required' => 'Please select a category.',
            'agriculture_category_id.exists' => 'Selected category does not exist.',
            
            'primary_image.image' => 'Primary image must be an image file.',
            'primary_image.mimes' => 'Primary image must be in JPEG, PNG, or WebP format.',
            'primary_image.max' => 'Primary image size must not exceed 2MB.',
            'primary_image.dimensions' => 'Primary image must be at least 400x400 pixels and not exceed 4000x4000 pixels.',
            
            'gallery_images.max' => 'You can upload maximum 5 gallery images.',
            'gallery_images.*.image' => 'All gallery files must be images.',
            'gallery_images.*.mimes' => 'Gallery images must be in JPEG, PNG, or WebP format.',
            'gallery_images.*.max' => 'Each gallery image must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'agriculture_category_id' => 'category',
            'primary_image' => 'product image',
            'gallery_images.*' => 'gallery image',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize text inputs
        $this->merge([
            'name' => strip_tags($this->name ?? ''),
            'slug' => strtolower(trim($this->slug ?? '')),
            'short_description' => strip_tags($this->short_description ?? ''),
            'brand' => strip_tags($this->brand ?? ''),
            'model' => strip_tags($this->model ?? ''),
            'power_source' => strip_tags($this->power_source ?? ''),
            'warranty' => strip_tags($this->warranty ?? ''),
            'sku' => strtoupper(trim($this->sku ?? '')),
            
            // Convert checkboxes to boolean
            'is_featured' => $this->has('is_featured') ? (bool) $this->is_featured : false,
            'is_active' => $this->has('is_active') ? (bool) $this->is_active : true,
        ]);

        // Sanitize description (allow some HTML tags)
        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags(
                    $this->description,
                    '<p><br><strong><em><ul><ol><li><h3><h4><h5><h6>'
                ),
            ]);
        }
    }
}


