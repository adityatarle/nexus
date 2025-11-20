<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = AgricultureCategory::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:agriculture_categories,name',
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
            while (AgricultureCategory::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $category = AgricultureCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $request->image,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A category with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    public function show(AgricultureCategory $category)
    {
        $category->load(['products', 'subcategories']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(AgricultureCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, AgricultureCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:agriculture_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        try {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            if ($slug !== $category->slug) {
                $counter = 1;
                while (AgricultureCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'image' => $request->image,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A category with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    public function destroy(AgricultureCategory $category)
    {
        $productCount = $category->products()->count();
        $subcategoryCount = $category->subcategories()->count();
        
        if ($productCount > 0 || $subcategoryCount > 0) {
            $messages = [];
            if ($productCount > 0) {
                $messages[] = "This category has {$productCount} product(s).";
            }
            if ($subcategoryCount > 0) {
                $messages[] = "This category has {$subcategoryCount} subcategory(ies).";
            }
            $messages[] = "Please move or delete them first before deleting this category.";
            
            return redirect()->back()
                ->with('error', implode(' ', $messages));
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
