<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        return BrandResource::collection(
            Brand::active()->withCount(['products' => fn ($q) => $q->visible()])->orderBy('name')->get()
        );
    }

    public function show(string $slug): BrandResource
    {
        return new BrandResource(Brand::active()->where('slug', $slug)->firstOrFail());
    }
}
