<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use App\Models\AgricultureSubcategory;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request)
    {
        $query = Offer::with(['product', 'category', 'subcategory']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('start_date', '>', now());
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('offer_type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $offers = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $products = AgricultureProduct::active()->get();
        $categories = AgricultureCategory::active()->get();
        $subcategories = AgricultureSubcategory::active()->get();

        return view('admin.offers.create', compact('products', 'categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        try {
            // Prepare data for validation
            $requestData = $request->all();
            
            // Convert empty strings to null for nullable fields
            if ($request->offer_type !== 'product') {
                $requestData['product_id'] = null;
            } else {
                $requestData['product_id'] = $request->product_id ?: null;
            }
            if ($request->offer_type !== 'category') {
                $requestData['category_id'] = null;
            } else {
                $requestData['category_id'] = $request->category_id ?: null;
            }
            if ($request->offer_type !== 'subcategory') {
                $requestData['subcategory_id'] = null;
            } else {
                $requestData['subcategory_id'] = $request->subcategory_id ?: null;
            }
            
            // Replace empty strings with null for nullable fields
            $nullableFields = ['min_purchase_amount', 'min_quantity', 'max_uses', 'max_uses_per_user', 'sort_order', 'priority', 'terms_conditions', 'description'];
            foreach ($nullableFields as $field) {
                if (isset($requestData[$field]) && $requestData[$field] === '') {
                    $requestData[$field] = null;
                }
            }

            $validated = validator($requestData, [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'banner_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'offer_type' => 'required|in:product,category,subcategory,general',
                'product_id' => 'required_if:offer_type,product|nullable|exists:agriculture_products,id',
                'category_id' => 'required_if:offer_type,category|nullable|exists:agriculture_categories,id',
                'subcategory_id' => 'required_if:offer_type,subcategory|nullable|exists:agriculture_subcategories,id',
                'discount_type' => 'required|in:percentage,fixed',
                    'discount_value' => 'required|numeric|min:0.01',
                'min_purchase_amount' => 'nullable|numeric|min:0',
                'min_quantity' => 'nullable|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'max_uses' => 'nullable|integer|min:1',
                'max_uses_per_user' => 'nullable|integer|min:1',
                'is_active' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
                'priority' => 'nullable|integer|min:0',
                'terms_conditions' => 'nullable|string',
                'for_customers' => 'nullable|boolean',
                'for_dealers' => 'nullable|boolean',
            ])->validate();

            $data = $validated;
            
            // Generate unique slug
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (Offer::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;

            // Handle banner image upload
            if ($request->hasFile('banner_image')) {
                $bannerPath = $this->fileUploadService->uploadProductImage(
                    $request->file('banner_image'),
                    'banners'
                );
                $data['banner_image'] = $bannerPath;
            }

            // Set defaults
            $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
            $data['is_featured'] = $request->has('is_featured') ? (bool) $request->is_featured : false;
            $data['for_customers'] = $request->has('for_customers') ? (bool) $request->for_customers : true;
            $data['for_dealers'] = $request->has('for_dealers') ? (bool) $request->for_dealers : false;
            $data['sort_order'] = $request->sort_order ?? 0;
            $data['priority'] = $request->priority ?? 0;
            $data['used_count'] = 0;

            // Ensure proper date format (datetime-local format: Y-m-d\TH:i)
            if (strpos($request->start_date, 'T') !== false) {
                $data['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->start_date)));
            } else {
                $data['start_date'] = date('Y-m-d H:i:s', strtotime($request->start_date));
            }
            
            if (strpos($request->end_date, 'T') !== false) {
                $data['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->end_date)));
            } else {
                $data['end_date'] = date('Y-m-d H:i:s', strtotime($request->end_date));
            }

            // Clear unrelated IDs
            if ($data['offer_type'] !== 'product') {
                $data['product_id'] = null;
            }
            if ($data['offer_type'] !== 'category') {
                $data['category_id'] = null;
            }
            if ($data['offer_type'] !== 'subcategory') {
                $data['subcategory_id'] = null;
            }

            $offer = Offer::create($data);

            return redirect()->route('admin.offers.index')
                ->with('success', 'Offer created successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Offer creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error creating offer: ' . $e->getMessage()]);
        }
    }

    public function show(Offer $offer)
    {
        $offer->load(['product', 'category', 'subcategory']);
        return view('admin.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $products = AgricultureProduct::active()->get();
        $categories = AgricultureCategory::active()->get();
        $subcategories = AgricultureSubcategory::active()->get();

        return view('admin.offers.edit', compact('offer', 'products', 'categories', 'subcategories'));
    }

    public function update(Request $request, Offer $offer)
    {
        try {
            // Prepare data for validation
            $requestData = $request->all();
            
            // Convert empty strings to null for nullable fields
            if ($request->offer_type !== 'product') {
                $requestData['product_id'] = null;
            } else {
                $requestData['product_id'] = $request->product_id ?: null;
            }
            if ($request->offer_type !== 'category') {
                $requestData['category_id'] = null;
            } else {
                $requestData['category_id'] = $request->category_id ?: null;
            }
            if ($request->offer_type !== 'subcategory') {
                $requestData['subcategory_id'] = null;
            } else {
                $requestData['subcategory_id'] = $request->subcategory_id ?: null;
            }
            
            // Replace empty strings with null for nullable fields
            $nullableFields = ['min_purchase_amount', 'min_quantity', 'max_uses', 'max_uses_per_user', 'sort_order', 'priority', 'terms_conditions', 'description'];
            foreach ($nullableFields as $field) {
                if (isset($requestData[$field]) && $requestData[$field] === '') {
                    $requestData[$field] = null;
                }
            }

            $validator = Validator::make($requestData, [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'banner_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'offer_type' => 'required|in:product,category,subcategory,general',
                'product_id' => 'required_if:offer_type,product|nullable|exists:agriculture_products,id',
                'category_id' => 'required_if:offer_type,category|nullable|exists:agriculture_categories,id',
                'subcategory_id' => 'required_if:offer_type,subcategory|nullable|exists:agriculture_subcategories,id',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0.01',
                'min_purchase_amount' => 'nullable|numeric|min:0',
                'min_quantity' => 'nullable|integer|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'max_uses' => 'nullable|integer|min:1',
                'max_uses_per_user' => 'nullable|integer|min:1',
                'is_active' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
                'priority' => 'nullable|integer|min:0',
                'terms_conditions' => 'nullable|string',
                'for_customers' => 'nullable|boolean',
                'for_dealers' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $validator->validated();

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner if exists
            if ($offer->banner_image) {
                $this->fileUploadService->deleteFile($offer->banner_image);
            }

            $bannerPath = $this->fileUploadService->uploadProductImage(
                $request->file('banner_image'),
                'banners'
            );
            $data['banner_image'] = $bannerPath;
        }

        // Set defaults for boolean fields (checkboxes: if not in request, default to false)
        $data['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        $data['is_featured'] = $request->has('is_featured') ? (bool) $request->is_featured : false;
        $data['for_customers'] = $request->has('for_customers') ? (bool) $request->for_customers : false;
        $data['for_dealers'] = $request->has('for_dealers') ? (bool) $request->for_dealers : false;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['priority'] = $data['priority'] ?? 0;

        // Clear unrelated IDs
        if ($data['offer_type'] !== 'product') {
            $data['product_id'] = null;
        }
        if ($data['offer_type'] !== 'category') {
            $data['category_id'] = null;
        }
        if ($data['offer_type'] !== 'subcategory') {
            $data['subcategory_id'] = null;
        }

        // Ensure proper date format (datetime-local format: Y-m-d\TH:i)
        if (strpos($request->start_date, 'T') !== false) {
            $data['start_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->start_date)));
        } else {
            $data['start_date'] = date('Y-m-d H:i:s', strtotime($request->start_date));
        }
        
        if (strpos($request->end_date, 'T') !== false) {
            $data['end_date'] = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $request->end_date)));
        } else {
            $data['end_date'] = date('Y-m-d H:i:s', strtotime($request->end_date));
        }

        // Generate unique slug if title changed
        if ($offer->title !== $request->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (Offer::where('slug', $slug)->where('id', '!=', $offer->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
        } else {
            // Keep existing slug if title hasn't changed
            $data['slug'] = $offer->slug;
        }

            $offer->update($data);

            return redirect()->route('admin.offers.index')
                ->with('success', 'Offer updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Offer update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error updating offer: ' . $e->getMessage()]);
        }
    }

    public function destroy(Offer $offer)
    {
        // Delete banner image if exists
        if ($offer->banner_image) {
            $this->fileUploadService->deleteFile($offer->banner_image);
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully!');
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        
        $status = $offer->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Offer {$status} successfully!");
    }

    public function toggleFeatured(Offer $offer)
    {
        $offer->update(['is_featured' => !$offer->is_featured]);
        
        $status = $offer->is_featured ? 'added to' : 'removed from';
        return redirect()->back()
            ->with('success', "Offer {$status} featured offers!");
    }
}
