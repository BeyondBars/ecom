<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\SaveCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return CategoryResource::collection(
            QueryBuilder::for(Category::query()->with('parent')->withCount('products'))
                ->allowedFilters(
                    AllowedFilter::exact('is_active'),
                    AllowedFilter::exact('parent_id'),
                    AllowedFilter::partial('search', 'name'),
                )
                ->allowedSorts('name', 'sort_order', 'created_at')
                ->defaultSort('sort_order', 'name')
                ->get()
        );
    }

    public function store(SaveCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return (new CategoryResource($category->load('parent')))->response()->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource
    {
        $this->authorize('view', $category);

        return new CategoryResource($category->load('parent', 'children')->loadCount('products'));
    }

    public function update(SaveCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category->load('parent'));
    }

    public function destroy(Category $category): Response|JsonResponse
    {
        $this->authorize('delete', $category);

        // Products must be moved first so nothing silently disappears from the shop
        if ($category->products()->exists()) {
            return response()->json(['message' => 'Move or delete this category\'s products first.'], 422);
        }

        $category->delete();

        return response()->noContent();
    }
}
