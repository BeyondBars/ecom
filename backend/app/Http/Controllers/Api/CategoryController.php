<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        } else {
            // If parent_id not specified, include null for root categories
            $query->whereNull('parent_id');
        }

        // Apply sorting
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Get all categories (no pagination for hierarchical data)
        $categories = $query->get();

        return response()->json($categories);
    }

    /**
     * Get all categories as a hierarchical tree.
     */
    public function tree()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount('products');
            }])
            ->withCount('products')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = Category::create($data);
        $category->loadCount('products');

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('products');
        
        if ($category->parent_id) {
            $category->load('parent');
        }
        
        $category->load('children');
        
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $data = $request->validated();
        
        // Update slug if name changed and slug not provided
        if (isset($data['name']) && !isset($data['slug']) && $data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Prevent category from being its own parent
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            return response()->json([
                'message' => 'A category cannot be its own parent.'
            ], 422);
        }

        $category->update($data);
        $category->loadCount('products');

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if the category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with associated products.'
            ], 422);
        }

        // Check if the category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with child categories.'
            ], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
