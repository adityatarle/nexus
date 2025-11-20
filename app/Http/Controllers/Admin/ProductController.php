<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use App\Models\AgricultureSubcategory;
use App\Http\Requests\ProductStoreRequest;
use App\Services\FileUploadService;
use App\Imports\ProductImport;
use App\Exports\ProductTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $query = AgricultureProduct::with(['category', 'subcategory']);
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('agriculture_category_id', $request->category);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'low_stock') {
                $query->where('stock_quantity', '<', 10);
            }
        }
        
        $products = $query->latest()->paginate(15);
        $categories = AgricultureCategory::active()->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }
    
    public function create()
    {
        $categories = AgricultureCategory::active()->get();
        $subcategories = AgricultureSubcategory::active()->get();
        return view('admin.products.create', compact('categories', 'subcategories'));
    }
    
    public function store(ProductStoreRequest $request)
    {
        try {
            // Debug: Log the request data
            \Log::info('Product Store Request Data:', [
                'has_primary_image' => $request->hasFile('primary_image'),
                'has_gallery_images' => $request->hasFile('gallery_images'),
                'gallery_images_count' => $request->hasFile('gallery_images') ? count($request->file('gallery_images')) : 0,
                'all_data' => $request->all()
            ]);
            
            $data = $request->validated();
            $data['slug'] = Str::slug($request->name);
            
            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                $primaryImagePath = $this->fileUploadService->uploadProductImage(
                    $request->file('primary_image'),
                    'primary'
                );
                $data['primary_image'] = $primaryImagePath;
            }
            
            // Handle gallery images upload
            $galleryImages = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImages[] = $this->fileUploadService->uploadProductImage(
                        $image,
                        'gallery'
                    );
                }
                // Pass as array, model will cast to JSON automatically
                $data['gallery_images'] = $galleryImages;
            }
            
            // Calculate dealer discount percentage
            if (isset($data['price']) && isset($data['dealer_price'])) {
                $discount = (($data['price'] - $data['dealer_price']) / $data['price']) * 100;
                $data['dealer_discount_percentage'] = round($discount, 2);
            }
            
            // Set dealer minimum quantity if not provided
            if (!isset($data['dealer_min_quantity'])) {
                $data['dealer_min_quantity'] = 1;
            }
            
            // Convert boolean fields properly (form sends "1" or "0" as strings)
            $data['is_featured'] = isset($data['is_featured']) ? (bool) $data['is_featured'] : false;
            $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : true;
            $data['in_stock'] = isset($data['in_stock']) ? (bool) $data['in_stock'] : true;
            $data['manage_stock'] = isset($data['manage_stock']) ? (bool) $data['manage_stock'] : true;
            $data['is_dealer_exclusive'] = isset($data['is_dealer_exclusive']) ? (bool) $data['is_dealer_exclusive'] : false;
            
            // Ensure gallery_images is array if it exists (model will cast to JSON automatically)
            if (isset($data['gallery_images']) && is_string($data['gallery_images'])) {
                $data['gallery_images'] = json_decode($data['gallery_images'], true);
            }
            
            \Log::info('Product Data Before Create:', ['data' => $data]);
            
            // Remove any fields not in fillable (to prevent mass assignment errors)
            $fillableFields = (new AgricultureProduct())->getFillable();
            $data = array_intersect_key($data, array_flip($fillableFields));
            
            // Create the product
            $product = AgricultureProduct::create($data);
            \Log::info('Product Created Successfully:', ['product_id' => $product->id, 'product_name' => $product->name]);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully with images!');
                
        } catch (ValidationException $e) {
            // Re-throw validation exceptions so they're handled properly
            throw $e;
        } catch (QueryException $e) {
            // Database constraint errors
            \Log::error('Product Creation Failed - Database Error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
            ]);
            
            $errorMessage = 'Database error: ';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'slug')) {
                    $errorMessage = 'A product with this name already exists. Please use a different name.';
                } elseif (str_contains($e->getMessage(), 'sku')) {
                    $errorMessage = 'A product with this SKU already exists. Please use a different SKU.';
                } else {
                    $errorMessage = 'This product already exists.';
                }
            } else {
                $errorMessage .= $e->getMessage();
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $errorMessage]);
        } catch (Exception $e) {
            // Log full exception details for debugging
            \Log::error('Product Creation Failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? 'Data not available',
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error creating product: ' . $e->getMessage()]);
        }
    }
    
    public function show(AgricultureProduct $product)
    {
        $product->load('category', 'orderItems');
        return view('admin.products.show', compact('product'));
    }
    
    public function edit(AgricultureProduct $product)
    {
        $categories = AgricultureCategory::active()->get();
        $subcategories = AgricultureSubcategory::active()->get();
        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }
    
    public function update(Request $request, AgricultureProduct $product)
    {
        try {
            // Debug: Log the request data
            \Log::info('Product Update Request Data:', [
                'product_id' => $product->id,
                'has_primary_image' => $request->hasFile('primary_image'),
                'has_gallery_images' => $request->hasFile('gallery_images'),
                'gallery_images_count' => $request->hasFile('gallery_images') ? count($request->file('gallery_images')) : 0,
                'removed_gallery_images' => $request->removed_gallery_images,
                'all_data' => $request->all()
            ]);
            
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|min:50',
                'short_description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'dealer_price' => 'required|numeric|min:0',
                'dealer_sale_price' => 'nullable|numeric|min:0',
                'sku' => 'required|string|unique:agriculture_products,sku,' . $product->id,
                'stock_quantity' => 'required|integer|min:0',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'power_source' => 'nullable|string|max:255',
                'warranty' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'dimensions' => 'nullable|string|max:255',
                'agriculture_category_id' => 'required|exists:agriculture_categories,id',
                'agriculture_subcategory_id' => 'nullable|exists:agriculture_subcategories,id',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
                'in_stock' => 'boolean',
                'manage_stock' => 'boolean',
                'primary_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);
            
            $data = $validated;
            $data['slug'] = Str::slug($request->name);
            
            // Handle primary image replacement
            if ($request->hasFile('primary_image')) {
                // Delete old primary image if exists
                if ($product->primary_image) {
                    $this->fileUploadService->deleteFile($product->primary_image);
                }
                
                // Upload new primary image
                $primaryImagePath = $this->fileUploadService->uploadProductImage(
                    $request->file('primary_image'),
                    'primary'
                );
                $data['primary_image'] = $primaryImagePath;
            }
            
            // Handle gallery images (normalize to array)
            $currentGalleryImages = is_array($product->gallery_images)
                ? $product->gallery_images
                : (json_decode($product->gallery_images ?? '[]', true) ?? []);
            
            // Remove marked images
            if ($request->filled('removed_gallery_images')) {
                $removedIndices = explode(',', $request->removed_gallery_images);
                foreach ($removedIndices as $index) {
                    if (isset($currentGalleryImages[$index])) {
                        // Delete the file
                        $this->fileUploadService->deleteFile($currentGalleryImages[$index]);
                        unset($currentGalleryImages[$index]);
                    }
                }
                // Re-index array
                $currentGalleryImages = array_values($currentGalleryImages);
            }
            
            // Add new gallery images
            if ($request->hasFile('gallery_images')) {
                $newGalleryImages = [];
                foreach ($request->file('gallery_images') as $image) {
                    $newGalleryImages[] = $this->fileUploadService->uploadProductImage(
                        $image,
                        'gallery'
                    );
                }
                // Merge with existing images (up to 5 total)
                $currentGalleryImages = array_merge($currentGalleryImages, $newGalleryImages);
                $currentGalleryImages = array_slice($currentGalleryImages, 0, 5);
            }
            
            // Update gallery images if changed
            $existingCount = is_array($product->gallery_images)
                ? count($product->gallery_images)
                : (is_string($product->gallery_images) ? count(json_decode($product->gallery_images ?? '[]', true) ?? []) : 0);

            if (count($currentGalleryImages) !== $existingCount) {
                $data['gallery_images'] = json_encode($currentGalleryImages);
            }
            
            // Calculate dealer discount percentage
            if (isset($data['price']) && isset($data['dealer_price'])) {
                $discount = (($data['price'] - $data['dealer_price']) / $data['price']) * 100;
                $data['dealer_discount_percentage'] = round($discount, 2);
            }
            
            // Update the product
            $product->update($data);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully!');
                
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error updating product: ' . $e->getMessage()]);
        }
    }
    
    public function destroy(AgricultureProduct $product)
    {
        try {
            // Delete primary image if exists
            if ($product->primary_image) {
                $this->fileUploadService->deleteFile($product->primary_image);
            }
            
            // Delete gallery images if exist
            $existingGalleryForDelete = is_array($product->gallery_images)
                ? $product->gallery_images
                : (json_decode($product->gallery_images ?? '[]', true) ?? []);
            if (!empty($existingGalleryForDelete)) {
                foreach ($existingGalleryForDelete as $image) {
                    $this->fileUploadService->deleteFile($image);
                }
            }
            
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product and associated images deleted successfully!');
                
        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error deleting product: ' . $e->getMessage()]);
        }
    }
    
    public function toggleStatus(AgricultureProduct $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Product {$status} successfully!");
    }
    
    public function toggleFeatured(AgricultureProduct $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        
        $status = $product->is_featured ? 'added to' : 'removed from';
        return redirect()->back()
            ->with('success', "Product {$status} featured products!");
    }
    
    /**
     * Download Excel template for product import
     */
    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport(), 'product_import_template.xlsx');
    }
    
    /**
     * Import products from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // 10MB max
        ]);
        
        try {
            $import = new ProductImport();
            
            \Log::info('Starting product import', [
                'file_name' => $request->file('excel_file')->getClientOriginalName(),
                'file_size' => $request->file('excel_file')->getSize(),
            ]);
            
            Excel::import($import, $request->file('excel_file'));
            
            $successCount = $import->getSuccessCount();
            $failCount = $import->getFailCount();
            $errors = $import->getErrors();
            $info = $import->getInfo();
            
            $totalRowsProcessed = method_exists($import, 'getTotalRowsProcessed') ? $import->getTotalRowsProcessed() : 0;
            
            \Log::info('Product import completed', [
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'error_count' => count($errors),
                'info_count' => count($info),
                'total_rows_processed' => $totalRowsProcessed,
            ]);
            
            // If no rows were processed at all, it might be a file format issue
            if ($totalRowsProcessed == 0 && $successCount == 0 && $failCount == 0) {
                \Log::warning('Product Import - No rows were processed. This might indicate a file format issue.');
                return redirect()->back()
                    ->withErrors(['excel_file' => 'No data rows found in the Excel file. Please ensure the file has data rows below the header.'])
                    ->with('import_errors', ['No data rows were found in the Excel file.']);
            }
            
            $message = "Import completed! {$successCount} product(s) imported successfully.";
            
            if ($failCount > 0) {
                $message .= " {$failCount} product(s) failed to import.";
            }
            
            if (!empty($info)) {
                // Store info messages in session to display
                $request->session()->flash('import_info', $info);
            }
            
            if (!empty($errors)) {
                // Store errors in session to display
                $request->session()->flash('import_errors', $errors);
            }
            
            return redirect()->route('admin.products.index')
                ->with('success', $message)
                ->with('import_errors', $errors)
                ->with('import_info', $info);
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = $failure->errors();
                
                foreach ($errors as $error) {
                    $errorMessages[] = "Row {$row}, Column {$attribute}: {$error}";
                }
            }
            
            return redirect()->back()
                ->withErrors(['excel_file' => 'Validation errors in Excel file'])
                ->with('import_errors', $errorMessages);
                
        } catch (\Exception $e) {
            \Log::error('Product Import Failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $errorMessage = 'Error importing products: ' . $e->getMessage();
            
            // If we have any errors from the import, include them
            if (isset($import) && method_exists($import, 'getErrors')) {
                $importErrors = $import->getErrors();
                if (!empty($importErrors)) {
                    $errorMessage .= "\n\nImport Errors:\n" . implode("\n", array_slice($importErrors, 0, 10));
                    if (count($importErrors) > 10) {
                        $errorMessage .= "\n... and " . (count($importErrors) - 10) . " more errors.";
                    }
                }
            }
            
            return redirect()->back()
                ->withErrors(['excel_file' => $errorMessage])
                ->with('import_errors', isset($import) && method_exists($import, 'getErrors') ? $import->getErrors() : []);
        }
    }
}