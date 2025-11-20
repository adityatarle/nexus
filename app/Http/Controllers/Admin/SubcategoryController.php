<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureSubcategory;
use App\Models\AgricultureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AgricultureSubcategory::with('category')->withCount('products');
        
        // Filter by category if provided
        if ($request->filled('category_id')) {
            $query->where('agriculture_category_id', $request->category_id);
        }
        
        $subcategories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);
        
        $categories = AgricultureCategory::active()->get();
        
        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = AgricultureCategory::active()->orderBy('sort_order')->orderBy('name')->get();
        $selectedCategoryId = $request->get('category_id');
        return view('admin.subcategories.create', compact('categories', 'selectedCategoryId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:agriculture_subcategories,name',
            'agriculture_category_id' => 'required|exists:agriculture_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        try {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (AgricultureSubcategory::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $subcategory = AgricultureSubcategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'agriculture_category_id' => $request->agriculture_category_id,
                'description' => $request->description,
                'image' => $request->image,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.subcategories.index')
                ->with('success', 'Subcategory created successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A subcategory with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    public function show(AgricultureSubcategory $subcategory)
    {
        $subcategory->load(['category', 'products']);
        return view('admin.subcategories.show', compact('subcategory'));
    }

    public function edit(AgricultureSubcategory $subcategory)
    {
        $categories = AgricultureCategory::active()->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, AgricultureSubcategory $subcategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:agriculture_subcategories,name,' . $subcategory->id,
            'agriculture_category_id' => 'required|exists:agriculture_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        try {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            if ($slug !== $subcategory->slug) {
                $counter = 1;
                while (AgricultureSubcategory::where('slug', $slug)->where('id', '!=', $subcategory->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            $subcategory->update([
                'name' => $request->name,
                'slug' => $slug,
                'agriculture_category_id' => $request->agriculture_category_id,
                'description' => $request->description,
                'image' => $request->image,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.subcategories.index')
                ->with('success', 'Subcategory updated successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A subcategory with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    public function destroy(AgricultureSubcategory $subcategory)
    {
        if ($subcategory->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete subcategory with products. Please move or delete products first.');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully!');
    }
}

