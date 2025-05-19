<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandStoreRequest;
use App\Http\Requests\BrandUpdateRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Brand::class, 'brand');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::withCount('products');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $brands = $query->paginate($perPage);

        return response()->json($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandStoreRequest $request)
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $brand = Brand::create($data);
        $brand->loadCount('products');

        return response()->json($brand, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->loadCount('products');
        
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        $data = $request->validated();
        
        // Update slug if name changed and slug not provided
        if (isset($data['name']) && !isset($data['slug']) && $data['name'] !== $brand->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $brand->update($data);
        $brand->loadCount('products');

        return response()->json($brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Check if the brand has products
        if ($brand->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete brand with associated products.'
            ], 422);
        }

        $brand->delete();

        return response()->json(null, 204);
    }
}
