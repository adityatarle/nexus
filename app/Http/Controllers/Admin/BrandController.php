<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        try {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Brand::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $brand = Brand::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'logo' => $request->logo,
                'website' => $request->website,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand created successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A brand with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->loadCount('products');
        $brand->load('products');
        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        try {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            if ($slug !== $brand->slug) {
                $counter = 1;
                while (Brand::where('slug', $slug)->where('id', '!=', $brand->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            $brand->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'logo' => $request->logo,
                'website' => $request->website,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0
            ]);

            return redirect()->route('admin.brands.index')
                ->with('success', 'Brand updated successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'A brand with this name already exists. Please choose a different name.']);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $productCount = $brand->products()->count();
        
        if ($productCount > 0) {
            return redirect()->back()
                ->with('error', "This brand has {$productCount} product(s). Please move or delete them first before deleting this brand.");
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully!');
    }
}
