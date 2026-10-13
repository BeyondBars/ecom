<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Enums\UserAbility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Image uploads for products, categories, brands, banners and blog posts. Files go on the public
 * disk (run `php artisan storage:link`), and the returned URL is saved on the record.
 */
class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        abort_unless(
            $request->user()->can(UserAbility::ManageCatalog->value) || $request->user()->can(UserAbility::ManageContent->value),
            403
        );

        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $path = $request->file('file')->store('uploads/'.now()->format('Y/m'), 'public');

        return response()->json(['data' => ['url' => Storage::disk('public')->url($path), 'path' => $path]], 201);
    }
}
