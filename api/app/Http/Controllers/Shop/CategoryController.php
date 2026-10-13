<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::active()
                ->withCount(['products' => fn ($q) => $q->visible()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function show(string $slug): CategoryResource
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->with(['parent', 'children' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->firstOrFail();

        return new CategoryResource($category);
    }
}
