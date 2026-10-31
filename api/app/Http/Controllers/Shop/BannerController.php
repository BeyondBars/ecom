<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['placement' => ['nullable', Rule::in(Banner::PLACEMENTS)]]);

        return BannerResource::collection(
            Banner::live()
                ->when($request->input('placement'), fn ($q, $placement) => $q->where('placement', $placement))
                ->orderBy('sort_order')
                ->get()
        );
    }
}
